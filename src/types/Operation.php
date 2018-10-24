<?php
namespace obray\ipp\types;

class Operation extends \obray\ipp\types\Enum implements \obray\ipp\interfaces\IPPTypeInterface
{
    const printJob = 2;
    const printURI = 3;
    const validateJob = 4;
    const createJob = 5;
    const sendDocument = 6;
    const sendURI = 7;
    const cancelJob = 8;
    const getJobAttributes = 9;
    const getJobs = 10;
    const getPrinterAttributes = 11;
    const holdJob = 12;
    const releaseJob = 13;
    const restartJob = 14;
    const pausePrinter = 15;
    const resumePrinter = 16;
    const purgeJobs = 17;
}