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
$sql_user = mysql_db_query(DATABASE2,"SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$uid) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
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
	$sd = strtotime(now);
	$ed = strtotime(now);
	$fromDate = date("Y-m-d",strtotime(now));
	$toDate = date("Y-m-d",strtotime(now));
}
/*-------------------------------*/
$oid = "";
if(isset($_REQUEST['oid'])){$oid = $_REQUEST['oid'];}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$sql = mysql_db_query(DATABASE2,"SELECT * FROM tbl_indent WHERE indent_id=".$oid);
	$row = mysql_fetch_assoc($sql);
} elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="recall"){
	$sql = mysql_db_query(DATABASE2,"SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN ".DATABASE1.".location ON tbl_indent.order_from = location.location_id WHERE indent_id=".$oid);
	$row = mysql_fetch_assoc($sql);
	$dateIndent = $row['indent_date'];
	$particulars = "From ".$row['location_name'];
	$res = mysql_db_query(DATABASE2,"UPDATE tbl_indent SET ind_status='U' WHERE indent_id=".$oid) or die(mysql_error());
	//insert into logbook
	$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
	$row = mysql_fetch_assoc($sql);
	$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
	$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
	$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$lname."','Recall','".$uname."')";
	$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
	//end of inserting record into logbook
	//header('Location:indentorder.php?action=new');
}
/*-------------------------------
if(isset($_POST['submit'])){
	$dateIndent=substr($_POST['indentDate'],6,4)."-".substr($_POST['indentDate'],3,2)."-".substr($_POST['indentDate'],0,2);
	$dateSupply=substr($_POST['supplyDate'],6,4)."-".substr($_POST['supplyDate'],3,2)."-".substr($_POST['supplyDate'],0,2);
	/*-------------------------------
	$sql = mysql_db_query(DATABASE1,"SELECT * FROM location WHERE location_id=".$_POST['indentFrom']);
	$row_loc = mysql_fetch_assoc($sql);
	$particulars = "From ".$row_loc['location_name'];
	/*-------------------------------
	if($_POST['submit']=="update"){
		$sql = "UPDATE tbl_indent SET indent_date='".$dateIndent."',order_from=".$_POST['indentFrom'].",";
		if($row_loc['location_prefix']==null)
			$sql .= "ind_prefix=null,";
		else
			$sql .= "ind_prefix='".$row_loc['location_prefix']."',";
		$sql .= "supply_date='".$dateSupply."',order_by=".$_POST['orderBy'].",uid=".$uid." WHERE indent_id=".$oid;
		$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
		//insert into logbook
		$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$lname."','Change','".$uname."')";
		$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		header('Location:indentitem.php?action=new&oid='.$oid);
	} elseif($_POST['submit']=="delete"){
		$res = mysql_db_query(DATABASE2,"DELETE FROM tbl_indent WHERE indent_id=".$oid) or die(mysql_error());
		$res = mysql_db_query(DATABASE2,"DELETE FROM tbl_indent_item WHERE indent_id=".$oid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_db_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$lname."','Delete','".$uname."')";
		$res = mysql_db_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		header('Location:indentorder.php?action=new');
	} elseif($_POST['submit']=="new"){
		$sql = mysql_db_query(DATABASE2,"SELECT Max(indent_id) as maxid FROM tbl_indent");
		$row = mysql_fetch_assoc($sql);
		$oid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_db_query(DATABASE2,"SELECT Max(indent_no) as maxno FROM tbl_indent WHERE order_from=".$_POST['indentFrom']." AND (indent_date BETWEEN '".date("Y-m-d",strtotime($syear))."' AND '".date("Y-m-d",strtotime($eyear))."')");
		$row = mysql_fetch_assoc($sql);
		$ino = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tbl_indent(indent_id,indent_date,indent_no,order_from,ind_prefix,supply_date,order_by,uid) VALUES(".$oid.",'".$dateIndent."',".$ino.",".$_POST['indentFrom'].",";
		if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
		$sql .= "'".$dateSupply."',".$_POST['orderBy'].",".$uid.")";
		$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
		echo '<script language="javascript">function show_message_indent_number(value1,value2){
			alert("Indent No. = "+value2);
			window.location="indentitem.php?action=new&oid="+value1;}
			show_message_indent_number('.$oid.','.$ino.');</script>';
//			header('Location:indentitem.php?action=new');
	}
}*/
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
<script type="text/javascript" src="js/tigra_hints.js"></script>
<script language="javascript" type="text/javascript">
function validate_indent()
{
	var err="";
	if(document.getElementById("indentDate").value!=""){
		if(!checkdate(document.indentorder.indentDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.indentorder.indentDate,document.indentorder.endYear);
			if(no_of_days1 < 0){
				err += "* Order Indent date wrongly selected. Please correct and submit again.\n";
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.indentorder.startYear,document.indentorder.indentDate);
				if(no_of_days2 < 0){
					err += "* Order Indent date wrongly selected. Please correct and submit again.\n";
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.indentorder.maxDate,document.indentorder.indentDate);
					if(no_of_days3 < 0){
						err += "* Order Indent date wrongly selected. Please correct and submit again.\n"+
						"Last indent date was "+document.getElementById("maxDate").value+", so lower date is not acceptable.\n";
					}
				}
			}
		}
	} else
		err += "* please input/select indent date!\n";
	if(document.getElementById("indentFrom").value==0)
		err += "* please select location, where order being sent from!\n";
	if(document.getElementById("orderBy").value==0)
		err += "* please select name of staff, who has given the order!\n";
	if(err==""){
		document.getElementById("submit").style.display = 'none';
		get_indent_submit(document.getElementById("xn").value,document.getElementById("indentDate").value,document.getElementById("supplyDate").value,document.getElementById("indentFrom").value,document.getElementById("orderBy").value,document.getElementById("indid").value);
		return true;
	} else {
		alert("Error: \n"+err);
		return false;
	}
}

