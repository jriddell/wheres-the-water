<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/* Make available a data structure of rivers and their current gauge readings
init with doGrabSepaRiversReadings($riverSectionsData) then you have
$obj->riversReadingsData = {'1243':
                     {
                         "currentReading": '1.323',
                         "trend": "STEADY",
                         "currentReadingTime": "1/12/18 8:32"
                     }
                    }
*/

require_once('GrabSepaRiverReading.php');

class GrabSepaRivers {
    const DATADIR = 'data';
    const TIMESTAMP = 'rivers_download_timestamp';
    const SEPA_DOWNLOAD_PERIOD = 60 * 5; // make sure current download is no older than 5 minutes
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/';
    const RIVERS_READINGS_JSON = 'rivers-readings.json';
    const ROOT = '/var/www/canoescotland.org/wheres-the-water';
    public $filename = self::ROOT . '/' . self::DATADIR . '/' . self::RIVERS_READINGS_JSON;
    public $timestampFile = self::DATADIR . '/' . self::TIMESTAMP;
    public $riversReadingsData = [];
    
    //TODO separate admin page to download river data, else use old data
    //TODO report correctly on out of date data or no data
    public function doGrabSepaRiversReadings($riverSectionsData) {
        $this->riverSectionsData = $riverSectionsData;
        if (!file_exists($this->timestampFile) || time()-filemtime($this->timestampFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $newTimeStampFile = fopen($this->timestampFile, "w") or die("Unable to open file!");
            fwrite($newTimeStampFile, "");
            fclose($newTimeStampFile);
            $this->downloadRiversData();
        } else {
            $this->readFromJson();
        }
    }
    
    private function downloadRiversData() {
        $this->riversReadingsData = [];
        foreach($this->riverSectionsData as $riverSection) {
            $river = new GrabSepaRiverReading();
            $river->doGrabSepaRiver($riverSection['gauge_location_code']);
            $this->riversReadingsData[$river->gauge_id] = [
                                            "currentReading"=>$river->currentReading,
                                            "trend"=>$river->trend,
                                            "currentReadingTime"=>$river->currentReadingTime
                                            ];
        }
        $this->writeToJson();
    }

    /* write data to file */
    function writeToJson() {
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->riversReadingsData, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    /* read river data from file */
    function readFromJson() {
        $json = file_get_contents($this->filename);
        $this->riversReadingsData = json_decode($json, true);
        //print_r($this->riversReadingsData);
        //print_r(array_values($this->riversReadingsData));
    }
}
