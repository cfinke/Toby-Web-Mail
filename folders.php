<?php

// Message Navigation File
// This file should take care of anything in the leftmost frame, used
// for navigating through the messages.

include("globals.php");

// The default method of navigation is folder-based.
if (!$_REQUEST["method"]){
	$_REQUEST["method"] = "folders";
}

$highest_folder = '0';

if ($_REQUEST["method"] == "from"){
	$query = "SELECT `From` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `From` != '' GROUP BY `From`";
	$result = run_query($query);
	$highest_folder = mysql_num_rows($result);
	
	$message_nav = get_email_by_sender();
}
elseif ($_REQUEST["method"] == "to"){
	$query = "SELECT `To` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `To` != '' GROUP BY `To`";
	$result = run_query($query);
	$highest_folder = mysql_num_rows($result);
	
	$message_nav = get_email_by_receiver();
}
elseif ($_REQUEST["method"] == "folders"){
	$query = "SELECT `id` FROM `email_folders` WHERE `user`='".$_SESSION["toby"]["userid"]."' ORDER BY `id` DESC LIMIT 1";
	$result = run_query($query);
	$highest_folder = mysql_result($result,0,'id');
	
	$message_nav = get_email_by_folder();
}
elseif ($_REQUEST["method"] == "date"){
	$query = "SELECT SUBSTRING(`niceDate`,1,6) AS `category` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." GROUP BY `category`";
	$result = run_query($query);
	$highest_folder = mysql_num_rows($result);
	
	$message_nav = get_email_by_date();
}

