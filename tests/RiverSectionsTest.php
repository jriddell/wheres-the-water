<?php

require_once('../lib/RiverSections.php');

use PHPUnit\Framework\TestCase;

final class RiverSectionsTest extends TestCase
{
    /* some test data */
    function initScratchData($riverSections) {
        $riverSections->riverSectionsData[0] = ['name'=> 'Tay',
                                 'gauge_location_code' => 10048,
                                 'longitude' => 58.1234,
                                 'latitude' => 0.123,
                                 'scrape_value' => 1.0,
                                 'medium_value' => 2.0,
                                 'high_value' => 3.0,
                                 'very_high_value' => 4.0,
                                 'huge_value' => 5.0
                                 ];
        $riverSections->riverSectionsData[1] = ['name'=> 'Ericht',
                                 'gauge_location_code' => 12345,
                                 'longitude' => 58.1234,
                                 'latitude' => 0.133,
                                 'scrape_value' => 1.5,
                                 'medium_value' => 2.5,
                                 'high_value' => 3.5,
                                 'very_high_value' => 4.5,
                                 'huge_value' => 5.5
                                 ];
    }

    public function testWriteToJson() {
        $riverSections = new RiverSections;
        $riverSections->filename = $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON;
        $this->initScratchData($riverSections);
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
        $this->assertFileEquals('data/river-sections-good.json', 'data/river-sections-testReadFromWriteToJson.json');
        unlink('data/river-sections-testReadFromWriteToJson.json');
    }
    
    public function testReadFromDatabase() {
        $riverSections = new RiverSections;
        $riverSections->readFromDatabase();
        $this->assertEquals(59, sizeof($riverSections->riverSectionsData));
    }
    
    public function testReadFromDatabaseWriteJson() {
        $riverSections = new RiverSections;
        $riverSections->filename = $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON;
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
        $riverSections->filename = $riverSections::ROOT . '/' . $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON;
        $riverSections->writeToJson();
        $this->assertFileExists($riverSections::ROOT . '/' . $riverSections::DATADIR.'/'.$riverSections::RIVER_SECTIONS_JSON);
    }
    */

    public function testEditRiverForm() {
        $riverSections = new RiverSections;
        $riverSections->filename = 'data/river-sections-good.json';
        $riverSections->readFromJson();
        $form = $riverSections->editRiverForm();
        $this->assertEquals('<legend>Tay</legend><label for', substr($form, 0, 30));
    }
    
    public function testEditRiverFormLine() {
        $riverSections = new RiverSections;
        $riverSections->filename = 'data/river-sections-good.json';
        $riverSections->readFromJson();
        $formLine = $riverSections->editRiverFormLine(0, $riverSections->riverSectionsData[0]);
        $this->assertEquals('<legend>Tay</legend><label for', substr($formLine, 0, 30));
    }
}
