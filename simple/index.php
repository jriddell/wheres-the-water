<?php
require_once '../wheres-the-water/common.php';
require_once '../wheres-the-water/config.php';
heading();
?>
<style>
body {
    padding: 0px;
    margin: 0px;
}

table {
    border-spacing: 0px;
    border: 1px solid #ddd;
    width: 100%;
    max-width: 40em;
}

th {
    cursor: pointer;
}

th, td {
    text-align: left;
    padding: 16px;
}

tr:nth-child(even) {
    background-color: #f2f2f2
}

table a {
    text-decoration: none;
    color: black;
}
.hide {
    display: none;
}
.riverLinks a, .riverLinks {
    font-size: small;
    color: black;
}
.riverLinks {
    width: 15em;
}
.riverForecast {
    width: 10em;
    font-size: smaller;
    display: table-cell;
}
.desktop {
    display: none;
}
@media only screen and (min-width: 768px) {
  .desktop {
    display: inline;
  }
}
.mobile {
    display: none;
}
@media only screen and (max-width: 768px) {
  .mobile {
    display: inline;
  }
}

</style>
<!--
<link href="https://www.kde.org/css/ekko-lightbox.css" rel="stylesheet" type="text/css" />

<script src="https://www.kde.org/js/jquery-3.1.1.min.js" defer="true"></script>
<script src="https://www.kde.org/js/ekko-lightbox.min.js" defer="true"></script>
<script src="https://www.kde.org/js/use-ekko-lightbox.js" defer="true"></script>
-->

<?php
require_once('../wheres-the-water/lib/RiverSections.php');
$riverSections = new RiverSections();
$riverSections->readFromJson();
?>

<p><a href="/wtw/map">Simple Map View</a></p>

    	<div class="clearfix">
            <div style="float: left; margin-right: 1em">
                <p><b>Data Last Polled</b> <?php print $riverSections->downloadTime() ?></p>
                <p><b>Most Recent SEPA Reading</b> <?php print $riverSections->calculateMostRecentReading() ?></p>
            </div>
            <!--
            <div style="float: left">
                <p><b>Symbols Key</b></p>
                <p><img src='/wheres-the-water/pics/graph-icon.png' /> SEPA gauge graph</p>
                <p><img src='/wheres-the-water/pics/phone-icon.png' /> SEPA gauge graph (mobile friendly)</p>
                <p><img src='/wheres-the-water/pics/osm.png' /> Map link</p>
                <p><img src='/wheres-the-water/pics/22-apps-marble.png' /> Map link for mobile phones</p>
                <p><img src='/wheres-the-water/pics/ukrgb.ico' /> UK Rivers Guide Book link</p>
            </div>
            <div style="margin-left: 1em; float: left">
                <p><img src='/wheres-the-water/pics/sca.png' /> SCA guide book reference number</p>
                <p><img src='/wheres-the-water/pics/warning.png' /> Access issue link</p>
                <p><img title='Weekly Chart' src='/wheres-the-water/pics/chart.png' /> Weekly River Level Chart</p>
                <p><img title='Monthly Chart' src='/wheres-the-water/pics/chart-monthly.png' /> Monthly River Level Chart</p>
                <p><img title='Yearly Chart' src='/wheres-the-water/pics/chart-yearly.png' /> Yearly River Level Chart</p>
            </div>
            -->
        </div>
            			
<br clear="all" />        	
        	<p>Search by river name: <input type="text" name="table-search" id="table-search"/></p>
        	<p>Click on River Section, Grade or Level to sort the table</p>
        	
        <div id="river-table-div" class='js-tab table-tab'>
        	<table id="river-table">
        		<tr>
        			<th class='clickable sort-asc' id='js-river-name'>River Section <span class='order-arrow'>&#x25BC;</span></th>
        			<th class='clickable' id='js-river-grade'>Grade <span class='order-arrow'></span></th>
        			<th class='clickable' id='js-river-level'>Level <span class='order-arrow'></span></th>
        			<th>Trend</th>
        			<th>Link</th>
        			<th>Forecast</th>
        		</tr>
        		<?php $riverSections->printTable();?>
        	</table>
          </div>

<script>
// ---------------- Shows the level value in m ----------------
// ---------------- Updates the calibrations table when the row 
// ---------------- is clicked or mouseovered -----------------
jQuery(document).ready( function(){
	jQuery('.waterLevelValueRead').on('click', function(){
		jQuery(this).hide();
		jQuery(this).siblings('.currentReading').show();
	});
	jQuery('.currentReading').on('click', function(){
		jQuery(this).hide();
		jQuery(this).siblings('.waterLevelValueRead').show();
	});
	jQuery('.riverSectionRow').on('mouseover click', function(){
		var riverSection = jQuery(this).find('.riverSection').text();
		var waterLevelValue = jQuery(this).find('.waterLevelValue').text();
		var currentReadingTime = jQuery(this).find('.currentReadingTime').text();
		var currentReading = jQuery(this).find('.currentReading').text();
		var trend = jQuery(this).find('.trend').text();
		var scrapeValue = jQuery(this).find('.scrapeValue').text();
		var lowValue = jQuery(this).find('.lowValue').text();
		var mediumValue = jQuery(this).find('.mediumValue').text();
		var highValue = jQuery(this).find('.highValue').text();
		var veryHighValue = jQuery(this).find('.veryHighValue').text();
		var hugeValue = jQuery(this).find('.hugeValue').text();
		showSectionInfo(riverSection, waterLevelValue, currentReadingTime, currentReading, trend);
		showConversionInfo(waterLevelValue, scrapeValue, lowValue, mediumValue, highValue, veryHighValue, hugeValue);
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

<?php
footer();