function ClearForm()
{
	document.getElementById("indentNo").value="";
//	document.getElementById("indentDate").value=date("d-m-Y");
	document.getElementById("indentFrom").value=0;
	document.getElementById("orderBy").value=0;
//	document.getElementById("supplyDate").value=date("d-m-Y");
	document.getElementById("termsCondition").value="";
}

function validate_indentlist()
{
	if(checkdate(document.iolist.rangeFrom)){
		if(checkdate(document.iolist.rangeTo)){
			var no_of_days = getDaysbetween2Dates(document.iolist.rangeFrom,document.iolist.rangeTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else
				return true;
		}
	}
}

function listrange(me)
{
	document.getElementById("rf").value = me;
	paging_indent();
}

function paging_indent()
{
	if(document.getElementById("xn").value=="new")
		window.location="indentorder.php?action="+document.getElementById("xn").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&rf="+document.getElementById("rf").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
	else
		window.location="indentorder.php?action="+document.getElementById("xn").value+"&oid="+document.getElementById("indid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&rf="+document.getElementById("rf").value+"&sd="+document.getElementById("sd").value+"&ed="+document.getElementById("ed").value;
}

function firstpage_indent()
{
	document.getElementById("page").value = 1;
	paging_indent();
}

function previouspage_indent()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_indent();
}

function nextpage_indent()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_indent();
}

function lastpage_indent()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_indent();
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


