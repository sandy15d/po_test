<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['tid'])){
	$tid = $_REQUEST['tid'];
	$sql = mysql_query("SELECT * FROM tblcash_item WHERE txn_id=".$tid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblcashmemo WHERE txn_id=".$tid) or die(mysql_error());
	}
	header('Location:cashpurchase.php?action=new');
}
?>