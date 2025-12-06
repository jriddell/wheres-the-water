<?php

require_once('../config.php');
require_once('../lib/GrabSepaGaugesTimeseries.php');
use PHPUnit\Framework\TestCase;

final class GrabSepaGaugesTest extends TestCase
{

    public function testVerifyCsvData() {
        $grabSepa = new GrabSepaGaugesTimeseries();
        $grabSepa->sepaJsonData = file_get_contents("data/getStationList-good.json");
        $this->assertEquals(true, $grabSepa->verifyJsonData());
        $grabSepa->sepaJsonData = file_get_contents("data/getStationList-bad.json");
        $this->assertEquals(false, $grabSepa->verifyJsonData());
        $grabSepa->sepaJsonData = file_get_contents("data/getStationList-bad2.json");
        $this->assertEquals(false, $grabSepa->verifyJsonData());
    }

    public function testDoGrab()
    {
        $grabSepa = new GrabSepaGaugesTimeseries();
        $grabSepa->doGrab();
        $this->assertEquals(
            'data/getStationList-good.json',
            $grabSepa->sepaFile
        );
        unlink($grabSepa->sepaFile);
        $grabSepa2 = new GrabSepaGaugesTimeseries();
        $grabSepa2->doGrab();
        $this->assertEquals(
            'data/SgetStationList-good.json',
            $grabSepa->sepaFile
        );
        
    }
    
    public function testConvertJsonToArray() {
        $grabSepa = new GrabSepaGaugesTimeseries();
        $grabSepa->sepaCsvData = file_get_contents("data/getStationList-good.json");
        $grabSepa->convertJsonToArray();
        $this->assertEquals(['gauge_name' => 'Perth'], $grabSepa->sepaData[10048]);
    }
    
    public function testSepaData() {
        $grabSepa = new GrabSepaGaugesTimeseries();
        $sepaData = $grabSepa->sepaData();
        $this->assertGreaterThan(20, sizeof($sepaData));
        $this->assertInternalType('array', $sepaData);
    }
}
