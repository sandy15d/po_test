<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$uid = $_POST['id'];
	$sqlUnit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$uid) or die(mysql_error());
	$rowUnit = mysql_fetch_assoc($sqlUnit);
	echo $rowUnit['unit_name'];
}
?>