<?php
/* Copyright 2019 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/*
Class and methods to download stations (gauges) list from riverzone.eu and match to SEPA gauges and offer hash

Call parseRiverZoneStations() and $sepaIdToRiverZoneId has mapping of sepa ids to river zone ids
*/
class RiverZoneStations {
    const RIVER_ZONE_STATIONS_FILENAME = 'river-zone-stations.json';
    const DATADIR = 'data';
    const RIVER_ZONE_DOWNLOAD_PERIOD = 86400; // 60 * 60 * 24; // download stations once a day
    const RIVER_ZONE_STATIONS_URL = 'https://api.riverzone.eu/v2/stations?key='; // . RIVER_ZONE_API_KEY;
    const SEPA_SOURCE_ID = '53027ba1-4afc-4848-8768-c4c0caf3a1a5'; // UUID RZ give to SEPA found in https://api.riverzone.eu/v2/sources
    const SEPA_ID_TO_RIVERZONE_ID = 'sepa-id-to-riverzone-id.json';

    public $riverZoneStationsFile; // path to json file
    public $riverZoneStationsJson; // json data as string
    public $riverZoneStationsData; // the data in associative array form
    public $sepaIdToRiverZoneId; // hash mapping SEPA gauge ID to River Zone UUID
    public $sepaIdToRiverZoneIdFile; // above hash saved to a file for caching

    function __construct() {
        $this->riverZoneStationsFile = ROOT . '/' . self::DATADIR . '/' . self::RIVER_ZONE_STATIONS_FILENAME; // filename
        $this->sepaIdToRiverZoneIdFile = ROOT . '/' . self::DATADIR . '/' . self::SEPA_ID_TO_RIVERZONE_ID; // filename
        $this->riverZoneStationsUrl = self::RIVER_ZONE_STATIONS_URL . RIVER_ZONE_API_KEY;
    }
    /* if file does not exist or is too old download it and write, else just read locally */
    function doGrab() {
        if (!file_exists($this->riverZoneStationsFile) || time()-filemtime($this->riverZoneStationsFile) > self::RIVER_ZONE_DOWNLOAD_PERIOD) {
            $this->riverZoneStationsJson = file_get_contents($this->riverZoneStationsUrl);
            $this->riverZoneStationsData = json_decode($this->riverZoneStationsJson, true); // truely we do want this to be an array PHP
            $newRiverZoneStationsFile = fopen($this->riverZoneStationsFile, "w") or die("Unable to open file!");
            fwrite($newRiverZoneStationsFile, $this->riverZoneStationsJson);
        } else {
            $this->riverZoneStationsJson = file_get_contents($this->riverZoneStationsFile);
            $this->riverZoneStationsData = json_decode($this->riverZoneStationsJson, true); // truely we do want this to be an array PHP
        }
    }

    /*  parse the json from riverzone for the stuff we care about which is just the SEPA ID and RZ ID
        populate $sepaIdToRiverZoneId
        This is cached in another json file $this->sepaIdToRiverZoneIdFile to save resouces
    */
    function parseRiverZoneStations() {
        if (!file_exists($this->sepaIdToRiverZoneIdFile) || time()-filemtime($this->sepaIdToRiverZoneIdFile) > self::RIVER_ZONE_DOWNLOAD_PERIOD) {
            $this->doGrab();
            foreach($this->riverZoneStationsData['stations'] as $station) {
                if ($station['dataSourceId'] == self::SEPA_SOURCE_ID) {
                    $sepaGauge = explode(',', $station['parserConfigs'])[0];
                    $this->sepaIdToRiverZoneId[$sepaGauge] = $station['id'];
                }
            }
            $newSepaIdToRiverZoneIdFile = fopen($this->sepaIdToRiverZoneIdFile, "w") or die("Unable to open file!");
            fwrite($newSepaIdToRiverZoneIdFile, json_encode($this->sepaIdToRiverZoneId, JSON_PRETTY_PRINT));
        } else {
            $sepaIdToRiverZoneIdData = file_get_contents($this->sepaIdToRiverZoneIdFile);
            $this->sepaIdToRiverZoneId = json_decode($sepaIdToRiverZoneIdData, true); // truely we do want this to be an array PHP
        }
    }

    function link($riverSection) {
        if (!in_array($riverSection['gauge_location_code'], array_keys($this->sepaIdToRiverZoneId))) {
            print "RiverZoneStations link return false";
            return false;
        }
        $url = 'https://riverzone.eu/calibration/';
        $url .= $this->sepaIdToRiverZoneId[$riverSection['gauge_location_code']];
        $url .= '.H#height=600&creditName=SCA Where%27s the Water&title=';
        $url .= $riverSection['name'];
        $url .= '&zones=';
        $url .= ',ff0000,Huge|';
        $url .= $riverSection['huge_value']*100 . ',ff6060,Very+High|';
        $url .= $riverSection['very_high_value']*100 . ',ffc004,High|';
        $url .= $riverSection['high_value']*100 . ',ffff33,Medium|';
        $url .= $riverSection['medium_value']*100 . ',00ff00,Low|';
        $url .= $riverSection['low_value']*100 . ',ccffcc,Scrapeable|';
        $url .= $riverSection['scrape_value']*100 . ',cccccc,Empty';
        return $url;
    }
}
