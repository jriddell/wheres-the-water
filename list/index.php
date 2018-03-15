<?php
require_once '../wheres-the-water/common.php';
require_once '../wheres-the-water/config.php';
heading();
?>
<style>
body {
    padding: 0px;
    margin: 0px;
}

table {
    border-spacing: 0px;
    border: 1px solid #ddd;
    width: 100%;
    max-width: 40em;
}

th {
    cursor: pointer;
}

th, td {
    text-align: left;
    padding: 16px;
}

tr:nth-child(even) {
    background-color: #f2f2f2
}

table a {
    text-decoration: none;
    color: black;
}
.hide {
    display: none;
}

</style>

<?php 
require_once('../wheres-the-water/lib/RiverSections.php');
$riverSections = new RiverSections();
$riverSections->readFromJson();
print "<p>Readings downloaded from SEPA at " . $riverSections->downloadTime() . "</p>\n";
print "<p>Most recent SEPA reading: " . $riverSections->calculateMostRecentReading() . "</p>\n";
?>


<p>
&nbsp;<img src='/wheres-the-water/pics/22-apps-marble.png' width='22' height='22' /> Map link for mobile phones<br />
&nbsp;<img src='/wheres-the-water/pics/osm.png' width='22' height='22' /> OpenStreetMap link<br />
&nbsp;<img title='SCA WW Guidebook number' src='/wheres-the-water/pics/sca.png' /> SCA WW Guidebook (3rd edition) Entry number<br />
&nbsp;<img src='/wheres-the-water/pics/ukrgb.ico'/> Online Guidebook Link<br />
&nbsp;<img title='Access Issue Link' src='/wheres-the-water/pics/warning.png' /> Access Issue Links<br />
</p>
<table id="riverTable">
  <tr>
   <!--When a header is clicked, run the sortTable function, with a parameter, 0 for sorting by names, 1 for sorting by country:-->  
    <th onclick="sortTable(0)">Name</th>
    <th onclick="sortTable(1)">Level</th>
    <th onclick="sortTable(2)">Grade</th>
    <th>Links</th>
  </tr>
  <?php $riverSections->printTable(); ?>
</table>

<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("riverTable");
  switching = true;
  //Set the sorting direction to ascending:
  dir = "asc"; 
  /*Make a loop that will continue until
  no switching has been done:*/
  while (switching) {
    //start by saying: no switching is done:
    switching = false;
    rows = table.getElementsByTagName("TR");
    /*Loop through all table rows (except the
    first, which contains table headers):*/
    for (i = 1; i < (rows.length - 1); i++) {
      //start by saying there should be no switching:
      shouldSwitch = false;
      /*Get the two elements you want to compare,
      one from current row and one from the next:*/
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /*check if the two rows should switch place,
      based on the direction, asc or desc:*/
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          //if so, mark as a switch and break the loop:
          shouldSwitch= true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /*If a switch has been marked, make the switch
      and mark that a switch has been done:*/
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      //Each time a switch is done, increase this count by 1:
      switchcount ++;      
    } else {
      /*If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again.*/
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>

<?php
footer();
