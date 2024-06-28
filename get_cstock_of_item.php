<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['iid']) && $_POST['iid']!=""){
	$iid = $_POST['iid'];
	$lid = $_POST['lid'];
	$edt = date("Y-m-d",strtotime($_POST['edt']));		//ending date
	$sql_item = mysql_query("SELECT * FROM item WHERE item_id=".$iid) or die(mysql_error());
	$row_item = mysql_fetch_assoc($sql_item);
	/*---------------------------------*/
	$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row_item['unit_id']) or die(mysql_error());
	$row_unit = mysql_fetch_assoc($sql_unit);
	$prime_unit_name = $row_unit['unit_name'];
	$alt_unit_name = "";
	if($row_item['alt_unit_id']!=0){
		$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row_item['alt_unit_id']) or die(mysql_error());
		$row_unit = mysql_fetch_assoc($sql_unit);
		$alt_unit_name = $row_unit['unit_name'];
	}
	
	$clqnty_prime = 0;
	$clqnty_alt = 0;
	$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$iid." AND location_id=".$lid." AND entry_date<='".$edt."' GROUP BY unit_id") or die(mysql_error());
	while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
		if($row_stk_rgstr['unit_id']==$row_item['unit_id']){
			$clqnty_prime += $row_stk_rgstr['qty'];
			$clqnty_alt += $row_stk_rgstr['qty'] * $row_item['alt_unit_num'];
		} elseif($row_stk_rgstr['unit_id']==$row_item['alt_unit_id']){
			$clqnty_prime += $row_stk_rgstr['qty'] / $row_item['alt_unit_num'];
			$clqnty_alt += $row_stk_rgstr['qty'];
		}
	}
	echo $prime_unit_name."~~".number_format($clqnty_prime,3,'.','')."~~".$row_item['alt_unit']."~~".$row_item['unit_id']."~~".$row_item['alt_unit_id']."~~".number_format($clqnty_alt,3,'.','')."~~".$alt_unit_name;
}
?>