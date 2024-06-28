<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['oid'])){
	$oid = $_REQUEST['oid'];
	$sql = mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$oid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tbl_indent WHERE indent_id=".$oid) or die(mysql_error());
	}
	header('Location:newindent.php');
}
?>