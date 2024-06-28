<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['oid']) && $_POST['oid']!=""){
	$oid = $_POST['oid'];
	$sql=mysql_query("SELECT indent_date FROM tbl_indent WHERE indent_id=".$oid);
	$row=mysql_fetch_assoc($sql);
	echo date("d-m-Y",strtotime($row['indent_date']));
}
?>