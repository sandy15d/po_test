<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT ps1,ps2,ps3,ps4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
$msg = "";
$pid = $_REQUEST['pid'];
$sql1 = mysql_query("SELECT tblpstock.*, location_name FROM tblpstock INNER JOIN location ON tblpstock.location_id = location.location_id WHERE ps_id=".$pid);
$row1 = mysql_fetch_assoc($sql1);
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_query("SELECT tblpstock_item.*, unit_name FROM tblpstock_item INNER JOIN unit ON tblpstock_item.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
	$row2 = mysql_fetch_assoc($sql2);
	/*-------------------------------*/
	$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
	$row4=mysql_fetch_assoc($sql4);
	if($row4['alt_unit']=="A" && $row4['alt_unit_id']!="0"){
		$sql5=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$row4['alt_unit_id']);
		$row5=mysql_fetch_assoc($sql5);
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$sql=mysql_query("SELECT item_name, item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname=$row["item_name"];
	/*-------------------------------*/
	if($row['prime_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'];
	} elseif($row['alt_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'] / $row['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$datePStock=$row1["ps_date"];
	$particulars = $row1['location_name'];
//	$pno = $row1['ps_no'];
	$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
	$entrymode = ($row1['ps_type']=="I" ? "P+" : "P-");
	/*-------------------------------*/
	$sql = mysql_query("SELECT * FROM tblpstock_item WHERE ps_id=".$pid." AND item_id=".$_POST['item']) or die(mysql_error());
	$row_ps = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_ps['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into physical stock record.";
			elseif($row_ps['rec_id']==$rid)
				$res = mysql_query("UPDATE tblpstock_item SET item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",ps_qnty=".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty'])." WHERE rec_id=".$rid) or die(mysql_error());
				$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".($entrymode=="P+" ? $itemQnty : -1*$itemQnty)." WHERE entry_mode='".$entrymode."' AND entry_id=".$pid." AND entry_date='".$datePStock."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty']).",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="psitem.php?action=new&pid='.$pid.'";</script>';
		} elseif($count==0){
			$res = mysql_query("UPDATE tblpstock_item SET item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",ps_qnty=".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty'])." WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".($entrymode=="P+" ? $itemQnty : -1*$itemQnty)." WHERE entry_mode='".$entrymode."' AND entry_id=".$pid." AND entry_date='".$datePStock."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty']).",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="psitem.php?action=new&pid='.$pid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblpstock_item WHERE rec_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='".$entrymode."' AND entry_id=".$pid." AND entry_date='".$datePStock."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty']).",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="psitem.php?action=new&pid='.$pid.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into physical stock record.";
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblpstock_item");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblpstock_item WHERE ps_id=".$pid);
			$row = mysql_fetch_assoc($sql);
			$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tblpstock_item (rec_id,ps_id,seq_no,item_id,unit_id,ps_qnty) VALUES(".$rid.",".$pid.",".$sno.",".$_POST['item'].",".$_POST['unit'].",".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty']).")";
			$res = mysql_query($sql) or die(mysql_error());
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,unit_id,item_qnty,item_rate) VALUES(".$sid.",'".$entrymode."',".$pid.",'".$datePStock."',".$sno.",".$row1['location_id'].",".$_POST['item'].",".$unitid.",".($entrymode=="P+" ? $itemQnty : -1*$itemQnty).",0)";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".($entrymode=="P+" ? $_POST['itemQnty'] : -1*$_POST['itemQnty']).",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="psitem.php?action=new&pid='.$pid.'";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_psitem()
{
	var err="";
	if(document.getElementById("item").value==0)
		err = "* please select an item to issue!\n";
	if(document.getElementById("itemQnty").value!="" && ! IsNumeric(document.getElementById("itemQnty").value))
		err += "* please input valid (numeric only) quantity of the item!\n";
	if(parseFloat(document.getElementById("itemQnty").value)==0 || document.getElementById("itemQnty").value=="")
		err += "* Item's quantity is mandatory field!\n";
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
<table align="center" cellspacing="0" cellpadding="0" height="200px" width="675px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Physical Stock - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>PS No.:</td>
			<?php if(isset($_REQUEST["action"])){
				$ps_number = ($row1['ps_no']>999 ? $row1['ps_no'] : ($row1['ps_no']>99 && $row1['ps_no']<1000 ? "0".$row1['ps_no'] : ($row1['ps_no']>9 && $row1['ps_no']<100 ? "00".$row1['ps_no'] : "000".$row1['ps_no'])));
				if($row1['ps_prefix']!=null){$ps_number = $row1['ps_prefix']."/".$ps_number;}
			}?>
			<td><input name="pstockNo" id="pstockNo" maxlength="15" size="20" readonly="true" value="<?php echo $ps_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>PS Date:</td>
			<td><input name="pstockDate" id="pstockDate" maxlength="10" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo date("d-m-Y",strtotime($row1["ps_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<?php
			$type1_status = 'checked';
			$type2_status = 'unchecked';
			if(isset($_REQUEST["action"])){
				$selected_radio = $row1["ps_type"];
				if($selected_radio=="I"){
					$type1_status = 'checked';
					$type2_status = 'unchecked';
				} elseif($selected_radio=="D"){
					$type1_status = 'unchecked';
					$type2_status = 'checked';
				}
			} ?>
			<td class="th" nowrap>Stock Volume:</td>
			<td><input type="radio" name="pstockType" id="type1" value="I" <?php echo $type1_status;?> disabled>&nbsp;Increased&nbsp;&nbsp;<input type="radio" name="pstockType" id="type2" value="D" <?php echo $type2_status;?> disabled>&nbsp;Decreased&nbsp;&nbsp;</td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Location:</td>
			<td><input type="hidden" name="location" id="location" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1['location_id'];}?>">

<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1['location_name'];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
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
	<form name="psitem"  method="post" onsubmit="return validate_psitem()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" nowrap>Item Name:</td>
			<td><select name="item" id="item" onchange="get_curent_stock_of_item(this.value)" style="width:300px"><option value="0">-- Select --</option><?php 
			$sql_item=mysql_query("SELECT * FROM item ORDER BY item_name");
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
				$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$row2["item_id"]." AND location_id=".$row1['location_id']." AND entry_date<='".date("Y-m-d",strtotime($row1["ps_date"]))."' GROUP BY unit_id") or die(mysql_error());
				while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
					if($row_stk_rgstr['unit_id']==$row4['prime_unit_id']){
						$clqnty_prime += $row_stk_rgstr['qty'];
						$clqnty_alt += $row_stk_rgstr['qty'] * $row4['alt_unit_num'];
					} elseif($row_stk_rgstr['unit_id']==$row4['alt_unit_id']){
						$clqnty_prime += $row_stk_rgstr['qty'] / $row4['alt_unit_num'];
						$clqnty_alt += $row_stk_rgstr['qty'];
					}
				}
			}?>
			<td class="th" nowrap>Stock On Date:</td>
			<td width="36%"><input name="itemStock" id="itemStock" maxlength="10" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo number_format($clqnty_prime,3,".","");}?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row4['prime_unit_name']; if($row4['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$row5['alt_unit_name'].')</span>';}} else {echo "";}?></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Stock Physical:</td>
			<td><input name="itemQnty" id="itemQnty" maxlength="10" size="15" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["ps_qnty"];}?>" ></td>
			
			<td class="th" nowrap>Unit :</td>
			<td id="tblcol1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($row4['alt_unit']=="N") || ($row4['alt_unit']=="A" && $row4['alt_unit_id']==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option></select>';
				} elseif($row4['alt_unit']=="A" && $row4['alt_unit_id']!=0){
					if($row2['unit_id']==$row4['prime_unit_id']){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option><option value="'.$row4['alt_unit_id'].'">'.$row5['alt_unit_name'].'</option></select>';
					} elseif($row2['unit_id']==$row4['alt_unit_id']){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option><option selected value="'.$row4['alt_unit_id'].'">'.$row5['alt_unit_name'].'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"><option value="0">&nbsp;</option></select>';
			}?></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['ps1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['ps1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.psitem.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='psitem.php?action=new&pid=<?php echo $pid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='psitem.php?action=new&pid=<?php echo $pid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='physicalstock1.php?pid=<?php echo $pid;?>'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>Physical Stock - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="55%">Item Name</th>
			<th width="30%">Item Stock</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_ps = mysql_query("SELECT tblpstock_item.*, item_name, unit_name FROM tblpstock_item INNER JOIN item ON tblpstock_item.item_id = item.item_id INNER JOIN unit ON tblpstock_item.unit_id = unit.unit_id WHERE ps_id=".$pid." ORDER BY seq_no") or die(mysql_error());
		while($row_ps=mysql_fetch_array($sql_ps)){
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "psitem.php?action=delete&pid=".$pid."&rid=".$row_ps['rec_id'];
			$edit_ref = "psitem.php?action=edit&pid=".$pid."&rid=".$row_ps['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_ps['item_name'].'</td><td align="right">'.$row_ps['ps_qnty']." ".$row_ps['unit_name'].'</td>';
			if($row_user['ps2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ps2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ps3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ps3']==0)
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
