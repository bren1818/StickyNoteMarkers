<?php
include("includes.php");
printHeader("Welcome");
?>
	<h2>Current Project Sheets</h2>
    <script src="<?php echo pathToRoot(); ?>tablesorter.min.js" /></script>
    <link rel="stylesheet" href="<?php echo pathToRoot()."/"; ?>table_sorter_style.css" />
	

    <table class="tablesorter">
	<thead>
	<tr>
		<th class="th_projId leftCorner">Project Id</th>
        <th class="th_thumb">Thumb</th>
		<th class="th_projTitle">Project Title</th>
		<th class="th_projDesc">Project Description</th>
        <th class="th_projUrl">Project URL</th>
		<th class="th_projCreator">Creator</th>
		<th class="th_creationDate  <?php if(  $_SESSION['isAdministrator'] == 0 ){	echo " rightCorner"; }?>">Creation Date</th>
        <?php if(  $_SESSION['isAdministrator'] == 1 ){	?>
		<th class="th_adminTools rightCorner">Admin</th>
        <?php }	?>
	</tr>
	</thead>
	<tbody>
	<?php
		if(  $_SESSION['isAdministrator'] == 1 ){
			/*Ignore the Permissions*/
			$query = mysql_query("SELECT `thumb_id`,`project_sheets`.`id`, `username`, `title`, `description`, `creation_date` FROM  `project_sheets` 
			INNER JOIN `users` on `project_sheets`.`creator`=`users`.`id` 
			
			ORDER BY  `id` ASC"); /*INNER JOIN `project_sheet_permissions` on `project_sheet_permissions`.`user_id` = `users`.`id` */
		}else{
		    $query = mysql_query("SELECT `thumb_id`,`project_sheets`.`id`, `username`, `title`, `description`, `creation_date` FROM  `project_sheets` 
			INNER JOIN `users` on `project_sheets`.`creator`=`users`.`id` 
			INNER JOIN `project_sheet_permissions` on `project_sheet_permissions`.`user_id` = `users`.`id` 
			ORDER BY  `id` ASC");
		}
		$num_rows = mysql_num_rows($query);
		if($num_rows > 0 ){
			while($info = mysql_fetch_assoc( $query ))
			{
				echo "<tr>";
					echo "<td>".$info["id"]."</td>";
					echo "<td><a href='".pathToRoot()."viewProjectSheet.php?viewSheet=".$info["id"]."'><img width='120' height='90' src='".pathToRoot()."getFile.php?id=".$info["thumb_id"]."&type=application/octet-stream'/></a></td>";
					echo "<td><a class='projectTitleLink' href='".pathToRoot()."viewProjectSheet.php?viewSheet=".$info["id"]."'>".$info["title"]."</a></td>";
					echo "<td>".$info["description"]."</td>";
					echo "<td>". $_SERVER['HTTP_HOST']."viewProjectSheet.php?viewSheet=".$info["id"]."</td>";
					
					echo "<td>".$info["username"]."</td>";
					echo "<td>".$info["creation_date"]."</td>";
					if(  $_SESSION['isAdministrator'] == 1 ){	
						echo "<td> <a class='button buttonEdit' href='".pathToRoot()."viewProjectSheet.php?viewSheet=".$info["id"]."'>Edit</a>  
						<a class='button buttonDelete' projId='".$info["id"]."' href='#' class='deleteProject'>Delete</a>  
						<a class='button buttonPerms' href='".pathToRoot()."editPermissions.php?project=".$info["id"]."' class='permissionsProject'>Update Permissions</a>  
						<a class='button buttonProjectLog' href='".pathToRoot()."viewProjectSheetLogs.php?viewSheet=".$info["id"]."' class='viewProjectLog'>Project Log</a></td>"; 	}
				echo "</tr>";
			}
			mysql_free_result($query);
		}
	?>
    </tbody>
	</table>
    <script type="text/javascript">
		$(function(){
			$('.tablesorter').tablesorter();
			
			    <?php if(  $_SESSION['isAdministrator'] == 1 ){	?>
    
		/*Admin Scripts*/
		
			$('.buttonDelete').click(function(){
				var confirmDelete = confirm("Are you sure you wish to delete the project and all of it's notes?");
				if( confirmDelete == true){
					$(this).parent().parent().remove();
					var jsonObj = {deleteProject: $(this).attr('projId') }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData};
					$.ajax({
							type: 'POST',
							url: "_deleteProject.php",
							data: postArray,
							success: function(data){
								window.alert("Project Deleted Successfully");	
							}
					});
				}
			});
		
	
    <?php }	?>
			
			
			
			
		});
	</script>
    <?php if(  $_SESSION['isAdministrator'] == 1 ){ ?>
    	<p><a class='button buttonCreateProject' href="<?php echo pathToRoot()."createProjectSheet.php"; ?>">Create New Project</a> <a class='button buttonManageUsers' href="<?php echo pathToRoot()."editUsers.php"; ?>">Manage Users</a> <a class='button buttonCreateProject' href="<?php echo pathToRoot()."editCompanies.php"; ?>">Manage Companies</a>
        
        
        
        </p>
	<?php }	?>    
    
<?php
printFooter();
?>