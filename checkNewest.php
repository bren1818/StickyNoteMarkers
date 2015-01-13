<?php
	require_once("includes.php");	
	
	function getNewLayersCount($newestLayerId, $projectId){
		
		$layersCountQuery = mysql_query("SELECT COUNT(`layerId`) 
			FROM  `project_worksheet_layers` 
			WHERE  `layerId` > ".$newestLayerId." &&  `project_sheet_id` =".$projectId);
		if ($layersCountQuery) {
			$layersCountQuery = mysql_fetch_row($layersCountQuery);
			$layersCountQuery = $layersCountQuery[0];
		}else{
			$layersCountQuery = 0;
		}
		
		return $layersCountQuery;
	}
	
	function getLayersCount($projectId){
		$layersCountQuery = mysql_query("SELECT COUNT(`layerId`) 
			FROM  `project_worksheet_layers` 
			WHERE `project_sheet_id` =".$projectId);
		if ($layersCountQuery) {
			$layersCountQuery = mysql_fetch_row($layersCountQuery);
			$layersCountQuery = $layersCountQuery[0];
		}else{
			$layersCountQuery = 0;
		}
		
		return $layersCountQuery;
	}
	
	
	function getNewNotesCount($newestNoteId, $projectId){
		$notesCountQuery = mysql_query("SELECT COUNT(`note_id`) 
			FROM  `project_worksheet_layers` 
			WHERE  `note_id` > ".$newestNoteId." &&  `project_sheet_id` =".$projectId);
		if ($notesCountQuery) {
			$notesCountQuery = mysql_fetch_row($notesCountQuery);
			$notesCountQuery = $notesCountQuery[0];
		}else{
			$notesCountQuery = 0;
		}
			
			
		return $notesCountQuery;
	}
	
	function getNoteUpdatesCount($timeStamp, $projectId){
		$notesUpdateCountQuery = mysql_query("SELECT COUNT(`note_id`) 
			FROM  `project_notes` 
			WHERE  `timestamp` > '".$timeStamp."' &&  `project_sheet_id` =".$projectId);
			
		if ($notesUpdateCountQuery) {
			$notesUpdateCountQuery = mysql_fetch_row($notesUpdateCountQuery);
			$notesUpdateCountQuery = $notesUpdateCountQuery[0];
		}else{
			$notesUpdateCountQuery = 0;
		}
		return $notesUpdateCountQuery;
	}
	
	function getNumNotes($projectId){
		$notesCountQuery = mysql_query("SELECT COUNT(`note_id`) 
			FROM  `project_notes` 
			WHERE  `project_sheet_id` =".$projectId);
		if ($notesCountQuery) {
			$notesCountQuery = mysql_fetch_row($notesCountQuery);
			$notesCountQuery = $notesCountQuery[0];
		}else{
			$notesCountQuery = 0;
		}
		return $notesCountQuery;
	}
	
	function getNewestLayerId($projectId){
		$NewestLayerId = mysql_query("SELECT MAX(`layerId`) FROM `project_worksheet_layers` WHERE  `project_sheet_id` =".$projectId." LIMIT 1");
		if ($NewestLayerId) {
			$NewestLayerId = mysql_fetch_row($NewestLayerId);
			$NewestLayerId = $NewestLayerId[0];
		}else{
			$NewestLayerId = 0;
		}
		return $NewestLayerId;
	}
	
	
	function getNewestNoteId($projectId){
			/*Newest */
		$NewestNoteId = mysql_query("SELECT MAX(`note_id`) FROM `project_notes` WHERE  `project_sheet_id` =".$projectId." LIMIT 1");
		if( mysql_num_rows($NewestNoteId) != 0 ){
			if ($NewestNoteId ) {
				$NewestNoteId = mysql_fetch_row($NewestNoteId);
				$NewestNoteId = $NewestNoteId[0];
			}else{
				$NewestNoteId = 0;
			}
		}else{
			$NewestNoteId = 0;
		}
		
		if(  $NewestNoteId == ""){
			 $NewestNoteId = 0;
		}
		
		return $NewestNoteId;
	}
	
	
	function getNewestNoteTimeStamp($projectId){
		/*Newest Timestamp*/
		$NewestTimeStampQuery =  mysql_query("SELECT MAX(`timestamp`) FROM  `project_notes` WHERE  `project_sheet_id` =".$projectId." LIMIT 1"); 
		if ($NewestTimeStampQuery) {
			$NewestTimeStampQuery = mysql_fetch_row($NewestTimeStampQuery);
			$NewestTimeStampQuery = $NewestTimeStampQuery[0];
		}else{
			$NewestTimeStampQuery =	0;
		}
		return $NewestTimeStampQuery;
	}
	
	
	
	if( isset($_POST["json"]) )
	{
		$json = stripslashes($_POST["json"]);
		$proj = json_decode($json);
		$id = $proj->{'projectId'};
		
		if( isset( $proj->{'returnNewLayers'} ) ){
			//echo "I should spit out ".getNewLayersCount($proj->{'layerToStart'}, $id);
			
			$layerQuery = mysql_query("SELECT * 
			FROM  `project_worksheet_layers` 
			WHERE  `layerId` > ".$proj->{'layerToStart'}." &&  `project_sheet_id` =".$id);
			//$num_rows = mysql_num_rows($layerQuery);
			$layers = array();
			$count = 0;
			while($Linfo = mysql_fetch_assoc( $layerQuery ) )
			{
					$layers[$count] = $Linfo;
					$count++;
			}
			echo json_encode($layers);
		}else if(isset( $proj->{'returnAllLayerIds'} )){
			
			$layerQuery = mysql_query("SELECT `layerId` 
			FROM  `project_worksheet_layers` 
			WHERE `project_sheet_id` =".$id);
			$layers = array();
			$count = 0;
			while($Linfo = mysql_fetch_assoc( $layerQuery ) )
			{
					$layers[$count] = $Linfo;
					$count++;
			}
			echo json_encode($layers);
			
		}else if( isset( $proj->{'returnNewNotes'} ) ){
			
			//$newNotes = mysql_query("SELECT * FROM `project_notes` WHERE `timestamp` > '".$proj->{'since'}."' && `project_sheet_id` =".$id);	
			
			$q = "SELECT `users`.`username`, `note_id`, `project_sheet_id`, `project_layer_id`, `user_id`, `note_title`, `note_content`, `note_x`, `note_y`, `timestamp`, `note_likes`, `note_dislikes`  FROM `project_notes` INNER JOIN `users` ON `project_notes`.`user_id` = `users`.`id` WHERE `timestamp` > \"".$proj->{'since'}."\" && `project_sheet_id` =".$id;
			
			//echo $q;
			
			$newNotes = mysql_query($q);
			
			/*$newNotes = mysql_query("SELECT `username`, `note_id`, `project_sheet_id`, `project_layer_id` `user_id`, `note_title`, `note_content`, `note_x`, `note_y`, `timestamp` FROM `project_notes` 
			INNER JOIN `users` on  `users`.`id` = `project_notes`.`user_id`
			WHERE  `project_notes`.`timestamp` > '".$proj->{'since'}."' && `project_sheet_id` =".$id);	
			*/
			
			$notes = array();
			$count = 0;
			while($Ninfo = mysql_fetch_assoc( $newNotes ) )
			{
					$notes[$count] = $Ninfo;
					$count++;
			}
			echo json_encode($notes);
			
			//echo "Yep";
			
		}else if( isset( $proj->{'returnAllExitingNotes'}) ){
			
			$newNotes = mysql_query("SELECT `note_id` FROM `project_notes` WHERE `project_sheet_id` =".$id);	
			
			$notes = array();
			$count = 0;
			while($Ninfo = mysql_fetch_assoc( $newNotes ) )
			{
					$notes[$count] = $Ninfo;
					$count++;
			}
			echo json_encode($notes);
			
			
		}else{
		
		
			$nlid = $proj->{'newestLayer'};
			$nn = $proj->{'newestNote'};
			$nts = $proj->{'newestTimeStamp'};
			$numNotes = $proj->{'numNotes'};
		
			$json = array('numNewNotes' => getNewNotesCount($nn, $id), 'numNewLayers' => getNewLayersCount($nlid, $id), 'numNoteUpdates' => getNoteUpdatesCount($nts, $id), 'numNotes' => getNumNotes($id), 'numLayers'=> getLayersCount($id), 'newestLayerId' => getNewestLayerId($id)  );
		
			echo json_encode($json);
		}
	}
?>