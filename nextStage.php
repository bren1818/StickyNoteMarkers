<?php
include("includes.php");
printHeader("Begin Next Stage");
if(  $_SESSION['isAdministrator'] == 1 ){
	$pID = $_REQUEST['oldProject'];
	
	$projExists =  mysql_query("SELECT COUNT(`id`) FROM `project_sheets` WHERE `id`=".$pID);
	$row = 0;
	if( $projExists ){
		$row = mysql_fetch_array($projExists );
		$row = $row['COUNT(`id`)'];
	}
	
	if( $row == 1){
		$projInfo =  mysql_query("SELECT * FROM `project_sheets` WHERE `id`=".$pID);
		$projInfo = mysql_fetch_array($projInfo );
		$projTitle = $projInfo['title'];
		$projRevision = $projInfo['revision'];
		$projDescription = $projInfo['description'];
		$projOwner = $projInfo['creator'];
?>
	<h2>Begin Next Phase for: <?php echo $projTitle; ?></h2>
    <form method="post" action="_nextStage.php" style="width: 1000px; padding: 20px 0px; overflow:hidden" enctype="multipart/form-data">
    	<div class="row">
			<div class="titleCol">
				Project Title:
			</div>
			<div class="valueCol">
				<input type="text" name="new_project_title" value="<?php echo $projTitle; ?>" required="required"/>
			</div>
		</div>
    
        <div class="row">
			<div class="titleCol">
				Project Description:
			</div>
			<div class="valueCol">
				<textarea name="new_project_description"><?php echo $projDescription; ?></textarea>
            </div>
		</div>
    
        <div class="row">
			<div class="titleCol">
				Project Revision:
			</div>
			<div class="valueCol">
				<input type="hidden" name="new_project_revision" value="<?php echo ($projRevision + 1); ?>"/>
                <input type="text" name="revision" value="<?php echo ($projRevision + 1); ?>" required="required" disabled="disabled"/>
			</div>
		</div>
    
        <div class="row">
			<div class="titleCol">
				New Image (leave blank to keep origional):
			</div>
			<div class="valueCol">
				<input size="25" name="file" type="file" />
			</div>
		</div>
    
    	 <div class="row">
            <div class="titleCol">
            	<input type="hidden" name="oldID" value="<?php echo $pID; ?>">
                <input type="hidden" name="owner" value="<?php echo $projOwner; ?>">
            	<input class="buttonSaveButton" name="save" type="submit" value="Save"/>
            </div>
           	<div class="valueCol">
            </div>
         </div>
    </form>
    
    

    
    
    
    
    
<?php
	}
	//echo "Next Steps for: ".$pID;
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/viewProjectSheet.php?viewSheet='.$pID.'">Cancel</a></p>';
}else{
	printHeader("Sorry you must be an admin to view this Screen");
	echo '<p>Oops! You shouldn\'t be here, only an administrator can modify project permissions</p>';
}
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/index.php">Go Back Home</a></p>';
	printFooter();
?>