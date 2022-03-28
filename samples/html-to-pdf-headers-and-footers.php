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
        ->setMargins(0) // PDF page margins
        ->setPageBreaksEnhancedAlgorithm(true) // enhanced page break algorithm

        // header properties
        ->setShowHeader(true) // display header
        // ->setHeaderHeight(50) // header height
        // ->setHeaderUrl($url) // header url
        ->setHeaderHtml("This is the <b>HEADER</b>!!!!") // header html

        // footer properties
        ->setShowFooter(true) // display footer
        // ->setFooterHeight(60) // footer height
        // ->setFooterUrl($url) // footer url
        ->setFooterHtml("This is the <b>FOOTER</b>!!!!") // footer html

        // footer page numbers
        ->setShowPageNumbers(true) // show page numbers in footer
        ->setPageNumbersTemplate("{page_number} / {total_pages}") // page numbers template
        ->setPageNumbersFontName("Verdana") // page numbers font name
        ->setPageNumbersFontSize(12) // page numbers font size
        ->setPageNumbersAlignment(SelectPdf\Api\PageNumbersAlignment::Center) // page numbers alignment (2-Center)
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