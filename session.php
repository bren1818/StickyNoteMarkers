<?php
session_start();
function pathToRoot(){
	(substr_count( getcwd(), '\\') - 2 > 0) ? $dir_depth = substr_count( getcwd(), '\\') - 2  : $dir_depth=0; 
	$toRoot= "";
	for( $x = 0; $x < $dir_depth; $x++){
		$toRoot = $toRoot."../";
	}
	return $toRoot;
}

function curPageName() {
 return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

$inactive = 18000; //1800

if(isset($_SESSION['userId']) ) {
	$session_life = time() - $_SESSION['time'];
	if($session_life > $inactive)
    { 
		session_unset();
		session_destroy(); 
		session_start();
		$_SESSION['goBackto'] = curPageURL();
		$_SESSION['loginAttempts'] = 0;
		header("Location: ".pathToRoot()."login.php?timeOut=1"); 
	}else{	
		$userId = $_SESSION['userId'];
		$username = $_SESSION['username']; 
		$startTime = $_SESSION['start_time'];
		$activeFor = (time() - $startTime);
	}
}else{
	if ( curPageName() != "login.php" ){
		$_SESSION['goBackto'] = curPageURL();
		header("Location: ".pathToRoot()."login.php?plzLogin=1"); 
	}
}

$_SESSION['time'] = time(); //reset the time

//if( isset($_SESSION['ip']) && $_SESSION['ip'] != $_SERVER['REMOTE_ADDR']){ //are we on the session registered to the machine?
//	$sessionWarning = "IP mis-match, you may be logged in at multiple locations or someone may be trying to do a session highjack";
//}

if( isset($_REQUEST['destroy']) ){ //called on logout
	session_destroy(); 
	//session_start();
	$_SESSION['loginAttempts'] = 0;
	header("Location: ".pathToRoot()."login.php?logout=1"); 
}
?>