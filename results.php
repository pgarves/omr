<?php

session_start();
require_once("support.php");
$title = "OMR Database Results";
$result = $_SESSION['result'];
$body = "<p>";
$body .= "<table border='1px'>";
$body .= "<thead><th>Manufacturer</th><th>Tool ID</th><th>Feature</th><th>Material</th><th>Speed</th></thead>";
$body .= "<tbody>";
$body .= "<tr>";
foreach ($result["Manufacturer"] as $item) {
    $body .= "<td>$item</td>";
};
foreach ($result["Tool_Id"] as $item) {
    $body .= "<td>$item</td>";
}
foreach ($result["Feature"] as $item) {
    $body .= "<td>$item</td>";
}
$body .= "<td>";
foreach ($result["Material"] as $container) {
    foreach($container as $list) {
        foreach($list as $item) {
            $body .= "$item<br />";
        }
    }
}
$body .= "</td><td>";
foreach ($result["Speed"] as $container) {
    foreach($container as $list) {
        foreach($list as $item) {
            $body .= "$item<br />";
        }
    }
}
$body .= "</td>";
$body .= "</tr>";
$body .= "</tbody></table></p>";
$body .= "<input type='button' onclick='user_form.php' value='New Process' />";

$page = generatePage($body, $title);
echo $page;