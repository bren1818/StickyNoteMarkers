<?php
include("includes.php");
if(  $_SESSION['isAdministrator'] == 1 ){
	 
	 printHeader("Edit Users");
	 
	 if (!empty($_POST)){
			if( isset( $_POST['numUsers'] ) && $_POST['numUsers'] > 0 ){
				echo "Updating User Perms";
				
				/*Delete User Permissions*/
				$remPerms = "UPDATE `users` SET `is_Admin` = 0";
				$remPerms = mysql_query($remPerms);
				$newPerms ="";
				for( $x = 0; $x < $_POST['numUsers']; $x++){
					if( isset($_POST['userPermission_'.$x]) ){
						/*Add User Permission*/
						$q = mysql_query("UPDATE  `users` SET  `is_Admin` =  '1' WHERE  `users`.`id` = ".$_POST['userPermission_'.$x].";");
					}
				}
		}
		
		if( isset( $_POST['submit_new_user']) ){
			$err = 0;
			if( ! isset($_POST['new_user_username']) ) {
				$err = 1;
			}else{
				$newUserName = $_POST['new_user_username'];
			}
			
			if( !isset($_POST['new_user_password']) ){
				$err = 1;
			}else{
				$newUserPass =  $_POST['new_user_password'];
			}
			
			if( isset($_POST['new_user_is_admin']) ){
				$newUserisAdmin =  1;
			}else{
				$newUserisAdmin =  0;
			}
			
			if( isset($_POST['new_user_company']) ){
				$new_user_company = $_POST['new_user_company'];
			}else{
				$new_user_company = 1;
			}
			
			if( isset($_POST['new_user_email'])  ){
				//validate the email!
				$new_user_email = $_POST['new_user_email'];
			}else{
				$err = 1;
			}
			
			
			
			
			
			
			//do checks!
			
			
			if( !$err && strlen($newUserName) >=4 && strlen($newUserPass) >=5 ){
				
				
				
				$newUserQuery = "INSERT INTO  `users` (
								`id` ,
								`username` , `company_id`, `email`,
								`is_Admin`
								)
								VALUES (
								NULL ,  '".$newUserName."', '".$new_user_company."', '".$new_user_email."', '".$newUserisAdmin."'
								);";
								
						//		echo $newUserQuery;
								
				$newUserQuery = mysql_query($newUserQuery );
				
				if( $newUserQuery ){
					$nId = mysql_insert_id(); 
				
					$newSalt = sha1(getTimeStamp().getIP().$nId);
					$newPass =  md5( $newSalt.sha1($newUserPass) );
					$query = "UPDATE `users` SET  `password` =  '".$newPass."', `salt` = '".$newSalt."' WHERE  `users`.`id` =".$nId.";";
					//echo $query;
					$query = mysql_query($query);
				}
				
            	if($newUserQuery  && $query){
					echo '<p>New User: '.$newUserName.' added successfully.</p>';
				}else{
					echo '<p class="error">Could not add new user. Please Try again</p>';
				}
				
			}else{
				echo '<p>There was a problem with adding the new user. Ensure username is 4 or mor characters and password is 5 or more characters.</p>';	
			}
		}	
	}	
	?>
	<script src="<?php echo pathToRoot(); ?>tablesorter.min.js" /></script>
	<link rel="stylesheet" href="<?php echo pathToRoot(); ?>table_sorter_style.css" />
	<h2>Edit Users</h2>
	<form name="UserPermissions"  id="userPermissions" method="post" action=""/>
        <table class="tablesorter" id="currentUsers">
        <thead>
        <tr>
            <th class="leftCorner">Username</th>
            <th>is Administrator</th>
            <th>Company</th>
            <th>Email</th>
            <th class="rightCorner">Tools</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $numUsers = 0;
            $users = "SELECT  `users`.`id` ,  `username` , `email`,  `is_Admin` ,  `company_name` 
