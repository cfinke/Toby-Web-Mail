<?php

// Address Book File
// This file should take care of anything having to do with managing
// the address book.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

// Take care of any actions from submitted forms.
switch($_REQUEST["action"]){
	case ADD_ADDRESS:
		// Add an address to the address book.
		$query = "INSERT INTO `email_address_book` (`userid`,`name`,`email_address`) VALUES (".$_SESSION["toby"]["userid"].",'".$_REQUEST["name"]."','".$_REQUEST["email_address"]."')";
		$result = run_query($query);
		break;
	case UPDATE_ADDRESS:
		$query = "UPDATE `email_address_book` SET `name`='".$_REQUEST["name"]."',`email_address`='".$_REQUEST["email_address"]."' WHERE `id`='".$_REQUEST["id"]."'";
		$result = run_query($query);
		break;
	case DELETE_SELECTED_ADDRESSES:
		// Delete selected addresses from the address book.
		if (is_array($_REQUEST["addresses"])){
			foreach($_REQUEST["addresses"] as $address_id){
				$query = "DELETE FROM `email_address_book` WHERE `id`=".$address_id;
				$result = run_query($query);
			}
		}
		
		break;
	case 'edit':
		$query = "SELECT * FROM `email_address_book` WHERE `id`='".$_REQUEST["id"]."'";
		$result = run_query($query);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$address = $row["email_address"];
		$row_name = $row["name"];
		break;
	default:
		$address = '';
		$row_name = '';
		break;
}

$output = $transdtd . '
	<html>
		<head>
			<title>'.ADDRESS_BOOK.'</title>
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
		<form action="'.$_SERVER["PHP_SELF"].'" method="post">
		<table style="width: 90%; margin: 3%; background: #000000;" cellspacing="1">
			<tr class="settingsrow_head">
				<td colspan="2">
					'.ADD_AN_ADDRESS.'
				</td>
			</tr>
			<tr class="settingsrow1">
				<td>
					'.NAME.':
				</td>
				<td>
					<input type="text" name="name" value="'.$row_name.'" />
				</td>
			</tr>
			<tr class="settingsrow2">
				<td>
					'.EMAIL_ADDRESS.':
				</td>
				<td>
					<input type="text" name="email_address" value="'.$address.'" />
				</td>
			</tr>
			<tr class="settingsrow_head">
				<td colspan="2" style="text-align: center;">';

if ($_REQUEST["action"] == "edit"){
	$output .= '
		<input type="hidden" name="id" value="'.$_REQUEST["id"].'" />
		<input type="submit" name="action" value="'.UPDATE_ADDRESS.'" />';
}
else{
	$output .= '<input type="submit" name="action" value="'.ADD_ADDRESS.'" />';
}

$output .= '
				</td>
			</tr>
		</table>
	</form>';

// Get all addresses for this user
$query = "SELECT * FROM `email_address_book` WHERE `userid`=".$_SESSION["toby"]["userid"]." ORDER BY `name`";
$result = run_query($query);

if (mysql_num_rows($result) > 0){
	$output .= '
		<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="mainform" id="mainform">
			<table style="width: 90%; margin: 3%; background: #000000;" cellspacing="1">
				<tr class="settingsrow_head">
					<td>
						<input type="checkbox" name="checkall" value="x" onclick="check_boxes();" />
					</td>
					<td colspan="2">
						'.ADDRESS_BOOK.'
					</td>
				</tr>';
	
	// Display each address as a row in the table.
	while ($row = mysql_fetch_array($result)){
		$output .= '
			<tr class="settingsrow'.(($i++ % 2) + 1).'">
				<td style="width: 3%;">
					<input type="checkbox" name="addresses[]" value="'.$row["id"].'" />
				</td>
				<td>
					<a href="'.$_SERVER["PHP_SELF"].'?action=edit&amp;id='.$row["id"].'" style="color: #000000; font-weight: bold;">'.$row["name"].'</a>
				</td>
				<td>
					'.htmlentities($row["email_address"]).'
				</td>
			</tr>';
	}
	
	$output .= '
						<tr class="settingsrow_head">
							<td colspan="4" style="text-align: center;">
								<input type="submit" name="action" value="'.DELETE_SELECTED_ADDRESSES.'" />
							</td>
						</tr>
					</table>
				</form>';
}

$output .= '
		</html>
	</body>';

echo $output;
exit;

?>