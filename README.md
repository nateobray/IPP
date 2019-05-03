[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

# IPP
An Internet Printing Protocol PHP Implementation.  This implements the raw IPP protocol and will work with any IPP printer
or IPP print server.

**Please note the current version is in development and does not have a stable release.  A stable release is planned soon.**

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

## Constructor & Methods
A breif description of the constructor it's parameters and the available methods in this implementation of IPP.

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
