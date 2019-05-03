

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

#### Printer Constructor
###### Usage:
```PHP
$printer = new /obray/IPP/Printer(
  {printer-uri},
  {username}, // optional
  {password}  // optional
);
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| printer-uri | yes | The printer uri depends a lot on what printer or print server you are attempting to print to.  For more information see [Printer URIs](#printer-uris) section. |
| username | no | If your printer or print server needs to authenticate supply the username here |
| password | no | If your printer or print server needs to authenticate supply the password here |

#### Print Job
Takes a document and prints it to the printer return the response with the print job description.
###### Usage:
```PHP
$response = $printer->printJob({raw document}, {[attributes]});
```
| Parameter | Required | Description |
| --------- | -------- | ----------- |
| document | yes | Document to be sent to the printer. |
| attributes | no | An array of print job attributes.  For more information see [Print Job Attributes](#print-job-attributes) |

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
