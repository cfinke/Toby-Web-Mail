 <?php

// Settings file
// This file should take care of the main settings page.

error_reporting(0);

include("globals.php");

if ($_REQUEST["action"] == CHANGE_MAIN_SETTINGS){
	$oldlang = $_SESSION["toby"]["lang"];
	update_settings($_REQUEST["realname"],$_REQUEST["email_address"],$_REQUEST["save"],$_REQUEST["save_sent"],$_REQUEST["compose_type"], $_REQUEST["lang"],$_REQUEST["timezone"], $_REQUEST["refresh_interval"]);
	
	if ($_REQUEST["lang"] != $oldlang){
		header("Location: ".$mainpage);
	}
}

$output .= $transdtd . '
	<html>
		<head>
			<title>'.OPTIONS.'</title>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body>';

$query = "SELECT * FROM `email_users` WHERE `id`=".$_SESSION["toby"]["userid"];
$result = run_query($query);
$row = mysql_fetch_array($result);

$output .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post">
				<table cellspacing="1" cellpadding="0" id="foldertable">
					<tr class="settingsrow_head">
						<td colspan="3">
							'.OPTIONS.'
						</td>
					</tr>
					<tr class="settingsrow1">
						<td class="formlabel">
							<label for="realname">
								'.REAL_NAME.':
							</label>
						</td>
						<td colspan="2">
							<input type="text" name="realname" id="realname" value="'.$_SESSION["toby"]["realname"].'" />
						</td>
					</tr>
					<tr class="settingsrow2">
						<td class="formlabel">
							<label for="email_address">
								'.EMAIL_ADDRESS.':
							</label>
						</td>
						<td colspan="2">
							<input type="text" name="email_address" id="email_address" value="'.$_SESSION["toby"]["email_address"].'" />
						</td>
					</tr>
					<tr class="settingsrow1">
						<td class="formlabel">
							<label for="language">
								'.LANGUAGE.':
							</label>
						</td>
						<td colspan="2">
							<select name="lang" id="lang">';

foreach($langs as $key => $lang){
	$output .= '<option value="'.$key.'"';
	if ($_SESSION["toby"]["lang"] == $key) $output .= ' selected="selected"';
	$output .= '>'.$lang.'</option>';
}


$output .= '				</select>
						</td>
					</tr>
					<tr class="settingsrow2">
						<td class="formlabel">'.TIMEZONE.':</td>
						<td class="forminput" colspan="2">
							<select name="timezone" id="timezone">
								'.get_timezone_dropdown($row["timezone"]).'
							</select>
						</td>
					</tr>
					<tr class="settingsrow1">
						<td class="formlabel" style="width: 40%;">'.MAIL_REFRESH_QUESTION.'</td>
						<td class="forminput" colspan="2"><input type="text" name="refresh_interval" value="'.$row["refresh_interval"].'" maxlength="4" /></td>
					</tr>
					<tr class="settingsrow2">
						<td class="formlabel" style="width: 40%;">'.SAVE_INCOMING_QUESTION.'</td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="save" id="save_yes" value="1" ';if ($row["save_messages"]) $output .= ' checked="checked" ';$output .= '/><label for="save_yes">'.YES.'</label></td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="save" id="save_no" value="0" ';if (!$row["save_messages"]) $output .= ' checked="checked" ';$output .= '/><label for="save_no">'.NO.'</label></td>
					</tr>					
					<tr class="settingsrow1">
						<td class="formlabel" style="width: 40%;">'.SAVE_SENT_QUESTION.'</td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="save_sent" id="save_sent_yes" value="1" ';if ($row["save_sent"]) $output .= ' checked="checked" ';$output .= '/><label for="save_sent_yes">'.YES.'</label></td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="save_sent" id="save_sent_no" value="0" ';if (!$row["save_sent"]) $output .= ' checked="checked" ';$output .= '/><label for="save_sent_no">'.NO.'</label></td>
					</tr>
					<tr class="settingsrow2">
						<td class="formlabel" style="width: 40%;">'.DEFAULT_MODE.':</td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="compose_type" id="compose_type_html" value="html" ';if ($_SESSION["toby"]["compose_type"] == "html") $output .= ' checked="checked" ';$output .= '/><label for="compose_type_html">'.HTML.'</label></td>
						<td class="forminput" style="width: 30%;"><input type="radio" name="compose_type" id="compose_type_text" value="text" ';if ($_SESSION["toby"]["compose_type"] == "text") $output .= ' checked="checked" ';$output .= '/><label for="compose_type_text">'.TEXT.'</label></td>
					</tr>
					<tr class="settingsrow_head">
						<td>
						</td>
						<td colspan="2">
							<input type="submit" name="'.str_replace(" ","_",CHANGE_MAIN_SETTINGS).'" value="'.CHANGE_MAIN_SETTINGS.'" />
						</td>
					</tr>
				</table>
			</form>
		</body>
	</html>';

echo $output;
exit;

function update_settings($realname,$email_address,$save, $save_sent, $compose_type, $lang, $timezone, $refresh_interval){
	$_SESSION["toby"]["realname"] = $realname;
	$_SESSION["toby"]["email_address"] = $email_address;
	$_SESSION["toby"]["save"] = $save;
	$_SESSION["toby"]["compose_type"] = $compose_type;
	$_SESSION["toby"]["lang"] = $lang;
	$_SESSION["toby"]["refresh_interval"] = $refresh_interval;
	
	$query = "UPDATE `email_users` SET `save_sent`=".($save_sent / 1).",`save_messages`=".($save / 1).", `realname`='".$realname."', `email_address`='".$email_address."',`compose_type`='".$compose_type."',`lang`='".$lang."',`timezone`='".$timezone."',`refresh_interval`='".((int) ($refresh_interval / 1))."' WHERE `id`=".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	return;
}

function get_timezone_dropdown($selected = "+0000"){
	$output .= '<option value="'.$selected.'" selected="selected">'.$selected.'</option>';
	for ($i = 12; $i >= 10; $i--){
		$output .= '<option value="-'.$i.'00"';
		if (strcmp("-$i00",$selected) == 0) $output .= ' selected="selected"';
		$output .= '>-'.$i.'00</option>';
	}
	
	for ($i = 9; $i > 0; $i--){
		$output .= '<option value="-0'.$i.'00"';
		if (strcmp("-0$i00",$selected) == 0) $output .= ' selected="selected"';
		$output .= '>-0'.$i.'00</option>';	
	}
	
	for ($i = 0; $i < 10; $i++){
		$output .= '<option value="+0'.$i.'00"';
		if (strcmp("+0$i00",$selected) == 0) $output .= ' selected="selected"';
		$output .= '>+0'.$i.'00</option>';	
	}
	
	for ($i = 10; $i <= 13; $i++){
		$output .= '<option value="+'.$i.'00"';
		if (strcmp("+$i00",$selected) == 0) $output .= ' selected="selected"';
		$output .= '>+'.$i.'00</option>';
	}
	
	return $output;
}

?>