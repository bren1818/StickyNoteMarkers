<?php
include("includes.php");
if(  $_SESSION['isAdministrator'] == 1 ){
	 
	 printHeader("Edit Companies");
?>
	<h2>Current Companies</h2>
    <script src="<?php echo pathToRoot(); ?>tablesorter.min.js" /></script>
    <link rel="stylesheet" href="<?php echo pathToRoot(); ?>table_sorter_style.css" />
	 
    <table class="tablesorter" id="currentUsers">
    <thead>
    <tr>
        <th class="leftCorner">Company Name</th>
        <th class="rightCorner">Tools</th>
    </tr>
    </thead>
    <tbody> 
         <?php
         	$comps = "SELECT * FROM  `users_company` ";

 $comps  = mysql_query($comps );	
        $num_rows = mysql_num_rows($comps );		
        if($num_rows > 0 ){
            while($info = mysql_fetch_assoc( $comps  ))
            {
                echo '<tr>';

                    echo '<td>'.$info['company_name'].'</td><td> <a class="permDeleteCompany" companyId="'.$info['id'].'" href="#">Delete</a> | <a class="renameCompany" href="#" companyId="'.$info['id'].'">Rename Company</a> </td>';
                echo '</tr>';
            }
        }
         
		 
		 
		 
         ?>
    </tbody>
    </table>    
    <script type="text/javascript">
        $(function(){
            $('.tablesorter').tablesorter();
			$('#addCompany').click(function(){
				var companyName = prompt("Please Enter a new Company Name");
				while( !confirm("Are you sure you want to add the company: '" + companyName + "'") && companyName.length != 0 ){
					companyName = prompt("Please Enter a new Company Name");
				}
				
				var jsonObj = {company: companyName, action : 'add' }
				var postData = JSON.stringify(jsonObj);
				var postArray = { json:postData };
				
				$.ajax({
					type: 'POST',
					url: "_editCompanies.php",
					data: postArray,
					success: function(data){
				
						if( data == "1"){
							window.alert("Added " + companyName);
							window.location.reload(true);
						}
					}
				});
			});
			
			$('.permDeleteCompany').click(function(){
				var companyId = parseInt($(this).attr('companyId') );
				var companyName = $(this).parent().parent().find('td').first().html().toString();
				var jsonObj = {companyId: companyId, action : 'delete', company: companyName}
				var postData = JSON.stringify(jsonObj);
				var postArray = { json:postData };
				
				if( confirm("Are you sure you wish to delete: '" + companyName + "'?") ){
					$.ajax({
					type: 'POST',
					url: "_editCompanies.php",
					data: postArray,
					success: function(data){
				
						if( data == "1"){
							window.alert("Deleted: " + companyName);
							window.location.reload(true);
						}
					}
				});
					
				}
				
			});
			
			$('.renameCompany').click(function(){
				var companyId = parseInt($(this).attr('companyId') );
				var companyName = $(this).parent().parent().find('td').first().html().toString();
				var newCompanyName = prompt("Enter a new Company name for the company: " + companyName);
				
				while( !confirm("Are you sure you want to rename the company: '" + companyName + "' to: '" + newCompanyName + "'") && companyName.length != 0 ){
					newCompanyName = prompt("Enter a new Company name for the company: " + companyName);
				}
				
				
				
				var jsonObj = {companyId: companyId, action : 'rename', company: companyName, newCompanyName: newCompanyName }
				var postData = JSON.stringify(jsonObj);
				var postArray = { json:postData };
				
				
				$.ajax({
					type: 'POST',
					url: "_editCompanies.php",
					data: postArray,
					success: function(data){
				
						if( data == "1"){
							window.alert("Renamed: '" + companyName + "' to : '" + newCompanyName + "'");
							window.location.reload(true);
						}
					}
				});
				
				
				
				
			});
			
			
        });
    </script>
   <p><a id="addCompany" class="button buttonAddCompany" href="#">Add Company</a></p>
<?php	 
}else{
	printHeader("Sorry you must be an admin to edit Companies");
	echo '<p>Oops! You shouldn\'t be here, only an administrator can modify project permissions</p>';
}
	echo '<p><a class="button buttonGoBack" href="'.pathToRoot().'index.php">Go Back</a></p>';
	printFooter();
?>}