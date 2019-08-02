<?php

require_once '../../wheres-the-water/common.php';
require_once '../../wheres-the-water/config.php';
if (array_key_exists('iframe', $_GET) and $_GET['iframe'] == 'true') {
    print '<p>Iframe heading</p>';
    heading(true);
} else {
    heading();
}
require_once '../../wheres-the-water/lib/RiverSections.php';
require_once '../../wheres-the-water/lib/WheresTheWater.php';

print '<p>XXX iframe: ' . $_GET['iframe'];
$riverSections = new RiverSections;
$riverSections->readFromJson();

$wtw = new WheresTheWater;
$wtw->headerStuff();
$wtw->theMap($riverSections);
$wtw->theJavaScript();

if (array_key_exists('iframe', $_GET) and $_GET['iframe'] == 'true') {
    footer();
} else {
    ?>
    </body>
</html>
<?php
}
