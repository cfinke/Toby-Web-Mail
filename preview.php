<?php

// Preview listing file
// This file should take care of the preview listings frame.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

$onload = '';
$num_rows = 0;
$orderbys = array("From","Subject","To","niceDate");
$directions = array("ASC","DESC");
$message_rows = array();

if (!isset($_REQUEST["orderby"]) || (!in_array($_REQUEST["orderby"], $orderbys))){
	unset($_REQUEST["orderby"]);
	$_REQUEST["orderby"] = "niceDate";
}
if (!isset($_REQUEST["direction"]) || (!in_array($_REQUEST["direction"], $directions))){
	unset($_REQUEST["direction"]);
	$_REQUEST["direction"] = "DESC";
}

switch ($_REQUEST["action"]){
	case 'trash':
	case EMPTY_TRASH:
	case UNDELETE:
		$message_rows = get_message_rows("trash",'',$_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
	case 'viewbysender':
		$message_rows = get_message_rows("sender",$_REQUEST["sender"],$_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
	case 'viewbyreceiver':
		$message_rows = get_message_rows("receiver",$_REQUEST["receiver"],$_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
	case 'viewbyfolder':
		$message_rows = get_message_rows("folder",$_REQUEST["folder"],$_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
	case 'viewbymonth':
		$message_rows = get_message_rows("month",$_REQUEST["month"],$_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
	case DELETE_STRING:
		$_REQUEST["action"] = $_REQUEST["oldaction"];
	case SEND:
	default:
		$num_new = download_messages();
		
		if ($_SESSION["toby"]["refresh_interval"] > 0){
			$meta = '<meta http-equiv="refresh" content="'.($_SESSION["toby"]["refresh_interval"] * 60).'; url='.$_SERVER["PHP_SELF"].'?refresh=1">';
		}
		
		if (isset($_REQUEST["refresh"]) && ($num_new > 0)){
			$onload = ' onload="alert(\'You have '.$num_new.' new message';
			if ($num_new > 1) $onload .= 's';
			$onload .= '.\');"';
		}
		
		unset($_REQUEST["folder"]);
		$message_rows = get_message_rows("inbox", '', $_REQUEST["orderby"],$_REQUEST["direction"]);
		break;
}

$orderbystring = build_query_string($_REQUEST);

$output = $transdtd . '
	<html>
		<head>
			<title>'.MESSAGES.'</title>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
			<script type="text/javascript">
				<!--
				
				image1 = new Image();
				image1.src = "images/expand.gif";
				
				function check_boxes() {
					var state = document.mainform.checkall.checked;
					for (i = 0; i < document.mainform.elements.length; i++){
						if (document.mainform.elements[i].type == \'checkbox\'){
							document.mainform.elements[i].checked = state;
						}
					}
				}
				
				var numTr = '.$num_rows.';
				
				function over(strId) {
					for(i=1;i<=numTr;i++){
						document.getElementById(\'message_row_\' + i).className = "row_unselected";
					}
					
					strId.className = "row_selected";
				}
				
				function hide(begin, end){
					for (i = begin; i <= end; i++){
						var display_mode = document.getElementById(\'message_row_\' + i).style.display;
						
						if (display_mode == "none"){
							document.getElementById(\'message_row_\' + i).style.display = "";
							document.getElementById(\'header_\' + begin).src = \'images/collapse.gif\';
						}
						else{
							document.getElementById(\'message_row_\' + i).style.display = "none";
							document.getElementById(\'header_\' + begin).src = \'images/expand.gif\';
						}
					}
				}
				
				// -->
			</script>
			'.$meta.'
		</head>
		<body'.$onload.'>
			<form action="'.$wrapperpage.'" target="_parent" method="post" enctype="multipart/form-data" name="mainform" id="mainform">
				<div id="nav">';

if (isset($_REQUEST["sender"])) $output .= '<input type="hidden" name="sender" value="'.$_REQUEST["sender"].'" />';
if (isset($_REQUEST["folder"])) $output .= '<input type="hidden" name="folder" value="'.$_REQUEST["folder"].'" />';
else $_REQUEST["folder"] = 0;
if (isset($_REQUEST["action"])) $output .= '<input type="hidden" name="oldaction" value="'.$_REQUEST["action"].'" />';

$output .= '
					<input type="submit" name="'.str_replace(" ","_",INBOX).'" value="'.INBOX.'" class="image_submit" style="background-image: url(\'images/inbox.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",COMPOSE).'" value="'.COMPOSE.'" class="image_submit" style="background-image: url(\'images/compose.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",REPLY).'" value="'.REPLY.'" class="image_submit" style="background-image: url(\'images/reply.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",REPLY_TO_ALL).'" value="'.REPLY_TO_ALL.'" class="image_submit" style="background-image: url(\'images/replytoall.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",FORWARD).'" value="'.FORWARD.'" class="image_submit" style="background-image: url(\'images/forward.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",OPTIONS).'" value="'.OPTIONS.'" class="image_submit" style="background-image: url(\'images/settings.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",ADDRESS_BOOK).'" value="'.ADDRESS_BOOK.'" class="image_submit" style="background-image: url(\'images/addresses.gif\');" />';
	
	if (($_REQUEST["action"] == "trash") || ($_REQUEST["action"] == EMPTY_TRASH) || ($_REQUEST["action"] == UNDELETE)){
		$output .= ' <input type="submit" name="'.str_replace(" ","_",UNDELETE).'" value="'.UNDELETE.'" class="image_submit" style="background-image: url(\'images/undelete.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",EMPTY_TRASH).'" value="'.EMPTY_TRASH.'" class="image_submit" style="background-image: url(\'images/emptytrash.gif\');" /> ';
	}
	else{
		$output .= ' <input type="submit" name="'.str_replace(" ","_",DELETE_STRING).'" value="'.DELETE_STRING.'" class="image_submit" style="background-image: url(\'images/delete.gif\');" /> ';
	}
	
	$output .= '<input type="submit" name="'.str_replace(" ","_",LOG_OUT).'" value="'.LOG_OUT.'" class="image_submit" style="background-image: url(\'images/logout.gif\');" />';
	
	if (($_REQUEST["action"] != "trash") && ($_REQUEST["action"] != EMPTY_TRASH) && ($_REQUEST["action"] != UNDELETE)){
		$output .= '
			<br />
			<input type="submit" name="'.str_replace(" ","_",MOVE).'" value="'.MOVE.'" class="text_button" />
			<select name="movefolder" style="width: 540px;">
				<option value="0">'.SELECT_NEW_LOCATION.'</option>
				'.get_folder_dropdown($_REQUEST["folder"], true).'
			</select>';
	}
	
	$output .= '</div>
				<div id="messages">
					<table cellspacing="0">
						<tr class="row1">
							<td class="checkbox"><input type="checkbox" name="checkall" value="x" onclick="check_boxes();" /></td>
							<td class="date"><a class="unseen" href="'.$orderbystring.'&amp;orderby=niceDate&amp;direction=';
	
	$output .= (($_REQUEST["orderby"] == "niceDate") && ($_REQUEST["direction"] == "DESC")) ? 'ASC' : 'DESC';
	
	$output .= '"><b>'.DATE_STRING.'</b></a></td>
							<td class="from"><a class="unseen" href="'.$orderbystring.'&amp;orderby=From&amp;direction=';
	
	$output .= (($_REQUEST["orderby"] == "From") && ($_REQUEST["direction"] == "ASC")) ? 'DESC' : 'ASC';
	
	$output .= '"><b>'.FROM.'</b></a></td>
							<td class="attachment">&nbsp;</td>
							<td class="subject"><a class="unseen" href="'.$orderbystring.'&amp;orderby=Subject&amp;direction=';
	
	$output .= (($_REQUEST["orderby"] == "Subject") && ($_REQUEST["direction"] == "ASC")) ? 'DESC' : 'ASC';
	
	$output .= '"><b>'.SUBJECT.'</b></a></td>
							<td class="to"><a class="unseen" href="'.$orderbystring.'&amp;orderby=To&amp;direction=';
	
	$output .= (($_REQUEST["orderby"] == "To") && ($_REQUEST["direction"] == "ASC")) ? 'DESC' : 'ASC';
	
	$output .= '"><b>'.SEND_TO.'</b></a></td>
							
						</tr>
						'.$message_rows.'
					</table>
				</div>
			</form>
		</body>
	</html>';

echo $output;
exit;

function get_message_rows($type, $subtype = '', $orderby = "niceDate", $direction = "DESC"){
	global $messagepage;
	global $num_rows;
	
	$counter = 0;
	$output = '';
	
	switch ($type){
		case 'inbox':
			$clause = " AND `folder` = 0 AND `user`=".$_SESSION["toby"]["userid"]." ";
			break;
		case 'sender':
			$clause = " AND `From` LIKE '".$subtype."' AND `user`=".$_SESSION["toby"]["userid"]." ";
			break;
		case 'receiver':
			$clause = " AND `To` LIKE '".$subtype."' AND `user`=".$_SESSION["toby"]["userid"]." ";
			break;
		case 'trash':
			$clause = " AND `user` = -".$_SESSION["toby"]["userid"]." ";
			break;
		case 'folder':
			$clause = " AND `folder` = ".$subtype." AND `user`=".$_SESSION["toby"]["userid"]." ";
			break;
		case 'month':
			$clause = " AND `niceDate` LIKE '".$subtype."%' AND `user`=".$_SESSION["toby"]["userid"]." ";
			break;
	}
	
	$query = "SELECT *, UNIX_TIMESTAMP(`niceDate`) as `unix_time` FROM `email` WHERE 1 ".$clause." ORDER BY `".$orderby."` ".$direction;
	$result = run_query($query);
	
	$num_rows = mysql_num_rows($result);
	$today = date("Y m d");
	$old_rows = '';
	
	$begin = 0;
	$end = 0;
	
	if ($num_rows > 0){
		while($row = mysql_fetch_array($result)){
			$message = get_message_array($row, $counter);
			$counter = $message["num"];
			
			switch($orderby){
				case 'niceDate':
					if (isset($current_date) && ($current_date != $message["date"])){
						if ($old_rows != ''){
							$output .= '
								<tr class="date_header" style="display: auto;">
									<td style="text-align: center;">
										<a href="javascript:void(0);" onclick="hide('.$begin.','.($message["num"] - 1).');"><img src="images/collapse.gif" id="header_'.$begin.'" /></a>
									</td>
									<td colspan="6"><b>'.date("l F j, Y", $unix_time).'</b></td>
								</tr>';
							
							$output .= $old_rows;
							$old_rows = '';
						}
						
						$begin = $message["num"];
						$unix_time = $message["unix_time"];
					}
					break;
				case 'From':
				case 'To':
				case 'Subject':
					if ($current_letter != strtoupper(substr($message[$orderby], 0, 1))){
						if ($old_rows != ''){
							$output .= '
								<tr class="date_header" style="display: auto;">
									<td style="text-align: center;">
										<a href="javascript:void(0);" onclick="hide('.$begin.','.($message["num"] - 1).');"><img src="images/collapse.gif" id="header_'.$begin.'" /></a>
									</td>
									<td colspan="6"><b>'.$current_letter.'</b></td>
								</tr>';
							
							$output .= $old_rows;
							$old_rows = '';
						}
						
						$begin = $message["num"];
					}
					break;
				default:
					$output .= $old_rows;
					$old_rows = '';
					break;
			}
			
			$old_rows .= make_message_row($message);
			
			if ($orderby == "niceDate"){
				$current_date = $message["date"];
			}
			else{
				$current_letter = strtoupper(substr($message[$orderby], 0, 1));
			}
		}
		
		switch($orderby){
			case 'niceDate':
				$output .= '
					<tr class="date_header" style="display: auto;">
						<td style="text-align: center;">
							<a href="javascript:void(0);" onclick="hide('.$begin.','.$message["num"].');"><img src="images/collapse.gif" id="header_'.$begin.'" /></a>
						</td>
						<td colspan="6"><b>'.date("l F j, Y", $message["unix_time"]).'</b></td>
					</tr>' . $old_rows;
					break;
			case 'From':
			case 'To':
			case 'Subject':
				$output .= '
					<tr class="date_header" style="display: auto;">
						<td style="text-align: center;">
							<a href="javascript:void(0);" onclick="hide('.$begin.','.$message["num"].');"><img src="images/collapse.gif" id="header_'.$begin.'" /></a>
						</td>
						<td colspan="6"><b>'.$current_letter.'</b></td>
					</tr>' . $old_rows;
					break;
			default:
				$output .= $old_rows;
				$old_rows = '';
				break;
		}
	}
	else{
		$output .= '<tr><td colspan="6" style="text-align: center; padding: 15px; width: 100%;">'.NO_MESSAGES.'</td></tr>';
	}
	
	return $output;
}

function get_message_array($row, $counter){
	global $messagepage;
	
	$from_length = 25;
	$subject_length = 35;
	$to_length = 25;
	
	$nicefrom = get_nice_sender($row["From"]);
	$niceto = get_nice_sender($row["To"]);
	
	$message["num"] = ++$counter;
	$message["id"] = $row["id"];
	$message["attachment"] = ($row["num_attachments"] > 0) ? '<img src="images/attachsmall.gif" alt="'.ATTACHMENT.'"/>' : '&nbsp;';
	$message["subject"] = (strlen(trim($row["Subject"])) == 0) ? '['.NO_SUBJECT.']' : $row["Subject"];
	$message["viewlink"] = $messagepage.'?id='.$row["id"];
	$message["viewlink"] .= ($row["has_html"] == 1) ? '&amp;action=view_html' : '&amp;action=view';
	$message["date"] = substr($row["niceDate"],0,4).' '.substr($row["niceDate"],4,2).' '.substr($row["niceDate"],6,2);
	$message["timestamp"] = $row["niceDate"];
	$message["unix_time"] = $row["unix_time"];
	$message["To"] = $row["To"];
	$message["From"] = $row["From"];
	$message["Subject"] = $row["Subject"];
	
	$message["from"] = '<abbr title="'.htmlentities($row["From"]).'">';
	if (strlen($nicefrom) > $from_length){
		$message["from"] .= substr($nicefrom, 0, $from_length - 2) . '...';
	}
	else{
		$message["from"] .= $nicefrom;
	}
	$message["from"] .= '</abbr>';
	
	$message["to"] = '<abbr title="'.htmlentities($row["To"]).'">';
	if (strlen($niceto) > $to_length){
		$message["to"] .= substr($niceto, 0, $to_length - 2) . '...';
	}
	else{
		$message["to"] .= $niceto;
	}
	$message["to"] .= '</abbr>';
	
	if (strlen($message["subject"]) > $subject_length){
		$message["subject"] = '<abbr title="'.$message["subject"].'">'.substr($message["subject"],0,$subject_length - 2).'...</abbr>';
	}
	
	$message["seen"] = $row["seen"];
	
	return $message;
}

function make_message_row($message){
	## This function creates a listing row for the specified $message.
	
	$class = ($message["seen"]) ? 'seen' : 'unseen';
	
	$meridian = (substr($message["timestamp"],8,2) > 11) ? 'PM' : 'AM';
	$time = (((substr($message["timestamp"],8,2) - 1) % 12) + 1);
	if ($time == '0') $time = '12';
	$time .= ':' . substr($message["timestamp"],10,2) . ' ' . $meridian;
	
	$row = '
		<tr class="row_unselected" id="message_row_'.$message["num"].'" onclick="over(this);" style="display: auto;">
			<td class="checkbox"><input type="checkbox" name="dmsg[]" value="'.$message["id"].'" /></td>
			<td class="date"><abbr title="'.date("l F j, Y",$message["unix_time"]).' '.$time.'">'.$time.'</abbr></td>
			<td class="from">'.$message["from"].'&nbsp;</td>
			<td class="attachment">'.$message["attachment"].'</td>
			<td class="subject"><a href="'.$message["viewlink"].'" target="message" class="'.$class.'">'.$message["subject"].'</a></td>
			<td class="to">'.$message["to"].'</td>
		</tr>';
	
	return $row;
}

?>