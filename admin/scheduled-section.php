<?php
/* Copyright 2020 Jonathan Riddell <jr@jriddell.org>
   May be copied under the GNU GPL version 3 only
*/
require_once('../config.php');
require_once('../lib/ScheduledSections.php');
$scheduledSections = new ScheduledSections;
$scheduledSections->readFromJson();
if (isset($_POST['scheduledUpdates']) && isset($_POST['save'])) {
  $message = $scheduledSections->updateScheduledSections($_POST);
}
if (isset($_POST['scheduledUpdates']) && isset($_POST['delete'])) {
  $message = $scheduledSections->deleteScheduledSection($_POST);
}
if (isset($_POST['add'])) {
  $message = $scheduledSections->addNewScheduledSection($_POST);
}
  
?>
<!html>
<html>
<head>
<title>SCA Where's the Water Scheduled Section Editing</title>
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
input.delete[type=submit] { grid-column: 3/3; 
                     border: 2px solid black;
                   }
input.adddate[type=button] { grid-column: 1/2; 
                     border: 2px solid black;
                   }

p.message { border: 1px solid black;
            color: #339;
            padding: 1ex;
          }
p.message b { font-size: larger; }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<h1>Where's the Water Scheduled Section Editing</h1>

<p><a href="index.html">&#8592; back to admin index</a></p>

<?php
if (isset($message)) {
  print "<p class='message'>$message</p>";
}

print $scheduledSections->editScheduledSectionsForm();
print $scheduledSections->addScheduledSectionForm();
print $scheduledSections->editScheduledSectionsFormJavascript();
?>
</body>
</html>
