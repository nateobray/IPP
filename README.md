

# IPP
[![Latest Release](https://img.shields.io/github/v/release/nateobray/IPP)](https://github.com/nateobray/IPP/releases)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![CI](https://img.shields.io/github/actions/workflow/status/nateobray/IPP/ci.yml?branch=master)](https://github.com/nateobray/IPP/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

An Internet Printing Protocol (IPP) PHP Client Implementation.  This implements the raw IPP protocol defined in [RFC2911](https://tools.ietf.org/html/rfc2911), [RFC2910](https://tools.ietf.org/html/rfc2910), [RFC8011](https://tools.ietf.org/html/rfc8011), [RFC3995](https://datatracker.ietf.org/doc/html/rfc3995), and [RFC3996](https://datatracker.ietf.org/doc/html/rfc3996), and will work with any IPP printer or IPP print server such as CUPS.

The goals of this implementation is to follow the IPP specification as closely as possible and offer a raw interface to that protocol in a form that is as simple as possible to use.

## Table of Contents

 - [Installation](#installation)
 - [Usage](#usage)
 - [Testing](#testing)
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
   - [Method `identifyPrinter`](#method-identifyprinter)
   - [Printer Administration Methods](#printer-administration-methods)
   - [Method `getDefault` (CUPS)](#method-getdefault-cups)
   - [Method `getPrinters` (CUPS)](#method-getprinters-cups)
   - [Method `getClasses` (CUPS)](#method-getclasses-cups)
   - [Method `setPrinterAttributes`](#method-setprinterattributes)
   - [Method `createPrinterSubscription`](#method-createprintersubscription)
   - [Method `getSubscriptions`](#method-getsubscriptions)
   - [Method `getNotifications` (Printer)](#method-getnotifications-printer)
 - [Subscription Object & Methods](#subscription-object-and-methods)
   - [Method `getSubscriptionAttributes`](#method-getsubscriptionattributes)
   - [Method `renewSubscription`](#method-renewsubscription)
   - [Method `cancelSubscription`](#method-cancelsubscription)
   - [Method `getNotifications` (Subscription)](#method-getnotifications-subscription)
 - [Document Object & Methods](#document-object-and-methods)
   - [Method `getDocumentAttributes`](#method-getdocumentattributes)
   - [Method `setDocumentAttributes`](#method-setdocumentattributes)
   - [Method `cancelDocument`](#method-canceldocument)
 - [Job Object & Methods](#job-object-and-methods)
   - [Method `sendDocument`](#method-senddocument)
   - [Method `sendURI`](#method-senduri)
   - [Method `cancelJob`](#method-canceljob)
   - [Method `getJobAttributes`](#method-getjobattributes)
   - [Method `holdJob`](#method-holdjob)
   - [Method `releaseJob`](#method-releasejob)
   - [Method `restartJob`](#method-restartjob)
   - [Method `moveJob` (CUPS)](#method-movejob-cups)
   - [Method `authenticateJob` (CUPS)](#method-authenticatejob-cups)
   - [Method `setJobAttributes`](#method-setjobattributes)
   - [Method `createJobSubscription`](#method-createjobsubscription)
   - [Job Administration Methods](#job-administration-methods)
 - [Exceptions](#exceptions)
 - [Printer URIs](#printer-uris)
 - [Project Status](#project-status)

## Installation
Install the stable release with Composer:

```bash
composer require obray/ipp:^1.2
```

This library currently supports PHP `8.1+` and is tested locally on PHP `8.1`, `8.2`, `8.3`, and `8.4`.

If you need unreleased changes from the main branch, require the development version explicitly instead.

## Usage
The most basic way of using this implementation is to create a `Printer` object and call `printJob` method like this:
```PHP
$printer = new \obray\ipp\Printer(
  {printer-uri},
  {username}, // optional
  {password},    // optional
  {curlOptions} // optional
);
$response = $printer->printJob({raw document}, {attributes});
```
Depending on the printer and the document you are trying to print the above may not give you the results you desire (i.e. printing PDF as plain text, or a black page, etc).  Printers often have only specific document formats they will print.  To find out which formats your printer supports list the printer attributes like so:

```PHP
$printer = new \obray\ipp\Printer(
  {printer-uri},
  {username}, // optional
  {password},    // optional
  {curlOptions} // optional
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
        "attributes-natural-language": "en"
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
$printer = new \obray\ipp\Printer(
  {printer-uri},
  {username}, // optional
  {password}, // optional
  {curlOptions}, // optional
);
$attributes = $printer->printJob(
   {raw document},
   123, // optional request ID
   [
     'document-format': 'application/pdf'
   ]
);
```

## Testing
The repository includes unit tests, local printer integration tests, and recorded real-printer fixture replay tests.

```bash
composer test
composer test:unit
composer test:integration
composer test:fixtures
composer record:fixtures
```

See `docs/local_printer_testing.md` for local CUPS and fixture recording details.

### Connecting Directly to Printers OR CUPS

This library supports direct connections to network printers and printing through a CUPS server.

To print directly to a network printer, use its IPP URI, for example:
```
ipp://network.hostname.of.printer
ipp://network.hostname.of.printer/ipp
ipps://network.hostname.of.printer/ipp/print
```
For CUPS queues, the URI is usually one of these forms:

```
ipp://hostname.of.cups/printers/{queue-name}
ipp://localhost/printers/{queue-name}
ipps://hostname.of.cups/printers/{queue-name}
```

For USB printers and other locally attached devices, install the printer in CUPS and print through the exported queue URI.

For secure transport, pass an `ipps://` printer URI. The library maps `ipp://` to HTTP on port `631` and `ipps://` to HTTPS on port `443`, with TLS handled by the underlying PHP cURL/OpenSSL stack. For self-signed or private CA deployments, supply the appropriate cURL SSL options when constructing the `Printer` or `Job` object.

For the full API surface, see [Printer Object and Methods](#printer-object-and-methods) and [Job Object and Methods](#job-object-and-methods).

## Printer Object and Methods
The printer object defines a printer based on a specified URI. When a method is called on a printer, it connects, sends the request, and interprets the response.

### Printer Constructor
Create a printer object by specifying the URI for the printer and any credentials if needed. Once you have a printer you can call its methods.
###### Usage:
```PHP
$printer = new \obray\ipp\Printer(
  {printer-uri},
  {username},    // optional
  {password},    // optional
  {curlOptions}, // optional
  {requestClass} // optional
);
```
| Parameter | Required | Description                                                                                                                                                   |
| --------- | -------- |---------------------------------------------------------------------------------------------------------------------------------------------------------------|
| printer-uri | yes | The printer URI depends on the printer or print server you are targeting. For more information, see [Printer URIs](#printer-uris). |
| username | no | Username for printer or print server authentication. |
| password | no | Password for printer or print server authentication. |
| curlOptions | no | cURL options, for example `[['key' => CURLOPT_SSL_VERIFYPEER, 'value' => false], ['key' => CURLOPT_SSL_VERIFYHOST, 'value' => false]]` |
| requestClass | no | Override the request implementation class. This is mainly useful for testing or custom transports. |

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
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |

#  
### Method `PrintURI`
RFC 2911 3.2.2: This _OPTIONAL_ operation is identical to the [Print-Job](#method-printjob) operation except that a client supplies a URI reference to the document data using the "document-uri" (uri) operation attribute (in Group 1) rather than including the document data itself.  Before returning the response, the Printer MUST validate that the Printer supports the retrieval method (e.g., http, ftp, etc.) implied by the URI, and MUST check for valid URI syntax.  If the client-supplied URI scheme is not supported, i.e. the value is not in the Printer object’s "referenced-uri-scheme-supported" attribute, the Printer object MUST reject the request and return the ’client-error-uri-scheme-not-supported’ status code.

###### Usage:
```PHP
$response = $printer->printURI({document-uri}, {request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document-uri | yes | URI reference to the document data. |
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |

#  
### Method `validateJob`
[RFC 2911 3.2.3](https://tools.ietf.org/html/rfc2911#section-3.2.3): This _REQUIRED_ operation is similar to the [Print-Job](#method-printjob) operation except that a client supplies no document data and the Printer allocates no resources (i.e., it does not create a new Job object).  This operation is used only to verify capabilities of a printer object against whatever attributes are supplied by the client in the Validate-Job request.  By using the Validate-Job operation a client can validate that an identical Print-Job operation (with the document data) would be accepted. The Validate-Job operation also performs the same security negotiation as the Print-Job operation, so that a client can check that the client and Printer object security requirements can be met before performing a Print-Job operation.

###### Usage:
```PHP
$response = $printer->validateJob({request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |

#  
### Method `createJob`
[RFC 2911 3.2.4](https://tools.ietf.org/html/rfc2911#section-3.2.4): This _OPTIONAL_ operation is similar to the [Print-Job](#method-printjob) operation except that in the Create-Job request, a client does not supply document data or any reference to document data.  Also, the client does not supply any of the "document-name", "document-format", "compression", or "document-natural-language" operation attributes.  This operation is followed by one or more Send-Document or Send-URI operations.  In each of those operation requests, the client OPTIONALLY supplies the "document-name", "document-format", and "document-natural-language" attributes for each document in the multi-document Job object.

###### Usage:
```PHP
$response = $printer->createJob({request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |

#  
### Method `getPrinterAttributes`
[RFC 2911 3.2.5](https://tools.ietf.org/html/rfc2911#section-3.2.5): This _REQUIRED_ operation allows a client to request the values of the attributes of a Printer object. In the request, the client supplies the set of Printer attribute names and/or attribute group names in which the requester is interested.  In the response, the Printer object returns a corresponding attribute set with the appropriate attribute values filled in.  By default this method will get all the available attributes.

###### Usage:
```PHP
$response = $printer->getPrinterAttributes({request-id}, {[requested-attributes]}, {document-format});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |
| requested-attributes | no | An array of printer attribute names or group names to request. |
| document-format | no | MIME media type used to request format-specific printer attribute values. |

#  
### Method `getJobs`
[RFC 2911 3.2.6](https://tools.ietf.org/html/rfc2911#section-3.2.6): This _REQUIRED_ operation allows a client to retrieve the list of Job objects belonging to the target Printer object.  The client may also supply a list of Job attribute names and/or attribute group names (by default it includes all group names).  A group of Job object attributes will be returned for each Job object that is returned.

###### Usage:
```PHP
$response = $printer->getJobs({request-id}, {which-jobs}, {limit}, {my-jobs}, {[requested-attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |
| which-jobs | no | Filter jobs, typically `completed` or `not-completed`. |
| limit | no | Maximum number of jobs to return. |
| my-jobs | no | If `true`, only return jobs for the authenticated user. |
| requested-attributes | no | An array of job attribute names or group names to request. |

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
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |

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
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |

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
| request-id | no | A unique identifier for the print request. If omitted, the default is `1`. |


### Printer Administration Methods
The following methods require operator/administrator privileges on the target printer. All accept only an optional `request-id` parameter.

| Method | Operation | Description |
| ------ | --------- | ----------- |
| `enablePrinter(int $requestId = 1)` | Enable-Printer | Allow the printer to accept new jobs. |
| `disablePrinter(int $requestId = 1)` | Disable-Printer | Stop the printer from accepting new jobs. |
| `pausePrinterAfterCurrentJob(int $requestId = 1)` | Pause-Printer-After-Current-Job | Pause once the current job finishes. |
| `holdNewJobs(int $requestId = 1)` | Hold-New-Jobs | Hold all newly submitted jobs. |
| `releaseHeldNewJobs(int $requestId = 1)` | Release-Held-New-Jobs | Release jobs previously held by `holdNewJobs`. |
| `deactivatePrinter(int $requestId = 1)` | Deactivate-Printer | Deactivate the printer (stop processing). |
| `activatePrinter(int $requestId = 1)` | Activate-Printer | Reactivate a deactivated printer. |
| `restartPrinter(int $requestId = 1)` | Restart-Printer | Restart the printer service. |
| `shutdownPrinter(int $requestId = 1)` | Shutdown-Printer | Shut down the printer service. |
| `startPrinter(int $requestId = 1)` | Start-Printer | Start a previously shut-down printer. |

#  
### Method `getDefault` (CUPS)
CUPS extension ([cups.org](https://www.cups.org/doc/spec-ipp.html)): Returns the attributes of the default printer configured on the CUPS server. Use this with a CUPS server URI (e.g. `ipp://localhost/`) rather than a specific printer queue URI.

###### Usage:
```PHP
$response = $printer->getDefault({request-id}, {[requested-attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the request. If omitted, the default is `1`. |
| requested-attributes | no | An array of printer attribute names to return. If omitted, all attributes are returned. |

#  
### Method `getPrinters` (CUPS)
CUPS extension: Returns attributes for all printers configured on the CUPS server. Use this with a CUPS server URI (e.g. `ipp://localhost/`) rather than a specific printer queue URI.

###### Usage:
```PHP
$response = $printer->getPrinters({request-id}, {[requested-attributes]}, {limit});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the request. If omitted, the default is `1`. |
| requested-attributes | no | An array of printer attribute names to return. |
| limit | no | Maximum number of printers to return. |

#  
### Method `getClasses` (CUPS)
CUPS extension: Returns attributes for all printer classes configured on the CUPS server. Use this with a CUPS server URI (e.g. `ipp://localhost/`) rather than a specific printer queue URI.

###### Usage:
```PHP
$response = $printer->getClasses({request-id}, {[requested-attributes]}, {limit});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | A unique identifier for the request. If omitted, the default is `1`. |
| requested-attributes | no | An array of printer attribute names to return. |
| limit | no | Maximum number of classes to return. |

### Method `identifyPrinter`
[RFC 8011 4.2.22](https://tools.ietf.org/html/rfc8011#section-4.2.22): Causes the printer to perform one or more human-perceptible identification actions (e.g. flash an LED, sound a tone, display a message) so a user can locate the physical device.

###### Usage:
```PHP
$response = $printer->identifyPrinter({request-id}, {[identify-actions]}, {message});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| identify-actions | no | Array of action keywords, e.g. `['flash', 'sound']`. Printer-specific; common values: `flash`, `sound`, `display`, `speak`. |
| message | no | Text message to display on the printer. |

### Method `setPrinterAttributes`
[RFC 8011 4.2.19](https://tools.ietf.org/html/rfc8011#section-4.2.19): Modifies one or more printer object attributes. Pass an associative array of attribute names to values; standard attributes are type-checked automatically and unknown attributes fall back to type inference.

###### Usage:
```PHP
$response = $printer->setPrinterAttributes({attributes}, {request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| attributes | yes | Associative array of printer attribute names to set (`['printer-info' => 'My printer', ...]`). |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

### Method `createPrinterSubscription`
[RFC 3995 §7](https://datatracker.ietf.org/doc/html/rfc3995#section-7): Creates a printer-level event notification subscription. Returns a response whose `subscriptionAttributes` property contains the assigned `notify-subscription-id`. Use the returned id to construct a [`Subscription`](#subscription-object-and-methods) object for subsequent polling or management operations.

###### Usage:
```PHP
$response = $printer->createPrinterSubscription({subscription-attributes}, {request-id});
$subGroup = $response->subscriptionAttributes[0];
$subscriptionId = (int)(string) $subGroup->{'notify-subscription-id'};
$subscription = new \obray\ipp\Subscription($printerUri, $subscriptionId, $user, $password);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| subscription-attributes | yes | Associative array of subscription template attributes, e.g. `['notify-pull-method' => 'ippget', 'notify-events' => ['all'], 'notify-lease-duration' => 300]`. |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `getSubscriptions`
[RFC 3995 §9](https://datatracker.ietf.org/doc/html/rfc3995#section-9): Returns the list of active subscriptions on the printer. The response `subscriptionAttributes` array contains one entry per subscription.

###### Usage:
```PHP
$response = $printer->getSubscriptions({request-id}, {notify-job-id}, {requested-attributes}, {my-subscriptions});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| notify-job-id | no | If set, only subscriptions belonging to this job id are returned. |
| requested-attributes | no | Array of subscription attribute names to include in the response. |
| my-subscriptions | no | If `true`, only subscriptions belonging to the authenticated user are returned. |

#  
### Method `getNotifications` (Printer)
[RFC 3996 §5](https://datatracker.ietf.org/doc/html/rfc3996#section-5): Polls for pending event notifications across one or more subscriptions. The response `eventNotificationAttributes` array contains one `EventNotificationAttributes` group (tag `0x07`) per pending event. `notify-get-interval` in `operationAttributes` tells the client how many seconds to wait before polling again.

###### Usage:
```PHP
$response = $printer->getNotifications({subscription-ids}, {request-id}, {sequence-numbers}, {wait});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| subscription-ids | yes | Integer subscription id or array of integer subscription ids to poll. |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| sequence-numbers | no | Integer or array of integers — the last sequence number received per subscription. Events at or before this number are not re-delivered. |
| wait | no | If `true`, the printer may hold the response open until an event arrives (long-poll). |

## Subscription Object and Methods

The `Subscription` object wraps a `(printer-uri, subscription-id)` pair and provides methods to inspect, renew, cancel, and poll an individual subscription.

### Subscription Constructor

```PHP
$subscription = new \obray\ipp\Subscription(
  {printer-uri},
  {subscription-id},
  {username},    // optional
  {password},    // optional
  {curlOptions}  // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| printer-uri | yes | The URI of the printer that owns the subscription. |
| subscription-id | yes | The numeric subscription id returned by `createPrinterSubscription` or `createJobSubscription`. |
| username | no | Username for authentication. |
| password | no | Password for authentication. |
| curlOptions | no | cURL options array. |

#  
### Method `getSubscriptionAttributes`
[RFC 3995 §11.2.4](https://datatracker.ietf.org/doc/html/rfc3995#section-11.2.4): Returns all attributes of this subscription.

###### Usage:
```PHP
$response = $subscription->getSubscriptionAttributes({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `renewSubscription`
[RFC 3995 §11.2.5](https://datatracker.ietf.org/doc/html/rfc3995#section-11.2.5): Extends the subscription lease. If `$leaseDuration` is omitted the printer applies its default duration.

###### Usage:
```PHP
$response = $subscription->renewSubscription({request-id}, {lease-duration});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| lease-duration | no | New lease duration in seconds. If omitted, the printer's default is used. |

#  
### Method `cancelSubscription`
[RFC 3995 §11.2.6](https://datatracker.ietf.org/doc/html/rfc3995#section-11.2.6): Cancels and removes this subscription.

###### Usage:
```PHP
$response = $subscription->cancelSubscription({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `getNotifications` (Subscription)
[RFC 3996 §5](https://datatracker.ietf.org/doc/html/rfc3996#section-5): Polls for pending events on this subscription. The response `eventNotificationAttributes` array contains one group per event. Pass `$lastSequenceNumber` to suppress events you have already processed.

###### Usage:
```PHP
$response = $subscription->getNotifications({request-id}, {last-sequence-number}, {wait});
foreach ($response->eventNotificationAttributes as $event) {
    echo $event->{'notify-subscribed-event'} . PHP_EOL;
}
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| last-sequence-number | no | The sequence number of the last event already processed. Events at or before this number are not re-delivered. |
| wait | no | If `true`, the printer may hold the response open until an event arrives (long-poll). |

## Document Object and Methods

The `Document` object (PWG5100.5) wraps a `(printer-uri, job-id, document-number)` tuple and provides methods to inspect, modify, and cancel an individual document within a multi-document job.

### Document Constructor

```PHP
$document = new \obray\ipp\Document(
  {printer-uri},
  {job-id},          // integer job-id or full job-uri string
  {document-number}, // 1-based document sequence number
  {username},        // optional
  {password},        // optional
  {curlOptions}      // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| printer-uri | yes | The URI of the printer that owns the job. |
| job-id | yes | The numeric job-id or full job-uri string. |
| document-number | yes | The 1-based document sequence number within the job. |
| username | no | Username for authentication. |
| password | no | Password for authentication. |
| curlOptions | no | cURL options array. |

#  
### Method `getDocumentAttributes`
[PWG5100.5 §3.3.1](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf): Returns the attributes of this document.

###### Usage:
```PHP
$response = $document->getDocumentAttributes({request-id}, {requested-attributes});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id _(default 1)_ |
| requested-attributes | no | Array of attribute names to retrieve. If omitted, all supported attributes are returned. |

#  
### Method `setDocumentAttributes`
[PWG5100.5 §3.3.2](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf): Modifies attributes on this document.

###### Usage:
```PHP
$response = $document->setDocumentAttributes({attributes}, {request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| attributes | yes | Associative array of document attribute names and values to set. |
| request-id | no | Client request id _(default 1)_ |

#  
### Method `cancelDocument`
[PWG5100.5 §3.3.3](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf): Cancels this document.

###### Usage:
```PHP
$response = $document->cancelDocument({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id _(default 1)_ |

## Job Object and Methods

### Job Constructor
Create a job object when you already know the target printer URI and either the numeric `job-id` or the full `job-uri`.

###### Usage:
```PHP
$jobById = new \obray\ipp\Job(
  {printer-uri},
  {job-id},
  {username},    // optional
  {password},    // optional
  {curlOptions}, // optional
  {requestClass} // optional
);

$jobByUri = new \obray\ipp\Job(
  {printer-uri},
  {job-uri},
  {username},    // optional
  {password},    // optional
  {curlOptions}, // optional
  {requestClass} // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| printer-uri | yes | Printer URI associated with the job. |
| job-id / job-uri | yes | Either the numeric IPP job id or the full IPP job URI. |
| username | no | Username for printer or print server authentication. |
| password | no | Password for printer or print server authentication. |
| curlOptions | no | cURL options to use for requests sent by this job object. |
| requestClass | no | Override the request implementation class. This is mainly useful for testing or custom transports. |

#  
### Method `sendDocument`
[RFC 2911 3.3.1](https://tools.ietf.org/html/rfc2911#section-3.3.1): This _OPTIONAL_ operation allows a client to create a multi-document Job object that is initially "empty" (contains no documents).  In the Create-Job response, the Printer object returns the Job object's URI (the "job-uri" attribute) and the Job object's 32-bit identifier (the "job-id" attribute).  For each new document that the client desires to add, the client uses a Send-Document operation.  Each Send-Document Request contains the entire stream of document data for one document.

###### Usage:
```PHP
$response = $job->sendDocument({document}, {last-document}, {request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document | no | Document data to append to the multi-document job. |
| last-document | no | Boolean flag indicating whether this is the final document in the job. |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| attributes | no | Optional document operation attributes such as `document-format`. |

#  
### Method `sendURI`
[RFC 2911 3.3.2](https://tools.ietf.org/html/rfc2911#section-3.3.2): This _OPTIONAL_ operation is identical to the Send-Document operation (see section 3.3.1) except that a client MUST supply a URI reference ("document-uri" operation attribute) rather than the document data itself.  If a Printer object supports this operation, clients can use both Send-URI or Send-Document operations to add new documents to an existing multi-document Job object.  However, if a client needs to indicate that the previous Send-URI or Send-Document was the last document,  the client MUST use the Send-Document operation with no document data and the "last-document" flag set to 'true' (rather than using a Send-URI operation with no "document-uri" operation attribute).

The Printer object MUST validate the syntax and URI scheme of the supplied URI before returning a response, just as in the Print-URI operation.  The IPP Printer MAY validate the accessibility of the document as part of the operation or subsequently (see section 3.2.2).

###### Usage:
```PHP
$response = $job->sendURI({document-uri}, {last-document}, {request-id}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document-uri | yes | URI reference to the document data. |
| last-document | no | Boolean flag indicating whether this is the final document in the job. |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| attributes | no | Optional document operation attributes such as `document-format`. |

#  
### Method `cancelJob`
[RFC 2911 3.3.3](https://tools.ietf.org/html/rfc2911#section-3.3.3): This _REQUIRED_ operation allows a client to cancel a Print Job from the time the job is created up to the time it is completed, canceled, or aborted.  Since a Job might already be printing by the time a Cancel-Job is received, some media sheet pages might be printed before the job is actually terminated.

###### Usage:
```PHP
$response = $job->cancelJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `getJobAttributes`
[RFC 2911 3.3.4](https://tools.ietf.org/html/rfc2911#section-3.3.4): This _REQUIRED_ operation allows a client to request the values of attributes of a Job object and it is almost identical to the Get-Printer-Attributes operation (see section 3.2.5).  The only differences are that the operation is directed at a Job object rather than a Printer object, there is no "document-format" operation attribute used when querying a Job object, and the returned attribute group is a set of Job object attributes rather than a set of Printer object attributes.

###### Usage:
```PHP
$response = $job->getJobAttributes({request-id}, {[requested-attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| requested-attributes | no | An array of job attribute names or group names to request. |

#  
### Method `holdJob`
[RFC 2911 3.3.5](https://tools.ietf.org/html/rfc2911#section-3.3.5): This _OPTIONAL_ operation allows a client to hold a pending job in the queue so that it is not eligible for scheduling.  If the Hold-Job operation is supported, then the Release-Job operation MUST be supported, and vice-versa.  The OPTIONAL "job-hold-until" operation attribute allows a client to specify whether to hold the job indefinitely or until a specified time period, if supported.

###### Usage:
```PHP
$response = $job->holdJob({request-id}, {job-hold-until});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| job-hold-until | no | Optional hold keyword such as `indefinite` or an implementation-supported time period. |

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
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `restartJob`
[RFC 2911 3.3.7](https://tools.ietf.org/html/rfc2911#section-3.3.7): This OPTIONAL operation allows a client to restart a job that is retained in the queue after processing has completed

###### Usage:
```PHP
$response = $job->restartJob({request-id}, {job-hold-until});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |
| job-hold-until | no | Optional hold keyword to apply when restarting the job. |

#  
### Method `moveJob` (CUPS)
CUPS extension: Moves a job to a different printer queue. The destination printer URI is passed as an operation attribute (`job-printer-uri`). The `Printer` object must be the source printer queue and the `Job` must belong to that queue.

###### Usage:
```PHP
$response = $job->moveJob({destination-printer-uri}, {request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| destination-printer-uri | yes | IPP URI of the destination printer queue (e.g. `ipp://localhost/printers/other-queue`). |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

#  
### Method `authenticateJob` (CUPS)
CUPS extension: Authenticates a held job that requires credentials before it can be released for printing. This is used when a job was held because authentication was required (job state reason: `authentication-needed`).

###### Usage:
```PHP
$response = $job->authenticateJob({request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

### Method `setJobAttributes`
[RFC 8011 4.2.20](https://tools.ietf.org/html/rfc8011#section-4.2.20): Modifies one or more attributes of an existing job. Pass an associative array of attribute names to values; standard job template attributes are type-checked and unknown attributes fall back to type inference.

###### Usage:
```PHP
$response = $job->setJobAttributes({attributes}, {request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| attributes | yes | Associative array of job attribute names to set (`['job-priority' => 50, ...]`). |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

### Method `createJobSubscription`
[RFC 3995 §7](https://datatracker.ietf.org/doc/html/rfc3995#section-7): Creates a job-level event notification subscription scoped to this job. Returns a response whose `subscriptionAttributes` property contains the assigned `notify-subscription-id`. Use the returned id to construct a [`Subscription`](#subscription-object-and-methods) object.

###### Usage:
```PHP
$response = $job->createJobSubscription({subscription-attributes}, {request-id});
$subGroup = $response->subscriptionAttributes[0];
$subscriptionId = (int)(string) $subGroup->{'notify-subscription-id'};
$subscription = new \obray\ipp\Subscription($printerUri, $subscriptionId, $user, $password);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| subscription-attributes | yes | Associative array of subscription template attributes, e.g. `['notify-pull-method' => 'ippget', 'notify-events' => ['job-completed']]`. |
| request-id | no | Client request id, will be passed back in the response _(default 1)_ |

### Method `getDocuments`
[PWG5100.5 §3.2.1](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf): Returns document attribute groups for all documents within this job. The response `documentAttributes` array contains one `DocumentAttributes` object per document.

###### Usage:
```PHP
$response = $job->getDocuments({request-id}, {requested-attributes});
foreach ($response->documentAttributes as $doc) {
    echo $doc->{'document-number'} . ': ' . $doc->{'document-name'} . PHP_EOL;
}
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| request-id | no | Client request id _(default 1)_ |
| requested-attributes | no | Array of attribute names to retrieve for each document. |

#  
### Method `createDocument`
[PWG5100.5 §3.2.2](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf): Creates a new Document object within this job. The printer assigns a `document-number` and returns it in the response. Follow up with `sendDocument` or `sendURI` to supply the actual document data.

###### Usage:
```PHP
$response = $job->createDocument({document-attributes}, {request-id});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document-attributes | no | Associative array of document attributes (e.g. `['document-name' => 'Report', 'document-format' => 'application/pdf']`). |
| request-id | no | Client request id _(default 1)_ |

### Job Administration Methods
Advanced job operations for reordering, suspending, and managing active jobs. All accept only an optional `request-id` parameter unless noted.

| Method | Operation | Description |
| ------ | --------- | ----------- |
| `cancelCurrentJob(int $requestId = 1)` | Cancel-Current-Job | Cancel the job currently being printed. |
| `suspendCurrentJob(int $requestId = 1)` | Suspend-Current-Job | Suspend the job currently being printed. |
| `resumeJob(int $requestId = 1)` | Resume-Job | Resume a previously suspended job. |
| `promoteJob(int $requestId = 1)` | Promote-Job | Move this job ahead of other pending jobs. |
| `reprocessJob(int $requestId = 1)` | Reprocess-Job | Re-queue a completed job for reprinting. |
| `scheduleJobAfter(int\|string $jobAfter, int $requestId = 1)` | Schedule-Job-After | Schedule this job to print after `$jobAfter` (job-id integer or job-uri string). |

#  
## Exceptions

The library throws typed exceptions so callers can distinguish network failures from protocol errors.

| Exception | Description |
| --------- | ----------- |
| `\obray\ipp\exceptions\NetworkError` | Thrown when the cURL transport layer fails (connection refused, DNS failure, TLS error, timeout, etc.). Carries the printer URI, cURL error code, and cURL error message. |
| `\obray\ipp\exceptions\IppStatusException` | Thrown when the printer returns a non-successful IPP status code (any code ≥ `0x0100`, i.e. client-error or server-error). Carries the full decoded response so callers can inspect unsupported attributes or other details. |
| `\obray\ipp\exceptions\AuthenticationError` | Thrown when the HTTP layer returns 401 Unauthorized. |
| `\obray\ipp\exceptions\HTTPError` | Thrown when the HTTP layer returns any non-200, non-401 status code. |
| `\obray\ipp\exceptions\IPPDecodeError` | Thrown when the IPP response binary cannot be decoded (malformed response). |

```PHP
use obray\ipp\exceptions\IppStatusException;
use obray\ipp\exceptions\NetworkError;

try {
    $response = $printer->printJob($document);
} catch (IppStatusException $e) {
    // $e->getMessage()      — e.g. "IPP error from ipp://…: client-error-document-format-not-supported"
    // $e->getStatusCode()   — \obray\ipp\types\StatusCode instance
    // $e->getResponse()     — full \obray\ipp\transport\IPPPayload for inspection
    echo (string) $e->getStatusCode(); // "client-error-document-format-not-supported"
} catch (NetworkError $e) {
    // $e->getPrinterURI()   — the printer URI that was targeted
    // $e->getCurlErrorCode() — the cURL error code (CURLE_* constant value)
}
```

#   
## Printer URIs
Each printer object is identified by a unique URI that must be supplied to the Printer constructor.  Here are a few examples of 
possible printer URIs:

If you use the `ipp` scheme, the default port is `631`. If you use the `ipps` scheme, the default port is `443`. You can also specify the port explicitly:
  >ipp://hostname/ipp/
 
  >ipp://hostname:port/ipp/

  >ipps://hostname/ipp/print

  >ipps://hostname:port/ipp/print
  
  >ipp://hostname/ipp/port1

If your printer does not support IPP directly, you can usually install it in CUPS and send requests to the CUPS queue instead. For example:

  >ipp://localhost/printers/{printer-name-in-cups}
  
In this case CUPS would be installed on localhost and listening on port 631 (default IPP port).

## Project Status

Core IPP/1.1 support (RFC 2910, RFC 2911, RFC 8011), administrative operations (RFC 3380, RFC 3998), and event notification subscriptions with pull-delivery (RFC 3995, RFC 3996) are complete. Newer IPP/PWG extensions are tracked in the matrix below.

**DONE** in the matrix means the spec's required attribute types, encoding, and operation payloads are implemented and covered by unit tests. It does **not** mean every operation has been exercised against real hardware — fixture-backed interoperability tests exist for the core IPP/1.1 and subscription operations, but less-common extensions (notably PWG5100.5 Document Object ops, which most printers including CUPS do not implement) are spec-tested only.

| IETF or PWG Specification | obray\ipp | IPP/1.1 | IPP/2.0 | IPP/2.1 | IPP/2.2 |
| ------------------------- | --------- | ------- | ------- | ------- | ------- |
| [PWG5100.1 - IPP Finishings 2.1](http://ftp.pwg.org/pub/pwg/candidates/cs-ippfinishings21-20170217-5100.1.pdf)                 |   DONE    |         |   REQ   |   REQ   |   REQ   |
| [PWG5100.2 - “output-bin” attribute extension](https://ftp.pwg.org/pub/pwg/candidates/cs-ippoutputbin10-20010207-5100.2.pdf)                 |   DONE    |         |   REQ   |   REQ   |   REQ   |
| [PWG5100.3 - Production Printing Attributes – Set1](https://ftp.pwg.org/pub/pwg/candidates/cs-ippprodprint10-20010212-5100.3.pdf)                 |   DONE    |         |         |   REQ   |   REQ   |
| [PWG5100.5 - Document Object](https://ftp.pwg.org/pub/pwg/candidates/cs-ippdocobject10-20031031-5100.5.pdf)                 |   DONE    |         |         |         |   REQ   |
| [PWG5100.6 - Page Overrides](https://ftp.pwg.org/pub/pwg/candidates/cs-ipppageoverride10-20031031-5100.6.pdf)                 |   DONE    |         |         |   REC   |   REQ   |
| [PWG5100.7 - IPP Job Extensions v2.0](https://ftp.pwg.org/pub/pwg/candidates/cs-ippjobext20-20190816-5100.7.pdf)                 |   DONE    |         |         |   REQ   |   REQ   |
| [PWG5100.8 - “-actual” attributes](https://ftp.pwg.org/pub/pwg/candidates/cs-ippactuals10-20030313-5100.8.pdf)                 |   DONE    |         |         |         |   REQ   |
| [PWG5100.9 - Printer State Extensions v1.0](https://ftp.pwg.org/pub/pwg/candidates/cs-ippstate10-20090731-5100.9.pdf)                 |   DONE    |         |  RECMD  |   REQ   |   REQ   |
| [PWG5100.11 - Job and Printer Extensions – Set 2](https://ftp.pwg.org/pub/pwg/candidates/cs-ippjobprinterext10-20101030-5100.11.pdf)                |   DONE    |         |         |  RECMD  |   REQ   |
| [PWG5101.1 - PWG Media Standardized Names 2.0](https://ftp.pwg.org/pub/pwg/candidates/cs-pwgmsn20-20130328-5101.1.pdf)                 |   DONE    |         |   REQ   |   REQ   |   REQ   |
| [PWG5107.2 - PWG Command Set Format for IEEE 1284 Device ID v1.0](https://ftp.pwg.org/pub/pwg/candidates/cs-pmp1284cmdset10-20100531-5107.2.pdf)                 |   DONE    |         |  RECMD  |  RECMD  |   REQ   |
| [RFC2910 - Encoding and Transport](https://datatracker.ietf.org/doc/html/rfc2910)                   |   DONE    |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC2911 - Model and Semantics](https://datatracker.ietf.org/doc/html/rfc2911)                   |   DONE    |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC3380 - Job and Printer Set Operations](https://datatracker.ietf.org/doc/html/rfc3380)                   |   DONE    |         |         |   REQ   |   REQ   |
| [RFC3382 - The 'collection' attribute syntax](https://datatracker.ietf.org/doc/html/rfc3382)                   |   DONE    |         |         |   REQ   |   REQ   |
| [RFC3510 - IPP URL Scheme](https://datatracker.ietf.org/doc/html/rfc3510)                   |   DONE    |   REQ   |   REQ   |   REQ   |   REQ   |
| [RFC3995 - Event Notifications and Subscriptions](https://datatracker.ietf.org/doc/html/rfc3995)                   |   DONE    |         |         |   REQ   |   REQ   |
| [RFC3996 - The 'ippget' Delivery Method for Event Notifications](https://datatracker.ietf.org/doc/html/rfc3996)                   |   DONE    |         |         |   REQ   |   REQ   |
| [RFC3998 - Job and Printer Administrative Operations](https://datatracker.ietf.org/doc/html/rfc3998)                   |   DONE    |         |         |   REQ   |   REQ   |
| [RFC5246 - The Transport Layer Security (TLS) Protocol](https://datatracker.ietf.org/doc/html/rfc5246)                   |  via PHP/cURL/OpenSSL  |         |  RECMD  |  RECMD  |   REQ   |
| [RFC7472 - HTTPS Transport Binding and the 'ipps' URI Scheme](https://datatracker.ietf.org/doc/html/rfc7472)                   |   DONE    |         |  RECMD  |  RECMD  |   REQ   |
| [RFC8011 - Model and Semantics (supersedes RFC 2911)](https://datatracker.ietf.org/doc/html/rfc8011)                   |   DONE    |   REQ   |   REQ   |   REQ   |   REQ   |
