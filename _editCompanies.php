<?php
	include("includes.php");
	if(  $_SESSION['isAdministrator'] == 1  && isset($_POST["json"])){
		$json = stripslashes($_POST["json"]);
		$company = json_decode($json);
		
		$query = 0;
		
		if(  $company->{'action'} == "delete"){
			//echo "Delete ". $user->{'user'};
			$query = "DELETE FROM `users_company` WHERE `users_company`.`id` = ".$company->{'companyId'};
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
				SystemLog(1, "Company (name: ".$company->{'company'}.") has been deleted");
				
				//MUST UPDATE USERS
				
				//select users with company id of x set to 1 (no company)
				
				$query = "UPDATE `users` set `company_id` = '1' WHERE `users`.`company_id` = ".$company->{'companyId'};
				$query = mysql_query($query);
				if( $query){
					SystemLog(1, "Users origionally with company (name: ".$company->{'company'}.") have been updated to have 'No Company'");
				}else{
					SystemLog(1, "Users origionally with company (name: ".$company->{'company'}." - Company Id: ".$company->{'companyId'}.") could not be editted. This may have caused integrity issue. Please contact an admin");
				}
				
			}
			
		}else if ($company->{'action'} == "add"){	
			$query = "INSERT INTO `users_company` (`id` ,`company_name`)VALUES (NULL ,  '".$company->{'company'}."');";
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
				SystemLog(1, "Company (name: ".$company->{'company'}.") has been added");
			}
		}
		else if( $company->{'action'} == "rename" ){
			
			
			$query = "UPDATE  `users_company` SET  `company_name` =  '".$company->{'newCompanyName'}."' WHERE  `users_company`.`id` =".$company->{'companyId'};
			$query = mysql_query($query);
			if( $query ){
				echo "1";	
				SystemLog(1, "Company (name: ".$company->{'company'}.") has been renamed to: '".$company->{'newCompanyName'}."'");
			}else{
				echo "0";
				SystemLog(1, "Could NOT: rename ".$company->{'company'}." to: '".$company->{'newCompanyName'}."'");
			}
			
		}else{
			echo "0";	
			SystemLog(1, "Companies - Unknown Action : ".$company->{'action'}." - Failed gracefully");
		}
		
	}
?>