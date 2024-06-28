<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$oid = $_POST['id'];
	$sql1 = mysql_query("SELECT tblpo.*, company_name, party_name, address1, address2, address3, city_name, state_name, location_name FROM tblpo INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE tblpo.po_id=".$oid) or die(mysql_error());
	$num_row = mysql_num_rows($sql1);
	if($num_row>0){
		$row1 = mysql_fetch_assoc($sql1);
		echo date("d-m-Y",strtotime($row1["po_date"]))."~~".$row1['party_name']."~~".$row1['address1']."~~".$row1['address2']."~~".$row1['address3']."~~".$row1['city_name']."~~".$row1['state_name']."~~".date("d-m-Y",strtotime($row1["delivery_date"]))."~~".$row1['delivery_at']."~~".$row1['location_name']."~~".$row1['company_name'];
	} elseif($num_row==0){
		echo ""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~".""."~~"."";
	}
}
?>