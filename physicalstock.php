<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT ps1,ps2,ps3,ps4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
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
$pid = "";
if(isset($_REQUEST['pid'])){$pid = $_REQUEST['pid'];}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$sql = mysql_query("SELECT tblpstock.*,location_name FROM tblpstock INNER JOIN location ON tblpstock.location_id = location.location_id WHERE ps_id=".$pid);
	$row = mysql_fetch_assoc($sql);
	$ps_location = $row['location_id'];
	$ps_number = $row['ps_no'];
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$datePStock=substr($_POST['pstockDate'],6,4)."-".substr($_POST['pstockDate'],3,2)."-".substr($_POST['pstockDate'],0,2);
	$entrymode = ($_POST['pstockType']=="I" ? "P+" : "P-");
	$sql_loc = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['location']) or die(mysql_error());
	$row_loc = mysql_fetch_assoc($sql_loc);
	$particulars = $row_loc['location_name'];
	/*-----------------------------*/
	if($_POST['submit']=="update"){
		if($_POST['location']!=$ps_location){
			$sql = mysql_query("SELECT Max(ps_no) as maxno FROM tblpstock WHERE location_id=".$_POST['location']." AND (ps_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
			$row = mysql_fetch_assoc($sql);
			$pno = $row["maxno"] + 1;
		} else {
			$pno = $ps_number;
		}
		$sql = "UPDATE tblpstock SET ps_date='".$datePStock."',ps_no=".$pno.",";
		if($row_loc['location_prefix']==null){$sql .= "ps_prefix=null, ";} else {$sql .= "ps_prefix='".$row_loc['location_prefix']."', ";}
		$sql .= "ps_type='".$_POST['pstockType']."',location_id=".$_POST['location']." WHERE ps_id=".$pid;
		$res = mysql_query($sql) or die(mysql_error());
		$res = mysql_query("UPDATE stock_register SET entry_date='".$datePStock."',location_id=".$_POST['location']." WHERE entry_mode='".$entrymode."' AND entry_id=".$pid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="psitem.php?action=new&pid='.$pid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblpstock WHERE ps_id=".$pid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblpstock_item WHERE ps_id=".$pid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='".$entrymode."' AND entry_id=".$pid." AND entry_date='".$datePStock."'") or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePStock."','Phy.Stock','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="physicalstock.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT Max(ps_id) as maxid FROM tblpstock");
		$row = mysql_fetch_assoc($sql);
		$pid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(ps_no) as maxno FROM tblpstock WHERE location_id=".$_POST['location']." AND (ps_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
		$row = mysql_fetch_assoc($sql);
		$pno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tblpstock(ps_id,ps_no,ps_prefix,ps_date,ps_type,location_id) VALUES(".$pid.",".$pno.",";
		if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
		$sql .= "'".$datePStock."','".$_POST['pstockType']."',".$_POST['location'].")";
		$res = mysql_query($sql) or die(mysql_error());
		echo '<script language="javascript">function show_message_ps_number(value1,value2){
			alert("Physical Stock No. = "+value2);
			window.location="psitem.php?action=new&pid="+value1;}
			show_message_ps_number('.$pid.','.$pno.');</script>';
//		header('Location:psitem.php?action=new&mid='.$pid);
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
function validate_pstock()
{
	var err="";
	if(document.getElementById("pstockDate").value!=""){
		if(!checkdate(document.physicalstock.pstockDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.physicalstock.pstockDate,document.physicalstock.endYear);
			if(no_of_days1 < 0){
				err += "* Physical Stock date wrongly selected. Please correct and submit again.\n";
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.physicalstock.startYear,document.physicalstock.pstockDate);
				if(no_of_days2 < 0){
					err += "* Physical Stock date wrongly selected. Please correct and submit again.\n";
				}
			}
		}
	} else if(document.getElementById("pstockDate").value==""){
		err += "* please select/input physical stock date!\n";
	}
	if(document.getElementById("location").value==0)
		err += "* please select location, where physical stock being verified!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function validate_pstocklist()
{
	if(checkdate(document.pslist.rangeFrom)){
		if(checkdate(document.pslist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.pslist.rangeFrom,document.pslist.rangeTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else
				return true;
		}
	}
}

function paging_pstock()
{
	if(document.getElementById("xson").value=="new"){
		window.location="physicalstock.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	} else {
		window.location="physicalstock.php?action="+document.getElementById("xson").value+"&pid="+document.getElementById("psid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	}
}

function firstpage_pstock()
{
	document.getElementById("page").value = 1;
	paging_pstock();
}

function previouspage_pstock()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_pstock();
}

function nextpage_pstock()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_pstock();
}

function lastpage_pstock()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_pstock();
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
<table align="center" cellspacing="0" cellpadding="0" height="200px" width="675px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="physicalstock"  method="post" onsubmit="return validate_pstock()">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Physical Stock - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>PS No.:</td>
			<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$ps_number = ($row['ps_no']>999 ? $row['ps_no'] : ($row['ps_no']>99 && $row['ps_no']<1000 ? "0".$row['ps_no'] : ($row['ps_no']>9 && $row['ps_no']<100 ? "00".$row['ps_no'] : "000".$row['ps_no'])));
				if($row['ps_prefix']!=null){$ps_number = $row['ps_prefix']."/".$ps_number;}
			} else {
				$ps_number = "";
			}?>
			<td><input name="pstockNo" id="pstockNo" maxlength="15" size="20" readonly="true" value="<?php echo $ps_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>PS Date:<span style="color:#FF0000">*</span></td>
			<td><input name="pstockDate" id="pstockDate" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["ps_date"]));} else echo date("d-m-Y");?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'physicalstock', 'controlname': 'pstockDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<?php
			$type1_status = 'checked';
			$type2_status = 'unchecked';
			if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$selected_radio = $row["ps_type"];
				if($selected_radio=="I"){
					$type1_status = 'checked';
					$type2_status = 'unchecked';
				} elseif($selected_radio=="D"){
					$type1_status = 'unchecked';
					$type2_status = 'checked';
				}
			} ?>
			<td class="th" nowrap>Stock Volume:<span style="color:#FF0000">*</span></td>
			<td><input type="radio" name="pstockType" id="type1" value="I" <?php echo $type1_status;?> >&nbsp;Increased&nbsp;&nbsp;<input type="radio" name="pstockType" id="type2" value="D" <?php echo $type2_status;?> >&nbsp;Decreased&nbsp;&nbsp;</td>
			
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/></td>
			<td>&nbsp;</td>
		</tr>
		
		<tr class="Controls">
			<?php if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
				echo '<td class="th">Location:<span style="color:#FF0000">*</span></td>';
				echo '<td><select name="location" id="location" style="width:300px">';
				echo '<option value="0">-- Select --</option>';
				$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
				while($row_location=mysql_fetch_array($sql_location)){
					if($row_location["location_id"]==$row["location_id"])
						echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
					else
						echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
				}
				echo '</select></td>';
			} elseif($_SESSION['stores_utype']=="U"){
				echo '<td class="th">Location:</td>';
				echo '<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$_SESSION['stores_locid'].'" /></td>';
			}?>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['ps1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['ps1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.physicalstock.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='psitem.php?action=new&pid=<?php echo $pid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='physicalstock.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='physicalstock.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<form name="pslist"  method="post" onsubmit="return validate_pstocklist()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Physical Stock - [ List ]</strong></td>
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
			<th colspan="6">List From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'pslist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'pslist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="4%">Sl.No.</th>
			<th width="30%">P.S. No.</th>
			<th width="20%">Date</th>
			<th width="40%">Location</th>
			<th width="3%">Edit</th>
			<th width="3%">Del</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
			$sql_ps = mysql_query("SELECT tblpstock.*, location_name FROM tblpstock INNER JOIN location ON tblpstock.location_id = location.location_id WHERE ps_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name, ps_date, ps_no LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_ps = mysql_query("SELECT tblpstock.*, location_name FROM tblpstock INNER JOIN location ON tblpstock.location_id = location.location_id WHERE tblpstock.location_id=".$_SESSION['stores_locid']." AND ps_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name, ps_date, ps_no LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_ps=mysql_fetch_array($sql_ps)){
			$sql_item = mysql_query("SELECT tblpstock_item.*, item_name, unit_name FROM tblpstock_item INNER JOIN item ON tblpstock_item.item_id = item.item_id INNER JOIN unit ON tblpstock_item.unit_id = unit.unit_id WHERE ps_id=".$row_ps['ps_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['ps_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "physicalstock.php?action=delete&pid=".$row_ps['ps_id'];
			$edit_ref = "physicalstock.php?action=edit&pid=".$row_ps['ps_id'];
			
			$ps_number = ($row_ps['ps_no']>999 ? $row_ps['ps_no'] : ($row_ps['ps_no']>99 && $row_ps['ps_no']<1000 ? "0".$row_ps['ps_no'] : ($row_ps['ps_no']>9 && $row_ps['ps_no']<100 ? "00".$row_ps['ps_no'] : "000".$row_ps['ps_no'])));
			if($row_ps['ps_prefix']!=null){$ps_number = $row_ps['ps_prefix']."/".$ps_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is p.s. number '.$ps_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$ps_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ps['ps_date'])).'</td><td>'.$row_ps['location_name'].'</td>';
			if($row_user['ps2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ps2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ps3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ps3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="6" align="center">
			<?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblpstock WHERE ps_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblpstock WHERE location_id=".$_SESSION['stores_locid']." AND ps_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_pstock()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="psid" id="psid" value="'.$pid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pstock()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pstock()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pstock()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pstock()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pstock()" />';
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