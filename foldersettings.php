<?php

// Folder Settings File
// This file should take care of anything having to do with the
// managing of the folder structure.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

// On any of these actions, the leftmost frame should be refreshed.
$refresh_folderlist_actions = array(ADD_FOLDER,DELETE_FOLDER,MOVE_FOLDER,RENAME_FOLDER);

switch($_REQUEST["action"]){
	case ADD_FOLDER:
		add_folder($_REQUEST["newfolderparent"], $_REQUEST["newfolder"]);
		break;
	case DELETE_FOLDER:
		delete_folder($_REQUEST["folder"], $_REQUEST["delete_messages_in_folder"]);
		break;
	case MOVE_FOLDER:
		move_folder($_REQUEST["movefolderto"], $_REQUEST["foldertomove"]);
		break;
	case RENAME_FOLDER:
		rename_folder($_REQUEST["foldername"], $_REQUEST["editfolder"]);
		break;
}

$output .= $transdtd . '
	<html>
		<head>
			<title>'.FOLDER_SETTINGS.'</title>
			<script type="text/javascript">
				<!-- 
				function refresh_folderlist(){
					top.folders.location.href = "'.$folderpage.'?method=folders";
				}
				// -->
			</script>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body';

if (in_array($_REQUEST["action"], $refresh_folderlist_actions)){
	$output .= ' onload="refresh_folderlist();"';
}

$output .= '>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<table cellspacing="1" cellpadding="0" id="foldertable">
					<tr class="settingsrow_head">
						<td colspan="3">
							'.FOLDERS.'
						</td>
					</tr>
					<tr class="settingsrow1">
						<td style="text-align: center;">
							<input type="submit" value="'.ADD_FOLDER.'" name="'.str_replace(" ","_",ADD_FOLDER).'" />
						</td>
						<td>
							<select name="newfolderparent">
								<option value="0">'.ADD_UNDER.'</option>
								'.get_folder_dropdown().'
							</select>
						</td>
						<td>
							<input type="text" name="newfolder" size="10" />
						</td>
					</tr>
					<tr class="settingsrow2">
						<td style="text-align: center;">
							<input type="submit" value="'.RENAME_FOLDER.'" name="'.str_replace(" ","_",RENAME_FOLDER).'" />
						</td>
						<td>
							<select name="editfolder">
								<option value="">'.SELECT_RENAME_FOLDER.'</option>
								'.get_folder_dropdown().'
							</select>
						</td>
						<td>
							<input type="text" name="foldername" size="10" />
						</td>
					</tr>
					<tr class="settingsrow1">
						<td rowspan="2" style="text-align: center;">
							<input type="submit" value="'.MOVE_FOLDER.'" name="'.str_replace(" ","_",MOVE_FOLDER).'" />
						</td>
						<td colspan="2">
							<select name="foldertomove">
								<option value="">'.SELECT_MOVE_FOLDER.'</option>
								'.get_folder_dropdown().'
							</select>
						</td>
					</tr>
					<tr class="settingsrow2">
						<td colspan="2">
							<select name="movefolderto">
								<option value="0">'.SELECT_NEW_LOCATION.'</option>
								'.get_folder_dropdown().'
							</select>
						</td>
					</tr>
					<tr class="settingsrow1">
						<td style="text-align: center;">
							<input type="submit" value="'.DELETE_FOLDER.'" name="'.str_replace(" ","_",DELETE_FOLDER).'" />
						</td>
						<td colspan="2">
							<select name="folder">
								<option value="">'.SELECT_DELETE_FOLDER.'</option>
								'.get_folder_dropdown().'
							</select><br />
							<input type="checkbox" name="delete_messages_in_folder" value="yes" /><small>'.DELETE_MESSAGES_IN_FOLDER.'</small>
						</td>
					</tr>
				</table>
			</form>
		</body>
	</html>';

echo $output;
exit;

function rename_folder($new_name, $folder_id){
	if (($new_name != '') && ($folder_id != '')){
		$query = "UPDATE `email_folders` SET `folder_name`='".$new_name."' WHERE `id`=".$folder_id;
		$result = run_query($query);
	}
	
	return;
}

function move_folder($new_parent_id, $folder_id){
	if ($folder_id != ''){
		$query = "UPDATE `email_folders` SET `parent_id`=".$new_parent_id." WHERE `id`=".$folder_id;
		$result = run_query($query);
	}
	
	return;
}

function delete_folder($folder_id, $delete_messages = false){
	if ($folder_id != ''){
		if ($delete_messages == "yes"){
			$query = "SELECT `id` FROM `email` WHERE `folder`=".$folder_id;
			$result = run_query($query);
			
			while ($row = mysql_fetch_array($result)){
				$messages_to_delete[] = $row["id"];
			}
			
			delete_messages($messages_to_delete);
		}
		
		$query = "SELECT * FROM `email_folders` WHERE `id`=".$folder_id;
		$result = run_query($query);
		$row = mysql_fetch_array($result);
		
		$query = "UPDATE `email` SET `folder`=".$row["parent_id"]." WHERE `folder`=".$folder_id;
		$result = run_query($query);
		
		$query = "UPDATE `email_folders` SET `parent_id`=".$row["parent_id"]." WHERE `parent_id`=".$folder_id;
		$result = run_query($query);
		
		$query = "DELETE FROM `email_folders` WHERE `id`=".$folder_id; 
		$result = run_query($query);
	}
	
	return;
}

function add_folder($parent_folder, $folder_name){
	$query = "INSERT INTO `email_folders` (`parent_id`,`user`,`folder_name`) VALUES (".$parent_folder.",".$_SESSION["toby"]["userid"].",'".$folder_name."')";
	$result = @mysql_query($query);
	
	return;
}

?>