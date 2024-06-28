<?php 
include("menu.php");
/*------------------------------------------*/
$sql_user = mysql_query("SELECT cp1,cp2,cp3,cp4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*----------------------------------------*/
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
$tid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$txn_number = "";
$memo_no = "";
$memo_date = date("d-m-Y");
$particulars = "";
$memo_amt = "";
$company_id = 0;
if(isset($_REQUEST['tid'])){
	$tid = $_REQUEST['tid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT tblcashmemo.*, company_name,location_name FROM tblcashmemo INNER JOIN company ON tblcashmemo.company_id = company.company_id INNER JOIN location ON tblcashmemo.location_id = location.location_id WHERE txn_id=".$tid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$txn_number = ($row['txn_id']>999 ? $row['txn_id'] : ($row['txn_id']>99 && $row['txn_id']<1000 ? "0".$row['txn_id'] : ($row['txn_id']>9 && $row['txn_id']<100 ? "00".$row['txn_id'] : "000".$row['txn_id'])));
		$memo_no = $row["memo_no"];
		$memo_date = date("d-m-Y",strtotime($row["memo_date"]));
		$particulars = $row["particulars"];
		$memo_amt = $row["memo_amt"];
		$company_id = $row["company_id"];
		$location_id = $row["location_id"];
	}
}
/*------------------------------------------*/
if(isset($_POST['submit'])){
	$dateMemo=substr($_POST['memoDate'],6,4)."-".substr($_POST['memoDate'],3,2)."-".substr($_POST['memoDate'],0,2);
	$particulars = "From ".$_POST['particulars'];
	$itemname = "Bill To ".$_POST['companyName'];
	/*------------------------------------------*/
	$sql=mysql_query("SELECT txn_id FROM tblcashmemo WHERE memo_no='".$_POST['memoNo']."' AND memo_date='".$dateMemo."' AND company_id=".$_POST['company']." AND location_id=".$_POST['location']) or die(mysql_error());
	$row_memo=mysql_fetch_assoc($sql);
	$count=mysql_num_rows($sql);
	/*------------------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_memo['txn_id']!=$tid)
				$msg = "Duplication Error! can&prime;t update into cash purchase record.";
			elseif($row_memo['txn_id']==$tid) {
				$sql = "UPDATE tblcashmemo SET memo_no='".$_POST['memoNo']."',memo_date='".$dateMemo."',company_id=".$_POST['company'].",location_id=".$_POST['location'].",particulars='".$_POST['particulars']."',memo_amt=".$_POST['purchaseAmount']." WHERE txn_id=".$tid;
				$res = mysql_query($sql) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$voucherid = ($tid>999 ? $tid : ($tid>99 && $tid<1000 ? "0".$tid : ($tid>9 && $tid<100 ? "00".$tid : "000".$tid)));
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."',".$_POST['purchaseAmount'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
			}
		} elseif($count==0){
			$sql = "UPDATE tblcashmemo SET memo_no='".$_POST['memoNo']."',memo_date='".$dateMemo."',company_id=".$_POST['company'].",location_id=".$_POST['location'].",particulars='".$_POST['particulars']."',memo_amt=".$_POST['purchaseAmount']." WHERE txn_id=".$tid;
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$voucherid = ($tid>999 ? $tid : ($tid>99 && $tid<1000 ? "0".$tid : ($tid>9 && $tid<100 ? "00".$tid : "000".$tid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."',".$_POST['purchaseAmount'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="cashpuritem.php?action=new&tid='.$tid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblcashmemo WHERE txn_id=".$tid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblcash_item WHERE txn_id=".$tid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='C+' AND entry_id=".$tid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($tid>999 ? $tid : ($tid>99 && $tid<1000 ? "0".$tid : ($tid>9 && $tid<100 ? "00".$tid : "000".$tid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateMemo."','Cash Pur.','".date("Y-m-d")."','".$particulars."','".$itemname."',".$_POST['purchaseAmount'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="cashpurchase.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into cash purchase record.";
		else {
			$sql = mysql_query("SELECT Max(txn_id) as maxid FROM tblcashmemo");
			$row = mysql_fetch_assoc($sql);
			$tid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO tblcashmemo(txn_id, memo_no, memo_date, company_id, location_id, particulars, memo_amt) VALUES(".$tid.",'".$_POST['memoNo']."','".$dateMemo."',".$_POST['company'].",".$_POST['location'].",'".$_POST['particulars']."',".$_POST['purchaseAmount'].")";
			$res = mysql_query($sql) or die(mysql_error());
			//header('Location:cashpurchase.php?action=new&tid='.$tid);
			echo '<script language="javascript">function show_message_txn_number(value1){
				alert("Txn.No. = "+value1);
				window.location="cashpuritem.php?action=new&tid="+value1;}
				show_message_txn_number('.$tid.');</script>';
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
	if(document.getElementById("memoNo").value==""){
		err = "* please input cash memo number!\n";
		retval = false;
	}
	if(document.getElementById("memoDate").value!=""){
		if(!checkdate(document.cashpurchase.memoDate)){
			retval = false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.cashpurchase.memoDate,document.cashpurchase.endYear);
			if(no_of_days1 < 0){
				err += "* Cash Memo date wrongly selected. Please correct and submit again.\n";
				retval = false;
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.cashpurchase.startYear,document.cashpurchase.memoDate);
				if(no_of_days2 < 0){
					err += "* Cash Memo date wrongly selected. Please correct and submit again.\n";
					retval = false;
				}
			}
		}
	} else if(document.getElementById("memoDate").value==""){
		err += "* please select/input cash memo date!\n";
		retval = false;
	}
	if(document.getElementById("purchaseAmount").value!="" && ! IsNumeric(document.getElementById("purchaseAmount").value)){
		err += "* please input valid numeric data for cash memo amount!\n";
		retval = false;
	}
	if(document.getElementById("purchaseAmount").value=="" && document.getElementById("purchaseAmount").value==0){
		err += "* please input cash memo amount!\n";
		retval = false;
	}
	if(document.getElementById("company").value==0){
		err += "* please select a company from the list!\n";
		retval = false;
	}
	if(document.getElementById("location").value==0){
		err += "* please select a location from the list!\n";
		retval = false;
	}
	if(err!=""){alert("Error: \n"+err);}
	return retval;
}

function validate_cpList()
{
	if(checkdate(document.cplist.rangeFrom)){
		if(checkdate(document.cplist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.cplist.rangeFrom,document.cplist.rangeTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else
				return true;
		}
	}
}

function paging_cpurchase()
{
	if(document.getElementById("xson").value=="new"){
		window.location="cashpurchase.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	} else {
		window.location="cashpurchase.php?action="+document.getElementById("xson").value+"&tid="+document.getElementById("cpid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	}
}

function firstpage_cpurchase()
{
	document.getElementById("page").value = 1;
	paging_cpurchase();
}

function previouspage_cpurchase()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_cpurchase();
}

function nextpage_cpurchase()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_cpurchase();
}

function lastpage_cpurchase()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_cpurchase();
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
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="850px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="cashpurchase"  method="post" onsubmit="return validate_billdetail()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
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
			
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/></td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Memo No.:<span style="color:#FF0000">*</span></td>
			<td><input name="memoNo" id="memoNo" maxlength="15" size="20" value="<?php echo $memo_no;?>"></td>
			
			<td class="th" nowrap>Memo Date:<span style="color:#FF0000">*</span></td>
			<td><input name="memoDate" id="memoDate" maxlength="10" size="10" value="<?php echo $memo_date;?>">&nbsp;<script language="JavaScript">new tcal ({'formname': 'cashpurchase', 'controlname': 'memoDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Particulars:</td>
			<td><input name="particulars" id="particulars" maxlength="50" size="45" value="<?php echo $particulars;?>"></td>
			
			<td class="th" nowrap>Purchase Amount:<span style="color:#FF0000">*</span></td>
			<td><input name="purchaseAmount" id="purchaseAmount" maxlength="10" size="10" value="<?php echo $memo_amt;?>"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Company Name:<span style="color:#FF0000">*</span></td>
			<td><select name="company" id="company" style="width:300px"><option value="0">-- Select --</option><?php 
			$sql_company=mysql_query("SELECT * FROM company ORDER BY company_name");
			while($row_company=mysql_fetch_array($sql_company)){
				if($row_company["company_id"]==$company_id)
					echo '<option selected value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
				else
					echo '<option value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>Location:<span style="color:#FF0000">*</span></td>
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
				echo '<td><select name="location" id="location" style="width:300px">';
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
				echo '<td class="th">Location::</td>';
				echo '<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$locid.'" /></td>';
			}?>
		</tr>
		
 		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['cp1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['cp1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
			&nbsp;&nbsp;<a href="javascript:document.cashpurchase.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='cashpuritem.php?action=new&tid=<?php echo $tid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='cashpurchase.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='cashpurchase.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
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
	<form name="cplist"  method="post" onsubmit="return validate_cpList()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Cash Purchase - [ List ]</strong></td>
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
			<th align="right" colspan="10">List Range From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script language="JavaScript">new tcal ({'formname': 'cplist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script language="JavaScript">new tcal ({'formname': 'cplist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="15%">Memo No.</th>
			<th width="10%">Memo Date</th>
			<th width="10%">Amount</th>
			<th width="25%">Company</th>
			<th width="25%">Location</th>
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
			$sql_memo = mysql_query("SELECT tblcashmemo.*, location_name, company_name FROM tblcashmemo INNER JOIN location ON tblcashmemo.location_id = location.location_id INNER JOIN company ON tblcashmemo.company_id = company.company_id WHERE memo_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY memo_date,memo_no LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_memo = mysql_query("SELECT tblcashmemo.*, location_name, company_name FROM tblcashmemo INNER JOIN location ON tblcashmemo.location_id = location.location_id INNER JOIN company ON tblcashmemo.company_id = company.company_id WHERE location_id=".$locid." AND memo_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY memo_date,memo_no LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_memo=mysql_fetch_array($sql_memo))
		{
			$sql_item = mysql_query("SELECT tblcash_item.*, item_name, unit_name FROM tblcash_item INNER JOIN item ON tblcash_item.item_id = item.item_id INNER JOIN unit ON tblcash_item.unit_id = unit.unit_id WHERE txn_id=".$row_memo['txn_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item))
			{
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['memo_qnty'].' '.$row_item['unit_name'].'</td><td>'.$row_item['rate'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "cashpurchase.php?action=delete&tid=".$row_memo['txn_id'];
			$edit_ref = "cashpurchase.php?action=edit&tid=".$row_memo['txn_id'];
			
			$memoNumber = $row_memo['memo_no'];
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is cash memo number '.$memoNumber.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$memoNumber.'</td><td align="center">'.date("d-m-Y",strtotime($row_memo['memo_date'])).'</td><td align="right">'.number_format($row_memo['memo_amt'],2,".","").'</td><td>'.$row_memo['company_name'].'</td><td>'.$row_memo['location_name'].'</td>';
			
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
		
		<tr class="Footer">
			<td colspan="8" align="center">
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblcashmemo WHERE memo_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblcashmemo WHERE location_id=".$locid." AND memo_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_cpurchase()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="cpid" id="cpid" value="'.$tid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_cpurchase()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_cpurchase()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_cpurchase()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_cpurchase()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_cpurchase()" />';
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