<?php
/* Copyright 2017 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 only
*/

/*
Class to deal with the river sections data
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
                                 'low_value' => $row["field_low_value"],
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

    /* HTML editable form */
    public function editRiverForm() {
        $reply = "";

        foreach($this->riverSectionsData as $jsonid => $riverSection) {
            $reply .= "<form action='river-section.php' method='post'>\n";
            $reply .= "<input type='hidden' name='riverUpdates' value='{$jsonid}' />\n";
            $reply .= $this->editRiverFormLine($riverSection);
            $reply .= "<input type='submit' name='save' value='Save' />\n";
            $reply .= "<input type='submit' name='delete' value='&#10060;' class='right' />\n";
            $reply .= "</form>\n";
        }
        return $reply;
    }

    /* HTML editable form for a river section */
    public function editRiverFormLine($riverSection) {
        $reply = "";
        $reply .= "<legend>" . $riverSection->name . "</legend>";
        $reply .= $this->editRiverFormInputItem("River/Section Name", "name", $riverSection->name);
        $reply .= $this->editRiverFormInputItem("SEPA Gauge Code", "gauge_location_code", $riverSection->gauge_location_code, "right");
        $reply .= $this->editRiverFormInputItem("Longitude", "longitude", $riverSection->longitude);
        $reply .= $this->editRiverFormInputItem("Latitude", "latitude", $riverSection->latitude, "right");
        $reply .= $this->editRiverFormInputItem("Scrape", "scrape_value", $riverSection->scrape_value);
        $reply .= $this->editRiverFormInputItem("Low", "low_value", $riverSection->low_value, "right");
        $reply .= $this->editRiverFormInputItem("Medium", "medium_value", $riverSection->medium_value);
        $reply .= $this->editRiverFormInputItem("High", "high_value", $riverSection->high_value, "right");
        $reply .= $this->editRiverFormInputItem("Very High", "very_high_value", $riverSection->very_high_value);
        $reply .= $this->editRiverFormInputItem("Huge", "huge_value", $riverSection->huge_value, "right");
        return $reply;
    }
    
    /* one text field in the river form */
    public function editRiverFormInputItem($text, $name, $value, $column="left") {
        $reply = "";
        $reply .= "<label for='{$name}' class='{$column}'>{$text}:</label>\n";
        $reply .= "<input type='text' name='{$name}' value='{$value}' class='{$column}' /> \n";
        return $reply;
    }

    /* read submitted HTML form to update rivers */
    public function updateRiverSection($postData) {
        $jsonid = $postData['riverUpdates'];
        $riverSection = $this->riverSectionsData[$jsonid];
        $riverSection->name = $postData['name'];
        $riverSection->gauge_location_code = $postData['gauge_location_code'];
        $riverSection->longitude = $postData['longitude'];
        $riverSection->latitude = $postData['latitude'];
        $riverSection->scrape_value = $postData['scrape_value'];
        $riverSection->low_value = $postData['low_value'];
        $riverSection->medium_value = $postData['medium_value'];
        $riverSection->high_value = $postData['high_value'];
        $riverSection->very_high_value = $postData['very_high_value'];
        $riverSection->huge_value = $postData['huge_value'];
        $this->writeToJson();
        return "Updated data for " . $riverSection->name;
    }

    /* TODO add new river */
    public function newRiverSection() {
        return $this->variable;
    }

    /* deal with submitted request to delete a river */
    public function deleteRiverSection($postData) {
        $jsonid = $postData['riverUpdates'];
        unset($this->riverSectionsData[$jsonid]); // turns it into a hash.
        $this->riverSectionsData = array_values($this->riverSectionsData); // returns it to array.  Go PHP.
        $this->writeToJson();
        return "Deleted section " . $postData['name'];
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
