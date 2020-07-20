<?php

use src\libs\GoogleAnalyticsReportClass;

require 'vendor/autoload.php';

ini_set('display_errors', "On");

$googleAnalyticsReport = new GoogleAnalyticsReportClass();

try {
    $responseArray = $googleAnalyticsReport->getReportData();
    echo '<pre>';
    var_dump($responseArray);
    echo '</pre>';
} catch (Google_Exception $e) {
    echo $e->getMessage();
    exit;
}
