<?php

require_once('../config.php');
require_once('../lib/GrabSepaRiverReading.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaRiverReadingTest extends TestCase
{

    // This test needs a way to override the input data else the values need changed each time
    public function testVerifyRiver() {
        $grabSepaRiver = new GrabSepaRiverReading();
        $grabSepaRiver->sepaURL = "http://embra.edinburghlinux.co.uk/~jr/";
        $grabSepaRiver->dataDir = 'data';
        @unlink('data/133094-SG.csv'); // funny syntax to supress error if it does not exist
        $grabSepaRiver->doGrabSepaRiver('133094');
        $this->assertEquals('133094', $grabSepaRiver->gauge_id);
        $this->assertEquals('0.591', $grabSepaRiver->currentReading);
        $this->assertEquals('04/03/2018 10:00:00', $grabSepaRiver->currentReadingTime);
        $this->assertEquals('STABLE', $grabSepaRiver->trend);
    }
}
