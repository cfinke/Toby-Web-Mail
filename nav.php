<?php

// Navigation file
// This file should take care of the icon-based navigation.

include("globals.php");

$output = $transdtd . '
	<html>
		<head>
			<title>'.NAVIGATION.'</title>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body>
			<form action="'.$wrapperpage.'" target="wrapper" method="post" enctype="multipart/form-data" name="mainform" id="mainform">
				<div id="nav">
					<input type="hidden" name="oldaction" value="'.$_REQUEST["action"].'" />
					<input type="submit" name="'.str_replace(" ","_",INBOX).'" value="'.INBOX.'" class="image_submit" style="background-image: url(\'images/inbox.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",COMPOSE).'" value="'.COMPOSE.'" class="image_submit" style="background-image: url(\'images/compose.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",REPLY).'" value="'.REPLY.'" class="image_submit" style="background-image: url(\'images/reply.gif\');" disabled="disabled" />
					<input type="submit" name="'.str_replace(" ","_",REPLY_TO_ALL).'" value="'.REPLY_TO_ALL.'" class="image_submit" style="background-image: url(\'images/replytoall.gif\');" disabled="disabled" />
					<input type="submit" name="'.str_replace(" ","_",FORWARD).'" value="'.FORWARD.'" class="image_submit" style="background-image: url(\'images/forward.gif\');" disabled="disabled" />
					<input type="submit" name="'.str_replace(" ","_",OPTIONS).'" value="'.OPTIONS.'" class="image_submit" style="background-image: url(\'images/settings.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",ADDRESS_BOOK).'" value="'.ADDRESS_BOOK.'" class="image_submit" style="background-image: url(\'images/addresses.gif\');" />
					<input type="submit" name="'.str_replace(" ","_",DELETE_STRING).'" value="'.DELETE_STRING.'" class="image_submit" style="background-image: url(\'images/delete.gif\');" disabled="disabled" />
					<input type="submit" name="'.str_replace(" ","_",LOG_OUT).'" value="'.LOG_OUT.'" class="image_submit" style="background-image: url(\'images/logout.gif\');" />
				</div>';
		
		if ($_REQUEST["action"] == OPTIONS){
			$output .= '
				<div id="options_nav">
					<a href="options.php" target="options_subpage">'.OPTIONS.'</a> - 
					<a href="foldersettings.php" target="options_subpage">'.FOLDERS.'</a> - 
					<a href="transfer.php" target="options_subpage">'.TRANSFER_EMAIL.'</a> - 
					<a href="downloadmessages.php" target="options_subpage">'.DOWNLOAD_MESSAGES.'</a> - 
					<a href="uploadmessages.php" target="options_subpage">'.UPLOAD_MESSAGES.'</a>
				</div>';
		}
		
		$output .= '
			</form>
		</body>
	</html>';

echo $output;
exit;

?>