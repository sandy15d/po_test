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
/*--------------------------------*/
$lid = 0;
if(isset($_REQUEST['lid'])){
	$lid = $_REQUEST['lid'];
	$sql = mysql_query("SELECT * FROM location WHERE location_id=".$lid) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$lname = $row['location_name'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<script language="javascript" type="text/javascript">
function paging_pilist()
{
	window.location="penindlist1.php?lid="+document.getElementById("location").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
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
<?php echo date("d-m-Y, h:i:s");?>
<form name="penindlist" method="post">
<table align="center" border="0" cellpadding="2" cellspacing="1" width="1000px">
<tbody>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
		<td>Pending Indent List</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000" >
		<td><?php 
		echo 'Location: ';
		echo '<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$lname.'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" />';
		?>
		</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #000000;" >
		<td><?php echo "From : ".date("d-m-Y",strtotime($_SESSION['stores_syr']))." To : ".date("d-m-Y",strtotime($_SESSION['stores_eyr']));?></td>
	</tr>
	<tr><td>
		<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%">
		<tbody>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="5%" rowspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
				<td width="25%" valign="middle" style="border-top:none; border-left:none">Indent No.</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none">Date</td>
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none">Location</td>
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none">Order By</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none">Supply Date</td>
			</tr>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="40%" colspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
				<td width="12%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Indent&nbsp;</td>
				<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="12%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Approved&nbsp;</td>
				<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Approval Status</td>
			</tr>
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr = $start;
		
		$sql = "SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE ind_status='U' AND order_from=".$lid." AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') ORDER BY location_name, indent_date, indent_id LIMIT ".$start.",".$end;
		$res = mysql_query($sql) or die(mysql_error());
		while($row=mysql_fetch_array($res))
		{
			$ctr++;
			$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
			if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
			
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="25%">'.$indent_number.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.date("d-m-Y",strtotime($row['indent_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['location_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['staff_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.date("d-m-Y",strtotime($row['supply_date'])).'</td>';
			echo '</tr>';
			
			$sqlItem = mysql_query("SELECT tbl_indent_item.*, item_name, unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE indent_id=".$row['indent_id']." ORDER BY seq_no") or die(mysql_error());
			while($rowItem=mysql_fetch_array($sqlItem)){
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="40%" colspan="2">'.$rowItem['seq_no']."&nbsp;&nbsp;".$rowItem['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="12%" align="right">'.($rowItem['qnty']==0?"&nbsp;":$rowItem['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="8%">&nbsp;'.($rowItem['qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="12%" align="right">'.($rowItem['aprvd_qnty']==0?"&nbsp;":$rowItem['aprvd_qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="8%">&nbsp;'.($rowItem['aprvd_qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.($rowItem['aprvd_status']==1?"Yes":"No").'</td>';
				echo '</tr>';
			}
		}
		?>
		</tbody></table>
	</td></tr>
	<tr><td align="center"><?php 
		$sql_total = "SELECT * FROM tbl_indent WHERE ind_status='U' AND order_from=".$lid." AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')";
		$res_total = mysql_query($sql_total) or die(mysql_error());
		$tot_row=mysql_num_rows($res_total);
		$total_page=0;
		echo 'Total <span style="color:red">'.$tot_row.'</span> indent(s) pending &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pilist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($tot_row>$end){
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
		
		echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';
		if($total_page>1 && $pg>1)
			echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pilist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pilist()" />&nbsp;&nbsp;';
		if($total_page>1 && $pg<$total_page)
			echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pilist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pilist()" />';?>
		&nbsp;&nbsp;<input type="button" name="CloseMe" value="Close Window" onclick="javascript:window.close()" />
</tbody>
</table>
</form>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>