<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingItems" id="matchingItems" size="10" style="width:300px" >';
	$sqlItems=mysql_query("SELECT * FROM item WHERE item_name LIKE '".$_POST['nm']."%' ORDER BY item_name") or die(mysql_error());
	while($rowItems=mysql_fetch_array($sqlItems))
	{
		echo '<option value="'.$rowItems["item_id"].'">'.$rowItems["item_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>