<?php

require_once('../config.php');
require_once('../lib/GrabSepaRiverReading.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaRiverReadingTest extends TestCase
{

    // This test needs a way to override the input data else the values need changed each time
    public function testVerifyRiver() {
        $grabSepaRiver = new GrabSepaRiverReading();
        $grabSepaRiver->testMode = true; // uses right URL
        $grabSepaRiver->sepaURL = "http://embra.edinburghlinux.co.uk/~jr/";
        $grabSepaRiver->dataDir = 'data';
        @unlink('data/133094-SG.csv'); // funny syntax to supress error if it does not exist
        $grabSepaRiver->doGrabSepaRiver('133094');
        $this->assertEquals('133094', $grabSepaRiver->gauge_id);
        $this->assertEquals('0.74', $grabSepaRiver->currentReading);
        $this->assertEquals('16/11/2021 05:15:00', $grabSepaRiver->currentReadingTime);
        $this->assertEquals('RISING', $grabSepaRiver->trend);
    }

    public function testVerifyRiver2() {
        $grabSepaRiver = new GrabSepaRiverReading();
        $grabSepaRiver->testMode = true; // uses right URL
        $grabSepaRiver->sepaURL = "http://embra.edinburghlinux.co.uk/~jr/";
        $grabSepaRiver->dataDir = 'data';
        @unlink('data/234217-SG.csv'); // funny syntax to supress error if it does not exist
        $grabSepaRiver->doGrabSepaRiver('234217');
        $this->assertEquals('234217', $grabSepaRiver->gauge_id);
        $this->assertEquals('0.735', $grabSepaRiver->currentReading);
        $this->assertEquals('16/11/2021 15:30:00', $grabSepaRiver->currentReadingTime);
        $this->assertEquals('STABLE', $grabSepaRiver->trend);
    }

    // Get the most recent valid reading from the CSV files
    public function testMostRecentReading() {
        $grabSepaRiver = new GrabSepaRiverReading();
        $grabSepaRiver->sepaURL = "http://embra.edinburghlinux.co.uk/~jr/";
        $grabSepaRiver->dataDir = 'data';
        @unlink('data/133094-SG.csv'); // funny syntax to supress error if it does not exist
        $riverData = "14/11/2021 16:30:00,0.107\n14/11/2021 16:45:00,0\n14/11/2021 16:45:00,0\n";
        $mostRecentReading = $grabSepaRiver->mostRecentReading($riverData);
        $this->assertEquals("14/11/2021 16:30:00,0.107", $mostRecentReading);
    }
}
