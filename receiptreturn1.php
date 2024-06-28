<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['rid'])){
	$rid = $_REQUEST['rid'];
	$sql = ("SELECT * FROM tblreceipt_return2 WHERE return_id=".$rid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = ("DELETE FROM tblreceipt_return1 WHERE return_id=".$rid) or die(mysql_error());
	}
	header('Location:receiptreturn.php?action=new');
}
?>