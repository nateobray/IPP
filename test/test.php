<?php

$loader = require_once 'vendor/autoload.php';

$operationAttributes = new \obray\ipp\OperationAttributes();
$operationAttributes->printerURI = "ipp://my-printer/print/";
$operationAttributes->requestingUserName = "nate";

$printer = new \obray\ipp\Printer();
$printer->printJob("test",$operationAttributes);
