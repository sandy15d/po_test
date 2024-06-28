<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['oid'])){
	$oid = $_REQUEST['oid'];
	$sql = mysql_query("SELECT * FROM tblpo_item WHERE po_id=".$oid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblpo WHERE po_id=".$oid) or die(mysql_error());
	}
	header('Location:purchaseorder.php?action=new');
}
?>