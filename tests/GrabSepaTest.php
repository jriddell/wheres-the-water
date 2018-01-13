<?php

require_once('../lib/GrabSepa.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaTest extends TestCase
{

    public function testVerifyCsvData() {
        $grabSepa = new GrabSepa();
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-good.csv");
        $this->assertEquals(true, $grabSepa->verifyCsvData());
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-bad2.csv");
        $this->assertEquals(false, $grabSepa->verifyCsvData());
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-bad.csv");
        $this->assertEquals(false, $grabSepa->verifyCsvData());
    }

    public function testDoGrab()
    {
        $grabSepa = new GrabSepa();
        $grabSepa->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        unlink($grabSepa->sepaFile);
        $grabSepa2 = new GrabSepa();
        $grabSepa2->doGrab();
        $this->assertEquals(
            'data/SEPA_River_Levels_Web.csv',
            $grabSepa->sepaFile
        );
        
    }
    
    public function testConvertCsvToArray() {
        $grabSepa = new GrabSepa();
        $grabSepa->sepaCsvData = file_get_contents("data/SEPA_River_Levels_Web-good.csv");
        $grabSepa->convertCsvToArray();
        $this->assertEquals(['current_level'=> '2.08', 'reading_datetime'=> '213456'], $grabSepa->sepaData[10048]);
    }
    
}
