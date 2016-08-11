<?php
/*
* Load the engine header and base template
* Contains variables, access control,
*/
require_once "headerFiles/engineHeader.php";
templates::display("header");
?>

<h2><a href="{local var="baseDirectory"}">{local var="currentDisplayObjectTitle"}</a></h2>


<?php templates::display('footer'); ?>
