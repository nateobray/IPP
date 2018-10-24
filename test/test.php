<?php

$loader = require_once 'vendor/autoload.php';

$operationAttributes = new \obray\ipp\OperationAttributes();
$operationAttributes->target = "ipp://my-printer/print/";
$operationAttributes->userName = "nate";

$printer = new \obray\ipp\Printer();
$printer->printJob("test",$operationAttributes);
