<?php 
include("menu.php");
/*------------------------------------------*/
$sql_user = mysql_query("SELECT pb1,pb2,pb3,pb4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*------------------------------------------*/
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
/*------------------------------------------*/
$msg = "";
$bid = "";
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$bid = $_REQUEST['bid'];
	$sql = mysql_query("SELECT tblbill.*, party_name, address1, address2, address3, city_name, state_name, company_name FROM tblbill INNER JOIN party ON tblbill.party_id = party.party_id INNER JOIN company ON tblbill.company_id = company.company_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE bill_id=".$bid) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
}
/*------------------------------------------*/
if(isset($_POST['submit'])){
	$dateBill=substr($_POST['billDate'],6,4)."-".substr($_POST['billDate'],3,2)."-".substr($_POST['billDate'],0,2);
	$billAmt = ($_POST['billAmount']=="" ? 0 : $_POST['billAmount']);
	$particulars = "From ".$_POST['partyName'];
	$itemname = "Bill To ".$_POST['companyName'];
	/*------------------------------------------*/
	$sql=mysql_query("SELECT bill_id FROM tblbill WHERE bill_no='".$_POST['billNo']."' AND bill_date='".$dateBill."' AND party_id=".$_POST['party']." AND company_id=".$_POST['company']) or die(mysql_error());
	$row_bill=mysql_fetch_assoc($sql);
	$count=mysql_num_rows($sql);
	/*------------------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_bill['bill_id']!=$bid)
				$msg = "Duplication Error! can&prime;t update into purchase bill record.";
			elseif($row_bill['bill_id']==$bid) {
				$sql = "UPDATE tblbill SET bill_no='".$_POST['billNo']."',bill_date='".$dateBill."',party_id=".$_POST['party'].",company_id=".$_POST['company'].",bill_amt=".$billAmt." WHERE bill_id=".$bid;
				$res = mysql_query($sql) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$voucherid = ($bid>999 ? $bid : ($bid>99 && $bid<1000 ? "0".$bid : ($bid>9 && $bid<100 ? "00".$bid : "000".$bid)));
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."',".$billAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
			}
		} elseif($count==0){
			$sql = "UPDATE tblbill SET bill_no='".$_POST['billNo']."',bill_date='".$dateBill."',party_id=".$_POST['party'].",company_id=".$_POST['company'].",bill_amt=".$billAmt." WHERE bill_id=".$bid;
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$voucherid = ($bid>999 ? $bid : ($bid>99 && $bid<1000 ? "0".$bid : ($bid>9 && $bid<100 ? "00".$bid : "000".$bid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."',".$billAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblbill WHERE bill_id=".$bid) or die(mysql_error());
		//$res = mysql_query("DELETE FROM tblbill_po WHERE bill_id=".$bid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblbill_item WHERE bill_id=".$bid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($bid>999 ? $bid : ($bid>99 && $bid<1000 ? "0".$bid : ($bid>9 && $bid<100 ? "00".$bid : "000".$bid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."',".$billAmt.",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="purchasebill.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into purchase bill record.";
		else {
			$sql = mysql_query("SELECT Max(bill_id) as maxid FROM tblbill");
			$row = mysql_fetch_assoc($sql);
			$bid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO tblbill(bill_id, bill_no, bill_date, party_id, company_id, bill_amt) VALUES(".$bid.",'".$_POST['billNo']."','".$dateBill."',".$_POST['party'].",".$_POST['company'].",".$billAmt.")";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$voucherid = ($bid>999 ? $bid : ($bid>99 && $bid<1000 ? "0".$bid : ($bid>9 && $bid<100 ? "00".$bid : "000".$bid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."',".$billAmt.",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
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
function validate_billdetail()
{
	var err="";
	var retval = true;
	if(document.getElementById("billNo").value==""){
		err = "* please input purchase bill number!\n";
		retval = false;
	}
	if(document.getElementById("billDate").value!=""){
		if(!checkdate(document.purchasebill.billDate)){
			retval = false;
		}
	} else if(document.getElementById("billDate").value==""){
		err += "* please select/input purchase bill date!\n";
		retval = false;
	}
	if(document.getElementById("party").value==0){
		err += "* please select a party from the list!\n";
		retval = false;
	}
	if(document.getElementById("billAmount").value!="" && ! IsNumeric(document.getElementById("billAmount").value)){
		err += "* please input valid numeric data for bill amount!\n";
		retval = false;
	}
	if(document.getElementById("billAmount").value=="" && document.getElementById("billAmount").value==0){
		err += "* please input bill amount!\n";
		retval = false;
	}
	if(document.getElementById("company").value==0){
		err += "* please select a company from the list!\n";
		retval = false;
	}
	if(err!=""){
		alert("Error: \n"+err);
	}
	return retval;
}

function get_company_name(me)
{
	var w = document.getElementById('company').selectedIndex;
	var selected_text = document.getElementById('company').options[w].text;
	document.getElementById('companyName').value = selected_text;
}

function validate_pbillList()
{
	if(checkdate(document.pblist.rangeFrom)){
		if(checkdate(document.pblist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.pblist.rangeFrom,document.pblist.rangeTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else
				return true;
		}
	}
}

function paging_pbill()
{
	if(document.getElementById("xson").value=="new"){
		window.location="purchasebill.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	} else {
		window.location="purchasebill.php?action="+document.getElementById("xson").value+"&bid="+document.getElementById("biid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	}
}

function firstpage_pbill()
{
	document.getElementById("page").value = 1;
	paging_pbill();
}

function previouspage_pbill()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_pbill();
}

function nextpage_pbill()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_pbill();
}

function lastpage_pbill()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_pbill();
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
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="purchasebill"  method="post" onsubmit="return validate_billdetail()">
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
			<td class="th" nowrap>Bill No.:<span style="color:#FF0000">*</span></td>
			<td><input name="billNo" id="billNo" maxlength="15" size="20" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["bill_no"];}?>" ></td>
			
			<td class="th" nowrap>Bill Date:<span style="color:#FF0000">*</span></td>
			<td><input name="billDate" id="billDate" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["bill_date"]));} else { echo date("d-m-Y");}?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'purchasebill', 'controlname': 'billDate'	});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:<span style="color:#FF0000">*</span></td>
			<td><select name="party" id="party" style="width:300px" onchange="get_partydetails_on_pbill(this.value)">
			<option value="0">-- Select --</option>
			<?php 
			$sql_party=mysql_query("SELECT * FROM party ORDER BY party_name");
			while($row_party=mysql_fetch_array($sql_party)){
				if($row_party["party_id"]==$row["party_id"])
					echo '<option selected value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
				else
					echo '<option value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
			}?>
			</select><input type="hidden" name="partyName" id="partyName" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["party_name"];}?>"></td>
			
			<td class="th" nowrap>Bill Amount:</td>
			<td><input name="billAmount" id="billAmount" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["bill_amt"];}?>" ></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address1"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Company Name:<span style="color:#FF0000">*</span></td>
			<td><select name="company" id="company" style="width:300px" onchange="get_company_name(this.value)">
			<option value="0">-- Select --</option>
			<?php 
			$sql_company=mysql_query("SELECT * FROM company ORDER BY company_name");
			while($row_company=mysql_fetch_array($sql_company)){
				if($row_company["company_id"]==$row["company_id"])
					echo '<option selected value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
				else
					echo '<option value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
			}?>
			</select><input type="hidden" name="companyName" id="companyName" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["company_name"];}?>"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address2"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address-3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address3"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["city_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["state_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
 		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['pb1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['pb1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
			&nbsp;&nbsp;<a href="javascript:document.purchasebill.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='pbitem.php?action=new&bid=<?php echo $bid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='purchasebill.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='purchasebill.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<form name="pblist"  method="post" onsubmit="return validate_pbillList()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Bill - [ List ]</strong></td>
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
			<th align="right" colspan="8">List Range From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'pblist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'pblist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="15%">Bill No.</th>
			<th width="10%">Bill Date</th>
			<th width="25%">Party Name</th>
			<th width="10%">Bill Amount</th>
			<th width="25%">company Name</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql_bill = mysql_query("SELECT tblbill.*, party_name, company_name FROM tblbill INNER JOIN party ON tblbill.party_id = party.party_id INNER JOIN company ON tblbill.company_id = company.company_id WHERE bill_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY party_name,bill_date, bill_no LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_bill=mysql_fetch_array($sql_bill)){
			$sql_item = mysql_query("SELECT tblbill_item.*, item_name, unit_name FROM tblbill_item INNER JOIN item ON tblbill_item.item_id = item.item_id INNER JOIN unit ON tblbill_item.unit_id = unit.unit_id WHERE bill_id=".$row_bill['bill_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['bill_qnty'].' '.$row_item['unit_name'].'</td><td>'.$row_item['rate'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "purchasebill.php?action=delete&bid=".$row_bill['bill_id'];
			$edit_ref = "purchasebill.php?action=edit&bid=".$row_bill['bill_id'];
			
			$billNumber = $row_bill['bill_no'];
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is bill number '.$billNumber.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$billNumber.'</td><td align="center">'.date("d-m-Y",strtotime($row_bill['bill_date'])).'</td><td>'.$row_bill['party_name'].'</td><td align="right">'.number_format($row_bill['bill_amt'],2,".","").'</td><td>'.$row_bill['company_name'].'</td>';
			
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
		
		<tr class="Footer">
			<td colspan="8" align="center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM tblbill WHERE bill_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_pbill()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="biid" id="biid" value="'.$bid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pbill()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pbill()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pbill()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pbill()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pbill()" />';
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