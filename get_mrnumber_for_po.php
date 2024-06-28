<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pid = $_POST['id'];
	echo '<select name="mrNo" id="mrNo" style="width:150px" onchange="get_mrdetails_on_pbill(this.value)"><option value="0">-- Select --</option>';
	$sql_rcpt=mysql_db_query(DATABASE2,"SELECT tblreceipt1.* FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id WHERE tbldelivery1.po_id=".$pid." ORDER BY receipt_id") or die(mysql_error());
	while($row_rcpt=mysql_fetch_array($sql_rcpt)){
		$receipt_number = ($row_rcpt['receipt_no']>999 ? $row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>99 && $row_rcpt['receipt_no']<1000 ? "0".$row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>9 && $row_rcpt['receipt_no']<100 ? "00".$row_rcpt['receipt_no'] : "000".$row_rcpt['receipt_no'])));
		if($row_rcpt['receipt_prefix']!=null){$receipt_number = $row_rcpt['receipt_prefix']."/".$receipt_number;}
		echo '<option value="'.$row_rcpt["receipt_id"].'">'.$receipt_number.'</option>';
	}
	echo '</select>';
}
?>