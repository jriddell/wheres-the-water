<?php

require_once('../config.php');
require_once('../lib/GrabSepaGauges.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaGaugesTest extends TestCase
{

    public function testVerifyCsvData() {
        $grabSepa = new GrabSepaGauges();
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-good.csv");
        $this->assertEquals(true, $grabSepa->verifyCsvData());
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-bad2.csv");
        $this->assertEquals(false, $grabSepa->verifyCsvData());
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-bad.csv");
        $this->assertEquals(false, $grabSepa->verifyCsvData());
    }

    public function testDoGrab()
    {
        $grabSepa = new GrabSepaGauges();
        $grabSepa->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        unlink($grabSepa->sepaFile);
        $grabSepa2 = new GrabSepaGauges();
        $grabSepa2->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        
    }
    
    public function testConvertCsvToArray() {
        $grabSepa = new GrabSepaGauges();
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-good.csv");
        $grabSepa->convertCsvToArray();
        $this->assertEquals(['reading_timestamp'=> 1515845700, 'gauge_name' => 'Perth'], $grabSepa->sepaData[10048]);
    }
    
    public function testSepaData() {
        $grabSepa = new GrabSepaGauges();
        $sepaData = $grabSepa->sepaData();
        $this->assertGreaterThan(20, sizeof($sepaData));
        $this->assertInternalType('array', $sepaData);
    }
}
