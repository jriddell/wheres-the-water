<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

require_once 'GrabSepaGauges.php';
require_once 'GrabSepaRivers.php';
require_once('GrabWeatherForecast.php');

/*
Class to deal with the scheduled sections data
call readFromJson() then $obj->scheduledSectionsData is an array of rivers with their data
[
    {
        "name": "Falls of Lora",
        "notes": "Tidal static waves",
        "longitude": "-4.84497",
        "latitude": "57.0759",
        "grade": "2-3",
        "info_link": "http://www.canoescotland.org/tummel-releases",
        "guidebook_link": "http://www.ukriversguidebook.co.uk/foo",
        "sca_guidebook_no": "123",
        "access_issue": "http://www.canoescotland.org/news/river-clyde",
        "google_mymaps": "https://drive.google.com/open?id=1A3Jqx9E46jVymhbP1-3UNudWxdx4PNuG&usp=sharing",
        "kml": "http://www.andyjacksonfund.org.uk/wheres-the-water/kml/stanley.kml",
        "webcam": "https://www.farsondigitalwatercams.com/locations/crossford",
        "put_in_long": "-4.84497",
        "put_in_lat": "57.0759",
        "get_out_long": "-4.84497",
        "get_out_lat": "57.0759",
        "dates": ["2020-01-01", "2020-02-01", "2020-03-01"],
        "constant": 0
    }
]
put_in_long, put_in_lat, get_out_long, get_out_lat: added at request of Tim from rivermap.ch so he can add those.  in return we get pretty graphs.
*/
class ScheduledSections {
    const SCHEDULED_SECTIONS_JSON = 'scheduled-sections.json';
    const DATADIR = 'data';

    public $scheduledSectionsData;
    public $filename;

    function __construct() {
        $this->scheduledSectionsData = array();
        $this->filename = ROOT . '/' . self::DATADIR . '/' . self::SCHEDULED_SECTIONS_JSON;
    }

