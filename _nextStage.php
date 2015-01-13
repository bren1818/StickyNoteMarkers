<?php
	include("includes.php");
	include("includes_imageManipulation.php");
	if(  $_SESSION['isAdministrator'] == 1 ){
		printHeader("Saving Project");
		define ("MAX_SIZE","4000"); //4mb
?>

<?php

	$oldID = $_POST['oldID'];


	$image = $_FILES["file"]["name"];
	$imgId = 0;
	$thumbId = 0;
	
	if ($image){
		echo "<p>Creating new Image thumbnails...";
		$img = imagetoThumb( $_FILES["file"], 1200  );
		$thumb = imagetoThumb( $_FILES["file"], 120, 100, 1  );

		$query = "INSERT INTO `project_sheet_images` (`filename`, `size`, `type`, `content`, `height`, `width` ) ".
		"VALUES ('".$img['fileName']."', '".$img['size']."', '".$img['type']."', '".$img['content']."', '".$img['height']."', '".$img['width']."')";
		mysql_query($query);// or die('Error, query failed'); 
		$imgId = mysql_insert_id();
		
		$query = "INSERT INTO `project_sheet_images` (`filename`, `size`, `type`, `content`, `height`, `width` ) ".
		"VALUES ('".$img['fileName']."', '".$img['size']."', '".$img['type']."', '".$img['content']."', '".$img['height']."', '".$img['width']."')";
		mysql_query($query);// or die('Error, query failed'); 
		$thumbId = mysql_insert_id();
		echo " Complete!</p>";
	}else{
		//get the thumb ids of old project
		echo "<p>Setting up project to use existing thumbnails...";
		$oldProjInfo =  mysql_query("SELECT `image_id`, `thumb_id` FROM `project_sheets` WHERE `id`=".$oldID);
		
		$oldProjInfo = mysql_fetch_array($oldProjInfo );
		$imgId = $oldProjInfo['image_id'];
		$thumbId = $oldProjInfo['thumb_id'];
		echo " Complete!</p>";
	}
	
	echo "<p>Setting up project data...";
	
	$title = $_POST['new_project_title'];
	$description =  $_POST['new_project_description'];
	$revision = $_POST['new_project_revision'];
	$owner = $_POST['owner'];
	
	$query = "INSERT INTO `project_sheets` (`id`, `creator`, `creation_date`, `title`, `description`, `image_id`, `thumb_id`, `revision`, `parent_id`) VALUES (NULL, '".$owner."', CURRENT_TIMESTAMP, '".$title."', '".$description."', '".$imgId."', '".$thumbId."',".$revision." , ".$oldID.");";

	mysql_query($query);
	
	$psId = mysql_insert_id();
	
	echo " Complete!</p>";
	
	 UpdateLog($psId, "", "", "Created Next Revision (".$revision.") for the Project (".$title.")", $_SESSION['userId']);
	
	if ($image){
		/*Image Update*/
		echo "<p>Linking thumbnails...";
		$img = "UPDATE `project_sheet_images` SET  `project_id` =  '".$psId."' WHERE  `project_sheet_images`.`id` =".$imgId.";";
		mysql_query($img);
		$thumb = "UPDATE `project_sheet_images` SET  `project_id` =  '".$psId."' WHERE  `project_sheet_images`.`id` =".$thumbId.";";
		mysql_query($thumb);
		echo " Complete!</p>";
	}
	
	//DUP PERMISSIONS
	
	echo "<p>Duplicating project permissions and applying to revision ".$revision."...";
	
	
	
	/***DO THIS**********************************************************/
	
	
	
	
	echo " Complete!</p>";	
	//close old project
	
	echo "<p>Closing Old Project....";
	
	/***DO THIS**********************************************************/
	
	echo " Complete!</p>";	
	
	SystemLog(1, "Project (".$title.") has progressed to revision: ".$revision);
	
	echo "</hr />";
	
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/viewProjectSheet.php?viewSheet='.$psId.'">Continue to Revision '.$revision.'</a><a class="button buttonGoBack" href="'.pathToRoot().'notes/index.php">Go Back Home</a></p>';
	
	
	
}else{
	printHeader("Sorry you must be an admin to view this Screen");
	echo '<p>Oops! You shouldn\'t be here, only an administrator can modify project permissions</p>';
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/index.php">Go Back Home</a></p>';
}
	
	printFooter();
?>