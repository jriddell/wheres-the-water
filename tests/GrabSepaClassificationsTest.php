<?php

require_once('../config.php');
require_once('../lib/GrabSepaClassifications.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaClassificationsTest extends TestCase
{
    public function initGrabSepaClassificationsTest() {
        $this->classifications = new GrabSepaClassifications();
        $this->classifications->riverSections->filename = ROOT . '/tests/data/river-sections-good.json';
        //$this->classifications->riverSections->filename = ROOT . '/data/river-sections-sca-copy.json';
    }

    public function testDoClassificationsGrab() {
        $this->initGrabSepaClassificationsTest();
        
        $this->classifications->doClassificationsGrab();
        print_r($this->classifications->riverSections->riverSectionsData);
        $jsonFromFile = file_get_contents(ROOT . '/tests/data/river-sections-new.json');
        $json = json_encode($this->classifications->riverSections->riverSectionsData, JSON_PRETTY_PRINT);
        
        $this->assertEquals($json, $jsonFromFile);
    }

    public function testDoClassificationsGrab() {
        $this->initGrabSepaClassificationsTest();
        
        $this->classifications->doClassificationsGrab();
        print_r($this->classifications->riverSections->riverSectionsData);
        $jsonFromFile = file_get_contents(ROOT . '/tests/data/river-sections-new.json');
        $json = json_encode($this->classifications->riverSections->riverSectionsData, JSON_PRETTY_PRINT);
        
        $this->assertEquals($json, $jsonFromFile);
    }
}
