<?php

// Composer File
// This file should take care of anything having to do with composing
// email messages.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

// If the user is entering the composition page for the first time, clear
// the compose session variable.
if (($_REQUEST["action"] == COMPOSE) && ($_REQUEST["frompage"] != "attachments")){
	unset($_SESSION["toby"]["compose"]);
	
	if (isset($_REQUEST["compose_to"])){
		$to = $_REQUEST["compose_to"];
	}
}
elseif ($_REQUEST["action"] == FORWARD){
	get_forwarded_attachments($_REQUEST["dmsg"]);
}

if ($_REQUEST["action"] == HTML_MODE){
	$compose_type = "html";
}
elseif ($_REQUEST["action"] == TEXT_MODE){
	$compose_type = "text";
}
else{
	if (!isset($_REQUEST["compose_type"])) $compose_type = $_SESSION["toby"]["compose_type"];
	else $compose_type = $_REQUEST["compose_type"];
}

switch($_REQUEST["action"]){
	case REPLY_TO_ALL:
		$cc =  str_replace('"','',get_receivers($_REQUEST["dmsg"]));
		$bcc =  str_replace('"','',get_ccd($_REQUEST["dmsg"]));
	case REPLY:
		$to = str_replace('"','',get_senders($_REQUEST["dmsg"]));
		
		if (is_array($_REQUEST["dmsg"])) $id = $_REQUEST["dmsg"][0];
		else $id = $_SESSION["toby"]["lastviewed"];
		
		$query = "SELECT `Message-ID` FROM `email` WHERE `id`='".$id."'";
		$result = run_query($query);
		
		if (mysql_num_rows($result) > 0) $in_reply_to = mysql_result($result, 0, 'Message-ID');
	case FORWARD:
		if (count($dmsg) > 0){
			$msgnum = $_REQUEST["dmsg"][0];
		}
		else{
			$msgnum = $_SESSION["toby"]["lastviewed"];
		}
		
		$body = get_reply_message($_REQUEST["dmsg"], $compose_type);
		$prefix = ($action == FORWARD) ? FORWARD_PREFIX . ": " : REPLY_PREFIX . ": ";
		$subject = get_subject($_REQUEST["dmsg"], $prefix);
		
		$num_attachments = num_attachments($msgnum);
		
		for ($i = 1; $i <= $num_attachments; $i++){
			$attached_files[] = array("name"=>get_attached_file_name($i, $msgnum));
		}
		
		break;
	case HTML_MODE:
		$body = str_replace("\n","<br />",$_SESSION["toby"]["compose"]["body"]);
		$body = preg_replace("/&lt;\s*(\S*)@(\S*)\s*&gt;/Ui","&amp;lt;\\1@\\2&amp;gt;",$body);
		break;
	case TEXT_MODE:
		$body = stripslashes($_SESSION["toby"]["compose"]["body"]);
		$body = str_replace("\r","",$body);
		$body = html_entity_decode($body);
		$body = preg_replace("/<\s*(\S*)@(\S*)\s*>/Ui","&lt;\\1@\\2&gt;",$body);
		$body = preg_replace("/<\/*p.*>/Ui","\n",$body);
		$body = preg_replace("/<br.*>/Ui","\n",$body);
		$body = strip_tags($body);
		$body = str_replace("\r","",$body);
		
		do{
			$tempbody = str_replace("\n\n\n", "\n\n",$body);
		} while (($tempbody != $body) && ($body = $tempbody));
		
		break;
}

if (isset($_REQUEST["frompage"]) && ($_REQUEST["frompage"] == "attachments")){
	if (count($_SESSION["toby"]["compose"]) > 0){
		foreach ($_SESSION["toby"]["compose"] as $key => $value){
			if (!is_array($_SESSION["toby"]["compose"][$key])){
				$_SESSION["toby"]["compose"][$key] = stripslashes(str_replace('"',"''",$value));
			}
		}
		
		$in_reply_to = $_SESSION["toby"]["compose"]["in_reply_to"];
		$body = $_SESSION["toby"]["compose"]["body"];
		$to = $_SESSION["toby"]["compose"]["to"];
		$cc = $_SESSION["toby"]["compose"]["cc"];
		$bcc = $_SESSION["toby"]["compose"]["bcc"];
		$subject = $_SESSION["toby"]["compose"]["subject"];
		$checked = ($_SESSION["toby"]["compose"]["save_sent"]) ? ' checked="checked"' : '';
	}
}

