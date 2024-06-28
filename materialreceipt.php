<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT mr1,mr2,mr3,mr4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
if(isset($_POST['show'])){
	$fromDate=substr($_POST['rangeFrom'],6,4)."-".substr($_POST['rangeFrom'],3,2)."-".substr($_POST['rangeFrom'],0,2);
	$toDate=substr($_POST['rangeTo'],6,4)."-".substr($_POST['rangeTo'],3,2)."-".substr($_POST['rangeTo'],0,2);
	$sd = strtotime($fromDate);
	$ed = strtotime($toDate);
} elseif(isset($_REQUEST['sd'])){
	$sd = $_REQUEST['sd'];
	$ed = $_REQUEST['ed'];
	$fromDate = date("Y-m-d",$sd);
	$toDate = date("Y-m-d",$ed);
} else {
	$sd = strtotime(date("Y-m-d"));
	$ed = strtotime(date("Y-m-d"));
	$fromDate = date("Y-m-d");
	$toDate = date("Y-m-d");
}
/*----------------------------------------*/
$msg = "";
$mid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$receipt_number = "";
$receipt_no = 0;
$receipt_date = date("d-m-Y");
$dc_date = "";
$po_date = "";
$delivery_date = "";
$po_number = "";
$challan_no = "";
$challan_date = "";
$transit_point = 0;
$party_name = "";
$delivery_at = "";
$address1 = "";
$address2 = "";
$address3 = "";
$recd_by = 0;
$city_name = "";
$state_name = "";
$freight_paid = "N";
/*----------------------------------------*/
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT tblreceipt1.*, dc_no, dc_date, po_no, po_date, delivery_date, party_name, address1, address2, address3, city_name, state_name, location_name AS delivery_at FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE receipt_id=".$mid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$location_id = $row['recd_at'];
		$receipt_no = $row['receipt_no'];
		$receipt_number = ($receipt_no>999 ? $receipt_no : ($receipt_no>99 && $receipt_no<1000 ? "0".$receipt_no : ($receipt_no>9 && $receipt_no<100 ? "00".$receipt_no : "000".$receipt_no)));
		if($row['receipt_prefix']!=null){$receipt_number = $row['receipt_prefix']."/".$receipt_number;}
		$receipt_date = date("d-m-Y",strtotime($row["receipt_date"]));
		$recd_by = $row["recd_by"];
		$dc_date = date("d-m-Y",strtotime($row["dc_date"]));
		$po_date = date("d-m-Y",strtotime($row["po_date"]));
		$po_number = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
		$challan_no = $row["challan_no"];
		$challan_date = ($row["challan_date"]==NULL ? "" : date("d-m-Y",strtotime($row["challan_date"])));
		$transit_point = $row["transit_point"];
		$delivery_date = date("d-m-Y",strtotime($row["delivery_date"]));
		$party_name = $row["party_name"];
		$delivery_at = $row["delivery_at"];
		$address1 = $row["address1"];
		$address2 = $row["address2"];
		$address3 = $row["address3"];
		$city_name = $row["city_name"];
		$state_name = $row["state_name"];
		$freight_paid = $row["freight_paid"];
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateReceipt=substr($_POST['receiptDate'],6,4)."-".substr($_POST['receiptDate'],3,2)."-".substr($_POST['receiptDate'],0,2);
	$dateChallan=substr($_POST['challanDate'],6,4)."-".substr($_POST['challanDate'],3,2)."-".substr($_POST['challanDate'],0,2);
	$freightAmt = ($_POST['freightAmount']=="" ? 0 : $_POST['freightAmount']);
	$particulars = "From ".$_POST['partyName'];
	/*--------------------------------*/
	$sql = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['location']);
	$row_loc = mysql_fetch_assoc($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		if($_POST['location']!=$location_id){
			$sql = mysql_query("SELECT Max(receipt_no) as maxno FROM tblreceipt1 WHERE recd_at=".$_POST['location']." AND (receipt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
			$row = mysql_fetch_assoc($sql);
			$rno = $row["maxno"] + 1;
		} else {
			$rno = $receipt_no;
		}
		$sql = "UPDATE tblreceipt1 SET receipt_date='".$dateReceipt."', dc_id=".$_POST['dcNo'].", receipt_no=".$rno.", challan_no='".$_POST['challanNo'];
		if($_POST['challanDate']==""){$sql .= "', challan_date=NULL, ";} else {$sql .= "', challan_date='".$dateChallan."', ";}
		if($row_loc['location_prefix']==null){$sql .= "receipt_prefix=null, ";} else {$sql .= "receipt_prefix='".$row_loc['location_prefix']."', ";}
		$sql .= "transit_point=".$_POST['transitPoint'].", recd_at=".$_POST['location'].", recd_by=".$_POST['staffName'].", freight_paid='".$_POST['freightPaid']."', freight_amt=".$freightAmt." WHERE receipt_id=".$mid;
		$res = mysql_query($sql) or die(mysql_error());
		$res = mysql_query("UPDATE stock_register SET entry_date='".$dateReceipt."', location_id=".$_POST['location']." WHERE entry_mode='R+' AND entry_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="receiptitem.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblreceipt1 WHERE receipt_id=".$mid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblreceipt2 WHERE receipt_id=".$mid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='R+' AND entry_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReceipt."','Mtrl.Rcpt.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="materialreceipt.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT Max(receipt_id) as maxid FROM tblreceipt1");
		$row = mysql_fetch_assoc($sql);
		$mid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(receipt_no) as maxno FROM tblreceipt1 WHERE recd_at=".$_POST['location']." AND (receipt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
		$row = mysql_fetch_assoc($sql);
		$rno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tblreceipt1(receipt_id, receipt_date, receipt_no, dc_id, challan_no, receipt_prefix, challan_date, transit_point, recd_at, recd_by, freight_paid, freight_amt) VALUES(".$mid.",'".$dateReceipt."',".$rno.",".$_POST['dcNo'].",'".$_POST['challanNo'];
		if($row_loc['location_prefix']==null){$sql .= "',null,";} else {$sql .= "','".$row_loc['location_prefix']."',";}
		if($_POST['challanDate']==""){$sql .= "NULL,";} else {$sql .= "'".$dateChallan."',";}
		$sql .= $_POST['transitPoint'].",".$_POST['location'].",".$_POST['staffName'].",'".$_POST['freightPaid']."',".$freightAmt.")";
		$res = mysql_query($sql) or die(mysql_error());
//		header('Location:receiptitem.php?action=new&mid='.$mid);
		echo '<script language="javascript">function show_message_mr_number(value1,value2){
			alert("Receipt No. = "+value2);
			window.location="receiptitem.php?action=new&mid="+value1;}
			show_message_mr_number('.$mid.','.$rno.');</script>';
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
<script type="text/javascript" src="js/tigra_hints.js"></script>
<script language="javascript" type="text/javascript">
function validate_receipt()
{
	if(document.getElementById("dcNo").value==0){
		alert("* please select Delivery Confirmation number!");
		return false;
	}
	if(document.getElementById("receiptDate").value!=""){
		if(!checkdate(document.materialreceipt.receiptDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.materialreceipt.receiptDate,document.materialreceipt.endYear);
			if(no_of_days1 < 0){
				alert("* Material Receipt date wrongly selected. Please correct and submit again.");
				return false;
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.materialreceipt.startYear,document.materialreceipt.receiptDate);
				if(no_of_days2 < 0){
					alert("* Material Receipt date wrongly selected. Please correct and submit again.");
					return false;
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.materialreceipt.poDate,document.materialreceipt.receiptDate);
					if(no_of_days3 < 0){
						alert("* Material receipt date is beyond PO date. Please correct and submit again.");
						return false;
					} else {
						var no_of_days4 = getDaysbetween2Dates(document.materialreceipt.maxDate,document.materialreceipt.receiptDate);
						if(no_of_days4 < 0){
							alert("* Material Receipt date wrongly selected. Please correct and submit again.\n"+
							"Last receipt date was "+document.getElementById("maxDate").value+", so lower date is not acceptable.");
							return false;
						}
					}
				}
			}
		}
	} else {
		alert("* please select/input material receipt date!");
		return false;
	}
	if(document.getElementById("challanDate").value!=""){
		if(!checkdate(document.materialreceipt.challanDate)){return false;}
	}
	if(document.getElementById("poDate").value!="" && document.getElementById("challanDate").value!=""){
		var no_of_days = getDaysbetween2Dates(document.materialreceipt.poDate,document.materialreceipt.challanDate);
		if(no_of_days < 0){
			alert("* Material challan date wrongly selected. Please correct and submit again.");
			return false;
		}
	}
	if(document.getElementById("transitPoint").value==0){
		alert("* please select transit point, where from the material being transited.");
		return false;
	}
	if(document.getElementById("location").value==0){
		alert("* please select location, where the material being received.");
		return false;
	}
	if(document.getElementById("staffName").value==0){
		alert("* please select staff, by whom the material being received.");
		return false;
	}
	if(document.getElementById("freightAmount").value!="" && ! IsNumeric(document.getElementById("freightAmount").value)){
		alert("* please input valid numeric data for freight amount!");
		return false;
	}
	return true;
}

function set_freight_focus(value1)
{
	if(value1=="Y"){
		document.getElementById('frgtAmt').innerHTML = '<input name="freightAmount" id="freightAmount" maxlength="10" size="10" value="">';
	} else if(value1=="N"){
		document.getElementById("freightAmount").value=="";
		document.getElementById('frgtAmt').innerHTML = '<input name="freightAmount" id="freightAmount" maxlength="10" size="10" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF">';
	}
}

function validate_receiptlist()
{
	if(checkdate(document.mrlist.rangeFrom)){
		if(checkdate(document.mrlist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.mrlist.rangeFrom,document.mrlist.rangeTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else
				return true;
		}
	}
}

function paging_mr()
{
	if(document.getElementById("xson").value=="new"){
		window.location="materialreceipt.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	} else {
		window.location="materialreceipt.php?action="+document.getElementById("xson").value+"&mid="+document.getElementById("mrid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	}
}

function firstpage_mr()
{
	document.getElementById("page").value = 1;
	paging_mr();
}

function previouspage_mr()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_mr();
}

function nextpage_mr()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_mr();
}

function lastpage_mr()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_mr();
}

var HINTS_CFG = {
	'wise'       : true, // don't go off screen, don't overlap the object in the document
	'margin'     : 10, // minimum allowed distance between the hint and the window edge (negative values accepted)
	'gap'        : -10, // minimum allowed distance between the hint and the origin (negative values accepted)
	'align'      : 'brtl', // align of the hint and the origin (by first letters origin's top|middle|bottom left|center|right to hint's top|middle|bottom left|center|right)
	'show_delay' : 100, // a delay between initiating event (mouseover for example) and hint appearing
	'hide_delay' : 0 // a delay between closing event (mouseout for example) and hint disappearing
};
var myHint = new THints (null, HINTS_CFG);

// custom JavaScript function that updates the text of the hint before displaying it
function myShow(s_text, e_origin) {
	var e_hint = getElement('reusableHint');
	e_hint.innerHTML = s_text;
	myHint.show('reusableHint', e_origin);
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="450px" width="950px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="materialreceipt"  method="post" onsubmit="return validate_receipt()">
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
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" width="10%" nowrap>Receipt No.:</td>
			<td width="40%"><input name="receiptNo" id="receiptNo" maxlength="15" size="20" readonly="true" value="<?php echo $receipt_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" width="15%" nowrap>Receipt Date:<span style="color:#FF0000">*</span></td>
			<td width="35%"><input name="receiptDate" id="receiptDate" maxlength="10" size="10" value="<?php echo $receipt_date;?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'materialreceipt', 'controlname': 'receiptDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>DC No.:<span style="color:#FF0000">*</span></td>
			<td><select name="dcNo" id="dcNo" style="width:145px" onchange="get_dcdetails_on_mat_rcpt(this.value)"><option value="0">-- Select --</option><?php 
			if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
				$sql_dc=mysql_query("SELECT tbldelivery1.* FROM tbldelivery1 INNER JOIN tbldelivery2 ON tbldelivery1.dc_id = tbldelivery2.dc_id WHERE item_received='N' ORDER BY dc_no") or die(mysql_error());
			} elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$sql_dc=mysql_query("SELECT tbldelivery1.* FROM tbldelivery1 INNER JOIN tbldelivery2 ON tbldelivery1.dc_id = tbldelivery2.dc_id WHERE item_received='N' OR tbldelivery1.dc_id=".$row['dc_id']." ORDER BY dc_no") or die(mysql_error());
			}
			while($row_dc=mysql_fetch_array($sql_dc)){
				$dc_number = ($row_dc['dc_no']>999 ? $row_dc['dc_no'] : ($row_dc['dc_no']>99 && $row_dc['dc_no']<1000 ? "0".$row_dc['dc_no'] : ($row_dc['dc_no']>9 && $row_dc['dc_no']<100 ? "00".$row_dc['dc_no'] : "000".$row_dc['dc_no'])));
				if($row_dc["dc_id"]==$row["dc_id"])
					echo '<option selected value="'.$row_dc["dc_id"].'">'.$dc_number.'</option>';
				else
					echo '<option value="'.$row_dc["dc_id"].'">'.$dc_number.'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>DC Date:</td>
			<td><input name="dcDate" id="dcDate" maxlength="10" size="10" readonly="true" value="<?php echo $dc_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>PO No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_number;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>PO Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" readonly="true" value="<?php echo $po_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Challan No.:</td>
			<td><input name="challanNo" id="challanNo" maxlength="50" size="45" value="<?php echo $challan_no;?>" ></td>
			
			<td class="th" nowrap>Challan Date:</td>
			<td><input name="challanDate" id="challanDate" maxlength="10" size="10" value="<?php echo $challan_date;?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'materialreceipt', 'controlname': 'challanDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Transit Point:<span style="color:#FF0000">*</span></td>
			<td><select name="transitPoint" id="transitPoint" style="width:300px"><option value="0">-- Select --</option><?php 
			$sql_location=mysql_query("SELECT * FROM transit ORDER BY transit_name");
			while($row_location=mysql_fetch_array($sql_location)){
				if($row_location["transit_id"]==$transit_point)
					echo '<option selected value="'.$row_location["transit_id"].'">'.$row_location["transit_name"].'</option>';
				else
					echo '<option value="'.$row_location["transit_id"].'">'.$row_location["transit_name"].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a href="transit.php?action=new" target="_blank"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
			
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
			
			<td class="th" nowrap>Received At:<span style="color:#FF0000">*</span></td>
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
				echo '<td><select name="location" id="location" onchange="get_staffs(this.value)" style="width:300px">';
				echo '<option value="0">-- Select --</option>';
				$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
				while($row_location=mysql_fetch_array($sql_location)){
					if($row_location["location_id"]==$location_id)
						echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
					else
						echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
				}
				echo '</select></td>';
			} elseif($_SESSION['stores_utype']=="U"){
				echo '<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$_SESSION['stores_locid'].'" /></td>';
			} ?>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Received By:<span style="color:#FF0000">*</span></td>
			<td><span id="staffOption"><select name="staffName" id="staffName" style="width:300px"><option value="0">-- Select --</option><?php 
			$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$location_id." ORDER BY staff_name");
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$recd_by)
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
			</select></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php echo $address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Paid:<span style="color:#FF0000">*</span></td>
			<td><select name="freightPaid" id="freightPaid" style="width:90px" onchange="set_freight_focus(this.value)"><?php 
			if($freight_paid=="Y")
				echo '<option selected value="Y">Yes</option><option value="N">No</option>';
			elseif($freight_paid=="N")
				echo '<option value="Y">Yes</option><option selected value="N">No</option>';
			?>
			</select></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php echo $city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Freight Amount:</td>
			<?php 
			if($freight_paid=="Y")
				echo '<td><span id="frgtAmt"><input name="freightAmount" id="freightAmount" maxlength="10" size="10" value="'.$row["freight_amt"].'" ></span></td>';
			elseif($freight_paid=="N")
				echo '<td><span id="frgtAmt"><input name="freightAmount" id="freightAmount" maxlength="10" size="10" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF"></span></td>';
			?>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php echo $state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/><input type="hidden" name="maxDate" id="maxDate" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/></td>
			<td>&nbsp;</td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['mr1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['mr1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.materialreceipt.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='receiptitem.php?action=new&mid=<?php echo $mid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='materialreceipt.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='materialreceipt.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
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
	<form name="mrlist"  method="post" onsubmit="return validate_receiptlist()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Receipt - [ List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
<!-- HTML code for the hint, make sure the hint has unique ID, it is positioned absolutely and initially invisible.
	The same hint element will be reused for all cells of the table. This approach simplifies and optimizes the
	implementation of the dynamically generated pages. -->

<div id="reusableHint" style="position:absolute;z-index:1;visibility:hidden;padding:10px;background-color:#FFFFCC;border:2px solid #CCCC00;"></div>
<!-- End of the HTML code for the hint -->

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="right" colspan="9">List Range From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'mrlist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'mrlist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="show" value="show"/><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="15%">Receipt No.</th>
			<th width="10%">Rcpt.Date</th>
			<th width="10%">D.C. No.</th>
			<th width="10%">D.C. Date</th>
			<th width="20%">Received At</th>
			<th width="24%">Party Name</th>
			<th width="3%">Edit</th>
			<th width="3%">Del</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql = "SELECT tblreceipt1.*, dc_no, dc_date, location_name, party_name FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id INNER JOIN location ON tblreceipt1.recd_at = location.location_id INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id WHERE (receipt_date BETWEEN '".$fromDate."' AND '".$toDate."')";
		if($_SESSION['stores_utype']=="U"){
			$sql .= " AND recd_at=".$_SESSION['stores_locid'];
		}
		$sql .= " ORDER BY receipt_date, receipt_id LIMIT ".$start.",".$end;
		$sql_receipt = mysql_query($sql) or die(mysql_error());
		while($row_receipt=mysql_fetch_array($sql_receipt)){
			$sql_item = mysql_query("SELECT tblreceipt2.*,item_name,unit_name FROM tblreceipt2 INNER JOIN item ON tblreceipt2.item_id = item.item_id INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE receipt_id=".$row_receipt['receipt_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['receipt_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "materialreceipt.php?action=delete&mid=".$row_receipt['receipt_id']."&sd=".$sd."&ed=".$ed;
			$edit_ref = "materialreceipt.php?action=edit&mid=".$row_receipt['receipt_id']."&sd=".$sd."&ed=".$ed;
			
			$receipt_number = ($row_receipt['receipt_no']>999 ? $row_receipt['receipt_no'] : ($row_receipt['receipt_no']>99 && $row_receipt['receipt_no']<1000 ? "0".$row_receipt['receipt_no'] : ($row_receipt['receipt_no']>9 && $row_receipt['receipt_no']<100 ? "00".$row_receipt['receipt_no'] : "000".$row_receipt['receipt_no'])));
			if($row_receipt['receipt_prefix']!=null){$receipt_number = $row_receipt['receipt_prefix']."/".$receipt_number;}
			
			$dc_number = ($row_receipt['dc_no']>999 ? $row_receipt['dc_no'] : ($row_receipt['dc_no']>99 && $row_receipt['dc_no']<1000 ? "0".$row_receipt['dc_no'] : ($row_receipt['dc_no']>9 && $row_receipt['dc_no']<100 ? "00".$row_receipt['dc_no'] : "000".$row_receipt['dc_no'])));
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is receipt number '.$receipt_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$receipt_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_receipt['receipt_date'])).'</td><td align="center">'.$dc_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_receipt['dc_date'])).'</td><td align="center">'.$row_receipt['location_name'].'</td><td>'.$row_receipt['party_name'].'</td>';
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
		
		<tr class="Footer">
			<td colspan="9" align="center">
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblreceipt1 WHERE (receipt_date BETWEEN '".$fromDate."' AND '".$toDate."')") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblreceipt1 WHERE recd_at=".$_SESSION['stores_locid']." AND (receipt_date BETWEEN '".$fromDate."' AND '".$toDate."')") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_mr()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="mrid" id="mrid" value="'.$mid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_mr()" style="vertical-align:middle">';
				for($i=1;$i<=$total_page;$i++)
				{
					if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)
						echo '<option selected value="'.$i.'">'.$i.'</option>';
					else
						echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}else {
				echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';
			if($total_page>1 && $pg>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_mr()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_mr()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_mr()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_mr()" />';
			?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
</table>
</center>
</body>
</html>