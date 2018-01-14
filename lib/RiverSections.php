<?php
/* Copyright 2017 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 only
*/

/*
Class to deal with river sections data
*/
class RiverSections {
    const RIVER_SECTIONS_JSON = 'river-sections.json';
    const DATADIR = 'data';
    const ROOT = '/var/www/canoescotland.org/wheres-the-water';

    public $riverSectionsData = array();
    public $filename = self::ROOT . '/' . self::DATADIR . '/' . self::RIVER_SECTIONS_JSON;

    /* write data to file */
    function writeToJson() {
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->riverSectionsData, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    /* read river data from file */
    function readFromJson() {
        $json = file_get_contents($this->filename);
        $this->riverSectionsData = json_decode($json);
    }

    /* read from database and convert to our format, 
       to be called once ever in testReadFromDatabaseWriteJsonForReal() 
       to overwrite the .json file 
    */
    public function readFromDatabase() {  
        require('../config/database.php'); // sets $servername, $username, $password, $dbname
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM node_revisions join content_type_river_section ON node_revisions.vid = content_type_river_section.vid";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $riverSection = ['name'=> $row["title"],
                                 'gauge_location_code' => $row["field_guageid_0_value"],
                                 'longitude' => $row["field_longitude_value"],
                                 'latitude' => $row["field_latitude_value"],
                                 'scrape_value' => $row["field_scrape_value"],
                                 'medium_value' => $row["field_medium_value"],
                                 'high_value' => $row["field_high_value"],
                                 'very_high_value' => $row["field_very_high_value"],
                                 'huge_value' => $row["field_huge_value"]
                                 ];
                $this->riverSectionsData[] = $riverSection;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }

    /* TODO HTML editable form */
    public function editRiverForm() {
        $reply = "";

        foreach($this->riverSectionsData as $jsonid => $riverSection) {
            $reply .= "<form action='river-section.php' method='post'>\n";
            $reply .= "<input type='hidden' name='riverUpdates' value='{$jsonid}' />\n";
            $reply .= $this->editRiverFormLine($riverSection);
            $reply .= "<input type='submit' value='Save' />\n";
            $reply .= "<input type='submit' value='&#10060;' class='right' />\n";
            $reply .= "</form>\n";
        }
        return $reply;
    }

    /* TODO HTML editable form */
    public function editRiverFormLine($riverSection) {
        $reply = "";
        $reply .= "<legend>" . $riverSection->name . "</legend>";
        $reply .= $this->editRiverFormInputItem("name", "name", $riverSection->name);
        $reply .= $this->editRiverFormInputItem("SEPA Gauge Code", "gauge_location_code", $riverSection->gauge_location_code, "right");
        $reply .= $this->editRiverFormInputItem("longitude", "longitude", $riverSection->longitude);
        $reply .= $this->editRiverFormInputItem("latitude", "latitude", $riverSection->latitude, "right");
        $reply .= $this->editRiverFormInputItem("scrape_value", "scrape_value", $riverSection->scrape_value);
        $reply .= $this->editRiverFormInputItem("medium_value", "medium_value", $riverSection->medium_value, "right");
        $reply .= $this->editRiverFormInputItem("high_value", "high_value", $riverSection->high_value);
        $reply .= $this->editRiverFormInputItem("very_high_value", "very_high_value", $riverSection->very_high_value, "right");
        $reply .= $this->editRiverFormInputItem("huge_value", "huge_value", $riverSection->huge_value);
        return $reply;
    }
    
    public function editRiverFormInputItem($text, $name, $value, $column="left") {
        $reply = "";
        $reply .= "<label for='{$name}' class='{$column}'>{$text}:</label>\n";
        $reply .= "<input type='text' name='{$name}' value='{$value}' class='{$column}' /> \n";
        return $reply;
    }

    /* TODO read HTML form to update rivers */
    public function updateRiverSection($postData) {
        $jsonid = $postData['riverUpdates'];
        $riverSection = $this->riverSectionsData[$jsonid];
        $riverSection->name = $postData['name'];
        $riverSection->gauge_location_code = $postData['gauge_location_code'];
        $riverSection->longitude = $postData['longitude'];
        $riverSection->latitude = $postData['latitude'];
        $riverSection->scrape_value = $postData['scrape_value'];
        $riverSection->medium_value = $postData['medium_value'];
        $riverSection->high_value = $postData['high_value'];
        $riverSection->very_high_value = $postData['very_high_value'];
        $riverSection->huge_value = $postData['huge_value'];
        $this->writeToJson();
    }

    /* TODO add new river */
    public function newRiverSection() {
        return $this->variable;
    }

    /* TODO delete a river */
    public function deleteRiverSection() {
        return $this->variable;
    }
    
    /* TODO javascript for website */
    public function outputJavascript() {
        return $this->variable;
    }
}
/*
$grabSepa = new GrabSepa;
$grabSepa->setVariable('hello');
print "<p>" . $grabSepa->getVariable();
*/

/*
if (time()-filemtime(datadir + SEPA_CSV) > sepa_download_period) {
  // file older than 2 hours
  //grab file
  //check it's valid
  //parse to variable
  //write
} else {
  // read value
}
*/
