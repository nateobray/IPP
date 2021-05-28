

# IPP
An Internet Printing Protocol (IPP) PHP Client Implementation.  This implements the raw IPP protocol defined in [RFC2911](https://tools.ietf.org/html/rfc2911) & [RFC2910](https://tools.ietf.org/html/rfc2910) and will work with any IPP printer or IPP print server such as CUPS.

The goals of this implementation is to follow the IPP specification as closely as possible and offer a raw interface to that protocol in a form that is as simple as possible to use.

**PLEASE NOTE: the current version is in development and does not have a stable release.  A stable release is planned soon (see [project status](#project-status) section).**

## Table of Contents

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
   - [Method `sendDocument`](#method-senddocument)
   - [Method `sendURI`](#method-senduri)
   - [Method `cancelJob`](#method-canceljob)
   - [Method `getJobAttributes`](#method-getjobattributes)
   - [Method `holdJob`](#method-holdjob)
   - [Method `releaseJob`](#method-releasejob)
   - [Method `restartJob`](#method-restartjob)
 - [Printer URIs](#printer-uris)
 - [Project Status](#project-status)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation
The easiest way is to use composer and add obray/IPP to the require section:
```JSON
"require": {
    "obray/ipp": "dev-master"
}
```

Then just run `composer install` or `composer update`. Alternatively you can clone the repo or download the source 
code and use it however you see fit. 

## Usage
The most basic way of using this implementation is to create a `Printer` object and call `printJob` method like this:
```PHP
$printer = new \obray\IPP\Printer(
  {printer-uri},
  {username}, // optional
  {password}  // optional
);
$response = $printer->printJob({raw document}, {attributes});
```
Depending on the printer and the document you are trying to print the above may not give you the results you desire (i.e. printing PDF as plain text, or a black page, etc).  Printers often have only specific document formats they will print.  To find out which formats your printer supports list the printer attributes like so:

```PHP
$printer = new \obray\IPP\Printer(
  {printer-uri},
  {username}, // optional
  {password}  // optional
);
$attributes = $printer->getPrinterAttributes();
```

This should give you a structure something like (encoded to JSON):

```JSON
{
    "versionNumber": "1.1",
    "requestId": 1,
    "statusCode": "successful-ok",
    "operationAttributes": {
        "attributes-charset": "utf-8",
        "attributes-natural-language": "en-us"
    },
    "jobAttributes": null,
    "printerAttributes": {
    
        ...
        
        "document-format-supported": [
            "application\/octet-stream",
            "image\/urf",
            "image\/pwg-raster",
            "application\/pdf",
            "image\/jpeg",
            "application\/postscript",
            "application\/vnd.hp-PCL",
            "text\/plain"
        ],
        
        ...
        
    }
}
```

To print a PDF to this printer you would do something like this:

```PHP
$printer = new \obray\IPP\Printer(
  {printer-uri},
  {username}, // optional
  {password}, // optional
  
);
$attributes = $printer->print(
   123, // optional request ID
   [
     'document-format': 'application/pdf'
   ]
);
```
### Connecting Directly to Printers OR CUPS

This library supports directly connecting to network printers and printing documents or printing to a CUPS server or compouter with CUPS installed.

To connection an print directly to a network printer it usually just a matter of getting it's host name and using it like one of the following:
```
ipp://network.hostname.of.printer
ipp://network.hostname.of.printer/ipp
```
To use this library with cups, it works exactly the same except usually the URL is something like: `ipp://hostname.of.cups/ipp/{printer-name-goes-here}`

To use this with a USB printer or other kinds of printers, you'll need to use CUPS.  Install the printer on a computer that has CUPS install and then you can print to the printer using this library through CUPS.

To see what other methods are available see the below documentation on [Printer Object and Methods](#printer-object-and-methods) and [Job Object and Methods](#job-object-and-methods)

## Printer Object and Methods
The printer object defines a printer based on a specified URI.  When a method is called on a printer it will attempt to connect and send the request and interpret the response.

### Printer Constructor
Create a printer object by specifing the URI for the printer the credentials if needed.  Once you have a printer you can call it's methods.
###### Usage:
```PHP
$printer = new \obray\IPP\Printer(
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
[RFC 2911 3.2.1](https://tools.ietf.org/html/rfc2911#section-3.2.1): This _REQUIRED_ operation allows a client to submit a print job with only one document and supply the document data (rather than just a reference to the data).

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

RFC 2911 3.2.2: This _OPTIONAL_ operation is identical to the [Print-Job](#method-printjob) operation except that a client supplies a URI reference to the document data using the "document-uri" (uri) operation attribute (in Group 1) rather than including the document data itself.  Before returning the response, the Printer MUST validate that the Printer supports the retrieval method (e.g., http, ftp, etc.) implied by the URI, and MUST check for valid URI syntax.  If the client-supplied URI scheme is not supported, i.e. the value is not in the Printer object’s "referenced-uri-scheme-supported" attribute, the Printer object MUST reject the request and return the ’client-error-uri-scheme-not-supported’ status code.

#  
### Method `validateJob`
[RFC 2911 3.2.3](https://tools.ietf.org/html/rfc2911#section-3.2.3): This _REQUIRED_ operation is similar to the [Print-Job](#method-printjob) operation except that a client supplies no document data and the Printer allocates no resources (i.e., it does not create a new Job object).  This operation is used only to verify capabilities of a printer object against whatever attributes are supplied by the client in the Validate-Job request.  By using the Validate-Job operation a client can validate that an identical Print-Job operation (with the document data) would be accepted. The Validate-Job operation also performs the same security negotiation as the Print-Job operation, so that a client can check that the client and Printer object security requirements can be met before performing a Print-Job operation.

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
[RFC 2911 3.2.5](https://tools.ietf.org/html/rfc2911#section-3.2.5): This _REQUIRED_ operation allows a client to request the values of the attributes of a Printer object. In the request, the client supplies the set of Printer attribute names and/or attribute group names in which the requester is interested.  In the response, the Printer object returns a corresponding attribute set with the appropriate attribute values filled in.  By default this method will get all the available attributes.

###### Usage:
```PHP
$response = $printer->getPrinterAttributes({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `getJobs`
[RFC 2911 3.2.6](https://tools.ietf.org/html/rfc2911#section-3.2.6): This _REQUIRED_ operation allows a client to retrieve the list of Job objects belonging to the target Printer object.  The client may also supply a list of Job attribute names and/or attribute group names (by default it includes all group names).  A group of Job object attributes will be returned for each Job object that is returned.

###### Usage:
```PHP
$response = $printer->getJobs({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request, if not specified it will pass 0 |

#  
### Method `pausePrinter`
[RFC 2911 3.2.7](https://tools.ietf.org/html/rfc2911#section-3.2.7): This _OPTIONAL_ operation allows a client to stop the Printer object from scheduling jobs on all its devices.  Depending on implementation, the Pause-Printer operation MAY also stop the Printer from processing the current job or jobs.  Any job that is currently being printed is either stopped as soon as the implementation permits or is completed, depending on implementation.  The Printer object MUST still accept create operations to create new jobs, but MUST prevent any jobs from entering the 'processing' state.

_If the Pause-Printer operation is supported, then the Resume-Printer operation MUST be supported, and vice-versa._

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

#  
### Method `sendDocument`
**NOT IMPLEMENTED YET**

[RFC 2911 3.3.1](https://tools.ietf.org/html/rfc2911#section-3.3.1): This _OPTIONAL_ operation allows a client to create a multi-document Job object that is initially "empty" (contains no documents).  In the Create-Job response, the Printer object returns the Job object's URI (the "job-uri" attribute) and the Job object's 32-bit identifier (the "job-id" attribute).  For each new document that the client desires to add, the client uses a Send-Document operation.  Each Send-Document Request contains the entire stream of document data for one document.

#  
### Method `sendURI`
**NOT IMPLEMENTED YET**

[RFC 2911 3.3.2](https://tools.ietf.org/html/rfc2911#section-3.3.2): This _OPTIONAL_ operation is identical to the Send-Document operation (see section 3.3.1) except that a client MUST supply a URI reference ("document-uri" operation attribute) rather than the document data itself.  If a Printer object supports this operation, clients can use both Send-URI or Send-Document operations to add new documents to an existing multi-document Job object.  However, if a client needs to indicate that the previous Send-URI or Send-Document was the last document,  the client MUST use the Send-Document operation with no document data and the "last-document" flag set to 'true' (rather than using a Send-URI operation with no "document-uri" operation attribute).

The Printer object MUST validate the syntax and URI scheme of the supplied URI before returning a response, just as in the Print-URI operation.  The IPP Printer MAY validate the accessibility of the document as part of the operation or subsequently (see section 3.2.2).

#  
### Method `cancelJob`
[RFC 2911 3.3.3](https://tools.ietf.org/html/rfc2911#section-3.3.3): This _REQUIRED_ operation allows a client to cancel a Print Job from the time the job is created up to the time it is completed, canceled, or aborted.  Since a Job might already be printing by the time a Cancel-Job is received, some media sheet pages might be printed before the job is actually terminated.

###### Usage:
```PHP
$response = $job->cancelJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 0)_ |

#  
### Method `getJobAttributes`
[RFC 2911 3.3.4](https://tools.ietf.org/html/rfc2911#section-3.3.4): This _REQUIRED_ operation allows a client to request the values of attributes of a Job object and it is almost identical to the Get-Printer-Attributes operation (see section 3.2.5).  The only differences are that the operation is directed at a Job object rather than a Printer object, there is no "document-format" operation attribute used when querying a Job object, and the returned attribute group is a set of Job object attributes rather than a set of Printer object attributes.

###### Usage:
```PHP
$response = $job->getJobAttributes({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 0)_ |

#  
### Method `holdJob`
[RFC 2911 3.3.5](https://tools.ietf.org/html/rfc2911#section-3.3.5): This _OPTIONAL_ operation allows a client to hold a pending job in the queue so that it is not eligible for scheduling.  If the Hold-Job operation is supported, then the Release-Job operation MUST be supported, and vice-versa.  The OPTIONAL "job-hold-until" operation attribute allows a client to specify whether to hold the job indefinitely or until a specified time period, if supported.

###### Usage:
```PHP
$response = $job->holdJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 0)_ |

#  
### Method `releaseJob`
[RFC 2911 3.3.6](https://tools.ietf.org/html/rfc2911#section-3.3.6): This OPTIONAL operation allows a client to release a previously held job so that it is again eligible for scheduling.  If the Hold-Job operation is supported, then the Release-Job operation MUST be supported, and vice-versa.
 
This operation removes the "job-hold-until" job attribute, if present, from the job object that had been supplied in the create or most recent Hold-Job or Restart-Job operation and removes its effect on the job.  The IPP object MUST remove the 'job-hold-until-specified' value from the job's "job-state-reasons" attribute, if present.

###### Usage:
```PHP
$response = $job->releaseJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 0)_ |

#  
### Method `restartJob`
[RFC 2911 3.3.7](https://tools.ietf.org/html/rfc2911#section-3.3.7): This OPTIONAL operation allows a client to restart a job that is retained in the queue after processing has completed

###### Usage:
```PHP
$response = $job->restartJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 0)_ |

#   
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

## Project Status

Currently this library does not have a stable release but when it does it will fully support IPP/1.1. The other version will be supported in future releases. Currently this is the status of this library compared to the requirements and recommendations of each version.

| IETF or PWG Specification | obray\ipp | IPP/1.1 | IPP/2.0 | IPP/2.1 | IPP/2.2 |
| ------------------------- | --------- | ------- | ------- | ------- | ------- |
| [PWG5100.1 - IPP Finishings 2.1](http://ftp.pwg.org/pub/pwg/candidates/cs-ippfinishings21-20170217-5100.1.pdf)                 |           |         |   REQ   |   REQ   |   REQ   |
| [PWG5100.2 - “output-bin” attribute extension](https://ftp.pwg.org/pub/pwg/candidates/cs-ippoutputbin10-20010207-5100.2.pdf)                 |           |         |   REQ   |   REQ   |   REQ   |
| [PWG5100.3 - Production Printing Attributes – Set1](https://ftp.pwg.org/pub/pwg/candidates/cs-ippprodprint10-20010212-5100.3.pdf)                 |           |         |         |   REQ   |   REQ   |
| [PWG5100.5 - Document Object](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf)                 |           |         |         |         |   REQ   |
| [PWG5100.6 - Page Overrides](https://ftp.pwg.org/pub/pwg/candidates/cs-ipppageoverride10-20031031-5100.6.pdf)                 |           |         |         |   REC   |   REQ   |
| [PWG5100.7 - IPP Job Extensions v2.0](https://ftp.pwg.org/pub/pwg/candidates/cs-ippjobext20-20190816-5100.7.pdf)                 |           |         |         |   REQ   |   REQ   |
| [PWG5100.8 - "-actual" attributes](https://ftp.pwg.org/pub/pwg/candidates/cs-ippactuals10-20030313-5100.8.pdf)                 |           |         |         |         |   REQ   |
| [PWG5100.9 - Printer State Extensions v1.0](https://ftp.pwg.org/pub/pwg/candidates/cs-ippstate10-20090731-5100.9.pdf)                 |           |         |  RECMD  |   REQ   |   REQ   |
| [PWG5100.11 - Job and Printer Extensions – Set 2](https://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf)                |           |         |         |  RECMD  |   REQ   |
| [PWG5101.1 - PWG Media Standardized Names 2.0](https://ftp.pwg.org/pub/pwg/candidates/cs-pwgmsn20-20130328-5101.1.pdf)                 |           |         |   REQ   |   REQ   |   REQ   |
| [PWG5107.2 - PWG Command Set Format for IEEE 1284 Device ID v1.0](https://ftp.pwg.org/pub/pwg/candidates/cs-pmp1284cmdset10-20100531-5107.2.pdf)                 |           |         |  RECMD  |  RECMD  |   REQ   |
| [RFC2910 - Encoding and Transport](https://datatracker.ietf.org/doc/html/rfc2910)                   |  TESTING  |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC2911 - Job and Printer Set Operations](https://datatracker.ietf.org/doc/html/rfc2911)                   |  TESTING  |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC3380 - Model and Semantics](https://datatracker.ietf.org/doc/html/rfc3380)                   |           |         |         |   REQ   |   REQ   |
| [RFC3382 - The 'collection' attribute syntax](https://datatracker.ietf.org/doc/html/rfc3382)                   |           |         |         |   REQ   |   REQ   |
| [RFC3510 - IPP URL Scheme](https://datatracker.ietf.org/doc/html/rfc3510)                   |    DONE    |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC3995 - Event Notifications and Subscriptions](https://datatracker.ietf.org/doc/html/rfc3995)                   |           |         |         |   REQ   |   REQ   |
| [RFC3996 - The 'ippget' Delivery Method for Event Notifications](https://datatracker.ietf.org/doc/html/rfc3996)                   |           |         |         |   REQ   |   REQ   |
| [RFC3998 - Job and Printer Administrative Operations](https://datatracker.ietf.org/doc/html/rfc3998)                   |           |         |         |   REQ   |   REQ   |
| [RFC5246- The Transport Layer Security (TLS) Protocol](https://datatracker.ietf.org/doc/html/rfc5246)                   |           |         |  RECMD  |  RECMD  |   REQ   |
| [RFC7472 - HTTPS Transport Binding and the 'ipps' URI Scheme](https://datatracker.ietf.org/doc/html/rfc7472)                   |           |         |  RECMD  |  RECMD  |   REQ   |

