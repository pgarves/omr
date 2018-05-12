<?php
session_start();
require 'aws/aws-autoloader.php';
require_once("support.php");

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$title="Search";
$form = <<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
        <p>
            <strong>Manufacturer: </strong><input type="text" name="manufacturer" id="manufacturer" required="required"/>
            Try: Champion
        </p>
        <p>
            <strong>Tool: </strong><input type="text" name="tool" id="tool" required="required"/>
            Try: 705CT-1/8 or 105-1/16
        </p> 
        <p>
            <input type="submit" name="submit" value="Search" />
        </p>  
    </form>
EOBODY;


$error = "";


if(isset($_POST['submit'])) {

    $sdk = new Aws\Sdk([
        'region' => 'us-east-1',
        'version'  => 'latest'
    ]);

    $dynamodb = $sdk->createDynamoDb();

    $marshaler = new Marshaler();

    $table = 'OMR_main';
    $manufacturer = $_POST['manufacturer'];
    $tool = $_POST['tool'];


    $item = $marshaler->marshalJson('
        {
            "Manufacturer": "' . $manufacturer . '",
            "Tool_Id": "' . $tool . '"
        }
    ');

    $params = [
        'TableName' => $table,
        'Key' => $item
    ];

    try{
       $result = $dynamodb->getItem($params);
       if ($result["Item"] == NULL) {
           $error .= "<h2>There is no data associated with Manufacturer: $manufacturer and Tool: $tool </h2>";
       } else {
           $_SESSION['result'] = $result["Item"];
           header("Location: results.php");
       }
    } catch(DynamoDbException $e) {
        $error .= $e->getMessage();
    }
}
$body = $form.$error;
$page = generatePage($body, $title);
echo $page;