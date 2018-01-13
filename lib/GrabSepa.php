<?php

/*
PHP which checks if .json is older than 1 min
if so downloads SEPA CSV and writes
reads CSV and converts to json
writes json
*/
define("SEPA_CSV", "SEPA_River_Levels_Web.csv");
define("datadir", "data");
define("sepa_download_period", 60 * 10); // how often to download SEPA file in seconds

class GrabSepa {
    const SEPA_CSV = 'SEPA_River_Levels_Web.csv';
    const DATADIR = 'data';
    const SEPA_DOWNLOAD_PERIOD = 60 * 10;
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/SEPA_River_Levels_Web.csv';

    public $sepaFile = self::DATADIR . '/' . self::SEPA_CSV;
    private $sepaCsvData;

    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {        
        if (!file_exists($this->sepaFile) || time()-filemtime($this->sepaFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $this->sepaCsvData = file_get_contents(self::SEPA_URL);
            $this->verifyCsvData();
            $newSepaFile = fopen($this->sepaFile, "w") or die("Unable to open file!");
            fwrite($newSepaFile, $this->sepaCsvData);
        } else {
            $this->sepaCsvData = file_get_contents($this->sepaFile);
            $this->verifyCsvData();
        }
    }
 
    public function verifyCsvData() {
        print "VERIFYING";
    }

    public function __toString(): string {
        return $this->variable;
    }

    public function getVariable(): string {
        return $this->variable;
    }
}

/*
if (time()-filemtime($grabSepa::DATADIR . '/' . $grabSepa::SEPA_CSV) > sepa_download_period) {
  // file older than 2 hours
  //grab file
  //check it's valid
  //parse to variable
  //write
} else {
  // read value
}
*/