FROM  `users` 
INNER JOIN  `users_company` ON  `users_company`.`id` =  `users`.`company_Id` 
WHERE  `is_DELETED` =0";
            $users  = mysql_query($users );	
            $num_rows = mysql_num_rows($users );		
            if($num_rows > 0 ){
                while($info = mysql_fetch_assoc( $users  ))
                {
					//if( $info['is_Deleted'] == 0 ){
						echo '<tr>';
							if( $info['is_Admin'] == 1){
								echo '<td>'.$info['username'].'</td><td><input class="isAdminCheck" type="checkbox" name="userPermission_'.$numUsers.'" value="'.$info['id'].'" checked="checked"/></td>';
							}else{
								echo '<td>'.$info['username'].'</td><td><input class="isAdminCheck" type="checkbox" name="userPermission_'.$numUsers.'" value="'.$info['id'].'"/></td>';
							}
							echo '<td>'.$info['company_name'].'</td><td>'.$info['email'].'</td><td> <a class="resetPass" userId="'.$info['id'].'" href="#">Reset Pass</a> | <a class="deleteUser" href="#" userId="'.$info['id'].'">Delete User</a> </td>'; //alert its better to just change the password  
						echo '</tr>';
						$numUsers++;
					//}
                }
            }
        ?>
        </tbody>
        </table>
		<input type="hidden" id="numUsers" name="numUsers" value="<?php echo $numUsers; ?>" />
		
		<input type="submit" value="Save Changes" />
	</form>
    
    
    <h2>Deleted Users</h2>
    <table class="tablesorter" id="deletedUsers">
    <thead>
    <tr>
        <th class="leftCorner">Username</th>
        <th>is Administrator</th>
        <th>Company</th>
        <th>Email</th>
        <th class="rightCorner">Tools</th>
    </tr>
    </thead>
    <tbody>
    <?php
        $numUsers = 0;
        $users = "SELECT `users`.`id`, `username`, `email`, `is_Admin`,  `company_name` 
FROM  `users` 
INNER JOIN  `users_company` ON  `users_company`.`id` =  `users`.`company_Id` WHERE `is_DELETED` = 1";
        $users  = mysql_query($users );	
        $num_rows = mysql_num_rows($users );		
        if($num_rows > 0 ){
            while($info = mysql_fetch_assoc( $users  ))
            {
                echo '<tr>';
                    if( $info['is_Admin'] == 1){
                        echo '<td>'.$info['username'].'</td><td>True</td>';
                    }else{
                        echo '<td>'.$info['username'].'</td><td>False</td>';
                    }
					echo '<td>'.$info['email'].'</td>';
                    echo '<td>'.$info['company_name'].'</td><td> <a class="permDeleteUser" userId="'.$info['id'].'" href="#">Permanentally Delete</a> | <a class="undeleteUser" href="#" userId="'.$info['id'].'">Un-Delete User</a> </td>';
                echo '</tr>';
            }
        }
    ?>
    </tbody>
    </table>