<body onload="document.getElementById('indentDate').focus()">
<center>
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="775px" border="0">
<tr>
	<td align="left" width="30%" style="color:#009900"><?php echo 'user: '.$uname.', location: '.$lname; ?></td>
	<td align="center" width="40%" style="color:#0000FF"><?php echo '('.$syear.' to '.$eyear.')'; ?></td>
	<td align="left" width="30%" style="color:#009900">&nbsp;</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="indentorder"  method="post">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Indent No.:</td>
			<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
				if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
			} else {
				$indent_number = "";
			} ?>
			<td><input name="indentNo" id="indentNo" maxlength="15" size="20" readonly="true" value="<?php echo $indent_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Indent Date:<span style="color:#FF0000">*</span></td>
			<td><input name="indentDate" id="indentDate" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["indent_date"]));} else echo date("d-m-Y");?>"><script language="JavaScript">new tcal ({'formname': 'indentorder', 'controlname': 'indentDate'});</script></td>
		</tr>
		
		<tr class="Controls">
		<?php if($utype=="A" || $utype=="S"){?>
				<td class="th">Indent From:<span style="color:#FF0000">*</span></td>
				<td><select name="indentFrom" id="indentFrom" onchange="get_orderby_location(this.value)" style="width:300px">
				<option value="0">-- Select --</option>
				<?php 
				$sql_location=mysql_db_query(DATABASE1,"SELECT * FROM location ORDER BY location_name");
				while($row_location=mysql_fetch_array($sql_location))
				{
					if($row_location["location_id"]==$row["order_from"])
						echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
					else
						echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
				} ?>
				</select></td>
		<?php } elseif($utype=="U"){?>
				<td class="th">Indent From:</td>
				<td><input name="location" id="location" maxlength="50" size="45" readonly="true" value="<?php echo $lname; ?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="indentFrom" id="indentFrom" value="<?php echo $locid; ?>" /></td>
		<?php } ?>
			
			<td class="th" nowrap>Estimated Supply Date:</td>
			<td><input name="supplyDate" id="supplyDate" maxlength="10" size="10" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["supply_date"]));} else echo date("d-m-Y");?>"><script language="JavaScript">new tcal ({'formname': 'indentorder', 'controlname': 'supplyDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Order By:<span style="color:#FF0000">*</span></td>
			<td><div id="orderbydiv"><select name="orderBy" id="orderBy" onchange="get_max_date(document.getElementById('indentFrom').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('callingPage').value)" style="width:300px">
			<option value="0">-- Select --</option>
			<?php 
			if($utype=="A" || $utype=="S"){
				if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"))
					$sql_staff=mysql_db_query(DATABASE1,"SELECT * FROM staff WHERE location_id=".$row['order_from']." ORDER BY staff_name");
				else
					$sql_staff=mysql_db_query(DATABASE1,"SELECT * FROM staff WHERE location_id=0 ORDER BY staff_name");
			} elseif($utype=="U")
				$sql_staff=mysql_db_query(DATABASE1,"SELECT * FROM staff WHERE location_id=".$locid." ORDER BY staff_name");
			
			while($row_staff=mysql_fetch_array($sql_staff))
			{
				if($row_staff["staff_id"]==$row["order_by"])
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
			</select></div></td>
			
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($syear));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($eyear));?>"/><input type="hidden" name="maxDate" id="maxDate" value="<?php echo date("d-m-Y",strtotime($syear));?>"/><input type="hidden" name="startDate" id="startDate" value="<?php echo strtotime($syear);?>"/><input type="hidden" name="endDate" id="endDate" value="<?php echo strtotime($eyear);?>"/><input type="hidden" name="callingPage" id="callingPage" value="order_indent"/><input type="hidden" name="xn" id="xn" value="<?php echo $_REQUEST["action"];?>" /><input type="hidden" name="indid" id="indid" value="<?php echo $oid;?>" /></td>
			<td>&nbsp;</td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['oi1']==1){?>
				<img id="submit" src="images/add.gif" width="72" height="22" style="cursor:hand;" onclick="return validate_indent()"/>
			<?php } ?>
&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="reset()" />
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
				<img id="submit" src="images/update.gif" width="82" height="22" style="cursor:hand;" onclick="return validate_indent()"/>&nbsp;&nbsp;<img src="images/next.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='indentitem.php?action=new&oid=<?php echo $oid;?>'" />&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='indentorder.php?action=edit&oid=<?php echo $oid;?>'"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
				<img id="submit" src="images/delete.gif" width="72" height="22" style="cursor:hand;" onclick="return validate_indent()"/>&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='indentorder.php?action=delete&oid=<?php echo $oid;?>'"/>
		<?php }?>
&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='menu.php'"/>
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
	<form name="iolist"  method="post" onsubmit="return validate_indentlist()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
<!-- HTML code for the hint, make sure the hint has unique ID, it is positioned absolutely and initially invisible.
	The same hint element will be reused for all cells of the table. This approach simplifies and optimizes the
	implementation of the dynamically generated pages. -->

