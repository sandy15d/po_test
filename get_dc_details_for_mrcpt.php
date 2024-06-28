<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$did = $_POST['id'];
	$sql1 = mysql_query("SELECT tbldelivery1.*, po_no, po_date, delivery_date, party_name, address1, address2, address3, city_name, state_name, location_name FROM tbldelivery1 INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE dc_id=".$did) or die(mysql_error());
	$num_row = mysql_num_rows($sql1);
	if($num_row>0){
		$row1 = mysql_fetch_assoc($sql1);
		$poNumber = ($row1['po_no']>999 ? $row1['po_no'] : ($row1['po_no']>99 && $row1['po_no']<1000 ? "0".$row1['po_no'] : ($row1['po_no']>9 && $row1['po_no']<100 ? "00".$row1['po_no'] : "000".$row1['po_no'])));
		echo date("d-m-Y",strtotime($row1["dc_date"]))."~~".$poNumber."~~".date("d-m-Y",strtotime($row1["po_date"]))."~~".$row1['party_name']."~~".$row1['address1']."~~".$row1['address2']."~~".$row1['address3']."~~".$row1['city_name']."~~".$row1['state_name']."~~".date("d-m-Y",strtotime($row1["delivery_date"]))."~~".$row1['location_name'];
	} elseif($num_row==0){
		echo ""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~"."";
	}
}
?>