<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$bid = $_POST['id'];
	$sql1 = mysql_query("SELECT * FROM tblbill WHERE bill_id=".$bid) or die(mysql_error());
	$num_row = mysql_num_rows($sql1);
	if($num_row>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo date("d-m-Y",strtotime($row1["bill_date"]))."~~".$row1['bill_amt'];
	} elseif($num_row==0){
		echo ""."~~"."";
	}
}
?>