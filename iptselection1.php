<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	$sql = mysql_query("SELECT * FROM tblipt_item WHERE ipt_id=".$mid,$con) or die(mysql_error());
	if(mysql_num_rows($sql)==0){
		$res = mysql_query("DELETE FROM tblipt WHERE ipt_id=".$mid,$con) or die(mysql_error());
	}
	header('Location:iptselection.php?action=new');
}
?>