<div id="reusableHint" style="position:absolute;z-index:1;visibility:hidden;padding:10px;background-color:#FFFFCC;border:2px solid #CCCC00;"></div>
<!-- End of the HTML code for the hint -->

		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="right" colspan="7">
			<?php 
			if($utype=="A" || $utype=="S"){
				echo 'List For:&nbsp;&nbsp;<select name="rangeFor" id="rangeFor" style="width:110px" onchange="listrange(this.value)">';
				if(isset($_REQUEST['rf'])){
					if($_REQUEST['rf']=="U"){
						echo '<option selected value="U">Unsent items</option><option value="S">Sent items</option>';
					} elseif($_REQUEST['rf']=="S"){
						echo '<option value="U">Unsent items</option><option selected value="S">Sent items</option>';
					}
				} else {
					echo '<option selected value="U">Unsent items</option><option value="S">Sent items</option>';
				}
				echo '</select>';
			} ?>
			From:&nbsp;&nbsp;<input name="rangeFrom" id="rangeFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sd);?>"/>&nbsp;<script language="JavaScript">new tcal ({'formname': 'iolist', 'controlname': 'rangeFrom'});</script>&nbsp;&nbsp;To:&nbsp;&nbsp;<input name="rangeTo" id="rangeTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$ed);?>"/>&nbsp;<script language="JavaScript">new tcal ({'formname': 'iolist', 'controlname': 'rangeTo'});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="search" src="images/search.gif" width="82" height="22" alt="search"><input type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="20%">Indent No.</th>
			<th width="15%">Date</th>
			<th width="25%">Indent From</th>
			<th width="25%">Order By</th>
			<?php 
			if(isset($_REQUEST['rf'])){
				if($_REQUEST['rf']=="U"){echo '<th width="5%">Edit</th>';} elseif($_REQUEST['rf']=="S"){echo '<th width="5%">Recall</th>';}
			} else {
				echo '<th width="5%">Edit</th>';
			}
			?>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$start=0;
		if(isset($_REQUEST['rf'])){$rangeFor=$_REQUEST['rf'];} else {$rangeFor="U";}
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}
		
		$i = $start;
		$sql = "SELECT tbl_indent.*,location_name,staff_name FROM tbl_indent INNER JOIN ".DATABASE1.".location ON tbl_indent.order_from = location.location_id INNER JOIN ".DATABASE1.".staff ON tbl_indent.order_by = staff.staff_id WHERE ind_status='".$rangeFor."' AND (indent_date BETWEEN '".$fromDate."' AND '".$toDate."')";
		if($utype=="U"){
			$sql .= " AND order_from=".$locid;
		}
		$sql .= " ORDER BY location_name,indent_date,indent_id ";
		$sql .= "LIMIT ".$start.",".$end;
		$sql_indent = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
		
		while($row_indent=mysql_fetch_array($sql_indent))
		{
			$sql_item = mysql_db_query(DATABASE2,"SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN ".DATABASE1.".item ON tbl_indent_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$row_indent['indent_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item))
			{
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			if($row_indent['ind_status']=='S'){
				$edit_ref = "indentorder.php?action=recall&oid=".$row_indent['indent_id'];
			} elseif($row_indent['ind_status']=='U'){
				$edit_ref = "indentorder.php?action=edit&oid=".$row_indent['indent_id'];
			}
			$delete_ref = "indentorder.php?action=delete&oid=".$row_indent['indent_id'];
			
			$indent_number = ($row_indent['indent_no']>999 ? $row_indent['indent_no'] : ($row_indent['indent_no']>99 && $row_indent['indent_no']<1000 ? "0".$row_indent['indent_no'] : ($row_indent['indent_no']>9 && $row_indent['indent_no']<100 ? "00".$row_indent['indent_no'] : "000".$row_indent['indent_no'])));
			if($row_indent['ind_prefix']!=null){$indent_number = $row_indent['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is indent number '.$indent_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_indent['indent_date'])).'</td><td>'.$row_indent['location_name'].'</td><td>'.$row_indent['staff_name'].'</td>';
			if($row_indent['appr_status']=='S'){
				echo '<td align="center" style="color:#FF0000">approved</td>';
				echo '<td align="center">&nbsp;</td>';
			} elseif($row_indent['appr_status']=='U'){
				if($row_user['oi2']==1){
					if($row_indent['ind_status']=='S'){
						echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/undo.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
					} elseif($row_indent['ind_status']=='U'){
						echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
					}
				} elseif($row_user['oi2']==0)
					echo '<td align="center">&nbsp;</td>';
				if($row_user['oi3']==1)
					echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				elseif($row_user['oi3']==0)
					echo '<td align="center">&nbsp;</td>';
			}
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="7" align="center">
			<?php 
			$sql_total = "SELECT * FROM tbl_indent WHERE ind_status='".$rangeFor."' AND (indent_date BETWEEN '".$fromDate."' AND '".$fromDate."')";
			if($utype=="U"){
				$sql_total .= " AND order_from=".$locid;
			}
			$sql_total = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_indent()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" tabindex="11" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_indent()" style="vertical-align:middle">';
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
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="rf" id="rf" value="'.$rangeFor.'" />';
			if($total_page>1 && $_REQUEST["pg"]>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_indent()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_indent()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pg"]<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_indent()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_indent()" />';
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