<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$oid = $_POST['id'];
	$sql1 = mysql_query("SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$oid) or die(mysql_error());
	$num_row = mysql_num_rows($sql1);
	if($num_row>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo date("d-m-Y",strtotime($row1["indent_date"]))."~~".$row1['orderfrom']."~~".date("d-m-Y",strtotime($row1["supply_date"]))."~~".$row1['staff_name']."~~".$row1['terms_condition'];
	} elseif($num_row==0){
		echo ""."~~".""."~~".""."~~".""."~~"."";
	}
}
?>