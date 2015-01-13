<?php
include("includes.php");
if(  $_SESSION['isAdministrator'] == 1 ){
	
	
	
	printHeader("Edit Project Permissions for - ");
	if( isset($_REQUEST['project']) ) 
	{
		$projectId = (int)$_REQUEST['project'];
		
		/*Check to see if there is a post*/
		 if (!empty($_POST)){
			if( isset( $_POST['numUsers'] ) && $_POST['numUsers'] > 0 ){
				/*Delete User Permissions*/
				$remPerms = "Delete FROM `project_sheet_permissions` WHERE `project_id` =".$projectId;
				$remPerms = mysql_query($remPerms);
				$newPerms ="";
				for( $x = 0; $x < $_POST['numUsers']; $x++){
					if( isset($_POST['userPermission_'.$x]) ){
						$newPerms = $newPerms."('".$projectId."','".$_POST['userPermission_'.$x]."'), ";
						/*Add User Permission*/
					}
				}
				$newPerms = substr($newPerms,0, -2);
				$newPerms = "INSERT INTO `project_sheet_permissions` (`project_id`, `user_id`) VALUES ".$newPerms.";";
				$newPerms = mysql_query($newPerms);
				SystemLog(2, "Project Permissions were updated on project : ".$projectId);
			}
		}
		
		$query = "SELECT * FROM  `project_sheets` WHERE `id` =".$_REQUEST['project'];
		$query = mysql_query($query);
		$num_rows = mysql_num_rows($query);
		if($num_rows == 1 ){
			while($info = mysql_fetch_assoc( $query ))
			{
				$creationDate = $info['creation_date'];
				$title = $info['title'];
				$description = $info['description'];
			}
			?>
            <script src="<?php echo pathToRoot(); ?>tablesorter.min.js" /></script>
    		<link rel="stylesheet" href="<?php echo pathToRoot(); ?>table_sorter_style.css" />
			<h2>Edit Permissions for: <?php echo $title; ?></h2>
            <form name="UserPermissions" method="post" action=""/>
            <table class="tablesorter">
            <thead>
            <tr>
                <th class="leftCorner">Username</th>
                <th>Company</th>
                <th class="rightCorner">Has Permissions</th>
           	</tr>
            </thead>
            <tbody>
			<?php
				$numUsers = 0;
			
				$userPerms = "SELECT  `users`.`id` ,  `username` ,  `project_id` ,  `company_name` 
FROM  `users` ,  `users_company` ,  `project_sheet_permissions` 
WHERE  `users`.`company_id` =  `users_company`.`id` &&  `users`.`id` =  `project_sheet_permissions`.`user_id` &&  `project_id` =".$projectId;
				//echo $userPerms;
				
				$userPerms = mysql_query($userPerms);	
				$num_rows = mysql_num_rows($userPerms);		
				if($num_rows > 0 ){
					while($info = mysql_fetch_assoc( $userPerms ))
					{			
						echo '<tr>';
							echo '<td>'.$info['username'].'</td><td>'.$info['company_name'].'</td><td><input type="checkbox" name="userPermission_'.$numUsers.'" value="'.$info['id'].'" checked="checked"/></td>';
						echo '</tr>';
						$numUsers++;		
					}
				}
				
				$noPerms = "SELECT  `id` ,  `username` 
							FROM  `users` 
							WHERE NOT 
							EXISTS (
							SELECT  `user_id` ,  `project_id` 
							FROM  `project_sheet_permissions` 
							WHERE  `users`.`id` =  `project_sheet_permissions`.`user_id` &&  `project_sheet_permissions`.`project_id` =".$projectId.")";
				$noPerms = mysql_query($noPerms);	
				$num_rows = mysql_num_rows($noPerms);		
				if($num_rows > 0 ){
					while($info = mysql_fetch_assoc( $noPerms ))
					{			
						echo '<tr>';
							echo '<td>'.$info['username'].'</td><td><input type="checkbox" name="userPermission_'.$numUsers.'" value="'.$info['id'].'"/></td>';
						echo '</tr>';	
						$numUsers++;		
					}
				}

			?>
            </tbody>
            </table>
            	<input type="hidden" name="numUsers" value="<?php echo $numUsers; ?>" />
            	<input type="submit" value="Save Changes" style="float: left;" />
                <a class='button projectTitleLink' href='<?php echo pathToRoot()."viewProjectSheet.php?viewSheet=".$_REQUEST['project']; ?>'> View Project Sheet</a>
                
            </form>
			<script type="text/javascript">
            $(function(){
            	$('.tablesorter').tablesorter();
            });
			</script>
                
			<?php
			//echo $_REQUEST['project'];
			echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'index.php">Go Back</a></p>';
		}else{
			echo '<p>Oops! Something happened, no project was specified to edit, please go back and try again...</p>';
			echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'index.php">Go Back</a></p>';
		}
	}else{
		echo '<p>Oops! Something happened, no project was specified to edit, please go back and try again...</p>';
		echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'index.php">Go Back</a></p>';
	}
}else{
		printHeader("Sorry you must be an admin to edit permissions");
		echo '<p>Oops! You shouldn\'t be here, only an administrator can modify project permissions</p>';
		echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/index.php">Go Back</a></p>';
}
printFooter();
?>