<?php

// Message Download Page
// This page should take care of anything having to do with downloading
// message backups.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

// If the user has submitted the form, send the zip file.
if ($_REQUEST["action"] == GET_MESSAGES){
	folder_backup($_REQUEST["backupfolder"]);
}

$output .= $trans_dtd . '
	<html>
		<head>
			<title>'.DOWNLOAD_MESSAGES.'</title>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<table cellspacing="1" cellpadding="0" id="foldertable">
					<tr class="settingsrow_head">
						<td colspan="2">
							'.DOWNLOAD_AS_ZIP.'
						</td>
					</tr>
					<tr class="settingsrow1">
						<td style="text-align: center;">
							<input type="submit" name="'.str_replace(" ","_",GET_MESSAGES).'" value="'.GET_MESSAGES.'" />
						</td>
						<td>
							<select name="backupfolder">
								<option value="0">'.ALL_FOLDERS.'</option>
								'.get_folder_dropdown().'
							</select>
						</td>
					</tr>
				</table>
			</form>
		</body>
	</html>';

echo $output;

function folder_backup($folder_id){
	do {
		$dir = "Toby.".str_replace(" ","_",BACKUP).".".date("Y.m.d").".".++$i . "/";
	} while (is_dir($dir));
	
	mkdir($dir, 0777);
	
	add_child_folder($folder_id, $dir);
	
	do {
		$filename = rand();
	} while (is_file($filename));
	
	system("tar -cf ".$filename. " " .$dir);
	
	$handle = fopen($filename, "r");
	
	if ($handle){
		$file = fread($handle, filesize($filename));
		fclose($handle);
		
		// Send the content type
		header("Content-type: application/zip;");
		
		// Get a filename for it too
		header("Content-Disposition: attachment; filename=Toby.".str_replace(" ","_",BACKUP).".".date("Y.m.d").".tar;");
		
		// Send the attachment
		echo $file;
		
		system("rm -r ".$filename);
		system("rm -r ".$dir);
	}
	
	return;
}

function add_child_folder($folder_id, $path){
	$file_array = array();
	
	if ($folder_id == 0){
		$newpath = $path;
	}
	else{
		$query = "SELECT `folder_name` FROM	`email_folders` WHERE `id`=".$folder_id." AND `user` = ".$_SESSION["toby"]["userid"];
		$result = run_query($query);
		$folder_name = str_replace(" ","_",str_replace("/","-",mysql_result($result, 0, 'folder_name'))) . '/';
		
		mkdir($path . $folder_name, 0777);
		
		$newpath = $path . $folder_name;
	}
	
	#############################
	
	$query = "SELECT `id` FROM `email` WHERE `folder` = ".$folder_id." AND `user` = ".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	while ($row = mysql_fetch_array($result)){
		$file_array = write_email_to_file($row["id"], $newpath, $file_array);
	}
	
	$query = "SELECT `id` FROM `email_folders` WHERE `parent_id` = ".$folder_id." AND `user`=".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	while ($row = mysql_fetch_array($result)){
		add_child_folder($row["id"], $newpath);
	}
	
	#############################
	
	return;
}

function write_email_to_file($id,$dir,$file_array){
	$query = "SELECT `headers`,`message`,`Subject` FROM `email` WHERE `id`=".$id;
	$result = run_query($query);
	$row = mysql_fetch_array($result);
	
	$text = $row["headers"] . "
	
	" . $row["message"];
	
	$filename = str_replace("?","",
				str_replace(":","-",
				str_replace("/","-",
				str_replace("\\","-",
				str_replace("|","-",
				str_replace('"',"'",
				str_replace("*","",
				str_replace("<","",
				str_replace(">","",substr($row["Subject"],0,56))))))))));
	
	if (trim($filename) == ""){
		$filename = '['.NO_SUBJECT.']';
	}
	
	$temp_filename = $filename;
	$i = 1;
	
	$query = "SELECT `part` FROM `email_overflow` WHERE `key`=".$id." ORDER BY `part_id` ASC";
	$result = run_query($query);
	
	while ($row = mysql_fetch_array($result)){
		$text .= $row["part"];
	}
	
	while ((is_file($dir . $filename . '.eml')) || (in_array(strtolower($filename), $file_array))){
		$filename = $temp_filename . ' (' . $i++ . ')';
	}
	
	$file_array[] = strtolower($filename);
	
	$filename .= '.eml';
	
	$handle = fopen($dir . $filename, "w");
	
	fwrite($handle, $text);
	
	fclose($handle);
	
	return $file_array;
}

?>