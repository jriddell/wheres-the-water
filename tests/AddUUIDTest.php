<?php

require_once('../config.php');
require_once('../lib/AddUUIDTest.php');

use PHPUnit\Framework\TestCase;

final class AddUUIDTest extends TestCase
{
    public function initAddUUIDTest() {
        $this->addUUID = new AddUUID();
        $this->addUUID->riverSections->filename = ROOT . '/tests/data/river-sections-uuid.json';
    }

    public function testDoAddUUID() {
        $this->initAddUUIDTest();
        
        $this->addUUID->doAddUUID();
        //print_r($this->addUUID->riverSections->riverSectionsData);
        $jsonFromFile = file_get_contents(ROOT . '/tests/data/river-sections-uuid-updated.json');
        $json = json_encode($this->addUUID->riverSections->riverSectionsData, JSON_PRETTY_PRINT);
        
        $this->assertEquals($json."\n", $jsonFromFile);
    }
}