$query = "SELECT `sent_folder`,`save_sent` FROM `email_users` WHERE `id`='".$_SESSION["toby"]["userid"]."'";
$result = run_query($query);

if ($_SESSION["toby"]["compose"]["save_message_in"]){
	$sent_folder = $_SESSION["toby"]["compose"]["save_message_in"];
}
else{
	$sent_folder = mysql_result($result, 0, 'sent_folder');
}

if (!isset($checked)){
	if (mysql_result($result, 0, 'save_sent')){
		$checked = ' checked="checked"';
	}
	else{
		$checked = '';
	}
}

$query = "SELECT * FROM `email_address_book` WHERE `userid`='".$_SESSION["toby"]["userid"]."' ORDER BY `name`";
$result = run_query($query);

while ($row = mysql_fetch_array($result)){
	$addresses[] = $row;
}

$output .= $transdtd . '
	<html>
		<head>
			<title>'.COMPOSE.'</title>
			<script type="text/javascript">
				<!-- 
				function getSelectedRadio(buttonGroup) {
					// returns the array number of the selected radio button or -1 if no button is selected
					if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
						for (var i=0; i<buttonGroup.length; i++) {
							if (buttonGroup[i].checked) {
								return i
							}
						}
					} else {
						if (buttonGroup.checked) { return 0; } // if the one button is checked, return zero
					}
					// if we get to this point, no radio button is selected
					return -1;
				} // Ends the "getSelectedRadio" function
				
				function getSelectedRadioValue(buttonGroup) {
					var i = getSelectedRadio(buttonGroup);
					if (i == -1) {
						return "";
					}
					else {
						if (buttonGroup[i]) { // Make sure the button group is an array (not just one button)
							return buttonGroup[i].value;
						}
						else {
							// The button group is just the one button, and it is checked
							return buttonGroup.value;
						}
					}
				}
				
				// -->
			</script>';

