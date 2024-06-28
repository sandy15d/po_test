<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT rr1,rr2,rr3,rr4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
if(isset($_POST['rangeFrom'])){
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
/*--------------------------------*/
$msg = "";
$rid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$return_no = "";
$return_date = date("d-m-Y");
$receipt_id = 0;
$receipt_date = "";
$po_no = "";
$po_date = "";
$challan_no = "";
$challan_date = "";
$transit_name = "";
$delivery_date = "";
$delivery_at = "";
$received_at = "";
$received_by = "";
$party_name = "";
$address1 = "";
$address2 = "";
$address3 = "";
$city_name = "";
$state_name = "";
$freight_paid = "";
$freight_amt = "";
$return_by = 0;
/*--------------------------------*/
if(isset($_REQUEST['rid'])){
	$rid = $_REQUEST['rid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM tblreceipt_return1 WHERE return_id=".$rid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$return_no = ($row['return_no']>999 ? $row['return_no'] : ($row['return_no']>99 && $row['return_no']<1000 ? "0".$row['return_no'] : ($row['return_no']>9 && $row['return_no']<100 ? "00".$row['return_no'] : "000".$row['return_no'])));
		$return_date = date("d-m-Y",strtotime($row["return_date"]));
		$receipt_id = $row["receipt_id"];
		$return_by = $row["return_by"];
		
		$sql1 = mysql_query("SELECT tblreceipt1.*, transit_name FROM tblreceipt1 INNER JOIN transit ON tblreceipt1.transit_point = transit.transit_id WHERE receipt_id=".$receipt_id) or die(mysql_error());
		$row1 = mysql_fetch_assoc($sql1);
		$location_id = $row1['recd_at'];
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
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateReturn = substr($_POST['returnDate'],6,4)."-".substr($_POST['returnDate'],3,2)."-".substr($_POST['returnDate'],0,2);
	$particulars = "To ".$_POST['partyName'];
	$sql = mysql_query("SELECT return_id FROM tblreceipt_return1 WHERE return_date='".$dateReturn."' AND receipt_id=".$_POST['receiptNo']." AND return_by=".$_POST['staffName']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblreceipt_return1 SET return_date='".$dateReturn."',receipt_id=".$_POST['receiptNo'].",return_by=".$_POST['staffName']." WHERE return_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($rid>999 ? $rid : ($rid>99 && $rid<1000 ? "0".$rid : ($rid>9 && $rid<100 ? "00".$rid : "000".$rid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="returnitem.php?action=new&rid='.$rid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblreceipt_return1 WHERE return_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblreceipt_return2 WHERE return_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='R-' AND entry_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($rid>999 ? $rid : ($rid>99 && $rid<1000 ? "0".$rid : ($rid>9 && $rid<100 ? "00".$rid : "000".$rid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateReturn."','Rcpt.Rtrn.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="receiptreturn.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into material return record.";
		else {
			$sql = mysql_query("SELECT Max(return_id) as maxid FROM tblreceipt_return1");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(return_no) as maxno FROM tblreceipt_return1 WHERE return_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."'");
			$row = mysql_fetch_assoc($sql);
			$rno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tblreceipt_return1(return_id,return_date,return_no,receipt_id,return_by) VALUES(".$rid.",'".$dateReturn."',".$rno.",".$_POST['receiptNo'].",".$_POST['staffName'].")";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">function show_message_rr_number(value1,value2){
				alert("Return No. = "+value2);
				window.location="returnitem.php?action=new&rid="+value1;}
				show_message_rr_number('.$rid.','.$rno.');</script>';
//			header('Location:returnitem.php?action=new&rid='.$rid);
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
<script type="text/javascript" src="js/tigra_hints.js"></script>
<script language="javascript" type="text/javascript">
function validate_receipt_return()
{
	if(document.getElementById("returnDate").value!=""){
		if(!checkdate(document.receiptreturn.returnDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.receiptreturn.returnDate,document.receiptreturn.endYear);
			if(no_of_days1 < 0){
				alert("* Material Return date wrongly selected. Please correct and submit again.");
				return false;
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.receiptreturn.startYear,document.receiptreturn.returnDate);
				if(no_of_days2 < 0){
					alert("* Material Return date wrongly selected. Please correct and submit again.");
					return false;
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.receiptreturn.receiptDate,document.receiptreturn.returnDate);
					if(no_of_days3 < 0){
						alert("* Material return date can't back day of receipt date. Please correct and submit again.");
						return false;
					}
				}
			}
		}
	} else if(document.getElementById("returnDate").value==""){
		alert("* please select/input material return date.");
		return false;
	}
	if(document.getElementById("receiptNo").value==0){
		alert("* please select material receipt number.");
		return false;
	}
	if(document.getElementById("staffName").value==0){
		alert("* please select staff, by whom the material being returned.");
		return false;
	}
	return true;
}

function validate_returnlist()
{
	if(checkdate(document.rrlist.rangeFrom)){
		if(checkdate(document.rrlist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.rrlist.rangeFrom,document.rrlist.rangeTo);
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
		window.location="receiptreturn.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	} else {
		window.location="receiptreturn.php?action="+document.getElementById("xson").value+"&rid="+document.getElementById("rrid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
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
<table align="center" cellspacing="0" cellpadding="0" height="500px" width="950px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="receiptreturn"  method="post" onsubmit="return validate_receipt_return()">
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
			
			<td class="th" nowrap>Return Date:<span style="color:#FF0000">*</span></td>
			<td><input name="returnDate" id="returnDate" maxlength="10" size="10" value="<?php echo $return_date;?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'receiptreturn', 'controlname': 'returnDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Receipt No.:<span style="color:#FF0000">*</span></td>
			<td><select name="receiptNo" id="receiptNo" style="width:300px" onchange="get_receipt_details(this.value)"><option value="0">-- Select --</option><?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_rcpt=mysql_query("SELECT * FROM tblreceipt1 ORDER BY recd_at,receipt_date,receipt_no");
			elseif($_SESSION['stores_utype']=="U"){
				$sql_rcpt=mysql_query("SELECT * FROM tblreceipt1 WHERE recd_at=".$locid." ORDER BY recd_at,receipt_date,receipt_no");}
			
			while($row_rcpt=mysql_fetch_array($sql_rcpt)){
				$receipt_number = ($row_rcpt['receipt_no']>999 ? $row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>99 && $row_rcpt['receipt_no']<1000 ? "0".$row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>9 && $row_rcpt['receipt_no']<100 ? "00".$row_rcpt['receipt_no'] : "000".$row_rcpt['receipt_no'])));
				if($row_rcpt['receipt_prefix']!=null){$receipt_number = $row_rcpt['receipt_prefix']."/".$receipt_number;}
				if($row_rcpt["receipt_id"]==$receipt_id)
					echo '<option selected value="'.$row_rcpt["receipt_id"].'">'.$receipt_number.'</option>';
				else
					echo '<option value="'.$row_rcpt["receipt_id"].'">'.$receipt_number.'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>Receipt Date:</td>
			<td><input name="receiptDate" id="receiptDate" maxlength="10" size="10" value="<?php echo $receipt_date;?>" style="background-color:#E7F0F8; color:#0000FF"><input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/><input type="hidden" name="recdLocation" id="recdLocation" value="<?php echo $location_id;?>"/></td>
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
			
			<td class="th" nowrap>Returned By:<span style="color:#FF0000">*</span></td>
			<td><span id="staffOption"><select name="staffName" id="staffName" style="width:300px"><option value="0">-- Select --</option><?php 
			$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$location_id." ORDER BY staff_name");
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$row["return_by"])
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
			</select></span></td>
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
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='returnitem.php?action=new&rid=<?php echo $rid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='receiptreturn.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='receiptreturn.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
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
	<form name="rrlist"  method="post" onsubmit="return validate_returnlist()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Receipt Return - [ List ]</strong></td>
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
			<th align="right" colspan="8">List Range From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'rrlist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'rrlist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="10%">Return No.</th>
			<th width="10%">Return Date</th>
			<th width="10%">Receipt No.</th>
			<th width="20%">Received At</th>
			<th width="35%">Party Name</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
			$sql_return = mysql_query("SELECT tblreceipt_return1.*, receipt_no, receipt_prefix, location_name, party_name FROM tblreceipt_return1 INNER JOIN tblreceipt1 ON tblreceipt_return1.receipt_id = tblreceipt1.receipt_id INNER JOIN location ON tblreceipt1.recd_at = location.location_id INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id WHERE return_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name, return_date, return_id LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_return = mysql_query("SELECT tblreceipt_return1.*, receipt_no, receipt_prefix, location_name, party_name FROM tblreceipt_return1 INNER JOIN tblreceipt1 ON tblreceipt_return1.receipt_id = tblreceipt1.receipt_id INNER JOIN location ON tblreceipt1.recd_at = location.location_id INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id WHERE recd_at=".$locid." AND return_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name, return_date, return_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_return=mysql_fetch_array($sql_return)){
			$sql_item = mysql_query("SELECT tblreceipt_return2.*,item_name,unit_name FROM tblreceipt_return2 INNER JOIN item ON tblreceipt_return2.item_id = item.item_id INNER JOIN unit ON tblreceipt_return2.unit_id = unit.unit_id WHERE return_id=".$row_return['return_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['return_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			
			$delete_ref = "receiptreturn.php?action=delete&rid=".$row_return['return_id'];
			$edit_ref = "receiptreturn.php?action=edit&rid=".$row_return['return_id'];
			
			$return_number = ($row_return['return_no']>999 ? $row_return['return_no'] : ($row_return['return_no']>99 && $row_return['return_no']<1000 ? "0".$row_return['return_no'] : ($row_return['return_no']>9 && $row_return['return_no']<100 ? "00".$row_return['return_no'] : "000".$row_return['return_no'])));
			
			$receipt_number = ($row_return['receipt_no']>999 ? $row_return['receipt_no'] : ($row_return['receipt_no']>99 && $row_return['receipt_no']<1000 ? "0".$row_return['receipt_no'] : ($row_return['receipt_no']>9 && $row_return['receipt_no']<100 ? "00".$row_return['receipt_no'] : "000".$row_return['receipt_no'])));
			if($row_return['receipt_prefix']!=null){$receipt_number = $row_return['receipt_prefix']."/".$receipt_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is return number '.$return_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$return_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_return['return_date'])).'</td><td>'.$receipt_number.'</td><td>'.$row_return['location_name'].'</td><td>'.$row_return['party_name'].'</td>';
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
		
		<tr class="Footer">
			<td colspan="8" align="center">
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblreceipt_return1 WHERE return_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblreceipt_return1 WHERE recd_at=".$locid." AND return_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_mr()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="rrid" id="rrid" value="'.$rid.'" />';
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