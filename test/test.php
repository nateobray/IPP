<?php

$loader = require_once 'vendor/autoload.php';

$printerURI = "ipp://localhost:631/printers/label-01";

$operationAttributes = new \obray\ipp\OperationAttributes();
        
$payload = new \obray\ipp\transport\IPPPayload(
    new \obray\ipp\types\VersionNumber('1.1'),
    new \obray\ipp\types\Operation(\obray\ipp\types\Operation::CUPS_GET_PPD),
    new \obray\ipp\types\Integer(1),
    null,
    $operationAttributes
);
//print_r($payload);
$encodedPayload = $payload->encode();
$response =  \obray\ipp\Request::send($printerURI, $encodedPayload);
print_r(json_encode($response, JSON_PRETTY_PRINT));

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

