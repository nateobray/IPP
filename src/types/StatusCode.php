<?php
namespace obray\ipp\types;

class StatusCode extends \obray\ipp\types\Enum {
    
    // success codes
    const successful_ok = 0;
    const successful_ok_ignored_or_substituted_attributes = 1;
    const successful_ok_conflicting_attributes = 2;
    // error codes
    const client_error_bad_request = 0x0400;
    const client_error_forbidden = 0x0401;
    const client_error_not_authenticated = 0x0402;
    const client_error_not_authorized = 0x0403;
    const client_error_not_possible = 0x0404;
    const client_error_timeout = 0x0405;
    const client_error_not_found = 0x0406;
    const client_error_gone = 0x0407;
    const client_error_request_entity_too_large = 0x0408;
    const client_error_request_value_too_long = 0x0409;
    const client_error_document_format_not_supported = 0x040A;
    const client_error_attributes_or_values_not_supported = 0x040B;
    const client_error_uri_scheme_not_supported = 0x040C;
    const client_error_charset_not_supported = 0x040D;
    const client_error_conflicting_attributes = 0x040E;
    const client_error_compression_not_supported = 0x040F;
    const client_error_compression_error = 0x0410;
    const client_error_document_format_error = 0x0411;
    const client_error_document_access_error = 0x0412;
    // Server Error Status Codes
    const server_error_internal_error = 0x0500;
    const server_error_operation_not_supported = 0x0501;
    const server_error_service_unavailable = 0x0502;
    const server_error_version_not_supported = 0x0503;
    const server_error_device_error = 0x0504;
    const server_error_temporary_error = 0x0505;
    const server_error_not_accepting_jobs = 0x0506;
    const server_error_busy = 0x0507;
    const server_error_job_canceled = 0x0508;
    const server_error_multiple_document_jobs_not_supported = 0x0509;

    private $class;

    public function __construct($value)
    {
        parent::__construct($value);

        if( $value >= 0x0000 && $value <= 0x00FF ){
            $this->class = 'successful';
        } else if ($value >= 0x0100 && $value <= 0x01FF){
            $this->class = 'informational';
        } else if ($value >= 0x0200 && $value <= 0x02FF){
            $this->class = 'redirection';
        } else if ($value >= 0x0400 && $value <= 0x04FF){
            $this->class = 'client-error';
        } else if ($value >= 0x0500 && $value <= 0x05FF){
            $this->class = 'server-error';
        }
    }
    
}