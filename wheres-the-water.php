<?php
// Debugging
function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);
        
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}
require_once 'common.php';
require_once 'config.php';
heading();
require_once 'lib/RiverSections.php';
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
.clearfix {
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
}

#river-readings {
    display: none;
}

.clickable {
    cursor: pointer;
}
</style>
<div class='clearfix' style='width: 100%'>
    
     

    <div>
        <p>Data Last Polled</p>
        <p><?php print $riverSections->downloadTime() ?></p>
        <p>Most Recent SEPA Reading</p>
        <p><?php print $riverSections->calculateMostRecentReading() ?></p>
            			
    	<a class='js-tab-top active' id='map-tab' href=''>Map view</a><a class='js-tab-top' id='table-tab' href=''>Table view</a>
        
        <div class='js-tab map-tab'><div id="map" style="height: 500px; width: 100%; "></div></div>
        
        <div id="river-table-div" class='js-tab table-tab' style="display: none">
        	<p>Symbol key:</p>
        	<p><img src='' /> SEPA gauge graph</p>
        	<p><img src='' /> SEPA gauge graph (mobile friendly)</p>
        	<p><img src='/wheres-the-water/pics/osm.png' /> Map link</p>
        	<p><img src='/wheres-the-water/pics/ukrgb.ico' /> UK Rivers Guide Book link</p>
        	<p><img src='/wheres-the-water/pics/sca.png' /> SCA guide book reference number</p>
        	<p><img src='/wheres-the-water/pics/warning.png' /> Access issue link</p>
        	
        	<table id="river-table">
        		<tr>
        			<th class='clickable' id='js-river-name'>River Section</th>
        			<th class='clickable' id='js-river-grade'>Grade</th>
        			<th class='clickable' id='js-river-level'>Level</th>
        			<th>Trend</th>
        			<th>Link</th>
        		</tr>
        		<?php $riverSections->printTable();?>
        	</table>
        </div>
    </div>
    
</div>
<script>
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
jQuery(document).ready( function(){
	// Table ordering
	// Initial order, alphabetical by river name
	sortTable("river-table", "riverSectionRow", 0, true);

	jQuery('#js-river-name').on('click', function(){
		sortTable("river-table", "riverSectionRow", 0, true);
	});
	jQuery('#js-river-grade').on('click', function(){
		if (jQuery(this).hasClass('sort-asc')){
			sortTable("river-table", "riverSectionRow", 1, false);
			jQuery(this).removeClass('sort-asc');
		}
		else {
			sortTable("river-table", "riverSectionRow", 1, true);
			jQuery(this).addClass('sort-asc')
		}
	});
	jQuery('#js-river-level').on('click', function(){
		sortTable("river-table", "riverSectionRow", 4, false);
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
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAr0GC5SjROQdQKwS78LI-abrgyULq-9g&callback=initMap"></script>


  <script>

  function initMap() {
		
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 7,
			center: {lat: 57.172, lng:  -4.6582}
		});

		var iconBase = 'pics/';
		var ext = '.png';
		var icons = {
			EMPTY: {
				icon: iconBase + 'EMPTY' + ext,
			},
			SCRAPE: {
				icon: iconBase + 'SCRAPE' + ext
			},
			LOW: {
				icon: iconBase + 'LOW' + ext
			},
			MEDIUM: {
				icon: iconBase + 'MEDIUM' + ext
			},
			HIGH: {
				icon: iconBase + 'HIGH' + ext
			},
			VERY_HIGH: {
				icon: iconBase + 'VERY_HIGH' + ext
			},
			HUGE: {
				icon: iconBase + 'HUGE' + ext
			},
			OLD_DATA: {
				icon: iconBase + 'OLD_DATA' + ext
			},
			NO_GUAGE_DATA: {
				icon: iconBase + 'NO_GUAGE_DATA' + ext
			},
			CONVERSION_UNKNOWN: {
				icon: iconBase + 'CONVERSION_UNKNOWN' + ext
			},
			NEEDS_CALIBRATIONS: {
				icon: iconBase + 'NEEDS_CALIBRATIONS' + ext
			}
		};
		var infowindow = new google.maps.InfoWindow();
		
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

            var boxColors = ['#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2', '#f2f2f2'];

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

            var riverReadings = '<table style="background-color: #424242">' +
				'<tbody>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #FF0000">Huge</td>' +
						'<td class="callibVals" id="huge" style="background-color: ' + boxColors[0] + '">> ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #FF6060">Very High</td>' +
						'<td class="callibVals" id="veryHigh" style="background-color: ' + boxColors[1] + '">' + veryHighValue + ' - ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #FFC004">High</td>' +
						'<td class="callibVals" id="high" style="background-color: ' + boxColors[2] + '">' + highValue + ' - ' + veryHighValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #FFFF33">Medium</td>' +
						'<td class="callibVals" id="medium" style="background-color: ' + boxColors[3] + '">' + mediumValue + ' - ' + highValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #00FF00">Low</td>' +
						'<td class="callibVals" id="low" style="background-color: ' + boxColors[4] + '">' + lowValue + ' - ' + mediumValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #CCFFCC">Scrapeable</td>' +
						'<td class="callibVals" id="justRunnable" style="background-color: ' + boxColors[5] + '">' + scrapeValue + ' - ' + lowValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="background-color: #CCCCCC">Empty</td>' +
						'<td class="callibVals" id="empty" style="background-color: ' + boxColors[6] + '">< ' + scrapeValue + '</td>' +
					'</tr>' +
				'</tbody>' +
			'</table>';
            var contentString = "<div><p><b>" + riverSection + "</b></p><p>Level: " + currentReading + " (" + waterLevelValue + 
            ") <img src='" + iconBase + waterLevelValue + ext + "' /></p><p>Trend: " + trend + "</p><p>Last reading: " + currentReadingTime + 
            "</p><p><a  target='_blank' rel='noopener' href='http://apps.sepa.org.uk/waterlevels/default.aspx?sd=t&lc=" + gaugeLocationCode + 
            "'>Go to the SEPA gauge graph</a></p><p><a target='_blank' rel='noopener' href='http://riverlevels.mobi/SiteDetails/Index/" + gaugeLocationCode +
            "'>Mobile friendly SEPA gauge graph</a></p>" + riverReadings + "</div>";
			
			if (jQuery(this).is('.riverSectionRow:last')){
				// If this is the last marker we need to know so we can add them to the map
				last = true;
			}
			
			position = new google.maps.LatLng(latitude, longitude);
			        	
			        	
			var marker = new google.maps.Marker({
			      position: position,
			      map: map,
			      icon: icons[waterLevelValue].icon,
			      title: riverSection
			});

			        	
			marker.addListener('click', function(){
				infowindow.setContent(contentString);
				infowindow.open(map, marker);
				
			});

			
		});
		
		

	}
</script>
<?php
footer();
