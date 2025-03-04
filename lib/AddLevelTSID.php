<?php
/* Copyright 2025 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only

   Using the SEPA Timeseries API download the list of 15Minute Level metadata for all their gauges to get the magic ts_id we need to get the levels
   And add that into our river-sections.json file
*/

require_once('RiverSections.php');

class AddLevelTSID {
    const DATADIR = 'data';
    const RIVER_SECTIONS_FILE = 'river-sections.json'; // Write output here
    const LEVEL_TSID_LIST_URL = "https://timeseries.sepa.org.uk/KiWIS/KiWIS?service=kisters&type=queryServices&datasource=0&request=getTimeseriesList&station_name=*&ts_name=15minute&parametertype_name=S&format=json";
    const LEVEL_TSID_LIST_FILE = "level-tsid-list.json";
    const SEPA_DOWNLOAD_PERIOD = 86400; // 60 * 60 * 24; // download once a day

    function __construct() {
        $this->sectionsFilename = ROOT . '/' . self::DATADIR . '/' . self::RIVER_SECTIONS_FILE;
        $this->riverSections = new RiverSections();
        $this->levelTSIDListFile = ROOT . '/' . self::DATADIR . '/' . self::LEVEL_TSID_LIST_FILE;
    }

    /* If the level-tsid-list.json file does not exist or is too old then download it else read it
       We save it locally so we don't have to worry about overloading the API
     */
    public function downloadLevelTSIDList() {
        if (!file_exists($this->levelTSIDListFile) || time()-filemtime($this->levelTSIDListFile) > self::SEPA_DOWNLOAD_PERIOD) {
            $this->levelTSIDJsonData = file_get_contents(self::LEVEL_TSID_LIST_URL);
            $this->levelTSIDList = json_decode($this->levelTSIDJsonData, true);
            $this->verifyLevelTSIDList() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked.<br /> <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // JSON data did not verify
            $newlevelTSIDFile = fopen($this->levelTSIDListFile, "w") or die("Unable to open file!");
            fwrite($newlevelTSIDFile, json_encode($this->levelTSIDList, JSON_PRETTY_PRINT));
        } else {
            $this->levelTSIDJsonData = file_get_contents($this->levelTSIDListFile);
            $this->levelTSIDList = json_decode($this->levelTSIDJsonData, true);
            $this->verifyLevelTSIDList() || die('<a href="https://www.sepa.org.uk/help/system-temporarily-unavailable">SEPA data invalid</a>, cybers have attacked.<br /> <img src="https://i.redd.it/8falj3k93rg21.jpg">'); // JSON data did not verify
        }

    }

    /* basic JSON file verification */
    function verifyLevelTSIDList() {
        if (sizeof($this->levelTSIDList) < 20) {
            return false;
        }
        return true;
    }

    public function doAddLevelTSIDs() {
        $this->downloadLevelTSIDList();
        if (!$this->riverSections->readFromJson()) {
            print "<h1>Sorry no river section data available, try again soon</h1>";
            die();
        }
        $riverSectionId = 0;
        foreach ($this->riverSections->riverSectionsData as $river) {
            if (array_key_exists('level_ts_id', $river) && $river['level_ts_id'] != "") {
                $riverSectionId++;
                continue;
            }
            foreach ($this->levelTSIDList as $gauge) {
                if ($gauge[1]==$river['gauge_location_code']) {
                    print "<p>Found location code for " . $river['name'];
                    $this->riverSections->riverSectionsData[$riverSectionId]['level_ts_id'] = $gauge[3];
                }
            }
            $riverSectionId++;
        }
        $this->writeOutJson();
    }

    public function writeOutJson() {
            $this->riverSections->writeToJson();
    }
}
