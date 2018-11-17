<?php

$loader = require_once 'vendor/autoload.php';

$printer = new \obray\ipp\Printer("ipp://10.5.2.82/printers/devprinter", "nate");
$printer->printJob("Hello World!");
