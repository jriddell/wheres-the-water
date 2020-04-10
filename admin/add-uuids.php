<?php
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/AddUUID.php');

?>

<html>
<head><title>Add UUIDs</title></head>
<body>

<h1>Add UUIDs</h1>
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

<form action="add-uuid.php" method="get">
<input type="submit" name="download" value="Add UUIDs" />
</form>

</body>
</html>