    /* write data to file */
    function writeToJson() {
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->scheduledSectionsData, JSON_PRETTY_PRINT));
        fwrite($fp, "\n");
        fclose($fp);
    }

    /* read river data from file */
    function readFromJson() {
        $json = file_get_contents($this->filename);
        $this->scheduledSectionsData = json_decode($json, true); // truely we do want this to be an array PHP
        return true;
    }

    /* HTML editable form */
    public function editScheduledSectionsForm() {
        $reply = "";
        $sectionCount = 1;

        $reply .= "<form action='scheduled-section.php' method='post'>\n";
        $reply .= "<input type='hidden' name='scheduledUpdates' />\n";
        foreach($this->scheduledSectionsData as $jsonid => $scheduledSection) {
            $reply .= "<div class='form' id='section-" . $sectionCount . "'>\n";
            $reply .= $this->editScheduledSectionFormLine($scheduledSection, $sectionCount);
            //$reply .= "<input type='button' name='${sectionCount}_adddate' value='Add Date ${sectionCount}' class='adddate' onclick='adddate(${sectionCount});' />\n";
            $datesLength = 0;
            if (isset($scheduledSection['dates'])) {
                $datesLength = sizeof($scheduledSection['dates']);
            }
            $reply .= "<button type='button' name='add-${sectionCount}-$datesLength' class='add adddate btn btn-success'>Add Date</button>";
            $reply .= "<button type='button' name='delete-${sectionCount}' class='delete'>Delete Section</button>";
            $reply .= "</div>\n";
            // $reply .= "<input type='submit' name='${sectionCount}_delete' value='&#10060; Delete ${sectionCount}' class='delete' />\n";
            $sectionCount++;
        }
        $reply .= "<input type='submit' name='save' value='Save' />\n";
        $reply .= "</form>\n";
        return $reply;
    }

    /* HTML editable form for a river section */
    public function editScheduledSectionFormLine($scheduledSection, $sectionCount=0) {
        /* for newly added fields we can explicitly set them to empty here
        if (!array_key_exists('access_issue', $scheduledSection)) {
            $scheduledSection['access_issue'] = '';
        }
        */
        $reply = "";
        $reply .= "<legend>" . $sectionCount . ") " . $scheduledSection['name'] . "</legend>\n";
        $reply .= $this->editScheduledSectionFormInputItem("River/Section Name", "${sectionCount}_sectionname", $scheduledSection['name']);
        $reply .= $this->editScheduledSectionFormInputItem("Latitude", "${sectionCount}_latitude", $scheduledSection['latitude'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Longitude", "${sectionCount}_longitude", $scheduledSection['longitude']);
        $reply .= $this->editScheduledSectionFormInputItem("Grade", "${sectionCount}_grade", $scheduledSection['grade'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Guidebook Link", "${sectionCount}_guidebook_link", $scheduledSection['guidebook_link']);
        $reply .= $this->editScheduledSectionFormInputItem("SCA Guidebook No", "${sectionCount}_sca_guidebook_no", $scheduledSection['sca_guidebook_no'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Info Link", "${sectionCount}_info_link", $scheduledSection['info_link']);
        $reply .= $this->editScheduledSectionFormInputItem("Acccess Issue Link", "${sectionCount}_access_issue", $scheduledSection['access_issue'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Google My Maps Link", "${sectionCount}_google_mymaps", $scheduledSection['google_mymaps']);
        $reply .= $this->editScheduledSectionFormInputItem("KML Link", "${sectionCount}_kml", $scheduledSection['kml'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Webcam", "${sectionCount}_webcam", $scheduledSection['webcam']);
        $reply .= $this->editScheduledSectionFormInputItem("Notes", "${sectionCount}_notes", $scheduledSection['notes'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Put In Latitude", "${sectionCount}_put_in_lat", $scheduledSection['put_in_lat']);
        $reply .= $this->editScheduledSectionFormInputItem("Put In Longitude", "${sectionCount}_put_in_long", $scheduledSection['put_in_long'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Get Out Latitude", "${sectionCount}_get_out_lat", $scheduledSection['get_out_lat']);
        $reply .= $this->editScheduledSectionFormInputItem("Get Out Longitude", "${sectionCount}_get_out_long", $scheduledSection['get_out_long'], "right");
        $reply .= $this->editScheduledSectionFormInputItem("Constant (0/1 boolean)", "${sectionCount}_constant", $scheduledSection['constant']);
        $reply .= "<div id='{$sectionCount}_dates' class='datesdiv'/>\n";
        if (isset($scheduledSection['dates'])) {
            foreach($scheduledSection['dates'] as $dateid => $date) {
                $reply .= "<div id='div-${sectionCount}_date_{$dateid}'>";
                $reply .= $this->editScheduledSectionFormInputItem("Date {$dateid}", "${sectionCount}_date_{$dateid}", $date);
                $reply .= "<button type='button' name='delete-${sectionCount}_date_{$dateid}' class='delete-date'>&#10060;</button><br />\n";
                $reply .= "</div>";
            }
        }
        $reply .= "</div><!-- id=X-dates -->\n";
        //$reply .= "</div><!-- id=section-X -->\n";
        return $reply;
    }

    /* one text field in the river form */
    public function editScheduledSectionFormInputItem($text, $name, $value, $column="left") {
        $value = str_replace("'", "&#39;", $value);
        $reply = "";
        $reply .= "<label for='{$name}' class='{$column}'>{$text}:</label>\n";
        $reply .= "<input type='text' name='{$name}' value='{$value}' class='{$column}' /> \n";
        return $reply;
    }

    /* print the javascript */
    public function editScheduledSectionsFormJavascript() {
?>
    <script>
        $(document).ready( function() {
            var currentDateCount = 0;
            $('.add').click(function() {  
                //console.log('add button clicked' + $(this).attr('name'));
                var dateValues = $(this).attr('name').split("-");
                var sectionCount = dateValues[1];
                var dateCount = dateValues[2];
                if (currentDateCount == 0) {
                    currentDateCount = dateCount;
                }
                newInput = '<div id="div-'+sectionCount+'_date_'+currentDateCount+'">';
                newInput += '<label for="'+sectionCount+'_date_'+currentDateCount+'" class="left">Date '+currentDateCount+': </label>';
                newInput += "<input type='text' name='"+sectionCount+"_date_"+currentDateCount+"' value='' /> ";
                // The added delete button does not call the .delete-date function below for some reason
                //newInput += "<button type='button' name='delete-"+sectionCount+"_date_"+ currentDateCount +"' class='delete-date'>&#10060;</button>";
                newInput += "</div>\n";
                //console.log('adding to: ' + '#' + sectionCount + '_dates');
                $('#' + sectionCount + '_dates').append(newInput);
                currentDateCount++;
            });  
            $('.delete').click(function() {  
                //console.log('delete section' + $(this).attr('name'));
                var sectionCount = $(this).attr('name').split("-")[1];
                //console.log('delete section count:' + sectionCount);
                $('#section-' + sectionCount).remove();
            });  
            $('.delete-date').click(function() {  
                console.log('delete date button: ' + $(this).attr('name'));
                var dateNumber = $(this).attr('name').split("-")[1];
                console.log('delete date count:' + dateNumber);
                $('#div-' + dateNumber).remove();
            });  
      });
    </script>
<?php
    }

    /* read submitted HTML form to update rivers */
    public function updateScheduledSections($postData) {
        $sectionCount = 0;
        $newScheduledSectionsData = array();
        while (true) {
            $sectionCount++;
            // code red, this count is an arbitrary number which limits how many we can add, fix coding somehow
            if ($sectionCount > 100) {
                break;
            }

            if (isset($postData["{$sectionCount}_sectionname"])) {
                $scheduledSection = [];
                $scheduledSection['name'] = $postData["{$sectionCount}_sectionname"];
                $scheduledSection['latitude'] = $postData["{$sectionCount}_latitude"];
                $scheduledSection['longitude'] = $postData["{$sectionCount}_longitude"];
                $scheduledSection['grade'] = $postData["{$sectionCount}_grade"];
                $scheduledSection['guidebook_link'] = $postData["{$sectionCount}_guidebook_link"];
                $scheduledSection['sca_guidebook_no'] = $postData["{$sectionCount}_sca_guidebook_no"];
                $scheduledSection['info_link'] = $postData["{$sectionCount}_info_link"];
                $scheduledSection['access_issue'] = $postData["{$sectionCount}_access_issue"];
                $scheduledSection['google_mymaps'] = $postData["{$sectionCount}_google_mymaps"];
                $scheduledSection['kml'] = $postData["{$sectionCount}_kml"];
                $scheduledSection['webcam'] = $postData["{$sectionCount}_webcam"];
                $scheduledSection['notes'] = $postData["{$sectionCount}_notes"];
                $scheduledSection['put_in_lat'] = $postData["{$sectionCount}_put_in_lat"];
                $scheduledSection['put_in_long'] = $postData["{$sectionCount}_put_in_long"];
                $scheduledSection['get_out_lat'] = $postData["{$sectionCount}_get_out_lat"];
                $scheduledSection['get_out_long'] = $postData["{$sectionCount}_get_out_long"];
                $scheduledSection['constant'] = $postData["{$sectionCount}_constant"];
                $scheduledSection['dates'] = array();
                $dateCount = 0;
                while (true) {
                    if (isset($postData["{$sectionCount}_date_{$dateCount}"])) {
                        $scheduledSection['dates'][] = $postData["{$sectionCount}_date_{$dateCount}"];
                    } else {
                        break;
                    }
                    $dateCount++;
                }
                $newScheduledSectionsData[] = $scheduledSection;
                try {
                    $this->validateScheduledSectionUpdateData($scheduledSection);
                } catch (Exception $e) {
                    $name = $scheduledSection['name'];
                    return "<b>&#9888;Not updated $name</b><br />Validation error: " . $e->getMessage();
                }
            }
        }

        $this->scheduledSectionsData = $newScheduledSectionsData;
        $this->writeToJson();
        return "Updated schedule sections data";
    }

    /* do validation on river section values
       throw exception if a problem
    */
    private function validateScheduledSectionUpdateData($scheduledSection) {
        $this->validateFloat("Longitude", $scheduledSection['longitude']);
        $this->validateFloat("Latitude", $scheduledSection['latitude']);
        $this->validateFloatOrEmpty("Put In Longitude", $scheduledSection['put_in_long']);
        $this->validateFloatOrEmpty("Put In Latitude", $scheduledSection['put_in_lat']);
        $this->validateFloatOrEmpty("Get Out Longitude", $scheduledSection['get_out_long']);
        $this->validateFloatOrEmpty("Get Out Latitude", $scheduledSection['get_out_lat']);
        $this->validateNotNegative("Latitude", $scheduledSection['latitude']);
        $this->validateBoolean("Constant", $scheduledSection['constant']);
        if (!filter_var($scheduledSection['name'], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z \(\)]+$/")))) {
            throw new Exception("Name not text");
        }
        if (isset($scheduledSection['dates'])) {
            foreach($scheduledSection['dates'] as $date) {
                $this->validateDate("Date", $date);
            }
        }
    }

    /* throw exception if it's not a float */
    private function validateFloat($name, $data) {
        if (filter_var($data, FILTER_VALIDATE_FLOAT) === false && filter_var($data, FILTER_VALIDATE_INT) === false) {
            throw new Exception("$name $data is not a number");
        }
    }

    /* throw exception if it's not a float or empty */
    private function validateFloatOrEmpty($name, $data) {
        if ($data != "" && filter_var($data, FILTER_VALIDATE_FLOAT) === false && filter_var($data, FILTER_VALIDATE_INT) === false) {
            throw new Exception("$name $data is not a number");
        }
    }

    /* throw exception if it's negatuve */
    private function validateNotNegative($name, $data) {
        if ($data < 0) {
            throw new Exception("$name is negative");
        }
    }

    /* throw exception if it's negatuve */
    private function validateBoolean($name, $data) {
        $int = (int) $data;
        if ($int != "0" && $int != "1") {
            throw new Exception("$name is not 0 or 1");
        }
    }

    /* throw exception if it's negatuve */
    private function validateDate($name, $data) {
        if ($data == "") {
            return;
        }
        $date = explode("-", $data);
        if ($date[0] < 2020 or $date[0] > 2100) {
            throw new Exception("$data is not a valid year");
        }
        if ($date[1] < 1 or $date[1] > 12) {
            throw new Exception("$data is not a valid month");
        }
        if ($date[2] < 1 or $date[2] > 31) {
            throw new Exception("$data is not a valid day");
        }
    }

    /* HTML editable form for adding a new section */
    public function addScheduledSectionForm() {
        $scheduledSection = array();
        $scheduledSection['name'] = "";
        $scheduledSection['longitude'] = "";
        $scheduledSection['latitude'] = "";
        $scheduledSection['grade'] = "";
        $scheduledSection['guidebook_link'] = "";
        $scheduledSection['sca_guidebook_no'] = "";
        $scheduledSection['info_link'] = "";
        $scheduledSection['access_issue'] = "";
        $scheduledSection['google_mymaps'] = "";
        $scheduledSection['kml'] = "";
        $scheduledSection['webcam'] = "";
        $scheduledSection['notes'] = "";
        $scheduledSection['put_in_long'] = "";
        $scheduledSection['put_in_lat'] = "";
        $scheduledSection['get_out_long'] = "";
        $scheduledSection['get_out_lat'] = "";
        $scheduledSection['constant'] = "";
        $scheduledSection['dates'] = array();

        $reply = "<form action='scheduled-section.php' method='post'>\n";
        $reply .= "<div class='form' id='section-new'>";
        $reply .= "<legend>Add New Scheduled Section</legend>";
        $reply .= $this->editScheduledSectionFormLine($scheduledSection);
        $reply .= "<input type='submit' name='add' value='Add New Scheduled Section' />\n";
        $reply .= "</div>";
        $reply .= "</form>\n";
        return $reply;
    }

    /* process add new section submit */
    public function addNewScheduledSection($postData) {
        $scheduledSection = array();
        $scheduledSection['name'] = $postData['0_sectionname'];
        $scheduledSection['longitude'] = $postData['0_longitude'];
        $scheduledSection['latitude'] = $postData['0_latitude'];
        $scheduledSection['grade'] = $postData['0_grade'];
        $scheduledSection['guidebook_link'] = $postData['0_guidebook_link'];
        $scheduledSection['sca_guidebook_no'] = $postData['0_sca_guidebook_no'];
        $scheduledSection['info_link'] = $postData['0_info_link'];
        $scheduledSection['access_issue'] = $postData['0_access_issue'];
        $scheduledSection['google_mymaps'] = $postData['0_google_mymaps'];
        $scheduledSection['kml'] = $postData['0_kml'];
        $scheduledSection['webcam'] = $postData['0_webcam'];
        $scheduledSection['notes'] = $postData['0_notes'];
        $scheduledSection['put_in_long'] = $postData['0_put_in_long'];
        $scheduledSection['put_in_lat'] = $postData['0_put_in_lat'];
        $scheduledSection['get_out_long'] = $postData['0_get_out_long'];
        $scheduledSection['get_out_lat'] = $postData['0_get_out_lat'];
        $scheduledSection['constant'] = $postData['0_constant'];
        $scheduledSection['dates'] = array();
        try {
            $this->validateScheduledSectionUpdateData($scheduledSection);
        } catch (Exception $e) {
            $name = $postData['0_sectionname'];
            return "<b>&#9888;Not added $name</b><br />Validation error: " . $e->getMessage() . "<br />Click Back to retry";
        }
        $this->scheduledSectionsData[] = $scheduledSection;

        $this->writeToJson();
        return "Added new section " . $scheduledSection['name'];
    }

    // Used by table-view.php to print the table
    public function printTable() {
        $grabSepaGauges = new GrabSepaGauges;
        $sepaGaugesData = $grabSepaGauges->sepaData();
        $grabSepaRivers = new GrabSepaRivers();
        if (!$grabSepaRivers->readFromJson()) {
            print "<h1>Sorry no river reading data available, try again soon</h1>";
            die();
        }
        foreach($this->riverSectionsData as $jsonid => $riverSection) {
            //read river data and pass to jsForRiver
            $forecast = new GrabWeatherForecast();
            $forecast->doGrabWeatherForecast($riverSection['gauge_location_code'], $riverSection['longitude'], $riverSection['latitude']);
            $forecastHtml = $forecast->forecastHtml();
            
            $this->trForRiver($jsonid, $riverSection, $sepaGaugesData, $grabSepaRivers->riversReadingsData[$riverSection['gauge_location_code']], $forecastHtml);
        }
    }

    /* takes a reading time "24/01/2019 00:15:00" and returns true if it is over 24 days old */
    private function readingIsOld($currentReadingTime) {
        $old = 60 * 60 * 24; // 24 hours
        $time_explode = explode('/', $currentReadingTime); // need to swap date and month cos PHP likes US date format
        $ustime = $time_explode[1] . '/' . $time_explode[0] . '/' . $time_explode[2];
        $timestamp = strtotime($ustime);
        if ((time() - $timestamp) > $old) {
            return true;
        }
        return false;
    }

    private function trForRiver($jsonid, $riverSection, $sepaGaugesData, $riverReadingData, $forecastHtml) {
        $sepaGaugeLocationCode = $riverSection['gauge_location_code'];
        $gaugeName = $sepaGaugesData[$sepaGaugeLocationCode]['gauge_name'];
        print "<tr class='riverSectionRow'>\n";
        if (!array_key_exists($sepaGaugeLocationCode, $sepaGaugesData)) {
            //print "\n// Error: no SEPA reading for river " . $riverSection['name'] . "\n";
            //return;
            $riverReadingData['currentReading'] = 0;
            $waterLevelValue = "NO_GUAGE_DATA";
        } elseif ($riverReadingData['currentReading'] == '-1' || $this->readingIsOld($riverReadingData['currentReadingTime'])) {
            $waterLevelValue = "OLD_DATA";
        } else {
            $waterLevelValue = $this->waterLevelValue($riverReadingData['currentReading'], $riverSection);
        };
        
        if ($riverSection['scrape_value'] == $riverSection['huge_value']) {
            $waterLevelValue = "NEEDS_CALIBRATIONS";
        }


        $linkContent = "<div class='riverLinks'>";
        if (!empty($riverSection['notes'])) {
            $linkContent .= "<img width='16' height='16' title='Notes' src='/wheres-the-water/pics/notes.png' /> <b>Notes:</b> ".$riverSection['notes']."<br />";
        }
        $linkContent .= "<span class='desktop'><a target='_blank' rel='noopener' href='https://www2.sepa.org.uk/waterlevels/default.aspx?sd=t&lc=".$riverSection['gauge_location_code']."'><img width='16' height='16' title='SEPA gauge link' src='/wheres-the-water/pics/graph-icon.png'/> SEPA Gauge: ".$gaugeName."</a><br /></span>";
        $linkContent .= "<span class='mobile'><a target='_blank' rel='noopener' href='http://www.riverlevels.mobi/SiteDetails/Index/".$riverSection['gauge_location_code']."'><img width='16' height='16' title='SEPA gauge link - mobile friendly' src='/wheres-the-water/pics/graph-icon.png'/> SEPA Gauge: ".$gaugeName."</a><br /></span>";
        $linkContent .= "<img title='Open maps Link' src='/wheres-the-water/pics/osm.png' width='16' height='16' /> <a target='_blank' rel='noopener' href='https://www.openstreetmap.org/?mlat=".$riverSection['latitude']."&mlon=".$riverSection['longitude']."#map=12/".$riverSection['latitude']."/".$riverSection['longitude']."'>OpenStreetMap</a> / ";
        $linkContent .= "<span class='desktop'><a target='_blank' rel='noopener' href='https://www.bing.com/maps?cp=".$riverSection['latitude']."~".$riverSection['longitude']."&sp=point.".$riverSection['latitude']+"_".$riverSection['longitude']."&lvl=14&style=s'>Ordnance Survey</a> / </span>";
        $linkContent .= "<span class='desktop'><a target='_blank' rel='noopener' href='https://maps.google.com/maps?z=12&t=h&q=loc:".$riverSection['latitude']."+".$riverSection['longitude']."'>Google Maps</a></span>";
        $linkContent .= "<span class='mobile'><a href='geo:0,0?q=".$riverSection['latitude'].",".$riverSection['longitude']."'>Maps App</a></span><br />";
        
        if (!empty($riverSection['guidebook_link'])) {
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['guidebook_link']."'><img width='16' height='16' title='UKRGB Link' src='/wheres-the-water/pics/ukrgb.ico'/> UKRGB</a><br />";
        }
        if (!empty($riverSection['sca_guidebook_no'])) {
            $linkContent .= "<img width='16' height='16' title='SCA WW Guidebook number' src='/wheres-the-water/pics/sca.png' /> SCA Guidebook No ".$riverSection['sca_guidebook_no']."<br />";
        }
        if (!empty($riverSection['access_issue'])) {
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['access_issue']."'><img width='16' height='16' title='Access Issue Link' src='/wheres-the-water/pics/warning.png' /> Access Issue</a><br />";
        }
        if (!empty($riverSection['google_mymaps'])) {
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['google_mymaps']."'><img width='16' height='16' title='Google MyMaps Link' src='/wheres-the-water/pics/google-mymaps.png' /> Google MyMaps</a><br />";
        }
        /*
        if (!empty($riverSection['kml'])) {
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['kml']."'><img width='16' height='16' title='KML Map' src='/wheres-the-water/pics/kml.png' /> KML Map Layer</a><br />";
        }
        */
        if (!empty($riverSection['webcam'])) {
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['webcam']."'><img width='16' height='16' title='Webcam' src='/wheres-the-water/pics/webcam.png' /> Webcam</a><br />";
        }
        if (!empty($riverSection['classification'])) {
            $classificatonIcon = explode(" ", $riverSection['classification']);
            $classificatonIcon = $classificatonIcon[0];
            $classificatonIcon = strtolower($classificatonIcon);
            $linkContent .= "<a target='_blank' rel='noopener' href='".$riverSection['classification_url']."'><img width='16' height='16' title='Webcam' src='/wheres-the-water/pics/classification-".$classificatonIcon.".png' /> Water Classification: ".$riverSection['classification']."</a><br />";
        }
        $chartLink = $this->riverZoneStations->link($riverSection);
        if ($chartLink != false) {
            $linkContent .= "<span class='desktop'><a target='_blank' rel='noopener' href='".$this->riverZoneStations->link($riverSection)."'><img width='16' height='16' title='Webcam' src='/wheres-the-water/pics/chart-yearly.png' /> RiverZone Chart</a><br /></span>";
            $linkContent .= "<span class='mobile'><a target='_blank' rel='noopener' href='".$this->riverZoneStations->link($riverSection, true)."'><img width='16' height='16' title='Webcam' src='/wheres-the-water/pics/chart-yearly.png' /> RiverZone Chart</a><br /></span>";
        }
        /* Render the picture */
        $filename = strtolower($riverSection['name']);
        $filename = str_replace(" ", "-", $filename);
        $filename = str_replace("(", "", $filename);
        $filename = str_replace(")", "", $filename);
        /* charts not on new server jriddell 2019-08-01
        $linkContent .= "<img width='16' height='16' title='Charts' src='/wheres-the-water/pics/chart-monthly.png' /> <a data-toggle='lightbox' target='_blank' rel='noopener' href='/wheres-the-water/charts/${filename}-weekly.png'>Weekly Chart</a> / ";
        $linkContent .= "<a data-toggle='lightbox' target='_blank' rel='noopener' href='/wheres-the-water/charts/${filename}-monthly.png'>Monthly Chart</a> / ";
        $linkContent .= "<a data-toggle='lightbox' target='_blank' rel='noopener' href='/wheres-the-water/charts/${filename}-yearly.png'>Yearly Chart</a><br />";
        */
        $linkContent .= "</div><!--riverLinks-->";
        
        //User friendly water level values
        $waterLevelValueReadable = array('EMPTY'=>'Empty', 'SCRAPE'=>'Scrape', 'LOW'=>'Low', 'MEDIUM'=>'Medium', 'HIGH'=>'High', 'VERY_HIGH'=>'Very High', 'HUGE'=>'Huge', 'NO_GUAGE_DATA'=>'No Gauge Data', 'OLD_DATA'=>'Old Data', 'NEEDS_CALIBRATIONS'=>'Needs Calibrations');
        
        //Symbols for trends
        $trends = array('RISING' => '&#x25B2;', 'FALLING' => '&#x25BC;', 'STABLE' => '<b>-</b>', '' => '-');
        
        // Making the table orderable by water level
        $waterLevelValueArray = array('NO_GUAGE_DATA', 'OLD_DATA', 'NEEDS_CALIBRATIONS', 'EMPTY', 'SCRAPE', 'LOW', 'MEDIUM', 'HIGH', 'VERY_HIGH', 'HUGE');
        $waterLevelValueNumber = array_search($waterLevelValue, $waterLevelValueArray);

        // Create an array of info
        $infoArray = array('riverSection' => $riverSection['name'],
            'riverGrade' => $riverSection['grade'],
            'waterLevelValue' => $waterLevelValue,
            'waterLevelValueRead' => $waterLevelValueReadable[$waterLevelValue],
            'waterLevelValueNumber' => $waterLevelValueNumber,
            'latitude' => $riverSection['latitude'],
            'longitude' => $riverSection['longitude'],
            'trend' => $riverReadingData['trend'],
            'currentReadingTime' => $riverReadingData['currentReadingTime'],
            'currentReading' => $riverReadingData['currentReading'],

            'trend' => $riverReadingData['trend'],
            'trendSymbol' => $trends[$riverReadingData['trend']],

            'scrapeValue' => $riverSection['scrape_value'],
            'lowValue' => $riverSection['low_value'],
            'mediumValue' => $riverSection['medium_value'],
            'highValue' => $riverSection['high_value'],
            'veryHighValue' => $riverSection['very_high_value'],
            'hugeValue' => $riverSection['huge_value'],
            'gaugeLocationCode' => $riverSection['gauge_location_code'],
            'link' => $linkContent,
            'forecast' => $forecastHtml
        );
        
        switch ($infoArray['waterLevelValue']){
            case 'EMPTY':
                $color = '#CCCCCC';
                break;
            case 'SCRAPE':
                $color = '#CCFFCC';
                break;
            case 'LOW':
                $color = '#00FF00';
                break;
            case 'MEDIUM':
                $color = '#FFFF33';
                break;
            case 'HIGH':
                $color = '#FFC004';
                break;
            case 'VERY_HIGH':
                $color = '#FF6060';
                break;
            case 'HUGE':
                $color = '#FF0000';
                break;
            case 'OLD_DATA':
                $color = '#FFFFFF';
                break;
            case 'NO_GUAGE_DATA':
                $color = '#FFFFFF';
                break;
            case 'CONVERSION_UNKNOWN':
                $color = '#FFFFFF';
                break;
            default:
                $color = '#FFFFFF';
        }
        

        $displayedValues = array('riverSection', 'riverGrade', 'waterLevelValueRead', 'trendSymbol', 'link', 'forecast');

        
        // Populate the table
        foreach ($infoArray as $class => $val){
            if (in_array($class, $displayedValues)){
                $visibility = " style='background-color: $color' ";
            }
            else if ($class == 'currentReading'){
                $visibility = " style='display: none; background-color: $color' ";
                $class .= ' clickable';
            }
            else {
                $visibility = " style='display: none' ";
            }
            print "<td class='$class'$visibility>$val</td>\n";
        }
        print "</tr>\n";
    }

    /* javascript for website - note this is not used */
    public function outputJavascript() {
        $grabSepaGauges = new GrabSepaGauges;
        $sepaGaugesData = $grabSepaGauges->sepaData();
        /*
        print "sepaData: " . $sepaGaugesData['234189']['current_level'] . ";\n";
        print "json: " . json_encode($sepaGaugesData, JSON_PRETTY_PRINT) . ";\n";
        */
        //print json_encode($this->riverSectionsData, JSON_PRETTY_PRINT);
        //print json_encode($sepaGaugesData, JSON_PRETTY_PRINT);
        $grabSepaRivers = new GrabSepaRivers();
        if (!$grabSepaRivers->readFromJson()) {
            print "</script>";
            print "<h1>Sorry no river reading data available, try again soon</h1>";
            die();
        }
        foreach($this->riverSectionsData as $jsonid => $riverSection) {
            //read river data and pass to jsForRiver
            $this->jsForRiver($jsonid, $riverSection, $sepaGaugesData, $grabSepaRivers->riversReadingsData[$riverSection['gauge_location_code']]);
        }
    }

    /* note this is not used */
    private function jsForRiver($jsonid, $riverSection, $sepaGaugesData, $riverReadingData) {
        $sepaGaugeLocationCode = $riverSection['gauge_location_code'];
        $waterLevelValue = "";

        if (!array_key_exists($sepaGaugeLocationCode, $sepaGaugesData)) {
            print "\n// Warning: no SEPA reading for river " . $riverSection['name'] . "\n";
            $riverReadingData['currentReading'] = 0;
            $waterLevelValue = "NO_GUAGE_DATA";
        } elseif ($riverReadingData['currentReading'] == '-1') {
            $waterLevelValue = "OLD_DATA";
        } else {
            $waterLevelValue = $this->waterLevelValue($riverReadingData['currentReading'], $riverSection);
        }
        if ($riverSection['scrape_value'] == $riverSection['huge_value']) {
            $waterLevelValue = "NEEDS_CALIBRATIONS";
        }

        print "var point$jsonid = new GLatLng(".$riverSection['latitude'].",".$riverSection['longitude'].");\n";
        print "markerOptions = { icon:${waterLevelValue}Icon };\n";
        print "var marker$jsonid = new GMarker(point$jsonid, markerOptions);\n";
        print "GEvent.addListener(marker$jsonid, \"mouseover\", function() {\n";
        print "    showSectionInfo(\"".$riverSection['name']."\", \"$waterLevelValue\", \"".$riverReadingData['currentReadingTime']."\", \"".$riverReadingData['currentReading']."\", \"".$riverReadingData['trend']."\" );\n";
        print "    showConversionInfo(\"$waterLevelValue\", \"".$riverSection['scrape_value']."\",\"".$riverSection['low_value']."\", \"".$riverSection['medium_value']."\", \"".$riverSection['high_value']."\", \"".$riverSection['very_high_value']."\", \"".$riverSection['huge_value']."\");\n";
        print "});\n";
        print "GEvent.addListener(marker$jsonid, \"click\", function() {  showPicWin('https://www2.sepa.org.uk/waterlevels/default.aspx?sd=t&lc=".$riverSection['gauge_location_code']."') });\n";
        print "map.addOverlay(marker$jsonid);\n\n";
    }

    // return the human readable water level (low, medium etc)
    //TODO will puting a space in very high break anything? yep, fix
    /* Note: this is not used */
    private function waterLevelValue($currentLevel, $riverSection) {
        if ($currentLevel < $riverSection['scrape_value']) {
            return "EMPTY";
        } elseif ($currentLevel < $riverSection['low_value']) {
            return "SCRAPE";
        } elseif ($currentLevel < $riverSection['medium_value']) {
            return "LOW";
        } elseif ($currentLevel < $riverSection['high_value']) {
            return "MEDIUM";
        } elseif ($currentLevel < $riverSection['very_high_value']) {
            return "HIGH";
        } elseif ($currentLevel < $riverSection['huge_value']) {
            return "VERY_HIGH";
        } else {
            return "HUGE";
        }
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
