<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingUnits" id="matchingUnits" size="5" style="width:260px" >';
	$sqlUnit=mysql_query("SELECT * FROM unit WHERE unit_name LIKE '".$_POST['nm']."%' ORDER BY unit_name") or die(mysql_error());
	while($rowUnit=mysql_fetch_array($sqlUnit))
	{
		echo '<option value="'.$rowUnit["unit_id"].'">'.$rowUnit["unit_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>