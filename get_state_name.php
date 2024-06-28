<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$cid = $_POST['id'];
	$sqlState = mysql_query("SELECT state_name FROM city INNER JOIN state ON city.state_id = state.state_id WHERE city_id=".$cid) or die(mysql_error());
	$rowState = mysql_fetch_assoc($sqlState);
	$sname = $rowState['state_name'];
	echo '<input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="'.$sname.'" style="background-color:#E7F0F8; color:#0000FF">';
}
?>