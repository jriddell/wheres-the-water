<?php

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
        foreach($this->riverSectionsData as $id => $riverSection) {
            $reply .= $this->editRiverFormLine($id, $riverSection);
        }
        return $reply;
    }

    /* TODO HTML editable form */
    public function editRiverFormLine($id, $riverSection) {
        $reply = "";
        $reply .= "<legend>" . $riverSection->name . "</legend>";
        $reply .= $this->editRiverFormInputItem("name", $id, $riverSection->name);
        $reply .= $this->editRiverFormInputItem("gauge_location_code", $id, $riverSection->gauge_location_code);
        return $reply;
    }

    public function editRiverFormInputItem($name, $id, $value) {
        $reply = "";
        $reply .= "<label for='{$id}_{$name}'>{$name}:</label><input type='text' id='{$id}_{$name}' value='{$value}'. /> \n";
        return $reply;
    }

    /* TODO read HTML form */
    public function formSubmit() {
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
