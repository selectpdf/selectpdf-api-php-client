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