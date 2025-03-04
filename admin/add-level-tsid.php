<?php
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/AddLevelTSID.php');

//Download the list of 15minute Level TS_ID from SEPA and add into the river-sections.json file
//FIXME do this after adding a new section
?>

<html>
<head><title>Add SEPA Timeseries 15minute Level TS_IDs</title></head>
<body>

<h1>Add Level TS_IDs</h1>
<p>Download the list of 15 minutes Level TS_IDs from SEPA Timeseries API and add into the river sections file.</p>
<p>FIXME do this after adding a new section</p>
<p><a href="index.html">&#8592; back to admin index</a></p>

<?php
if (array_key_exists('download', $_GET)) {
    print "\n<h1>Adding 15minute Level TS_IDs</h1>\n";
    $updater = new AddLevelTSID();
    $updater->doAddLevelTSIDs();
    print "\n<h1>Done, check river-sections.json now all have a level_ts_id entry.</h1>\n";
}

?>

<form action="add-level-tsid.php" method="get">
<input type="submit" name="download" value="Add Level TD_ISs" />
</form>

</body>
</html>

