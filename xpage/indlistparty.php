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
//if($utype=="U"){header("Location: login.php");}
/*--------------------------------*/
$pid = 0;
if(isset($_REQUEST['pid'])){$pid = $_REQUEST['pid'];}
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$pid = $_POST['party'];
} elseif(isset($_POST['showBottom'])){
	$sm=strtotime($_POST['dateFrombottom']);
	$em=strtotime($_POST['dateTobottom']);
	$pid = $_POST['party'];
} elseif(isset($_REQUEST['sm'])){
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
} else {
	$sm = strtotime(now);
	$em = strtotime(now);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Store Management System</title>
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_dateselection()
{
	if(checkdate(document.indentlist.dateFrom)){
		if(checkdate(document.indentlist.dateTo)){
			var no_of_days = getDaysbetween2Dates(document.indentlist.dateFrom,document.indentlist.dateTo);
			if(no_of_days < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else {
				return true;
			}
		}
	}
}

function setDateRange11()
{
	document.indentlist.dateFrombottom.value = document.indentlist.dateFrom.value;
}
function setDateRange12()
{
	document.indentlist.dateTobottom.value = document.indentlist.dateTo.value;
}
function setDateRange21()
{
	document.indentlist.dateFrom.value = document.indentlist.dateFrombottom.value;
}
function setDateRange22()
{
	document.indentlist.dateTo.value = document.indentlist.dateTobottom.value;
}

function paging_pilist()
{
	window.location="indlistparty.php?pid="+document.getElementById("party").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sm="+document.getElementById("date1").value+"&em="+document.getElementById("date2").value;
}

function firstpage_pilist()
{
	document.getElementById("page").value = 1;
	paging_pilist();
}

function previouspage_pilist()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_pilist();
}

function nextpage_pilist()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_pilist();
}

function lastpage_pilist()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_pilist();
}

</script>
</head>

