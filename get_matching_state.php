<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingState" id="matchingState" size="10" style="width:295px" >';
	$sqlState=mysql_query("SELECT * FROM state WHERE state_name LIKE '".$_POST['nm']."%' ORDER BY state_name") or die(mysql_error());
	while($rowState=mysql_fetch_array($sqlState))
	{
		echo '<option value="'.$rowState["state_id"].'">'.$rowState["state_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>