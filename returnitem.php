<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT rr1,rr2,rr3,rr4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
$msg = "";
$rid = $_REQUEST['rid'];
$sql = mysql_query("SELECT * FROM tblreceipt_return1 WHERE return_id=".$rid) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
$return_no = ($row['return_no']>999 ? $row['return_no'] : ($row['return_no']>99 && $row['return_no']<1000 ? "0".$row['return_no'] : ($row['return_no']>9 && $row['return_no']<100 ? "00".$row['return_no'] : "000".$row['return_no'])));
$return_date = date("d-m-Y",strtotime($row["return_date"]));
$receipt_id = $row["receipt_id"];

$sql1 = mysql_query("SELECT tblreceipt1.*, transit_name FROM tblreceipt1 INNER JOIN transit ON tblreceipt1.transit_point = transit.transit_id WHERE receipt_id=".$receipt_id) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$receipt_no = ($row1['receipt_no']>999 ? $row1['receipt_no'] : ($row1['receipt_no']>99 && $row1['receipt_no']<1000 ? "0".$row1['receipt_no'] : ($row1['receipt_no']>9 && $row1['receipt_no']<100 ? "00".$row1['receipt_no'] : "000".$row1['receipt_no'])));
if($row1['receipt_prefix']!=null){$receipt_no = $row1['receipt_prefix']."/".$receipt_no;}
$receipt_date = date("d-m-Y",strtotime($row1["receipt_date"]));
$challan_no = $row1["challan_no"];
$challan_date = ($row1["challan_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["challan_date"])));
$transit_name = $row1["transit_name"];
$freight_paid = ($row1["freight_paid"]=="Y" ? "Yes" : "No");
$freight_amt = $row1["freight_amt"];

$sql2 = mysql_query("SELECT * FROM tbldelivery1 WHERE dc_id=".$row1["dc_id"]) or die(mysql_error());
$row2 = mysql_fetch_assoc($sql2);

$sql3 = mysql_query("SELECT * FROM tblpo WHERE po_id=".$row2["po_id"]) or die(mysql_error());
$row3 = mysql_fetch_assoc($sql3);
$po_no = ($row3['po_no']>999 ? $row3['po_no'] : ($row3['po_no']>99 && $row3['po_no']<1000 ? "0".$row3['po_no'] : ($row3['po_no']>9 && $row3['po_no']<100 ? "00".$row3['po_no'] : "000".$row3['po_no'])));
$po_date = date("d-m-Y",strtotime($row3["po_date"]));
$delivery_date = date("d-m-Y",strtotime($row3["delivery_date"]));

$sql4 = mysql_query("SELECT party.*, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$row3["party_id"]) or die(mysql_error());
$row4 = mysql_fetch_assoc($sql4);
$party_name = $row4["party_name"];
$address1 = $row4["address1"];
$address2 = $row4["address2"];
$address3 = $row4["address3"];
$city_name = $row4["city_name"];
$state_name = $row4["state_name"];

$sql5 = mysql_query("SELECT * FROM location WHERE location_id=".$row3["delivery_at"]) or die(mysql_error());
$row5 = mysql_fetch_assoc($sql5);
$delivery_at = $row5["location_name"];

$sql5 = mysql_query("SELECT * FROM location WHERE location_id=".$row1["recd_at"]) or die(mysql_error());
$row5 = mysql_fetch_assoc($sql5);
$received_at = $row5["location_name"];

$sql6 = mysql_query("SELECT * FROM staff WHERE staff_id=".$row1["recd_by"]) or die(mysql_error());
$row6 = mysql_fetch_assoc($sql6);
$received_by = $row6["received_by"];
/*--------------------------------*/
$rid2 = 0;
$item_name = "";
$receipt_qnty = "";
$unit_name = "";
$unit_id = 0;
$cur_return_qnty = 0;
$prev_return_qnty_prime = 0;
$prev_return_qnty_alt = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
if(isset($_REQUEST['rid2'])){
	$rid2 = $_REQUEST['rid2'];				//record-id of material receipt table
	$rid3 = $_REQUEST['rid3'];				//record-id of receipt return table
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2 = mysql_query("SELECT tblreceipt2.*, item_name, unit_name FROM tblreceipt2 INNER JOIN item ON tblreceipt2.item_id = item.item_id INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE rec_id=".$rid2) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_name = $row2["item_name"];
		$receipt_qnty = $row2["receipt_qnty"];
		$unit_id = $row2["unit_id"];
		$unit_name = $row2['unit_name'];
		/*--------------------------------*/
		$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
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
		/*--------------------------------*/
		$sql3 = mysql_query("SELECT Sum(return_qnty) AS ret_qnty, unit_id FROM tblreceipt_return2 INNER JOIN tblreceipt_return1 ON tblreceipt_return2.return_id = tblreceipt_return1.return_id WHERE receipt_id=".$row2['receipt_id']." AND item_id=".$row2['item_id']." AND rec_id!=".$rid3." GROUP BY unit_id") or die(mysql_error());
		while($row3 = mysql_fetch_array($sql3)){
			if($row3['unit_id']==$prime_unit_id)
				$prev_return_qnty_prime += $row3['ret_qnty'];
			elseif($row3['unit_id']==$alt_unit_id)
				$prev_return_qnty_alt += $row3['ret_qnty'];
		}
		if($alt_unit=="A" && $alt_unit_id!=0){
			$prev_return_qnty_alt += $prev_return_qnty_prime * $alt_unit_num;
			$prev_return_qnty_prime += $prev_return_qnty_alt / $alt_unit_num;
		}
		/*--------------------------------*/
		if($rid3!=0){
			$sql3 = mysql_query("SELECT return_qnty, seq_no FROM tblreceipt_return2 WHERE rec_id=".$rid3) or die(mysql_error());
			$row3 = mysql_fetch_assoc($sql3);
			$cur_return_qnty = $row3['return_qnty'];
		}
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateReturn = $row1["return_date"];
	$itemname = $row2["item_name"];
	$particulars = "To ".$row1['party_name'];
	$voucherid = ($rid>999 ? $rid : ($rid>99 && $rid<1000 ? "0".$rid : ($rid>9 && $rid<100 ? "00".$rid : "000".$rid)));
	/*--------------------------------*/
	if($row4['prime_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['returnQnty'];
	} elseif($row4['alt_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['returnQnty'] / $row4['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*--------------------------------*/
	$sql = mysql_query("SELECT * FROM tblreceipt_return2 WHERE return_id=".$rid." AND item_id=".$row2['item_id']) or die(mysql_error());
	$row_return = mysql_fetch_assoc($sql);
	$row_count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		if($rid3==0){
			if($row_count==0){
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblreceipt_return2");
				$row = mysql_fetch_assoc($sql);
				$rid3 = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblreceipt_return2 WHERE return_id=".$rid);
				$row = mysql_fetch_assoc($sql);
				$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
				$sql = "INSERT INTO tblreceipt_return2 (rec_id,return_id,seq_no,item_id,unit_id,return_qnty) VALUES(".$rid3.",".$rid.",".$sno.",".$row2['item_id'].",".$_POST['unit'].",".$_POST['returnQnty'].")";
				$res = mysql_query($sql) or die(mysql_error());
				$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
				$row = mysql_fetch_assoc($sql);
				$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,unit_id,item_qnty,item_rate) VALUES(".$sid.",'R-',".$rid.",'".$dateReturn."',".$sno.",".$row1['recd_at'].",".$row2['item_id'].",".$unitid.",".(-1*$itemQnty).",0)";
				$res = mysql_query($sql) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",0,0,'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="returnitem.php?action=new&rid='.$rid.'";</script>';
			} elseif($row_count>0)
				$msg = "Duplication Error! can&prime;t insert into material return record.";
		} elseif($rid3>0){
			if($row_count>0){
				if($row_return['rec_id']!=$rid3)
					$msg = "Duplication Error! can&prime;t update into material return record.";
				elseif($row_return['rec_id']==$rid3){
					$res = mysql_query("UPDATE tblreceipt_return2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",return_qnty=".$_POST['returnQnty']." WHERE rec_id=".$rid3) or die(mysql_error());
					$res = mysql_query("UPDATE stock_register SET item_id=".$row2['item_id'].",unit_id=".$unitid.",item_qnty=".(-1*$itemQnty)." WHERE entry_mode='R-' AND entry_id=".$rid." AND entry_date='".$dateReturn."' AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
					//insert into logbook
					$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
					$row = mysql_fetch_assoc($sql);
					$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
					$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
					$res = mysql_query($sql) or die(mysql_error());
					//end of inserting record into logbook
					echo '<script language="javascript">window.location="returnitem.php?action=new&rid='.$rid.'";</script>';
				}
			} elseif($row_count==0){
				$res = mysql_query("UPDATE tblreceipt_return2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",return_qnty=".$_POST['returnQnty']." WHERE rec_id=".$rid3) or die(mysql_error());
				$res = mysql_query("UPDATE stock_register SET item_id=".$row2['item_id'].",unit_id=".$unitid.",item_qnty=".(-1*$itemQnty)." WHERE entry_mode='R-' AND entry_id=".$rid." AND entry_date='".$dateReturn."' AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="returnitem.php?action=new&rid='.$rid.'";</script>';
			}
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblreceipt_return2 WHERE rec_id=".$rid3) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='R-' AND entry_id=".$rid." AND entry_date='".$dateReturn."' AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_qnty,unit,item_rate,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",0,0,'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="returnitem.php?action=new&rid='.$rid.'";</script>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_receipt_return_item()
{
	var err="";
	if(document.getElementById("itemName").value=="")
		err = "* please select item, which is to be returned!\n";
	if(document.getElementById("returnQnty").value!="" && ! IsNumeric(document.getElementById("returnQnty").value))
		err += "* please input valid (numeric only) item quantity!\n";
	if(document.getElementById("returnQnty").value=="" || document.getElementById("returnQnty").value==0)
		err += "* Item return quantity is madatory!\n";
	if(parseFloat(document.getElementById("returnQnty").value) > parseFloat(document.getElementById("receiptQnty").value))
		err += "* Invalid Item quantity! excess quantity not acceptable.\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
function funMail()
{
    $.post("get_mail_rtnitem.php",{},function(data){
        alert(data);
    })
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="500px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Receipt Return - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Return No.:</td>
			<td><input name="returnNo" id="returnNo" maxlength="15" size="20" readonly="true" value="<?php echo $return_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Return Date:</td>
			<td><input name="returnDate" id="returnDate" maxlength="10" size="10" readonly="true" value="<?php echo $return_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Receipt No.:</td>
			<td><input name="receiptNo" id="receiptNo" maxlength="15" size="20" readonly="true" value="<?php echo $receipt_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Receipt Date:</td>
			<td><input name="receiptDate" id="receiptDate" maxlength="10" size="10" readonly="true" value="<?php echo $receipt_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>P.O. Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" readonly="true" value="<?php echo $po_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Challan No.:</td>
			<td><input name="challanNo" id="challanNo" maxlength="15" size="20" readonly="true" value="<?php echo $challan_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Challan Date:</td>
			<td><input name="challanDate" id="challanDate" maxlength="10" size="10" readonly="true" value="<?php echo $challan_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Transit Point:</td>
			<td><input name="transitPoint" id="transitPoint" maxlength="50" size="45" readonly="true" value="<?php echo $transit_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Delivery Date:</td>
			<td><input name="deliveryDate" id="deliveryDate" maxlength="10" size="10" readonly="true" value="<?php echo $delivery_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="partyName" id="partyName" maxlength="50" size="45" readonly="true" value="<?php echo $party_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Delivery At:</td>
			<td><input name="deliveryAt" id="deliveryAt" maxlength="50" size="45" readonly="true" value="<?php echo $delivery_at;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php echo $address1;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received At:</td>
			<td><input name="receivedAt" id="receivedAt" maxlength="50" size="45" readonly="true" value="<?php echo $received_at;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received By:</td>
			<td><input name="recdBy" id="recdBy" maxlength="50" size="45" readonly="true" value="<?php echo $received_by;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php echo $address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Paid:</td>
			<td><input name="freightPaid" id="freightPaid" maxlength="10" size="20" readonly="true" value="<?php echo $freight_paid;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php echo $city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Amount:</td>
			<td><input name="freightAmount" id="freightAmount" maxlength="10" size="20" readonly="true" value="<?php echo $freight_amt;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php echo $state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Returned By:</td>
			<td><input name="returnBy" id="returnBy" maxlength="50" size="45" readonly="true" value="<?php echo $return_by;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="receiptreturn"  method="post" onsubmit="return validate_receipt_return_item()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" width="10%">Item Name :</td>
			<td width="40%"><input name="itemName" id="itemName" size="45" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="10%">Received Qnty. :</td>
			<td width="40%"><input name="receiptQnty" id="receiptQnty" size="15" readonly="true" value="<?php echo $receipt_qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php echo $unit_name;?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Prev.Returned:</td>
			<td><input name="prevReturned" id="prevReturned" size="15" readonly="true" value="<?php echo $prev_return_qnty_prime;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php echo $prime_unit_name; if($alt_unit=="A" && $alt_unit_id!=0){echo '<br><span style="font-size: 10px;">('.$prev_return_qnty_alt." ".$alt_unit_name.')</span>';} else {echo "";}?></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Curr.Returned:</td>
			<td><input name="returnQnty" id="returnQnty" maxlength="10" size="15" value="<?php echo $cur_return_qnty;?>" ></td>
			
			<td class="th" nowrap>Unit :</td>
			<td><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
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
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['rr1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['rr1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.receiptreturn.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='returnitem.php?action=new&rid=<?php echo $rid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='returnitem.php?action=new&mid=<?php echo $rid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
                            <img src="images/send.gif" width="72" height="22" style="display:inline;cursor:pointer;" border="0" onclick="funMail()"/>
&nbsp;&nbsp;<a href="javascript:window.location='receiptreturn1.php?rid=<?php echo $rid;?>'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>Material Receipt Return - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="45%">Item Name</th>
			<th width="20%">Received Qnty.</th>
			<th width="20%">Return Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_receipt = mysql_query("SELECT tblreceipt2.*, item_name, unit_name FROM tblreceipt2 INNER JOIN item ON tblreceipt2.item_id = item.item_id INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE receipt_id=".$row1['receipt_id']." ORDER BY seq_no") or die(mysql_error());
		while($row_receipt=mysql_fetch_array($sql_receipt)){
			$recid = 0;
			$returnQnty = 0;
			$sql_return = mysql_query("SELECT tblreceipt_return2.*, unit_name FROM tblreceipt_return2 INNER JOIN unit ON tblreceipt_return2.unit_id = unit.unit_id WHERE return_id=".$rid." AND item_id=".$row_receipt['item_id']) or die(mysql_error());
			$row_return = mysql_fetch_assoc($sql_return);
			if(mysql_num_rows($sql_return)>0){
				$recid = $row_return['rec_id'];
				$returnQnty = $row_return['return_qnty'];
			}
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "returnitem.php?action=delete&rid=".$rid."&rid2=".$row_receipt['rec_id']."&rid3=".$recid;
			$edit_ref = "returnitem.php?action=edit&rid=".$rid."&rid2=".$row_receipt['rec_id']."&rid3=".$recid;
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_receipt['item_name'].'</td><td align="right">'.$row_receipt['receipt_qnty']." ".$row_receipt['unit_name'].'</td><td align="right">'.($returnQnty==0?$returnQnty:$returnQnty." ".$row_return['unit_name']).'</td>';
			if($row_user['rr2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['rr2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['rr3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['rr3']==0)
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