<br /><br/>
	<input id="addUser" type="button" value="Add New User" />
	<form id="newUserForm" name="newUser" method="post" action="" style="display: none;">
		<div class="row">
			<div class="titleCol">
				Username:
			</div>
			<div class="valueCol">
				<input type="text" name="new_user_username" value="" maxlength="30" required="required"/>
			</div>
		</div>
		
		<div class="row">
			<div class="titleCol">
				Password:
			</div>
			<div class="valueCol">
				<input type="password" name="new_user_password" value="" required="required"/>
			</div>
		</div>
		
        <div class="row">
			<div class="titleCol">
				Email:
			</div>
			<div class="valueCol">
				<input type="email" name="new_user_email" value="" required="required"/>
			</div>
		</div>
        
        <div class="row">
			<div class="titleCol">
				Company:
			</div>
			<div class="valueCol">
				<!--<input type="text" name="new_user_password" value="" required="required"/>-->
                <select name="new_user_company">
                <?php
                	$options = "SELECT * FROM  `users_company` ";
                	$options = mysql_query($options);
					if( $options ){
						$num_rows = mysql_num_rows($options);
						if($num_rows > 0 ){
							while($info = mysql_fetch_assoc( $options ))
							{
								echo "<option value=".$info['id'].">".$info['company_name']."</option>";
							}
						}
					}
                ?>
                </select>
			</div>
		</div>
        
        
        
        
	   <div class="row">
			<div class="titleCol">
				User is Administrator:
			</div>
			<div class="valueCol">
				<input type="checkbox" name="new_user_is_admin" value="1" />
			</div>
		</div>
		<div class="row center">
		<input name="submit_new_user" type="submit" value="Add the new user"/>
		</div>
	</form>
	
	<script type="text/javascript">
		$(function(){
			$('.tablesorter').tablesorter();
			
			$('#addUser').click(function(){
				$('#newUserForm').show();
			});
			
			function reBindClicks(){
				$('.undeleteUser, .deleteUser, .resetPass, .permDeleteUser').each(function(){
					$(this).unbind('click');
				});
				 bindClicks();
			}
			
			function bindClicks()
			{
			$('.undeleteUser').click(function(){
				var userId = $(this).attr('userId');	
				var jsonObj = {user: userId, action : 'undelete' }
				var postData = JSON.stringify(jsonObj);
				var postArray = { json:postData };
				var row = $(this).parent().parent();
				$.ajax({
					type: 'POST',
					url: "_editUser.php",
					data: postArray,
					success: function(data){
						if( data == 1 ){
							$(row).find('.permDeleteUser').addClass('resetPass').removeClass('permDeleteUser').html("Reset Pass");
							$(row).find('.undeleteUser').addClass('deleteUser').removeClass('undeleteUser').html("Delete User");
							//$("#numUsers").attr('value', parseInt( $("#numUsers").attr('value') + 1) );
							var numUsers = parseInt($("#numUsers").attr('value') );
							numUsers = numUsers + 1;
							$("#numUsers").attr('value', numUsers);
							
							if( $(row).find('td:eq(1)').html() == "True"){
								$(row).find('td:eq(1)').html('<input class="isAdminCheck" type="checkbox" name="userPermission_' + numUsers + '" value="1" checked="checked"/>');
							}else{
								$(row).find('td:eq(1)').html('<input class="isAdminCheck" type="checkbox" name="userPermission_' + numUsers + '" value="1" />');
							}
							
							
							$(row).detach().appendTo($('#currentUsers') );
							
							reBindClicks();
							window.alert("User Restored");
						}
					}
				});
				
			});
			
			$('.deleteUser').click(function(){
				var userId = $(this).attr('userId');	
				var jsonObj = {user: userId, action : 'delete' }
				var postData = JSON.stringify(jsonObj);
				var postArray = { json:postData };
				var row = $(this).parent().parent();
				$.ajax({
					type: 'POST',
					url: "_editUser.php",
					data: postArray,
					success: function(data){
						if( data == 1 ){
							$(row).find('.resetPass').addClass('permDeleteUser').removeClass('resetPass').html("Permanentally Delete");
							$(row).find('.deleteUser').addClass('undeleteUser').removeClass('deleteUser').html("Un-Delete User");
							if( $(row).find('.isAdminCheck').attr('checked') == "checked"){
								$(row).find('.isAdminCheck').replaceWith("True");
							}else{
								$(row).find('.isAdminCheck').replaceWith("False");
							}
							$(row).detach().appendTo($('#deletedUsers') );
							
							var numUsers = parseInt($("#numUsers").attr('value') );
							numUsers = numUsers - 1;
							$("#numUsers").attr('value', numUsers);
							
							reBindClicks();
							window.alert("User Deleted");
						}
					}
				});
				
			});
			
			
			$('.permDeleteUser').click(function(){
				
				var row = $(this).parent().parent();
				
				if( confirm("Are You sure you wish to Delete user: '" +  $(row).find('td:eq(0)').html() + "'? This action CANNOT be un-done.") == 1){
					var userId = $(this).attr('userId');	
					var jsonObj = {user: userId, action : 'permdelete' }
					var postData = JSON.stringify(jsonObj);
					var postArray = { json:postData };
					var row = $(this).parent().parent();
					$.ajax({
						type: 'POST',
						url: "_editUser.php",
						data: postArray,
						success: function(data){
							if( data == 1 ){
								$(row).remove();
								window.alert("User Permanentally Deleted");
							}
						}
					});
				}
			});
			
			$('.resetPass').click(function(){
				var userId = $(this).attr('userId');
				var newPassword = prompt("Please enter a new password for the user","");
				if (newPassword!=null && newPassword!=""){
					
					var jsonObj = {user: userId, action : 'resetPass', newPass : newPassword }
					var postData = JSON.stringify(jsonObj);
					var postArray = { json:postData };
					var row = $(this).parent().parent();
					$.ajax({
						type: 'POST',
						url: "_editUser.php",
						data: postArray,
						success: function(data){
							if( data == 1 ){
								window.alert("Password updated");
							}
						}
					});	
					
				}else{
					window.alert("No new password entered. Cancelled");
				}			
			});
			
			}
			
			bindClicks();
			
		});
	</script>
	<?php

}else{
	printHeader("Sorry you must be an admin to edit Users");
	echo '<p>Oops! You shouldn\'t be here, only an administrator can modify project permissions</p>';
}
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'notes/index.php">Go Back</a></p>';
	printFooter();
?>