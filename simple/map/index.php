<?php

require_once '../../wheres-the-water/common.php';
require_once '../../wheres-the-water/config.php';
$ps = false;
if (array_key_exists('iframe', $_GET) and $_GET['iframe'] == 'true') {
    $ps = true;
} else if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], "paddlescotland") > 0) {
    $ps = true;
}

if ($ps) {
    heading(true);
} else {
    heading();
require_once '../../wheres-the-water/lib/RiverSections.php';
require_once '../../wheres-the-water/lib/WheresTheWater.php';

$riverSections = new RiverSections;
$riverSections->readFromJson();

$wtw = new WheresTheWater;
$wtw->headerStuff();
$wtw->theMap($riverSections);
$wtw->theJavaScript();
}

if ($ps) {
    ?>
    </body>
</html>
<?php
} else {
    footer();
}
