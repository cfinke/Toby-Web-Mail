<?php

// Message Upload File
// This file should take care of anything having to do with uploading
// message files into Toby.

error_reporting(E_ALL ^ E_NOTICE);

include("globals.php");

if ($_REQUEST["action"] == UPLOAD_MESSAGE){
	if (save_message($_FILES["emailfile"])){
		$status = '<p style="margin: 4%; font-weight: bold;">'.UPLOAD_SUCCESS.'</p>';
	}
	else{
		$status = '<p style="margin: 4%; font-weight: bold; color: #aa0000;">'.UPLOAD_ERROR.'</p>';
	}
}
else{
	$status = '';
}

$output .= $transdtd . '
	<html>
		<head>
			<title>'.UPLOAD_MESSAGES.'</title>
			<script type="text/javascript">
				<!-- 
				function refresh_folderlist(){
					parent.folders.location.href = "'.$folderpage.'?method=folders";
				}
				// -->
			</script>
			<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />
		</head>
		<body>
			<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">
				'.$status.'
				<table cellspacing="1" cellpadding="0" id="foldertable">
					<tr class="settingsrow_head">
						<td colspan="2">
							'.UPLOAD_EMAIL.'
						</td>
					</tr>
					<tr class="settingsrow1">
						<td style="text-align: center;">
							<input type="submit" name="'.str_replace(" ","_",UPLOAD_MESSAGE).'" value="'.UPLOAD_MESSAGE.'" />
						</td>
						<td>
							<input type="file" name="emailfile" />
						</td>
					</tr>
				</table>
			</form>
		</body>
	</html>';

echo $output;

?>