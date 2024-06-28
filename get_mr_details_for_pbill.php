<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$mid = $_POST['id'];
	$sql1 = mysql_query("SELECT receipt_date, recd_at, location_name FROM tblreceipt1 INNER JOIN location ON tblreceipt1.recd_at = location.location_id WHERE receipt_id=".$mid) or die(mysql_error());
	if(mysql_num_rows($sql1)>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo $mid."~~".date("d-m-Y",strtotime($row1["receipt_date"]))."~~".$row1['location_name']."~~".$row1['recd_at'];
	} else {
		echo $mid."~~".""."~~".""."~~"."";
	}
}
?>