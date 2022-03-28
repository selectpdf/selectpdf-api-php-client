# SelectPdf Online REST API - PHP Client

SelectPdf Online REST API is a professional solution for managing PDF documents online. It now has a dedicated, easy to use, PHP client library that can be setup in minutes.

## Installation

Download [selectpdf-api-php-client-1.4.0.zip](https://github.com/selectpdf/selectpdf-api-php-client/releases/download/1.4.0/selectpdf-api-php-client-1.4.0.zip), unzip it and require SelectPdf.Api.php in your code.

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

## HTML To PDF API - PHP Client

SelectPdf HTML To PDF Online REST API is a professional solution that lets you create PDF from web pages and raw HTML code in your applications. The API is easy to use and the integration takes only a few lines of code.

### Features

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

### Sample Code

```php
<?php
require("SelectPdf.Api.php");

$url = 'https://selectpdf.com';
$localFile = "Test.pdf";
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\HtmlToPdfClient($apiKey);

    // set parameters - see full list at https://selectpdf.com/html-to-pdf-api/
    $client
        // main properties

        ->setPageSize(SelectPdf\Api\PageSize::A4) // PDF page size
        ->setPageOrientation(SelectPdf\Api\PageOrientation::Portrait) // PDF page orientation
        ->setMargins(0) // PDF page margins
        ->setRenderingEngine(SelectPdf\Api\RenderingEngine::WebKit) // rendering engine
        ->setConversionDelay(1) // conversion delay
        ->setNavigationTimeout(30) // navigation timeout 
        ->setShowPageNumbers(false) // page numbers
        ->setPageBreaksEnhancedAlgorithm(true) // enhanced page break algorithm

        // additional properties

        // ->setUseCssPrint(true) // enable CSS media print
        // ->setDisableJavascript(true) // disable javascript
        // ->setDisableInternalLinks(true) // disable internal links
        // ->setDisableExternalLinks(true) // disable external links
        // ->setKeepImagesTogether(true) // keep images together
        // ->setScaleImages(true) // scale images to create smaller pdfs
        // ->setSinglePagePdf(true) // generate a single page PDF
        // ->setUserPassword("password") // secure the PDF with a password

        // generate automatic bookmarks

        // ->setPdfBookmarksSelectors("H1, H2") // create outlines (bookmarks) for the specified elements
        // ->setViewerPageMode(SelectPdf\Api\PageMode::UseOutlines) // display outlines (bookmarks) in viewer
    ;

    echo ("Starting conversion ...\n");
    
    // convert url to file
    $client->convertUrlToFile($url, $localFile);

    // convert url to memory
    // $pdf = $client->convertUrl($url);

    // convert html string to file
    // $client->convertHtmlStringToFile("This is some <b>html</b>.", $localFile);

    // convert html string to memory
    // $pdf = $client->convertHtmlString("This is some <b>html</b>.");

    echo ("Finished! Number of pages: " . $client->getNumberOfPages() . ".\n");

    // get API usage
    $usageClient = new \SelectPdf\Api\UsageClient($apiKey);
    $usage = $usageClient->getUsage(true);
    echo("Conversions remained this month: " . $usage["available"] . ".\n");

}
catch (Exception $ex) {
	echo("An error occurred: " . $ex . ".\n");
}
?>
```

## Pdf Merge API

SelectPdf Pdf Merge REST API is an online solution that lets you merge local or remote PDFs into a final PDF document.

### Features

* Merge local PDF document.
* Merge remote PDF from public url.
* Set PDF viewer options and PDF document information.
* Secure generated PDF with a password.
* Works in all programming languages.

See [PDF Merge API](https://selectpdf.com/pdf-merge-api/) page for full list of parameters.

### Sample Code

```php
<?php
require("SelectPdf.Api.php");

$testUrl = "https://selectpdf.com/demo/files/selectpdf.pdf";
$testPdf = "Input.pdf";
$localFile = "Result.pdf";
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\PdfMergeClient($apiKey);

    // set parameters - see full list at https://selectpdf.com/pdf-merge-api/
    $client
        // specify the pdf files that will be merged (order will be preserved in the final pdf)

        ->addFile($testPdf) // add PDF from local file
        ->addUrlFile($testUrl) // add PDF From public url
        // ->addFile($testPdf, "pdf_password") // add PDF (that requires a password) from local file
        // ->addUrlFile($testUrl, "pdf_password") // add PDF (that requires a password) from public url
    ;

    echo ("Starting pdf merge ...\n");
    
    // merge pdfs to local file
    $client->saveToFile($localFile);

    // merge pdfs to memory
    // $pdf = $client->save();

    echo ("Finished! Number of pages: " . $client->getNumberOfPages() . ".\n");

    // get API usage
    $usageClient = new \SelectPdf\Api\UsageClient($apiKey);
    $usage = $usageClient->getUsage(true);
    echo("Conversions remained this month: " . $usage["available"] . ".\n");

}
catch (Exception $ex) {
	echo("An error occurred: " . $ex . ".\n");
}
?>
```

## Pdf To Text API

SelectPdf Pdf To Text REST API is an online solution that lets you extract text from your PDF documents or search your PDF document for certain words.

### Features

* Extract text from PDF.
* Search PDF.
* Specify start and end page for partial file processing.
* Specify output format (plain text or html).
* Use a PDF from an online location (url) or upload a local PDF document.

See [Pdf To Text API](https://selectpdf.com/pdf-to-text-api/) page for full list of parameters.

### Sample Code - Pdf To Text

```php
<?php
require("SelectPdf.Api.php");

$testUrl = "https://selectpdf.com/demo/files/selectpdf.pdf";
$testPdf = "Input.pdf";
$localFile = "Result.txt";
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\PdfToTextClient($apiKey);

    // set parameters - see full list at https://selectpdf.com/pdf-to-text-api/
    $client
        ->setStartPage(1) // start page (processing starts from here)
        ->setEndPage(0) // end page (set 0 to process file til the end)
        ->setOutputFormat(SelectPdf\Api\OutputFormat::Text) // set output format (0-Text or 1-HTML)
    ;

    echo ("Starting pdf to text ...\n");
    
    // convert local pdf to local text file
    $client->getTextFromFileToFile($testPdf, $localFile);

    // extract text from local pdf to memory
    // $text = $client->getTextFromFile($testPdf);
    // print text
    // echo($text);

    // convert pdf from public url to local text file
    // $client->getTextFromUrlToFile($testUrl, $localFile);

    // extract text from pdf from public url to memory
    // $text = $client->getTextFromUrl($testUrl);
    // print text
    // echo($text);

    echo ("Finished! Number of pages processed: " . $client->getNumberOfPages() . ".\n");

    // get API usage
    $usageClient = new \SelectPdf\Api\UsageClient($apiKey);
    $usage = $usageClient->getUsage(true);
    echo("Conversions remained this month: " . $usage["available"] . ".\n");

}
catch (Exception $ex) {
	echo("An error occurred: " . $ex . ".\n");
}
?>
```

### Sample Code - Search Pdf

```php
<?php
require("SelectPdf.Api.php");

$testUrl = "https://selectpdf.com/demo/files/selectpdf.pdf";
$testPdf = "Input.pdf";
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\PdfToTextClient($apiKey);

    // set parameters - see full list at https://selectpdf.com/pdf-to-text-api/
    $client
        ->setStartPage(1) // start page (processing starts from here)
        ->setEndPage(0) // end page (set 0 to process file til the end)
        ->setOutputFormat(SelectPdf\Api\OutputFormat::Text) // set output format (0-Text or 1-HTML)
    ;

    echo ("Starting search pdf ...\n");
    
    // search local pdf
    $results = $client->searchFile($testPdf, "pdf");

    // search pdf from public url
    // $results = $client->searchUrl($testUrl, "pdf");

    // print results
    $search_results_count = count($results);
    $search_results_string = json_encode($results, JSON_PRETTY_PRINT);

    echo ("Search results:\n$search_results_string\nSearch results count: $search_results_count.\n");

    echo ("Finished! Number of pages processed: " . $client->getNumberOfPages() . ".\n");

    // get API usage
    $usageClient = new \SelectPdf\Api\UsageClient($apiKey);
    $usage = $usageClient->getUsage(true);
    echo("Conversions remained this month: " . $usage["available"] . ".\n");

}
catch (Exception $ex) {
	echo("An error occurred: " . $ex . ".\n");
}
?>
```
