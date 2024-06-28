<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['did'])){
	$did = $_REQUEST['did'];
	$sql = mysql_query("SELECT * FROM tbldelivery2 WHERE dc_id=".$did) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tbldelivery1 WHERE dc_id=".$did) or die(mysql_error());
	}
	header('Location:deliveryconfirm.php?action=new');
}
?>