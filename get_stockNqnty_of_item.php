<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['iid']) && $_POST['iid']!=""){
	$iid = $_POST['iid'];
	$lid = $_POST['lid'];
	$edt = date("Y-m-d",$_POST['edt']);		//ending date
	$pid = $_POST['pid'];
	$mid = $_POST['mid'];
	/*---------------------------------*/
	$prime_unit_name = "";
	$alt_unit_name = "";
	$prime_unit_id = 0;
	$alt_unit_id = 0;
	$alt_unit = "N";
	$clqnty_prime = 0;
	$clqnty_alt = 0;
	$ordqnty_prime = 0;
	$ordqnty_alt = 0;
	$rcdqnty_prime = 0;
	$rcdqnty_alt = 0;
	/*---------------------------------*/
	if($iid>0){
		$sql_item = mysql_query("SELECT * FROM item WHERE item_id=".$iid) or die(mysql_error());
		$row_item = mysql_fetch_assoc($sql_item);
		$prime_unit_id = $row_item['unit_id'];
		$alt_unit = $row_item['alt_unit'];
		$alt_unit_id = $row_item['alt_unit_id'];
		/*---------------------------------*/
		$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$prime_unit_id) or die(mysql_error());
		$row_unit = mysql_fetch_assoc($sql_unit);
		$prime_unit_name = $row_unit['unit_name'];
		if($alt_unit_id!=0){
			$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$alt_unit_id) or die(mysql_error());
			$row_unit = mysql_fetch_assoc($sql_unit);
			$alt_unit_name = $row_unit['unit_name'];
		}
		/*---------------------------------*/
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
		/*---------------------------------*/
		$sql_ord = mysql_query("SELECT Sum(qnty) AS qty, unit_id FROM tblpo_item WHERE po_id=".$pid." AND item_id=".$iid." GROUP BY unit_id") or die(mysql_error());
		while($row_ord=mysql_fetch_array($sql_ord)){
			if($row_ord['unit_id']==$row_item['unit_id']){
				$ordqnty_prime += $row_ord['qty'];
				$ordqnty_alt += $row_ord['qty'] * $row_item['alt_unit_num'];
			} elseif($row_ord['unit_id']==$row_item['alt_unit_id']){
				$ordqnty_prime += $row_ord['qty'] / $row_item['alt_unit_num'];
				$ordqnty_alt += $row_ord['qty'];
			}
		}
		/*---------------------------------*/
		$sql_rcd = mysql_query("SELECT Sum(receipt_qnty) AS qty, unit_id FROM tblreceipt2 WHERE receipt_id=".$mid." AND item_id=".$iid." GROUP BY unit_id") or die(mysql_error());
		while($row_rcd=mysql_fetch_array($sql_rcd)){
			if($row_rcd['unit_id']==$row_item['unit_id']){
				$rcdqnty_prime += $row_rcd['qty'];
				$rcdqnty_alt += $row_rcd['qty'] * $row_item['alt_unit_num'];
			} elseif($row_rcd['unit_id']==$row_item['alt_unit_id']){
				$rcdqnty_prime += $row_rcd['qty'] / $row_item['alt_unit_num'];
				$rcdqnty_alt += $row_rcd['qty'];
			}
		}
	}
	/*---------------------------------*/
	echo number_format($clqnty_prime,3,'.','')."~~".number_format($clqnty_alt,3,'.','')."~~".number_format($ordqnty_prime,3,'.','')."~~".number_format($ordqnty_alt,3,'.','')."~~".number_format($rcdqnty_prime,3,'.','')."~~".number_format($rcdqnty_alt,3,'.','')."~~".$alt_unit."~~".$prime_unit_id."~~".$alt_unit_id."~~".$prime_unit_name."~~".$alt_unit_name;
}
?>