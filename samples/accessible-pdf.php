<?php
// Tagged (accessible) PDF and PDF conformance standards.
require("SelectPdf.Api.php");

$url = 'https://selectpdf.com';
$localFile = "Accessible.pdf";

// Works with the keyless demo endpoint too - pass null or "demo" instead of the key.
// Note the demo stamps its output after conversion, so a demo PDF demonstrates the
// feature rather than being a conformant artifact; use a real key for output you intend to ship.
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\HtmlToPdfClient($apiKey);

    // set parameters - see full list at https://selectpdf.com/html-to-pdf-api-parameters/
    $client
        // Produce a tagged PDF: a logical structure tree covering headings,
        // paragraphs, lists, tables, figures with alternate text, links and
        // reading order - what a screen reader needs to read the document.
        ->setTagged(true)

        // A tagged document needs a title. Without this the converter falls
        // back to the HTML <title>.
        ->setDocTitle("SelectPdf - accessible sample")

        // Shown by viewers that honour it; accessible PDF expects it on, and
        // the API turns it on for you whenever tagged output is requested.
        ->setViewerDisplayDocTitle(true)

        // The document language, written as the PDF /Lang entry and onto the
        // tagged structure elements.
        ->setDocumentLanguage("en-US")

        // Conformance target. PdfA3A is the ACCESSIBLE level of PDF/A-3: it
        // implies a tagged document on its own, and it is the level required
        // to carry a ZUGFeRD / Factur-X invoice (see electronic-invoice.php).
        //   Full   - the complete PDF feature set (default)
        //   PdfA / PdfA2B / PdfA3B / PdfA3U - long term archiving
        //   PdfA3A - archiving + accessibility
        //   PdfX   - graphics exchange
        //   PdfSiqQ_A / PdfSiqQ_B - digital signatures
        ->setPdfStandard(SelectPdf\Api\PdfStandard::PdfA3A)
    ;

    // Tagged output requires the Blink or Chromium engine - the WebKit
    // engines cannot build a structure tree. You can name one explicitly:
    //
    //     $client->setRenderingEngine(SelectPdf\Api\RenderingEngine::Chromium);
    //
    // If you don't, the API promotes the conversion to Chromium for you and
    // reports the engine it used in the X-SelectPdf-Engine response header.
    // Asking for tagged output together with an explicit WebKit engine is
    // rejected with HTTP 400 rather than silently producing an untagged PDF.

    echo ("Starting conversion ...\n");

    // convert url to local file
    $client->convertUrlToFile($url, $localFile);

    // convert url to memory
    // $pdf = $client->convertUrl($url);

    echo ("Finished! Number of pages: " . $client->getNumberOfPages() . ".\n");

    // response telemetry
    echo ("Mode: " . $client->getMode() . ", Execution: " . $client->getExecutionMode() . ".\n");
    echo ("Credits remaining: " . $client->getCreditsRemaining() . " / " . $client->getCreditsTotal() . ".\n");
}
catch (SelectPdf\Api\ApiException $ex) {
    echo ("API error: " . $ex . "\n");
}
catch (Exception $ex) {
	echo("An error occurred: " . $ex . ".\n");
}
?>
