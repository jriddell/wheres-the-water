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
}
