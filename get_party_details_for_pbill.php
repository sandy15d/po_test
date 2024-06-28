<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$pid = $_POST['id'];
	$sqlparty = mysql_query("SELECT party.*,city_name,state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$pid) or die(mysql_error());
	$rowparty = mysql_fetch_assoc($sqlparty);
	echo $rowparty['address1']."~~".$rowparty['address2']."~~".$rowparty['address3']."~~".$rowparty['city_name']."~~".$rowparty['state_name']."~~".$rowparty['party_name'];
}
?>