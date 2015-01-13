<?php
	function imageToThumb($imgData, $t_width, $quality = 100,$deletetemp =0){
		$thumbData = array();
		
		$thumbData['fileName']  = stripslashes($imgData['name']);
		$thumbData['extension'] = strtolower(pathinfo($thumbData['fileName'], PATHINFO_EXTENSION)); 
		$uploadedfile           = $imgData['tmp_name'];
		$thumbData['Orig_size'] = filesize($uploadedfile);
		$thumbData['error']     = "No errors";
		
		if( ($thumbData['extension'] != "jpg") && ($thumbData['extension'] != "jpeg") && ($thumbData['extension'] != "png") && ($thumbData['extension'] != "gif")) 
 		{
 			$errors=1;
			$thumbData['error'] = "invalid file type.";
 		}
 		else
 		{
			$size = $thumbData['Orig_size'];
			if ($size > MAX_SIZE*1024)
			{
				$errors = 1;
				$thumbData['error'] = "File to Big!";
			}else{
				if($thumbData['extension'] =="jpg" || $thumbData['extension']=="jpeg" )
				{
					$src = imagecreatefromjpeg($uploadedfile);
				}else if($thumbData['extension'] =="png"){
					$src = imagecreatefrompng($uploadedfile);
				}else{
					$src = imagecreatefromgif($uploadedfile);
				}
	
				list($width,$height)=getimagesize($uploadedfile);
				$t_height=($height/$width)*$t_width;
				$tmp=imagecreatetruecolor($t_width,$t_height);
				
				imagecopyresampled($tmp,$src,0,0,0,0, $t_width,$t_height,$width,$height);
				
				$filename = "tmp_images/". $_FILES['file']['name'];
				
				imagejpeg($tmp,$filename,$quality);
				
				$fp = fopen($filename, 'r');
				$thumbData['size'] = filesize($filename);
				$fileType= "application/octet-stream";
				$thumbData['content'] = fread($fp, filesize($filename));
				$thumbData['content'] = addslashes($thumbData['content']);
				fclose($fp);
				 
				$thumbData['height'] = $t_height;
				$thumbData['width'] = $t_width;
				$thumbData['type'] = $fileType;
				if( $deletetemp ){
					unlink($filename);
				}
				imagedestroy($tmp);
			}
		}
		return $thumbData;
	}
?>