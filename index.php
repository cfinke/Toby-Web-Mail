<?php

// Login page

include("config.php");

// If the cookies are set, log the user in automatically.
if (isset($_COOKIE["toby_email"]) && isset($_COOKIE["toby_pass"])){
	header("Location: client.php?_r=t");
	exit;
}

$output = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html>
		<head>
			<title>'.APP_TITLE.'</title>
			<script type="text/javascript">
				function toppify(){
					if (top.frames.length != 0) {
					    top.location = "index.php";
				    }
				}
				
				function set_focus(){
					document.forms.loginform.user.focus();
				}
			</script>
			<link rel="stylesheet" type="text/css" href="style.css" />
		</head>
		<body onload="toppify();set_focus();">
			<form action="client.php" method="post" name="loginform" id="loginform">
				<h1 style="text-align: center; margin-bottom: 50px;">'.APP_TITLE.'</h1>
				<div id="login">
					<center>
						<table>
							<tr>
								<td class="formlabel">
									<label for="user">'.EMAIL_ADDRESS.':</label>
								</td>
								<td class="forminput">
									<input type="text" name="user" />
								</td>
							</tr>
							<tr>
								<td class="formlabel">
									<label for="pass">'.PASSWORD.':</label>
								</td>
								<td class="forminput">
									<input type="password" name="pass" />
								</td>
							</tr>
							<tr>
								<td class="formlabel" style="text-align: right;">
									<input type="checkbox" id="remember" name="remember" value="yes" />
								</td>
								<td class="forminput">
									<label for="remember">'.STAY_LOGGED_IN.'</label>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="text-align: center;">
									<input type="submit" name="action" value="'.LOG_IN.'" />
								</td>
							</tr>
						</table>
					</center>
				</div>
			</form>
		</body>
	</html>';

echo $output;

?>