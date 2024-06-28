<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingCity" id="matchingCity" size="10" style="width:295px" >';
	$sqlCity=mysql_query("SELECT * FROM city WHERE city_name LIKE '".$_POST['nm']."%' ORDER BY city_name") or die(mysql_error());
	while($rowCity=mysql_fetch_array($sqlCity))
	{
		echo '<option value="'.$rowCity["city_id"].'">'.$rowCity["city_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>