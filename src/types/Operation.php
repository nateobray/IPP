<?php
namespace obray\ipp\types;

class Operation extends \obray\ipp\types\Enum
{
    const printJob = 0x0002;
    const printURI = 0x0003;
    const validateJob = 0x0004;
    const createJob = 0x0005;
    const sendDocument = 0x0006;
    const sendURI = 0x0007;
    const cancelJob = 0x0008;
    const getJobAttributes = 0x0009;
    const getJobs = 0x000A;
    const getPrinterAttributes = 0x000B;
    const holdJob = 0x000C;
    const releaseJob = 0x000D;
    const restartJob = 0x000E;
    const pausePrinter = 0x0010;
    const resumePrinter = 0x0011;
    const purgeJobs = 0x0012;

    // CUPS specific operations
    const cupsGetDefault = 0x4001;
    const cupsGetPrinters = 0x4002;
    const cupsAddModifyPrinter = 0x4003;
    const cupsDeletePrinter = 0x4004;
    const cupsGetClasses = 0x4005;
    const cupsAddModifyClasses = 0x4006;
    const cupsDeleteClass = 0x4007;
    const cupsSetDefault = 0x400A;
    const cupsGetDevices = 0x400B;
    const cupsGetPPDs = 0x400C;
}