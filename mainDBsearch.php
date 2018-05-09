<?php
session_start();
require 'aws/aws-autoloader.php';
require_once("support.php");

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$title="Search";
$form = <<<EOBODY
    <form action="{$_SERVER['PHP_SELF']}" method="post">
        <p>
            <strong>Material: </strong><input type="text" name="material" id="material" required="required"/>
        </p>
        <p>
            <strong>Tool: </strong><input type="text" name="tool" id="tool" required="required"/>
        </p> 
        <p>
            <input type="submit" name="submit" value="Search" />
        </p>  
    </form>
EOBODY;


$error = "";

if(isset($_POST['submit'])) {
    $sdk = new Aws\Sdk([
        'endpoint'   => 'http://localhost:8000',
        'region'   => 'us-west-2',
        'version'  => 'latest'
    ]);

    $dynamodb = $sdk->createDynamoDb();

    $marshaler = new Marshaler();

    $table = "OMR_Main";
    $material = $_POST['material'];
    $tool = $_POST['tool'];

    $item = $marshaler->marshalJson('
        {
            "material": ' . $material . ',
            "tool": ' . $tool . '
        }
    ');

    $params = [
        'TableName' => $table,
        'Item' => $item
    ];

    try{
       $result = $dynamodb->getItem($params);
       $_SESSION['result'] = $result["Item"];
       header("Location: results.php");
    } catch(DynamoDbException $e) {
        $error = "<h2>There is no data associated with Material: $material and Tool: $tool </h2>";
    }
}
$body = $form.$error;
$page = generatePage($body, $title);
echo $page;