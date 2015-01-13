<?php
include("includes.php");
printHeader("Create Project Sheet");
$done = 0;
$errors = 0;
 if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save']) &&  $_POST['save'] == "Save"  && $_SESSION['isAdministrator'] == 1  ){
	
	define ("MAX_SIZE","4000"); //4mb
	
	//get Image
	$image = $_FILES["file"]["name"];
 	$uploadedfile = $_FILES['file']['tmp_name'];
	if ($image){
 	
 		$filename = stripslashes($_FILES['file']['name']);
  		$extension =pathinfo($filename, PATHINFO_EXTENSION);
 		$extension = strtolower($extension);
 		if( ($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
 		{
 			$errors=1;
 		}
 		else
 		{
 			$size = filesize($_FILES['file']['tmp_name']);
			if ($size > MAX_SIZE*1024)
			{
				$errors = 1;
				echo "<p>File to Big!</p>";
			}else{
			
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
				}else if($extension=="png"){
					$uploadedfile = $_FILES['file']['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
				}else{
					$src = imagecreatefromgif($uploadedfile);
				}
	
				list($width,$height)=getimagesize($uploadedfile);
	
				$newwidth=1200; ///
				$newheight=($height/$width)*$newwidth;
				$tmp=imagecreatetruecolor($newwidth,$newheight);
				
				$newwidth1=120; ///
				$newheight1=($height/$width)*$newwidth1;
				$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
				
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
				imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
				
				$filename = "tmp_images/". $_FILES['file']['name'];
				$filename1 = "tmp_images/small". $_FILES['file']['name'];
				
				imagejpeg($tmp,$filename,100);
				imagejpeg($tmp1,$filename1,100);
				
				$fp = fopen($filename, 'r');
				$size = filesize($filename);
				$fileType= "application/octet-stream";
				$content = fread($fp, filesize($filename));
				$content = addslashes($content);
				fclose($fp);
				$query = "INSERT INTO `project_sheet_images` (`filename`, `size`, `type`, `content`, `height`, `width` ) ".
				"VALUES ('".$_FILES['file']['name']."', '$size', '$fileType', '$content', '$newheight', '$newwidth')";
				mysql_query($query);// or die('Error, query failed'); 
				$imageId = mysql_insert_id();
				unlink($filename);
			
				$fp = fopen($filename1, 'r');
				$size = filesize($filename1);
				$fileType= "application/octet-stream";
				$content = fread($fp, filesize($filename1));
				$content = addslashes($content);
				fclose($fp);
				$query = "INSERT INTO `project_sheet_images` (`filename`, `size`, `type`, `content`, `height`, `width` ) ".
				"VALUES ('".$_FILES['file']['name']."', '$size', '$fileType', '$content', '$newheight1', '$newwidth1')";
				mysql_query($query);// or die('Error, query failed'); 
				$thumbId = mysql_insert_id();
				unlink($filename1);
				
				imagedestroy($src);
				imagedestroy($tmp);
				imagedestroy($tmp1);
			}
		}
	}

	$title = mysql_real_escape_string($_POST['title']);
	$description = mysql_real_escape_string($_POST['description']);
	$owner = $_SESSION['userId'];
	
	if( $errors == 0 ){
		$query = "INSERT INTO `project_sheets` (`id`, `creator`, `creation_date`, `title`, `description`, `image_id`, `thumb_id`) VALUES (NULL, '".$owner."', CURRENT_TIMESTAMP, '".$title."', '".$description."', '".$imageId."', '".$thumbId."');";

		mysql_query($query);
		
		$psId = mysql_insert_id();
		
		 UpdateLog($psId, "", "", "Created New Project (".$title.")", $_SESSION['userId']);
		
		
		
		/*Image Update*/
		$img = "UPDATE `project_sheet_images` SET  `project_id` =  '".$psId."' WHERE  `project_sheet_images`.`id` =".$imageId.";";
		mysql_query($img);
		$thumb = "UPDATE `project_sheet_images` SET  `project_id` =  '".$psId."' WHERE  `project_sheet_images`.`id` =".$thumbId.";";
		mysql_query($thumb);
		
		$query = "INSERT INTO `project_worksheet_layers` (`layerId`, `project_sheet_id`, `layer_title`, `layer_color`, `owner_id`) VALUES (NULL, '".$psId."', 'Default', '".$_POST['layerColor']."', ".$_SESSION['userId'].");";
		mysql_query($query);
		$Lid = mysql_insert_id();
		
		UpdateLog($psId, $Lid, "", "Layer Created (Default Layer) for Project: ".$title, $_SESSION['userId']);
		
		$done = 1;
		
		
		
		SystemLog(1, "A new project as created by user: ".$_SESSION['username']." titled (".$title.")");
		
		echo "<p>Project Created OK!</p>";
		?>
         <p><a href="<?php echo pathToRoot()."viewProjectSheet.php?viewSheet=".$psId; ?>">Continue to Project</a>.</p>
         <p><a class="button buttonGoBack" href="<?php echo pathToRoot()."notes/index.php"; ?>">Go Back</a>.</p>
        <?php
	}
	
}
if( $done ==  0 ){
	if(  $_SESSION['isAdministrator'] == 1 ){
	?>
	<form id="newProjectSheet" name="newProjectSheet" method="POST" action="" enctype="multipart/form-data">
		 <div class="row">
				<div class="titleCol">
					Project Title:
				</div>
				<div class="valueCol">
					<input type="text" id="title" name="title" value="" maxlength="55" required="required" />
				</div>
                 <div class="clear" ></div>
			</div>
			
			 <div class="row">
				<div class="titleCol">
					Description:
				</div>
				<div class="valueCol">
					<textarea name="description" id="description"></textarea>
				</div>
                 <div class="clear" ></div>
			</div>
			
            <div class="row">
				<div class="titleCol">
					Starting Note Color:
				</div>
				<div class="valueCol">
                    <select name="layerColor" id="layerColor">
                        <?php
                            $layerColors = "SELECT * FROM `layer_colors`";
                            $layerColors = mysql_query($layerColors);
                            while($colours = mysql_fetch_assoc( $layerColors )){
                                echo '<option value="'.$colours['value'].'" style="background-color: '.$colours['value'].'">'.$colours['color'].'</option>';
                            }
                        ?>
                    </select>
				</div>
                 <div class="clear" ></div>
		  </div>
            
            
			<div class="row">
				<div class="titleCol">
					Image:
				</div>
				<div class="valueCol">
					 <input size="25" name="file" type="file" required="required"/>
				</div>
                 <div class="clear" ></div>
		  </div>
		  <input class="buttonSaveButton" name="save" type="submit" value="Save"/>           
	</form>
    <p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Go Back</a></p>    
	<?php
	}else{
	?>
		<p>Sorry! You do not have access here.<p>
		<p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Go Back</a></p>
	<?php	
	}
}
printFooter();
?>