<?php

// Attachment handling file
// This file should take care of any operations involving adding/removing
// attachments from an e-mail.

include("globals.php");

// If this page is being requested from the message composition page,
// save the email information in the session variables.
if ($_REQUEST["action"] == ATTACHMENTS){
	$_SESSION["toby"]["compose"]["in_reply_to"] = $_REQUEST["in_reply_to"];
	$_SESSION["toby"]["compose"]["to"] = $_REQUEST["to"];
	$_SESSION["toby"]["compose"]["cc"] = $_REQUEST["cc"];
	$_SESSION["toby"]["compose"]["bcc"] = $_REQUEST["bcc"];
	$_SESSION["toby"]["compose"]["subject"] = $_REQUEST["subject"];
	$_SESSION["toby"]["compose"]["save_message_in"] = $_REQUEST["save_message_in"];
	$_SESSION["toby"]["compose"]["save_sent"] = ($_REQUEST["save_sent"]) ? 1 : 0;
}
elseif ($_REQUEST["action"] == DELETE_SELECTED_ATTACHMENTS){
	if (is_array($_REQUEST["dmsg"])){
		foreach($_REQUEST["dmsg"] as $attachment){
			remove_attachment($attachment);
		}
	}
}
elseif ($_REQUEST["action"] == ATTACH_FILE){
	attach_file($_FILES["userfile"]);
}

$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
		<head>
			<title>'.ATTACHMENTS.'</title>
			<script type="text/javascript">
				<!-- 
				function check_boxes() {
					var state = document.mainform.checkall.checked;
					for (i = 0; i < document.mainform.elements.length; i++){
						if (document.mainform.elements[i].type == \'checkbox\'){
							document.mainform.elements[i].checked = state;
						}
					}
				}
				// -->
			</script>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend>'.MANAGE_ATTACHMENTS.'</legend>
					<input type="hidden" name="compose_type" value="'.$_REQUEST["compose_type"].'" />
					<table style="width: 100%; background: #000000;" cellspacing="1">
						<tr class="settingsrow_head">
							<td colspan="3">
								'.ADD_ATTACHMENT.'
							</td>
						</tr>
						<tr class="settingsrow1">
							<td>
								<input type="file" name="userfile" />
							</td>
							<td colspan="2">
								<input type="submit" name="'.str_replace(" ","_",ATTACH_FILE).'" value="'.ATTACH_FILE.'" />
							</td>
						</tr>';
		
		// If there are any files currently attached, display each of 
		// them as a table row.
		if(count($_SESSION["toby"]["compose"]["attached_files"]) > 0){
			// Display the table header.
			$output .= '
					<tr class="settingsrow_head">
						<td>
							'.FILENAME.'
						</td>
						<td>
							'.SIZE.'
						</td>
						<td>
							'.TYPE.'
						</td>
					</tr>';
			
			// Display the table rows.
			foreach ($_SESSION["toby"]["compose"]["attached_files"] as $key => $file){
				if ($file["name"] != ''){
					$output .= '
						<tr class="settingsrow1">
							<td>
								<input type="checkbox" name="dmsg[]" value="'.$key.'" id="check'.$key.'" />
								<label for="check'.$key.'">'.$file["name"].'</label>
							</td>
							<td>
								'.($file["size"] / 1000).' '.KILOBYTES_UNIT.'
							</td>
							<td>
								'.$file["mimetype"].'
							</td>
						</tr>';
				}
			}
			
			// Display the table footer.
			$output .= '
				<tr class="settingsrow_head">
					<td colspan="3" style="text-align: center;">
						<input type="submit" name="'.str_replace(" ","_",DELETE_SELECTED_ATTACHMENTS).'" value="'.DELETE_SELECTED_ATTACHMENTS.'" />
					</td>
				</tr>';
		}
		
		$output .= '</table>
					<p><a href="composer.php?frompage=attachments&amp;compose_type='.$_REQUEST["compose_type"].'">'.BACK_TO_MESSAGE.'</a></p>
				</fieldset>
			</form>
		</body>
	</html>';

echo $output;

function attach_file($file){
	global $temp_directory;
	
	if (is_file($file["tmp_name"])){
		do{
			$filename = $temp_directory.rand();
		} while(is_file($filename));
		
		copy($file["tmp_name"], $filename);
		
		$_SESSION["toby"]["compose"]["attached_files"][] = array("name"=>$file["name"],"tmp_name"=>$filename,"size"=>$file["size"],"mimetype"=>$file["type"]);
	}
	
	return;
}

function remove_attachment($attachment_key){
	$filename = $_SESSION["toby"]["compose"]["attached_files"][$attachment_key]["tmp_name"];
	
	if (is_file($filename)){
		unlink($filename);
	}
	
	unset($_SESSION["toby"]["compose"]["attached_files"][$attachment_key]);
	
	return;
}

?>