<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['oid']) && $_POST['oid']!=""){
	$oid = $_POST['oid'];?>
	<select name="item" id="item" onchange="get_curent_stock_of_item(this.value)" style="width:290px"><option value="0">-- Select --</option><?php 
	$sql_item=mysql_query("SELECT tbl_indent_item.item_id, item_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id WHERE indent_id=".$oid." ORDER BY item_name");
	while($row_item=mysql_fetch_array($sql_item)){
		echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
	}?>
	</select><?php 
}
?>