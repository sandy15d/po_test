<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	$sql = mysql_query("SELECT * FROM tblissue2 WHERE issue_id=".$mid) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblissue1 WHERE issue_id=".$mid) or die(mysql_error());
	}
	header('Location:materialissue.php?action=new');
}
?>