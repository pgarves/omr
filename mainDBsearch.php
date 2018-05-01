<?php
require_once("support.php");
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


$alert = "";
$body = $form.$alert;
$page = generatePage($body, $title);
echo $page;