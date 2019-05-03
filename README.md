

# IPP
An Internet Printing Protocol (IPP) PHP Client Implementation.  This implements the raw IPP protocol defined in [RFC2911](https://tools.ietf.org/html/rfc2911) & [RFC2910](https://tools.ietf.org/html/rfc2910) and will work with any IPP printer or IPP print server such as CUPS.

The goals of this implementation is to follow the IPP specification as closely as possible and offer a raw interface to that protocol in a form that is as simple as possible.

**Please note the current version is in development and does not have a stable release.  A stable release is planned soon.**

**Current Limitations**
 - Does not support ipps:// (encryption not supported, will be added soon)
 - Need to implement better testing with PHPUnit

### Table of Contents
 - [Installation](#installation)
 - [Usage](#usage)
 - [Printer Object & Methods](#printer-object-and-methods)
   - [Method `printJob`](#method-printjob)
   - [Method `printURI`](#method-printuri)
   - [Method `validateJob`](#method-validateJob)
   - [Method `createJob`](#method-createjob)
   - [Method `getPrinterAttributes`](#method-getprinterattributes)
   - [Method `getJobs`](#method-getjobs)
   - [Method `pausePrinter`](#method-pauseprinter)
   - [Method `resumePrinter`](#method-resumeprinter)
   - [Method `purgeJobs`](#method-purgejobs)
 - [Job Object & Methods](#job-object-and-methods)
 - [Printer URIs](#printer-uris)
 - [Print Job Attributes](#print-job-attributes)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation
The easiest way is to use composer and add obray/IPP to the require section:
```JSON
"require": {
    "obray/IPP": "dev-master"
}
```

Then just run `composer install` or `composer update`. Alternatively you can clone the repo or down the source 
code and use it the way that best suites you use case. 

## Usage

```PHP
$printer = new /obray/IPP/Printer(
  {printer-uri},
  {username},
  {password}
);
$response = $printer->printJob({raw document}, {attributes});
```

## Printer Object and Methods
The printer object defines a printer based on a specified URI.  When a method is called on a printer it will attempt to connect and send the request and interpret the response.

### Printer Constructor
Create a printer object by specifing the URI for the printer the credentials if needed.  Once you have a printer you can call it's methods.
###### Usage:
```PHP
$printer = new /obray/IPP/Printer(
  {printer-uri},
  {username},   // optional
  {password}    // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| printer-uri | yes | The printer uri depends a lot on what printer or print server you are attempting to print to.  For more information see [Printer URIs](#printer-uris) section. |
| username | no | If your printer or print server needs to authenticate supply the username here |
| password | no | If your printer or print server needs to authenticate supply the password here |

#  
### Method `printJob`
[RFC 2911 3.2.1](https://tools.ietf.org/html/rfc2911#section-3.2.1): This _REQUIRED_ operation allows a client to submit a print job with only one document and supply the document data (rather than just a reference to the data).  See Section 15 for the suggested steps for processing create operations and their Operation and Job Template attributes.
###### Usage:
```PHP
$response = $printer->printJob(
  {raw document},
  {request-id},  // optional
  {[attributes]} // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document | yes | Document to be sent to the printer. |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `PrintURI`
**NOT IMPLEMENTED YET**

RFC 2911 3.2.2: This _OPTIONAL_ operation is identical to the Print-Job operation (section 3.2.1) except that a client supplies a URI reference to the document data using the "document-uri" (uri) operation attribute (in Group 1) rather than including the document data itself.  Before returning the response, the Printer MUST validate that the Printer supports the retrieval method (e.g., http, ftp, etc.) implied by the URI, and MUST check for valid URI syntax.  If the client-supplied URI scheme is not supported, i.e. the value is not in the Printer object’s "referenced-uri-scheme-supported" attribute, the Printer object MUST reject the request and return the ’client-error-uri-scheme-not-supported’ status code.

#  
### Method `validateJob`
[RFC 2911 3.2.3](https://tools.ietf.org/html/rfc2911#section-3.2.3): This _REQUIRED_ operation is similar to the Print-Job operation (section 3.2.1) except that a client supplies no document data and the Printer allocates no resources (i.e., it does not create a new Job object).  This operation is used only to verify capabilities of a printer object against whatever attributes are supplied by the client in the Validate-Job request.  By using the Validate-Job operation a client can validate that an identical Print-Job operation (with the document data) would be accepted. The Validate-Job operation also performs the same security negotiation as the Print-Job operation (see section 8), so that a client can check that the client and Printer object security requirements can be met before performing a Print-Job operation.

###### Usage:
```PHP
$response = $printer->validateJob({request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |

#  
### Method `createJob`
**NOT IMPELMENTED YET**

[RFC 2911 3.2.4](https://tools.ietf.org/html/rfc2911#section-3.2.4): This _OPTIONAL_ operation is similar to the [Print-Job](#method-printjob) operation except that in the Create-Job request, a client does not supply document data or any reference to document data.  Also, the client does not supply any of the "document-name", "document-format", "compression", or "document-natural-language" operation attributes.  This operation is followed by one or more Send-Document or Send-URI operations.  In each of those operation requests, the client OPTIONALLY supplies the "document-name", "document-format", and "document-natural-language" attributes for each document in the multi-document Job object.

#  
### Method `getPrinterAttributes`
[RFC 2911 3.2.5](https://tools.ietf.org/html/rfc2911#section-3.2.5): This _REQUIRED_ operation allows a client to request the values of the attributes of a Printer object. In the request, the client supplies the set of Printer attribute names and/or attribute group names in which the requester is interested.  In the response, the Printer object returns a corresponding attribute set with the appropriate attribute values filled in.

###### Usage:
```PHP
$response = $printer->getPrinterAttributes({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `getJobs`
[RFC 2911 3.2.6](https://tools.ietf.org/html/rfc2911#section-3.2.6): This _REQUIRED_ operation allows a client to retrieve the list of Job objects belonging to the target Printer object.  The client may also supply a list of Job attribute names and/or attribute group names.  A group of Job object attributes will be returned for each Job object that is returned.

###### Usage:
```PHP
$response = $printer->getJobs({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `pausePrinter`
[RFC 2911 3.2.7](https://tools.ietf.org/html/rfc2911#section-3.2.7): This _OPTIONAL_ operation allows a client to stop the Printer object from scheduling jobs on all its devices.  Depending on implementation, the Pause-Printer operation MAY also stop the Printer from processing the current job or jobs.  Any job that is currently being printed is either stopped as soon as the implementation permits

###### Usage:
```PHP
$response = $printer->pausePrinter({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `resumePrinter`
[RFC 2911 3.2.8](https://tools.ietf.org/html/rfc2911#section-3.2.8): This operation allows a client to resume the Printer object scheduling jobs on all its devices.  The Printer object MUST remove the ’paused’ and ’moving-to-paused’ values from the Printer object’s "printer-state-reasons" attribute, if present.  If there are no other reasons to keep a device paused (such as media-jam), the IPP Printer is free to transition itself to the ’processing’ or ’idle’ states, depending on whether there are jobs to be processed or not, respectively, and the device(s) resume processing jobs.

_If the Pause-Printer operation is supported, then the Resume-Printer operation MUST be supported, and vice-versa._

###### Usage:
```PHP
$response = $printer->resumePrinter({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `purgeJobs`
[RFC 2911 3.2.9](https://tools.ietf.org/html/rfc2911#section-3.2.9): This _OPTIONAL_ operation allows a client to remove all jobs from an IPP Printer object, regardless of their job states, including jobs in the Printer object’s Job History (see Section 4.3.7.2).  After a Purge-Jobs operation has been performed, a Printer object MUST return no jobs in subsequent Get-Job-Attributes and Get-Jobs responses (until new jobs are submitted).
     
Whether the Purge-Jobs (and Get-Jobs) operation affects jobs that were submitted to the device from other sources than the IPP Printer object in the same way that the Purge-Jobs operation affects jobs that were submitted to the IPP Printer object using IPP, depends on implementation, i.e., on whether the IPP protocol is being used as a universal management protocol or just to manage IPP jobs, respectively.

###### Usage:
```PHP
$response = $printer->purgeJobs({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |


## Job Object and Methods

coming soon (not implemented)

## Printer URIs
Each printer object is identified by a unique URI that must be supplied to the Printer constructor.  Here are a few examples of 
possible printer URIs:

If you specify IPP as the protocol then it assumes port 631 in most cases
  >ipp://hostname/ipp/
  
  >ipp://hostname/ipp/port1

If your printer doesn't support IPP directly often you can setup a CUPS server, install the printers there, and then send
all of your requests to the CUPS server.  An example of that would be something like this:

  >ipp://localhost/printers/{printer-name-in-cups}
  
In this case CUPS would be installed on localhost and listening on port 631 (default IPP port).

## Print Job Attributes
