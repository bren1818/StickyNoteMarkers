<?php
	include("includes.php");
	if(  $_SESSION['isAdministrator'] == 1  && isset($_POST["json"])){ //should only have to be a user
		$json = stripslashes($_POST["json"]);
		$proj = json_decode($json);
		$delProj = $proj->{'deleteProject'};
		//echo $delProj;
		$delNotes = mysql_query("DELETE FROM `project_sheets` WHERE `project_sheets`.`id` =".$delProj.";");
		$delNotes = mysql_query("DELETE FROM `project_worksheet_layers` WHERE `project_sheet_id` =".$delProj.";");
		$delNotes = mysql_query("DELETE FROM `project_notes` WHERE `project_sheet_id` =".$delProj.";");
		$delNotes = mysql_query("DELETE FROM `project_sheet_images` WHERE `project_id` =".$delProj.";");
		$delPerms = mysql_query("DELETE FROM `project_sheet_permissions`  WHERE `project_id` =".$delProj.";");
		$delLogs = mysql_query("DELETE FROM `note_logs`  WHERE `project_id` =".$delProj.";");
		UpdateLog($delProj, "", "", "Project, it's layers and Notes Deleted", $_SESSION['userId']);
		SystemLog(2, "Project (id: ".$delProj." has been deleted");
	}
?>

