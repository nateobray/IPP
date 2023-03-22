<?php

$loader = require_once 'vendor/autoload.php';

$printerURI = "ipp://127.0.0.1:631/printers/pdf";

try{
    $printer = new \obray\ipp\Printer($printerURI);
    $response = $printer->getPrinterAttributes();
} catch(\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}

print_r(json_encode($response, JSON_PRETTY_PRINT));
sleep(2);
 
try {
    
    $printer = new \obray\ipp\Printer($printerURI);
    $response = $printer->printJob("Hello World!", 1, array(
        "document-format" => 'text/plain'
    ));
} catch(\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}
print_r(json_encode($response, JSON_PRETTY_PRINT));

sleep(5);

try {
    print_r("Attempting to get job\n");
    $job = new \obray\ipp\Job(
        $printerURI,
        $response->jobAttributes->{'job-id'}->getAttributeValue(),
        null,
        null
    );
    print_r("Getting job attributes\n");
    $response = $job->getJobAttributes();
} catch (\Exception $e){
    print_r($e->getMessage() . "\n");
    exit();
}
print_r(json_encode($response, JSON_PRETTY_PRINT));
exit();

