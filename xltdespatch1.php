<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['xid'])){
	$xid = $_REQUEST['xid'];
	$sql = mysql_query("SELECT * FROM tblxlt_item WHERE xlt_id=".$xid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblxlt WHERE xlt_id=".$xid) or die(mysql_error());
	}
	header('Location:xltdespatch.php?action=new');
}
?>