<body>
<?php echo date("d-m-Y, h:i:s",strtotime(now));?>
<form name="indentlist" method="post">
<table align="center" border="0" cellpadding="2" cellspacing="1" width="1000">
<tbody>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
		<td>Indent List Partywise</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000" >
		<td><?php 
		if($utype=="A" || $utype=="S"){
			echo 'Select Party: ';
			echo '<select name="party" id="party" style="width:200px" >';
			echo '<option value="0">All Parties</option>';
			$sql_party=mysql_db_query(DATABASE1,"SELECT * FROM party ORDER BY party_name");
			while($row_party=mysql_fetch_array($sql_party))
			{
				if($row_party["party_id"]==$pid)
					echo '<option selected value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
				else
					echo '<option value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
			}
			echo '</select>';
		}?>
		</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000" >
		<td align="center">Date From:&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>" onchange="setDateRange11()" tabindex="1">&nbsp;<script language="JavaScript">new tcal ({"formname": "indentlist", "controlname": "dateFrom"});</script>&nbsp;&nbsp;Date To:&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>" onchange="setDateRange12()" tabindex="2">&nbsp;<script language="JavaScript">new tcal ({"formname": "indentlist", "controlname": "dateTo"});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show" tabindex="3"><input type="hidden" name="show" value="show"/>&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" tabindex="4"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
	</td></tr>
	<tr><td>
		<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%">
		<tbody>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="5%" valign="middle">Sl. No.</td>
				<td width="10%" valign="middle" style="border-left:none">Indent No.</td>
				<td width="10%" valign="middle" style="border-left:none">Date</td>
				<td width="15%" valign="middle" style="border-left:none">Location</td>
				<td width="15%" valign="middle" style="border-left:none">Order By</td>
				<td width="10%" valign="middle" style="border-left:none">Supply Date</td>
				<td width="20%" valign="middle" style="border-left:none">Item Name</td>
				<td width="15%" colspan="2" valign="middle" style="border-left:none" align="right">Indent Qnty.</td>
			</tr>
		<?php 
		$start=0;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}
		$ctr = $start;
		$partyNo=0;
		
		if($pid==0){
			$sql = mysql_db_query(DATABASE2,"SELECT rec_id, tblpo_item.po_id, tblpo_item.indent_id, seq_no, tblpo_item.item_id, sum(qnty) AS qnty, tblpo.party_id, party_name, indent_date, supply_date, location_name, staff_name, item_name, unit_name FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id INNER JOIN ".DATABASE1.".location ON tbl_indent.order_from = location.location_id INNER JOIN ".DATABASE1.".staff ON tbl_indent.order_by = staff.staff_id INNER JOIN ".DATABASE1.".item ON tblpo_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON item.unit_id = unit.unit_id WHERE (indent_date>='".date("Y-m-d",$sm)."' AND indent_date<='".date("Y-m-d",$em)."') GROUP BY tblpo_item.indent_id, tblpo_item.item_id ORDER BY party_name, tblpo_item.indent_id, seq_no LIMIT ".$start.",".$end) or die(mysql_error());
		} else {
			$sql = mysql_db_query(DATABASE2,"SELECT rec_id, tblpo_item.po_id, tblpo_item.indent_id, seq_no, tblpo_item.item_id, sum(qnty) AS qnty, tblpo.party_id, party_name, indent_date, supply_date, location_name, staff_name, item_name, unit_name FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id INNER JOIN ".DATABASE1.".location ON tbl_indent.order_from = location.location_id INNER JOIN ".DATABASE1.".staff ON tbl_indent.order_by = staff.staff_id INNER JOIN ".DATABASE1.".item ON tblpo_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON item.unit_id = unit.unit_id WHERE tblpo.party_id=".$_POST['party']." AND (indent_date>='".date("Y-m-d",$sm)."' AND indent_date<='".date("Y-m-d",$em)."') GROUP BY tblpo_item.indent_id, tblpo_item.item_id ORDER BY party_name, tblpo_item.indent_id, seq_no LIMIT ".$start.",".$end) or die(mysql_error());
		}
		while($row=mysql_fetch_array($sql))
		{
			$indentNo = ($row['indent_id']>999 ? $row['indent_id'] : ($row['indent_id']>99 && $row['indent_id']<1000 ? "0".$row['indent_id'] : ($row['indent_id']>9 && $row['indent_id']<100 ? "00".$row['indent_id'] : "000".$row['indent_id'])));
			$ctr += 1;
			if($row['party_id']==$partyNo){
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-bottom:none" width="5%">'.$ctr.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.$indentNo.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['indent_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['location_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['staff_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['supply_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '</tr>';
			} elseif($row['party_id']!=$partyNo){
				$partyNo = $row['party_id'];
				
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-bottom:none; font-weight:bold" width="5%">Party : </td>';
				echo '<td style="border-left:none; border-bottom:none; font-weight:bold" width="10%" colspan="8">'.$row['party_name'].'</td>';
				echo '</tr>';
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-bottom:none" width="5%">'.$ctr.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.$indentNo.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['indent_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['location_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['staff_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['supply_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '</tr>';
			}
		}
		
		?>
		</tbody></table></td></tr>
	<tr><td align="center"> <?php 
			if($pid==0){
				$sql_total = mysql_db_query(DATABASE2,"SELECT rec_id, tblpo_item.po_id, tblpo_item.indent_id, seq_no, tblpo_item.item_id, sum(qnty), tblpo.party_id, party_name, indent_date FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE (indent_date>='".date("Y-m-d",$sm)."' AND indent_date<='".date("Y-m-d",$em)."') GROUP BY tblpo_item.indent_id, tblpo_item.item_id ORDER BY party_name, tblpo_item.indent_id, seq_no") or die(mysql_error());
			} else {
				$sql_total = mysql_db_query(DATABASE2,"SELECT rec_id, tblpo_item.po_id, tblpo_item.indent_id, seq_no, tblpo_item.item_id, sum(qnty), tblpo.party_id, party_name, indent_date FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE tblpo.party_id=".$_POST['party']." AND (indent_date>='".date("Y-m-d",$sm)."' AND indent_date<='".date("Y-m-d",$em)."') GROUP BY tblpo_item.indent_id, tblpo_item.item_id ORDER BY party_name, tblpo_item.indent_id, seq_no") or die(mysql_error());
			}
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pilist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" tabindex="5" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pilist()" style="vertical-align:middle">';
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
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="date1" id="date1" value="'.$sm.'" /><input type="hidden" name="date2" id="date2" value="'.$em.'" />';
			if($total_page>1 && $_REQUEST["pg"]>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pilist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pilist()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pg"]<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pilist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pilist()" />'; ?>
	</td></tr>
	<tr><td align="center">
		Date From:&nbsp;&nbsp;<input name="dateFrombottom" id="dateFrombottom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>" onchange="setDateRange21()" tabindex="6">&nbsp;<script language="JavaScript">new tcal ({"formname": "logbook", "controlname": "dateFrombottom"});</script>&nbsp;&nbsp;Date To:&nbsp;&nbsp;<input name="dateTobottom" id="dateTobottom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>" onchange="setDateRange22()" tabindex="7">&nbsp;<script language="JavaScript">new tcal ({"formname": "logbook", "controlname": "dateTobottom"});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show" tabindex="8"><input type="hidden" name="showBottom" value="showbottom"/>&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" tabindex="9"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
	</td></tr>
</tbody></table>
</form>
<?php echo date("d-m-Y, h:i:s",strtotime(now));?>
</body>
</html>