<?php
include("includes.php");
printHeader("Welcome");
if( isset($_REQUEST['viewSheet'])  )
{
	$id = $_REQUEST['viewSheet'];
	$allowed = 0;
	/*Check Permissions!*/
	if(  $_SESSION['isAdministrator'] == 1 ){ //you're an admin so you're allowed
			?>
             	<script src="<?php echo pathToRoot()."notes/"; ?>tablesorter.min.js" /></script>
    			<link rel="stylesheet" href="<?php echo pathToRoot()."notes/"; ?>table_sorter_style.css" />
                
                <div id="projectInfo">
                <?php
                	$projectInfo =  mysql_query("SELECT `creator`, `creation_date`, `description`, `title`, `thumb_id`, `users`.`username`
FROM `project_sheets`
INNER JOIN `users` on `users`.`id` =  `project_sheets`.`creator` WHERE `project_sheets`.`id` = ".$id);
					$num_rows = mysql_num_rows($projectInfo );
					if($num_rows > 0 ){
						while($info = mysql_fetch_assoc( $projectInfo ))
						{
							?>
                            
                            <div class="row">
                                <div class="titleCol">
                                    Project Title:
                                </div>
                                <div class="valueCol">
                                   <?php echo $info['title']; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            
                          <div class="row">
                                <div class="titleCol">
                                    Project Thumbnail:
                                </div>
                                <div class="valueCol">
                                   <?php echo '<img width="120" height="90" src="'.pathToRoot()."notes/getFile.php?id=".$info["thumb_id"].'&type=application/octet-stream"/>'; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            
                            
                           <div class="row">
                                <div class="titleCol">
                                    Project Description:
                                </div>
                                <div class="valueCol">
                                   <?php echo '<textarea disabled="disabled">'.$info['description'].'</textarea>'; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            
                            <div class="row">
                                <div class="titleCol">
                                    Project Creator:
                                </div>
                                <div class="valueCol">
                                   <?php echo $info['username']; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            
                           <div class="row">
                                <div class="titleCol">
                                    Creation Date:
                                </div>
                                <div class="valueCol">
                                   <?php echo $info['creation_date']; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            
                            <div class="row">
                                <div class="titleCol">
                                   View:
                                </div>
                                <div class="valueCol">
                                   <?php echo '<a href="'.pathToRoot()."notes/viewProjectSheet.php?viewSheet=".$id.'">"'.$info["title"].'"</a>'; ?>
                                </div>
                                <div class="clear" ></div>
                            </div>
                            <?php		 
									 
						}
					}
                ?>
                </div>
                <h2>Current - Project Layers</h2>
                <?php
					$projectLogs =  mysql_query("SELECT `layerId`, 	`project_sheet_id`, 	`layer_title`, 	`layer_color`, `username` FROM  `project_worksheet_layers`
						INNER JOIN `users` ON `users`.`id` = `owner_id`
						WHERE `project_sheet_id`= ".$id);
					$num_rows = mysql_num_rows($projectLogs );
					if($num_rows > 0 ){
						echo '<table class="tablesorter"><thead><tr>';
								echo "<th class='leftCorner'>Layer Title</th>";
								echo "<th>Layer ID</th>";
								echo "<th>Color</th>";
								echo "<th class='rightCorner'>Creator</th>";
							echo "</tr></thead><tbody>";
						while($info = mysql_fetch_assoc( $projectLogs ))
						{
							echo "<tr>";
								echo "<td>". $info['layer_title']."</td>";
								echo "<td><a name='layer_".$info['layerId']."' class='viewLayerInfo'>". $info['layerId']."</a></td>";	
								echo "<td><span style='background-color: ". $info['layer_color']."'>[Color]</span></td>";
								echo "<td>". $info['username']."</td>";
							echo "</tr>";
						}
						echo "</tbody></table>";
					}
				?>
                <h2>Current - Project Notes</h2>
                <?php
					
					
					$q = "SELECT `layer_title`, `username`, `note_id`, `project_layer_id`, `user_id`, `note_title`,  `note_content`, `timestamp`, `note_likes`, `note_dislikes`  FROM `project_notes` INNER JOIN `users` on `users`.`id` =  `project_notes`.`user_id` 
					INNER JOIN `project_worksheet_layers` on `project_worksheet_layers`.`layerId` =  `project_notes`.`project_layer_id`
					 WHERE `project_notes`.`project_sheet_id` = ".$id;
					$projectNotes =  mysql_query($q);
					
					
					$num_rows = mysql_num_rows($projectLogs );
					if($num_rows > 0 ){
						echo '<table class="tablesorter"><thead><tr>';
								echo "<th class='leftCorner'>Note ID</th>";
								//echo "<th>project_sheet_id</th>";
								echo "<th>Parent Layer</th>";
								echo "<th>Owner</th>";
								echo "<th>Title</th>";
								echo "<th>Content</th>";
								echo "<th>Timestamp</th>";
								echo "<th>Likes</th>";
								echo "<th class='rightCorner'>Dis-Likes</th>";
							echo "</tr></thead><tbody>";
						while($info = mysql_fetch_assoc( $projectNotes ))
						{
							echo "<tr>";
								echo "<td><a name='note_".$info['note_id']."' class='viewNoteInfo'>". $info['note_id']."</a></td>";
								//echo "<td>". $info['project_sheet_id']."</td>";	
								echo "<td>". $info['layer_title']."</td>";	
								echo "<td>". $info['username']."</td>";	
								echo "<td>". $info['note_title']."</td>";	
								echo "<td>". $info['note_content']."</td>";	
								echo "<td>". $info['timestamp']."</td>";	
								echo "<td>". $info['note_likes']."</td>";	
								echo "<td>". $info['note_dislikes']."</td>";	
							echo "</tr>";
						}
						echo "</tbody></table>";
					}
				?>
                
                
                
                
                
                <h2>Project Timeline</h2>
            <?php
			$projectLogs =  mysql_query("SELECT `action_id`, `LayerId`,`NoteId`,`action`,`timestamp`, `username` FROM `note_logs` INNER JOIN `users` on `users`.`id` = `note_logs`.`userId` WHERE `project_id`= ".$id);
			$num_rows = mysql_num_rows($projectLogs );
			if($num_rows > 0 ){
				echo '<table class="tablesorter"><thead><tr>';
						echo "<th class='leftCorner'>Action ID</th>";	
						//echo "<td>". $info['project_id']."</td>";
						echo "<th>Layer ID</th>";
						echo "<th>Note ID</th>";
						echo "<th>Action</th>";
						echo "<th>Time Stamp</th>";
						echo "<th class='rightCorner'>User</th>";
					echo "</tr></thead><tbody>";
				while($info = mysql_fetch_assoc( $projectLogs ))
				{
					echo "<tr>";
						echo "<td>". $info['action_id']."</td>";	
						echo "<td><a href='#layer_".$info['LayerId']."' class='viewLayerInfo'>". $info['LayerId']."</a></td>";
						echo "<td><a href='#note_".$info['NoteId']."' class='viewNoteInfo'>". $info['NoteId']."</a></td>";
						echo "<td>". $info['action']."</td>";
						echo "<td>". $info['timestamp']."</td>";
						echo "<td>". $info['username']."</td>";
					echo "</tr>";
				}
				echo "</tbody></table>";
				?>
				<script type="text/javascript">
                    $(function(){
                        $('.tablesorter').tablesorter();
                    });
                </script>
                <?php
			}	
	}else{
		echo "<p>Sorry you do not have permissions to see project logs.</p>";
	}
}
?>
	<p><a class="button buttonGoBack" href="<?php echo pathToRoot()."notes/index.php"; ?>">Go Back</a></p>
    
<?php
printFooter();
?>