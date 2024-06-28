<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
if(isset($_SESSION["stores_utype"])){
	$uid = $_SESSION["stores_uid"];
	$uname = $_SESSION["stores_uname"];
	$utype = $_SESSION["stores_utype"];
	$locid = $_SESSION["stores_locid"];
	$lname = $_SESSION["stores_lname"];
	$syear = $_SESSION["stores_syr"];
	$eyear = $_SESSION["stores_eyr"];
}
/*-------------------------------*/
$sql_user = mysql_db_query(DATABASE2,"SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$uid) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
$oid = $_REQUEST['oid'];
$sql1 = mysql_db_query(DATABASE2,"SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN ".DATABASE1.".location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN ".DATABASE1.".staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$oid);
$row1 = mysql_fetch_assoc($sql1);
/*-------------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="send"){
	$sql2 = mysql_db_query(DATABASE2,"SELECT * FROM tbl_indent_item WHERE indent_id=".$oid);
	$row2 = mysql_fetch_assoc($sql2);
	$count = mysql_num_rows($sql2);
	if($count>0){
		$res = mysql_db_query(DATABASE2,"UPDATE tbl_indent SET ind_status='S' WHERE indent_id=".$oid) or die(mysql_error());
		header('Location:msgsend.php?oid='.$oid);
//		header('Location:indentorder.php?action=new');
	} elseif($count==0){
		$msg = "Sorry! This order can not send, since having no item! Please retry....";
		header('Location:indentitem.php?action=new&oid='.$oid.'&msg='.$msg);
	}
}
/*-------------------------------*/
if(isset($_REQUEST["msg"])){
	$msg = $_REQUEST['msg'];
	unset($_REQUEST['msg']);
}
/*-------------------------------*/
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_db_query(DATABASE2,"SELECT tbl_indent_item.*, item.unit_id AS prime_unit_id, item.alt_unit, item.alt_unit_id, item.alt_unit_num, unit_name FROM tbl_indent_item INNER JOIN ".DATABASE1.".item ON tbl_indent_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON tbl_indent_item.unit_id = unit.unit_id WHERE rec_id=".$rid);
	$row2 = mysql_fetch_assoc($sql2);
	$sql = mysql_db_query(DATABASE1,"SELECT unit_name FROM unit WHERE unit_id=".$row2['prime_unit_id']);
	$row = mysql_fetch_assoc($sql);
	$prime_unit_name = $row['unit_name'];
	$alt_unit_name = "";
	if($row2['alt_unit_id']!=0){
		$sql = mysql_db_query(DATABASE1,"SELECT unit_name FROM unit WHERE unit_id=".$row2['alt_unit_id']);
		$row = mysql_fetch_assoc($sql);
		$alt_unit_name = $row['unit_name'];
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_db_query(DATABASE1,"SELECT * FROM item WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname = $row["item_name"];
	/*-------------------------------*/
	if($row['alt_unit']=="N"){$unitid = $row['unit_id'];} elseif($row['alt_unit']=="A"){$unitid = $_POST['unit'];}
	$sql = mysql_db_query(DATABASE1,"SELECT * FROM unit WHERE unit_id=".$unitid);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$dateIndent = date("Y-m-d",strtotime($row1['indent_date']));
	$particulars = "From ".$row1['orderfrom'];
	$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
	/*-------------------------------*/
	$sql=mysql_db_query(DATABASE2,"SELECT * FROM tbl_indent_item WHERE indent_id=".$oid." AND item_id=".$_POST['item']." AND qnty=".$_POST['itemQnty']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	if($_POST['submit']=="update"){
		if($count>=0){
			$sql = "UPDATE tbl_indent_item SET item_id=".$_POST['item'].",qnty=".$_POST['itemQnty'].",unit_id=".$unitid.",uid=".$uid.",";
			if($_POST['remark']=="")
				$sql .= "remark=null";
			else
				$sql .= "remark='".$_POST['remark']."'";
			$sql .= " WHERE rec_id=".$rid;
			$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$lname."','Change','".$uname."')";
			$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
			//end of inserting record into logbook
			header('Location:indentitem.php?action=new&oid='.$oid);
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_db_query(DATABASE2,"DELETE FROM tbl_indent_item WHERE rec_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = $row["maxid"] + 1;
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$lname."','Delete','".$uname."')";
		$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		header('Location:indentitem.php?action=new&oid='.$oid);
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into order record.";
		else {
			$sql = mysql_db_query(DATABASE2,"SELECT Max(rec_id) as maxid FROM tbl_indent_item");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = mysql_db_query(DATABASE2,"SELECT Max(seq_no) as maxid FROM tbl_indent_item WHERE indent_id=".$oid);
			$row = mysql_fetch_assoc($sql);
			$sno = $row["maxid"] + 1;
			$sql = "INSERT INTO tbl_indent_item (rec_id,indent_id,seq_no,item_id,qnty,unit_id,item_ordered,uid,remark) VALUES(".$rid.",".$oid.",".$sno.",".$_POST['item'].",".$_POST['itemQnty'].",".$unitid.",'N',".$uid.",";
			if($_POST['remark']=="")
				$sql .= "null)";
			else
				$sql .= "'".$_POST['remark']."')";
			$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$lname."','New','".$uname."')";
			$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
			//end of inserting record into logbook
			header('Location:indentitem.php?action=new&oid='.$oid);
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Stores Management System</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_indent()
{
	var err="";
	if(document.getElementById("item").value==0)
		err = "* please select an item of the indent!\n";
	if(document.getElementById("itemQnty").value!="" && ! IsNumeric(document.getElementById("itemQnty").value))
		err += "* please input valid quantity of the item!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
</script>
</head>


<body onload="document.getElementById('item').focus()">
<center>
<form name="indentitem"  method="post" onsubmit="return validate_indent()">
<table align="center" cellspacing="0" cellpadding="0" height="320px" width="675px" border="0">
<tr>
	<td align="left" width="30%" style="color:#009900"><?php echo 'user: '.$uname.', location: '.$lname; ?></td>
	<td align="center" width="40%" style="color:#0000FF"><?php echo '('.$syear.' to '.$eyear.')'; ?></td>
	<td align="left" width="30%" style="color:#009900">&nbsp;</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Indent No.:</td>
			<?php if(isset($_REQUEST["action"])){
				$indent_number = ($row1['indent_no']>999 ? $row1['indent_no'] : ($row1['indent_no']>99 && $row1['indent_no']<1000 ? "0".$row1['indent_no'] : ($row1['indent_no']>9 && $row1['indent_no']<100 ? "00".$row1['indent_no'] : "000".$row1['indent_no'])));
				if($row1['ind_prefix']!=null){$indent_number = $row1['ind_prefix']."/".$indent_number;}
			} ?>
			<td><input name="indentNo" id="indentNo" size="20" readonly="true" value="<?php echo $indent_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Indent Date:</td>
			<td><input name="indentDate" id="indentDate" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo date("d-m-Y",strtotime($row1["indent_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Indent From:</td>
			<td><input name="indentFrom" id="indentFrom" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1['orderfrom'];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Estimated Supply Date:</td>
			<td><input name="supplyDate" id="supplyDate" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo date("d-m-Y",strtotime($row1["supply_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Order By:</td>
			<td><input name="orderBy" id="orderBy" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["staff_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;<input type="hidden" name="xn" id="xn" value="" /></td>
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
	<table align="center" width="635px" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" width="120px">Item Name:</td>
			<td width="235px"><select name="item" id="item" onchange="get_curent_stock_of_item(this.value,<?php echo $row1['order_from'];?>,'<?php echo strtotime($syear);?>','<?php echo strtotime($row1["indent_date"]);?>')" style="width:250px" tabindex="1">
			<option value="0">-- Select --</option>
			<?php 
			$sql_item=mysql_db_query(DATABASE1,"SELECT * FROM item ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($row_item["item_id"]==$row2["item_id"])
					echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
				else
					echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			}?>
			</select></td>
			
		<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
			$clqnty_prime = 0;
			$clqnty_alt = 0;
			$sql_stk_rgstr = mysql_db_query(DATABASE2,"SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$row2["item_id"]." AND location_id=".$row1['order_from']." AND entry_date<='".date("Y-m-d",strtotime($row1["indent_date"]))."' GROUP BY unit_id") or die(mysql_error());
			while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
				if($row_stk_rgstr['unit_id']==$row2['prime_unit_id']){
					$clqnty_prime += $row_stk_rgstr['qty'];
					$clqnty_alt += $row_stk_rgstr['qty'] * $row2['alt_unit_num'];
				} elseif($row_stk_rgstr['unit_id']==$row2['alt_unit_id']){
					$clqnty_prime += $row_stk_rgstr['qty'] / $row2['alt_unit_num'];
					$clqnty_alt += $row_stk_rgstr['qty'];
				}
			}
		}?>
			<td class="th" width="90px">Current Stock:</td>
			<td width="190px"><input name="itemStock" id="itemStock" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo number_format($clqnty_prime,3,".","");}?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $prime_unit_name; if($row2['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';}} else {echo "";}?></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Quantity:</td>
			<td><input name="itemQnty" id="itemQnty" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row2["qnty"];}?>" tabindex="2">&nbsp;<span id="spanUnit2"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){if($row2['alt_unit']=="N"){echo $row2["unit_name"];} elseif($row2['alt_unit']=="A"){echo "&nbsp;";}} else {echo "";}?></span></td>
			
			<td class="th" id="tblcol1" nowrap><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){if($row2['alt_unit']=="N"){echo "&nbsp;";} elseif($row2['alt_unit']=="A"){echo "Unit:";}} else {echo "&nbsp;";}?></td>
			<td id="tblcol2"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if($row2['alt_unit']=="N"){ echo "&nbsp;";}
				elseif($row2['alt_unit']=="A" && $row2['alt_unit_id']!=0){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row2['prime_unit_id'].'">'.$prime_unit_name.'</option><option value="'.$row2['alt_unit_id'].'">'.$alt_unit_name.'</option></select>';}
			} else {echo "&nbsp;";}?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Remark:</td>
			<td colspan="3"><input name="remark" id="remark" maxlength="100" size="85" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo ($row2["remark"]==null?"":$row2["remark"]);} else {echo "";} ?>" tabindex="2"></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['oi1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new" tabindex="3"><input type="hidden" name="submit" value="new" />
			<?php } elseif($row_user['oi1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new" tabindex="3" >
			<?php } ?>
&nbsp;&nbsp;<a href="javascript:document.indentitem.reset()" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update" tabindex="3"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='indentitem.php?action=new&oid=<?php echo $oid;?>'" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete" tabindex="3"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='indentitem.php?action=new&oid=<?php echo $oid;?>'" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='indentorder1.php?oid=<?php echo $oid;?>'" tabindex="5"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='indentitem.php?action=send&oid=<?php echo $oid;?>'" tabindex="6"><img src="images/send.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="635px" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="50%">Item Name</th>
			<th width="20%">Quantity</th>
			<th width="15%">Unit</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_order = mysql_db_query(DATABASE2,"SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN ".DATABASE1.".item ON tbl_indent_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$oid." ORDER BY seq_no") or die(mysql_error());
		while($row_order=mysql_fetch_array($sql_order))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "indentitem.php?action=delete&oid=".$oid."&rid=".$row_order['rec_id'];
			$edit_ref = "indentitem.php?action=edit&oid=".$oid."&rid=".$row_order['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td align="center">'.$row_order['qnty'].'</td><td>'.$row_order['unit_name'].'</td>';
			if($row_order['item_ordered']=="N"){
				if($row_user['oi2']==1)
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				elseif($row_user['oi2']==0)
					echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				if($row_user['oi3']==1)
					echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				elseif($row_user['oi3']==0)
					echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			} elseif($row_order['item_ordered']=="Y"){
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			}
			echo '</tr>';
		} ?>
		
		</table>
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