<?php

session_start();
require_once("support.php");
$title = "OMR Database Results";
$body = "Results";
$result = $_SESSION['result'];
//$body .= "<header> Table </header>";
//$body .= "<table border='1px'>";
//$body .= "<thead>";
//foreach ($result as  )
$body .= print($result["Item"]);

$body .= "<input type='button' onclick='user_form.php' value='New Process' />";

$page = generatePage($body, $title);
echo $page;