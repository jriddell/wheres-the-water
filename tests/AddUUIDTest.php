<?php

require_once('../config.php');
require_once('../lib/AddUUID.php');

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
        $stringFromFile = file_get_contents(ROOT . '/tests/data/river-sections-uuid-updated.json');
        $jsonFromFile = json_decode($stringFromFile, true);
        //print "result:";
        //print_r($jsonFromFile[0]["uuid"]);
        //$json = json_encode($this->addUUID->riverSections->riverSectionsData, JSON_PRETTY_PRINT);
        $json = $this->addUUID->riverSections->riverSectionsData;
        $this->assertEquals(substr($jsonFromFile[0]["uuid"], 0, 50), substr($json[0]["uuid"], 0, 50));
        $this->assertEquals(substr($jsonFromFile[1]["uuid"], 0, 50), substr($json[1]["uuid"], 0, 50));
    }
}
