<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/*
http://map.sepa.org.uk/arcgis/rest/services/WMS_Hydrography/MapServer/identify?geometry=-4.84497,57.0759&geometryType=esriGeometryPoint&sr=4326&layers=all%3A4&layerDefs=&time=&layerTimeOptions=&tolerance=2&mapExtent=57%2C-4%2C58%2C-3&imageDisplay=1056%2C816%2C96&returnGeometry=true&maxAllowableOffset=&geometryPrecision=&dynamicLayers=&returnZ=false&returnM=false&gdbVersion=&returnUnformattedValues=false&returnFieldName=false&datumTransformations=&layerParameterValues=&mapRangeValues=&layerRangeValues=&f=pjson
*/

require_once('RiverSections.php');

/* Download the SEPA Classifications and save to a file */
class GrabSepaClassifications {
    const DATADIR = 'data';
    const SEPA_CLASSIFICATIONS_URL = 'http://map.sepa.org.uk/arcgis/rest/services/WMS_Hydrography/MapServer/identify?geometryType=esriGeometryPoint&sr=4326&layers=all%3A4&layerDefs=&time=&layerTimeOptions=&tolerance=2&mapExtent=57%2C-4%2C58%2C-3&imageDisplay=1056%2C816%2C96&returnGeometry=true&maxAllowableOffset=&geometryPrecision=&dynamicLayers=&returnZ=false&returnM=false&gdbVersion=&returnUnformattedValues=false&returnFieldName=false&datumTransformations=&layerParameterValues=&mapRangeValues=&layerRangeValues=&f=pjson';
    const RIVER_SECTIONS_FILE = 'river-sections.json'; // Write output here

    function __construct() {
        $this->sectionsFilename = ROOT . '/' . self::DATADIR . '/' . self::RIVER_SECTIONS_FILE;
        $this->riverSections = new RiverSections();
    }

    public function doClassificationsGrab() {
        if (!$this->riverSections->readFromJson()) {
            print "<h1>Sorry no river section data available, try again soon</h1>";
            die();
        }
        $riverSectionId = 0;
        foreach ($this->riverSections->riverSectionsData as $river) {
            print "<p>".$river['name']."</p>\n";
            $geometry = 'geometry=' . $river['longitude'] . "," . $river['latitude'];
            $url = self::SEPA_CLASSIFICATIONS_URL . '&' . $geometry;
            print "URL: $url\n";
            $classificationDataJson = @file_get_contents($url);
            if ($classificationDataJson == false) {
                print "<p>No Classification data for " . $river['name'] . "</p>\n";
            } else if (!$this->validateClassificationData($classificationDataJson)) {
                print "<p>Empty classificatoin file downloaded for " . $river['name']  . "</p>\n";
            } else {
                $classificationData = json_decode($classificationDataJson, true);
                print "Num results: " . count($classificationData['results']) . "\n";
                if (count($classificationData['results']) == 0) {
                    print "<p>No results found for " . $river['name'] . " at " . $river['longitude'] .",".  $river['latitude']. "\n";
                } else if (count($classificationData['results']) > 1) {
                    print "<p>Multiple results found for " . $river['name'] . " at " . $river['longitude'] .",".  $river['latitude'] . "\n";
                } else {
                    $this->updateClassification($classificationData, $riverSectionId);
                }
            }
            $riverSectionId++;
        }
    }

    private function validateClassificationData($classificationData) {
        //TODO
        return true;
    }

    private function updateClassification($classificationData, $riverSectionId) {
        $riverClassificationAttributes= $classificationData['results'][0]['attributes'];
        
        print "name: " . $riverClassificationAttributes['WATER_BODY_NAME'] . "\n";
        print "url: " . $riverClassificationAttributes['CLASS_DS_URL'] . "\n";
        print "classification: " . $riverClassificationAttributes['OVERALL_CLASSIFICATION'] . "\n";
        print "\n";
        $this->riverSections->riverSectionsData[$riverSectionId]['classification'] = $riverClassificationAttributes['OVERALL_CLASSIFICATION'];
        $this->riverSections->riverSectionsData[$riverSectionId]['classification_url'] = $riverClassificationAttributes['CLASS_DS_URL'];
        
        flush();
        $this->riverSections->writeToJson();
    }
}
