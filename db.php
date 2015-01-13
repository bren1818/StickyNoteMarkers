<?php
	$dbHost ="localhost";
	$dbUser = "root";
	$dbPass = "usbw"; 
	$dbDatabase = "stickynotes"; 
	$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database."); 
	
	mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");
	$mysqli = new mysqli("$dbHost", "$dbUser", "$dbPass", "$dbDatabase");
	
	if ($mysqli->connect_errno) {
   		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
?>