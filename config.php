<?php

// These are temporary values.  If you can read this line,
// you haven't run the install or upgrade script.

$database_host = "";
$database_user = "";
$database_password = "";
$database_name = "";

$admin_email = "";

$temp_directory = "";

$default_lang = "en";

if (isset($_SESSION["toby"]["lang"])){
	include("lang/".$_SESSION["toby"]["lang"].".php");
}
else{
	include("lang/".$default_lang.".php");
}

?>