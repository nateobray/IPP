<?php

$loader = require_once 'vendor/autoload.php';

// binary test
$binary = pack("n",0x0002);
$hex = bin2hex($binary);
//print_r($hex."\n");
$unpacked = unpack("n",$binary);
//print_r($unpacked);
//exit();

$printer = new \obray\ipp\Printer("ipp://10.5.2.82/printers/devprinter", "nate");
// $printer->printJob("Hello World!");
$printer->pausePrinter();
