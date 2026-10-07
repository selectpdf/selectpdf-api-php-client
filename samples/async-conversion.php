<?php
require("SelectPdf.Api.php");

$url = 'https://selectpdf.com';
$localFile = "Test.pdf";

// Async conversions are not supported on the demo endpoint -
// this sample requires a paid API key.
$apiKey = "Your API key here";

echo ("This is SelectPdf-" . SelectPdf\Api\ApiClient::CLIENT_VERSION . ".\n");

try {
    $client = new SelectPdf\Api\HtmlToPdfClient($apiKey);

    // Tune polling for the async job (optional).
    // The client polls /api2/asyncjob/ every AsyncCallsPingInterval
    // seconds, up to AsyncCallsMaxPings times, then gives up.
    $client->AsyncCallsPingInterval = 3; // seconds between polls
    $client->AsyncCallsMaxPings = 1000;  // max polls before timeout

    $client
        ->setPageSize(SelectPdf\Api\PageSize::A4)
        ->setPageOrientation(SelectPdf\Api\PageOrientation::Portrait)
        ->setMargins(0)
        ->setPageBreaksEnhancedAlgorithm(true)
    ;

    echo ("Starting async conversion ...\n");

    // url to file (async)
    $client->convertUrlToFileAsync($url, $localFile);

    // url to memory (async)
    // $pdf = $client->convertUrlAsync($url);

    // html string to file (async)
    // $client->convertHtmlStringToFileAsync("This is some <b>html</b>.", $localFile);

    // html string to memory (async)
    // $pdf = $client->convertHtmlStringAsync("This is some <b>html</b>.");

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
