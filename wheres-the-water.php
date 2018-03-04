
<!DOCTYPE html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://canoescotland.org/sites/all/themes/basestation_open/js/riverLevels.js?p347k9"></script>
</head>

<body style="background: white">
<p><a href="http://www.andyjacksonfund.org.uk"><img src="andy-jackson-fund.png" width="560" height="86" /></a>
<a href="http://canoescotland.org"><img src="scottish-canoe-association-social.jpg" width="400" height="157" /></a></p>

<h1>SCA Where&#039;s The Water?</h1>

<h2>Scottish River Levels</h2>

<div style="float: right">

      <div class="content"> <table  cellspacing="0" class="riverlevels">
			<tr>
			  <td class="dataHeaders" colspan="2">Data Last Polled</td>
			</tr>
			<tr>
			  <td class="dataValues" style="width:190px" colspan="2">25th Feb at 10:01</td>
			</tr>
			<tr>
			  <td colspan="2" height="4"></td>
          		</tr>
			<tr>
			  <td class="dataHeaders" colspan="2">Most Recent Reading</td>
			  </tr>
			 <tr>
			  <td colspan="2" class="dataValues" style="width:190px" >North Esk (Upper) at 09:15 on 25th Feb was EMPTY			  </td>
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

<div id="map" style="height: 500px; "></div>

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
    require_once 'config.php';
    require_once 'lib/RiverSections.php';
    $riverSections = new RiverSections;
    $riverSections->outputJavascript();
    ?>


        } // if (GBrowserIsCompatible()) 
      
    } // function createMap()
</script>

<p>SCA Where's the Water uses <a href="
<a href="http://apps.sepa.org.uk/waterlevels/">water level data from SEPA</a>.</p>

<p>Code written and maintained by <a href="http://www.edinburghlinux.co.uk">Jonathan Riddell</a>, patched and bug reports welcome, <a href="https://github.com/jriddell/wheres-the-water">Where's the Water on GitHub</a>.</p>

</body>
</html>
