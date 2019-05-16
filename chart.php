<?php

include("config.php");
include("lib/SepaRiverReadingHistory.php");
include("lib/RiverSections.php");

define("CHARTS_GENERATED_TIMESTAMP", ROOT . '/charts/' . 'CHARTS-GENERATED-TIMESTAMP');

$riverSections = new RiverSections();
$riverSections->readFromJson();

foreach ($riverSections->riverSectionsData as $river) {
    $riverHistory = new SepaRiverReadingHistory($river['gauge_location_code']);
    $riverHistory->writeChart($river, 7, "weekly");
    $riverHistory->writeChart($river, 30, "monthly");
    $riverHistory->writeChart($river, 365, "yearly");
    print date('D, d M Y H:i:s') . " Written Chart for " . $river['name'] . "\n";
}

$timestampFile = fopen(CHARTS_GENERATED_TIMESTAMP, "w") or die("Unable to open file!");
fwrite($timestampFile, time());

/*
$river = $riverSections->riverSectionsData[1];
$riverHistory = new SepaRiverReadingHistory($river['gauge_location_code']);
$riverHistory->writeChart($river, 7, "weekly");
$riverHistory->writeChart($river, 30, "monthly");
$riverHistory->writeChart($river, 365, "yearly");
print "Written Chart for " . $river['name'];
*/