$output = $transdtd.'
	<html>
		<head>
			<title>'.FOLDERS.'</title>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
			<script type="text/javascript">
				<!--
					
					var numTr = '.$highest_folder.';
					
					function over(strId) {
						for(i=1;i<=numTr;i++){
							if (document.getElementById(\'folder_\' + i)){
								document.getElementById(\'folder_\' + i).className = "folder_unselected";
							}
						}
						
						document.getElementById(\'inbox_folder\').className = "folder_unselected";
						document.getElementById(\'trash_folder\').className = "folder_unselected";
						
						strId.className = "folder_selected";
					}
					
					function hide(listID){
						var display_mode = document.getElementById(\'folder_list_\' + listID).style.display;
						
						if (display_mode == "none"){
							document.getElementById(\'folder_list_\' + listID).style.display = "block";
							document.getElementById(\'list_image_\' + listID).src = \'images/collapse.gif\';
						}
						else{
							document.getElementById(\'folder_list_\' + listID).style.display = "none";
							document.getElementById(\'list_image_\' + listID).src = \'images/expand.gif\';
						}
					}
				//-->
			</script>
		</head>
		<body>
			<div id="foldertype">
				<form name="folderview" id="folderview" action="'.$_SERVER["PHP_SELF"].'" method="post">
					'.VIEW_BY.' 
					<select name="method" onchange="document.folderview.submit();return true;">
						<option value="folders"';if($_REQUEST["method"] == "folders") $output.= ' selected="selected" ';$output.= '>'.FOLDERS.'</option>
						<option value="from"';if($_REQUEST["method"] == "from") $output.= ' selected="selected" ';$output.= '>'.SENDER.'</option>
						<option value="to"';if($_REQUEST["method"] == "to") $output.= ' selected="selected" ';$output.= '>'.RECEIVER.'</option>
						<option value="date"';if($_REQUEST["method"] == "date") $output.= ' selected="selected" ';$output.= '>'.DATE_STRING.'</option>
					</select>
				</form>
			</div>
			<div id="folders">'.$message_nav.'</div>
		</body>
	</html>';

echo $output;
exit;

function get_main_folders(){
	global $wrapperpage;
	
	$output = '';
	
	$output .= '
			<span class="folder_selected" id="inbox_folder" style="padding-left: 2px; display: block;" onclick="over(this);"> <img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />  <a href="'.$wrapperpage.'" target="wrapper">'.INBOX.'</a></span>
			<span class="folder_unselected" id="trash_folder" style="padding-left: 2px; display: block;" onclick="over(this);"> <img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />  <a href="'.$wrapperpage.'?action=trash" target="wrapper">'.TRASH.'</a> [<a href="'.$wrapperpage.'?action='.urlencode(EMPTY_TRASH).'" target="wrapper">'.EMPTY_TRASH.'</a>]</span>';
	
	return $output;
}

function get_email_by_folder(){
	## This function returns an alphabetized, unordered list of all
	## the folders.
	
	## The pop inbox is listed at the top of the list.
	global $wrapperpage;
	
	$output = '
		<div id="folderlist">
			'.get_main_folders().'
			'.get_folder_sublist(0).'
		</div>';
	
	return $output;
}

function get_folder_sublist($id = 0, $pre = ""){
	global $wrapperpage;
	global $highest_folder;
	
	$query = "SELECT * FROM `email_folders` WHERE `parent_id`=".$id." AND `user`=".$_SESSION["toby"]["userid"]." ORDER BY `folder_name`";
	$result = run_query($query);
	
	if ($id != 0){
		$newquery = "SELECT `folder_name` FROM `email_folders` WHERE `id`=".$id." AND `user`=".$_SESSION["toby"]["userid"];
		$newresult = run_query($newquery);
		$folder_name = mysql_result($newresult, 0, 'folder_name');
		$pre .= '&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	
	$list = '';
	
	if (mysql_num_rows($result) > 0){
		while ($row = mysql_fetch_array($result)){
			$next_child = get_first_child_id($row["id"]);
			
			$list .= '<span class="folder_unselected" id="folder_'.$row["id"].'" onclick="over(this);" style="padding-left: 2px; display: block;"> '.$pre;
			
			if ($next_child != -1){
				$list .= '<a href="javascript:void(0);" onclick="hide('.$next_child.');"><img src="images/collapse.gif" id="list_image_'.$next_child.'" style="width: 9px; height: 9px;" alt="+/-" /></a> ';
			}
			else{
				$list .= '<img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" /> ';
			}
			
			$list .= '<a href="'.$wrapperpage.'?action=viewbyfolder&amp;folder='.$row["id"].'" target="wrapper">'.htmlentities($row["folder_name"]).'</a></span>';
			
			if ($next_child != -1){
				$list .= '<div id="folder_list_'.$next_child.'">';
				$list .= get_folder_sublist($row["id"], $pre);
				$list .= '</div>';
			}
		}
	}
	
	return $list;
}

function get_first_child_id($id){
	$query = "SELECT `id` FROM `email_folders` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `parent_id`=".$id." ORDER BY `folder_name` LIMIT 1";
	$result = run_query($query);
	$row = mysql_fetch_array($result);
	
	if (mysql_num_rows($result) > 0){
		return $row["id"];
	}
	else{
		return -1;
	}
}

function get_email_by_sender(){
	## This function returns an alphabetized, unordered list of all
	## the people who have sent e-mail that is in the archive.
	
	## The pop inbox is listed at the top of the list.
	
	global $wrapperpage;
	
	// Get a listing of all archived e-mails grouped by sender.
	$query = "SELECT `From`, COUNT(`From`) as `num_sent` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `From` != '' GROUP BY `From` ORDER BY `From` ASC";
	$result = run_query($query);
	
	$list = '<div id="folderlist">';
	$list .= get_main_folders();
	
	// Loop through the e-mails and add each sender to the list
	while($row = mysql_fetch_array($result)){
		$list .= '<span style="padding-left: 2px;" id="folder_'.++$counter.'" class="folder_unselected" onclick="over(this);"> <img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />  <a href="'.$wrapperpage.'?action=viewbysender&amp;sender='.urlencode($row["From"]).'" title="'.$row["num_sent"].' messages" target="wrapper">'.htmlentities($row["From"]).'</a></span>';
	}
	
	$list .= '</div>';
	
	return $list;
}

function get_email_by_receiver(){
	## This function returns an alphabetized, unordered list of all
	## the people who have sent e-mail that is in the archive.
	
	## The pop inbox is listed at the top of the list.
	
	global $wrapperpage;
	
	// Get a listing of all archived e-mails grouped by sender.
	$query = "SELECT `To`, COUNT(`To`) as `num_sent` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." AND `To` != '' GROUP BY `To` ORDER BY `To` ASC";
	$result = run_query($query);
	
	$list = '<div id="folderlist">';
	$list .= get_main_folders();
	
	// Loop through the e-mails and add each sender to the list
	while($row = mysql_fetch_array($result)){
		$list .= '<span style="padding-left: 2px;" id="folder_'.++$counter.'" class="folder_unselected" onclick="over(this);"> <img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />  <a href="'.$wrapperpage.'?action=viewbyreceiver&amp;receiver='.urlencode($row["To"]).'" title="'.$row["num_sent"].' messages" target="wrapper">'.htmlentities($row["To"]).'</a></span>';
	}
	
	$list .= '</div>';
	
	return $list;
}

function get_email_by_date(){
	## The pop inbox is listed at the top of the list.
	
	global $wrapperpage;
	global $months;
	
	// Get a listing of all archived e-mails grouped by receiver.
	$query = "SELECT SUBSTRING(`niceDate`,1,6) AS `category`, SUBSTRING(`niceDate`,1,4) AS `year`, SUBSTRING(`niceDate`,5,2) AS `month`,COUNT(`id`) AS `num_messages` FROM `email` WHERE `user`=".$_SESSION["toby"]["userid"]." GROUP BY `category` ORDER BY `year` DESC, `month` DESC";
	$result = run_query($query);
	
	$emailtree = '<div id="folderlist">';
	$emailtree .= get_main_folders();
	
	// Loop through the e-mails and add each sender to the list
	while($row = mysql_fetch_array($result)){
		$niceMonth = $months[($row["month"] / 1)];
		
		$emailtree .= '<span style="padding-left: 2px;" id="folder_'.++$counter.'" class="folder_unselected" onclick="over(this);"> <img src="images/bullet.gif" style="width: 9px; height: 9px;" alt="*" />  <a href="'.$wrapperpage.'?action=viewbymonth&amp;month='.$row["category"].'" target="wrapper" title="'.$row["num_messages"].' messages">' . $row["year"] . ' ' . $niceMonth .'</a></span>';
	}
	
	$emailtree .= '</div>';
	
	return $emailtree;
}

?>