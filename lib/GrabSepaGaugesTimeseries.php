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
        "gauge_name": "Perth"
    }
}

https://timeseries.sepa.org.uk/KiWIS/KiWIS?service=kisters&datasource=0&type=queryServices&request=getStationList&format=json

*/
class GrabSepaGaugesTimeseries {
    const SEPA_JSON = 'SEPA_gauges.json';
    const DATADIR = 'data';
    const SEPA_DOWNLOAD_PERIOD = 86400; // 60 * 60 * 24; // download gauges once a day
    const SEPA_URL = 'https://timeseries.sepa.org.uk/KiWIS/KiWIS?service=kisters&datasource=0&type=queryServices&request=getStationList&format=json';

    public $sepaFile;
    public $sepaCsvData; // csv data as string
    public $sepaData; // the data in associative array form

    function __construct() {
        $this->sepaFile = ROOT . '/' . self::DATADIR . '/' . self::SEPA_JSON; // local filename
    }
    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {
        if (!file_exists($this->sepaFile) || time()-filemtime($this->sepaFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $this->sepaJsonData = file_get_contents(self::SEPA_URL);
            $this->verifyJsonData() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked.<br /> <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // CSV data did not verify
            $newSepaFile = fopen($this->sepaFile, "w") or die("Unable to open file!");
            fwrite($newSepaFile, $this->sepaJsonData);
        } else {
            $this->sepaJsonData = file_get_contents($this->sepaFile);
            $this->verifyJsonData() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked.<br /> <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // CSV data did not verify
        }
    }

    /* basic JSON file verification */
    function verifyJsonData() {
        print "verifyJsonData()<br>"
        $sepaGauges = json_decode($this->sepaJsonData, true);
        if ($sepaGauges === null) {
            return false;
        }
        if (count($sepaGauges) < 500) { // sepa have a lot of gauges now
            return false;
        }
        if ($sepaGauges[0][0] != "station_name") {
            return false;
        }
        return true;
    }

    /* create the data array */
    function convertJsonToArray() {
        $sepaGauges = json_decode($this->sepaJsonData, true);
        foreach($sepaGauges as $gauge) {
            if ($gauge[0] == "station_name") {
                continue;
            }
            $gauge_id = (int)$gauge[1];
            $this->sepaData[$gauge_id] = array();
            $this->sepaData[$gauge_id]['gauge_name'] = $gauge[0]; // station_name
        }
    }

    /* get the data and return the data array, this is the main public API */
    function sepaData() {
        $this->doGrab();
        $this->convertJsonToArray();
        return $this->sepaData;
    }
}
