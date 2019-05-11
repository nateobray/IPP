<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class StatusCodeTest extends TestCase
{
    /***
     * 
     * // success codes
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
     * 
     */

    public function testStatusServerErrorCodes()
    {
        $statusCode = new \obray\ipp\types\StatusCode(0x0500);
        $this->assertSame('server-error-internal-error', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0501);
        $this->assertSame('server-error-operation-not-supported', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0502);
        $this->assertSame('server-error-service-unavailable', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0503);
        $this->assertSame('server-error-version-not-supported', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0504);
        $this->assertSame('server-error-device-error', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0505);
        $this->assertSame('server-error-temporary-error', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0506);
        $this->assertSame('server-error-not-accepting-jobs', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0507);
        $this->assertSame('server-error-busy', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0508);
        $this->assertSame('server-error-job-canceled', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0509);
        $this->assertSame('server-error-multiple-document-jobs-not-supported', (string)$statusCode);
        $this->assertSame('server-error', $statusCode->getClass());
    }

    public function testStatusClientErrorCodes()
    {
        $statusCode = new \obray\ipp\types\StatusCode(0x0400);
        $this->assertSame('client-error-bad-request', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0401);
        $this->assertSame('client-error-forbidden', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0402);
        $this->assertSame('client-error-not-authenticated', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0403);
        $this->assertSame('client-error-not-authorized', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0404);
        $this->assertSame('client-error-not-possible', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0405);
        $this->assertSame('client-error-timeout', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0406);
        $this->assertSame('client-error-not-found', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0407);
        $this->assertSame('client-error-gone', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0408);
        $this->assertSame('client-error-request-entity-too-large', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0409);
        $this->assertSame('client-error-request-value-too-long', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040A);
        $this->assertSame('client-error-document-format-not-supported', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040B);
        $this->assertSame('client-error-attributes-or-values-not-supported', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040C);
        $this->assertSame('client-error-uri-scheme-not-supported', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040D);
        $this->assertSame('client-error-charset-not-supported', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040E);
        $this->assertSame('client-error-conflicting-attributes', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x040F);
        $this->assertSame('client-error-compression-not-supported', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0410);
        $this->assertSame('client-error-compression-error', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0411);
        $this->assertSame('client-error-document-format-error', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0412);
        $this->assertSame('client-error-document-access-error', (string)$statusCode);
        $this->assertSame('client-error', $statusCode->getClass());
    }

    public function testStatusSuccessCodes()
    {
        $statusCode = new \obray\ipp\types\StatusCode(0x0000);
        $this->assertSame('successful-ok', (string)$statusCode);
        $this->assertSame('successful', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0001);
        $this->assertSame('successful-ok-ignored-or-substituted-attributes', (string)$statusCode);
        $this->assertSame('successful', $statusCode->getClass());
        $statusCode = new \obray\ipp\types\StatusCode(0x0002);
        $this->assertSame('successful-ok-conflicting-attributes', (string)$statusCode);
        $this->assertSame('successful', $statusCode->getClass());
        
        
    }
}