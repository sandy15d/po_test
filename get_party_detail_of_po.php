<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pid = $_POST['id'];
	$sql_party = mysql_query("SELECT party_name,address1,address2,address3,city_name,state_name,contact_person,tin FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$pid) or die(mysql_error());
	$row_party = mysql_fetch_assoc($sql_party);
	echo $row_party['address1']."~~".$row_party['address2']."~~".$row_party['address3']."~~".$row_party['city_name']."~~".$row_party['state_name']."~~".$row_party['contact_person']."~~".$row_party['tin'];
}
?>