<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only

   Greps https://www.farsondigitalwatercams.com/scotland/locations for each river section with a webcam and grabs
   the thumbnail image URL

*/

require_once('RiverSections.php');

class UpdateFarsonCameraThumbs {
    const DATADIR = 'data';
    // the HTML file from the farson website downloaded with get-farson-cameras.sh
    const FARSON_LOCATIONS = 'farson-camera-locations';
    const RIVER_SECTIONS_FILE = 'river-sections.json'; // Write output here

    function __construct() {
        $this->sectionsFilename = ROOT . '/' . self::DATADIR . '/' . self::RIVER_SECTIONS_FILE;
        $this->faronsonLocationsFile = ROOT . '/' . self::DATADIR . '/' . self::FARSON_LOCATIONS;
        $this->riverSections = new RiverSections();
        $this->faronsonLocationsFileArray = file($this->faronsonLocationsFile);
    }

    public function doUpdateThumbs() {
        if (!$this->riverSections->readFromJson()) {
            print "<h1>Sorry no river section data available, try again soon</h1>";
            die();
        }
        foreach ($this->riverSections->riverSectionsData as $river) {
            print "<p>".$river['name']."</p>\n";
            if (!array_key_exists('webcam', $river) || $river['webcam'] == "") {
                break;
            }
            $cameraName = $river['webcam'];
            $cameraName = str_replace("https://www.farsondigitalwatercams.com/locations/", "", $cameraName);
            print "XXX $cameraName";
            foreach ($this->faronsonLocationsFileArray as $line) {
                $lcline = strtolower($line);
                if (strpos($lcline, "camera at $cameraName") !== false) {
                    print "Found Line! $line";
                    $start = strpos($line, "src=\"");
                    $end = strpos($line, "\" />");
                    $url = substr($line, $start + 5, $end - $start - 5);
                    print "URL: $url";
                }
            }
            
        }
    }

    private function validateClassificationData($classificationData) {
        //TODO
        return true;
    }

    private function updateClassification($classificationData, $riverSectionId) {
        $riverClassificationAttributes = $classificationData['results'][0]['attributes'];
        
        print "<p>name: " . $riverClassificationAttributes['WATER_BODY_NAME'] . "<br />\n";
        print "classification: " . $riverClassificationAttributes['OVERALL_CLASSIFICATION'] . "<br />\n";
        print "\n</p>";
        $this->riverSections->riverSectionsData[$riverSectionId]['classification'] = $riverClassificationAttributes['OVERALL_CLASSIFICATION'];
        $this->riverSections->riverSectionsData[$riverSectionId]['classification_url'] = $riverClassificationAttributes['CLASS_DS_URL'];
        
        flush();
    }
    
    public function writeOutClassification() {
            $this->riverSections->writeToJson();
    }
}
