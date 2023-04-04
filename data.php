<?php
require_once("./properties.php");
require_once("./getFormAction.php");
//*
$action = new getFormAction();

echo $data = $action->query();
?>
