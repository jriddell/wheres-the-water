<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/*
Methods to download SEPA gauges data and convert it to an associative array
Main API is sepaData() which downloads the data and returns a hash of
gauge_ids with their relevant data
{
    "10048": {
        "reading_timestamp": 1519543800,
        "gauge_name": "Perth"
    }
}
*/
class GrabSepaGauges {
    const SEPA_CSV = 'SEPA_River_Levels_Web.csv';
    const DATADIR = 'data';
    const SEPA_DOWNLOAD_PERIOD = 86400; // 60 * 60 * 24; // download gauges once a day
    const SEPA_URL = 'https://www2.sepa.org.uk/waterlevels/CSVs/SEPA_River_Levels_Web.csv';

    public $sepaFile;
    public $sepaCsvData; // csv data as string
    public $sepaData; // the data in associative array form

    function __construct() {
        $this->sepaFile = ROOT . '/' . self::DATADIR . '/' . self::SEPA_CSV; // filename
    }
    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {
        if (!file_exists($this->sepaFile) || time()-filemtime($this->sepaFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $this->sepaCsvData = file_get_contents(self::SEPA_URL);
            $this->verifyCsvData() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked. <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // CSV data did not verify
            $newSepaFile = fopen($this->sepaFile, "w") or die("Unable to open file!");
            fwrite($newSepaFile, $this->sepaCsvData);
        } else {
            $this->sepaCsvData = file_get_contents($this->sepaFile);
            $this->verifyCsvData() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked. <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // CSV data did not verify
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
        }
    }

    /* get the data and return the data array, this is the main public API */
    function sepaData() {
        $this->doGrab();
        $this->convertCsvToArray();
        return $this->sepaData;
    }
}
