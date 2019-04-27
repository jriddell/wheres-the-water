<?php
/* Copyright 2019 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/*
Class and methods to download stations (gauges) list from riverzone.eu and match to SEPA gauges and offer maps

Create the object then call doGrab() and your data will appear in $riverZoneStationsData
*/
class RiverZoneStations {
    const RIVER_ZONE_STATIONS_FILENAME = 'river-zone-stations.json';
    const DATADIR = 'data';
    const RIVER_ZONE_DOWNLOAD_PERIOD = 86400; // 60 * 60 * 24; // download stations once a day
    const RIVER_ZONE_STATIONS_URL = 'https://api.riverzone.eu/v2/stations?key=' . RIVER_ZONE_API_KEY;

    public $riverZoneStationsFile; // path to json file
    public $riverZoneStationsJson; // json data as string
    public $riverZoneStationsData; // the data in associative array form

    function __construct() {
        $this->riverZoneStationsFile = ROOT . '/' . self::DATADIR . '/' . self::RIVER_ZONE_STATIONS_FILENAME; // filename
    }
    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {
        if (!file_exists($this->riverZoneStationsFile) || time()-filemtime($this->riverZoneStationsFile) > self::RIVER_ZONE_DOWNLOAD_PERIOD) {
            $this->riverZoneStationsJson = file_get_contents(self::RIVER_ZONE_STATIONS_URL);
            $this->riverZoneStationsData = json_decode($this->riverZoneStationsJson, true); // truely we do want this to be an array PHP
            $newRiverZoneStationsFile = fopen($this->riverZoneStationsFile, "w") or die("Unable to open file!");
            fwrite($newRiverZoneStationsFile, $this->riverZoneStationsJson);
        } else {
            $this->riverZoneStationsJson = file_get_contents($this->riverZoneStationsFile);
            $this->riverZoneStationsData = json_decode($this->riverZoneStationsJson, true); // truely we do want this to be an array PHP
        }
    }

}
