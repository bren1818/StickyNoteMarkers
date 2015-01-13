<?php
	include("db.php");
	$id =  $_REQUEST['id'];
	$type = $_REQUEST['type'];

	
	$query = "SELECT filename, type, size, content " .
			 "FROM `project_sheet_images` WHERE `id` = '$id'";
	
			 
	$result = mysql_query($query) or die('Error, query failed');
	list($name, $type, $size, $content) =  mysql_fetch_array($result);
	header("Content-length: $size");
	header("Content-type: $type");
	header("Content-Disposition: attachment; filename=$name");
	echo $content;
	mysql_free_result($query);
?>