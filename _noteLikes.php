<?php
	include("includes.php");
	if(  isset($_SESSION['userId'])  && isset($_POST["json"])) // dont have to be an admin to like
	{
		$json = stripslashes($_POST["json"]);
		$likes = json_decode($json);
		if (isset($likes->{'noteId'} ) ){	
			if(  isset($likes->{'likes'})){
				//echo "likes :".$likes->{'noteId'};
				$query = "UPDATE `project_notes` SET  `note_likes` =  (`note_likes` + 1), `timestamp` = CURRENT_TIMESTAMP WHERE `note_id` =".$likes->{'noteId'}.";"; //add timestamp
				$query = mysql_query($query);
				if( $query ){
					echo "1";	
				}
				UpdateLog( $likes->{'projectId'}, $likes->{'noteLayerId'}, $likes->{'noteId'}, "Note Liked!", $_SESSION['userId']);
				
			}else if ( isset($likes->{'dislikes'} ) ){
				//echo "dis likes :".$likes->{'noteId'};
				$query = "UPDATE `project_notes` SET  `note_dislikes` =  (`note_dislikes` + 1), `timestamp` = CURRENT_TIMESTAMP WHERE `note_id` =".$likes->{'noteId'}.";";
				$query = mysql_query($query);
				if( $query ){
					echo "1";	
				}
				UpdateLog( $likes->{'projectId'}, $likes->{'noteLayerId'}, $likes->{'noteId'}, "Note Dis-Liked!", $_SESSION['userId']);
			}
			
			//time stamp!
		}
	}
?>