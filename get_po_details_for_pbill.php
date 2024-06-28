<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pid = $_POST['id'];
	$sql1 = mysql_query("SELECT po_date, location_name FROM tblpo INNER JOIN location ON tblpo.delivery_at = location.location_id WHERE po_id=".$pid) or die(mysql_error());
	if(mysql_num_rows($sql1)>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo $pid."~~".date("d-m-Y",strtotime($row1["po_date"]))."~~".$row1['location_name'];
	} else {
		echo $pid."~~".""."~~"."";
	}
}
?>