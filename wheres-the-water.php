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
}

#river-table td, #river-table th {
    padding: 0.5em;
    border-bottom: 1px solid #595959;
}

#river-readings {
    display: none;
}
</style>
<div class='clearfix' style='width: 100%'>
    
     
    <div>
    	<a class='js-tab-top active' id='map-tab' href=''>Map view</a><a class='js-tab-top' id='table-tab' href=''>Table view</a>
        <div class='js-tab map-tab'><div id="map" style="height: 500px; width: 100%; "></div></div>
        <div id="river-table-div" class='js-tab table-tab' style="display: none">
        	<table id="river-table">
        		<tr><th>River Section</th><th>Level</th><th>Trend</th><th>Link</th></tr>
        		<?php $riverSections->printTable();?>
        	</table>
        </div>
    </div>
    <div style="float: right">
    
          <div class="content">
              <table  cellspacing="0" class="riverlevels">
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

            var riverReadings = '<table style="backgroundcolor: #424242">' +
				'<tbody>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #FF0000">Huge</td>' +
						'<td class="callibVals" id="huge">> ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #FF6060">Very High</td>' +
						'<td class="callibVals" id="veryHigh">' + veryHighValue + ' - ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #FFC004">High</td>' +
						'<td class="callibVals" id="high">' + highValue + ' - ' + veryHighValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #FFFF33">Medium</td>' +
						'<td class="callibVals" id="medium">' + mediumValue + ' - ' + highValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #00FF00">Low</td>' +
						'<td class="callibVals" id="low">' + lowValue + ' - ' + mediumValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #CCFFCC">Scrapeable</td>' +
						'<td class="callibVals" id="justRunnable">' + scrapeValue + ' - ' + lowValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td class="callibHeaders" style="backgroundcolor: #CCCCCC">Empty</td>' +
						'<td class="callibVals" id="empty">< ' + scrapeValue + '</td>' +
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

<?php
footer();
