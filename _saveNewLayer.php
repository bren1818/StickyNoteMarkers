<?php
	include("includes.php");
	if(  isset($_SESSION['userId'])  && isset($_POST["json"])){
		$json = stripslashes($_POST["json"]);
		$layer = json_decode($json);
		if( isset( $layer->{'deleteLayer'} ) ){
			$query = "DELETE FROM `project_worksheet_layers` WHERE `project_worksheet_layers`.`layerId` = ".$layer->{'deleteLayer'};
			mysql_query($query);
			UpdateLog( $layer->{'projectId'} , $layer->{'deleteLayer'}, "", "Layer Deleted", $_SESSION['userId']);
			echo "Deleted";
		}else if( isset( $layer->{'renameLayer'} ) ){
			$query = "UPDATE  `project_worksheet_layers` SET  `layer_title` =  '". mysql_real_escape_string($layer->{'layerName'})."' WHERE  `project_worksheet_layers`.`layerId` =".$layer->{'renameLayer'}.";";
			mysql_query($query);
			UpdateLog( $layer->{'projectId'} , $layer->{'renameLayer'}, "", "Layer Renamed to (".mysql_real_escape_string($layer->{'layerName'}).")", $_SESSION['userId']);
			
			echo "Renamed";
		}else{
			$query = "INSERT INTO `project_worksheet_layers` (`layerId`, `project_sheet_id`, `layer_title`, `layer_color`, `owner_id`) VALUES (NULL, '". mysql_real_escape_string($layer->{'projectId'})."', '". mysql_real_escape_string($layer->{'layerTitle'})."', '". mysql_real_escape_string($layer->{'layerColor'})."', '".$layer->{'creator'}."');";
			mysql_query($query);
			$newLayerId = mysql_insert_id();
			UpdateLog( $layer->{'projectId'}, $newLayerId, "", "Layer Created (".mysql_real_escape_string($layer->{'layerTitle'}).")", $_SESSION['userId']);
			echo $newLayerId;
		}
	}
?>