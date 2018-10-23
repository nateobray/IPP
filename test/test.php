<?php

$loader = require_once 'vendor/autoload.php';

$operationAttributes = new \obray\OperationAttributes();
$operationAttributes->target = "ipp://my-printer/print/";
$operationAttributes->userName = "nate";
