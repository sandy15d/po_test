<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['rid']) && $_POST['rid']!=""){
	$sql2 = mysql_query("SELECT tbl_indent_item.*, item.unit_id AS prime_unit_id, item.alt_unit, item.alt_unit_id, item.alt_unit_num, unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE rec_id=".$_POST['rid']);
	$row2 = mysql_fetch_assoc($sql2);
	/*---------------------------*/
	$sql = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row2['prime_unit_id']);
	$row = mysql_fetch_assoc($sql);
	$prime_unit_name = $row['unit_name'];
	$alt_unit_name = "";
	if($row2['alt_unit_id']!=0){
		$sql = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row2['alt_unit_id']);
		$row = mysql_fetch_assoc($sql);
		$alt_unit_name = $row['unit_name'];
	}
	/*---------------------------*/
	$sql1 = mysql_query("SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$row2['indent_id']);
	$row1 = mysql_fetch_assoc($sql1);
	/*---------------------------*/
	$clqnty_prime = 0;
	$clqnty_alt = 0;
	$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$row2["item_id"]." AND location_id=".$row1['order_from']." AND entry_date<='".date("Y-m-d",strtotime($row1["indent_date"]))."' GROUP BY unit_id") or die(mysql_error());
	while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
		if($row_stk_rgstr['unit_id']==$row2['prime_unit_id']){
			$clqnty_prime += $row_stk_rgstr['qty'];
			$clqnty_alt += $row_stk_rgstr['qty'] * $row2['alt_unit_num'];
		} elseif($row_stk_rgstr['unit_id']==$row2['alt_unit_id']){
			$clqnty_prime += $row_stk_rgstr['qty'] / $row2['alt_unit_num'];
			$clqnty_alt += $row_stk_rgstr['qty'];
		}
	}
	/*---------------------------*/
	?>
	
	<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
	<tr class="Controls">
		<td class="th" width="15%">Item Name:</td>
		<td width="45%" id="tblItemList"><select name="item" id="item" onchange="get_curent_stock_of_item(this.value,<?php echo $row1['order_from'];?>,'<?php echo strtotime($_SESSION['stores_syear']);?>','<?php echo strtotime($row1["indent_date"]);?>')" style="width:300px"><option value="0">-- Select --</option>
		<?php 
		$sql_item=mysql_query("SELECT * FROM item WHERE item_id NOT IN (SELECT item_id FROM tbl_indent_item WHERE indent_id=".$row2['indent_id']." AND rec_id!=".$_POST['rid'].") ORDER BY item_name");
		while($row_item=mysql_fetch_array($sql_item)){
			if($row_item["item_id"]==$row2["item_id"])
				echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			else
				echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
		}?>
		</select></td>
		
		<td class="th" width="15%">Current Stock:</td>
		<td width="25%"><input name="itemStock" id="itemStock" size="10" readonly="true" value="<?php echo number_format($clqnty_prime,3,".","");?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1"><?php echo $prime_unit_name; if($row2['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';}?></span></td>
	</tr>
	
	<tr class="Controls">
		<td class="th" nowrap>Quantity:</td>
		<td><input name="itemQnty" id="itemQnty" maxlength="10" size="10" value="<?php echo $row2["qnty"];?>">&nbsp;<span id="spanUnit2"><?php if($row2['alt_unit']=="N"){echo $row2["unit_name"];} elseif($row2['alt_unit']=="A"){echo "&nbsp;";}?></span></td>
		
		<td class="th" nowrap><span id="tblcol1" style="visibility:hidden;">Unit:</span></td>
		<td id="tblcol2"><?php if($row2['alt_unit']=="N"){echo '<input type="hidden" name="unit" id="unit" value="0"/>';}
			elseif($row2['alt_unit']=="A" && $row2['alt_unit_id']!=0){
				echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row2['prime_unit_id'].'">'.$prime_unit_name.'</option><option value="'.$row2['alt_unit_id'].'">'.$alt_unit_name.'</option></select>';}?>
		</td>
	</tr>
	
	<tr class="Controls">
		<td class="th" nowrap>Remark:</td>
		<td colspan="3"><input name="remark" id="remark" maxlength="100" size="85" value="<?php echo ($row2["remark"]==null?"":$row2["remark"]); ?>"></td>
	</tr>
        <tr class="Controls">
		<td class="th" nowrap>Any Other:</td>
		<td colspan="3"><input name="AnyOther" id="AnyOther" maxlength="100" size="85" value="<?php echo $row2["AnyOther"]; ?>"></td>
	</tr>
	
 	<tr class="Bottom">
		<td align="left" colspan="4">
			<span id="spanButton"><img id="submit" src="images/update.gif" width="82" height="22" style="cursor:hand;" onclick="return validate_indent()"/></span>
&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="reset()" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='newindent1.php?oid=<?php echo $row2['indent_id'];?>'"/>&nbsp;&nbsp;<img id="send" src="images/send.gif" width="72" height="22" style="cursor:hand;" onclick="get_indent_send(<?php echo $row2['indent_id'];?>)"/>
		</td>
	</tr>
	</table><?php 
}
?>
