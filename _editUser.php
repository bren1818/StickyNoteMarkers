<?php
	include("includes.php");
	if(  $_SESSION['isAdministrator'] == 1  && isset($_POST["json"])){
		$json = stripslashes($_POST["json"]);
		$user = json_decode($json);
		
		if(  $user->{'action'} == "delete"){
			//echo "Delete ". $user->{'user'};
			$query = "UPDATE `users` SET `is_DELETED` = 1 WHERE `users`.`id` = ".$user->{'user'};
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
			}
			
		}else if ($user->{'action'} == "permdelete"){	
			$query = "DELETE FROM `users` WHERE `users`.`id` = ".$user->{'user'};
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
				SystemLog(1, "User (id: ".$user->{'user'}." has been deleted");
			}
		}else if ($user->{'action'} == "undelete"){
			$query = "UPDATE `users` SET `is_DELETED` = 0 WHERE `users`.`id` = ".$user->{'user'};
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
			}
		}else if ($user->{'action'} == "resetPass"){
			$newSalt = sha1(getTimeStamp().getIP().$user->{'user'});
			$newPass =  md5( $newSalt.sha1($user->{'newPass'}) );
			$query = "UPDATE `users` SET  `password` =  '".$newPass."', `salt` = '".$newSalt."' WHERE  `users`.`id` =".$user->{'user'}.";";
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
			}
		}
	}
?>