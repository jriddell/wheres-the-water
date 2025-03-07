<?php

function heading($iframe=false) {
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="Water levels on popular kayaking rivers around Scotland" />
    <meta name="og:description" content="Water levels on popular kayaking rivers around Scotland" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="/wheres-the-water/js/riverLevels.js"></script>
    <script type="text/javascript" src="/wheres-the-water/js/utilLib.js?"></script>
    <!-- Latest compiled and minified CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
    <!-- jQuery library -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
    <!-- Latest compiled JavaScript -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
    <title>Where's the Water, Scottish Whitewater River Levels</title>
    <style>
        body {font-family: 'Ubuntu', sans-serif;}
    </style>
</head>

<body style="background: white" onload="sortTable(0)">
<?php
    if (!$iframe) {
?>
<p><a href="http://www.andyjacksonfund.org.uk"><img src="/wheres-the-water/andy-jackson-fund.png" width="350" /></a>
<!-- <a href="https://www.paddlescotland.org.uk"><img src="/wheres-the-water/pics/paddle-scotland.png" width="350" /></a>--></p>

<h1><!-- Paddle Scotland--> Where&#039;s The Water?</h1>

<h2>Scottish Whitewater River Levels</h2>
<?php
    } else {// if !frame
    ?>
    <p>Where's the Water is available from the <br /><a href="https://www.andyjacksonfund.org.uk/wtw/map/#map" target="_top">Andy Jackson Fund for Access website</a>.</p>
    <?php
    } // else iframe
}

function footer() {
?>
<p>Scheduled Water Flows.<br>
<img src="/wheres-the-water/pics/CONSTANT.png" width="10" height="10" /> Constant. Flow is always on.<br>
<img src="/wheres-the-water/pics/TODAY.png" width="10" height="10" /> Today, scheduled date on now.<br>
<img src="/wheres-the-water/pics/TOMORROW.png" width="10" height="10" /> Tomorrow, scheduled date is mañana.<br>
<img src="/wheres-the-water/pics/NEXT_7_DAYS.png" width="10" height="10" /> Next date is within 7 days.<br>
<img src="/wheres-the-water/pics/NEXT_30_DAYS.png" width="10" height="10" /> Next date is within 30 days.<br>
<img src="/wheres-the-water/pics/NOT_THIS_MONTH.png" width="10" height="10" /> Not This Month (schedule date over 30s days in future).<br>
<img src="/wheres-the-water/pics/NO_KNOWN_DATES.png" width="10" height="10" /> No known dates.<br>
Dates are added manually.   In case of discrepancy the info page linked is definitive over this list.   <a href="https://jriddell.org/contact/">Feedback and contributions welcome</a>.</p>

<p><!-- Paddle Scotland--> Where's the Water uses <a href="https://www2.sepa.org.uk/waterlevels/">water level data from SEPA</a>.</p>

<p>Code written and maintained by <a href="http://www.edinburghlinux.co.uk">Jonathan Riddell</a>. <a href="https://github.com/jriddell/wheres-the-water">Code on GitHub</a>.  Please file <a href="https://github.com/jriddell/wheres-the-water/issues">bug reports and feature requests on GitHub</a>.</p>

<p><a href="https://calendar.google.com/calendar/embed?src=scottishwwguide%40gmail.com&ctz=Europe%2FLondon">Scottish White Water Guide Calendar</a>.</p>
<p><a href="http://goo.gl/forms/nnEOgVkw8ebhygW52">Help Us Calibrate: River Level Report Form</a>.</p>
<p><a href="http://goo.gl/forms/YQ3xZTi30vrtFYpo1">River Gauge Request Form</a></a>.</p>
<p><a href="/wtw">Simple List View</a></p>
<p><a href="/wtw/map">Simple Map View</a></p>
<!-- <p><a href="https://www.paddlescotland.org.uk/where-go/wheres-water">Full Map View</a></p> -->
</body>
</html>
<?php
}
