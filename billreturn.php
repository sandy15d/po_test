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
$bid = $_REQUEST['bid'];
$sql1 = mysql_db_query(DATABASE2,"SELECT tblbill1.*, po_no, po_date, supply_date, party_name, address1, address2, address3, city_name, state_name, ordfrom.location_name AS orderfrom, delto.location_name AS deliveryto, ord.staff_name AS orderby, received.staff_name AS receivedby FROM tblbill1 INNER JOIN tblorder1 ON tblbill1.order_id = tblorder1.order_id INNER JOIN ".DATABASE1.".party ON tblorder1.party_id = party.party_id INNER JOIN ".DATABASE1.".location AS ordfrom ON tblorder1.order_from = ordfrom.location_id INNER JOIN ".DATABASE1.".location AS delto ON tblorder1.delivery_to = delto.location_id INNER JOIN ".DATABASE1.".city ON party.city_id = city.city_id INNER JOIN ".DATABASE1.".state ON city.state_id = state.state_id INNER JOIN ".DATABASE1.".staff AS ord ON tblorder1.order_by = ord.staff_id INNER JOIN ".DATABASE1.".staff AS received ON tblbill1.recd_by = received.staff_id WHERE bill_id=".$bid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
/*-------------------------------*/
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_db_query(DATABASE2,"SELECT tblbill2.*,item_name,unit_name FROM tblbill2 INNER JOIN ".DATABASE1.".item ON tblbill2.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON item.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
	$row2 = mysql_fetch_assoc($sql2);
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	if($_POST['submit']=="update"){
		$res = mysql_db_query(DATABASE2,"UPDATE tblbill2 SET return_qnty=".$_POST['returnQnty']." WHERE rec_id=".$rid) or die(mysql_error());
		header('Location:billreturn.php?action=new&bid='.$bid);
	} elseif($_POST['submit']=="delete"){
		$res = mysql_db_query(DATABASE2,"UPDATE tblbill2 SET return_qnty=0 WHERE rec_id=".$rid) or die(mysql_error());
		header('Location:billreturn.php?action=new&bid='.$bid);
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
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_bill_return_item()
{
	var err="";
	if(document.getElementById("returnQnty").value!="" && ! IsNumeric(document.getElementById("returnQnty").value))
		err = "* please input valid numeric data!\n";
	if(parseFloat(document.getElementById("returnQnty").value) > parseFloat(document.getElementById("billQnty").value))
		err += "* please input valid returned quantity of the item!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
</script>
</head>


<body onload="document.getElementById('returnQnty').focus()">
<table align="center" cellspacing="0" cellpadding="0" height="610px" width="100%" border="0">
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
			<td class="th"><strong>Purchase Bill Return - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Return No.:</td>
			<td><input name="returnNo" id="returnNo" maxlength="15" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["return_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Return Date:</td>
			<td><input name="returnDate" id="returnDate" maxlength="10" size="15" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo date("d-m-Y",strtotime($row1["return_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
 		
		<tr class="Controls">
			<td class="th" nowrap>Bill No.:</td>
			<td><input name="billNo" id="billNo" maxlength="15" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["bill_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Bill Date:</td>
			<td><input name="billDate" id="billDate" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo ($row1["bill_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["bill_date"])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
 		
		<tr class="Controls">
			<td class="th" nowrap>Challan No.:</td>
			<td><input name="challanNo" id="challanNo" maxlength="15" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["challan_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Challan Date:</td>
			<td><input name="challanDate" id="challanDate" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo ($row1["challan_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["challan_date"])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:</td>
			<td><input name="poNo" id="poNo" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo $row1["po_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>P.O. Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo ($row1["po_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["po_date"])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Order From:</td>
			<td><input name="orderFrom" id="orderFrom" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["orderfrom"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Delivery To:</td>
			<td><input name="deliveryTo" id="deliveryTo" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["deliveryto"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="partyName" id="partyName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["party_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Estimated Supply Date:</td>
			<td><input name="supplyDate" id="supplyDate" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo ($row1["supply_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["supply_date"])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["address1"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Order By:</td>
			<td><input name="orderBy" id="orderBy" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["orderby"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["address2"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received By:</td>
			<td><input name="recdBy" id="recdBy" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["receivedby"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["address3"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Paid:</td>
			<td><input name="freightPaid" id="freightPaid" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo ($row1["freight_paid"]=="Y" ? "Yes" : "No");}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["city_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Amount:</td>
			<td><input name="freightAmount" id="freightAmount" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["freight_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["state_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Bill Amount:</td>
			<td><input name="billAmount" id="billAmount" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["bill_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="billreturn"  method="post" onsubmit="return validate_bill_return_item()">
	<table align="center" width="860px" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Bill Return - [ Item ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="center" width="35%">Item Name</th>
			<th align="center" width="20%">Bill Qnty.</th>
			<th align="center" width="15%">Rate</th>
			<th align="center" width="20%">Return Qnty.</th>
		</tr>
		
		<tr class="Controls">
			<td><input name="itemName" id="itemName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["item_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td><input name="billQnty" id="billQnty" maxlength="10" size="15" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["bill_qnty"];}?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["unit_name"];} else { echo "";}?></td>
			
			<td><input name="itemRate" id="itemRate" maxlength="10" size="15" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["rate"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td><input name="returnQnty" id="returnQnty" maxlength="10" size="15" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["return_qnty"];}?>">&nbsp;<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["unit_name"];} else { echo "";}?></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:document.billreturn.reset()"><img src="images/reset.gif" width="122" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='pbselection.php'"><img src="images/back.gif" width="122" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<td valign="top">
	<table align="center" width="860px" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Bill Return - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="30%">Item Name</th>
			<th width="15%">Bill Qnty.</th>
			<th width="10%">Unit</th>
			<th width="15%">Rate</th>
			<th width="15%">Return Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_bill = mysql_db_query(DATABASE2,"SELECT tblbill2.*,item_name,unit_name FROM tblbill2 INNER JOIN ".DATABASE1.".item ON tblbill2.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON item.unit_id = unit.unit_id WHERE bill_id=".$bid." ORDER BY seq_no") or die(mysql_error());
		while($row_bill=mysql_fetch_array($sql_bill))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "billreturn.php?action=delete&bid=".$row_bill['bill_id']."&rid=".$row_bill['rec_id'];
			$edit_ref = "billreturn.php?action=edit&bid=".$row_bill['bill_id']."&rid=".$row_bill['rec_id'];
			
			if($utype=="A" || $utype=="S"){
				echo '<td align="center">'.$i.'.</td><td>'.$row_bill['item_name'].'</td><td align="center">'.$row_bill['bill_qnty'].'</td><td>'.$row_bill['unit_name'].'</td><td>'.$row_bill['rate'].'</td><td align="center">'.$row_bill['return_qnty'].'</td><td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td><td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			} elseif($utype=="U") {
				echo '<td align="center">'.$i.'.</td><td>'.$row_bill['item_name'].'</td><td align="center">'.$row_bill['bill_qnty'].'</td><td>'.$row_bill['unit_name'].'</td><td>'.$row_bill['rate'].'</td><td align="center">'.$row_bill['return_qnty'].'</td><td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td><td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			}
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="8">&nbsp;&nbsp;&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>