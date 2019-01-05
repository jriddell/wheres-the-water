<?php

require_once('../config.php');
require_once('../lib/GrabSepaClassifications.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaClassificationsTest extends TestCase
{
    public function initGrabSepaClassificationsTest() {
        $this->classifications = new GrabSepaClassifications();
        $this->classifications->riverSections->filename = 'data/river-sections-good.json';
    }

    public function testDoClassificationsGrab() {
        $this->initGrabSepaClassificationsTest();
        
        $this->classifications->doClassificationsGrab();
        $this->assertEquals("xx", $this->classifications->foo());
    }
}
