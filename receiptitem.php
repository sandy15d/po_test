<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT mr1,mr2,mr3,mr4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblreceipt1.*, dc_no, dc_date, po_no, po_date, delivery_date, delivery_at, transit_name, party_name, address1, address2, address3, city_name, state_name, ordfrom.location_name AS delivery_at, recd.location_name AS received_at, staff_name AS receivedby FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id INNER JOIN transit ON tblreceipt1.transit_point = transit.transit_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN location AS ordfrom ON tblpo.delivery_at = ordfrom.location_id INNER JOIN location AS recd ON tblreceipt1.recd_at = recd.location_id INNER JOIN staff ON tblreceipt1.recd_by = staff.staff_id WHERE receipt_id=".$mid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$oid = $row1['dc_id'];
$receipt_number = ($row1['receipt_no']>999 ? $row1['receipt_no'] : ($row1['receipt_no']>99 && $row1['receipt_no']<1000 ? "0".$row1['receipt_no'] : ($row1['receipt_no']>9 && $row1['receipt_no']<100 ? "00".$row1['receipt_no'] : "000".$row1['receipt_no'])));
if($row1['receipt_prefix']!=null){$receipt_number = $row1['receipt_prefix']."/".$receipt_number;}
$receipt_date = date("d-m-Y",strtotime($row1["receipt_date"]));
$dc_no = ($row1['dc_no']>999 ? $row1['dc_no'] : ($row1['dc_no']>99 && $row1['dc_no']<1000 ? "0".$row1['dc_no'] : ($row1['dc_no']>9 && $row1['dc_no']<100 ? "00".$row1['dc_no'] : "000".$row1['dc_no'])));
$dc_date = date("d-m-Y",strtotime($row1["dc_date"]));
$po_no = ($row1['po_no']>999 ? $row1['po_no'] : ($row1['po_no']>99 && $row1['po_no']<1000 ? "0".$row1['po_no'] : ($row1['po_no']>9 && $row1['po_no']<100 ? "00".$row1['po_no'] : "000".$row1['po_no'])));
$po_date = date("d-m-Y",strtotime($row1["po_date"]));
$challan_no = $row1["challan_no"];
$challan_date = ($row1["challan_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["challan_date"])));
$transit_name = $row1["transit_name"];
$delivery_date = date("d-m-Y",strtotime($row1["delivery_date"]));
$party_name = $row1["party_name"];
$delivery_at = $row1["delivery_at"];
$received_at = $row1["received_at"];
$receivedby = $row1["receivedby"];
$address1 = $row1["address1"];
$address2 = $row1["address2"];
$address3 = $row1["address3"];
$city_name = $row1["city_name"];
$state_name = $row1["state_name"];
$freight_paid = ($row1["freight_paid"]=="Y" ? "Yes" : "No");
$freight_amt = $row1["freight_amt"];
/*--------------------------------*/
$roid = 0;
$item_name = "";
$delivery_qnty = "";
$unit_name = "";
$recd_qnty = "";
$unit_id = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
if(isset($_REQUEST['roid'])){
	$roid = $_REQUEST['roid'];			// record id of purchase order table
	$rrid = $_REQUEST['rrid'];			// record id of material receipt table
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2 = mysql_query("SELECT tbldelivery2.*, item_name, unit_name FROM tbldelivery2 INNER JOIN item ON tbldelivery2.item_id = item.item_id INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE rec_id=".$roid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_name = $row2["item_name"];
		$delivery_qnty = $row2["delivery_qnty"];
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
		if($rrid!=0){
			$sql3 = mysql_query("SELECT receipt_qnty, seq_no FROM tblreceipt2 WHERE rec_id=".$rrid) or die(mysql_error());
			$row3 = mysql_fetch_assoc($sql3);
			$recd_qnty = $row3['receipt_qnty'];
		}
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateReceipt = $row1["receipt_date"];
	$itemname = $row2["item_name"];
	$particulars = "From ".$row1['party_name'];
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	/*-------------------------------*/
	if($row4['prime_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['receiptQnty'];
	} elseif($row4['alt_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['receiptQnty'] / $row4['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*--------------------------------*/
	$sql_po = mysql_query("SELECT * FROM tbldelivery2 WHERE dc_id=".$oid." AND item_id=".$row2['item_id']." ORDER BY seq_no") or die(mysql_error());
	$row_po = mysql_fetch_assoc($sql_po);
	$balance = $row_po['delivery_qnty'];
	/*--------------------------------*/
	$received = 0;
	if($row_po['unit_id']==$row4['prime_unit_id']){
		if($_POST['unit']==$row4['prime_unit_id'])
			$received += $_POST['receiptQnty'];
		elseif($_POST['unit']==$row4['alt_unit_id'])
			$received += $_POST['receiptQnty'] / $row4['alt_unit_num'];
	} elseif($row_po['unit_id']==$row4['alt_unit_id']){
		if($_POST['unit']==$row4['prime_unit_id'])
			$received += $_POST['receiptQnty'] * $row4['alt_unit_num'];
		elseif($_POST['unit']==$row4['alt_unit_id'])
			$received += $_POST['receiptQnty'];
	}
	/*--------------------------------*/
	$sql_rcpt = mysql_query("SELECT Sum(receipt_qnty) AS rcpt_qnty, unit_id FROM tblreceipt2 INNER JOIN tblreceipt1 ON tblreceipt2.receipt_id = tblreceipt1.receipt_id WHERE dc_id=".$oid." AND item_id=".$row2['item_id']." GROUP BY unit_id") or die(mysql_error());
	while($row_rcpt = mysql_fetch_array($sql_rcpt)){
		if($row_po['unit_id']==$row4['prime_unit_id']){
			if($row_rcpt['unit_id']==$row4['prime_unit_id'])
				$received += $row_rcpt['rcpt_qnty'];
			elseif($row_rcpt['unit_id']==$row4['alt_unit_id'])
				$received += $row_rcpt['rcpt_qnty'] / $row4['alt_unit_num'];
		} elseif($row_po['unit_id']==$row4['alt_unit_id']){
			if($row_rcpt['unit_id']==$row4['prime_unit_id'])
				$received += $row_rcpt['rcpt_qnty'] * $row4['alt_unit_num'];
			elseif($row_rcpt['unit_id']==$row4['alt_unit_id'])
				$received += $row_rcpt['rcpt_qnty'];
		}
	}
	if(($balance-$received) <= 0)
		$orderReceived = "Y";
	elseif(($balance-$received) > 0)
		$orderReceived = "N";
	/*--------------------------------*/
	$sql = mysql_query("SELECT * FROM tblreceipt2 WHERE receipt_id=".$mid." AND item_id=".$row2['item_id']) or die(mysql_error());
	$row_receipt = mysql_fetch_assoc($sql);
	$row_count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		if($rrid==0){
			if($row_count==0){
				//insert into material receipt table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblreceipt2");
				$row = mysql_fetch_assoc($sql);
				$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblreceipt2 WHERE receipt_id=".$mid);
				$row = mysql_fetch_assoc($sql);
				$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
				$sql = "INSERT INTO tblreceipt2 (rec_id,receipt_id,seq_no,item_id,unit_id,receipt_qnty) VALUES (".$rid.",".$mid.",".$sno.",".$row2['item_id'].",".$_POST['unit'].",".$_POST['receiptQnty'].")";
				$res = mysql_query($sql) or die(mysql_error());
				//insert into stock register table
				$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
				$row = mysql_fetch_assoc($sql);
				$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_qnty,unit_id) VALUES(".$sid.",'R+',".$mid.",'".$dateReceipt."',".$sno.",".$row1['recd_at'].",".$row2['item_id'].",".$itemQnty.",".$unitid.")";
				$res = mysql_query($sql) or die(mysql_error());
				//update into purchase order table
				$res = mysql_query("UPDATE tbldelivery2 SET item_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
				//insert into logbook table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiptQnty'].",0,0,'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="receiptitem.php?action=new&mid='.$mid.'";</script>';
			} elseif($row_count>0)
				$msg = "Duplication Error! can&prime;t insert into material receipt record.";
		} elseif($rrid>0){
			if($row_count>0){
				//update into material receipt table
				if($row_receipt['rec_id']!=$rrid)
					$msg = "Duplication Error! can&prime;t update into material receipt record.";
				elseif($row_receipt['rec_id']==$rrid){
					$res = mysql_query("UPDATE tblreceipt2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",receipt_qnty=".$_POST['receiptQnty']." WHERE rec_id=".$rrid) or die(mysql_error());
					$res = mysql_query("UPDATE stock_register SET entry_date='".$dateReceipt."',item_id=".$row2['item_id'].",item_qnty=".$itemQnty.",unit_id=".$unitid." WHERE entry_mode='R+' AND entry_id=".$mid." AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
					//update into purchase order table
					$res = mysql_query("UPDATE tbldelivery2 SET item_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
					//insert into logbook table
					$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
					$row = mysql_fetch_assoc($sql);
					$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
					$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiptQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
					$res = mysql_query($sql) or die(mysql_error());
					//end of inserting record into logbook
					echo '<script language="javascript">window.location="receiptitem.php?action=new&mid='.$mid.'";</script>';
				}
			} elseif($row_count==0){
				//update into material receipt and stock register table
				$res = mysql_query("UPDATE tblreceipt2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",receipt_qnty=".$_POST['receiptQnty']." WHERE rec_id=".$rrid) or die(mysql_error());
				$res = mysql_query("UPDATE stock_register SET entry_date='".$dateReceipt."',item_id=".$row2['item_id'].",item_qnty=".$itemQnty.",unit_id=".$unitid." WHERE entry_mode='R+' AND entry_id=".$mid." AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
				//update into purchase order table
				$res = mysql_query("UPDATE tbldelivery2 SET item_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
				//insert into logbook table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiptQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="receiptitem.php?action=new&mid='.$mid.'";</script>';
			}
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblreceipt2 WHERE rec_id=".$rrid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='R+' AND entry_id=".$mid." AND seq_no=".$row3['seq_no']." AND location_id=".$row1['recd_at']) or die(mysql_error());
		//update into purchase order table
		$res = mysql_query("UPDATE tbldelivery2 SET item_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
		//insert into logbook table
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['receiptQnty'].",0,0,'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="receiptitem.php?action=new&mid='.$mid.'";</script>';
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
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script language="javascript" type="text/javascript">
function validate_receipt()
{
	var err="";
	if(document.getElementById("receiptQnty").value=="" || document.getElementById("receiptQnty").value==0)
		err = "* Item received quantity is madatory!\n";
	if(document.getElementById("receiptQnty").value!="" && ! IsNumeric(document.getElementById("receiptQnty").value))
		err += "* please input valid (numeric only) quantity of the item!\n";
//	if(parseFloat(document.getElementById("receiptQnty").value)>parseFloat(document.getElementById("orderQnty").value))
//		err += "* Invalid Item quantity! excess quantity not acceptable.\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function funMail(){
    $.post("get_mail_recieptitem.php",{},function(data){
        alert(data);
    })
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="350px" width="850px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Receipt - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" width="10%" nowrap>Receipt No.:</td>
			<td width="40%"><input name="receiptNo" id="receiptNo" maxlength="15" size="20" readonly="true" value="<?php echo $receipt_number;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="10%" nowrap>Receipt Date:</td>
			<td width="40%"><input name="receiptDate" id="receiptDate" maxlength="10" size="10" readonly="true" value="<?php echo $receipt_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>DC No.:</td>
			<td><input name="dcNo" id="dcNo" maxlength="15" size="20" readonly="true" value="<?php echo $dc_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>DC Date:</td>
			<td><input name="dcDate" id="dcDate" maxlength="10" size="10" readonly="true" value="<?php echo $dc_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>PO No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>PO Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" readonly="true" value="<?php echo $po_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Challan No.:</td>
			<td><input name="challanNo" id="challanNo" maxlength="50" size="45" readonly="true" value="<?php echo $challan_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Challan Date:</td>
			<td><input name="challanDate" id="challanDate" maxlength="10" size="10" readonly="true" value="<?php echo $challan_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Transit Point:</td>
			<td><input name="transitPoint" id="transitPoint" maxlength="50" size="45" readonly="true" value="<?php echo $transit_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Required Date:</td>
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
			<td><input name="recdAt" id="recdAt" maxlength="50" size="45" readonly="true" value="<?php echo $received_at;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received By:</td>
			<td><input name="recdBy" id="recdBy" maxlength="50" size="45" readonly="true" value="<?php echo $receivedby;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php echo $address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Paid:</td>
			<td><input name="freightPaid" id="freightPaid" maxlength="10" size="10" readonly="true" value="<?php echo $freight_paid;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php echo $city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Amount:</td>
			<td><input name="freightAmount" id="freightAmount" maxlength="10" size="10" readonly="true" value="<?php echo $freight_amt;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php echo $state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
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
	<form name="receiptitem"  method="post" onsubmit="return validate_receipt()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" width="10%">Item Name :</td>
			<td width="38%"><input name="itemName" id="itemName" maxlength="50" size="45" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="12%">Order Qnty. :</td>
			<td width="39%"><input name="orderQnty" id="orderQnty" size="15" readonly="true" value="<?php echo $delivery_qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php echo $unit_name;?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Recd.Qnty. :</td>
			<td><input name="receiptQnty" id="receiptQnty" size="15" value="<?php echo $recd_qnty;?>" ></td>
			
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
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='receiptitem.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='receiptitem.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0"/></a>
		<?php }?>
                            <img src="images/send.gif" width="72" height="22" style="display:inline;cursor:pointer;" border="0" onclick="funMail()"/>
&nbsp;&nbsp;<a href="javascript:window.location='materialreceipt1.php?mid=<?php echo $mid;?>'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Receipt - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="30%">Item Name</th>
			<th width="15%">Order Qnty.</th>
			<th width="20%">Previous Recd.Qnty.</th>
			<th width="20%">Current Recd.Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_order = mysql_query("SELECT tbldelivery2.*, item_name, item.unit_id AS prime_unit, alt_unit, alt_unit_id, alt_unit_num, unit_name FROM tbldelivery2 INNER JOIN item ON tbldelivery2.item_id = item.item_id INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE dc_id=".$oid." ORDER BY seq_no") or die(mysql_error());
		while($row_order=mysql_fetch_array($sql_order)){
			$recid = 0;
			$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row_order['prime_unit']) or die(mysql_error());
			$row_unit = mysql_fetch_assoc($sql_unit);
			$prime_unit_name = $row_unit['unit_name'];
			if($row_order['alt_unit']=="A"){
				$sql_unit = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row_order['alt_unit_id']) or die(mysql_error());
				$row_unit = mysql_fetch_assoc($sql_unit);
				$alt_unit_name = $row_unit['unit_name'];
			}
			
			$previous_recd_qnty = 0;
			$previous_qnty_unit = "";
			$sql_receipt = mysql_query("SELECT Sum(receipt_qnty) AS rcpt_qnty, tblreceipt2.unit_id, unit_name FROM tblreceipt2 INNER JOIN tblreceipt1 ON tblreceipt2.receipt_id = tblreceipt1.receipt_id INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE dc_id=".$oid." AND tblreceipt2.receipt_id!=".$mid." AND item_id=".$row_order['item_id']." GROUP BY tblreceipt2.unit_id") or die(mysql_error());
			while($row_receipt = mysql_fetch_array($sql_receipt)){
				if($row_order['alt_unit']=="A"){
					if($row_receipt['unit_id']==$row_order['alt_unit_id']){
						$previous_recd_qnty += $row_receipt['rcpt_qnty'] / $row_order['alt_unit_num'];
					} else {
						$previous_recd_qnty += $row_receipt['rcpt_qnty'];
					}
					$previous_qnty_unit = $prime_unit_name;
				} elseif($row_order['alt_unit']=="N"){
					$previous_recd_qnty += $row_receipt['rcpt_qnty'];
					$previous_qnty_unit = $row_receipt['unit_name'];
				}
			}
			
			$current_recd_qnty = 0;
			$sql_receipt = mysql_query("SELECT tblreceipt2.*, unit_name FROM tblreceipt2 INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE receipt_id=".$mid." AND item_id=".$row_order['item_id']) or die(mysql_error());
			$row_receipt = mysql_fetch_assoc($sql_receipt);
			if(mysql_num_rows($sql_receipt)>0){
				$recid = $row_receipt['rec_id'];
				$current_recd_qnty = $row_receipt['receipt_qnty'];
			}
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "receiptitem.php?action=delete&mid=".$mid."&roid=".$row_order['rec_id']."&rrid=".$recid;
			$edit_ref = "receiptitem.php?action=edit&mid=".$mid."&roid=".$row_order['rec_id']."&rrid=".$recid;
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td align="right">'.$row_order['delivery_qnty']." ".$row_order['unit_name'].'</td><td>'.$previous_recd_qnty." ".$previous_qnty_unit.'</td><td>'.$current_recd_qnty." ".$row_receipt['unit_name'];
			if($row_order['alt_unit']=="A"){
				if($row_receipt['unit_id']==$row_order['alt_unit_id']){
					$strg = '('.($current_recd_qnty / $row_order['alt_unit_num']).' '.$prime_unit_name.')';
				} else {
					$strg = '('.($current_recd_qnty * $row_order['alt_unit_num']).' '.$alt_unit_name.')';
				}
				echo '<br><span style="font-size:10px;">'.$strg.'</span>';
			}
			echo '</td>';
			if($row_user['mr2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['mr2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['mr3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['mr3']==0)
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