<?php

require_once('../lib/GrabSepaRiver.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaRiverTest extends TestCase
{

    // This test needs a way to override the input data else the values need changed each time
    public function testVerifyRiver() {
        $grabSepaRiver = new GrabSepaRiver('133094');
        $this->assertEquals('133094', $grabSepaRiver->gauge_id);
        $this->assertEquals('0.613', $grabSepaRiver->currentReading);
        $this->assertEquals('03/03/2018 14:00:00', $grabSepaRiver->currentReadingTime);
        $this->assertEquals('FALLING', $grabSepaRiver->trend);
    }
}
