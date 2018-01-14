<?php

require_once('../lib/RiverSections.php');

use PHPUnit\Framework\TestCase;

final class RiverSectionsTest extends TestCase
{
    public function testWriteToJson() {
        $riverSections = new RiverSections;
        $riverSections->initScratchData();
        $riverSections->writeToJson();
        $this->assertFileExists($riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
        unlink($riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
    }

    public function testReadFromJson() {
        $riverSections = new RiverSections;
        $riverSections->filename = 'data/river-sections-good.json';
        $riverSections->readFromJson();
        $this->assertEquals('Tay', $riverSections->riverSectionsData[0]->name);
    }

    public function testReadFromWriteToJson() {
        $riverSections = new RiverSections;
        $riverSections->filename = 'data/river-sections-good.json';
        $riverSections->readFromJson();
        $riverSections->filename = 'data/river-sections-testReadFromWriteToJson.json';
        $riverSections->writeToJson();
        $this->assertFileExists('data/river-sections-testReadFromWriteToJson.json');
        $this->assertFileExists('data/river-sections-good.json', 'data/river-sections-testReadFromWriteToJson.json');
        unlink('data/river-sections-testReadFromWriteToJson.json');
    }
    
    public function testReadFromDatabase() {
        $riverSections = new RiverSections;
        $riverSections->readFromDatabase();
        $this->assertEquals(59, sizeof($riverSections->riverSectionsData));
    }
    
    public function testReadFromDatabaseWriteJson() {
        $riverSections = new RiverSections;
        $riverSections->readFromDatabase();
        $this->assertEquals(59, sizeof($riverSections->riverSectionsData));
        $riverSections->writeToJson();
        $this->assertFileExists($riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
        unlink($riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
    }

    /* uncomment this to do a one time database import, it'll overwrite the existing data used
    public function testReadFromDatabaseWriteJsonForReal() {
        $riverSections = new RiverSections;
        $riverSections->readFromDatabase();
        $this->assertEquals(59, sizeof($riverSections->riverSectionsData));
        $riverSections->filename = '../' . $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON;
        $riverSections->writeToJson();
        $this->assertFileExists('../' . $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
    }
    */
}
