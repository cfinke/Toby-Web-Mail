<?php

// E-mail transfer file
// This file should take care of transferring e-mail between Toby accounts.

error_reporting(0);

include("globals.php");

if ($_REQUEST["action"] == TRANSFER_EMAIL){
	add_share($_REQUEST["to"],$_REQUEST["message"]);
}
elseif($_REQUEST["action"] == CANCEL_TRANSFER){
	decline_share($_REQUEST["transfer_id"]);
}
elseif($_REQUEST["action"] == RETRIEVE_EMAIL){
	$num_messages = copy_share($_REQUEST["transfer_id"]);
	if ($num_messages > 0) $transferred = true;
}
elseif($_REQUEST["action"] == DECLINE){
	decline_share($_REQUEST["transfer_id"]);
}

$output = $transdtd . ' 
	<html>
		<head>
			<title>'.TRANSFER_EMAIL.'</title>
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
			<form action="'.$_SERVER["PHP_SELF"].'" method="post">';
	
	if ($transferred){
		$output .= '<p style="font-weight: bold; margin-left: 10px;">'.TRANSFER_SUCCESS_BEGIN.' '. $num_messages.' '.TRANSFER_SUCCESS_END.'</p>';
	}
	
	$query = "SELECT 
				`a`.`id`,
				`a`.`message`,
				`b`.`email_address`,
				`b`.`realname`
				FROM `email_transfer` AS `a`
					LEFT JOIN `email_users` AS `b` ON `a`.`from` = `b`.`id`
				WHERE `a`.`to` = '".$_SESSION["toby"]["email_address"]."'";
	$result = run_query($query);
	
	if (mysql_num_rows($result) > 0){
		$output .= '
			<table cellspacing="1" cellpadding="0" id="foldertable" style="background: #000000;">
				<tr class="settingsrow_head">
					<td colspan="3">
						'.RETRIEVE_EMAIL.'
					</td>
				</tr>
				<tr class="settingsrow_head">
					<td style="width: 1%;">&nbsp;</td>
					<td style="width: 30%;">'.FROM.'</td>
					<td>'.COMMENTS.'</td>
				</tr>';
		
		while ($row = mysql_fetch_array($result)){
			$output .= '
				<tr class="settingsrow1">
					<td>
						<input type="checkbox" name="transfer_id[]" value="'.$row["id"].'" />
					</td>
					<td>
						'.$row["realname"].' &lt;'.$row["email_address"].'&gt;
					</td>
					<td>
						'.stripslashes($row["message"]).'
					</td>
				</tr>';
		}
		
		$output .= '
				<tr class="settingsrow_head">
					<td colspan="3" style="text-align: center;">
						<input type="submit" name="'.str_replace(" ","_",RETRIEVE_EMAIL).'" value="'.RETRIEVE_EMAIL.'" />
						<input type="submit" name="'.str_replace(" ","_",DECLINE).'" value="'.DECLINE.'" />
					</td>
				</tr>
			</table>';
	}
	
	$output .= '
		<table cellspacing="1" cellpadding="0" id="foldertable">
			<tr class="settingsrow_head">
				<td colspan="3">
					'.TRANSFER_EMAIL.'
				</td>
			</tr>
			<tr class="settingsrow_head">
				<td colspan="3">
					'.TRANSFER_INSTR.'
				</td>
			</tr>';
	
	$query = "SELECT 
				`a`.`id`,
				`a`.`message`,
				`b`.`email_address`,
				`b`.`realname`
				FROM `email_transfer` AS `a`
					LEFT JOIN `email_users` AS `b` ON `a`.`to` = `b`.`email_address`
				WHERE `a`.`from` = ".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	if (mysql_num_rows($result) > 0){
		$output .= '
								<tr class="settingsrow_head">
									<td style="width: 34%;">'.SEND_TO.'</td>
									<td style="width: 66%;">'.COMMENTS.'</td>
								</tr>';
		
		while ($row = mysql_fetch_array($result)){
			$output .= '
				<tr class="settingsrow1">
					<td>
						<input type="hidden" name="transfer_id[]" value="'.$row["id"].'" />
						'.$row["realname"].' &lt;'.$row["email_address"].'&gt
					</td>
					<td>
						'.stripslashes($row["message"]).'
					</td>
				</tr>';
		}
		
		$output .= '	
			<tr class="settingsrow_head">
				<td colspan="3" style="text-align: center;">
					<input type="submit" name="'.str_replace(" ","_",CANCEL_TRANSFER).'" value="'.CANCEL_TRANSFER.'" />
				</td>
			</tr>';
	}
	else{
	
	$output .= '
		<tr class="settingsrow_head">
			<td>&nbsp;</td>
			<td>'.DESTINATION_ADDRESS.'</td>
			<td>'.COMMENTS.'</td>
		</tr>
		<tr class="settingsrow1">
			<td style="text-align: center;"><input type="submit" name="'.str_replace(" ","_",TRANSFER_EMAIL).'" value="'.TRANSFER_EMAIL.'" /></td>
			<td><input type="text" name="to" style="width: 90%;" /></td>
			<td><textarea name="message" rows="2" cols="30" style="width: 90%;"></textarea></td>
		</tr>';
	}
	
	$output .= '	</table>
				</form>
			</body>
		</html>';

echo $output;
exit;

function copy_share($transfer_id){
	if (is_array($transfer_id)){
		foreach ($transfer_id as $share_id){
			$query = "SELECT `id` FROM `email` WHERE `share` = ".((int) $share_id);
			$result = run_query($query);
			$num = mysql_num_rows($result);
			
			$query = "UPDATE `email` SET `user`=".((int) $_SESSION["toby"]["userid"]).",`folder`=0,`share`=0 WHERE `share` = ".((int) $share_id);
			$result = run_query($query);
			
			$query = "UPDATE `email_overflow` SET `user`=".((int) $_SESSION["toby"]["userid"]).",`share`=0 WHERE `share` = ".((int) $share_id);
			$result = run_query($query);
			
			$query = "DELETE FROM `email_transfer` WHERE `id` = ".$share_id;
			$result = run_query($query);
		}
	}
	
	return $num;
}

function decline_share($transfer_id){
	if (is_array($transfer_id)){
		foreach ($transfer_id as $share_id){
			$query = "UPDATE `email` SET `share`=0 WHERE `share` = ".((int) $share_id);
			$result = run_query($query);
			
			$query = "UPDATE `email_overflow` SET `share`=0 WHERE `share` = ".((int) $share_id);
			$result = run_query($query);
			
			$query = "DELETE FROM `email_transfer` WHERE `id` = ".$share_id;
			$result = run_query($query);
		}
	}
	
	return;
}

function add_share($to, $message){
	$query = "INSERT INTO `email_transfer` (`to`,`message`,`from`) VALUES ('".addslashes($to)."','".addslashes($message)."',".$_SESSION["toby"]["userid"].")";
	$result = run_query($query);
	$share_id = mysql_insert_id();
	
	$query = "UPDATE `email` SET `share`=".$share_id." WHERE `user` = ".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	$query = "UPDATE `email_overflow` SET `share`=".$share_id." WHERE `user` = ".$_SESSION["toby"]["userid"];
	$result = run_query($query);
	
	return;
}

?>