<?php 
include("menu.php");
/*----------------------------------------*/
$sql_user = mysql_query("SELECT cp1,cp2,cp3,cp4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*----------------------------------------*/
$msg = "";
$tid = $_REQUEST['tid'];
$sql1 = mysql_query("SELECT tblcashmemo.*, company_name,location_name FROM tblcashmemo INNER JOIN company ON tblcashmemo.company_id = company.company_id INNER JOIN location ON tblcashmemo.location_id = location.location_id WHERE txn_id=".$tid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$txn_number = ($row1['txn_id']>999 ? $row1['txn_id'] : ($row1['txn_id']>99 && $row1['txn_id']<1000 ? "0".$row1['txn_id'] : ($row1['txn_id']>9 && $row1['txn_id']<100 ? "00".$row1['txn_id'] : "000".$row1['txn_id'])));
$memo_no = $row1["memo_no"];
$memo_date = date("d-m-Y",strtotime($row1["memo_date"]));
$particulars = $row1["particulars"];
$memo_amt = $row1["memo_amt"];
$company_name = $row1['company_name'];
$location_id = $row1['location_id'];
$location_name = $row1['location_name'];
/*----------------------------------------*/
$rid = 0;
$indent_id = 0;
$indent_date = "";
$item_id = 0;
$unit_id = 0;
$memo_qnty = "";
$rate = "";
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
           
		$sql2 = mysql_query("SELECT tblcash_item.*, unit_name FROM tblcash_item INNER JOIN unit ON tblcash_item.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$indent_id = $row2["indent_id"];
		$item_id = $row2["item_id"];
		$memo_qnty = $row2["memo_qnty"];
		$unit_id = $row2['unit_id'];
		$rate = $row2["rate"];
		/*----------------------------------------*/
		$sql3 = mysql_query("SELECT * FROM tbl_indent WHERE indent_id=".$indent_id) or die(mysql_error());
		$row3 = mysql_fetch_assoc($sql3);
		$indent_date = date("d-m-Y",strtotime($row3['indent_date']));
		/*----------------------------------------*/
		$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$item_id);
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
		/*----------------------------------------*/
		$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$item_id." AND location_id=".$location_id." AND entry_date<='".date("Y-m-d",strtotime($memo_date))."' GROUP BY unit_id") or die(mysql_error());
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
/*----------------------------------------*/
if(isset($_POST['submit'])){
	$sql=mysql_query("SELECT item_name, item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname = $row["item_name"];
	$itemamt = number_format($_POST['itemQnty'] * $_POST['rate'],2,".","");
	/*-------------------------------*/
	if($row['prime_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'];
		$itemRate = $_POST['rate'];
	} elseif($row['alt_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'] / $row['alt_unit_num'];
		$itemRate = ($_POST['rate']==0 ? 0 : $itemamt / $_POST['rate']);
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$dateMemo=$row1["memo_date"];
	$particulars = "From ".$row1['particulars'];
	$voucherid = ($tid>999 ? $tid : ($tid>99 && $tid<1000 ? "0".$tid : ($tid>9 && $tid<100 ? "00".$tid : "000".$tid)));
	/*-------------------------------*/
	$sql=mysql_query("SELECT * FROM tblcash_item WHERE txn_id=".$tid." AND item_id=".$_POST['item']." AND indent_id=".$_POST['indentNo']) or die(mysql_error());
	$row_cash = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_cash['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into cash purchase record.";
			elseif($row_cash['rec_id']==$rid){
				$res = mysql_query("UPDATE tblcash_item SET indent_id=".$_POST['indentNo'].",item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",memo_qnty=".$_POST['itemQnty'].",rate=".$_POST['rate']." WHERE rec_id=".$rid) or die(mysql_error());
				$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".$itemQnty.",item_rate=".$itemRate.",item_amt=".$itemamt." WHERE entry_mode='C+' AND entry_id=".$tid." AND entry_date='".$dateMemo."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount, voucher_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['rate'].",".$itemamt.",".$row1['memo_amt'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE tblcash_item SET indent_id=".$_POST['indentNo'].",item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",memo_qnty=".$_POST['itemQnty'].",rate=".$_POST['rate']." WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".$itemQnty.",item_rate=".$itemRate.",item_amt=".$itemamt." WHERE entry_mode='C+' AND entry_id=".$tid." AND entry_date='".$dateMemo."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount, voucher_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['rate'].",".$itemamt.",".$row1['memo_amt'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblcash_item WHERE rec_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='C+' AND entry_id=".$tid." AND entry_date='".$dateMemo."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']." AND item_id=".$row2['item_id']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount, voucher_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['rate'].",".$itemamt.",".$row1['memo_amt'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into material issue record.";
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblcash_item");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblcash_item WHERE txn_id=".$tid);
			$row = mysql_fetch_assoc($sql);
			$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tblcash_item (rec_id,txn_id,indent_id,seq_no,item_id,unit_id,memo_qnty,rate) VALUES(".$rid.",".$tid.",".$_POST['indentNo'].",".$sno.",".$_POST['item'].",".$unitid.",".$_POST['itemQnty'].",".$_POST['rate'].")";
			$res = mysql_query($sql);
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_qnty,unit_id,item_rate,item_amt) VALUES(".$sid.",'C+',".$tid.",'".$dateMemo."',".$sno.",".$row1['location_id'].",".$_POST['item'].",".$itemQnty.",".$unitid.",".$itemRate.",".$itemamt.")";
			$res = mysql_query($sql) ;
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount, voucher_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['rate'].",".$itemamount.",".$row1['memo_amt'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) ;
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
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
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_cpitem()
{
	var err="";
	if(document.getElementById("indentNo").value==0)
		err = "* please select an indent number!\n";
	if(document.getElementById("item").value==0)
		err += "* please select an item!\n";
	if(document.getElementById("itemQnty").value!="" && ! IsNumeric(document.getElementById("itemQnty").value))
		err += "* please input valid quantity of the item!\n";
	if(parseFloat(document.getElementById("itemQnty").value)==0 || document.getElementById("itemQnty").value=="")
		err += "* Item's quantity is mandatory field!\n";
	if(document.getElementById("rate").value!="" && ! IsNumeric(document.getElementById("rate").value))
		err += "* please input valid numeric rate of the item!\n";
	if(parseFloat(document.getElementById("rate").value)==0 || document.getElementById("rate").value=="")
		err += "* Item's rate is mandatory field!\n";
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
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="850px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Cash Purchase - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Txn.No.:</td>
			<td><input name="txnNo" id="txnNo" maxlength="15" size="20" readonly="true" value="<?php echo $txn_number;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Memo No.:</td>
			<td><input name="memoNo" id="memoNo" maxlength="15" size="20" readonly="true" value="<?php echo $memo_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Memo Date:</td>
			<td><input name="memoDate" id="memoDate" maxlength="10" size="10" readonly="true" value="<?php echo $memo_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Particulars:</td>
			<td><input name="particulars" id="particulars" maxlength="50" size="45" readonly="true" value="<?php echo $particulars;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Purchase Amount:</td>
			<td><input name="purchaseAmount" id="purchaseAmount" maxlength="10" size="10" readonly="true" value="<?php echo $memo_amt;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Company Name:</td>
			<td><input name="company" id="company" maxlength="50" size="45" readonly="true" value="<?php echo $company_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th">Location:</td>
			<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="<?php echo $location_name;?>" style="background-color:#E7F0F8; color:#0000FF"><input type="hidden" name="location" id="location" value="<?php echo $location_id; ?>" /></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="cpitem"  method="post" onsubmit="return validate_cpitem()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td width="13%" class="th" nowrap>Indent No.:<span style="color:#FF0000">*</span></td>
			<td width="36%"><select name="indentNo" id="indentNo" onchange="get_item_of_indent(this.value)" style="width:290px"><option value="0">-- Select --</option><?php 
			$sql3 = mysql_query("SELECT * FROM tbl_indent WHERE order_from=".$row1['location_id']." AND indent_date<='".$row1['memo_date']."' ORDER BY indent_date, indent_no") or die(mysql_error());
			while($row3=mysql_fetch_array($sql3)){
				$indent_number = ($row3['indent_no']>999 ? $row3['indent_no'] : ($row3['indent_no']>99 && $row3['indent_no']<1000 ? "0".$row3['indent_no'] : ($row3['indent_no']>9 && $row3['indent_no']<100 ? "00".$row3['indent_no'] : "000".$row3['indent_no'])));
				if($row3['ind_prefix']!=null){$indent_number = $row3['ind_prefix']."/".$indent_number;}
				if($row3["indent_id"]==$indent_id){
					echo '<option selected value="'.$row3["indent_id"].'">'.$indent_number.",  ".date("d-m-Y",strtotime($row3['indent_date'])).'</option>';
				} else {
					echo '<option value="'.$row3["indent_id"].'">'.$indent_number.",  ".date("d-m-Y",strtotime($row3['indent_date'])).'</option>';
				}
			}?>
			</select></td>
			
			<td width="14%" class="th" nowrap>Indent Date:</td>
			<td width="37%"><input name="indentDate" id="indentDate" maxlength="10" size="10" readonly="true" value="<?php echo $indent_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Item Name:<span style="color:#FF0000">*</span></td>
			<td id="tdItem">
                            
                            
                            <select name="item" id="item" onchange="get_curent_stock_of_item(this.value)" style="width:290px"><option value="0">-- Select --</option><?php 
                        
			$sql_item=mysql_query("SELECT tbl_indent_item.item_id, item_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id WHERE indent_id=".$indent_id." ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($row_item["item_id"]==$item_id)
					echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
				else
					echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>Stock On Date:</td>
			<td><input name="itemStock" id="itemStock" maxlength="10" size="10" readonly="true" value="<?php echo number_format($clqnty_prime,3,".","");?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1"><?php echo $prime_unit_name; if($alt_unit=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';} else {echo "";}?></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Item Qnty.:<span style="color:#FF0000">*</span></td>
			<td><input name="itemQnty" id="itemQnty" maxlength="10" size="10" value="<?php echo $memo_qnty;?>"></td>
			
			<td class="th" nowrap>Unit :</td>
			<td id="tblcol1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($alt_unit=="N") || ($alt_unit=="A" && $alt_unit_id==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option></select>';
				} elseif($alt_unit=="A" && $alt_unit_id!=0){
					if($unit_id==$prime_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					} elseif($unit_id==$alt_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option selected value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"><option value="0">&nbsp;</option></select>';
			}?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Rate:<span style="color:#FF0000">*</span></td>
			<td><input name="rate" id="rate" maxlength="10" size="10" value="<?php echo $rate;?>"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<?php 
		if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['cp1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['cp1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.cpitem.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='cashpuritem.php?action=new&tid=<?php echo $tid;?>'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='cashpuritem.php?action=new&tid=<?php echo $tid;?>'"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='cashpurchase1.php?tid=<?php echo $tid;?>'"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>Cash Purchase - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="3%">Sl.No.</th>
			<th width="40%">Item Name</th>
			<th width="15%">Indent No.</th>
			<th width="10%">Date</th>
			<th width="10%">Item Qnty.</th>
			<th width="8%">Unit</th>
			<th width="8%">Rate</th>
			<th width="3%">Edit</th>
			<th width="3%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_item = mysql_query("SELECT tblcash_item.*, item_name, unit_name FROM tblcash_item INNER JOIN item ON tblcash_item.item_id = item.item_id INNER JOIN unit ON tblcash_item.unit_id = unit.unit_id WHERE txn_id=".$tid." ORDER BY seq_no") or die(mysql_error());
		while($row_item=mysql_fetch_array($sql_item))
		{
			$sql_ind = mysql_query("SELECT * FROM tbl_indent WHERE indent_id=".$row_item['indent_id']) or die(mysql_error());
			$row_ind=mysql_fetch_array($sql_ind);
			$indentNumber = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
			if($row_ind['ind_prefix']!=null){$indentNumber = $row_ind['ind_prefix']."/".$indentNumber;}
			$indentDate = date("d-m-Y",strtotime($row_ind['indent_date']));
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "cashpuritem.php?action=delete&tid=".$tid."&rid=".$row_item['rec_id'];
			$edit_ref = "cashpuritem.php?action=edit&tid=".$tid."&rid=".$row_item['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_item['item_name'].'</td><td>'.$indentNumber.'</td><td>'.$indentDate.'</td><td align="center">'.$row_item['memo_qnty'].'</td><td>'.$row_item['unit_name'].'</td><td>'.$row_item['rate'].'</td>';
			if($row_user['cp2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['cp2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['cp3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['cp3']==0)
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