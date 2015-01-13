<?php
	include("includes.php");
	if( isset($_SESSION['userId']) && isset($_POST["json"])){ //shouldnt have to be an admin
		$json = stripslashes($_POST["json"]);
		$note = json_decode($json);
		
		
		if(  $note->{'noteDeleted'} == 1){
			
			if( $note->{'noteId'} != -1 ){
				$query = "DELETE FROM `project_notes` WHERE `project_notes`.`note_id` =". $note->{'noteId'};
				mysql_query($query);
				
				UpdateLog($note->{'noteProjectId'}, $note->{'noteLayerId'} , $note->{'noteId'}, "Note Deleted", $_SESSION['userId']);
				echo "1";
			}
		}else{		
			if( $note->{'noteId'} == -1){
				//echo "New Note! - ".$note->{'noteTitle'};
				$query = "INSERT INTO `project_notes` (`note_id`, `project_sheet_id`, `project_layer_id`, `user_id`, `note_title`, `note_content`, `note_x`, `note_y`, `timestamp`) VALUES (NULL, '".$note->{'noteProjectId'}."', '".$note->{'noteLayerId'}."', '".$note->{'noteAuthor'}."', '".mysql_real_escape_string($note->{'noteTitle'})."', '".mysql_real_escape_string($note->{'noteText'})."', '". substr($note->{'noteX'},0,-2)."', '".substr($note->{'noteY'},0,-2)."', CURRENT_TIMESTAMP);";
				mysql_query($query);
				$newNoteId = mysql_insert_id();
				UpdateLog($note->{'noteProjectId'}, $note->{'noteLayerId'}, $newNoteId, "Note Created titled: (".mysql_real_escape_string($note->{'noteTitle'}).")", $_SESSION['userId']);
				echo $newNoteId;
			}else{
				date_default_timezone_set('America/New_York');
				$date =  date('Y-m-d H:i:s');
				//echo $date;
				
				$query = "UPDATE `project_notes` SET  `note_title` =  '".mysql_real_escape_string($note->{'noteTitle'})."',
	`note_content` =  '".mysql_real_escape_string($note->{'noteText'})."',
	`note_x` =  '". substr($note->{'noteX'},0,-2)."',
	`note_y` =  '". substr($note->{'noteY'},0,-2)."',
	`timestamp` =  '". $date."' WHERE  `project_notes`.`note_id` =". $note->{'noteId'}.";";
	
				mysql_query($query);
				
				UpdateLog($note->{'noteProjectId'}, $note->{'noteLayerId'} , $note->{'noteId'}, "Note Updated (Title: ".mysql_real_escape_string($note->{'noteTitle'})." - Content: ".mysql_real_escape_string($note->{'noteText'}).")", $_SESSION['userId']);
				
				echo "1";
			}
			
		}
	}
?>