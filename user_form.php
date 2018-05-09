<?php

require_once("support.php");
$form = <<<EOBODY
<header> Form Submission </header>
<form action="{$_SERVER['PHP_SELF']}" method="post">
        <p>
            <strong>Material: </strong><input type="text" name="material" id="material" required="required"/>
        </p>
        <p>
            <strong>Tool: </strong><input type="text" name="tool" id="tool" required="required"/>
        </p>
        <p>
            <strong>Phone Number: </strong><input type="phone" name="phone" id="phone" required="required"/>
            We will notify you when the given material and tool have been reviewed
        </p> 
        <p>
            <input type="submit" name="submit" value="Submit Data" />
        </p>  
EOBODY;

if(isset($_POST['submit'])) {

}
