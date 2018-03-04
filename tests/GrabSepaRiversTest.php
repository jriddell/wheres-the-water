<?php

require_once('../lib/GrabSepaRivers.php');

use PHPUnit\Framework\TestCase;

final class GrabSepaRiversTest extends TestCase
{

    public $riverSectionsData;

    /* some test data */
    function initScratchData() {
        $this->riverSectionsData = array();
        $this->riverSectionsData[0] = ['name'=> 'Tay',
                                 'gauge_location_code' => 10048,
                                 'longitude' => 58.1234,
                                 'latitude' => 0.123,
                                 'scrape_value' => 1.0,
                                 'low_value' => 1.1,
                                 'medium_value' => 2.0,
                                 'high_value' => 3.0,
                                 'very_high_value' => 4.0,
                                 'huge_value' => 5.0
                                 ];
        $this->riverSectionsData[1] = ['name'=> 'Ericht',
                                 'gauge_location_code' => 9514,
                                 'longitude' => 58.1234,
                                 'latitude' => 0.133,
                                 'scrape_value' => 1.5,
                                 'low_value' => 1.7,
                                 'medium_value' => 2.5,
                                 'high_value' => 3.5,
                                 'very_high_value' => 4.5,
                                 'huge_value' => 5.5
                                 ];
    }

    // This test needs a way to override the input data else the values need changed each time
    public function testVerifyRivers() {
        $this->initScratchData();
    
        $grabSepaRivers = new GrabSepaRivers();
        $grabSepaRivers->filename = 'data/rivers-readings.json';
        $grabSepaRivers->doGrabSepaRiversReadings($this->riverSectionsData);
        $this->assertEquals($this->riverSectionsData, $grabSepaRivers->riverSectionsData);
        //print_r($grabSepaRivers->riversReadingsData);
        $this->assertEquals(['10048'=>['currentReading'=>'0.485', 'trend'=>'FALLING', 'currentReadingTime'=>'03/03/2018 12:30:00'],
                             '9514'=>['currentReading'=>'1.456', 'trend'=>'RISING', 'currentReadingTime'=>'03/03/2018 14:45:00']
                             ], $grabSepaRivers->riversReadingsData);
    }

    public function testReadWriteJson() {
        $this->initScratchData();
    
        $grabSepaRivers = new GrabSepaRivers();
        $grabSepaRivers->filename = 'data/rivers-readings.json';
        $grabSepaRivers->doGrabSepaRiversReadings($this->riverSectionsData);
        $grabSepaRivers->filename = 'data/rivers-readings-written.json';
        $grabSepaRivers->writeToJson();
        $this->assertFileExists('data/rivers-readings-written.json');
        $this->assertFileEquals('data/rivers-readings.json', 'data/rivers-readings-written.json');
        unlink('data/rivers-readings-written.json');
    }
    
}
