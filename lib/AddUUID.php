<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only

   Greps https://www.farsondigitalwatercams.com/scotland/locations for each river section with a webcam and grabs
   the thumbnail image URL

*/

require_once('RiverSections.php');

class UpdateFarsonCameraThumbs {
    const DATADIR = 'data';
    const RIVER_SECTIONS_FILE = 'river-sections.json'; // Write output here

    function __construct() {
        $this->sectionsFilename = ROOT . '/' . self::DATADIR . '/' . self::RIVER_SECTIONS_FILE;
        $this->riverSections = new RiverSections();
    }

    public function doUpdateThumbs() {
        if (!$this->riverSections->readFromJson()) {
            print "<h1>Sorry no river section data available, try again soon</h1>";
            die();
        }
        $riverSectionId = 0;
        foreach ($this->riverSections->riverSectionsData as $river) {
            print "<p>".$river['name']."</p>\n";
            if (!array_key_exists('uuid', $river) || $river['uuid'] == "") {
                $riverSectionId++;
                continue;
            }
            $uuid = uniqid($this->riverSections->riverSectionsData[$riverSectionId]['name']);
            $this->riverSections->riverSectionsData[$riverSectionId]['uuid'] = $uuid;
            $riverSectionId++;
        }
    }

    public function writeOutJson() {
            $this->riverSections->writeToJson();
    }
}
