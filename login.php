<?php
	include("includes.php");
	printHeader("Login!");
	$err = "";
	$non_err = "";
	$waitTimeMsg = "";
	$continueLogin = 1;
	$waitTime = 600;
	$contactEmail = "bren1818@gmail.com";
	
	if( isset($_SESSION['loginAttempts']) )
	{
		if( $_SESSION['loginAttempts'] > 5)
		{
			$session_life =  time() - $_SESSION['wtime'];
			if( $session_life < $waitTime)
			{
				$waitTimeMsg = "You must wait another: <b>".($waitTime - $session_life  )."</b> seconds before you're allowed to re-attempt a login.";	
				$continueLogin = 0;
			}else{
				$continueLogin = 1;
				$_SESSION['loginAttempts'] = 0;
			}
		}
	}
	
	if( isset($_POST['submit']) &&  $continueLogin == 1 )
	{
		if( $_POST['password'] == "" || $_POST['username'] == "")
		{
			$err ="Please ensure all fields are filled in.";
		}else{ 
			$password = sha1($_POST['password']);
			$username = preg_replace('/[^a-z0-9]+/', '', strtolower($_POST['username']));
			$query = mysql_query("SELECT `id`, `username`, `password` , `is_Admin`,`is_Deleted`, `last_ip`, `last_login`, `salt` FROM `users` WHERE `username` = \"".mysql_real_escape_string($username)."\" LIMIT 1");
			
			if( isset($_SESSION['loginAttempts']) )
			{
				$_SESSION['loginAttempts']++;
				if( $_SESSION['loginAttempts'] >=6)
				{
							$_SESSION['wtime'] = time();
							$waitTimeMsg = "You must wait another: <b>".$waitTime."</b> seconds before you're allowed to re-attempt a login.";
							$continueLogin = 0;	
							SystemLog(1, "A user attempted to login with the username: ".$username.", ".($_SESSION['loginAttempts'] -1)." times un-successfully.");
				}else{
					$continueLogin = 1;
				}
			}else{
				$_SESSION['loginAttempts'] = 1;
			}
			
			$num_rows = mysql_num_rows($query);
			if($num_rows > 0 )
			{
				
				while($info = mysql_fetch_assoc( $query ))
				{	
					if( md5( $info["salt"].$password ) == $info["password"] && $info["is_Deleted"] == 0)
					{
						
						$lastLogin =  getTimeStamp();
						 $_SESSION['userId'] = $info["id"];
						 $_SESSION['username'] = $info["username"];
						 $_SESSION['start_time'] = time();
						 $_SESSION['time'] = time();
						 $_SESSION['ip'] = getIP();
						 $_SESSION['last_ip'] =  $info["last_ip"];
						 $_SESSION['isAdministrator'] = $info["is_Admin"];
						 $_SESSION['last_login'] =  $info["last_login"];
						 $_SESSION['goBackto'] = "";
						 
						 $updateQ = mysql_query("UPDATE `users` SET `last_ip` = '". getIP()."' WHERE `id` = ".$info["id"]);
						 $updateQ = mysql_query("UPDATE `users` SET `last_login` = '".getTimeStamp()."' WHERE `id` = ".$info["id"]);
						 
						if( isset($_POST['redirectTo']) && $_POST['redirectTo'] != ""){
							header("Location: ".$_POST['redirectTo']);
						}else{
							header("Location: ".pathToRoot()."index.php?welcome=1"); 
						}
						 
						 
					}else{
						if( $info["is_Deleted"] == 1){
							$err = "Your account has been temporarily Banned / Deleted. Contact an administrator to be granted priveledges.";
						}else{
							$err = "Invalid username or password.";
						}
					}
				}
				
				mysql_free_result($query);
			}else{
				$err = "Invalid username or password";
			}//end pass check
		}//end num rows
		
		
	}//submit
	
	
	if( isset($_REQUEST['timeOut']) ){
		$err = "Your Session has expired and you have been logged out. Please log back in...";
		$_SESSION['loginAttempts'] = 1;
	}
	
	if( isset($_REQUEST['logout']) ){
		$non_err = "You have successfully been logged out.";
	}
	
	if( isset($_REQUEST['login']) ){
		$err = "Please login to access administration section.";
	}
	
	if( isset($_REQUEST['plzLogin']) ){
		if( isset($_SESSION['goBackto']) && !endsWith($_SESSION['goBackto'], "index.php") ){
			$non_err = "Please login to view that page.<br/>You will be re-directed after sign in";
		}
	}
	
	if ( $continueLogin == 1 )
	{
?>
	<form id="login" class="inputForm" method="post" name="loginForm" action="<?php echo pathToRoot()."/login.php" ?>" novalidate> <!--onsubmit="return validateForm()"-->
    
    
    
		<div class="row center">
        	<?php if( $err != ""){ echo "<p class='errorMessage'>".$err."</p>"; } ?>
            <?php if( $non_err != ""){ echo "<p class='nonErrorMessage'>".$non_err."</p>"; } ?>
			<?php if( isset($_SESSION['loginAttempts']) ){ echo "<span id='message'>Login Attempt <b>".$_SESSION['loginAttempts']."</b> of 5.</span>";  }  ?>
            <?php
			if( isset($_REQUEST['plzLogin']) && $_REQUEST['plzLogin'] ==1 ){
				echo '<p>Welcome to Notes, please login.</p>';
			}
			?>
		</div>
		<div class="row">
			<div class="titleCol">
				Username:
			</div>
			<div class="valueCol">
				<input type="text" name="username" value="<?php if(isset($_POST['username'])){ echo preg_replace('/[^a-z0-9]+/', '', strtolower($_POST['username'])); }?>" pattern="[a-zA-Z0-9]{5,}" maxlength="30" required="required"/>
			</div>
            <div class="clear" ></div>
		</div>
		
		<div class="row">
			<div class="titleCol">
				Password:
			</div>
			<div class="valueCol">
				<input type="password" name="password" value="" required="required"/>
			</div>
            <div class="clear" ></div>
		</div>

		<div class="row center">
			<input id="loginFormBtn" class="genericButton genericSubmitButton" name="submit" type="submit" value="Login"/>
            <?php
				if( isset($_SESSION['goBackto']) ){
					echo '<input type="hidden" name="redirectTo" value="'.$_SESSION['goBackto'].'"/>';
				}
            ?>
		</div>
	</form>
<?php
	}else{
		echo '<form id="login" class="inputForm" method="post" name="loginForm" novalidate>';
		echo "<p class='errorMessage'>You have attempted to login multiple times un-successfully. Your account has been temporarily banned.</p>";
		echo "<p>$waitTimeMsg</p>";
		echo "<span id='message'>Email: <a href='mailto:$contactEmail'>$contactEmail</a> if this problem persists.</span>";
		echo "</form>";
		$user = "";
		if(isset($_POST['username'])){ $user = preg_replace('/[^a-z0-9]+/', '', strtolower($_POST['username'])); }
		SystemLog(1, "User [".$user."] has tried to login multiple times with incorrect password. May be an intruder or a user who's forgotton their password.");
	}
	printFooter();
?>
