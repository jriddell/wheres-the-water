<?php

/*
PHP which checks if .json is older than 1 min
if so downloads SEPA CSV and writes
reads CSV and converts to json
writes json
*/

class RiverSections {
    const RIVER_SECTIONS_JSON = 'river-sections.json';
    const DATADIR = 'data';

    public $riverSectionsData = array();
    public $filename = self::DATADIR . '/' . self::RIVER_SECTIONS_JSON;

    function initScratchData() {
        $this->riverSectionsData[0] = ['name'=> 'Tay',
                                 'gauge_location_code' => 10048,
                                 'longitude' => 58.1234,
                                 'latitude' => 0.123,
                                 'scrape_value' => 1.0,
                                 'medium_value' => 2.0,
                                 'high_value' => 3.0,
                                 'very_high_value' => 4.0,
                                 'huge_value' => 5.0
                                 ];
        $this->riverSectionsData[1] = ['name'=> 'Ericht',
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

    function writeToJson() {
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->riverSectionsData));
        fclose($fp);
    }

    function readFromJson() {
        $json = file_get_contents($this->filename);
        $this->riverSectionsData = json_decode($json);
    }
    
    public function __toString(): string {
        return $this->variable;
    }

    public function getVariable(): string {
        return $this->variable;
    }
}
/*
$grabSepa = new GrabSepa;
$grabSepa->setVariable('hello');
print "<p>" . $grabSepa->getVariable();
*/

/*
if (time()-filemtime(datadir + SEPA_CSV) > sepa_download_period) {
  // file older than 2 hours
  //grab file
  //check it's valid
  //parse to variable
  //write
} else {
  // read value
}
*/
