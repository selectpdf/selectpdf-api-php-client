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