<?php

$loader = require_once 'vendor/autoload.php';

$printer = new \obray\ipp\Printer("ipp://localhost/printers/Tremonton-printer-01", "nate");
$response = $printer->getPrinterAttributes();

$printer = new \obray\ipp\Printer("ipp://localhost/printers/Tremonton-printer-01", "nate");
$response = $printer->printJob("Hello World!", 1, array(
    "media-col" => array(
        "media-source" => "tray-2"
    )
));
print_r(json_encode($response, JSON_PRETTY_PRINT));

$job = new \obray\ipp\Job(
    $response->jobAttributes->{'job-uri'},
    $response->jobAttributes->{'job-id'}->getAttributeValue(),
    "nate"
);
$response = $job->getJobAttributes();
print_r(json_encode($response, JSON_PRETTY_PRINT));
exit();

