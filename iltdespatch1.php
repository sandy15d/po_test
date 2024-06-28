<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	$sql = mysql_db_query(DATABASE2,"SELECT * FROM tblilt2 WHERE ilt_id=".$mid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_db_query(DATABASE2,"DELETE FROM tblilt1 WHERE ilt_id=".$mid) or die(mysql_error());
	}
	header('Location:iltdespatch.php?action=new');
}
?>