<?php

require_once("support.php");
require 'aws/aws-autoloader.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Sns\SnsClient;

$title = "User Form";
$body = <<<EOBODY
<h1> User Form Submission </h1>
<h3>Enter in the information for the process you would like to have verified by our our extensive
Knowledge Based Machining Database </h3>
<form action="{$_SERVER['PHP_SELF']}" method="post">
        <p>
            <strong>Manufacturer: </strong><input type="text" name="manufacturer" id="manufacturer" required="required"/>
        </p> 
        <p>
            <strong>Tool ID: </strong><input type="text" name="tool" id="tool" required="required"/>
        </p>
        <p>
            <strong>Material: </strong><input type="text" name="material" id="material" required="required"/>
        </p>
        <p>
            <strong>Feature: </strong><input type="text" name="feature" id="feature" required="required"/>
        </p>
        <p>
            <strong>Speed: </strong><input type="text" name="speed" id="speed" required="required"/>
        </p>
        <p>
            <strong>Email Address: </strong><input type="email" name="email" id="email" required="required"/><br />
            We will notify you when the given material and tool have been reviewed and verified
        </p> 
        <p>
            <input type="submit" name="submit" value="Submit Data" />
        </p>  
EOBODY;

if(isset($_POST['submit'])) {
    $sdk = new Aws\Sdk([
        'region' => 'us-east-1',
        'version'  => 'latest'
    ]);

    $dynamodb = $sdk->createDynamoDb();

    $marshaler = new Marshaler();

    $main_table = "OMR_main";
    $table = trim($_POST['manufacturer']);
    $tool = $_POST['tool'];

    $item = $marshaler->marshalJson('
        {
        "Tool_Id": "' . $tool . '"
        }
    ');

    $params = [
        'TableName' => $table,
        'Key' => $item
    ];

    try {
        $result = $dynamodb->getItem($params);
        if ($result["Item"] == NULL) {
            $body .= "<h2>We are sorry but we do not contain that Tool in our database. <br /> 
                        Please check back later or make sure you spelled correctly!</h2>";
        } else {
            $sns = $sdk->createSns();
            $email = $_POST['email'];
            $email = preg_replace('/[\@\.]/', '', $email);
            $topic_arn = $sns->createTopic(array(
                'Name' => 'omr_response'.$email
            ));

            $item = $result["Item"];
            foreach($item["Feature"] as $feature) {
                if($feature == $_POST['feature']) {
                    foreach($item["Material"] as $material) {
                        if($material == $_POST['material']) {
                            foreach($item['Max Speed'] as $max_speed) {
                                if ($max_speed > $_POST['speed']) {
                                    foreach($item['Min Speed'] as $min_speed) {
                                        if($min_speed < $_POST['speed']) {
                                            $key = $marshaler->marshalJson('
                                                {
                                                    "Manufacturer": "' . $table . '",
                                                    "Tool_Id": "' . $tool . '"
                                                }
                                            ');
                                            $eav = $marshaler->marshalJson('
                                                {   
                                                    ":m": ["' . $_POST['material'] . '"],
                                                    ":s": ["' . $_POST['speed'] . '"]
                                                }
                                            ');
                                            $params = [
                                                'TableName' => $main_table,
                                                'Key' => $key,
                                                'UpdateExpression' => 'set Material = :m, Speed = :s',
                                                'ExpressionAttributeValues' => $eav,
                                                'ReturnValues' => 'UPDATED_NEW'
                                            ];
                                            try {
                                                $result = $dynamodb->updateItem($params);
                                                echo "Updated item.\n";
                                                print_r($result['Attributes']);
                                                $message = "Your process has been verified and the OMR database has been updated! Enjoy!";

                                            } catch (DynamoDbException $e) {
                                                echo "Unable to update item:\n";
                                                echo $e->getMessage() . "\n";
                                                $message = "Oh No! Something went wrong with the database! Please Resubmit.";
                                            }
                                        } else {
                                            $message = "The speed you specified exceeds the manufacturer specs";
                                        }
                                    }
                                } else {
                                    $message = "The speed you specified exceeds the manufacturer specs";
                                }
                            }
                        } else {
                            $message = "The material you specified DNE in our database. We'll get back you on that!";
                        }
                    }
                } else {
                    $message = "The feature you require DNE in our database. We'll get back to you on that!";
                }
            }
            $sns->publish(array(
                'TopicArn' => $topic_arn["TopicArn"],
                'Message' => $message,
                'Subject' => "OMR Database Update: $tool"
            ));
            $body .= "<h2>Data Submitted! Depending on the quality of the data provided, you can expect the OMR database 
            to be updated as soon as you receive notification that the verification process is complete</h2>";
        }
    } catch (DynamoDbException $e) {
        echo $e->getMessage() . "\n";
    }
}

$page = generatePage($body, $title);
echo $page;
