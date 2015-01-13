<?php
	include("includes.php");
	printHeader("Notifications");
?>
	<script src="<?php echo pathToRoot()."notes/"; ?>tablesorter.min.js" /></script>
	<link rel="stylesheet" href="<?php echo pathToRoot()."notes/"; ?>table_sorter_style.css" />
	<script type="text/javascript">
    
	function jsObject(params)
	{
		var self = this;
		
		if ( params != undefined){
			console.log(params);
		}
		
		
		return this;
	}
	
	jsObject.prototype = {	
	
	}
	
	
	
	var theObject = new jsObject({"id":"integer","name":"string"});
	
    </script>
    
    
    
    
    
	<?php
		/*if(  $_SESSION['isAdministrator'] == 1 ){ //you're an admin so you're allowed
        }else{
            echo "<p>Sorry you do not have permissions to see project logs.</p>";
        }*/
    ?>
	<p><a class="button buttonGoBack" href="<?php echo pathToRoot()."notes/index.php"; ?>">Go Back</a></p>
<?php
	printFooter();
?>