<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$iid = $_POST['id'];
	$sql1 = mysql_query("SELECT tblissue1.*, location_name, staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE issue_id=".$iid) or die(mysql_error());
	$num_row = mysql_num_rows($sql1);
	if($num_row>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo date("d-m-Y",strtotime($row1["issue_date"]))."~~".$row1['location_id']."~~".$row1['location_name']."~~".$row1['staff_name']."~~".$row1['issue_to'];
	} elseif($num_row==0){
		echo ""."~~".""."~~".""."~~".""."~~"."";
	}
}
?>