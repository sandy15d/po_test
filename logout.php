<?php 
session_start();
$_SESSION['stores_login'] = false;
unset($_SESSION["stores_uid"]);
unset($_SESSION["stores_uname"]);
unset($_SESSION["stores_utype"]);
unset($_SESSION["stores_locid"]);
unset($_SESSION["stores_lname"]);
unset($_SESSION["stores_syr"]);
unset($_SESSION["stores_eyr"]);
if(!$_SESSION['stores_call_from_other']){
	header("location:login.php?m=1");
} else {
	echo '<script language="javascript">window.close();</script>';
}
?>