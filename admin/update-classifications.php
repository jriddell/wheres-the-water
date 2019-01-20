<?php
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/GrabSepaRivers.php');
require_once('../lib/GrabSepaClassifications.php');

?>

<html>
<head><title>Download SEPA Classifications</title></head>
<body>

<h1>Download SEPA Classifications</h1>
<p><a href="index.html">&#8592; back to admin index</a></p>

<?php
if (array_key_exists('download', $_GET)) {
    print "\n<h1>Downloading SEPA River Readings...</h1>\n";
    $classifications = new GrabSepaClassifications();
    $classifications->doClassificationsGrab();
    $classifications->writeOutClassification();
    print "\n<h1>Done</h1>\n";
}

?>

<form action="update-classifications.php" method="get">
<input type="submit" name="download" value="Update Classifications" />
</form>

</body>
</html>

