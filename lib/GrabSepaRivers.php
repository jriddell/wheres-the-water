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
require_once('GrabWeatherForecast.php');

class GrabSepaRivers {
    const DATADIR = 'data';
    const TIMESTAMP = 'RIVERS_DOWNLOAD_TIMESTAMP';
    const SEPA_DOWNLOAD_PERIOD = 300; // 60 * 5; // make sure current download is no older than 5 minutes
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/';
    const RIVERS_READINGS_JSON = 'rivers-readings.json';
    const DOWNLOAD_LOCK_TIMEOUT = 3600; // 60 * 60; // remove download-lock if older than an hour, it means something crashed
    const DOWNLOAD_READINGS_TIMESTAMP = 'DOWNLOAD-READINGS-TIMESTAMP';
    const DOWNLOAD_LOCK = 'DOWNLOAD-LOCK';
    public $filename;
    public $timestampFile;
    public $downloadLockFile;
    public $riversReadingsData;

    function __construct() {
        $this->filename = ROOT . '/' . self::DATADIR . '/' . self::RIVERS_READINGS_JSON;
        $this->timestampFile = ROOT . '/' . self::DATADIR . '/' . self::TIMESTAMP;
        $this->downloadLockFile = ROOT . '/' . self::DATADIR . '/' . self::DOWNLOAD_LOCK;
        $this->downloadReadingsTimestampFile = ROOT . '/' . self::DATADIR .'/' . self::DOWNLOAD_READINGS_TIMESTAMP;
        $this->riversReadingsData = array();
    }

    //TODO report correctly on out of date data or no data
    public function doGrabSepaRiversReadings($riverSectionsData, $force = false) {
        $this->riverSectionsData = $riverSectionsData;
        // Remove download-lock if it is an hour old, means download crashed
        if (file_exists($this->downloadLockFile) && time()-filemtime($this->downloadLockFile) > self::DOWNLOAD_LOCK_TIMEOUT) {
            print "<p>Removing DOWNLOAD-LOCK as it is over an hour old</p>";
            unlink($this->downloadLockFile);
        }
        if ($force || !file_exists($this->timestampFile) || time()-filemtime($this->timestampFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $newTimeStampFile = fopen($this->timestampFile, "w") or die("Unable to open file!");
            fwrite($newTimeStampFile, "");
            fclose($newTimeStampFile);
            if (!file_exists($this->downloadLockFile)) {
                $newDownloadLockFile = fopen($this->downloadLockFile, "w") or die("Unable to open file download lock!");
                fwrite($newDownloadLockFile, "");
                fclose($newDownloadLockFile);
                $this->downloadRiversData();
                unlink($this->downloadLockFile);
            } else {
                print "<p>Download already in process (lock file present).</p>";
            }
        } else {
            print "<p>Previous river readings download was recently, just reading from local JSON data</p>";
            $this->readFromJson();
        }
    }
    
    private function downloadRiversData() {
        $this->riversReadingsData = array();
        foreach($this->riverSectionsData as $riverSection) {
            $river = new GrabSepaRiverReading();
            $river->doGrabSepaRiver($riverSection['gauge_location_code']);
            $this->riversReadingsData[$river->gauge_id] = array(
                                            "currentReading"=>$river->currentReading,
                                            "trend"=>$river->trend,
                                            "currentReadingTime"=>$river->currentReadingTime
                                            );
            $forecast = new GrabWeatherForecast();
            $forecast->doGrabWeatherForecast($riverSection['gauge_location_code'], $riverSection['longitude'], $riverSection['latitude']);
        }
        $this->writeToJson();
        $timestampFile = fopen($this->downloadReadingsTimestampFile, "w") or die("Unable to open file!");
        fwrite($timestampFile, time());
    }

    /* write data to file */
    function writeToJson() {
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->riversReadingsData, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    /* read river data from file */
    function readFromJson() {
        if (!file_exists($this->filename)) {
            return false;
        }
        $json = file_get_contents($this->filename);
        $this->riversReadingsData = json_decode($json, true);
        //print_r($this->riversReadingsData);
        //print_r(array_values($this->riversReadingsData));
        return true;
    }
}
