<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$lid = $_POST['id'];
	$sdt = date("Y-m-d",strtotime($_POST['sdt']));
	$edt = date("Y-m-d",strtotime($_POST['edt']));
	$calling_page = $_POST['pg'];
	if($calling_page=="iltdespatch")
		$sql=mysql_query("SELECT Max(ilt_date) AS maxdate FROM tblilt1 WHERE despatch_from=".$lid." AND ilt_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="xltdespatch")
		$sql=mysql_query("SELECT Max(xlt_date) AS maxdate FROM tblxlt WHERE location_id=".$lid." AND xlt_type='D' AND xlt_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="xltreceive")
		$sql=mysql_query("SELECT Max(xlt_date) AS maxdate FROM tblxlt WHERE location_id=".$lid." AND xlt_type='R' AND xlt_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="purchaseorder")
		$sql=mysql_query("SELECT Max(po_date) AS maxdate FROM tblpo WHERE po_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="indentorder")
		$sql=mysql_query("SELECT Max(indent_date) AS maxdate FROM tbl_indent WHERE order_from=".$lid." AND indent_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="materialissue")
		$sql=mysql_query("SELECT Max(issue_date) AS maxdate FROM tblissue1 WHERE location_id=".$lid." AND issue_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="materialreceipt")
		$sql=mysql_query("SELECT Max(receipt_date) AS maxdate FROM tblreceipt1 WHERE recd_at=".$lid." AND receipt_date BETWEEN '".$sdt."' AND '".$edt."'");
	elseif($calling_page=="deliveryconfirm")
		$sql=mysql_query("SELECT Max(dc_date) AS maxdate FROM tbldelivery1 WHERE dc_date BETWEEN '".$sdt."' AND '".$edt."'");
	if(mysql_num_rows($sql)>0){
		$row=mysql_fetch_assoc($sql);
		if($row["maxdate"]==null)
			echo date("d-m-Y",$_POST['sdt']);
		else
			echo date("d-m-Y",strtotime($row["maxdate"]));
	} else {
		echo date("d-m-Y",$_POST['sdt']);
	}
}
?>