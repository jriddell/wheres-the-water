<?php
	$result = db_query( "SELECT * FROM {application_status} where application_id = 1"); 
	$row = $result->fetch();
?>
<table  cellspacing="0" class="riverlevels">
			<tr>
			  <td class="dataHeaders" colspan="2">Data Last Polled</td>
			</tr>
			<tr>
			  <td class="dataValues" style="width:190px" colspan="2"><?php print date('jS M \a\t H:i',strtotime($row->last_run)) ?></td>
			</tr>
			<tr>
			  <td colspan="2" height="4"></td>
          		</tr>
			<tr>
			  <td class="dataHeaders" colspan="2">Most Recent Reading</td>
			  </tr>
			 <tr>
			  <td colspan="2" class="dataValues" style="width:190px" ><?php print $row->most_recently_updated_section ?> at <?php print date('H:i \o\n jS M',strtotime($row->most_recently_updated_time))  ?> was <?php print $row->most_recently_updated_level  ?>
			  </td>
			</tr>
	</table>
<div class="riverHeader" id="sectionname">&nbsp;</div>

<div class="riverHeader" id="level">&nbsp;</div>

<div class="riverHeader" id="lastUpdated">&nbsp;</div>

<table>
	<tbody>
		<tr>
			<td class="dataHeaders">Current Reading</td>
			<td class="dataValues" id="currentReading">&nbsp;</td>
		</tr>
		<tr>
			<td class="dataHeaders">Trend</td>
			<td class="dataValues" id="trend">&nbsp;</td>
		</tr>
		<tr>
			<td class="dataHeaders" colspan="2">Callibration used:</td>
		</tr>
		<tr>
			<td align="right" colspan="2" bgcolor="#424242">
			<table class="sub-table">
				<tbody>
					<tr>
						<td class="callibHeaders" bgcolor="#FF0000">Huge</td>
						<td class="callibVals" id="huge">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FF6060">Very High</td>
						<td class="callibVals" id="veryHigh">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FFC004">High</td>
						<td class="callibVals" id="high">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#FFFF33">Medium</td>
						<td class="callibVals" id="medium">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#00FF00">Low</td>
						<td class="callibVals" id="low">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#CCFFCC">Scrapeable</td>
						<td class="callibVals" id="justRunnable">0</td>
					</tr>
					<tr>
						<td class="callibHeaders" bgcolor="#CCCCCC">Empty</td>
						<td class="callibVals" id="empty">0</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
