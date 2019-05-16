<?php
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/UpdateFarsonCameraThumbs.php');

?>

<html>
<head><title>Update Farson Camera Thumbs</title></head>
<body>

<h1>Update Farson Camera Thumbs</h1>
<p><a href="index.html">&#8592; back to admin index</a></p>

<?php
if (array_key_exists('download', $_GET)) {
    print "\n<h1>Updating Farson Camera Thumbs</h1>\n";
    $updater = new UpdateFarsonCameraThumbs();
    $updater->doUpdateThumbs();
    $updater->writeOutThumbs();
    print "\n<h1>Done</h1>\n";
    //print "<p>Now run on embra: scp /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json canoedev@canoescotland.org:/var/www/vhosts/canoescotland.org/httpdocs/wheres-the-water/data/</p>";
}

?>

<form action="update-thumbs.php" method="get">
<input type="submit" name="download" value="Update Thumbs" />
</form>

</body>
</html>

