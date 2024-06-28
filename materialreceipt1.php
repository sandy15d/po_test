<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	$sql = mysql_query("SELECT * FROM tblreceipt2 WHERE receipt_id=".$mid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblreceipt1 WHERE receipt_id=".$mid) or die(mysql_error());
	}
	header('Location:materialreceipt.php?action=new');
}
?>