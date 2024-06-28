<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingItemGroup" id="matchingItemGroup" size="10" style="width:300px" >';
	$sqlITG=mysql_query("SELECT * FROM itemgroup WHERE itgroup_name LIKE '".$_POST['nm']."%' ORDER BY itgroup_name") or die(mysql_error());
	while($rowITG=mysql_fetch_array($sqlITG))
	{
		echo '<option value="'.$rowITG["itgroup_id"].'">'.$rowITG["itgroup_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>