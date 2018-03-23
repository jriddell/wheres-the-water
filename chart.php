<?php

include("lib/SepaRiverReadingHistory.php");
include("lib/RiverSections.php");

$riverSections = new RiverSections();
$riverSections->readFromJson();
$river = $riverSections->riverSectionsData[1];

$riverHistory = new SepaRiverReadingHistory($river['gauge_location_code']);
$riverHistory->writeChart($river);
