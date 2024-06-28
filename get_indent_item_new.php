<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['oid']) && $_POST['oid']!=""){
	$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION["stores_uid"]) or die(mysql_error());
	$row_user = mysql_fetch_assoc($sql_user);
	/*---------------------------*/
	$sql1 = mysql_query("SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$_POST['oid']);
	$row1 = mysql_fetch_assoc($sql1);
	/*---------------------------*/
	?>
	<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
	<tr class="Controls">
		<td class="th" width="15%">Item Name:</td>
		<td width="45%" id="tblItemList"><select name="item" id="item" onchange="get_curent_stock_of_item(this.value,<?php echo $row1['order_from'];?>,'<?php echo strtotime($_SESSION['stores_syear']);?>','<?php echo strtotime($row1["indent_date"]);?>')" style="width:300px"><option value="0">-- Select --</option>
		<?php 
		$sql_item=mysql_query("SELECT * FROM item WHERE item_id NOT IN (SELECT item_id FROM tbl_indent_item WHERE indent_id=".$_POST['oid'].") ORDER BY item_name");
		while($row_item=mysql_fetch_array($sql_item)){
			echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
		}?>
		</select></td>
		
		<td class="th" width="15%">Current Stock:</td>
		<td width="25%"><input name="itemStock" id="itemStock" size="10" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1">&nbsp;</span></td>
	</tr>
	
	<tr class="Controls">
		<td class="th" nowrap>Quantity:</td>
		<td><input name="itemQnty" id="itemQnty" maxlength="10" size="10" value="">&nbsp;<span id="spanUnit2">&nbsp;</span></td>
		
		<td class="th" nowrap><span id="tblcol1" style="visibility:hidden;">Unit:</span></td>
		<td id="tblcol2"><input type="hidden" name="unit" id="unit" value="0"/></td>
	</tr>
	
	<tr class="Controls">
		<td class="th" nowrap>Remark:</td>
		<td colspan="3"><input name="remark" id="remark" maxlength="100" size="85" value=""></td>
	</tr>
	
 	<tr class="Bottom">
		<td align="left" colspan="4">
	<?php if($row_user['oi1']==1){?>
			<span id="spanButton"><img id="submit" src="images/add.gif" width="72" height="22" style="cursor:hand;" onclick="return validate_indent()"/></span>
	<?php }?>
&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="reset()" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='newindent1.php?oid=<?php echo $_POST['oid'];?>'"/>&nbsp;&nbsp;<img id="send" src="images/send.gif" width="72" height="22" style="cursor:hand;" onclick="get_indent_send(<?php echo $_POST['oid'];?>)"/>
		</td>
	</tr>
	</table><?php 
}
?>