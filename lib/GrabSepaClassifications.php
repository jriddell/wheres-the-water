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
    const SEPA_URL = 'http://apps.sepa.org.uk/database/riverlevels/';
    const RIVER_CLASSIFICATIONS_FILE = 'river-classifications.json'; // Write output here

    function __construct() {
        $this->filename = ROOT . '/' . self::DATADIR . '/' . self::RIVER_CLASSIFICATIONS_FILE;
        $riverSections = new RiverSections();
    }

    public function doClassificationsGrab() {
        if (!$riverSections->readFromJson()) {
            print "<h1>Sorry no river section data available, try again soon</h1>";
            die();
        }
        foreach ($riverSections->riverSectionsData as $river) {
            print "<p>".$river['longitude']."</p>\n";
        }
    }
}
