<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pmid = $_POST['id'];					//paymode id
	$ptid = $_POST['ptid'];					//party id
	$pid = $_POST['pid'];					//payment id
	$pdt = date("Y-m-d",$_POST['pdt']);		//payment date
	if($pmid==1 || $pmid==3){
		echo '<select name="billNo" id="billNo" style="background-color:#E7F0F8; color:#0000FF; width:300px"><option value="0">-- Select --</option>';
	} elseif($pmid==2){
		echo '<select name="billNo" id="billNo" style="width:300px" onchange="get_bill_details(this.value)"><option value="0">-- Select --</option>';
		$sql_bill=mysql_query("SELECT * FROM tblbill WHERE (party_id=".$ptid." AND bill_return=0 AND bill_paid='N' AND bill_date<='".$pdt."') AND bill_id NOT IN (SELECT bill_id FROM tblpayment2 WHERE pay_id=".$pid.") ORDER BY bill_no");
		while($row_bill=mysql_fetch_array($sql_bill))
		{
			echo '<option value="'.$row_bill["bill_id"].'">'.$row_bill["bill_no"].'</option>';
		}
	}
	echo '</select>'; 
}
?>