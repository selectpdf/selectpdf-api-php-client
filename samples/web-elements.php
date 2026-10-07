<?php
require("SelectPdf.Api.php");

$url = 'https://selectpdf.com';
$localFile = "Test.pdf";

// Web elements lookup requires a paid API key (the demo endpoint
// does not expose the elements service).
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\HtmlToPdfClient($apiKey);

    // CSS selectors used to identify HTML elements whose location in
    // the resulting PDF should be reported back. See the API docs for
    // selector syntax: https://selectpdf.com/html-to-pdf-api/
    $client
        ->setPageSize(SelectPdf\Api\PageSize::A4)
        ->setMargins(0)
        ->setPdfWebElementsSelectors("H1, H2, *.menu, *#footer")
    ;

    echo ("Starting conversion ...\n");

    $client->convertUrlToFile($url, $localFile);

    echo ("Finished! Number of pages: " . $client->getNumberOfPages() . ".\n");

    // Retrieve element rectangles. Returns an empty array if no element
    // matched the configured selectors.
    $elements = $client->getWebElements();
    echo ("Web elements found: " . count($elements) . ".\n");

    foreach ($elements as $element) {
        $tag = isset($element["HtmlElementTagName"]) ? $element["HtmlElementTagName"] : "";
        $id = isset($element["HtmlElementId"]) ? $element["HtmlElementId"] : "";
        $cssClass = isset($element["HtmlElementCssClassName"]) ? $element["HtmlElementCssClassName"] : "";
        $rectangles = isset($element["PdfRectangles"]) && is_array($element["PdfRectangles"]) ? count($element["PdfRectangles"]) : 0;

        echo (" - <" . $tag . "> id='" . $id . "' class='" . $cssClass . "' rectangles=" . $rectangles . "\n");
    }

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
