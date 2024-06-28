<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION["stores_uid"]) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
 $oid = $_REQUEST['oid'];
 $ino=$_REQUEST['ino'];
$sql1 = mysql_query("SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$oid);
$row1 = mysql_fetch_assoc($sql1);
/*-------------------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script language="javascript" type="text/javascript">
function validate_indent()
{
	var err="";
	if(document.getElementById("item").value==0)
		err = "* please select an item of the indent!\n";
	if(document.getElementById("itemQnty").value=="")
		err += "* Quantity of the item is mandatory!\n";
	if(document.getElementById("itemQnty").value!="" && ! IsNumeric(document.getElementById("itemQnty").value))
		err += "* please input valid quantity of the item!\n";
	if(err==""){
		document.getElementById("submit").style.display = 'none';
		get_indent_item_submit(document.getElementById("xn").value,document.getElementById("indid").value,document.getElementById("ino").value,document.getElementById("recid").value,document.getElementById("item").value,document.getElementById("itemQnty").value,document.getElementById("unit").value,document.getElementById("remark").value,document.getElementById("AnyOther").value);
		return true;
	} else {
		alert("Error: \n"+err);
		return false;
	}
}
function funShow(val){
    if(val){
    $("#divGoogle").css({"display":"block"});
    $.post("get_newindent_item_remark.php",{val:val},function(data){
        $("#divGoogle").html(data);
    })
    }
    else
        $("#divGoogle").css({"display":"none"});
        
}
function fClick(val){

    document.getElementById("remark").value=val;
    document.getElementById("divGoogle").style.display="none";
}
</script>
<style>
    li{
        list-style:none;
        cursor: pointer;
        
    }
</style>
</head>


<body>
<center>
<form name="indentitem"  method="post">
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="775px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Indent No.:</td>
			<?php $indent_number = ($row1['indent_no']>999 ? $row1['indent_no'] : ($row1['indent_no']>99 && $row1['indent_no']<1000 ? "0".$row1['indent_no'] : ($row1['indent_no']>9 && $row1['indent_no']<100 ? "00".$row1['indent_no'] : "000".$row1['indent_no'])));
				if($row1['ind_prefix']!=null){$indent_number = $row1['ind_prefix']."/".$indent_number;}?>
			<td><input name="indentNo" id="indentNo" size="20" readonly="true" value="<?php echo $indent_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Indent Date:</td>
			<td><input name="indentDate" id="indentDate" size="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($row1["indent_date"]));?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Indent From:</td>
			<td><input name="indentFrom" id="indentFrom" size="45" readonly="true" value="<?php echo $row1['orderfrom'];?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Estimated Supply Date:</td>
			<td><input name="supplyDate" id="supplyDate" size="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($row1["supply_date"]));?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Order By:</td>
			<td><input name="orderBy" id="orderBy" size="45" readonly="true" value="<?php echo $row1["staff_name"];?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;<input type="hidden" name="xn" id="xn" value="new" /><input type="hidden" name="indid" id="indid" value="<?php echo $oid;?>" /><input type="hidden" name="ino" id="ino" value="<?php echo $ino;?>" /><input type="hidden" name="recid" id="recid" value="0" /></td>
			<td>&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<span id="spanIndentEditItem">
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" width="15%">Item Name:</td>
			<td width="45%" id="tblItemList"><select name="item" id="item" onchange="get_curent_stock_of_item(this.value,<?php echo $row1['order_from'];?>,'<?php echo strtotime($_SESSION['stores_syear']);?>','<?php echo strtotime($row1["indent_date"]);?>')" style="width:300px">
			<option value="0">-- Select --</option>
			<?php 
			$sql_item=mysql_query("SELECT * FROM item WHERE item_id NOT IN (SELECT item_id FROM tbl_indent_item WHERE indent_id=".$oid.") ORDER BY item_name");
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
			<td class="th" nowrap>Description:</td>
			<td colspan="3">
                            <textarea name="remark" id="remark" maxlength="500" size="85" value="" onkeyup="funShow(this.value)" ></textarea>
                           
                        </td>
                        <td class="th" nowrap>Any Other</td>
			<td><textarea name="AnyOther" id="AnyOther" maxlength="500" size="85" value=""></textarea></td>
		</tr>
                    <tr>
                        <td></td>
                        <td>
                            <div id="divGoogle" style="border:1px solid silver;background-color:white;display:none;z-index: 100;position: fixed"  ></div>
                        </td>
                    </tr>
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if($row_user['oi1']==1){?>
				<span id="spanButton"><img id="submit" src="images/add.gif" width="72" height="22" style="cursor:hand;" onclick="return validate_indent()"/></span>
		<?php }?>
&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="reset()" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='newindent1.php?oid=<?php echo $oid;?>'"/>&nbsp;&nbsp;<img id="send" src="images/send.gif" width="72" height="22" style="cursor:hand;" onclick="get_indent_send(<?php echo $oid.",".$ino;?>)"/>
			</td>
		</tr>
		</table>
		</span>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<span id="spanIndentItemList">

<?php $schk=mysql_query("select * from tbl_indent_item where AnyOther!='' AND indent_id=".$oid); $rrow=mysql_num_rows($schk); ?>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="40%">Item Name</th>
                        <th width="40%">Item Remark</th>
                        <?php if($rrow>0){ ?><th style="width:30%;">Comment</th><?php } ?> 
			<th width="20%">Quantity</th>
			<th width="15%">Unit</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		<?php 
		$i = 0;
		$sql_order = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$oid." ORDER BY seq_no") or die(mysql_error());
		while($row_order=mysql_fetch_array($sql_order))
		{
			$i++;
			echo '<tr class="Row">';
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td>'.$row_order['remark'].'</td>';

                        if($rrow>0){ echo "<td align='left'>".$row_order['AnyOther']."</td>"; }

                        echo '<td align="center">'.$row_order['qnty'].'</td><td>'.$row_order['unit_name'].'</td>';
			if($row_order['item_ordered']=="N"){
				if($row_user['oi2']==1)
					echo '<td align="center"><img src="images/edit.gif" style="display:inline;cursor:hand;" onclick="get_indent_item_edit('.$row_order['rec_id'].')"/></td>';
				elseif($row_user['oi2']==0)
					echo '<td align="center">&nbsp;</td>';
				if($row_user['oi3']==1)
					echo '<td align="center"><img src="images/cancel.gif" title="Delete" style="display:inline;cursor:hand;" onclick="get_indent_item_delete('.$row_order['rec_id'].')"></td>';
				elseif($row_user['oi3']==0)
					echo '<td align="center">&nbsp;</td>';
			} elseif($row_order['item_ordered']=="Y"){
				echo '<td align="center">&nbsp;</td>';
				echo '<td align="center">&nbsp;</td>';
			}
			echo '</tr>';
		} ?>
		</table>
		</span>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>
</center>
</body>
</html>
