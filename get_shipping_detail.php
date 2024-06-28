<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pid = $_POST['id'];
	if($_POST['sip2']==1 || $_POST['sip2']==2){
		$sql_party = mysql_query("SELECT c_address1,c_address2,c_address3,city_name,state_name FROM company INNER JOIN city ON company.c_cityid=city.city_id INNER JOIN state ON city.state_id=state.state_id WHERE company_id=".$pid) or die(mysql_error());
		$row_party = mysql_fetch_assoc($sql_party);
		echo $row_party['c_address1']."~~".$row_party['c_address2']."~~".$row_party['c_address3']."~~".$row_party['city_name']."~~".$row_party['state_name'];
	} elseif($_POST['sip2']==3){
		$sql_party = mysql_query("SELECT address1,address2,address3,city_name,state_name FROM party INNER JOIN city ON party.city_id=city.city_id INNER JOIN state ON city.state_id=state.state_id WHERE party_id=".$pid) or die(mysql_error());
		$row_party = mysql_fetch_assoc($sql_party);
		echo $row_party['address1']."~~".$row_party['address2']."~~".$row_party['address3']."~~".$row_party['city_name']."~~".$row_party['state_name'];
	}
}
?>