# SelectPdf Online REST API - PHP Client

SelectPdf Online REST API is a professional solution for managing PDF documents online. It now has a dedicated, easy to use, PHP client library that can be setup in minutes.

## Try without an API key (demo mode)

```php
<?php
require("SelectPdf.Api.php");

// No API key -> keyless demo mode (5-page cap, watermarked output, Chromium engine).
$client = new SelectPdf\Api\HtmlToPdfClient(); // also: new SelectPdf\Api\HtmlToPdfClient("demo")
$client->convertUrlToFile("https://example.com", "demo.pdf");

echo ("Pages: " . $client->getNumberOfPages() . "\n");
if ($client->wasClamped())
    echo ("Server clamped: " . implode(",", $client->getClampedFields()) . "\n");
// Upgrade for unwatermarked, unlimited-page PDFs: https://selectpdf.com/pricing/
?>
```

### Demo mode limitations

Demo mode is intended for evaluating the API. To keep the public endpoint stable and prevent abuse, several limits and restrictions apply that don't exist on a paid endpoint.

**Output:**

- **5 pages maximum.** Pages beyond the fifth are dropped, then a final branding/notice page is appended (so a typical demo PDF is 1-6 pages depending on the source).
- **Watermarked.** A SelectPdf demo stamp is rendered on every page plus a footer attribution line.
- **Chromium rendering engine only.** If you call `setRenderingEngine(...)` with `WebKit`, `Restricted` or `Blink`, the server switches to `Chromium`. The response reports it: `wasClamped()` returns true and `getClampedFields()` includes `engine`.

**Not available in demo mode** (the client throws `DemoUnsupportedException` before the request is sent):

| Method | Behavior in demo mode |
| --- | --- |
| `setUserPassword(...)` / `setOwnerPassword(...)` | Throws `DemoUnsupportedException` (field `user_password` / `owner_password`). |
| `convert*Async(...)` | Throws `DemoUnsupportedException` (field `async`). The demo endpoint is synchronous only. |
| `getWebElements()` | Throws `DemoUnsupportedException` (field `pdf_web_elements_selectors`). |

**Clamped parameters:** `setNavigationTimeout(...)` (`max_load_time`) is capped to 15 seconds and `setConversionDelay(...)` (`min_load_time`) to 5 seconds. The capped field names are listed by `getClampedFields()`.

**Silently dropped fields:** `auth_username` / `auth_password`, `cookies_string` (`setCookies(...)`), `pdf_name` and `pdf_web_elements_selectors` are accepted but never honored by the demo endpoint. Any value you set is reported back by `wasAnyFieldDropped()` / `getDroppedFields()`. Clamped means the value was modified, dropped means it was thrown away.

**URLs:** the demo endpoint accepts only publicly reachable `http://` / `https://` URLs. Requests targeting non-public destinations are rejected with `DemoSafetyException` (HTTP 400).

**Rate limits:** per-IP, daily global and concurrency limits apply. A request over a limit is rejected with `DemoRateLimitException`; `getReason()` is `per_ip`, `daily_cap` or `concurrency`, and `getRetryAfter()` carries the `Retry-After` header value in seconds.

**Request size:** the request body is capped at 1 MB (HTTP 413) - typically only an issue if you inline a very large HTML string.

**Response:** no credit headers (the demo endpoint is keyless, so there is no billing account): `getCreditsTotal()` and `getCreditsRemaining()` return null. `getMode()` returns `"demo"` (helper: `isDemoResponse()`).

### Catching demo-mode errors

The client has three typed exceptions for demo-specific failures, all derived from `ApiException`; everything else surfaces as a regular `ApiException`:

```php
<?php
require("SelectPdf.Api.php");

try {
    $client = new SelectPdf\Api\HtmlToPdfClient(); // demo mode
    $pdf = $client->convertUrl("https://example.com");
}
catch (SelectPdf\Api\DemoRateLimitException $ex) {
    // getReason(): "per_ip" | "daily_cap" | "concurrency"
    echo ("Rate limited: " . $ex->getReason() . ", retry after " . $ex->getRetryAfter() . "s\n");
    echo ("Upgrade: " . $ex->getUpgradeUrl() . "\n");
}
catch (SelectPdf\Api\DemoSafetyException $ex) {
    // The supplied URL was rejected. Use a paid endpoint for non-public targets.
    echo ("URL rejected: " . $ex->getField() . " (" . $ex->getReason() . ")\n");
}
catch (SelectPdf\Api\DemoUnsupportedException $ex) {
    // getField(): which parameter is unavailable in demo mode
    echo ("'" . $ex->getField() . "' not available in demo mode. Upgrade: " . $ex->getUpgradeUrl() . "\n");
}
catch (SelectPdf\Api\ApiException $ex) {
    echo ("API error: " . $ex . "\n");
}
?>
```

Every demo exception also exposes `getStatusCode()` and `getResponseBody()` (the raw JSON error body returned by the server).

For unwatermarked production output without these limits, pass a real API key - same client class, same setters:

```php
$client = new SelectPdf\Api\HtmlToPdfClient("Your API key here");
```

