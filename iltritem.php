<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT ilt1,ilt2,ilt3,ilt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, staff_name FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id WHERE ilt_id=".$mid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$ilt_number = ($row1['ilt_no']>999 ? $row1['ilt_no'] : ($row1['ilt_no']>99 && $row1['ilt_no']<1000 ? "0".$row1['ilt_no'] : ($row1['ilt_no']>9 && $row1['ilt_no']<100 ? "00".$row1['ilt_no'] : "000".$row1['ilt_no'])));
if($row1['ilt_prefix']!=null){$ilt_number = $row1['ilt_prefix']."/".$ilt_number;}
$location_id = $row1['receive_at'];
$ilt_date = date("d-m-Y",strtotime($row1["ilt_date"]));
$receive_date = date("d-m-Y",strtotime($row1["receive_date"]));
$sourceLocation = $row1['sourceLocation'];
$destinationLocation = $row1['destinationLocation'];
$staff_name = $row1['staff_name'];
$despatch_mode = ($row1['despatch_mode']==1 ? "Hand Delivery" : "By Vehicle");
$vehicle_num = $row1['vehicle_num'];
/*-------------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="receive"){
	$sql2 = mysql_query("SELECT * FROM tblilt2 WHERE ilt_id=".$mid." AND receive_qnty!=0");
	$row2 = mysql_fetch_assoc($sql2);
	$count = mysql_num_rows($sql2);
	if($count>0){
		$res = mysql_query("UPDATE tblilt1 SET receive_status='R' WHERE ilt_id=".$mid) or die(mysql_error());
		echo '<script language="javascript">window.location="iltreceive.php";</script>';
	} elseif($count==0){
		$msg = "Sorry! This transfer can not received, since having no item selected! Please retry....";
		echo '<script language="javascript">window.location="iltritem.php?action=new&mid='.$mid.'&msg='.$msg.'";</script>';
	}
}
/*-------------------------------*/
if(isset($_REQUEST["msg"])){
	$msg = $_REQUEST['msg'];
	unset($_REQUEST['msg']);
}
/*-------------------------------*/
$receive_staff = "";
if($row1['receive_by']!=0){
	$sql3 = mysql_query("SELECT * FROM staff WHERE staff_id=".$row1['receive_by']) or die(mysql_error());
	$row3 = mysql_fetch_assoc($sql3);
	$receive_staff = $row3['staff_name'];
}
/*-------------------------------*/
$rid = 0;
$item_id = 0;
$item_name = "";
$despatch_qnty = "";
$receive_qnty = "";
$unit_name = "";
$clqnty_prime = 0;
$clqnty_alt = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
if(isset($_REQUEST['rid'])){
	$rid = $_REQUEST['rid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2 = mysql_query("SELECT tblilt2.*, item_name, unit_name FROM tblilt2 INNER JOIN item ON tblilt2.item_id = item.item_id INNER JOIN .unit ON tblilt2.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_id = $row2["item_id"];
		$item_name = $row2["item_name"];
		$despatch_qnty = $row2["despatch_qnty"];
		$receive_qnty = $row2["receive_qnty"];
		$unit_name = $row2["unit_name"];
		/*-------------------------------*/
		$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN .unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
		$row4=mysql_fetch_assoc($sql4);
		$prime_unit_id = $row4['prime_unit_id'];
		$prime_unit_name = $row4['prime_unit_name'];
		$alt_unit = $row4['alt_unit'];
		$alt_unit_id = $row4['alt_unit_id'];
		$alt_unit_num = $row4['alt_unit_num'];
		
		if($alt_unit=="A" && $alt_unit_id!="0"){
			$sql5=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$alt_unit_id);
			$row5=mysql_fetch_assoc($sql5);
			$alt_unit_name = $row5['alt_unit_name'];
		}
		/*-------------------------------*/
		$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$item_id." AND location_id=".$location_id." AND entry_date<='".date("Y-m-d",strtotime($receive_date))."' GROUP BY unit_id") or die(mysql_error());
		while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
			if($row_stk_rgstr['unit_id']==$prime_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'];
				$clqnty_alt += $row_stk_rgstr['qty'] * $alt_unit_num;
			} elseif($row_stk_rgstr['unit_id']==$alt_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'] / $alt_unit_num;
				$clqnty_alt += $row_stk_rgstr['qty'];
			}
		}
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$dateILT=$row1["receive_date"];
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	$particulars = "From ".$row1['sourceLocation']." To ".$row1['destinationLocation'];
	$itemname=$row2["item_name"];
	$unitname = $row2['unit_name'];
	/*-------------------------------*/
	if($row4['prime_unit_id']==$row2['unit_id']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['receiveQnty'];
	} elseif($row4['alt_unit_id']==$row2['unit_id']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['receiveQnty'] / $row4['alt_unit_num'];
	}
	/*-------------------------------*/
	$sql = mysql_query("SELECT * FROM stock_register WHERE entry_mode='T+' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['receive_at']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblilt2 SET receive_qnty=".$_POST['receiveQnty']." WHERE rec_id=".$rid) or die(mysql_error());
		if($count==0){
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,unit_id,item_qnty,item_rate) VALUES(".$sid.",'T+',".$mid.",'".$dateILT."',".$row2['seq_no'].",".$row1['receive_at'].",".$row2['item_id'].",".$unitid.",".$itemQnty.",0)";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiveQnty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		} elseif($count>0){
			$res = mysql_query("UPDATE stock_register SET item_id=".$row2['item_id'].",unit_id=".$unitid.",item_qnty=".$itemQnty." WHERE entry_mode='T+' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['receive_at']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiveQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
		echo '<script language="javascript">window.location="iltritem.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("UPDATE tblilt2 SET receive_qnty=0 WHERE rec_id=".$rid) or die(mysql_error());
		if($count>0){
			$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='T+' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['receive_at']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiveQnty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
		echo '<script language="javascript">window.location="iltritem.php?action=new&mid='.$mid.'";</script>';
	}
}
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
<script language="javascript" type="text/javascript">
function validate_ilt_item()
{
	var err="";
	if(document.getElementById("receiveQnty").value!="" && ! IsNumeric(document.getElementById("receiveQnty").value))
		err = "* please input valid (numeric only) quantity of the item!\n";
	if(document.getElementById("receiveQnty").value=="" || parseFloat(document.getElementById("receiveQnty").value)==0)
		err += "* Item's quantity is a mandatory field!\n";
	if(parseFloat(document.getElementById("receiveQnty").value)>parseFloat(document.getElementById("despatchQnty").value))
		err += "* Item's receiving quantity is too much than despatching quantity. Hence it is not allowed!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="250px" width="825px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Inter Location Transfer - [ Receive Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" width="15%" nowrap>ILT No.:</td>
			<td width="35%"><input name="iltNo" id="iltNo" maxlength="15" size="20" readonly="true" value="<?php echo $ilt_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Despatch Date:</td>
			<td><input name="despatchDate" id="despatchDate" maxlength="10" size="10" readonly="true" value="<?php echo $ilt_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th">Receive Date:</td>
			<td><input name="receiveDate" id="receiveDate" maxlength="10" size="10" readonly="true" value="<?php echo $receive_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Despatch From:</td>
			<td><input name="despatchFrom" id="despatchFrom" maxlength="50" size="45" readonly="true" value="<?php echo $sourceLocation;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th">Receive At:</td>
			<td><input name="destination" id="destination" maxlength="50" size="45" readonly="true" value="<?php echo $destinationLocation;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Despatched By:</td>
			<td><input name="despatchBy" id="despatchBy" maxlength="50" size="45" readonly="true" value="<?php echo $staff_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received By:</td>
			<td><input name="receiveBy" id="receiveBy" maxlength="50" size="45" readonly="true" value="<?php echo $receive_staff;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Mode of Despatch:</td>
			<td><input name="despatchMode" id="despatchMode" maxlength="50" size="20" readonly="true" value="<?php echo $despatch_mode;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Vehicle No.:</td>
			<td><input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20" readonly="true" value="<?php echo $vehicle_num;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="iltritem"  method="post" onsubmit="return validate_ilt_item()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" width="15%" nowrap>Item Name:</td>
			<td width="35%"><input name="itemName" id="itemName" maxlength="50" size="40" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="15%" nowrap>Stock On Date:</td>
			<td width="35%"><input name="itemStock" id="itemStock" maxlength="10" size="10" readonly="true" value="<?php echo number_format($clqnty_prime,3,".","");?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span><?php echo $prime_unit_name; if($alt_unit=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';} else {echo "";}?></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Despatch Qnty.:</td>
			<td><input name="despatchQnty" id="despatchQnty" maxlength="10" size="10" readonly="true" value="<?php echo $despatch_qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span><?php echo $unit_name;?></span></td>
			
			<td class="th" nowrap>Receive Qnty.:</td>
			<td><input name="receiveQnty" id="receiveQnty" maxlength="10" size="10" value="<?php echo $receive_qnty;?>" >&nbsp;<span><?php echo $unit_name;?></span></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='iltritem.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='iltritem.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='iltreceive.php'"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='iltritem.php?action=receive&mid=<?php echo $mid;?>'" ><img src="images/receive.gif" width="82" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Inter Location Transfer - [ Receive Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="45%">Item Name</th>
			<th width="20%">Despatch Qnty.</th>
			<th width="20%">Receive Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_ilt = mysql_query("SELECT tblilt2.*, item_name, unit_name FROM tblilt2 INNER JOIN .item ON tblilt2.item_id = item.item_id INNER JOIN .unit ON tblilt2.unit_id = unit.unit_id WHERE ilt_id=".$mid." ORDER BY seq_no") or die(mysql_error());
		while($row_ilt=mysql_fetch_array($sql_ilt)){
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "iltritem.php?action=delete&mid=".$mid."&rid=".$row_ilt['rec_id'];
			$edit_ref = "iltritem.php?action=edit&mid=".$mid."&rid=".$row_ilt['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_ilt['item_name'].'</td><td align="right">'.$row_ilt['despatch_qnty']." ".$row_ilt['unit_name'].'</td><td align="right">'.$row_ilt['receive_qnty']." ".$row_ilt['unit_name'].'</td>';
			if($row_user['ilt2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ilt2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ilt3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ilt3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</center>
</body>
</html>