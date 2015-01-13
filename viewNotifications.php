<?php
	include("includes.php");
	printHeader("Notifications");
	if(  $_SESSION['isAdministrator'] == 1 ){ //you're an admin so you're allowed
?>
	<script src="<?php echo pathToRoot()."notes/"; ?>tablesorter.min.js" /></script>
	<link rel="stylesheet" href="<?php echo pathToRoot()."notes/"; ?>table_sorter_style.css" />
	
<?php
	$numNotifications = mysql_query("SELECT COUNT(`log_id`) FROM `system_logs` WHERE `event_time` > '".$_SESSION['last_login']."'LIMIT 1");
	while($row = mysql_fetch_array($numNotifications )){
		echo "<h2>Notifications (".$row['COUNT(`log_id`)'].") - Since last login</h2>";
	}
	$numNotifications = mysql_query("SELECT * FROM `system_logs` WHERE `event_time` > '".$_SESSION['last_login']."'");
	while($row = mysql_fetch_array($numNotifications )){
		echo "<p>".$row['log_id']." - ".$row['event_time']." - ".$row['description']." - From IP: ".$row['offending_ip']."</p>";
	}
	
	
	
	
	/*
	$projectLogs =  mysql_query("SELECT `action_id`, `LayerId`,`NoteId`,`action`,`timestamp`, `username` FROM `note_logs` INNER JOIN `users` on `users`.`id` = `note_logs`.`userId` WHERE `project_id`= ".$id);
	$num_rows = mysql_num_rows($projectLogs );
	if($num_rows > 0 ){
		echo '<table class="tablesorter"><thead><tr>';
				echo "<th class='leftCorner'>Action ID</th>";	
				//echo "<td>". $info['project_id']."</td>";
				echo "<th>Layer ID</th>";
				echo "<th>Note ID</th>";
				echo "<th>Action</th>";
				echo "<th>Time Stamp</th>";
				echo "<th class='rightCorner'>User</th>";
			echo "</tr></thead><tbody>";
		while($info = mysql_fetch_assoc( $projectLogs ))
		{
			echo "<tr>";
				echo "<td>". $info['action_id']."</td>";	
				echo "<td><a href='#layer_".$info['LayerId']."' class='viewLayerInfo'>". $info['LayerId']."</a></td>";
				echo "<td><a href='#note_".$info['NoteId']."' class='viewNoteInfo'>". $info['NoteId']."</a></td>";
				echo "<td>". $info['action']."</td>";
				echo "<td>". $info['timestamp']."</td>";
				echo "<td>". $info['username']."</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		?>
		<script type="text/javascript">
			$(function(){
				$('.tablesorter').tablesorter();
			});
		</script>
		<?php
		*/
		
	}else{
		echo "<p>Sorry you do not have permissions to see project logs.</p>";
	}

?>
	<p><a class="button buttonGoBack" href="<?php echo pathToRoot()."notes/index.php"; ?>">Go Back</a></p>
    
<?php
printFooter();
?>