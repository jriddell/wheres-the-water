<?php
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/AddUUID.php');

   Generate a UUID based on a set seed and the section name, add into the river-sections.json file.  This can be used by Riverzone etc to refer to our sections.
?>

<html>
<head><title>Add UUIDs</title></head>
<body>

<h1>Add UUIDs</h1>
<p>Generate a UUID based on a set seed and the section name, add into the river-sections.json file.  This can be used by Riverzone etc to refer to our sections.</p>
<p><a href="index.html">&#8592; back to admin index</a></p>

<?php
if (array_key_exists('download', $_GET)) {
    print "\n<h1>Adding UUIDs</h1>\n";
    $updater = new AddUUID();
    $updater->doAddUUID();
    $updater->writeOutJson();
    print "\n<h1>Done</h1>\n";
    //print "<p>Now run on embra: scp /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json canoedev@canoescotland.org:/var/www/vhosts/canoescotland.org/httpdocs/wheres-the-water/data/</p>";
}

?>

<form action="add-uuids.php" method="get">
<input type="submit" name="download" value="Add UUIDs" />
</form>

</body>
</html>

