<?php
/* Copyright 2018 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 (or later) only
*/

/*
 Functions to read and write a river reading history
 Uses file to data/history-1234.json of format associative array of timestamp: reading
 {
    "2147483647": "1.24",
    "2147483123": "2.12"
 }
*/

include("config.php");

/* pChart library inclusions */
include("pchart/class/pData.class.php");
include("pchart/class/pDraw.class.php");
include("pchart/class/pImage.class.php"); 
 
class SepaRiverReadingHistory {
    const DATADIR = 'data';
    public $gauge_id;
    public $filename;
    
    function __construct($gauge_id) {
        $this->dataDir = ROOT . '/' . self::DATADIR;
        $this->gauge_id = $gauge_id;
        $this->filename = $this->dataDir . '/history-' . $this->gauge_id . '.json';
    }

    private function readJson() {
        if (file_exists($this->filename)) {
            $json = file_get_contents($this->filename);
            $this->riversReadingsHistory = json_decode($json, true);
        } else {
            $this->riversReadingsHistory = array();
        }
    }
    
    public function newReading($timeStamp, $waterLevel) {
        $this->readJson();
        $this->riversReadingsHistory[$timeStamp] = $waterLevel;
        $fp = fopen($this->filename, 'w');
        fwrite($fp, json_encode($this->riversReadingsHistory, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    public function writeChart($river, $daysHistory, $chartName) {
        $this->readJson();
        /* Create and populate the pData object */
        $MyData = new pData();  
        
        $timeHistory = $daysHistory * 24 * 60 * 60; // number of seconds of history we want
        $readings = array();
        $timeLabels = array();
        foreach ($this->riversReadingsHistory as $timestamp => $reading) {
            // only save readings from now to $daysHistory period
            if (time() - $timestamp < $timeHistory) {
                $readings[] = $reading;
                $timeLabels[] = date('D d M Y H:i', $timestamp);
            }
        }        
        $MyData->addPoints($readings, "Gauge Reading");
        $MyData->addPoints($timeLabels, "Times");
        $MyData->setAbscissa("Times");
        
        //$MyData->setSerieTicks("Probe 2",4);
        $MyData->setAxisName(0,"Gauge Readings");

        /* Create the pChart object */
        $myPicture = new pImage(1200,700,$MyData);

        /* Turn of Antialiasing */
        $myPicture->Antialias = FALSE;

        /* Add a border to the picture */
        $myPicture->drawGradientArea(0, 0, 1200, 700, DIRECTION_VERTICAL,
                    array("StartR"=>180,"StartG"=>180,"StartB"=>220,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0, 0, 1200, 700, DIRECTION_HORIZONTAL,
                    array("StartR"=>180,"StartG"=>180,"StartB"=>220,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->drawFromPNG(700, 10, "../pics/scottish-canoe-association.png");
        
        /* Add a border to the picture */
        $myPicture->drawRectangle(0, 0, 1199, 699, array("R"=>0,"G"=>0,"B"=>0));

        /* Write the chart title */ 
        $myPicture->setFontProperties(array("FontName"=>"/usr/share/fonts/truetype/ubuntu-font-family/UbuntuMono-R.ttf","FontSize"=>20));
        $myPicture->drawText(150,35,$river['name'] . "($chartName)",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

        /* Set the default font */
        $myPicture->setFontProperties(array("FontName"=>"/usr/share/fonts/truetype/ubuntu-font-family/UbuntuMono-R.ttf","FontSize"=>15));

        /* Define the chart area */
        $myPicture->setGraphArea(150,40,1200,520);

        /* Draw the scale */
        $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"GridAlpha"=>100,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE, "LabelRotation"=>45, "LabelSkip" => count($timeLabels) / 10); // show 10 labels
        $myPicture->drawScale($scaleSettings);

        /* Turn on Antialiasing */
        $myPicture->Antialias = TRUE;

        /* Enable shadow computing */
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

        /* Draw the area chart */
        $Threshold = "";
        $Threshold[] = array("Min"=>0,"Max"=>$river['scrape_value'],"R"=>210,"G"=>210,"B"=>210,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['scrape_value'],"Max"=>$river['low_value'],"R"=>208,"G"=>255,"B"=>215,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['low_value'],"Max"=>$river['medium_value'],"R"=>0,"G"=>255,"B"=>62,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['medium_value'],"Max"=>$river['high_value'],"R"=>255,"G"=>251,"B"=>0,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['high_value'],"Max"=>$river['very_high_value'],"R"=>255,"G"=>173,"B"=>0,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['very_high_value'],"Max"=>$river['huge_value'],"R"=>255,"G"=>157,"B"=>147,"Alpha"=>100);
        $Threshold[] = array("Min"=>$river['huge_value'],"Max"=>100,"R"=>255,"G"=>0,"B"=>10,"Alpha"=>100);
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
        $myPicture->drawAreaChart(array("Threshold"=>$Threshold));

        /* Draw a line chart over */
        $myPicture->drawLineChart(array("ForceColor"=>TRUE,"ForceR"=>0,"ForceG"=>0,"ForceB"=>0));

        /* Draw a plot chart over */
        $myPicture->drawPlotChart(array("PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-255,"BorderAlpha"=>80));

        /* Write the thresholds */
        $myPicture->drawThreshold($river['scrape_value'],array("WriteCaption"=>TRUE,"Caption"=>"Scrape ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
        $myPicture->drawThreshold($river['low_value'],array("WriteCaption"=>TRUE,"Caption"=>"Low ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
        $myPicture->drawThreshold($river['medium_value'],array("WriteCaption"=>TRUE,"Caption"=>"Medium ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
        $myPicture->drawThreshold($river['high_value'],array("WriteCaption"=>TRUE,"Caption"=>"High ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
        $myPicture->drawThreshold($river['very_high_value'],array("WriteCaption"=>TRUE,"Caption"=>"Very High ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));
        $myPicture->drawThreshold($river['huge_value'],array("WriteCaption"=>TRUE,"Caption"=>"Huge ^","Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));

        /* Render the picture */
        $filename = strtolower($river['name']);
        $filename = str_replace(" ", "-", $filename);
        $filename = str_replace("(", "", $filename);
        $filename = str_replace(")", "", $filename);
        $myPicture->render("charts/".$filename."-".$chartName.".png"); 
    }
}
