<?php

require_once '../../wheres-the-water/common.php';
require_once '../../wheres-the-water/config.php';
heading();
require_once '../../wheres-the-water/lib/RiverSections.php';
$riverSections = new RiverSections;
$riverSections->readFromJson();

?>
<style>
.js-tab-top {
    padding: 1em;
    border-radius: 10px 10px 0 0;
    display: inline-block;
    background-color: #e6f2ff;
}

.active, .js-tab {
    background-color: #f2f2f2;
}

.js-tab {
    padding: 1em;
}
.clearfix::after {
    content: "";
    display: table;
    clear: both;
}
#river-table {
    border-collapse: collapse;
    width: 100%;
    background-color: #ffffff;
}

#river-table td, #river-table th {
    padding: 0.5em;
    border-bottom: 1px solid #595959;
    text-align: left;
}

#river-readings {
    display: none;
}

.clickable, .js-link {
    cursor: pointer;
}

.js-link {
    text-decoration: underline;
    color: blue;
}

.js-calib-table-content {
    border-collapse: separate;
    border-spacing: 1px;
}
#map { 
    height: 700px; 
    border: thin solid grey; 
}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
    integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
    crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
    integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
    crossorigin=""></script>   

<div class='clearfix' style='width: 100%'>

    <div>
    	<div class="clearfix">
            <div style="float: left; margin-right: 1em">
                <p><b>Data Last Polled</b></p>
                <p><?php print $riverSections->downloadTime() ?></p>
                <p><b>Most Recent SEPA Reading</b></p>
                <p><?php print $riverSections->calculateMostRecentReading() ?></p>
                
            </div>
            <div style="float: left">
                <p><b>Symbols Key</b></p>
                <p><img title='SEPA gauge link' src='/wheres-the-water/pics/graph-icon.png' /> SEPA gauge graph</p>
                <p><img title='SEPA gauge link - mobile friendly' src='/wheres-the-water/pics/phone-icon.png' /> SEPA gauge graph (mobile friendly)</p>
                <p><img title='OpenStreetMap link' src='/wheres-the-water/pics/osm.png' /> Map link</p>
                <p><img title='Geo reference' src='/wheres-the-water/pics/22-apps-marble.png' /> Map link for mobile phones</p>
                <p><img title='UKRGB link' src='/wheres-the-water/pics/ukrgb.ico' /> UK Rivers Guide Book link</p>
            </div>
            <div style="margin-left: 1em; float: left">
                <p><img title='SCA guide book reference number' src='/wheres-the-water/pics/sca.png' /> SCA guide book reference number</p>
                <p><img title='Access issue link' src='/wheres-the-water/pics/warning.png' /> Access issue link</p>
                <p><img title='Weekly Chart' src='/wheres-the-water/pics/chart.png' /> Weekly River Level Chart</p>
                <p><img title='Monthly Chart' src='/wheres-the-water/pics/chart-monthly.png' /> Monthly River Level Chart</p>
                <p><img title='Yearly Chart' src='/wheres-the-water/pics/chart-yearly.png' /> Yearly River Level Chart</p>
            </div>
        </div>
            			
    	<a class='js-tab-top active' id='map-tab' href=''>Map view</a><a class='js-tab-top' id='table-tab' href=''>List view</a>
        <input type="button" value="Show River Names" onclick="showTooltips()" id="showRiverNames" />
        
        <div class='js-tab map-tab'>
            <div id="map"></div>
        </div>
        
        <div id="river-table-div" class='js-tab table-tab' style="display: none">
        	
        	
        	<p>Search by river name: <input type="text" name="table-search" id="table-search"/></p>
        	<p>Click on River Section, Grade or Level to sort the table</p>
        	<p>Click on the Level to see the gauge reading</p>
        	
        	<table id="river-table">
        		<tr>
        			<th class='clickable sort-asc' id='js-river-name'>River Section <span class='order-arrow'>&#x25BC;</span></th>
        			<th class='clickable' id='js-river-grade'>Grade <span class='order-arrow'></span></th>
        			<th class='clickable' id='js-river-level'>Level <span class='order-arrow'></span></th>
        			<th>Trend</th>
        			<th>Link</th>
        		</tr>
        		<?php $riverSections->printTable();?>
        	</table>
        </div>
    </div>
    
