<?php

$loader = require_once 'vendor/autoload.php';

try{
    $printer = new \obray\ipp\Printer("ipp://localhost/printers/DevPrinter", "nate");
    $response = $printer->getPrinterAttributes();
} catch(\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}

print_r(json_encode($response, JSON_PRETTY_PRINT));

try {
    $printer = new \obray\ipp\Printer("ipp://localhost/printers/DevPrinter", "nate");
    $response = $printer->printJob("Hello World!", 1, array(
        "document-format" => 'application/vnd.cups-raw'
    ));
} catch(\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}
print_r(json_encode($response, JSON_PRETTY_PRINT));

try {
    $job = new \obray\ipp\Job(
    $response->jobAttributes->{'job-uri'},
    $response->jobAttributes->{'job-id'}->getAttributeValue(),
    "nate"
);
$response = $job->getJobAttributes();
} catch (\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}
print_r(json_encode($response, JSON_PRETTY_PRINT));
exit();

