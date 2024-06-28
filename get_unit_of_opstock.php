<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	if($_POST['id']!=0){
		$sql_item = mysql_query("SELECT unit.* FROM item INNER JOIN unit ON item.unit_id = unit.unit_id WHERE item_id=".$_POST['id']) or die(mysql_error());
		$row_item = mysql_fetch_assoc($sql_item);
		echo $row_item['unit_name']."~~".$row_item['unit_id'];
	} elseif($_POST['id']==0){
		echo " ~~0";
	}
}?>