`isDemoMode()` is set at construction time and stays the same for the client's lifetime, so a client created with a real key never falls back to demo behavior.

## Installation

Download [selectpdf-api-php-client-1.6.0.zip](https://github.com/selectpdf/selectpdf-api-php-client/releases/download/1.6.0/selectpdf-api-php-client-1.6.0.zip), unzip it and require SelectPdf.Api.php in your code.

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
* Produce tagged, accessible PDF and target a PDF/A, PDF/X or PDF/SiqQ conformance level.
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

### More conversion options

```php
$client
    ->setRenderingEngine(SelectPdf\Api\RenderingEngine::Chromium) // WebKit, Restricted, Blink or Chromium
    ->setWebPageHeight(800) // browser window height in pixels
    ->setWebPageFixedSize(true) // cut the page at the web page height (false: convert the whole page)
    ->setAuthUsername("user") // HTTP Basic authentication on the converted page
    ->setAuthPassword("password")
    ->setCookies(array("name" => "value")) // HTTP cookies sent to the converted page
;
```

### Asynchronous conversions

Use the `*Async` methods for long-running conversions. The client starts the job and polls `/api2/asyncjob/` every `AsyncCallsPingInterval` seconds, up to `AsyncCallsMaxPings` times.

```php
$client->AsyncCallsPingInterval = 3; // seconds between polls
$client->AsyncCallsMaxPings = 1000;  // max polls before timeout

$client->convertUrlToFileAsync("https://selectpdf.com", "Test.pdf");
// $pdf = $client->convertUrlAsync("https://selectpdf.com");
// $client->convertHtmlStringToFileAsync("This is some <b>html</b>.", "Test.pdf");
```

### Web elements

Get the location in the generated PDF of the HTML elements that match a set of CSS selectors.

```php
$client->setPdfWebElementsSelectors("H1, H2");
$client->convertUrlToFile("https://selectpdf.com", "Test.pdf");

$elements = $client->getWebElements(); // array of elements: HtmlElementTagName, HtmlElementId, HtmlElementCssClassName, PdfRectangles
echo ("Web elements found: " . count($elements) . ".\n");
```

### Response telemetry

Every client reports what the server returned with the most recent call:

| Method | Value |
| --- | --- |
| `getNumberOfPages()` | Number of pages of the resulted PDF (`X-SelectPdf-Pages`). |
| `getCreditsTotal()` | Monthly conversion limit of the subscription (`X-SelectPdf-Credits-Total`). -1 = unlimited. Null if absent (demo endpoint, error response). |
| `getCreditsRemaining()` | Conversions remaining this month (`X-SelectPdf-Credits-Remaining`). -1 = unlimited. Null if absent. |
| `getMode()` | `"production"` or `"demo"` (`X-SelectPdf-Mode`). Empty if absent. |
| `getExecutionMode()` | `"in-process"` or `"worker"` (`X-SelectPdf-Execution`). Empty for endpoints that do not convert. |

`HtmlToPdfClient` also has `isDemoMode()`, `isDemoResponse()`, `getClampedFields()` / `wasClamped()` and `getDroppedFields()` / `wasAnyFieldDropped()` (see demo mode above).

```php
echo ("Mode: " . $client->getMode() . ", Execution: " . $client->getExecutionMode() . ".\n");
echo ("Credits remaining: " . $client->getCreditsRemaining() . " / " . $client->getCreditsTotal() . ".\n");
```

## Accessible PDF and PDF Standards

Produce a tagged, accessible PDF and target a PDF conformance level, using the same `HtmlToPdfClient` you already use for conversions.

### Features

* Tagged PDF with a logical structure tree: headings, paragraphs, lists, tables, figures with alternate text, links and reading order.
* Document language written as the PDF `/Lang` entry and onto the tagged structure elements.
* PDF/A for long term archiving, PDF/X for graphics exchange, PDF/SiqQ for documents that will be digitally signed.
* PDF/A-3A is the accessible archival level and implies a tagged document on its own.

Tagged output requires the Blink or Chromium rendering engine - the WebKit engines cannot build a structure tree. If you do not set an engine, the API promotes the conversion to Chromium and reports the engine it used in the `X-SelectPdf-Engine` response header. Asking for tagged output together with an explicit WebKit engine is rejected rather than silently producing an untagged PDF.

Note this produces tagged PDF/A output; it is not a conformance certification.

### Sample Code - Accessible PDF

```php
<?php
require("SelectPdf.Api.php");

$client = new SelectPdf\Api\HtmlToPdfClient("Your API key here");
$client
    ->setTagged(true)
    ->setDocTitle("SelectPdf - accessible sample")
    ->setViewerDisplayDocTitle(true)
    ->setDocumentLanguage("en-US")
    ->setPdfStandard(SelectPdf\Api\PdfStandard::PdfA3A) // Full, PdfA, PdfA2B, PdfA3A, PdfA3B, PdfA3U, PdfX, PdfSiqQ_A, PdfSiqQ_B
;

$client->convertUrlToFile("https://selectpdf.com", "Accessible.pdf");
echo ("Pages: " . $client->getNumberOfPages() . ".\n");
?>
```

## Electronic Invoicing API - ZUGFeRD / Factur-X

Turn an HTML invoice into a hybrid electronic invoice: one PDF/A-3 file carrying both the invoice a human reads and the XML a recipient's accounting system reads, embedded as an associated file with the metadata invoice software looks for.

### Features

* Profiles MINIMUM, BASIC WL, BASIC, EN 16931, EXTENDED and XRECHNUNG (`ZugferdProfile` constants).
* The attachment relationship (`ZugferdRelationship`) is derived from the profile when you do not set one.
* The embedded file is named as the standard requires - `factur-x.xml`, or `xrechnung.xml` for the XRECHNUNG profile - because recipients look it up by name.
* Factur-X 1.0 metadata by default; the deprecated ZUGFeRD 2.0 schema (`ZugferdSchema::Zugferd20`) is available for recipients that still require it.
* Carrier defaults to PDF/A-3A, the accessible level, so the visible invoice is readable by assistive technology as well as archivable. PdfA3B and PdfA3U are also accepted.
* Every HTML to PDF conversion setting applies, because `InvoiceClient` derives from `HtmlToPdfClient`.

The invoice XML can only be embedded into a document created as PDF/A-3, so there is no variant that attaches it to an existing PDF you already have. An API key is required - the keyless demo endpoint does not produce electronic invoices, and `InvoiceClient` refuses a missing or "demo" key.

Use the `createFrom*` methods: `createFromUrl`, `createFromHtmlString`, `createFromHtmlStringWithBaseUrl`, each with `ToStream` / `ToFile` variants and `Async` variants (for example `createFromHtmlStringToFileAsync`).

### Sample Code - Electronic Invoice

```php
<?php
require("SelectPdf.Api.php");

$client = new SelectPdf\Api\InvoiceClient("Your API key here");
$client
    ->setInvoiceXmlFile("factur-x.xml") // or setInvoiceXml($xmlString)
    ->setZugferdProfile(SelectPdf\Api\ZugferdProfile::En16931)
;

$client->createFromHtmlStringToFile($invoiceHtml, "Invoice.pdf");
// ... or from the invoice page your application already renders:
// $client->createFromUrlToFile("https://your-app.example/invoices/INV-2026-001", "Invoice.pdf");

echo ("Pages: " . $client->getNumberOfPages() . ".\n");
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

## Changelog

### 1.6.0

Includes the 1.5.0 changes listed below (there was no separate 1.5.0 release of the PHP client).

* New `InvoiceClient` for ZUGFeRD / Factur-X hybrid electronic invoices (POST `/api2/invoice/`): converts a url or an html invoice into a PDF/A-3 document with the invoice XML embedded as an associated file. Methods `setInvoiceXmlFile`, `setInvoiceXml`, `setZugferdProfile`, `setZugferdRelationship`, `setZugferdSchema` and `createFromUrl` / `createFromHtmlString` / `createFromHtmlStringWithBaseUrl` with `ToStream`, `ToFile` and `Async` variants. Profiles MINIMUM, BASIC WL, BASIC, EN 16931, EXTENDED and XRECHNUNG, with the attachment relationship derived from the profile when not set. Requires an API key.
* Accessibility and standards on `HtmlToPdfClient`: `setTagged` (tagged, accessible PDF - requires the Blink or Chromium engine; the API promotes the engine automatically when none is set), `setPdfStandard` (Full, PdfA, PdfA2B, PdfA3A, PdfA3B, PdfA3U, PdfX, PdfSiqQ_A, PdfSiqQ_B) and `setDocumentLanguage`.
* `setWebPageFixedSize` (cut the page at the web page height or convert the whole page) and `setAuthUsername` / `setAuthPassword` (HTTP Basic authentication on the converted page).
* New constants classes: `PdfStandard`, `ZugferdProfile`, `ZugferdRelationship`, `ZugferdSchema`.
* `setPageSize` accepts A0, A6, A7 and A8 (the `PageSize` constants already existed).
* Fixed `convertUrlToStream`, which did not pass the output stream to the request.
* Requires PHP 7.1 or newer (the library already used PHP 7.1 syntax; composer.json now says so).

### 1.5.0 (included in 1.6.0)

* Keyless demo mode: `new HtmlToPdfClient()` (or a null, empty or "demo" key) converts with the public demo endpoint - no signup, watermarked output, 5-page cap, Chromium engine. `isDemoMode()`, `isDemoResponse()`, `getClampedFields()` / `wasClamped()`, `getDroppedFields()` / `wasAnyFieldDropped()`.
* Typed demo exceptions derived from `ApiException`: `DemoRateLimitException`, `DemoSafetyException`, `DemoUnsupportedException`. `setUserPassword`, `setOwnerPassword`, the async conversions and `getWebElements` throw `DemoUnsupportedException` in demo mode.
* Response telemetry on every client: `getCreditsTotal()`, `getCreditsRemaining()`, `getMode()`, `getExecutionMode()`, read from the `X-SelectPdf-*` response headers.
* `RenderingEngine::Chromium`.
