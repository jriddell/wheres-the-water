<?php

require_once('../config.php');
require_once('../lib/RiverZoneStations.php');

use PHPUnit\Framework\TestCase;

final class RiverZoneStationsTest extends TestCase
{
    public function testDoGrab()
    {
        $grabRiverZoneStations = new RiverZoneStations();
        // remove .json file if it exists
        file_exists($grabRiverZoneStations->riverZoneStationsFile) && unlink($grabRiverZoneStations->riverZoneStationsFile);
        $grabRiverZoneStations->doGrab();
        $this->assertEquals('Pitnacree', $grabRiverZoneStations->riverZoneStationsData['stations'][552]['name']);
        // now test from already downloaded .json file
        $grabRiverZoneStations = new RiverZoneStations();
        $grabRiverZoneStations->doGrab();
        $this->assertEquals('Pitnacree', $grabRiverZoneStations->riverZoneStationsData['stations'][552]['name']);
    }

    public function testParse()
    {
        $grabRiverZoneStations = new RiverZoneStations();
        $grabRiverZoneStations->parseRiverZoneStations();
        // print_r($grabRiverZoneStations->sepaIdToRiverZoneId);
        $this->assertEquals('bf4b8b77-7400-56f1-9aaa-59a825b6c40a', $grabRiverZoneStations->sepaIdToRiverZoneId['14935']);
    }

    public function testLink()
    {
        $grabRiverZoneStations = new RiverZoneStations();
        $grabRiverZoneStations->parseRiverZoneStations();
        $riverSection['name'] = 'foo';
        $riverSection['gauge_location_code'] = '14956';
        $riverSection['huge_value'] = '1';
        $riverSection['very_high_value'] = '0.9';
        $riverSection['high_value'] = '0.8';
        $riverSection['medium_value'] = '0.7';
        $riverSection['low_value'] = '0.6';
        $riverSection['scrape_value'] = '0.5';
        $link = $grabRiverZoneStations->link($riverSection);
        $this->assertEquals('https://riverzone.eu/calibration/254d4d1e-8593-5e71-923d-2ce85378da66.H#height=600&creditName=WtW&title=foo&zones=,1,ff0000,Huge|0.9,ff0000,Very+High|0.8,ff0000,High|0.7,ff0000,Medium|0.6,ff0000,Low|0.5,ff0000,Scrapeable|0,ff0000,Empty', $link);
    }
}
