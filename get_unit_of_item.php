<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$itid = $_POST['id'];
	$sql_item = mysql_query("SELECT unit_name FROM item INNER JOIN unit ON item.unit_id = unit.unit_id WHERE item_id=".$itid) or die(mysql_error());
	$row_item = mysql_fetch_assoc($sql_item);
	echo $row_item['unit_name'];
}
?>