<div id="map" style="width: 100%; height: 500px; "></div>

<script language="javascript" src="http://maps.google.com/maps?file=api&amp;v=3&amp;sensor=false&amp;key=AIzaSyD-WF6gFouMUCMfdvzw2ajMeOrE-F6RlRY" ></script>


  <script>
  
if(document.getElementById && document.createTextNode) {
	window.onload=function(){
		createMap();
	}
}

    function createMap() {
	    if (GBrowserIsCompatible()) 
	    {
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
	EMPTYIcon.image = "/sites/all/themes/basestation_open/img/EMPTY.gif";
	EMPTYIcon.iconSize = new GSize(10,10);
	EMPTYIcon.iconAnchor = new GPoint(5,5);

	var SCRAPEIcon = new GIcon();
	SCRAPEIcon.image = "/sites/all/themes/basestation_open/img/SCRAPE.gif";
	SCRAPEIcon.iconSize = new GSize(10,10);
	SCRAPEIcon.iconAnchor = new GPoint(5,5);

	var LOWIcon = new GIcon();
	LOWIcon.image = "/sites/all/themes/basestation_open/img/LOW.gif";
	LOWIcon.iconSize = new GSize(10,10);
	LOWIcon.iconAnchor = new GPoint(5,5);	

	var MEDIUMIcon = new GIcon();
	MEDIUMIcon.image = "/sites/all/themes/basestation_open/img/MEDIUM.gif";
	MEDIUMIcon.iconSize = new GSize(10,10);
	MEDIUMIcon.iconAnchor = new GPoint(5,5);
	
	var HIGHIcon = new GIcon();
	HIGHIcon.image = "/sites/all/themes/basestation_open/img/HIGH.gif";
	HIGHIcon.iconSize = new GSize(10,10);
	HIGHIcon.iconAnchor = new GPoint(5,5);

	var VERY_HIGHIcon = new GIcon();
	VERY_HIGHIcon.image = "/sites/all/themes/basestation_open/img/VERY_HIGH.gif";
	VERY_HIGHIcon.iconSize = new GSize(10,10);
	VERY_HIGHIcon.iconAnchor = new GPoint(5,5);
	
	var HUGEIcon = new GIcon();
	HUGEIcon.image = "/sites/all/themes/basestation_open/img/HUGE.gif";
	HUGEIcon.iconSize = new GSize(10,10);
	HUGEIcon.iconAnchor = new GPoint(5,5);

	var OLD_DATAIcon = new GIcon();
	OLD_DATAIcon.image = "/sites/all/themes/basestation_open/img/OLD_DATA.gif";
	OLD_DATAIcon.iconSize = new GSize(10,10);
	OLD_DATAIcon.iconAnchor = new GPoint(5,5);

	var NO_GUAGE_DATAIcon = new GIcon();
	NO_GUAGE_DATAIcon.image = "/sites/all/themes/basestation_open/img/NO_GUAGE_DATA.gif";
	NO_GUAGE_DATAIcon.iconSize = new GSize(10,10);
	NO_GUAGE_DATAIcon.iconAnchor = new GPoint(5,5);

	var CONVERSION_UNKNOWNIcon = new GIcon();
	CONVERSION_UNKNOWNIcon.image = "/sites/all/themes/basestation_open/img/CONVERSION_UNKNOWN.gif";
	CONVERSION_UNKNOWNIcon.iconSize = new GSize(10,10);
	CONVERSION_UNKNOWNIcon.iconAnchor = new GPoint(5,5);
			
	<?php  
	// Retrieve all resorts for this region from the db
	$result = db_query( "SELECT * FROM {node_revisions} join {content_type_river_section} ON {node_revisions}.vid = {content_type_river_section}.vid");

	// Display the rollover icon for each resort
	while ( $row = $result->fetch() )   
	{ 
	?>
		var point<?php print $row->vid ?> = new GLatLng(<?php print $row->field_latitude_value ?>,<?php print $row->field_longitude_value ?>);
        markerOptions = { icon:<?php print $row->field_current_level_value ?>Icon };
        var marker<?php print $row->vid ?> = new GMarker(point<?php print $row->vid ?>, markerOptions);
        GEvent.addListener(marker<?php print $row->vid ?>, "mouseover", function() {
        	showSectionInfo("<?php print $row->title ?>", "<?php print $row->field_current_level_value ?>", "<?php print $row->field_last_reading_date_value ?>", "<?php print $row->field_current_guage_reading_value ?>", "<?php print $row->field_trend_value ?>" );
        	showConversionInfo("<?php print $row->field_current_level_value ?>", "<?php print $row->field_scrape_value ?>","<?php print $row->field_low_value ?>", "<?php print $row->field_medium_value ?>", "<?php print $row->field_high_value ?>", "<?php print $row->field_very_high_value ?>", "<?php print $row->field_huge_value ?>");
		});
	GEvent.addListener(marker<?php print $row->vid ?>, "click", function() {  showPicWin('http://apps.sepa.org.uk/waterlevels/default.aspx?sd=t&lc=<?php print $row->field_guageid_0_value ?>') });
	

        map.addOverlay(marker<?php print $row->vid ?>);
	<?php  }?>

      }    }
</script>
