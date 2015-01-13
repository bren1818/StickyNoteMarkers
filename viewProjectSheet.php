<?php
include("includes.php");
if( isset($_REQUEST['viewSheet'])  )
{
	$id = $_REQUEST['viewSheet'];
	$allowed = 0;
	
	/*Check Permissions!*/
	if(  $_SESSION['isAdministrator'] == 1 ){ //you're an admin so you're allowed
			$allowed = 1;
	}else{
		$query = "SELECT * FROM `project_sheet_permissions` WHERE `project_id` =".$id." && `user_id` =".$userId.";";
		$query = mysql_query($query);
		$num_rows = mysql_num_rows($query);
		if($num_rows == 1 ){
			$allowed = 1;
		}
	}
	
	if( $allowed == 1 ){
	
	
	$query =  mysql_query("SELECT `username`, `creation_date`, `title`, `description`, `image_id`, `height`, `width` 
	FROM `project_sheets` 
	INNER JOIN `users` on `project_sheets`.`creator`=`users`.`id` 
	INNER JOIN `project_sheet_images` on `project_sheets`.`image_id`=`project_sheet_images`.`id`
	WHERE `project_sheets`.`id` = ".$id." LIMIT 1");
	
	$num_rows = mysql_num_rows($query);
	if($num_rows > 0 ){
		while($info = mysql_fetch_assoc( $query ))
		{
			$creationDate = $info['creation_date'];
			$title = $info['title'];
			$description = $info['description'];
			$imageId = $info['image_id'];
			$owner = $info['username'];
			$height = $info['height'];
			$width = $info['width'];
		}
		printHeader("Viewing Project Sheet - ".$title);
		?>
        	
            <div id="project_Info">
                <div class="row">
                    <div class="titleCol">
                        Project Title:
                    </div>
                    <div class="valueCol">
                        <?php echo $title; ?>
                    </div>
                    <div class="clear" ></div>
                </div>
                
                <div class="row">
                    <div class="titleCol">
                        Description:
                    </div>
                    <div class="valueCol">
                        <textarea disabled="disabled"><?php echo $description; ?></textarea>
                    </div>
                    <div class="clear" ></div>
                </div>       
                
                <div class="row">
                    <div class="titleCol">
                        Owner:
                    </div>
                    <div class="valueCol">
                        <?php echo $owner; ?>
                    </div>
                    <div class="clear" ></div>
                </div>     
                
                <div class="row">
                    <div class="titleCol">
                        Creation Date:
                    </div>
                    <div class="valueCol">
                        <?php echo $creationDate; ?>
                    </div>
                    <div class="clear" ></div>
                </div>    
           
            
            <?php
            	if(  $_SESSION['isAdministrator'] == 1 ){ 
			?>
                 <div class="row">
                    <div class="titleCol">
                        Project Permissions:
                    </div>
                    <div class="valueCol">
                      
                        <?php
                        	$numUsersWithAccess =  mysql_query("SELECT COUNT(`user_id`) FROM `project_sheet_permissions` WHERE `project_id`=".$id);
							if( $numUsersWithAccess ){
								while($row = mysql_fetch_array($numUsersWithAccess )){
									echo "  Users with access: <b>".$row['COUNT(`user_id`)']."</b>";
								}
							}
                        ?>
                        <a class='button buttonPerms' style="float: right;" href='<?php echo pathToRoot()."editPermissions.php?project=".$id; ?>'>Change Permissions</a>
                       
                    </div>
                    <div class="clear" ></div>
                </div>   
                
                <div class="row">
                    <div class="titleCol">
                		Initiate Next Stage
                 	</div>
                	<div class="valueCol">
                    	<a href="<?php echo pathToRoot()."nextStage.php?oldProject=".$id; ?>">Begin Next Stage...</a>
                    </div>
                    <div class="clear" ></div>
                </div>  
                
            <?php
				}
			?>
            </div>
            
            <script type="text/javascript">
			$(function(){
				
				function ScreenConsoleLog(message){
					$('#ScreenConsole').val( message + '\n' + $('#ScreenConsole').val() );
				}
				
				/*******************************************************/
				var captured = null;
				var highestZ = 0;
				var highestId = 0;
				var myNotes = new Array();
				var count = 0;
				var height = <?php echo $height; ?>;
				var width = <?php echo $width; ?>;
				<?php include("checkNewest.php"); ?>
				var NewestNoteTimeStamp = "<?php echo getNewestNoteTimeStamp($id); ?>";
				var NewestNoteId = <?php echo getNewestNoteId($id); ?>;
				var NewestLayerId = <?php echo getNewestLayerId($id); ?>;
				var numLayersCount =  <?php echo getLayersCount($id); ?>;
				var myId = <?php echo $userId; ?>;
				/*************************************************/
				
				function Note()
				{
					var self = this;
				
					var note = document.createElement('div');
					note.className = 'note';
					note.style.width = 200;
					note.addEventListener('mousedown', function(e) { return self.onMouseDown(e) }, false);
					note.addEventListener('click', function() { return self.onNoteClick() }, false);
					this.note = note;
				
					var close = document.createElement('div');
					close.className = 'closebutton';
					close.addEventListener('click', function(event) { return self.close(event) }, false);
					note.appendChild(close);
					
					var minimize = document.createElement('div');
					minimize.className = 'minimizebutton';
					minimize.addEventListener('click', function(event) { return self.minimize(event) }, false);
					note.appendChild(minimize);
					
					var likes = document.createElement('div');
					likes.className = 'likeebutton';
					likes.addEventListener('click', function(event) { return self.likes(event) }, false);
					note.appendChild(likes);
					
					var dislikes = document.createElement('div');
					dislikes.className = 'dislikeebutton';
					dislikes.addEventListener('click', function(event) { return self.dislikes(event) }, false);
					note.appendChild(dislikes);
					
					var saveThisNote  = document.createElement('div');
					saveThisNote.className = 'saveIcon';
					saveThisNote.addEventListener('click', function(event) { return self.saveThis(event) }, false);
					note.appendChild(saveThisNote);
					
					var title = document.createElement('div');
					title.className = 'title';
					title.setAttribute('contenteditable', true); //instead of text area?
					title.addEventListener('keyup', function() { return self.onKeyUp() }, false);
					note.appendChild(title);
					this.titleField = title;
				
					var edit = document.createElement('div');
					edit.className = 'edit';
					edit.setAttribute('contenteditable', true); //instead of text area?
					edit.addEventListener('keyup', function() { return self.onKeyUp() }, false);
					note.appendChild(edit);
					this.editField = edit;
				
					var author = document.createElement('div');
					author.className = 'author';
					author.setAttribute('contenteditable', false); 
					author.innerHTML = "Author: <?php echo $_SESSION['username']; ?>";
					note.appendChild(author);
					this.authorField = author;
					
					var likeCount = document.createElement('div');
					likeCount.className = 'likeCount';
					likeCount.setAttribute('contenteditable', false); 
					likeCount.innerHTML = "0";
					note.appendChild(likeCount);
					this.likesCount = likeCount;
					
					var dislikeCount = document.createElement('div');
					dislikeCount.className = 'dislikeCount';
					dislikeCount.setAttribute('contenteditable', false); 
					dislikeCount.innerHTML = "0";
					note.appendChild(dislikeCount);
					this.dislikesCount = dislikeCount;
				
					var ts = document.createElement('div');
					ts.className = 'timestamp';
					ts.addEventListener('mousedown', function(e) { return self.onMouseDown(e) }, false);
					note.appendChild(ts);
					this.lastModified = ts;
					
					var deleted = 0;
					this.deleted = deleted;
					
					var layerId = $('.activeNoteLayer input.layerId').attr('value').toString();
					this.layerId = layerId;
					
					var authorId = "<?php echo $_SESSION['userId']; ?>";
					this.authorId = authorId;
					
					note.style.backgroundColor = $('.activeNoteLayer .layerColor').attr('value').toString();
					
					var dirty = 1;
					this.dirty = dirty;
					
					var id = -1;
					this.id = id;
					
					$('.activeNoteLayer').append(note);
					
					return this;
				}
				
				Note.prototype = {	
					
					get id()
					{
						return this.note.id;
					},
				
					set id(x)
					{
						$(this.note).attr('id', x );
						this.note.id = x;
					},
				
					get text()
					{
						return this.editField.innerHTML;
					},
				
					set text(x)
					{
						this.editField.innerHTML = x;
					},
					
					get title()
					{
						return this.titleField.innerHTML;
					},
				
					set title(x)
					{
						this.titleField.innerHTML = x;
					},
				
					get author()
					{
						return this.authorField.innerHTML;
					},
				
					set author(x)
					{
						this.authorField.innerHTML = "Author: " + x;
					},
					
					set likeCount(x){
						this.likesCount.innerHTML =  x;
					},
					
					set dislikeCount(x){
						this.dislikesCount.innerHTML =  x;
					},
					
					get authorId()
					{
						return  this.note.authorId;
					},
				
					set authorId(x)
					{
						this.note.authorId = x;
					},
					
					set layerId(x){
						$(this.note).detach().appendTo($('#notes_layer_' + x));
						$(this.note).css('background-color', $('#notes_layer_' + x + ' .layerColor').attr('value') );
						this.note.layerId = x;
					},
					
					get layerId(){
						return this.note.layerId;	
					},
					
					get timestamp()
					{
						if (!("_timestamp" in this))
							this._timestamp = 0;
						return this._timestamp;
					},
				
					set timestamp(x)
					{
						if (this._timestamp == x)
							return;
				
						this._timestamp = x;
						var date = new Date();
						//date.setTime(parseFloat(x));
						this.lastModified.textContent = modifiedString(date);
					},
				
					set color(x){
						this.note.style.backgroundColor = x;
					},
				
					set deleted(x){
						this.note.deleted = x;
						this.dirty = 1;
					},
					get deleted(){
						return this.note.deleted;	
					},
					
					set dirty(x){
						this.note.dirty = x;	
					},
					get dirty(){
						return this.note.dirty;	
					},
				
					get left()
					{
						return this.note.style.left;
					},
				
					set left(x)
					{
						this.note.style.left = x + 'px';
					},
				
					get top()
					{
						return this.note.style.top;
					},
				
					set top(x)
					{
						this.note.style.top = x +'px';
					},
				
					get zIndex()
					{
						return this.note.style.zIndex;
					},
				
					set zIndex(x)
					{
						this.note.style.zIndex = x;
					},
				
					close: function(event)
					{
						var note = this;
						//window.alert(myId + " - " + this.authorId );
						if( myId == this.authorId){
							var duration = event.shiftKey ? 2 : .25;
							this.note.style.webkitTransition = '-webkit-transform ' + duration + 's ease-in, opacity ' + duration + 's ease-in';
							this.note.offsetTop; // Force style recalc
							this.note.style.webkitTransformOrigin = "0 0";
							this.note.style.webkitTransform = 'skew(30deg, 0deg) scale(0)';
							this.note.style.opacity = '0';
							this.note.deleted = 1;
							$('#' + this.id).remove(); 
						}else{
							window.alert("You cannot delete a note you do not own.");
						}
						
						var self = this;
						
					},
					
					minimize: function(event)
					{
						var note = this;
						if( $('#' + this.id).hasClass('pinned') ){
							$('#' + this.id).removeClass('pinned');
						}else{
							$('#' + this.id).addClass('pinned');
						}
						var self = this;
					},
							
					likes: function(event)
					{
						var note = this;
						
						if( myId != this.authorId){
						
							if( this.id == -1){
								window.alert("Please Save your own note, before liking your own note...");
							}else{
								var jsonObj = {noteId: this.id,  noteLayerId: this.layerId, likes: 1, projectId: <?php echo $id ?> }//, userID: myId
								var postData = JSON.stringify(jsonObj);
								var postArray = { json:postData };
								$.ajax({
									type: 'POST',
									url: "_noteLikes.php",
									data: postArray,
									success: function(data){
										if( data == 1 ){
											window.alert("Like has been recorded. Thank you for your feedback");
										}
									}
								});
								this.likesCount.innerHTML = (parseInt(this.likesCount.innerHTML) + 1);
							}
						}else{
							window.alert("You cannot like your own note.");
						}
						
						var self = this;
					},
					
					dislikes: function(event)
					{
						var note = this;
						
						if( myId != this.authorId){
							if( this.id == -1){
								window.alert("Please Save your own note, before dis-liking your own note...");
							}else{
								var jsonObj = {noteId: this.id, noteLayerId: this.layerId, dislikes: 1, projectId: <?php echo $id ?> } //, userID: myId
								var postData = JSON.stringify(jsonObj);
								var postArray = { json:postData };
								$.ajax({
									type: 'POST',
									url: "_noteLikes.php",
									data: postArray,
									success: function(data){
										if( data == 1 ){
											window.alert("Dis-like has been recorded. Thank you for your feedback");
										}
									}
								});
								this.dislikesCount.innerHTML = (parseInt( this.dislikesCount.innerHTML ) + 1);
							}
						}else{
							window.alert("You cannot dis-like your own note.");	
						}
						var self = this;
					},
					
					save: function( x )
					{
						<?php if( $_SESSION['isAdministrator'] != 1) {?>
						if( myId == this.authorId){
						<?php } ?>
						var jsonObj = {noteId: this.id, noteProjectId : <?php echo $id; ?>, noteLayerId: this.layerId, noteAuthor: this.authorId, noteTitle: this.title, noteText: this.text, noteX: this.left, noteY: this.top, noteTimeStamp : this.timestamp, noteDeleted: this.deleted }
						
						var postData = JSON.stringify(jsonObj);
						var postArray = { json:postData };
						$.ajax({
							type: 'POST',
							url: "_saveNewNote.php",
							data: postArray,
							success: function(data){
								if( data == 1 ){
									//success
								}else{
									note.id = data;
									NewestNoteId = parseInt(data); //update the record.
								}	
							}
						});
						
						<?php if( $_SESSION['isAdministrator'] != 1) {?>
						}else{
							ScreenConsoleLog("Cannot modify a note you don't own");	
						}
						<?php } ?>
						var note = this;
					},
				
					saveThis: function(event){
						var note = this;
						
						note.save( this.id );
						
						var self = this;
					},
				
				
					onMouseDown: function(e)
					{
						captured = this;
						this.startX = e.clientX - this.note.offsetLeft;
						this.startY = e.clientY - this.note.offsetTop;
						//this.zIndex = ++highestZ;
						/*****************************************************************************************Make Active Note? Highest Z?*/
				
						var self = this;
						if (!("mouseMoveHandler" in this)) {
							this.mouseMoveHandler = function(e) { return self.onMouseMove(e) }
							this.mouseUpHandler = function(e) { return self.onMouseUp(e) }
						}
				
						document.addEventListener('mousemove', this.mouseMoveHandler, true);
						document.addEventListener('mouseup', this.mouseUpHandler, true);
				
						return false;
					},
				
					onMouseMove: function(e)
					{
						if (this != captured)
							return true;
						this.left = e.clientX - this.startX ;
						this.top = e.clientY - this.startY ;
						return false;
					},
				
					onMouseUp: function(e)
					{
						document.removeEventListener('mousemove', this.mouseMoveHandler, true);
						document.removeEventListener('mouseup', this.mouseUpHandler, true);
						this.dirty = 1;
						
						// do update of x and y
						return false;
					},
				
					onNoteClick: function(e)
					{
						//this.timestamp = new Date().getTime();
					},
				
					onKeyUp: function()
					{
						this.dirty = 1;
						  this.timestamp = new Date().getTime();
					},
				}
				function loaded()
				{
					<?php
					/*Create the Note load script*/
					$workSheetLayersQuery = "SELECT * FROM `project_worksheet_layers` WHERE `project_sheet_id` = ".$id;
					$workSheetLayersQuery = mysql_query($workSheetLayersQuery);
					$num_rows = mysql_num_rows($workSheetLayersQuery);
					if($num_rows > 0 ){
						$count = 0;
						$layerSelectOptions = "";
						while($Linfo = mysql_fetch_assoc( $workSheetLayersQuery ))
						{
								$workSheetLayersStickiesQuery = "SELECT * FROM `project_notes` INNER JOIN `users` on `project_notes`.`user_id`=`users`.`id` WHERE `project_sheet_id` = ".$id." and `project_layer_id` = ".$Linfo['layerId'];
								$workSheetLayersStickiesQuery = mysql_query($workSheetLayersStickiesQuery);
								$num_notes = mysql_num_rows($workSheetLayersStickiesQuery);
								if($num_notes > 0 )
								{
									while($Ninfo = mysql_fetch_assoc( $workSheetLayersStickiesQuery ))
									{
										echo 'myNotes['.$count.'] = new Note(); ';
										echo 'myNotes['.$count.'].id = '.$Ninfo['note_id'].'; ';
										echo 'myNotes['.$count.'].title = "'.$Ninfo['note_title'].'"; ';
										echo 'myNotes['.$count.'].text = "'.$Ninfo['note_content'].'"; ';
										echo 'myNotes['.$count.'].timestamp = "'.(strtotime($Ninfo['timestamp']) * 1000).'"; '; //fix for date
										echo 'myNotes['.$count.'].left = '.$Ninfo['note_x'].'; ';
										echo 'myNotes['.$count.'].top = '.$Ninfo['note_y'].'; ';
								    	echo 'myNotes['.$count.'].color = "'.$Linfo['layer_color'].'"; ';
										echo 'myNotes['.$count.'].authorId = '.$Ninfo['id'].';';
										echo 'myNotes['.$count.'].layerId = '.$Linfo['layerId'].';';
										echo 'myNotes['.$count.'].author = "'.$Ninfo['username'].'";';
										echo 'myNotes['.$count.'].likeCount = "'.$Ninfo['note_likes'].'";';
										echo 'myNotes['.$count.'].dislikeCount = "'.$Ninfo['note_dislikes'].'";';
										
										echo 'myNotes['.$count.'].dirty = 0;';
										$count++;
									}
								}
								$layerSelectOptions = $layerSelectOptions.'<option value="'.$Linfo['layerId'].'">'.$Linfo['layer_title'].'</option>';
						}
					}else{
						$query = "INSERT INTO`project_worksheet_layers` (`layerId`, `project_sheet_id`, `layer_title`, `layer_color`) VALUES (NULL, '".$id."', 'Default', '#7CFC00');";
						$query = mysql_query($query);
						$nlid = mysql_insert_id();
						$layerSelectOptions = $layerSelectOptions.'<option value="'.$nlid.'">Default</option>';
					}
					?>
					count = <?php echo $count; ?>;
				}
				
				function modifiedString(date)
				{
					return 'Last Modified: ' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
				}
				
				function newNote()
				{
					myNotes[count] = new Note();
					myNotes[count].id = -1;
					myNotes[count].timestamp = new Date().getTime();
					myNotes[count].left = Math.round(Math.random() * 400) + 'px';
					myNotes[count].top = Math.round(Math.random() * 500) + 'px';
					//myNotes[count].zIndex = ++highestZ;
					myNotes[count].authorId = myId;
					count++;
				}
				
				$('#newNoteButton ').click(function(){	
					if( $('.note_layer').length > 0){
						newNote();
					}else{
						window.alert("There are no layers... You must create a layer before you can place a note.");	
					}
				});
				
				$('#SaveButton').click(function(){
					for(var x =0 ; x < myNotes.length; x++){
						if (myNotes[x] != undefined){
							if( myNotes[x].dirty == 1){
								myNotes[x].save( x ); /*save the note*/
								myNotes[x].dirty = 0;	
							}
						}
					}
					//window.alert("Saved");
				});
				
				/*Select Layer - toggle the layer's activity*/
				$('#layerSelect').change(function(){
					var layerId= $(this).attr('value');
					$('.activeNoteLayer').removeClass('activeNoteLayer');
					$('#notes_layer_' + layerId).addClass('activeNoteLayer');
				});
				
				/*New Layer Functions (for adding a new layer)*/
				$('#newLayerButton').click(function(){
					$('#newLayer').show();
				});
				
				$('#closeNewLayer').click(function(){
					$('#newLayer').hide();
				});
				
				/*Binding functions to events!*/
				function fBindFunctionToElement(){
					/*Un bind events to prevent duplicate click actions*/
					$('.deleteLayer').unbind('click');
					$('.renameLayer').unbind('click');
					$('.hideLayer').unbind('click');
					$('.pinNotes').unbind('click');
					
					/*Re-bind Click events*/
				   $('.deleteLayer').bind("click", deleteLayerBtn);
				   $('.renameLayer').bind("click", renameLayerBtn);
				   $('.hideLayer').bind('click', hideLayerBtn);
				   $('.pinNotes').bind('click', pinNotesBtn);
				   
				}
				
				function newLayer(data, project_Id, layer_Title, layer_Color)
				{	
					numLayersCount = numLayersCount + 1;
					NewestLayerId = data;		
					$('.activeNoteLayer').removeClass('activeNoteLayer');
					$('#layerSelect').append('<option value="' + data +'">'+ layer_Title +'</option>');
					/*Append the new layer*/
					$('#WorkSheet').prepend('<div id="notes_layer_' + data + '" class="note_layer activeNoteLayer" style="height:' + height + 'px; width:' + width + 'px;"><input type="hidden" class="layerId" name="layer_id_' + data+ '" value="' + data+ '"/><input type="hidden" class="layerColor" name="layer_'+ data +'_color" value="' + layer_Color + '"/></div>');
					/*Bind items to Layer Controls*/
					$('#layerControls').append('<div class="layerControl"><input type="hidden" class="layerId" value="' + data + '"/><span class="layerName">' + layer_Title + '<span class="layerColorBlock" style="background-color: ' + layer_Color + '">&nbsp;&nbsp;&nbsp;[Color]</span></span><button class="renameLayer">Rename Layer</button><button class="deleteLayer">Delete Layer</button><button class="hideLayer">Hide Layer</button><button class="pinNotes">Pin Notes</button></div>');
					/*re bind Events to buttons*/
					fBindFunctionToElement();
				
				}
				
				
				$('#saveNewLayer').click(function(){
					var layerId = -1;
					var project_Id = <?php echo $id; ?>;
					var layer_Title = $('#layerTitle').attr('value');
					var layer_Color = $('#layerColor').attr('value');
					
					var jsonObj = {layerTitle: layer_Title.toString(), projectId: project_Id, layerColor: layer_Color.toString(), creator: myId }
					// Lets convert our JSON object
					var postData = JSON.stringify(jsonObj);
					// Lets put our stringified json into a variable for posting
					var postArray = {json:postData};
					$.ajax({
						type: 'POST',
						url: "_saveNewLayer.php",
						data: postArray,
						success: function(data){
							window.alert("Saved!");
							
							$('#newLayer').hide();
							
							newLayer(data, project_Id, layer_Title, layer_Color);
							$("#layerSelect").val( data ).attr('selected',true);
						}
					});
				});
				
				/*Modify Layer Scripts*/
				/*Rename a layer*/
				function renameLayerBtn(){
					var layerId = $(this).parent().find('.layerId').attr('value');
					var layerTitle = $(this).parent().find('.layerName').html();

					var newLayerName = prompt("What would you rather name this layer?", $.trim(layerTitle) );
					if( newLayerName != $.trim(layerTitle) ){
						var jsonObj = {renameLayer: layerId, layerName: newLayerName, projectId: <?php echo $id ?>  }
						var postData = JSON.stringify(jsonObj);
						var postArray = {json:postData};
						$.ajax({
							type: 'POST',
							url: "_saveNewLayer.php",
							data: postArray,
							success: function(data){
								if(  $.trim(data).toString() == "Renamed" ){
									$('#layerControls').find('input.layerId[value="' + layerId + '"]').parent().find('.layerName').html(newLayerName);
									$('#layerSelect').find('option[value="' + layerId + '"]').html(newLayerName);
								}
							}
						});
					}	
				}
				/*Delete the layer*/
				function deleteLayerBtn(){
					var layerId = $(this).parent().find('.layerId').attr('value');
					var layerTitle = $(this).parent().find('.layerName').html();
					var confirmDelete = confirm("Are you sure you wish to delete '" + $.trim(layerTitle) + "' and all of it's notes?");
					if( confirmDelete ==true){
						
						//MOVE THIS INTO ANOTHER FUNCTION
						
						
						$('#notes_layer_' + layerId).hide();
						$(this).parent().remove();
						$("#layerSelect option[value='" + layerId +"']").remove();
						/*Mark notes as deleted and save (deletes them)*/
						for(var x =0 ; x < myNotes.length; x++){
							if (myNotes[x] != undefined){
								if( myNotes[x].layerId == layerId){
									myNotes[x].deleted = 1;
									myNotes[x].save();
									myNotes[x].dirty = 0;	
								}
							}
						}
						
						//THIS STAYS FOR CLICK
						
						/*Delete Layer*/
						var jsonObj = {deleteLayer: layerId, projectId: <?php echo $id ?> }
						var postData = JSON.stringify(jsonObj);
						var postArray = {json:postData};
						$.ajax({
							type: 'POST',
							url: "_saveNewLayer.php",
							data: postArray,
							success: function(data){
								//ScreenConsoleLog(data);
								if( data == "Deleted"){
									numLayersCount = numLayersCount - 1;
								}
							}
						});
					}//end confirm
				}
				
				/*Toggle visibility of the notes*/
				function hideLayerBtn(){
					var layerId = $(this).parent().find('.layerId').attr('value');
					if( $(this).html() == "Hide Layer"){
						$('#notes_layer_' + layerId).hide();
						$(this).html("Show Layer");
					}else{
						$('#notes_layer_' + layerId).show();
						$(this).html("Hide Layer");
					}
				}
				
				function pinNotesBtn(){
					var layerId = $(this).parent().find('.layerId').attr('value');
					
					if( $(this).html() == "Pin Notes"){
						$('#notes_layer_' + layerId ).addClass('pinnedLayer');
						$(this).html("Un-Pin Notes");
					}else{
						$('#notes_layer_' + layerId).removeClass('pinnedLayer');
						$(this).html("Pin Notes");
					}
				}
				
				function getAndCreateNewLayers(olderLayerId)
				{
					var jsonObj = { returnNewLayers : 1, projectId: <?php echo $id ?>, layerToStart : olderLayerId }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData}
					$.ajax({
						type: 'POST',
						url: "checkNewest.php",
						data: postArray,
						success: function(data){
							//this should return new layer(s)
							 var json = $.parseJSON(data);
							 $(json).each(function(index){
								/**Heres our Layer **/
								newLayer( json[index].layerId, json[index].project_sheet_id, json[index].layer_title, json[index].layer_color);
							 });
						}
					});		
				}
				
				function removeStrayLayers(){		
					var jsonObj = { returnAllLayerIds : 1, projectId: <?php echo $id ?> }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData}
					$.ajax({
						type: 'POST',
						url: "checkNewest.php",
						data: postArray,
						success: function(data){
							//this should return new layer(s)
							var allLayers=new Array();
							$('.note_layer').each(function(index){
								allLayers[index] = parseInt ($(this).attr('id').substr( 12, $(this).attr('id').length) );
							});
							var numLayers = allLayers.length;
							var json = $.parseJSON(data);
							$(json).each(function(index){
								for(var i = allLayers.length-1; i >= 0; i--){  // STEP 1
									if(allLayers[i] == json[index].layerId ){  // STEP 2
										allLayers.splice(i,1);                 // STEP 3
									}
								}	
							});
							//ScreenConsoleLog("Remove: " + allLayers.length + "layers " );
							
							for(var i =  allLayers.length-1; i >= 0; i--){
								ScreenConsoleLog("Remove : " +  allLayers[i] );
								$('#notes_layer_' + allLayers[i] + ' .note').remove(); //remove notes
								$('#notes_layer_' + allLayers[i]).remove();
								$("#layerSelect option[value='" + allLayers[i] +"']").remove();
								$('#layerControls').find('input.layerId[value="' + allLayers[i] +'"]').parent().remove();
								//remove it's unsaved notes!
								numLayersCount = numLayersCount - 1;	
							}
						}
					});		
					
				}
				
				function fetchNoteUpdates(projectId, timeStamp)
				{
					var jsonObj = { returnNewNotes : 1, projectId: <?php echo $id ?>, since: timeStamp }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData}
					$.ajax({
						type: 'POST',
						url: "checkNewest.php",
						data: postArray,
						success: function(data){
							var json = $.parseJSON(data);
							$(json).each(function(index){
								var update = 0;
								//Fix the timeStamp!
								
							//	ScreenConsoleLog( json[index].timestamp );		
								
								for(var x = 0; x < myNotes.length; x++){
									if( myNotes[x] != undefined &&  myNotes[x].id == json[index].note_id ){
										myNotes[x].left = json[index].note_x;
										myNotes[x].top =  json[index].note_y;
										myNotes[x].timestamp = json[index].timestamp;
										myNotes[x].title =  json[index].note_title;
										myNotes[x].text = json[index].note_content;
										myNotes[x].layerId = json[index].project_layer_id;
										myNotes[x].author =  json[index].username;
										myNotes[x].authorId =  json[index].user_id;
										
										myNotes[x].likeCount = json[index].note_likes;
										myNotes[x].dislikeCount =  json[index].note_dislikes;
										
										
										update = 1;
									}
								}
								
								if( update == 0 ){
									//its a new note?	
									count = count + 1;
									myNotes[count] = new Note();
									myNotes[count].id = json[index].note_id;
									myNotes[count].left = json[index].note_x;
									myNotes[count].top =  json[index].note_y;
									myNotes[count].timestamp = json[index].timestamp;
									myNotes[count].title =  json[index].note_title;
									myNotes[count].text = json[index].note_content;
									myNotes[count].layerId = json[index].project_layer_id;
									myNotes[count].author =  json[index].username;
									myNotes[count].authorId =  json[index].user_id;
									
									myNotes[count].likeCount = json[index].note_likes;
									myNotes[count].dislikeCount =  json[index].note_dislikes;
										
									
								}
								
								NewestNoteTimeStamp = json[index].timestamp;
								
							});

						}
					});
				}
				
				function removeDeleted(id){
					//ScreenConsoleLog("Checking to remove Deleted");
					var jsonObj = { returnAllExitingNotes : 1, projectId: <?php echo $id ?> }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData};
					$.ajax({
						type: 'POST',
						url: "checkNewest.php",
						data: postArray,
						success: function(data){
							var json = $.parseJSON(data);
							var newNoteArray =  new Array();
							var newNoteCount = 0;
							$('.note').each(function(index) 
							{
								if( $(this).attr('id') != -1)
								{
									var f = 0;
									for( var z=0; z < json.length; z++ )
									{
										//ScreenConsoleLog("Comparing " +  json[z].note_id + " " + $(this).attr('id') );
										if(  json[z].note_id == $(this).attr('id')){
											f = 1;
											break;
										}
										
									}
									if( f == 0 )
									{
										//ScreenConsoleLog($(this).attr('id') + " is not in the array" );
										$('#' + $(this).attr('id') ).remove(); 
									}
								}
                            });
						}
	
					});
							
				}
				
				var t;
				function checkNotes(){
					var jsonObj = { projectId: <?php echo $id ?>, newestLayer : NewestLayerId, newestNote : NewestNoteId , newestTimeStamp : NewestNoteTimeStamp, numNotes : count }
					var postData = JSON.stringify(jsonObj);
					var postArray = {json:postData};
					$.ajax({
						type: 'POST',
						url: "checkNewest.php",
						data: postArray,
						success: function(data){
							//var json = data;
							var obj = jQuery.parseJSON(data);
							var numNewLayers = parseInt(obj.numNewLayers);
							var numNewNotes = parseInt(obj.numNewNotes);
							var numNoteUpdates = parseInt(obj.numNoteUpdates);
							var numNotes = parseInt(obj.numNotes);
							var numLayers = parseInt(obj.numLayers);
							var newestDetectedLayer = parseInt(obj.newestLayerId);
							
							ScreenConsoleLog("Checking for Updates");
							
							if( numNewLayers > 0 || newestDetectedLayer > NewestLayerId){
								//get and create the new Layers!
								getAndCreateNewLayers(NewestLayerId);
							}
							
							if( numLayers < numLayersCount || (numLayers == numLayersCount && newestDetectedLayer != NewestLayerId) ){
								//ScreenConsoleLog("Layer was deleted!");
								//get new list of layers, determine which layer was deleted and delete it/them
								removeStrayLayers();
							}
							
							removeDeleted(<?php echo $id ?>);
							
							if ( numNewNotes > 0 || numNotes > count || numNoteUpdates >0  ){
								// a note was added!
								fetchNoteUpdates( <?php echo $id ?> ,NewestNoteTimeStamp);
							}
						}
					});
					
				}
				
				t = window.setInterval(checkNotes, 10000);   
				
				addEventListener('load', loaded, false); /*when page loads, activate note display & load*/
				fBindFunctionToElement();
			});
			</script>
            
            <div id="layerControls">
            	<h2>Note Controls</h2>
                
                <div id="NoteTools">
            		<button id="newNoteButton" >New Note</button> <button id="newLayerButton" >New Layer</button> <button id="SaveButton">Save Notes</button>
          		</div>
                
                <div id="LayerSwitcher">
               		<p><b>Choose Active Layer: </b> <select id="layerSelect" name="Switch Layer">
            		<?php
						echo $layerSelectOptions;
					?>
           			</select></p>
                </div>
                
            	<?php
            		$workSheetLayersQuery = "SELECT * FROM `project_worksheet_layers` WHERE `project_sheet_id` = ".$id;
					$workSheetLayersQuery = mysql_query($workSheetLayersQuery);
					$num_rows = mysql_num_rows($workSheetLayersQuery);
					if($num_rows > 0 ){
						$count = 0;
						$layerSelectOptions = "";
						while($Linfo = mysql_fetch_assoc( $workSheetLayersQuery ))
						{
				?>
                    <div class="layerControl">
                        <input type="hidden" class="layerId" value="<?php echo $Linfo['layerId']; ?>"/>
                        <span class="layerName">
                            <?php echo $Linfo['layer_title']; echo '&nbsp;&nbsp;&nbsp;<span class="layerColorBlock" style="background-color: '. $Linfo['layer_color'].'">[Color]</span>'; ?>
                        </span>
                        <button class="renameLayer">Rename Layer</button>
                        <button class="deleteLayer">Delete Layer</button>
                        <button class="hideLayer">Hide Layer</button>
                        <button class="pinNotes">Pin Notes</button>  
                    </div>
                <?php } } ?>
            </div>
            
 
            <!--New Layer -->
            <div id="newLayer" style="display:none;">
                <div class="row">
                    <div class="titleCol">
                     Layer Title:
                    </div>
                    <div class="valueCol">
                        <input name="layerTitle" id="layerTitle" type="text"  value="" maxlength="30" required="required"/>
                    </div>
                    <div class="clear" ></div>
                </div>
                
                <div class="row">
                    <div class="titleCol">
                    	Layer Color:
                    </div>
                    <div class="valueCol">
                    	<select name="layerColor" id="layerColor">
                        	<?php
								$layerColors = "SELECT * FROM `layer_colors`";
								$layerColors = mysql_query($layerColors);
								while($colours = mysql_fetch_assoc( $layerColors )){
									echo '<option value="'.$colours['value'].'">'.$colours['color'].'</option>';
								}
							?>
                        </select>
                    </div>
                    <div class="clear" ></div>
                </div>
                
                <div class="row">
                	<input id="saveNewLayer" type="button" value="Save new Layer" />
                    <input id="closeNewLayer" type="button" value="Close" />
                </div>
                   
            </div>
           
            <!--Notes Layer-->
            
            <textarea id="ScreenConsole" style="width: <?php echo $width; ?>px; height: 60px; overflow-y: auto;">
            
            </textarea>
            
            <div id="WorkSheet" style="background-image:url('<?php echo pathToRoot()."getFile.php?id=".$imageId."&type=application/octet-stream"; ?>'); background-repeat:no-repeat; width:<?php echo $width; ?>px; height: <?php echo $height; ?>px; background-position: center center;">
               
                <?php
					//grab worksheet layers
					$workSheetLayersQuery = "SELECT * FROM `project_worksheet_layers` WHERE `project_sheet_id` = ".$id;
					$workSheetLayersQuery = mysql_query($workSheetLayersQuery);
					$num_rows = mysql_num_rows($workSheetLayersQuery);
					if($num_rows > 0 ){
						$count = 0;
						while($Linfo = mysql_fetch_assoc( $workSheetLayersQuery ))
						{
							if($count == 0 ){
								echo '<div id="notes_layer_'.$Linfo['layerId'].'" class="note_layer activeNoteLayer" style="height:'.$height.'px; width:'.$width.'px;">';	
							}else{
								echo '<div id="notes_layer_'.$Linfo['layerId'].'" class="note_layer" style="height:'.$height.'px; width:'.$width.'px;">';
							}
							/*Layer Info*/
							echo '<input type="hidden" class="layerId" name="layer_id_'.$Linfo['layerId'].'" value="'.$Linfo['layerId'].'"/>';
							echo '<input type="hidden" class="layerColor" name="layer_'.$Linfo['layerId'].'_color" value="'.$Linfo['layer_color'].'"/>';
							echo '<input type="hidden" name="layer_'.$Linfo['layerId'].'_name" value="'.$Linfo['layer_title'].'"/>';
							echo '</div>';
							$count++;
						}
						
					}
                ?>
            </div>
            

            
            
            <p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Back to Projects</a></p>
        <?php
		printFooter();
	}else{
		printHeader("Viewing Project Sheet (Error)");
		?>
        	<p>Error retrieving project sheet...</p>
        	<p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Go Back</a></p>
        <?php
		printFooter();
	}
	}else{
		printHeader("Viewing Project Sheet - Sorry no permissions!");
		?>
        <p>Sorry you do not have permissions to this project. If you feel this is incorrect, please contact an administrator.</p>
        <p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Go Back</a></p>
        <?php
		printFooter();
	}
	
}else{
	printHeader("Viewing Project Sheet");
	?>
    	<p>Error! No Project Sheet Specified.</p>
        <p><a class="button buttonGoBack" href="<?php echo pathToRoot()."index.php"; ?>">Go Back</a></p>
    <?php
	printFooter();
}
?>