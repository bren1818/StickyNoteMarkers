<?php
	include("db.php");
	include("session.php");
	function printHeader($title)
	{
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo pathToRoot(); ?>style.css" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js" /></script>
    <script type="text/javascript" src="<?php echo pathToRoot(); ?>scripts.js" /></script>
    <!--[if gte IE 9]>
  <style type="text/css">

  </style>
<![endif]-->
</head>
<body>
<div id="bodyContent">
	<?php
        if(isset( $_SESSION['userId'] ) )
		{
            echo "<div id='adminTools'>";
				echo "<p>Welcome: ".$_SESSION['username'];
				if(  $_SESSION['isAdministrator'] == 1 ){
					echo ' (Administrator)';
				}else{
					echo ' (User)';
				}
				echo " - ";
				echo '<a href="session.php?destroy">Log Out</a> | ';
				echo 'Last Login at: '.$_SESSION['last_login']." from: ".$_SESSION['last_ip'];
				echo '</p>';
				
				$numNotifications = mysql_query("SELECT COUNT(`log_id`) FROM `system_logs` WHERE `event_time` > '".$_SESSION['last_login']."'");
				while($row = mysql_fetch_array($numNotifications )){
					$notificationsCount = $row['COUNT(`log_id`)'];
				}
				
				if(  $_SESSION['isAdministrator'] == 1 && $notificationsCount > 0 ){
					echo '<div id="NotificationUpdates"><p>You have: <a href="'.pathToRoot()."/".'viewNotifications.php"><span class="notificationCount">';
					echo $notificationsCount;
					echo '</span> notifications</a> pending.</div>';
				}
				
            echo "</div>";
        }
	}

	function printFooter(){
?>
		<div class="clear"></div>
    	<div class="push"></div>
        
	</div>
    <div class="clear"></div>
    <div id="footer">
        <p>Copyright &copy; 2012 Brendon Irwin</p>
    </div>
</body>
</html>
<?php
	}
	
	
function startsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function endsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}

function getTimeStamp(){
	date_default_timezone_set('America/New_York');
	return	date('Y-m-d H:i:s');
}

function getIP(){
	return $_SERVER['REMOTE_ADDR'];
}

function UpdateLog($projectId, $layerId, $noteId, $action, $userId){
	$q = mysql_query("INSERT INTO `note_logs` (`action_id`, `project_id`, `LayerId`, `NoteId`, `action`, `timestamp`, `userId`) VALUES (NULL, '$projectId', '$layerId', '$noteId', '$action', CURRENT_TIMESTAMP, '$userId');");
}

function SystemLog($log_code, $description){
	$q = mysql_query("INSERT INTO `system_logs` (`event_time`, `log_code`, `description`, `offending_ip`) VALUES ('".getTimeStamp()."', $log_code, '".mysql_real_escape_string($description)."', '".getIP()."');");
}


	
?>