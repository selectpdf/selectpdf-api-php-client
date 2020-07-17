# HTML To PDF API - PHP Client

SelectPdf HTML To PDF Online REST API is a professional solution that lets you create PDF from web pages and raw HTML code in your applications. The API is easy to use and the integration takes only a few lines of code.

## Features

* Create PDF from any web page or html string.
* Full html5/css3/javascript support.
* Set PDF options such as page size and orientation, margins, security, web page settings.
* Set PDF viewer options and PDF document information.
* Create custom headers and footers for the pdf document.
* Hide web page elements during the conversion.
* Automatically generate bookmarks during the html to pdf conversion.
* Support for partial page conversion.
* Easy integration, no third party libraries needed.
* Works in all programming languages.
* No installation required.

Sign up for for free to get instant API access to SelectPdf [HTML to PDF API](https://selectpdf.com/html-to-pdf-api/).

## Installation

Download [selectpdf-api-php-client-1.0.0.zip](https://github.com/selectpdf/selectpdf-api-php-client/releases/download/1.0.0/selectpdf-api-php-client-1.0.0.zip), unzip it and require SelectPdf.Api.php in your code.

OR

Install SelectPdf PHP Client for Online API from Packagist: [SelectPdf API on Packagist](https://packagist.org/packages/selectpdf/selectpdf-api-client).

```
composer require selectpdf/selectpdf-api-client
```

OR

Clone [selectpdf-api-php-client](https://github.com/selectpdf/selectpdf-api-php-client) from Github and require SelectPdf.Api.php in your code.

```
git clone https://github.com/selectpdf/selectpdf-api-php-client
cd selectpdf-api-php-client
```

## Sample Code

```
<?php
require("SelectPdf.Api.php");

$url = 'https://selectpdf.com';
$outFile = "test.pdf";

try {
    $api = new SelectPdf\Api\HtmlToPdfClient("Your key here");

    $api
        ->setPageSize(SelectPdf\Api\PageSize::A4)
        ->setPageOrientation(SelectPdf\Api\PageOrientation::Portrait)
        ->setMargins(0)
        ->setNavigationTimeout(30)
        ->setShowPageNumbers(false)
        ->setPageBreaksEnhancedAlgorithm(true)
    ;

    echo ("Starting conversion ...");
    
    $api->convertUrlToFile($url, $outFile);

    echo ("Conversion finished successfully!");

    $usage = new \SelectPdf\Api\UsageClient("Your key here");
    $info = $usage->getUsage(true);
    echo("Conversions left this month: " . $info["available"]);

}
catch (Exception $ex) {
	echo "An error occurred: " . $ex;
}
?>
```
