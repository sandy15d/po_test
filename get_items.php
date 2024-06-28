<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$gid = $_POST['id'];
	echo '<select name="itemName" id="itemName" style="width:200px"><option selected value="0">All Items</option>';
	if($gid>0)
		$sqlItems=mysql_query("SELECT * FROM item WHERE itgroup_id=".$gid." ORDER BY item_name") or die(mysql_error());
	elseif($gid==0)
		$sqlItems=mysql_query("SELECT * FROM item ORDER BY item_name") or die(mysql_error());
	while($rowItems=mysql_fetch_array($sqlItems))
	{
		echo '<option value="'.$rowItems["item_id"].'">'.$rowItems["item_name"].'</option>';
	}
	echo '</select>';
}
?>