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
}
