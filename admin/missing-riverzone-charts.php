<?php
/* Copyright 2019 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 only
*/
require_once('../config.php');
require_once('../lib/RiverSections.php');
require_once('../lib/RiverZoneStations.php');
$riverSections = new RiverSections;
$riverSections->readFromJson();

$riverZoneStations = new RiverZoneStations();



?>
<!html>
<html>
<head>
<title>SCA Where's the Water River Section Editing</title>
<style>

form {font-family: sans-serif; 
      border: 1px solid black; 
      background-color: #eee;
      grid-template-columns: 200px 1fr 200px 1fr;
      display: grid;
      padding: 1ex;
     }

legend {font-size: larger; 
        padding-top: 1em;
        grid-column: 1 / 4;
        font-weight: bold;
       }

label { grid-column: 1 / 2;
      }

input { border: 1px solid black; 
        grid-column: 2 / 3;
        margin-right: 3em;
      }

label.right { grid-column: 3 / 4;
      }

input.right {
        grid-column: 4 / 5;
      }

input[type=submit] { grid-column: 1/2;
                     border: 2px solid black;
                     margin: 1em;
                   }
input.right[type=submit] { grid-column: 2/3; 
                        width: 2em;
                     border: 2px solid black;
                   }

p.message { border: 1px solid black;
            color: #339;
            padding: 1ex;
          }
p.message b { font-size: larger; }
</style>
</head>
<body>
<h1>Where's the Water River Section Editing</h1>

<p><a href="index.html">&#8592; back to admin index</a> | <a href="http://canoescotland.org/admin/config/development/performance">Clear Cache to Show Changes</a></p>

<?php
foreach($riverSections->riverSectionsData as $jsonid => $riverSection) {
    if ($riverZoneStations->link($riverSection) === false) {
        print "No Riverzone station found for " . $riverSection['name'];
    }
}
?>
</body>
</html>
