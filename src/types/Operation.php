<?php
namespace obray\ipp\types;

class Operation extends \obray\ipp\types\Enum
{
    // IPP/1.0 (RFC 2566) and IPP/1.1 (RFC 2911) Operation Codes:
    const PRINT_JOB = 0x0002;
    const PRINT_URI = 0x0003;
    const VALIDATE_JOB = 0x0004;
    const CREATE_JOB = 0x0005;
    const SEND_DOCUMENT = 0x0006;
    const SEND_URI = 0x0007;
    const CANCEL_JOB = 0x0008;
    const GET_JOB_ATTRIBUTES = 0x0009;
    const GET_JOBS = 0x000A;
    const GET_PRINTER_ATTRIBUTES = 0x000B;
    const HOLD_JOB = 0x000C;
    const RELEASE_JOB = 0x000D;
    const RESTART_JOB = 0x000E;

    // IPP/1.1 (RFC 2911) Extended Operation Codes:
    const PAUSE_PRINTER = 0x0010;
    const RESUME_PRINTER = 0x0011;
    const PURGE_JOBS = 0x0012;

    // IPP/2.0 (RFC 8010) Extended Operation Codes:
    const SET_PRINTER_ATTIRBUTES = 0x0013;
    const SET_JOB_ATTRIBUTES = 0x0014;
    const GET_PRINTER_SUPPORTED_VALUES = 0x0015;
    const CREATE_PRINTER_SUBSCRIPTION = 0x0016;
    const CREATE_JOB_SUBSCRIPTION = 0x0017;
    const GET_SUBSCRIPTION_ATTRIBUTES = 0x0018;
    const GET_SUBSCRIPTION = 0x0019;
    const RENEW_SUBSCRIPTION = 0x001A;
    const CANCEL_SUBSCRIPTION = 0x001B;
    const GET_NOTIFICATION = 0x001C;
    const SEND_NOTIFICATION = 0x001D;
    const GET_RESOURCE_ATTRIBUTES = 0x001E;
    const GET_RESOURCE_DATE = 0x001F;
    const GET_RESOURCES = 0x0020;
    const GET_PRINTER_SUPPORTED_FILES = 0x0021;
    const ENABLE_PRINTER = 0x0022;
    const DISABLE_PRINTER = 0x0023;
    const PAUSE_PRINTER_AFTER_CURRENT_JOB = 0x0024;
    const HOLD_NEW_JOBS = 0x0025;
    const RELEASE_HELD_NEW_JOBS = 0x0026;
    const DEACTIVATE_PRINTER = 0x0027;
    const ACTIVATE_PRINTER = 0x0028;
    const RESTART_PRINTER = 0x0029;
    const SHUTDOWN_PRINTER = 0x002A;
    const START_PRINTER = 0x002B;
    const REPROCESS_JOB = 0x002C;
    const CANCEL_CURRENT_JOB = 0x002D;
    const SUSPEND_CURRENT_JOB = 0x002E;
    const RESUME_JOB = 0x002F;
    const PROMOTE_JOB = 0x0030;

    // IPP/2.1 (RFC 3380) Operation Codes:
    const SCHEDULE_JOB_AFTER = 0x0031;

    const CANCEL_JOBS = 0x0038;
    const CANCEL_MY_JOBS = 0x0039;
    const CLOSE_JOB = 0x003b;

    // CUPS specific operations
    const CUPS_GET_DEFAULT = 0x4001;
    const CUPS_GET_PRINTERS = 0x4002;
    const CUPS_ADD_MODIFY_PRINTER = 0x4003;
    const CUPS_DELETE_PRINTER = 0x4004;
    const CUPS_GET_CLASSES = 0x4005;
    const CUPS_ADD_MODIFY_CLASSES = 0x4006;
    const CUPS_DELETE_CLASS = 0x4007;
    const CUPS_ACCEPT_JOBS = 0x4008;
    const CUPS_REJECT_JOBS = 0x4009;
    const CUPS_SET_DEFAULT = 0x400A;
    const CUPS_GET_DEVICES = 0x400B;
    const CUPS_GET_PPDS = 0x400C;
    const CUPS_MOVE_JBO = 0x400D;
    const CUPS_AUTHENTICATE_JOB = 0x400E;
    const CUPS_GET_PPD = 0x400F;
    const CUPS_GET_DOCUMENT = 0x4027;
    const CUPS_CREATE_LOCAL_PRINTER = 0x4028;
}