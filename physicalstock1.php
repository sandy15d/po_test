<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['pid'])){
	$pid = $_REQUEST['pid'];
	$sql = mysql_query("SELECT * FROM tblpstock_item WHERE ps_id=".$pid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblpstock WHERE ps_id=".$pid) or die(mysql_error());
	}
	header('Location:physicalstock.php?action=new');
}
?>