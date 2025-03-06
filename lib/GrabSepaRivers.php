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
    const SEPA_URL = 'https://www2.sepa.org.uk/database/riverlevels/';
    const RIVERS_READINGS_JSON = 'rivers-readings.json';
    const DOWNLOAD_LOCK_TIMEOUT = 3600; // 60 * 60; // remove download-lock if older than an hour, it means something crashed
    const DOWNLOAD_READINGS_TIMESTAMP = 'DOWNLOAD-READINGS-TIMESTAMP';
    const DOWNLOAD_LOCK = 'DOWNLOAD-LOCK';
    const SECTION_FORECASTS_FILE = 'section-forecasts.json'; // Write HTML for all forecasts to a file for loading by map JS
    // Magic URL example: https://timeseries.sepa.org.uk/KiWIS/KiWIS?service=kisters&type=queryServices&datasource=0&request=getTimeseriesValues&format=json&ts_id=57174010,61557010
    const TIMESERIES_GETVALUES_URL = "https://timeseries.sepa.org.uk/KiWIS/KiWIS?service=kisters&type=queryServices&datasource=0&request=getTimeseriesValues&format=json&";
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
        $this->sectionForecastsFile = ROOT . '/' . self::DATADIR . '/' . self::SECTION_FORECASTS_FILE;
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
    
    /* Ported to Timeseries API 2025-03
       Instead of downloading one CSV file for each river we now download a JSON file for all the gauges then extract that
    */
    private function downloadRiversData() {
        // Create a stream
        $sepaKey = file_get_contents("/home/jr/.config/wtw-key.text");
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Authorization: Bearer $sepaKey\r\n"
            )
        );
        $context = stream_context_create($opts);

        $this->riversReadingsData = array();
        $sectionForecastsHtml = array();
        $riverSectionLevelTSIDString = "ts_id=";
        foreach($this->riverSectionsData as $riverSection) {
            $riverSectionLevelTSIDString .= $riverSection['level_ts_id'] . ",";
        }
        $riverSectionLevelTSIDString = rtrim($riverSectionLevelTSIDString, ","); // remove any final comma
        // This URL queries the gauges for the timeseries data for the 15Minutes level data (i.e. the height we care about for WtW)
        $riverSectionLevelTSIDUrl = self::TIMESERIES_GETVALUES_URL . $riverSectionLevelTSIDString;
        print "<p>URL: $riverSectionLevelTSIDUrl";
        $riverSectionLevelTSIDsJson = @file_get_contents($riverSectionLevelTSIDUrl, false, $context); // This can take some time to fetch, should I save to file?
        print "<p>result ";
        print_r($riverSectionLevelTSIDsJson);
        $riverSectionLevelTSIDs = json_decode($riverSectionLevelTSIDsJson);
        // extract height data from json which is in format {"ts_id": "57174010","rows": "1","columns":"Timestamp,Value", "data": [["2025-03-04T15:30:00.000Z",0.346]]}
        $riverSectionLevelTSIDsHeight = array();
        foreach($riverSectionLevelTSIDs as $riverSectionLevelTSID) {
            $riverSectionLevelTSIDsHeight[$riverSectionLevelTSID->ts_id] = $riverSectionLevelTSID->data[0][1];
        }
        // extract height timestamp from json which is in format {"ts_id": "57174010","rows": "1","columns":"Timestamp,Value", "data": [["2025-03-04T15:30:00.000Z",0.346]]}
        $riverSectionLevelTSIDsTimestamp = array();
        foreach($riverSectionLevelTSIDs as $riverSectionLevelTSID) {
            $riverSectionLevelTSIDsTimestamp[$riverSectionLevelTSID->ts_id] = $riverSectionLevelTSID->data[0][0];
        }

        // Now munge it into the JSON format we use for rivers-readings.json which is used by the frontend
        foreach($this->riverSectionsData as $riverSection) {
            $this->riversReadingsData[$riverSection['gauge_location_code']] = array(
                                            "currentReading"=>$riverSectionLevelTSIDsHeight[$riverSection['level_ts_id']],
                                            "trend"=>0, // Dropped in move to Timeseries API
                                            "currentReadingTime"=>$riverSectionLevelTSIDsTimestamp[$riverSection['level_ts_id']],
                                            );
            $forecast = new GrabWeatherForecast();
            $forecast->doGrabWeatherForecast($riverSection['gauge_location_code'], $riverSection['longitude'], $riverSection['latitude']);
            $sectionForecastsHtml[$riverSection['gauge_location_code']] = $forecast->forecastHtml();
        }
        print "<p>Written forecast HTML to " . self::SECTION_FORECASTS_FILE . "</p>\n";
        $fp = fopen($this->sectionForecastsFile, 'w');
        fwrite($fp, json_encode($sectionForecastsHtml, JSON_PRETTY_PRINT));
        fclose($fp);

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
