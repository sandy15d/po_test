<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$mid = $_POST['id'];?>
	<select name="item" id="item" onchange="get_stockNqnty_of_item_on_pbill(this.value, document.getElementById('locationID').value, document.getElementById('dateBill').value, document.getElementById('poNo').value, document.getElementById('mrNo').value)" style="width:295px"><option value="0">-- Select --</option><?php 
	$sql_item=mysql_query("SELECT tblreceipt2.item_id, item_name FROM tblreceipt2 INNER JOIN item ON tblreceipt2.item_id = item.item_id WHERE receipt_id=".$mid." ORDER BY item_name");
	while($row_item=mysql_fetch_array($sql_item)){
		echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
	}?>
	</select><?php 
}
?>