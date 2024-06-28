<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$rcptid = $_POST['id'];
        
	$sql1 = mysql_query("SELECT tblreceipt1.*, transit_name FROM tblreceipt1 INNER JOIN transit ON tblreceipt1.transit_point = transit.transit_id WHERE receipt_id=".$rcptid) or die(mysql_error());
	if(mysql_num_rows($sql1)>0){
		$row1 = mysql_fetch_assoc($sql1);
		$challandate = ($row1["challan_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["challan_date"])));
		$freightpaid = ($row1["freight_paid"]=="Y" ? "Yes" : "No");
		
		$sql2 = mysql_query("SELECT * FROM tbldelivery1 WHERE dc_id=".$row1["dc_id"]) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		
		$sql3 = mysql_query("SELECT * FROM tblpo WHERE po_id=".$row2["po_id"]) or die(mysql_error());
		$row3 = mysql_fetch_assoc($sql3);
		$po_number = ($row3['po_no']>999 ? $row3['po_no'] : ($row3['po_no']>99 && $row3['po_no']<1000 ? "0".$row3['po_no'] : ($row3['po_no']>9 && $row3['po_no']<100 ? "00".$row3['po_no'] : "000".$row3['po_no'])));
		
		$sql4 = mysql_query("SELECT party.*, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$row3["party_id"]) or die(mysql_error());
		$row4 = mysql_fetch_assoc($sql4);
		
		$sql5 = mysql_query("SELECT * FROM location WHERE location_id=".$row3["delivery_at"]) or die(mysql_error());
		$row5 = mysql_fetch_assoc($sql5);
		$delivery_at = $row5["location_name"];
		
		$sql5 = mysql_query("SELECT * FROM location WHERE location_id=".$row1["recd_at"]) or die(mysql_error());
		$row5 = mysql_fetch_assoc($sql5);
		$received_at = $row5["location_name"];
		
		$sql6 = mysql_query("SELECT * FROM staff WHERE staff_id=".$row1["recd_by"]) or die(mysql_error());
		$row6 = mysql_fetch_assoc($sql6);
		
		echo date("d-m-Y",strtotime($row1["receipt_date"]))."~~".$po_number."~~".date("d-m-Y",strtotime($row3["po_date"]))."~~".$row1['challan_no']."~~".$challandate."~~".$row1['transit_name']."~~".date("d-m-Y",strtotime($row3["delivery_date"]))."~~".$delivery_at."~~".$row4['party_name']."~~".$row4['address1']."~~".$row4['address2']."~~".$row4['address3']."~~".$row4['city_name']."~~".$row4['state_name']."~~".$received_at."~~".$row6['staff_name']."~~".$row1['recd_at']."~~".$freightpaid."~~".$row1['freight_amt'];
	} else {
		echo ""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~"."";
	}
}
?>