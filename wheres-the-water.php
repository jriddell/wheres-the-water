<?php
require_once 'common.php';
require_once 'config.php';
heading();
require_once 'lib/RiverSections.php';
$riverSections = new RiverSections;
$riverSections->readFromJson();

?>

<div style="float: right">

      <div class="content"> <table  cellspacing="0" class="riverlevels">
			<tr>
			  <td class="dataHeaders" colspan="2">Data Last Polled</td>
			</tr>
			<tr>
			  <td class="dataValues" style="width:190px" colspan="2"><?php print $riverSections->downloadTime() ?></td>
			</tr>
			<tr>
			  <td colspan="2" height="4"></td>
          		</tr>
			<tr>
			  <td class="dataHeaders" colspan="2">Most Recent SEPA Reading</td>
			  </tr>
			 <tr>
			  <td colspan="2" class="dataValues" style="width:190px" ><?php print $riverSections->calculateMostRecentReading() ?></td>
			</tr>
	</table>
<div class="riverHeader" id="sectionname">&nbsp;</div>

<div class="riverHeader" id="level">&nbsp;</div>

<div class="riverHeader" id="lastUpdated">&nbsp;</div>

<table>
	<tbody>
		<tr>
			<td class="dataHeaders">Current Reading</td>
			<td class="dataValues" id="currentReading">&nbsp;</td>
		</tr>
		<tr>
			<td class="dataHeaders">Trend</td>
			<td class="dataValues" id="trend">&nbsp;</td>
		</tr>
		<tr>
			<td class="dataHeaders" colspan="2">Callibration used:</td>
		</tr>
		<tr>
			<td align="right" colspan="2" bgcolor="#424242">
			<table class="sub-table">
				<tbody>
					<tr>
						<td class="callibHeaders" bgcolor="#FF0000">Huge</td>
						<td class="callibVals" id="huge">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FF6060">Very High</td>
						<td class="callibVals" id="veryHigh">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FFC004">High</td>
						<td class="callibVals" id="high">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FFFF33">Medium</td>
						<td class="callibVals" id="medium">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#00FF00">Low</td>
						<td class="callibVals" id="low">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#CCFFCC">Scrapeable</td>
						<td class="callibVals" id="justRunnable">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#CCCCCC">Empty</td>
						<td class="callibVals" id="empty">0</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
 </div>

 </div>

<div id="map" style="height: 500px; width: 100%; max-width: 800px; "></div>

<script language="javascript" src="http://maps.google.com/maps?file=api&amp;v=3&amp;sensor=false&amp;key=AIzaSyD-WF6gFouMUCMfdvzw2ajMeOrE-F6RlRY" ></script>


  <script>
  
if(document.getElementById && document.createTextNode) {
    window.onload=function(){
        createMap();
    }
}

    function createMap() {
        if (GBrowserIsCompatible()) {
            var map = new GMap2(document.getElementById("map"));
            map.addMapType(G_PHYSICAL_MAP);
            map.setCenter(new GLatLng(57.172,-4.6582), 7);
     
            // map.setMapType(G_HYBRID_MAP);
            map.setMapType(G_NORMAL_MAP);

            var mapControl = new GMapTypeControl();
            var mapTypesPosition = new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10,10));
            map.addControl(mapControl, mapTypesPosition);
            map.addControl(new GOverviewMapControl());
            map.addControl(new GSmallZoomControl());

        var tinyIcon = new GIcon();
        tinyIcon.image = "http://labs.google.com/ridefinder/images/mm_20_yellow.png";
        tinyIcon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
        tinyIcon.iconSize = new GSize(12, 20);
        tinyIcon.shadowSize = new GSize(22, 20);
        tinyIcon.iconAnchor = new GPoint(6, 20);

        var EMPTYIcon = new GIcon();
        EMPTYIcon.image = "/wheres-the-water/pics/EMPTY.png";
        EMPTYIcon.iconSize = new GSize(10,10);
        EMPTYIcon.iconAnchor = new GPoint(5,5);

        var SCRAPEIcon = new GIcon();
        SCRAPEIcon.image = "/wheres-the-water/pics/SCRAPE.png";
        SCRAPEIcon.iconSize = new GSize(10,10);
        SCRAPEIcon.iconAnchor = new GPoint(5,5);

        var LOWIcon = new GIcon();
        LOWIcon.image = "/wheres-the-water/pics/LOW.png";
        LOWIcon.iconSize = new GSize(10,10);
        LOWIcon.iconAnchor = new GPoint(5,5);    

        var MEDIUMIcon = new GIcon();
        MEDIUMIcon.image = "/wheres-the-water/pics/MEDIUM.png";
        MEDIUMIcon.iconSize = new GSize(10,10);
        MEDIUMIcon.iconAnchor = new GPoint(5,5);
        
        var HIGHIcon = new GIcon();
        HIGHIcon.image = "/wheres-the-water/pics/HIGH.png";
        HIGHIcon.iconSize = new GSize(10,10);
        HIGHIcon.iconAnchor = new GPoint(5,5);

        var VERY_HIGHIcon = new GIcon();
        VERY_HIGHIcon.image = "/wheres-the-water/pics/VERY_HIGH.png";
        VERY_HIGHIcon.iconSize = new GSize(10,10);
        VERY_HIGHIcon.iconAnchor = new GPoint(5,5);
        
        var HUGEIcon = new GIcon();
        HUGEIcon.image = "/wheres-the-water/pics/HUGE.png";
        HUGEIcon.iconSize = new GSize(10,10);
        HUGEIcon.iconAnchor = new GPoint(5,5);

        var OLD_DATAIcon = new GIcon();
        OLD_DATAIcon.image = "/wheres-the-water/pics/OLD_DATA.png";
        OLD_DATAIcon.iconSize = new GSize(10,10);
        OLD_DATAIcon.iconAnchor = new GPoint(5,5);

        var NO_GUAGE_DATAIcon = new GIcon();
        NO_GUAGE_DATAIcon.image = "/wheres-the-water/pics/NO_GUAGE_DATA.png";
        NO_GUAGE_DATAIcon.iconSize = new GSize(10,10);
        NO_GUAGE_DATAIcon.iconAnchor = new GPoint(5,5);

        var CONVERSION_UNKNOWNIcon = new GIcon();
        CONVERSION_UNKNOWNIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/CONVERSION_UNKNOWN.gif";
        CONVERSION_UNKNOWNIcon.iconSize = new GSize(10,10);
        CONVERSION_UNKNOWNIcon.iconAnchor = new GPoint(5,5);
                
    <?php  
    $riverSections->outputJavascript();
    ?>


        } // if (GBrowserIsCompatible()) 
      
    } // function createMap()
</script>

<?php
footer();
