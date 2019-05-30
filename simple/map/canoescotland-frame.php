<?php

require_once '../../wheres-the-water/common.php';
require_once '../../wheres-the-water/config.php';
heading();
require_once '../../wheres-the-water/lib/RiverSections.php';
require_once '../../wheres-the-water/lib/WheresTheWater.php';

$riverSections = new RiverSections;
$riverSections->readFromJson();

$wtw = new WheresTheWater;
$wtw->headerStuff();
$wtw->theMap($riverSections);
$wtw->theJavaScript();

footer();
