<?php 
function check_user()
{
	if(!$_SESSION['stores_login']){
		unset($_SESSION['stores_login']);
		unset($_SESSION['stores_uid']);
		unset($_SESSION['stores_yid']);
		unset($_SESSION['stores_syr']);
		unset($_SESSION['stores_eyr']);
		unset($_SESSION['stores_uname']);
		unset($_SESSION['stores_utype']);
		unset($_SESSION['stores_locid']);
		unset($_SESSION['stores_lname']);
		return false;
	} else {
		return true;
	}
}
?>