</div>
<script>
// ---------------------- Tab change -------------------------
jQuery(document).ready( function(){
	jQuery('.js-tab-top').on('click', function(e){
		e.preventDefault();
		
		//Check if this is the active tab
		if (!jQuery(this).hasClass('active')){
			var id = jQuery(this).attr('id');
		 	var tab = jQuery('.' + id);
			jQuery('.js-tab').hide();
			tab.show();
		
			jQuery('.js-tab-top').removeClass('active');
			
			jQuery(this).addClass('active');
			
			
			
		}
		
	});
});
</script>
<script>
// ---------------- Shows the level value in m ----------------

jQuery(document).ready( function(){
	jQuery('.waterLevelValueRead').on('click', function(){
		jQuery(this).hide();
		jQuery(this).siblings('.currentReading').show();
	});
	jQuery('.currentReading').on('click', function(){
		jQuery(this).hide();
		jQuery(this).siblings('.waterLevelValueRead').show();
	});
	
});
</script>

<script>
// ----------------- Table sorting -----------------------------
jQuery(document).ready( function(){

	var downArrow = "&#x25BC;";
	var upArrow = "&#x25B2;";
	// Initial order, alphabetical by river name
	sortTable("river-table", "riverSectionRow", 0, true);

	jQuery('#js-river-name').on('click', function(){
		jQuery('.order-arrow').html('');
		if (jQuery(this).hasClass('sort-asc')){
			sortTable("river-table", "riverSectionRow", 0, false);
			jQuery(this).removeClass('sort-asc');
    		jQuery(this).find('.order-arrow').html(upArrow);
		}
		else {
    		sortTable("river-table", "riverSectionRow", 0, true);
    		jQuery(this).find('.order-arrow').html(downArrow);
    		jQuery(this).addClass('sort-asc');
		}
	});
	jQuery('#js-river-grade').on('click', function(){
		jQuery('.order-arrow').html('');
		if (jQuery(this).hasClass('sort-asc')){
			sortTable("river-table", "riverSectionRow", 1, false);
			jQuery(this).removeClass('sort-asc');
			jQuery(this).find('.order-arrow').html(upArrow);
		}
		else {
			sortTable("river-table", "riverSectionRow", 1, true);
			jQuery(this).addClass('sort-asc');
			jQuery(this).find('.order-arrow').html(downArrow);
		}
	});
	jQuery('#js-river-level').on('click', function(){
		jQuery('.order-arrow').html('');
		if (jQuery(this).hasClass('sort-asc')){
			sortTable("river-table", "riverSectionRow", 4, false);
			jQuery(this).removeClass('sort-asc');
			jQuery(this).find('.order-arrow').html(upArrow);
			
		}
		else {
			sortTable("river-table", "riverSectionRow", 4, true);
			jQuery(this).addClass('sort-asc');
			jQuery(this).find('.order-arrow').html(downArrow);
		}
	});
});

sortTable = function(tableName, rowClass, columnNumber, ascending) {
    var row, cell, cellContent;
    var comparisonRow, comparisonCell, comparisonContent;

    $("#" + tableName + " tr." + rowClass).each(function(i) {
        row = $("#" + tableName + " tr." + rowClass + ":eq(" + i + ")");
        cell = $(row).find("td:eq(" + columnNumber + ")");
        cellContent = $(cell).html();

        $("#" + tableName + " tr." + rowClass).each(function(j) {
            comparisonRow = $("#" + tableName + " tr." + rowClass + ":eq(" + j + ")");
            comparisonCell = $(comparisonRow).find("td:eq(" + columnNumber + ")");
            comparisonContent = $(comparisonCell).html();

            if ( (ascending && cellContent < comparisonContent) || (!ascending && cellContent > comparisonContent) ) {
                $(row).insertBefore(comparisonRow);
                return false;
            }
        });
    });
};

