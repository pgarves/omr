<?php
session_start();
require 'aws/aws-autoloader.php';
require_once("support.php");

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Credentials\CredentialProvider;

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
$provider = CredentialProvider::ini();
$provider = CredentialProvider::memoize($provider);

if(isset($_POST['submit'])) {
    $sdk = new Aws\Sdk([
        'version'  => 'latest',
        'region'   => 'us-east-1a',
        'credentials' => $provider
    ]);

    $dynamodb = $sdk->createDynamoDb();

    $marshaler = new Marshaler();

    $table = "OMR_main";
    $manufacturer = $_POST['manufacturer'];
    $tool = $_POST['tool'];

    $item = $marshaler->marshalJson('
        {
            "Manufacturer": "Champion",
            "Tool_Id": "105-1/16"
        }
    ');

    $params = [
        'TableName' => $table,
        'Key' => $item
    ];

    try{
       $result = $dynamodb->getItem($params);
       $_SESSION['result'] = $result["Item"];
       header("Location: results.php");
    } catch(DynamoDbException $e) {
        $error = "<h2>There is no data associated with Manufacturer: $manufacturer and Tool: $tool </h2>";
    }
}
$body = $form.$error;
$page = generatePage($body, $title);
echo $page;