if ($compose_type == "html"){
	$output .= '
			<script type="text/javascript" src="htmlarea/htmlarea.js"></script>
			<script type="text/javascript" src="htmlarea/lang/en.js"></script>
			<script type="text/javascript" src="htmlarea/dialog.js"></script>
			<script type="text/javascript" src="htmlarea/popupwin.js"></script>
			<script type="text/javascript">
				var editor = null;
				
				function initEditor() {
					// create an editor for the "message" textbox
					editor = new HTMLArea("message");
					
  					editor.generate();
  					return false;
				}
				
				function insertHTML() {
					var html = prompt("'.ENTER_HTML_CODE.'");
					
  					if (html) {
    					editor.insertHTML(html);
  					}
				}
				
				function highlight() {
  					editor.surroundHTML(\'<span style="background-color: yellow">\', \'</span>\');
				}
			</script>	';
}

$output .= '
			<link rel="stylesheet" type="text/css" href="htmlarea/htmlarea.css" />
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body';

if ($compose_type == "html"){
	$output .= ' onload="initEditor();">
		<script type="text/javascript">
			_editor_url = "htmlarea/"; 
		</script> ';
}
else{
	$output .= '>';
}
$output .= '
			<form action="'.$wrapperpage.'" target="wrapper" method="post" enctype="multipart/form-data" name="mainform" id="mainform">
				<div id="compose">
					<input type="hidden" name="in_reply_to" value="'.$in_reply_to.'" />
					<input type="hidden" name="from" value="'.$_SESSION["toby"]["realname"].' <'.$_SESSION["toby"]["email_address"].'>" />
					<table style="width: 550px; margin: 10px;" cellspacing="1">
						<tr>
							<td style="width: 100px;"><label for="to">'.SEND_TO.':</label></td>
							<td style="width: 450px;"><input type="text" name="to" style="width: 100%;" id="to" value="'.$to.'" /></td>
						</tr>
						<tr>
							<td><label for="cc">'.CC_TO.':</label></td>
							<td><input type="text" name="cc" id="cc" style="width: 100%;" value="'.$cc.'" /></td>
						</tr>
						<tr>
							<td><label for="bcc">'.BCC_TO.':</label></td>
							<td><input type="text" name="bcc" id="bcc" style="width: 100%;" value="'.$bcc.'" /></td>
						</tr>';
		
		if (is_array($addresses)){
			$output .= '
						<tr>
							<td><label for="address_book_select">'.ADDRESS_BOOK.':</label></td>
							<td>
								<select name="address_book_select" id="address_book_select" onchange="document.getElementById(getSelectedRadioValue(to_type)).value += document.getElementById(\'address_book_select\').options[document.getElementById(\'address_book_select\').selectedIndex].value;document.getElementById(\'address_book_select\').selectedIndex = 0;">
									<option value=""></option>';
			
			foreach($addresses as $address){
				$output .= '<option value="'.$address["email_address"].', ">'.$address["name"].' &lt;'.$address["email_address"].'&gt;</option>';
			}
			
			$output .= '
								</select>
								<input type="radio" id="to_type" name="to_type" value="to" checked="checked" /> To
								<input type="radio" id="to_type" name="to_type" value="cc" /> Cc
								<input type="radio" id="to_type" name="to_type" value="bcc" /> Bcc
							</td>
						</tr>';
		}
		
		$output .= '
						<tr>
							<td><label for="subject">'.SUBJECT.':</label></td>
							<td><input type="text" name="subject" id="subject" style="width: 100%;" value="'.$subject.'" /></td>
						</tr>
						<tr>
							<td><nobr><input type="submit" name="'.str_replace(" ","_",ATTACHMENTS).'" value="'.ATTACHMENTS.'" class="button_as_link" />:</nobr></td>
							<td>
								<select name="attachments" id="attachments" style="width: 100%;">';
		
		if(is_array($_SESSION["toby"]["compose"]["attached_files"])){
			foreach($_SESSION["toby"]["compose"]["attached_files"] as $key => $file){
				if ($file["name"] != ''){
					$is = true;
					$output .= '<option value="'.$key.'">'.$file["name"].'</option>';
				}
			}
		}
		else{
			$output .= '<option value="">'.NONE.'</option>';
		}
		
		$output .= '
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="message">'.MESSAGE.':</label></td>
								<td>('.SWITCH_MODE.' ';
		
		if ($compose_type == "html"){
			$output .= '<input class="button_as_link" type="submit" name="'.str_replace(" ","_",TEXT_MODE).'" value="'.TEXT_MODE.'" onclick="document.mainform.compose_type.value = \'text\';" />';
		}
		else{
			$output .= '<input class="button_as_link" type="submit" name="'.str_replace(" ","_",HTML_MODE).'" value="'.HTML_MODE.'" onclick="document.mainform.compose_type.value = \'html\';" />';
		}
		
		$output .= ')
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea name="message" id="message" style="width: 550px; height: 250px;" cols="60" rows="10">'.$body.'</textarea>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="checkbox" name="save_sent" id="save_sent" value="1"'.$checked.' />
								<label for="save_sent">'.SAVE_IN.' </label>
								<select name="save_message_in">
									'.get_folder_dropdown($sent_folder).'
								</select>.
							</td>
						</tr>
						<tr>
							<td style="text-align: center;" colspan="2">
								<input type="hidden" name="compose_type" value="'.$compose_type.'" />
								<input type="submit" name="'.str_replace(" ","_",SEND).'" value="'.SEND.'" />
							</td>
						</tr>
					</table>
				</div>
			</form>
		</body>
	</html>';

echo $output;
exit;

function get_forwarded_attachments($messages){
	if (!is_array($messages)){
		$messages = array($_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$num = num_attachments($message);
		for($i = 1; $i <= $num; $i++){
			$tmp_file = make_tmp_file($i,$message);
			$_SESSION["toby"]["compose"]["attached_files"][] = array("name"=>get_attached_file_name($i, $message),"tmp_name"=>$tmp_file,"mimetype"=>get_file_type($i, $message), "size"=>filesize($tmp_file));
		}
	}
	
	return;
}

function get_ccd($messages){
	## This function gets the cc recipients of a specified message.
	
	$ccs = '';
	
	if (!is_array($messages)){
		$messages = array($_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$query = "SELECT `Cc` FROM `email` WHERE `id`='".$message."'";
		$result = run_query($query);
		$row = mysql_fetch_array($result);
		
		if ((mysql_num_rows($result) > 0) && ($row["Cc"] != "")){
			$ccs .= str_replace(",","",str_replace('"',"",$row["Cc"])) . ', ';
		}
	}
	
	if (strlen($ccs) == 2) return;
	else return $ccs;
}

function get_receivers($messages){
	## This function gets the "To" recipients of a specified message.
	
	$receivers = '';
	
	if (!is_array($messages)){
		$messages = Array($_SESSION["toby"]["lastviewed"]);
	}
	
	foreach($messages as $message){
		$query = "SELECT `To` FROM `email` WHERE `id`='".$message."'";
		$result = run_query($query);
		$row = mysql_fetch_array($result);
		
		if ((mysql_num_rows($result) > 0) && ($row["To"] != "")){
			$receivers .= str_replace(",","",str_replace('"',"",$row["To"])) . ', ';
		}
	}
	
	if (strlen($receivers) == 2) return;
	else return $receivers;
}

function get_reply_message($messages, $type = "text"){
	## This function returns the initial message body for a reply message
	## indented with > characters.
	
	$body = '';
	
	// If the user selected several messages to reply to, use the first message
	// as being replied to.
	if (!is_array($messages)){
		$message = $_SESSION["toby"]["lastviewed"];
	}
	else{
		$message = $messages[0];
	}
	
	if ($type == "text"){
		$body = "\n\n\n";
	}
	elseif ($type == "html"){
		$body = "<br />\n<br />\n<br />\n";
	}
	
	$body .= get_reply_header($message, $type) . get_reply_body($message, $type);
	
	return $body;
}

function get_reply_header($message_id, $type = "text"){
	$query = "SELECT `From`,`To`,`Cc`,`Date`,`Subject` FROM `email` WHERE `id`='".$message_id."'";
	$result = run_query($query);
	
	if (mysql_num_rows($result) == 0){
		return "";
	}
	else{
		$header = "";
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		if ($type == "html"){
			$header .= "----- ".ORIGINAL_MESSAGE." -----<br />\n";
			$header .= FROM.": ".htmlentities(htmlentities($row["From"]))."<br />\n";
			$header .= SEND_TO.": ".htmlentities(htmlentities($row["To"]))."<br />\n";
			$header .= SENT.": ".$row["Date"]."<br />\n";
			$header .= SUBJECT.": ".$row["Subject"]."<br />\n<br />\n";
		}
		else{
			$header .= "----- ".ORIGINAL_MESSAGE." -----\n";
			$header .= FROM.": ".$row["From"]."\n";
			$header .= SEND_TO.": ".$row["To"]."\n";
			$header .= SENT.": ".$row["Date"]."\n";
			$header .= SUBJECT.": ".$row["Subject"]."\n\n";
		}
		
		return $header;
	}
}

function get_reply_body($message_id, $type = "text"){
	if ($type == "html"){
		$body = get_html_body($message_id);
		
		if (strlen(trim($body)) == 0){
			return str_replace("\n","<br />\n",htmlentities(get_reply_body($message_id, "text")));
		}
		else{
			return $body;
		}
	}
	else{
		$wrap_length = 64;
		
		$body .= get_text_body($message_id);
		
		$bodyparts = explode("\n",$body);
		
		// Most of the following is to ensure that the lines
		// end with spaces and not in the middle of words.
		foreach ($bodyparts as $bodypart){
			$length = strlen(trim($bodypart));
			$i = 0;
			$line = $bodypart;
			
			do {
				$templine = substr($bodypart, $i, $wrap_length);
				
				if ($length > $wrap_length){
					for ($j = $i + $wrap_length; $j < $i + $wrap_length + 10; $j++){
						$letter = substr($line, $j, 1);
						if (strlen(trim($letter)) == 0){
							break;
						}
						else{
							$templine .= $letter;
						}
					}
					
					$i = $j;
				}
				else{
					$i = $length;
				}
				
				$newbody .= "> " . trim($templine) ."\n";
			} while ($i < $length);
		}
		
		// Replace two blank indented lines in a row with one until there are no more.
		do{
			$tempbody = str_replace("> \n> \n", "> \n",$newbody);
		} while (($tempbody != $newbody) && ($newbody = $tempbody));
		
		return trim($newbody);
	}
}

function get_subject($messages, $prefix=''){
	## This function returns the subject of a specified message.
	
	if (!is_array($messages)){
		$messages = array((int) $_SESSION["toby"]["lastviewed"]);
	}
	
	$query = "SELECT `Subject` FROM `email` WHERE `id`='".$messages[0]."'";
	$result = run_query($query);
	
	$subject = (mysql_num_rows($result) > 0) ? trim($prefix) . ' ' . mysql_result($result, 0, "Subject") : "";
	
	return $subject;
}

?>