</script>
<script>
// ------------------ Table search ---------------------------------------------
jQuery(document).ready( function(){
	jQuery('#table-search').on('keyup', function(){
		var searchString = jQuery(this).val().toLowerCase();
		if (searchString != ''){
			jQuery('.riverSectionRow').each( function(){
				if (jQuery(this).find('.riverSection').text().toLowerCase().indexOf(searchString) >= 0){
					jQuery(this).show();
				}
				else {
					jQuery(this).hide();
				}
				if (searchString == 'nesk' && jQuery(this).find('.riverSection').text().toLowerCase().indexOf('north esk') >= 0){
					jQuery(this).show();
				}
				if (searchString == 'the best river' && jQuery(this).find('.riverSection').text().toLowerCase().indexOf('tilt') >= 0){
					jQuery(this).show();
				}
			});
				
		}
		else {
			jQuery('.riverSectionRow').show();
		}
	});
});
</script>

    <script type="text/javascript">
        $ = jQuery; // for some reason SCA website uses JQuery but does not set $
        map = L.map( 'map', {
            center: [57.172, -4.6582],
            minZoom: 2,
            zoom: 7
        });
        
        var osmLayer = L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a> | <a href="https://github.com/jriddell/wheres-the-water">River Data</a> by SCA'
        }).addTo( map );
        
        /* Old and ugly but something to aim for in the future
        var wwLayer = L.tileLayer( 'http://whitewater.quaker.eu.org/tiles.php/contours/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo( map );
        */
        
        var baseMaps = {
            // "Open WhiteWater Map": wwLayer,
            "OpenStreetMap": osmLayer
        };
        L.control.layers(baseMaps).addTo(map);

        var emptyIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/EMPTY.png',
            iconRetinaUrl: '/wheres-the-water/pics/EMPTY.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var scrapeIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/SCRAPE.png',
            iconRetinaUrl: '/wheres-the-water/pics/SCRAPE.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var lowIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/LOW.png',
            iconRetinaUrl: '/wheres-the-water/pics/LOW.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var mediumIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/MEDIUM.png',
            iconRetinaUrl: '/wheres-the-water/pics/MEDIUM.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var highIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/HIGH.png',
            iconRetinaUrl: '/wheres-the-water/pics/HIGH.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var veryHighIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/VERY_HIGH.png',
            iconRetinaUrl: '/wheres-the-water/pics/VERY_HIGH.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var hugeIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/HUGE.png',
            iconRetinaUrl: '/wheres-the-water/pics/HUGE.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var oldDataIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/OLD_DATA.png',
            iconRetinaUrl: '/wheres-the-water/pics/OLD_DATA.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var noDataIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/NO_GUAGE_DATA.png',
            iconRetinaUrl: '/wheres-the-water/pics/NO_GUAGE_DATA.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        var needsCalibrationsIcon = L.icon({
            iconUrl: '/wheres-the-water/pics/NEEDS_CALIBRATIONS.png',
            iconRetinaUrl: '/wheres-the-water/pics/NEEDS_CALIBRATIONS.png',
            iconSize: [10, 10],
            iconAnchor: [0, 0],
            popupAnchor: [5, -3]
        });
        
        var riverSections;
        var riverSectionsFile = $.getJSON("/wheres-the-water/data/river-sections.json", function(data) {
                riverSections = data;
            }
        );
        var riverReadings;
        var riverReadingsFile = $.getJSON("/wheres-the-water/data/rivers-readings.json", function(data) {
                riverReadings = data;
            }
        );
        var bothFiles = $.when(riverSectionsFile, riverReadingsFile);

        bothFiles.done(function () {
            mergeRiverData();
            addRiverMarkers();
        });
        
        function mergeRiverData() {
            for (i=0; i<riverSections.length; i++) {
                try {
                    var gauge_location_code = riverSections[i]['gauge_location_code']
                    riverSections[i]['currentReading'] = riverReadings[gauge_location_code]['currentReading'];
                    riverSections[i]['trend'] = riverReadings[gauge_location_code]['trend'];
                    riverSections[i]['currentReadingTime'] = riverReadings[gauge_location_code]['currentReadingTime'];
                } catch(error) {
                    console.log('Error on merging ' + riverSections[i]['name'] + ' ' + error.message);
                }
            }
        }
        // make a string lower case starting with capital and replace _
        function tidyStatusString(string) {
            if (string) {
                string = string.toLocaleLowerCase();
                string = string.charAt(0).toUpperCase() + string.slice(1);
                return string.replace('_', ' ');
            } else {
                return string;
            }
        }
        function linksContent(riverSection) {
            var linksContent = "<a target='_blank' rel='noopener' href='http://apps.sepa.org.uk/waterlevels/default.aspx?sd=t&lc="+riverSection['gauge_location_code']+"'><img title='SEPA gauge link' src='/wheres-the-water/pics/graph-icon.png'/></a>";
            linksContent += "&nbsp; <a target='_blank' rel='noopener' href='http://riverlevels.mobi/SiteDetails/Index/"+riverSection['gauge_location_code']+"'><img  title='SEPA gauge link - mobile friendly' src='/wheres-the-water/pics/phone-icon.png'/></a>";
            linksContent += "&nbsp; <a target='_blank' rel='noopener' href='https://www.openstreetmap.org/?mlat="+riverSection['latitude']+"&mlon="+riverSection['longitude']+"#map=12/"
                            +riverSection['latitude']+"/"+riverSection['longitude']+"'><img title='Open maps Link' src='/wheres-the-water/pics/osm.png' width='22' height='22' /></a>";
            linksContent += "&nbsp; <a href='geo:"+riverSection['latitude']+","+riverSection['longitude']+"'><img title='Geo reference' src='/wheres-the-water/pics/22-apps-marble.png' width='22' height='22' /></a>";

            if ('guidebook_link' in riverSection && !riverSection['guidebook_link'].length == 0) {
                linksContent += "&nbsp; <a target='_blank' rel='noopener' href='"+riverSection['guidebook_link']+"'><img title='UKRGB Link' src='/wheres-the-water/pics/ukrgb.ico'/></a>";
            }
            if ('sca_guidebook_no' in riverSection && !riverSection['sca_guidebook_no'].length == 0) {
                linksContent += "&nbsp; <img title='SCA WW Guidebook number' src='/wheres-the-water/pics/sca.png' /> "+riverSection['sca_guidebook_no'];
            }
            if ('access_issue' in riverSection && !riverSection['access_issue'].length == 0) {
                linksContent += "&nbsp; <a target='_blank' rel='noopener' href='"+riverSection['access_issue']+"'><img title='Access Issue Link' src='/wheres-the-water/pics/warning.png' /></a>";
            }

            var filename = getRiverGraphFilename(riverSection);
            linksContent += "&nbsp; <a target='_blank' rel='noopener' href='/wheres-the-water/charts/"+filename+"-weekly.png'><img title='Weekly Chart' src='/wheres-the-water/pics/chart.png' /></a>";
            linksContent += "&nbsp; <a target='_blank' rel='noopener' href='/wheres-the-water/charts/"+filename+"-monthly.png'><img title='Monthly Chart' src='/wheres-the-water/pics/chart-monthly.png' /></a>";
            linksContent += "&nbsp; <a target='_blank' rel='noopener' href='/wheres-the-water/charts/"+filename+"-yearly.png'><img title='Yearly Chart' src='/wheres-the-water/pics/chart-yearly.png' /></a>";

            return linksContent;
        }
        function addRiverMarkers() {
            markers = new Array();
            tooltipsAreVisible = false;
            for (i=0; i<riverSections.length; i++) {
                var riverSection = riverSections[i]['name'];
                var currentReading = riverSections[i]['currentReading'];
                var waterLevelValue = getWaterLevelValue(riverSections[i]);
                var iconBase = '/wheres-the-water/pics/';
                var ext = '.png';
                var trend = riverSections[i]['trend'];
                var currentReadingTime = riverSections[i]['currentReadingTime'];
                var sectionLinks = linksContent(riverSections[i]);
                var riverReadings = getRiverReadingsTable(riverSections[i], waterLevelValue);
                var riverFilename = getRiverGraphFilename(riverSections[i]);
                var icon = getWaterLevelIcon(riverSections[i]);
                var contentString = "<div><p><h4>" + riverSection + "</h4></p><p><b>Level</b>: " + currentReading + " (" +
                    tidyStatusString(waterLevelValue) + 
                    ") <img src='" + iconBase + waterLevelValue + ext + "' /></p><p><b>Trend</b>: " +
                    tidyStatusString(trend) + "</p><p><b>Last reading</b>: " + currentReadingTime +
                    "</p><p>" + sectionLinks + "</p>" +
                    "<p><span class='js-calib-table'>Calibrations</span> / <span class='js-chart-weekly link' style='text-decoration: underline; color: blue; cursor: pointer'>Weekly Chart</span> / <span class='js-chart-monthly link' style='text-decoration: underline; color: blue; cursor: pointer'>Monthly Chart</span> / <span class='js-chart-yearly link' style='text-decoration: underline; color: blue; cursor: pointer'>Yearly Chart</span></p>" +
                    riverReadings + 
                    "<p class='js-chart-weekly-content' style='display: none'>" +
                    "<a href='/wheres-the-water/charts/"+riverFilename+"-weekly.png'>"+
                    "<img src='/wheres-the-water/charts/"+riverFilename+"-weekly.png' style='max-width: 250px; width: 100%' /></a></p>" +
                    "<p class='js-chart-monthly-content' style='display: none'>" +
                    "<a href='/wheres-the-water/charts/"+riverFilename+"-monthly.png'>"+
                    "<img src='/wheres-the-water/charts/"+riverFilename+"-monthly.png' style='max-width: 250px; width: 100%' /></a></p>" +
                    "<a href='/wheres-the-water/charts/"+riverFilename+"-yearly.png'>"+
                    "<img src='/wheres-the-water/charts/"+riverFilename+"-yearly.png' style='max-width: 250px; width: 100%' /></a></p>" +
                    "</div>";
                var marker = L.marker([riverSections[i]['latitude'], riverSections[i]['longitude']], {icon: icon}).bindPopup(contentString).addTo( map );
                marker.bindTooltip(riverSection);
                markers.push(marker);
            }
        }
        function getWaterLevelValue(riverSection) {
            currentReading = riverSection['currentReading'];
            if (currentReading == -1) {
                return "NO_GUAGE_DATA";
            } else if (currentReading == 0) {
                return "OLD_DATA";
            } else if (riverSection['scrape_value'] == riverSection['huge_value']) {
                return "NEEDS_CALIBRATIONS";
            } else if (currentReading < riverSection['scrape_value']) {
                return "EMPTY";
            } else if (currentReading < riverSection['low_value']) {
                return "SCRAPE";
            } else if (currentReading < riverSection['medium_value']) {
                return "LOW";
            } else if (currentReading < riverSection['high_value']) {
                return "MEDIUM";
            } else if (currentReading < riverSection['very_high_value']) {
                return "HIGH";
            } else if (currentReading < riverSection['huge_value']) {
                return "VERY_HIGH";
            } else {
                return "HUGE";
            }
        }
        function getRiverReadingsTable(riverSection, waterLevelValue) {

            var boxColors = ['#e6e6e6', '#e6e6e6', '#e6e6e6', '#e6e6e6', '#e6e6e6', '#e6e6e6', '#e6e6e6'];
            switch(waterLevelValue) {
                case 'HUGE':
                    boxColors[0] = '#ffffff';
                    break;
                case 'VERY_HIGH':
                    boxColors[1] = '#ffffff';
                    break;
                case 'HIGH':
                    boxColors[2] = '#ffffff';
                    break;
                case 'MEDIUM':
                    boxColors[3] = '#ffffff';
                    break;
                case 'LOW':
                    boxColors[4] = '#ffffff';
                    break;
                case 'SCRAPE':
                    boxColors[5] = '#ffffff';
                    break;
                case 'EMPTY':
                    boxColors[6] = '#ffffff';
                    break;
            }
            var riverReadings = '<table class="js-calib-table-content" style="background-color: #424242">' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td style="background-color: #FF0000">Huge</td>' +
                                    '<td style="background-color: ' + boxColors[0] + '">> ' + riverSection['huge_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #FF6060">Very High</td>' +
                                    '<td style="background-color: ' + boxColors[1] + '">' + riverSection['very_high_value'] + ' - ' + riverSection['huge_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #FFC004">High</td>' +
                                    '<td style="background-color: ' + boxColors[2] + '">' + riverSection['high_value'] + ' - ' + riverSection['very_high_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #FFFF33">Medium</td>' +
                                    '<td style="background-color: ' + boxColors[3] + '">' + riverSection['medium_value'] + ' - ' + riverSection['high_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #00FF00">Low</td>' +
                                    '<td style="background-color: ' + boxColors[4] + '">' + riverSection['low_value'] + ' - ' + riverSection['medium_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #CCFFCC">Scrapeable</td>' +
                                    '<td style="background-color: ' + boxColors[5] + '">' + riverSection['scrape_value'] + ' - ' + riverSection['low_value'] + '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="background-color: #CCCCCC">Empty</td>' +
                                    '<td style="background-color: ' + boxColors[6] + '">< ' + riverSection['scrape_value'] + '</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>';
            return riverReadings
        }
        function getRiverGraphFilename(riverSection) {
            var riverFilename = riverSection['name'].toLowerCase();
            riverFilename = riverFilename.replace(/ /g, '-');
            riverFilename = riverFilename.replace(/\(/g, '');
            riverFilename = riverFilename.replace(/\)/g, '');
            return riverFilename
        }
        function getWaterLevelIcon(riverSection) {
            currentReading = riverSection['currentReading'];
            if (currentReading == -1) {
                return oldDataIcon;
            } else if (currentReading == 0) {
                return noDataIcon;
            } else if (riverSection['scrape_value'] == riverSection['huge_value']) {
                return needsCalibrationsIcon;
            } else if (currentReading < riverSection['scrape_value']) {
                return emptyIcon;
            } else if (currentReading < riverSection['low_value']) {
                return scrapeIcon;
            } else if (currentReading < riverSection['medium_value']) {
                return lowIcon;
            } else if (currentReading < riverSection['high_value']) {
                return mediumIcon;
            } else if (currentReading < riverSection['very_high_value']) {
                return highIcon;
            } else if (currentReading < riverSection['huge_value']) {
                return veryHighIcon;
            } else {
                return hugeIcon;
            }
        }
        function showTooltips() {
            if (tooltipsAreVisible) {
                for (i=0; i<markers.length; i++) {
                    markers[i].closeTooltip();
                }
                tooltipsAreVisible = false;
            } else {
                for (i=0; i<markers.length; i++) {
                    markers[i].openTooltip();
                }
                tooltipsAreVisible = true;
            }
        }

        $(document).ready( function(){
            $('#map').attr('style', 'position: relative; height: ' + $(window).height() + 'px');
            window.location.hash = '#map';
            $('#map').on('click', '.js-calib-table', function(){
                    if (!$(this).hasClass('js-link')){
                        $('.js-chart-weekly-content').hide();
                        $('.js-chart-monthly-content').hide();
                        $('.js-chart-yearly-content').hide();
                        $('.js-calib-table-content').show();
                        $(this).attr('style', '');
                        $('.js-chart-weekly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-monthly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-yearly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                    }
                }
            );
            $('#map').on('click', '.js-chart-weekly', function(){
                    if (!$(this).hasClass('js-link')){
                        $('.js-calib-table-content').hide();
                        $('.js-chart-weekly-content').show();
                        $('.js-chart-monthly-content').hide();
                        $('.js-chart-yearly-content').hide();
                        $(this).attr('style', '');
                        $('.js-calib-table').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-monthly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-yearly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                    }
                }
            );
            $('#map').on('click', '.js-chart-monthly', function(){
                    if (!$(this).hasClass('js-link')){
                        $('.js-calib-table-content').hide();
                        $('.js-chart-weekly-content').hide();
                        $('.js-chart-monthly-content').show();
                        $('.js-chart-yearly-content').hide();
                        $(this).attr('style', '');
                        $('.js-calib-table').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-weekly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-yearly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                    }
                }
            );
            $('#map').on('click', '.js-chart-yearly', function(){
                    if (!$(this).hasClass('js-link')){
                        $('.js-calib-table-content').hide();
                        $('.js-chart-weekly-content').hide();
                        $('.js-chart-monthly-content').hide();
                        $('.js-chart-yearly-content').show();
                        $(this).attr('style', '');
                        $('.js-calib-table').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-weekly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                        $('.js-chart-monthly').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
                    }
                }
            );
            map.invalidateSize();
        });
  </script>

<?php
footer();
