<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
$msg = "";
$did = $_REQUEST['did'];
$sql1 = mysql_query("SELECT * FROM tbldelivery1 WHERE dc_id=".$did) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$oid = $row1['po_id'];
$dc_number = ($row1['dc_no']>999 ? $row1['dc_no'] : ($row1['dc_no']>99 && $row1['dc_no']<1000 ? "0".$row1['dc_no'] : ($row1['dc_no']>9 && $row1['dc_no']<100 ? "00".$row1['dc_no'] : "000".$row1['dc_no'])));
$dc_date = date("d-m-Y",strtotime($row1["dc_date"]));
/*--------------------------------*/
$sqlPO = mysql_query("SELECT * FROM tblpo WHERE po_id=".$oid) or die(mysql_error());
$rowPO = mysql_fetch_assoc($sqlPO);
$po_no = ($rowPO['po_no']>999 ? $rowPO['po_no'] : ($rowPO['po_no']>99 && $rowPO['po_no']<1000 ? "0".$rowPO['po_no'] : ($rowPO['po_no']>9 && $rowPO['po_no']<100 ? "00".$rowPO['po_no'] : "000".$rowPO['po_no'])));
$po_date = date("d-m-Y",strtotime($rowPO["po_date"]));
$location_id = $rowPO["delivery_at"];
$delivery_date = date("d-m-Y",strtotime($rowPO["delivery_date"]));
/*--------------------------------*/
$sqlParty = mysql_query("SELECT party.*, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$rowPO['party_id']) or die(mysql_error());
$rowParty = mysql_fetch_assoc($sqlParty);
$party_name = $rowParty["party_name"];
$address1 = $rowParty["address1"];
$address2 = $rowParty["address2"];
$address3 = $rowParty["address3"];
$city_name = $rowParty["city_name"];
$state_name = $rowParty["state_name"];
/*--------------------------------*/
$sqlComp = mysql_query("SELECT * FROM company WHERE company_id=".$rowPO['company_id']) or die(mysql_error());
$rowComp = mysql_fetch_assoc($sqlComp);
$company_name = $rowComp["company_name"];
/*--------------------------------*/
$sqlLoc = mysql_query("SELECT * FROM location WHERE location_id=".$location_id) or die(mysql_error());
$rowLoc = mysql_fetch_assoc($sqlLoc);
$location_name = $rowLoc["location_name"];
/*--------------------------------*/
$roid = 0;
$item_id = 0;
$item_name = "";
$qnty = "";
$unit_name = "";
$unit_id = 0;
$dc_qnty = "";
$clqnty_prime = 0;
$clqnty_alt = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
$previous_recd_qnty = "";
$previous_qnty_unit = "";
if(isset($_REQUEST['roid'])){
	$roid = $_REQUEST['roid'];			// record id of purchase order table
	$rdid = $_REQUEST['rdid'];			// record id of delivery confirmation table
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2 = mysql_query("SELECT tblpo_item.*, item_name, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id WHERE rec_id=".$roid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_id = $row2['item_id'];
		$item_name = $row2["item_name"];
		$qnty = $row2["qnty"];
		$unit_id = $row2["unit_id"];
		$unit_name = $row2["unit_name"];
		/*--------------------------------*/
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
		/*--------------------------------*/
		$sql3 = mysql_query("SELECT Sum(delivery_qnty) AS dlry_qnty, tbldelivery2.unit_id, unit_name FROM tbldelivery2 INNER JOIN tbldelivery1 ON tbldelivery2.dc_id = tbldelivery1.dc_id INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE po_id=".$oid." AND tbldelivery2.dc_id!=".$did." AND item_id=".$item_id." GROUP BY tbldelivery2.unit_id") or die(mysql_error());
		while($row3 = mysql_fetch_array($sql3)){
			if($alt_unit=="A"){
				if($row3['unit_id']==$alt_unit_id){
					$previous_recd_qnty += $row3['dlry_qnty'] / $alt_unit_num;
				} else {
					$previous_recd_qnty += $row3['dlry_qnty'];
				}
				$previous_qnty_unit = $prime_unit_name;
			} elseif($alt_unit=="N"){
				$previous_recd_qnty += $row3['dlry_qnty'];
				$previous_qnty_unit = $row3['unit_name'];
			}
		}
		/*--------------------------------*/
		if($rdid!=0){
			$sql3 = mysql_query("SELECT delivery_qnty, seq_no FROM tbldelivery2 WHERE rec_id=".$rdid) or die(mysql_error());
			$row3 = mysql_fetch_assoc($sql3);
			$dc_qnty = $row3['delivery_qnty'];
		}
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateDC = $row1["dc_date"];
	$itemname = $row2["item_name"];
	$particulars = "From ".$row1['party_name'];
	$voucherid = ($did>999 ? $did : ($did>99 && $did<1000 ? "0".$did : ($did>9 && $did<100 ? "00".$did : "000".$did)));
	/*-------------------------------*/
	if($row4['prime_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['deliveryQnty'];
	} elseif($row4['alt_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['deliveryQnty'] / $row4['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*--------------------------------*/
	$sql_po = mysql_query("SELECT * FROM tblpo_item WHERE po_id=".$oid." AND item_id=".$row2['item_id']." ORDER BY seq_no") or die(mysql_error());
	$row_po = mysql_fetch_assoc($sql_po);
	$balance = $row_po['qnty'];
	/*--------------------------------*/
	$received = 0;
	if($row_po['unit_id']==$row4['prime_unit_id']){
		if($_POST['unit']==$row4['prime_unit_id'])
			$received += $_POST['deliveryQnty'];
		elseif($_POST['unit']==$row4['alt_unit_id'])
			$received += $_POST['deliveryQnty'] / $row4['alt_unit_num'];
	} elseif($row_po['unit_id']==$row4['alt_unit_id']){
		if($_POST['unit']==$row4['prime_unit_id'])
			$received += $_POST['deliveryQnty'] * $row4['alt_unit_num'];
		elseif($_POST['unit']==$row4['alt_unit_id'])
			$received += $_POST['deliveryQnty'];
	}
	/*--------------------------------*/
	$sql_dlry = mysql_query("SELECT Sum(delivery_qnty) AS dlry_qnty, unit_id FROM tbldelivery2 INNER JOIN tbldelivery1 ON tbldelivery2.dc_id = tbldelivery1.dc_id WHERE po_id=".$oid." AND item_id=".$row2['item_id']." GROUP BY unit_id") or die(mysql_error());
	while($row_dlry = mysql_fetch_array($sql_dlry)){
		if($row_po['unit_id']==$row4['prime_unit_id']){
			if($row_dlry['unit_id']==$row4['prime_unit_id'])
				$received += $row_dlry['dlry_qnty'];
			elseif($row_dlry['unit_id']==$row4['alt_unit_id'])
				$received += $row_dlry['dlry_qnty'] / $row4['alt_unit_num'];
		} elseif($row_po['unit_id']==$row4['alt_unit_id']){
			if($row_dlry['unit_id']==$row4['prime_unit_id'])
				$received += $row_dlry['dlry_qnty'] * $row4['alt_unit_num'];
			elseif($row_dlry['unit_id']==$row4['alt_unit_id'])
				$received += $row_dlry['dlry_qnty'];
		}
	}
	if(($balance-$received) <= 0)
		$orderReceived = "Y";
	elseif(($balance-$received) > 0)
		$orderReceived = "N";
	/*--------------------------------*/
	$sql = mysql_query("SELECT * FROM tbldelivery2 WHERE dc_id=".$did." AND item_id=".$row2['item_id']) or die(mysql_error());
	$row_delivery = mysql_fetch_assoc($sql);
	$row_count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		if($rdid==0){
			if($row_count==0){
				//insert into material receipt table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tbldelivery2");
				$row = mysql_fetch_assoc($sql);
				$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tbldelivery2 WHERE dc_id=".$did);
				$row = mysql_fetch_assoc($sql);
				$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
				$sql = "INSERT INTO tbldelivery2 (rec_id,dc_id,seq_no,item_id,unit_id,delivery_qnty) VALUES (".$rid.",".$did.",".$sno.",".$row2['item_id'].",".$_POST['unit'].",".$_POST['deliveryQnty'].")";
				$res = mysql_query($sql) or die(mysql_error());
				//update into purchase order table
				$res = mysql_query("UPDATE tblpo_item SET order_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
				//insert into logbook table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$dateDC."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['deliveryQnty'].",0,0,'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="deliveryitem.php?action=new&did='.$did.'";</script>';
			} elseif($row_count>0)
				$msg = "Duplication Error! can&prime;t insert into delivery confirmation record.";
		} elseif($rdid>0){
			if($row_count>0){
				//update into material receipt table
				if($row_delivery['rec_id']!=$rdid)
					$msg = "Duplication Error! can&prime;t update into delivery confirmation record.";
				elseif($row_delivery['rec_id']==$rdid){
					$res = mysql_query("UPDATE tbldelivery2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",delivery_qnty=".$_POST['deliveryQnty']." WHERE rec_id=".$rdid) or die(mysql_error());
					//update into purchase order table
					$res = mysql_query("UPDATE tblpo_item SET order_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
					//insert into logbook table
					$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
					$row = mysql_fetch_assoc($sql);
					$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
					$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateDC."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['deliveryQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
					$res = mysql_query($sql) or die(mysql_error());
					//end of inserting record into logbook
					echo '<script language="javascript">window.location="deliveryitem.php?action=new&did='.$did.'";</script>';
				}
			} elseif($row_count==0){
				//update into material receipt and stock register table
				$res = mysql_query("UPDATE tbldelivery2 SET item_id=".$row2['item_id'].",unit_id=".$_POST['unit'].",delivery_qnty=".$_POST['deliveryQnty']." WHERE rec_id=".$rdid) or die(mysql_error());
				//update into purchase order table
				$res = mysql_query("UPDATE tblpo_item SET order_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
				//insert into logbook table
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action,user) VALUES(".$recordid.",'".$voucherid."','".$dateDC."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['deliveryQnty'].",0,0,'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="deliveryitem.php?action=new&did='.$did.'";</script>';
			}
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tbldelivery2 WHERE rec_id=".$rdid) or die(mysql_error());
		//update into purchase order table
		$res = mysql_query("UPDATE tblpo_item SET order_received='".$orderReceived."' WHERE rec_id=".$row_po['rec_id']) or die(mysql_error());
		//insert into logbook table
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateDC."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['deliveryQnty'].",0,0,'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="deliveryitem.php?action=new&did='.$did.'";</script>';
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
<script src="js/jquery-1.11.2.min.js"></script>
<script language="javascript" type="text/javascript">
function validate_delivery()
{
	var err="";
	if(document.getElementById("deliveryQnty").value=="" || document.getElementById("deliveryQnty").value==0)
		err = "* Item received quantity is madatory!\n";
	if(document.getElementById("deliveryQnty").value!="" && ! IsNumeric(document.getElementById("deliveryQnty").value))
		err += "* please input valid (numeric only) quantity of the item!\n";
//	if(parseFloat(document.getElementById("DeliveryQnty").value)>parseFloat(document.getElementById("orderQnty").value))
//		err += "* Invalid Item quantity! excess quantity not acceptable.\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
function funMail(){
    $.post("get_mail_delivery.php",{},function(data){
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
			<td class="th"><strong>Delivery Confirmation - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" width="10%" nowrap>DC No.:</td>
			<td width="40%"><input name="dcNo" id="dcNo" maxlength="15" size="20" readonly="true" value="<?php echo $dc_number;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="10%" nowrap>DC Date:</td>
			<td width="40%"><input name="dcDate" id="dcDate" maxlength="10" size="10" readonly="true" value="<?php echo $dc_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>P.O. Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" readonly="true" value="<?php echo $po_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="partyName" id="partyName" maxlength="50" size="45" readonly="true" value="<?php echo $party_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Company Name:</td>
			<td><input name="companyName" id="companyName" maxlength="50" size="45" readonly="true" value="<?php echo $company_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php echo $address1;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Delivery At:</td>
			<td><input name="deliveryAt" id="deliveryAt" maxlength="50" size="45" readonly="true" value="<?php echo $location_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Required Date:</td>
			<td><input name="deliveryDate" id="deliveryDate" maxlength="10" size="10" readonly="true" value="<?php echo $delivery_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php echo $address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php echo $city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
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
	<form name="deliveryitem"  method="post" onsubmit="return validate_delivery()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" width="10%">Item Name :</td>
			<td width="38%"><input name="itemName" id="itemName" maxlength="50" size="45" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="12%">&nbsp;</td>
			<td width="39%">&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Order Qnty. :</td>
			<td><input name="orderQnty" id="orderQnty" size="15" readonly="true" value="<?php echo $qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php echo $unit_name;?></td>
			
			<td class="th">Prev.Delivered :</td>
			<td><input name="preDelQnty" id="preDelQnty" size="15" readonly="true" value="<?php echo $previous_recd_qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php echo $previous_qnty_unit;?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Dlry.Qnty. :</td>
			<td><input name="deliveryQnty" id="deliveryQnty" size="15" value="<?php echo $dc_qnty;?>"></td>
			
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
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='deliveryitem.php?action=new&did=<?php echo $did;?>'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='deliveryitem.php?action=new&did=<?php echo $did;?>'"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
                            <img src="images/send.gif" width="72" height="22" style="display:inline;cursor:pointer;" border="0" onclick="funMail()"/>
&nbsp;&nbsp;<a href="javascript:window.location='deliveryconfirm1.php?did=<?php echo $did;?>'"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>Delivery Confirmation - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="40%">Item Name</th>
			<th width="15%">Order Qnty.</th>
			<th width="15%">Previous Delivered</th>
			<th width="15%">Current Dlry.Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_order = mysql_query("SELECT tblpo_item.*, item_name, item.unit_id AS prime_unit, alt_unit, alt_unit_id, alt_unit_num, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id WHERE po_id=".$oid." ORDER BY seq_no") or die(mysql_error());
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
			$sql_delivery = mysql_query("SELECT Sum(delivery_qnty) AS dlry_qnty, tbldelivery2.unit_id, unit_name FROM tbldelivery2 INNER JOIN tbldelivery1 ON tbldelivery2.dc_id = tbldelivery1.dc_id INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE po_id=".$oid." AND tbldelivery2.dc_id!=".$did." AND item_id=".$row_order['item_id']." GROUP BY tbldelivery2.unit_id") or die(mysql_error());
			while($row_delivery = mysql_fetch_array($sql_delivery)){
				if($row_order['alt_unit']=="A"){
					if($row_delivery['unit_id']==$row_order['alt_unit_id']){
						$previous_recd_qnty += $row_delivery['dlry_qnty'] / $row_order['alt_unit_num'];
					} else {
						$previous_recd_qnty += $row_delivery['dlry_qnty'];
					}
					$previous_qnty_unit = $prime_unit_name;
				} elseif($row_order['alt_unit']=="N"){
					$previous_recd_qnty += $row_delivery['dlry_qnty'];
					$previous_qnty_unit = $row_delivery['unit_name'];
				}
			}
			
			$current_recd_qnty = 0;
			$sql_delivery = mysql_query("SELECT tbldelivery2.*, unit_name FROM tbldelivery2 INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE dc_id=".$did." AND item_id=".$row_order['item_id']) or die(mysql_error());
			$row_delivery = mysql_fetch_assoc($sql_delivery);
			if(mysql_num_rows($sql_delivery)>0){
				$recid = $row_delivery['rec_id'];
				$current_recd_qnty = $row_delivery['delivery_qnty'];
			}
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "deliveryitem.php?action=delete&did=".$did."&roid=".$row_order['rec_id']."&rdid=".$recid;
			$edit_ref = "deliveryitem.php?action=edit&did=".$did."&roid=".$row_order['rec_id']."&rdid=".$recid;
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td align="right">'.$row_order['qnty']." ".$row_order['unit_name'].'</td><td>'.$previous_recd_qnty." ".$previous_qnty_unit.'</td><td>'.$current_recd_qnty." ".$row_delivery['unit_name'];
			if($row_order['alt_unit']=="A"){
				if($row_delivery['unit_id']==$row_order['alt_unit_id']){
					$strg = '('.($current_recd_qnty / $row_order['alt_unit_num']).' '.$prime_unit_name.')';
				} else {
					$strg = '('.($current_recd_qnty * $row_order['alt_unit_num']).' '.$alt_unit_name.')';
				}
				echo '<br><span style="font-size:10px;">'.$strg.'</span>';
			}
			echo '</td>';
			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['po3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po3']==0)
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