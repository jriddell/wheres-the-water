<?php

include("lib/SepaRiverReadingHistory.php");
include("lib/RiverSections.php");

$riverSections = new RiverSections();
$riverSections->readFromJson();

foreach ($riverSections->riverSectionsData as $river) {
    $riverHistory = new SepaRiverReadingHistory($river['gauge_location_code']);
    $riverHistory->writeChart($river, 7, "weekly");
    $riverHistory->writeChart($river, 30, "monthly");
    $riverHistory->writeChart($river, 365, "yearly");
    print "Written Chart for " . $river['name'] . "\n";
}

/*
$river = $riverSections->riverSectionsData[1];
$riverHistory = new SepaRiverReadingHistory($river['gauge_location_code']);
$riverHistory->writeChart($river, 7, "weekly");
$riverHistory->writeChart($river, 30, "monthly");
$riverHistory->writeChart($river, 365, "yearly");
print "Written Chart for " . $river['name'];
*/
