<?php 
include("menu.php");
//----------------------------------------//
$sql_user = mysql_query("SELECT pay1,pay2,pay3,pay4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
//----------------------------------------//
$msg = "";
$pid = $_REQUEST['pid'];
$sql = mysql_query("SELECT tblpayment1.*, party_name, address1, address2, address3, city_name, state_name, company_name, IFNULL(bank.bank_name, '') AS bankname FROM tblpayment1 INNER JOIN party ON tblpayment1.party_id = party.party_id INNER JOIN company ON tblpayment1.company_id = company.company_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id LEFT OUTER JOIN bank ON tblpayment1.bank_id = bank.bank_id WHERE pay_id=".$pid) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
//----------------------------------------//
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$bid = $_REQUEST['bid'];
	$rid = $_REQUEST['rid'];
	$sql1 = mysql_query("SELECT * FROM tblbill WHERE bill_id=".$bid) or die(mysql_error());
	$row1 = mysql_fetch_assoc($sql1);
	$sql2 = mysql_query("SELECT * FROM tblpayment2 WHERE rec_id=".$rid) or die(mysql_error());
	$row2 = mysql_fetch_assoc($sql2);
}
//----------------------------------------//
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="done"){
	$sqlpay = mysql_query("SELECT tblpayment2.*,bill_amt FROM tblpayment2 INNER JOIN tblbill ON tblpayment2.bill_id = tblbill.bill_id WHERE pay_id=".$pid." ORDER BY bill_id") or die(mysql_error());
	while($rowpay=mysql_fetch_array($sqlpay))
	{
		if($rowpay['bill_id']>0){
			$sql_pay = mysql_query("SELECT Sum(paid_amt+deduct) AS pamt FROM tblpayment2 WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
			$row_pay=mysql_fetch_assoc($sql_pay);
			if(mysql_num_rows($sql_pay)>0){
				if($rowpay['bill_amt']==$row_pay['pamt']){
					$res = mysql_query("UPDATE tblbill SET bill_paid='Y' WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
				}
			}
		}
	}
}
//----------------------------------------//
if(isset($_POST['submit'])){
	$datePayment = $row['pay_date'];
	$paidAmt = ($_POST['paidAmount']=="" ? 0 : $_POST['paidAmount']);
	$deductAmt = ($_POST['deductAmount']=="" ? 0 : $_POST['deductAmount']);
	//---------------------------------//
	$particulars = "To ".$row['party_name'];
	$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
	if($_POST['payMode']==1)
		$itemname = "Paid On Account";
	elseif($_POST['payMode']==2)
		$itemname = "Paid Against Bill No. ".$_POST['bnum'];
	elseif($_POST['payMode']==3)
		$itemname = "Paid in Advance";
	//---------------------------------//
	$sql = mysql_query("SELECT * FROM tblpayment2 WHERE pay_id=".$pid." AND pay_mode=".$_POST['payMode']." AND bill_id=".$_POST['billNo']." AND paid_amt=".$paidAmt) or die(mysql_error());
	$row_pay = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_pay['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into bill payment record.";
			elseif($row_pay['rec_id']==$rid) {
				$res = mysql_query("UPDATE tblpayment2 SET pay_id=".$pid.",pay_mode=".$_POST['payMode'].",bill_id=".$_POST['billNo'].",paid_amt=".$paidAmt.",deduct=".$deductAmt." WHERE rec_id=".$rid) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = $row["maxid"] + 1;
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE tblpayment2 SET pay_id=".$pid.",pay_mode=".$_POST['payMode'].",bill_id=".$_POST['billNo'].",paid_amt=".$paidAmt.",deduct=".$deductAmt." WHERE rec_id=".$rid) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$sqlpay = mysql_query("SELECT * FROM tblpayment2 WHERE rec_id=".$pid) or die(mysql_error());
		$rowpay=mysql_fetch_array($sqlpay);
		if($rowpay['bill_id']>0){
			$res = mysql_query("UPDATE tblbill SET bill_paid='N' WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
		}
		$res = mysql_query("DELETE FROM tblpayment2 WHERE rec_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = $row["maxid"] + 1;
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into bill payment record.";
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblpayment2");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = "INSERT INTO tblpayment2 (rec_id,pay_id,pay_mode,bill_id,paid_amt,deduct) VALUES(".$rid.",".$pid.",".$_POST['payMode'].",".$_POST['billNo'].",".$paidAmt.",".$deductAmt.")";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
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
function validate_paylist()
{
	var err="";
	var pamt = damt = bamt = 0;
	var netPaidAmt = parseFloat(document.getElementById("payAmount").value);
	var totalPaidAmt = parseFloat(document.getElementById("totalPaid").value);
	var curPaidAmt = parseFloat(document.getElementById("paidAmount").value);
	var selectPaidAmt = parseFloat(document.getElementById("selectedPay").value);
	
	if(document.getElementById("payMode").value==0)
		err = "* please select mode of payment, it is mandatory!\n";
	if(document.getElementById("payMode").value==2 && document.getElementById("billNo").value==0)
		err += "* please select bill number, it is mandatory!\n";
	if(document.getElementById("paidAmount").value=="")
		err += "* please input paid amount, it is mandatory!\n";
	if(document.getElementById("paidAmount").value!="" && ! IsNumeric(document.getElementById("paidAmount").value))
		err += "* please input valid numeric data for paid amount!\n";
	if(document.getElementById("deductAmount").value!="" && ! IsNumeric(document.getElementById("deductAmount").value))
		err += "* please input valid numeric data for deducted amount!\n";
	if(document.getElementById("paidAmount").value!="")
		pamt = parseFloat(document.getElementById("paidAmount").value);
	if(document.getElementById("deductAmount").value=="")
		damt = 0;
	else
		damt = parseFloat(document.getElementById("deductAmount").value);
	if(document.getElementById("billAmount").value=="")
		bamt = 0;
	else
		bamt = parseFloat(document.getElementById("billAmount").value);
	if(document.getElementById("payMode").value==2){
		if(pamt+damt>bamt){err += "* Payment exceeding bill amount is not acceptable!\n";}
	}
	if(document.getElementById("xson").value=="new"){
		if((totalPaidAmt+curPaidAmt)>netPaidAmt){err += "* Total Paid Amount exceeding actual Pay Amount and it is not acceptable!\n";}
	} else if(document.getElementById("xson").value=="edit"){
		if((totalPaidAmt-selectPaidAmt+curPaidAmt)>netPaidAmt){err += "* Total Paid Amount exceeding actual Pay Amount and it is not acceptable!\n";}
	}
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
<form name="billpaylist"  method="post" onsubmit="return validate_paylist()">
<table align="center" cellspacing="0" cellpadding="0" height="450px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Payment - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Payment No.:</td>
			<td><input name="paymentNo" id="paymentNo" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo ($row['pay_id']>999 ? $row['pay_id'] : ($row['pay_id']>99 && $row['pay_id']<1000 ? "0".$row['pay_id'] : ($row['pay_id']>9 && $row['pay_id']<100 ? "00".$row['pay_id'] : "000".$row['pay_id'])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Payment Date:</td>
			<td><input name="paymentDate" id="paymentDate" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo date("d-m-Y",strtotime($row["pay_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
 		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="party" id="party" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo $row['party_name'];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>company Name:</td>
			<td><input name="companyName" id="companyName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["company_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address1"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Payment Type:</td>
			<?php 
			if($row["pay_type"]=="1"){$paytype = "Cash";} elseif($row["pay_type"]=="2"){$paytype = "Cheque";} elseif($row["pay_type"]=="3"){$paytype = "Draft";} elseif($row["pay_type"]=="4"){$paytype = "ePay";}
			?>
			<td><input name="payType" id="payType" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo $paytype;}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address2"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Chq/DD/ePay No.:</td>
			<td><span id="chqnum"><input name="chequeNumber" id="chequeNumber" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["chq_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address3"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Chq/DD/ePay Date:</td>
			<td><input name="chequeDate" id="chequeDate" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo date("d-m-Y",strtotime($row["chq_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF" ></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["city_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Bank Name:</td>
			<td><input name="bankName" id="bankName" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["bankname"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["state_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Pay Amount:</td>
			<td><input name="payAmount" id="payAmount" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["pay_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Remarks:</td>
			<td rowspan="3"><textarea name="remarks" id="remarks" readonly="true" cols="35" rows="4" style="background-color:#E7F0F8; color:#0000FF"><?php if(isset($_REQUEST["action"])){ echo $row["remark"];}?></textarea></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
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
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Payment Mode:<span style="color:#FF0000">*</span></td>
			<td><select name="payMode" id="payMode" style="width:100px" onchange="get_controls_on_paymode(this.value,<?php echo $row['party_id'];?>,<?php echo $pid;?>,<?php echo strtotime($row['pay_date']);?>)" ><option value="0">-- Select --</option>
			<?php 
			if($row2["pay_mode"]=="1")
				echo '<option selected value="1">On Account</option>';
			else
				echo '<option value="1">On Account</option>';
			if($row2["pay_mode"]=="2")
				echo '<option selected value="2">Against Bill</option>';
			else
				echo '<option value="2">Against Bill</option>';
			if($row2["pay_mode"]=="3")
				echo '<option selected value="3">Advance</option>';
			else
				echo '<option value="3">Advance</option>';
			?>
			</select></td>
			
			<td class="th" nowrap>Bill Amount:</td>
			<td><span id="bamt_span"><input name="billAmount" id="billAmount" maxlength="15" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row1["bill_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF"></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Bill No.:</td>
			<td><span id="bnum_span">
			<?php 
			if($_REQUEST["action"]=="new"){
				echo '<select name="billNo" id="billNo" style="width:300px" onchange="get_bill_details(this.value)">
				<option value="0">-- Select --</option>';
				$sql_bill=mysql_query("SELECT * FROM tblbill WHERE (party_id=".$row['party_id']." AND bill_return=0 AND bill_paid='N' AND bill_date<='".$row['pay_date']."') AND bill_id NOT IN (SELECT bill_id FROM tblpayment2 WHERE pay_id=".$pid.") ORDER BY bill_no");
				while($row_bill=mysql_fetch_array($sql_bill))
				{
					if($row_bill["bill_id"]==$row1["bill_id"])
						echo '<option selected value="'.$row_bill["bill_id"].'">'.$row_bill["bill_no"].'</option>';
					else
						echo '<option value="'.$row_bill["bill_id"].'">'.$row_bill["bill_no"].'</option>';
				}
				echo '</select>';
			} elseif(($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete") && $row2["pay_mode"]=="2"){
				echo '<select name="billNo" id="billNo" style="width:300px" onchange="get_bill_details(this.value)">
				<option value="0">-- Select --</option>';
				$sql_bill=mysql_query("SELECT * FROM tblbill WHERE (party_id=".$row['party_id']." AND bill_return=0 AND bill_paid='N' AND bill_date<='".$row['pay_date']."') AND bill_id NOT IN (SELECT bill_id FROM tblpayment2 WHERE pay_id=".$pid." AND bill_id!=".$row1["bill_id"].") ORDER BY bill_no");
				while($row_bill=mysql_fetch_array($sql_bill))
				{
					if($row_bill["bill_id"]==$row1["bill_id"])
						echo '<option selected value="'.$row_bill["bill_id"].'">'.$row_bill["bill_no"].'</option>';
					else
						echo '<option value="'.$row_bill["bill_id"].'">'.$row_bill["bill_no"].'</option>';
				}
				echo '</select>';
			} else {
				echo '<select name="billNo" id="billNo" style="background-color:#E7F0F8; color:#0000FF; width:300px">
				<option value="0">-- Select --</option>';
				echo '</select>';
			}
			?>
			</span></td>
			
			<td class="th" nowrap>Paid Amount:</td>
			<td><input name="paidAmount" id="paidAmount" maxlength="15" size="20" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["paid_amt"];}?>" ></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Bill Date:</td>
			<td><span id="bdate_span"><input name="billDate" id="billDate" maxlength="10" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){if($row1["bill_date"]=="") echo ""; else echo date("d-m-Y",strtotime($row1["bill_date"]));} else {echo "";}?>" style="background-color:#E7F0F8; color:#0000FF"></span></td>
			
			<td class="th" nowrap>Deduction:</td>
			<td><span id="deduct_span">
			<?php 
			if($_REQUEST["action"]=="new"){
				echo '<input name="deductAmount" id="deductAmount" maxlength="15" size="20" value="">';
			} elseif(($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete") && $row2["pay_mode"]=="2"){
				echo '<input name="deductAmount" id="deductAmount" maxlength="15" size="20" value="'.$row2["deduct"].'" >';
			} else {
				echo '<input name="deductAmount" id="deductAmount" maxlength="15" size="20" readonly="true" value=""  style="background-color:#E7F0F8; color:#0000FF">';
			}
			?>
			</span></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['pay1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['pay1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.billpaylist.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='paybill.php?action=new&pid=<?php echo $pid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='paybill.php?action=new&pid=<?php echo $pid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
		&nbsp;&nbsp;<a href="javascript:window.location='payment.php?action=new'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='paybill.php?action=done&pid=<?php echo $pid;?>'" ><img src="images/done.gif" width="80" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>List of Paid Bills</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="12%">Pay Mode</th>
			<th width="23%">Bill No.</th>
			<th width="10%">Bill Date</th>
			<th width="15%">Bill Amount</th>
			<th width="15%">Paid Amount</th>
			<th width="10%">Deduction</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$total_paid = 0;
		$sql_pay = mysql_query("SELECT * FROM tblpayment2 WHERE pay_id=".$pid." ORDER BY bill_id") or die(mysql_error());
		while($row_pay=mysql_fetch_array($sql_pay))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "paybill.php?action=delete&pid=".$pid."&bid=".$row_pay['bill_id']."&rid=".$row_pay['rec_id'];
			$edit_ref = "paybill.php?action=edit&pid=".$pid."&bid=".$row_pay['bill_id']."&rid=".$row_pay['rec_id'];
			$total_paid += $row_pay['paid_amt'];
			
			$paymode = "&nbsp;";
			if($row_pay["pay_mode"]=="1")
				$paymode = "On Account";
			elseif($row_pay["pay_mode"]=="2")
				$paymode = "Against Bill";
			elseif($row_pay["pay_mode"]=="3")
				$paymode = "Advance";
			
			$billno = "&nbsp;";
			$billdate = "&nbsp;";
			$billamt = "&nbsp;";
			if($row_pay["bill_id"]>0){
				$sql_bill = mysql_query("SELECT * FROM tblbill WHERE bill_id=".$row_pay['bill_id']) or die(mysql_error());
				$row_bill = mysql_fetch_assoc($sql_bill);
				$billno = $row_bill['bill_no'];
				$billdate = date("d-m-Y",strtotime($row_bill['bill_date']));
				$billamt = $row_bill['bill_amt'];
			}
			
			echo '<td align="center">'.$i.'.</td><td>'.$paymode.'</td><td>'.$billno.'</td><td align="center">'.$billdate.'</td><td align="right">'.$billamt.'</td><td align="right">'.$row_pay['paid_amt'].'</td><td align="right">'.$row_pay['deduct'].'</td>';
			if($row_user['pay2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pay2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['pay3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pay3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<input type="hidden" name="xson" id="xson" value="<?php echo $_REQUEST["action"];?>" /><input type="hidden" name="totalPaid" id="totalPaid" value="<?php echo $total_paid;?>" /><input type="hidden" name="selectedPay" id="selectedPay" value="<?php if($_REQUEST["action"]=="edit"){echo $row2["paid_amt"];} else {echo '0';}?>" /><input type="hidden" name="bnum" id="bnum" value="" />
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