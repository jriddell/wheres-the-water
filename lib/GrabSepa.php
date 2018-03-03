<?php

/*
Methods to download SEPA data and convert it to an associative array
Main API is sepaData() which downloads the data and returns a hash of
gauge_ids with their relevant data
{
    "10048": {
        "current_level": "2.08",
        "reading_timestamp": 1519543800,
        "gauge_name": "Perth"
    }
}
*/
class GrabSepa {
    const SEPA_CSV = 'SEPA_River_Levels_Web.csv';
    const DATADIR = 'data';
    const SEPA_DOWNLOAD_PERIOD = 60 * 5; // make sure current download is no older than 5 minutes
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/SEPA_River_Levels_Web.csv';

    public $sepaFile = self::DATADIR . '/' . self::SEPA_CSV; // filename
    public $sepaCsvData; // csv data as string
    public $sepaData; // the data in associative array form

    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {
        if (!file_exists($this->sepaFile) || time()-filemtime($this->sepaFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $this->sepaCsvData = file_get_contents(self::SEPA_URL);
            $this->verifyCsvData() || die('CSV data did not verify');
            $newSepaFile = fopen($this->sepaFile, "w") or die("Unable to open file!");
            fwrite($newSepaFile, $this->sepaCsvData);
        } else {
            $this->sepaCsvData = file_get_contents($this->sepaFile);
            $this->verifyCsvData() || die('CSV data did not verify');
        }
    }

    /* basic CSV file verification */
    function verifyCsvData() {
        $csvData = explode("\n", $this->sepaCsvData);
        if (sizeof($csvData) < 20) {
            return false;
        }
        if (substr($csvData[0], 0, 40) != "SEPA_HYDROLOGY_OFFICE,STATION_NAME,LOCAT") {
            return false;
        }
        return true;
    }

    /* create the data array */
    function convertCsvToArray() {
        $csvData = explode("\n", $this->sepaCsvData);
        $this->sepaData = array();
        foreach($csvData as $csvLine) {
            if (substr($csvLine, 0, 40) == "SEPA_HYDROLOGY_OFFICE,STATION_NAME,LOCAT") {
                continue;
            }
            $csvSplit = explode(',', $csvLine);
            if (sizeof($csvSplit) < 10) {
                continue;
            }
            $gauge_id = (int)$csvSplit[2];
            $this->sepaData[$gauge_id] = array();
            $this->sepaData[$gauge_id]['reading_timestamp'] = strtotime($csvSplit[9]); // END_DATE
            $this->sepaData[$gauge_id]['gauge_name'] = $csvSplit[1]; // STATION_NAME
            $this->sepaData[$gauge_id]['current_level'] = $this->grabSepaRiverData($gauge_id);
        }
    }
    
    private function grabSepaRiverData($gauge_id) {
        $riverFilename = "${gauge_id}-SG.csv";
        $riverFilePath = $sepaFile = self::DATADIR . '/' . $riverFilename;
        $riverFileURL = "http://apps.sepa.org.uk/database/riverlevels/" . $riverFilename;
        if (!file_exists($riverFilePath) || time()-filemtime($riverFilePath) > self::SEPA_DOWNLOAD_PERIOD) {
            $riverDataFile = file_get_contents($riverFileURL);
            $newSepaFile = fopen($riverFilePath, "w") or die("Unable to open file!");
            fwrite($newSepaFile, $riverDataFile);
        } else {
            $riverDataFile = file_get_contents($riverFilePath);
        }
        return '1.0';
    }

    /* get the data and return the data array, this is the main public API */
    function sepaData() {
        $this->doGrab();
        $this->convertCsvToArray();
        return $this->sepaData;
    }
}
