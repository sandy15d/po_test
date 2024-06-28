<?php 
include("menu.php");
/*------------------------------------------*/
$sql_user = mysql_db_query(DATABASE2,"SELECT pb1,pb2,pb3,pb4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*------------------------------------------*/
$bid = $_REQUEST['bid'];
$sql = mysql_db_query(DATABASE2,"SELECT tblbill.*, party_name, address1, address2, address3, city_name, state_name, company_name FROM tblbill INNER JOIN ".DATABASE1.".party ON tblbill.party_id = party.party_id INNER JOIN ".DATABASE1.".company ON tblbill.company_id = company.company_id INNER JOIN ".DATABASE1.".city ON party.city_id = city.city_id INNER JOIN ".DATABASE1.".state ON city.state_id = state.state_id WHERE bill_id=".$bid) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
/*------------------------------------------*/
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql1 = mysql_db_query(DATABASE2,"SELECT tblbill_po.*, po_date, company_name, location_name AS orderfrom FROM tblbill_po INNER JOIN tblpo ON tblbill_po.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".location ON tblpo.delivery_at = location.location_id INNER JOIN ".DATABASE1.".company ON tblpo.company_id = company.company_id WHERE rec_id=".$rid) or die(mysql_error());
	$row1 = mysql_fetch_assoc($sql1);
}
/*------------------------------------------*/
if(isset($_POST['submit'])){
	$dateChallan=substr($_POST['challanDate'],6,4)."-".substr($_POST['challanDate'],3,2)."-".substr($_POST['challanDate'],0,2);
	$sql = mysql_db_query(DATABASE2,"SELECT * FROM tblbill_po WHERE bill_id=".$bid." AND po_id=".$_POST['poNo']) or die(mysql_error());
	$row_bill = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_bill['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into purchase bill record.";
			elseif($row_bill['rec_id']==$rid) {
				$res = mysql_db_query(DATABASE2,"UPDATE tblbill_po SET po_id=".$_POST['poNo'].",challan_no='".$_POST['challanNo']."',challan_date='".$dateChallan."',uid=".$_SESSION['stores_uid']." WHERE rec_id=".$rid) or die(mysql_error());
				echo '<script language="javascript">window.location="purchasebillitem.php?action=new&bid='.$bid.'&pid='.$_POST['poNo'].'";</script>';
			}
		} elseif($count==0){
			if($row1['po_id']!=$_POST['poNo']){
				$res = mysql_db_query(DATABASE2,"DELETE FROM tblbill_item WHERE bill_id=".$bid." AND po_id=".$row1['po_id']) or die(mysql_error());
			}
			$res = mysql_db_query(DATABASE2,"UPDATE tblbill_po SET po_id=".$_POST['poNo'].",challan_no='".$_POST['challanNo']."',challan_date='".$dateChallan."',uid=".$_SESSION['stores_uid']." WHERE rec_id=".$rid) or die(mysql_error());
			echo '<script language="javascript">window.location="purchasebillitem.php?action=new&bid='.$bid.'&pid='.$_POST['poNo'].'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_db_query(DATABASE2,"DELETE FROM tblbill_po WHERE rec_id=".$rid) or die(mysql_error());
		$res = mysql_db_query(DATABASE2,"DELETE FROM tblbill_item WHERE bill_id=".$bid." AND po_id=".$_POST['poNo']) or die(mysql_error());
		echo '<script language="javascript">window.location="purchasebillitem.php?action=new&bid='.$bid.'&pid='.$_POST['poNo'].'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into purchase bill record.";
		else {
			$sql = mysql_db_query(DATABASE2,"SELECT Max(rec_id) as maxid FROM tblbill_po");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = "INSERT INTO tblbill_po(rec_id, bill_id, po_id, challan_no, challan_date, uid) VALUES(".$rid.",".$bid.",".$_POST['poNo'].",'".$_POST['challanNo']."','".$dateChallan."',".$_SESSION['stores_uid'].")";
			$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
			echo '<script language="javascript">window.location="purchasebillitem.php?action=new&bid='.$bid.'&pid='.$_POST['poNo'].'";</script>';
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
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_billpo()
{
	var err="";
	if(document.getElementById("poNo").value==0)
		err = "* please select a P.O. number!\n";
	if(document.getElementById("challanDate").value!=""){
		if(!checkdate(document.billitem.challanDate)){return false;}
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
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Bill - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Bill No.:</td>
			<td><input name="billNo" id="billNo" maxlength="15" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["bill_no"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Bill Date:</td>
			<td><input name="billDate" id="billDate" maxlength="10" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo date("d-m-Y",strtotime($row["bill_date"]));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="partyName" id="partyName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["party_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Bill Amount:</td>
			<td><input name="billAmount" id="billAmount" maxlength="10" size="20" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["bill_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address1"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>company Name:</td>
			<td><input name="company" id="company" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["company_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address2"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["city_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["address3"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row["state_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="billitem"  method="post" onsubmit="return validate_billpo()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:<span style="color:#FF0000">*</span></td>
			<td><select name="poNo" id="poNo" style="width:150px" onchange="get_podetails_on_pbill(this.value)" tabindex="1">
			<option value="0">-- Select --</option>
			<?php 
			$sql_order=mysql_db_query(DATABASE2,"SELECT * FROM tblpo WHERE po_status='S' ORDER BY po_id");
			while($row_order=mysql_fetch_array($sql_order))
			{
				$po_number = ($row_order['po_id']>999 ? $row_order['po_id'] : ($row_order['po_id']>99 && $row_order['po_id']<1000 ? "0".$row_order['po_id'] : ($row_order['po_id']>9 && $row_order['po_id']<100 ? "00".$row_order['po_id'] : "000".$row_order['po_id'])));
				if($row_order["po_id"]==$row1["po_id"])
					echo '<option selected value="'.$row_order["po_id"].'">'.$po_number.'</option>';
				else
					echo '<option value="'.$row_order["po_id"].'">'.$po_number.'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>P.O. Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" readonly="true" value="<?php if(isset($_REQUEST["action"])){echo ($row1["po_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["po_date"])));}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Challan No.:</td>
			<td><input name="challanNo" id="challanNo" maxlength="15" size="20" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row1["challan_no"];}?>" tabindex="2"></td>
			
			<td class="th" nowrap>Challan Date:</td>
			<td><input name="challanDate" id="challanDate" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo ($row1["challan_date"]==""?"&nbsp;":date("d-m-Y",strtotime($row1["challan_date"])));} else { echo date("d-m-Y");}?>" tabindex="3">&nbsp;<script language="JavaScript">new tcal ({'formname': 'billitem', 'controlname': 'challanDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Company:</td>
			<td><input name="companyName" id="companyName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["company_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Order From:</td>
			<td><input name="orderFrom" id="orderFrom" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"])){ echo $row1["orderfrom"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<?php if($msg!=""){
		echo '<tr class="Controls">
			<td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td>
		</tr>';
		} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['pb1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new" tabindex="3"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['pb1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new" tabindex="3" >
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.billitem.reset()" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update" tabindex="3"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='purchasebillpo.php?action=new&bid=<?php echo $bid;?>'" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete" tabindex="3"><input type="hidden" name="submit" value="delete"/>
&nbsp;&nbsp;<a href="javascript:window.location='purchasebillpo.php?action=new&bid=<?php echo $bid;?>'" tabindex="4"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='purchasebill.php?action=new'" tabindex="5"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>Purchase Bill - [ P.O. List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="3%">Sl.No.</th>
			<th width="10%">P.O. No.</th>
			<th width="12%">P.O. Date</th>
			<th width="10%">Challan No.</th>
			<th width="10%">Challan Date</th>
			<th width="20%">Order For</th>
			<th width="25%">Company Name</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_billpo = mysql_db_query(DATABASE2,"SELECT tblbill_po.*, po_date, company_name, location_name FROM tblbill_po INNER JOIN tblpo ON tblbill_po.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".location ON tblpo.delivery_at = location.location_id INNER JOIN ".DATABASE1.".company ON tblpo.company_id = company.company_id WHERE bill_id=".$bid." ORDER BY tblbill_po.rec_id") or die(mysql_error());
		while($row_billpo=mysql_fetch_array($sql_billpo))
		{
			$i++;
			$po_number = ($row_billpo['po_id']>999 ? $row_billpo['po_id'] : ($row_billpo['po_id']>99 && $row_billpo['po_id']<1000 ? "0".$row_billpo['po_id'] : ($row_billpo['po_id']>9 && $row_billpo['po_id']<100 ? "00".$row_billpo['po_id'] : "000".$row_billpo['po_id'])));
			echo '<tr class="Row">';
			$delete_ref = "purchasebillpo.php?action=delete&bid=".$bid."&rid=".$row_billpo['rec_id'];
			$edit_ref = "purchasebillpo.php?action=edit&bid=".$bid."&rid=".$row_billpo['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$po_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_billpo['po_date'])).'</td><td align="center">'.$row_billpo['challan_no'].'</td><td>'.($row_billpo['challan_date']==""?"&nbsp;":date("d-m-Y",strtotime($row_billpo['challan_date']))).'</td><td>'.$row_billpo['location_name'].'</td><td>'.$row_billpo['company_name'].'</td>';
			if($row_user['pb2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pb2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['pb3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pb3']==0)
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