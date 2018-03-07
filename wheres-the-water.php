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
<div id="river-table-div" style="visibility: hidden">
	<table id="river-table">
		<tr><th>River Section</th><th>Level</th></tr>
		<?php printTable(); ?>
	</table>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAr0GC5SjROQdQKwS78LI-abrgyULq-9g&callback=initMap"></script>


  <script>

  function initMap() {
		
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 7,
			center: {lat: 57.172, lng:  -4.6582}
		});

		var iconBase = 'http://canoescotland.org/sites/all/themes/basestation_open/img/';
		var icons = {
			EMPTY: {
				icon: iconBase + 'EMPTY.gif'
			},
			LOW: {
				icon: iconBase + 'LOW.gif'
			},
			MEDIUM: {
				icon: iconBase + 'MEDIUM.gif'
			},
			HIGH: {
				icon: iconBase + 'HIGH.gif'
			},
			VERY_HIGH: {
				icon: iconBase + 'VERY_HIGH.gif'
			},
			HUGE: {
				icon: iconBase + 'HUGE.gif'
			},
			OLD_GAUGE_DATA: {
				icon: iconBase + 'OLD_GAUGE_DATA.gif'
			},
			NO_DATA: {
				icon: iconBase + 'NO_DATA.gif'
			},
			CONVERSION_UNKNOWN: {
				icon: iconBase + 'CONVERSION_UNKNOWN.gif'
			},
		};
		
		jQuery('.riverSectionRow').each(function() {

			var last = false;

			// River data for map marker creation
			var riverSection = jQuery(this).find('.riverSection').text();
			var waterLevelValue = jQuery(this).find('.waterLevelValue').text();
			var latitude = jQuery(this).find('.latitude').text();
			var longitude = jQuery(this).find('.longitude').text();
			var currentReadingTime = jQuery(this).find('.currentReadingTime').text();
			var currentReading = jQuery(this).find('.currentReading').text();
			var trend = jQuery(this).find('.trend').text();
			var scrapeValue = jQuery(this).find('.scrapeValue').text();
			var lowValue = jQuery(this).find('.lowValue').text();
			var mediumValue = jQuery(this).find('.mediumValue').text();
			var highValue = jQuery(this).find('.highValue').text();
			var veryHighValue = jQuery(this).find('.veryHighValue').text();
			var hugeValue = jQuery(this).find('.hugeValue').text();
            var gaugeLocationCode = jQuery(this).find('.gaugeLocationCode').text();

            var contentString = "<div><p>" + riverSection + "</p><p>Level: " + currentReading + " (" + waterLevelValue + 
            ")</p><p>Trend: " + trend + "</p><p>Last reading: " + currentReadingTime + 
            "</p><p><a  target='_blank' rel='noopener' href='http://apps.sepa.org.uk/waterlevels/default.aspx?sd=t&lc=" + gaugeLocationCode + "'>Go to the SEPA gauge map</a></p></div>";
			
			if (jQuery(this).is('.riverSectionRow:last')){
				// If this is the last marker we need to know so we can add them to the map
				last = true;
			}
			
			var infowindow = new google.maps.InfoWindow({
			        		content: contentString
			});
			
			
			position = new google.maps.LatLng(latitude, longitude);
			        	
			        	
			var marker = new google.maps.Marker({
			      position: position,
			      map: map,
			      icon: icons[waterLevelValue].icon,
			      title: riverSection
			});

			        	
			marker.addListener('click', function(){
				infowindow.open(map, marker);
			});

			marker.addListener('mouseover', function(){
				showSectionInfo(riverSection, waterLevelValue, currentReadingTime, currentReading, trend);
				showConversionInfo(waterLevelValue, scrapeValue, lowValue, mediumValue, highValue, veryHighValue, hugeValue);
			});
		});
		
		

	}
  
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
        EMPTYIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/EMPTY.gif";
        EMPTYIcon.iconSize = new GSize(10,10);
        EMPTYIcon.iconAnchor = new GPoint(5,5);

        var SCRAPEIcon = new GIcon();
        SCRAPEIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/SCRAPE.gif";
        SCRAPEIcon.iconSize = new GSize(10,10);
        SCRAPEIcon.iconAnchor = new GPoint(5,5);

        var LOWIcon = new GIcon();
        LOWIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/LOW.gif";
        LOWIcon.iconSize = new GSize(10,10);
        LOWIcon.iconAnchor = new GPoint(5,5);    

        var MEDIUMIcon = new GIcon();
        MEDIUMIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/MEDIUM.gif";
        MEDIUMIcon.iconSize = new GSize(10,10);
        MEDIUMIcon.iconAnchor = new GPoint(5,5);
        
        var HIGHIcon = new GIcon();
        HIGHIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/HIGH.gif";
        HIGHIcon.iconSize = new GSize(10,10);
        HIGHIcon.iconAnchor = new GPoint(5,5);

        var VERY_HIGHIcon = new GIcon();
        VERY_HIGHIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/VERY_HIGH.gif";
        VERY_HIGHIcon.iconSize = new GSize(10,10);
        VERY_HIGHIcon.iconAnchor = new GPoint(5,5);
        
        var HUGEIcon = new GIcon();
        HUGEIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/HUGE.gif";
        HUGEIcon.iconSize = new GSize(10,10);
        HUGEIcon.iconAnchor = new GPoint(5,5);

        var OLD_DATAIcon = new GIcon();
        OLD_DATAIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/OLD_DATA.gif";
        OLD_DATAIcon.iconSize = new GSize(10,10);
        OLD_DATAIcon.iconAnchor = new GPoint(5,5);

        var NO_GUAGE_DATAIcon = new GIcon();
        NO_GUAGE_DATAIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/NO_GUAGE_DATA.gif";
        NO_GUAGE_DATAIcon.iconSize = new GSize(10,10);
        NO_GUAGE_DATAIcon.iconAnchor = new GPoint(5,5);

        var CONVERSION_UNKNOWNIcon = new GIcon();
        CONVERSION_UNKNOWNIcon.image = "http://canoescotland.org/sites/all/themes/basestation_open/img/CONVERSION_UNKNOWN.gif";
        CONVERSION_UNKNOWNIcon.iconSize = new GSize(10,10);
        CONVERSION_UNKNOWNIcon.iconAnchor = new GPoint(5,5);
                
    <?php  
    //$riverSections->outputJavascript();
    ?>


        } // if (GBrowserIsCompatible()) 
      
    } // function createMap()
</script>

<?php
footer();
