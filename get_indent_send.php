<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['oid']) && $_POST['oid']!=""){

	$sql2 = mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$_POST['oid']);
	$count = mysql_num_rows($sql2);
	if($count>0){
		$row2 = mysql_fetch_assoc($sql2);
		$res = mysql_query("UPDATE tbl_indent SET ind_status='S' WHERE indent_id=".$_POST['oid']) or die(mysql_error());
	} elseif($count==0){
		// do nothing
	}
	echo $count."~~".$_POST['oid']."~~".$_POST['ino'];
}
?>