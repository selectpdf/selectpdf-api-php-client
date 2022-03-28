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