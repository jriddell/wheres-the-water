<?php
require_once 'wheres-the-water/common.php';
require_once 'wheres-the-water/config.php';
require_once 'wheres-the-water/lib/RiverSections.php';
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
    
</style>
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
                <p><img title='Open maps link' src='/wheres-the-water/pics/osm.png' /> Map link</p>
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
    	
    	
        
        <div class='js-tab map-tab'>
        	<div id="map" style="height: 700px; width: 100%; "></div>
        	
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
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyzZ6XajHorMkz31HaS1dHRZW8Wdhlfm8&callback=initMap"></script>


  <script>
// -------------------- Map creation ---------------------------------------
  function initMap() {
		
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 7,
			center: {lat: 57.172, lng:  -4.6582}
		});

		var iconBase = '/wheres-the-water/pics/';
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
            var sectionLinks = jQuery(this).find('.link').html();

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
						'<td style="background-color: ' + boxColors[0] + '">> ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #FF6060">Very High</td>' +
						'<td style="background-color: ' + boxColors[1] + '">' + veryHighValue + ' - ' + hugeValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #FFC004">High</td>' +
						'<td style="background-color: ' + boxColors[2] + '">' + highValue + ' - ' + veryHighValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #FFFF33">Medium</td>' +
						'<td style="background-color: ' + boxColors[3] + '">' + mediumValue + ' - ' + highValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #00FF00">Low</td>' +
						'<td style="background-color: ' + boxColors[4] + '">' + lowValue + ' - ' + mediumValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #CCFFCC">Scrapeable</td>' +
						'<td style="background-color: ' + boxColors[5] + '">' + scrapeValue + ' - ' + lowValue + '</td>' +
					'</tr>' +
					'<tr>' +
						'<td style="background-color: #CCCCCC">Empty</td>' +
						'<td style="background-color: ' + boxColors[6] + '">< ' + scrapeValue + '</td>' +
					'</tr>' +
				'</tbody>' +
			'</table>';
			var riverFilename = riverSection.toLowerCase();
			riverFilename = riverFilename.replace(/ /g, '-');
			riverFilename = riverFilename.replace(/\(/g, '');
			riverFilename = riverFilename.replace(/\)/g, '');
            var contentString = "<div><p><b>" + riverSection + "</b></p><p>Level: " + currentReading + " (" + waterLevelValue + 
            ") <img src='" + iconBase + waterLevelValue + ext + "' /></p><p>Trend: " + trend + "</p><p>Last reading: " + currentReadingTime + 
            "</p><p>" + sectionLinks + "</p>" +
            "<p><span class='js-calib-table'>Calibrations</span> / <span class='js-chart link' style='text-decoration: underline; color: blue; cursor: pointer'>Level chart</span></p>" + riverReadings + "<p class='js-chart-content' style='display: none'>" +
            "<a href='/wheres-the-water/charts/"+riverFilename+"-weekly.png'>"+
            "<img src='/wheres-the-water/charts/"+riverFilename+"-weekly.png' style='max-width: 250px; width: 100%' /></a></p>" +
            "</div>";
			
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
			google.maps.event.addListener(map, "click", function(event) {
			    infowindow.close();
			});

			
		});
		
		

	}
  jQuery(document).ready( function(){
      jQuery('#map').on('click', '.js-calib-table', function(){
    		if (!jQuery(this).hasClass('js-link')){
    			jQuery('.js-chart-content').hide();
    			jQuery('.js-calib-table-content').show();
    			jQuery(this).attr('style', '');
    			jQuery('.js-chart').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
    		}
    	});
    	jQuery('#map').on('click', '.js-chart', function(){
    		if (!jQuery(this).hasClass('js-link')){
    			jQuery('.js-calib-table-content').hide();
    			jQuery('.js-chart-content').show();
    			jQuery(this).attr('style', '');
    			jQuery('.js-calib-table').attr('style', 'text-decoration: underline; color: blue; cursor: pointer');
    		}
    	});
  });
</script>