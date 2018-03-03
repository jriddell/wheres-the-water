<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/* Downloads and makes available the reading levels for a given river defined by the SEPA gauge_id
   Uses level data .csv file e.g.
   http://apps.sepa.org.uk/database/riverlevels/133094-SG.csv
*/

class GrabSepaRiver {
    const DATADIR = 'data';
    const SEPA_DOWNLOAD_PERIOD = 60 * 5; // make sure current download is no older than 5 minutes
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/';

    public $gauge_id;
    public $currentReading;
    public $trend;
    public $currentReadingTime;

    public function __construct($gauge_id) {
        $this->gauge_id = $gauge_id;

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

        $riverDataArray = explode("\n", $riverDataFile);
        //print_r($riverDataArray);
        //Get the last value (uses -2 as -1 final entry is just a new line)
        $mostRecentReading = array_slice($riverDataArray, -2, 1)[0]; // '03/03/2018 12:45:00,0.53'
        $mostRecentReading = rtrim($mostRecentReading);
        $mostRecentReadingPair = explode(",", $mostRecentReading); // ['03/03/2018 12:45:00', '0.53']
        $this->currentReading = $mostRecentReadingPair[1];
        $this->currentReadingTime = $mostRecentReadingPair[0];
        $pastReading = array_slice($riverDataArray, -6, 1)[0]; // '03/03/2018 11:45:00,0.53'
        $pastReading = rtrim($pastReading);
        $pastReadingPair = explode(",", $pastReading); // ['03/03/2018 11:45:00', '0.53']
        if ($this->currentReading > $pastReadingPair[1]) {
            $this->trend = 'Gang Up';
        } elseif ($this->currentReading == $pastReadingPair[1]) {
            $this->trend = 'Steady';
        } else {
            $this->trend = 'Gang Doon';
        }
    }
}
