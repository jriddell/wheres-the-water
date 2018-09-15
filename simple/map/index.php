<?php

require_once '../../wheres-the-water/common.php';
require_once '../../wheres-the-water/config.php';
heading();
require_once '../../wheres-the-water/lib/RiverSections.php';
require_once '../../wheres-the-water/lib/WheresTheWater.php';

$riverSections = new RiverSections;
$riverSections->readFromJson();

$wtw = new WheresTheWater;
$wtw->headerStuff();

?>

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
<?php
$wtw->theJavaScript();
footer();
