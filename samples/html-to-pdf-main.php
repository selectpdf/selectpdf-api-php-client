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