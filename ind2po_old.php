<?php 
include("menu.php");
/*--------------------*/
$lid = 0;
if(isset($_REQUEST['lid'])){$lid = $_REQUEST['lid'];}
/*--------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$lid = $_POST['location'];
} elseif(isset($_REQUEST['sm'])){
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
} else {
	$sm = strtotime(date("Y-m-d"));
	$em = strtotime(date("Y-m-d"));
}
/*--------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_dateselection()
{
	if(checkdate(document.indentlist.dateFrom)){
		if(checkdate(document.indentlist.dateTo)){
			var no_of_days1 = getDaysbetween2Dates(document.indentlist.dateFrom,document.indentlist.dateTo);
			if(no_of_days1 < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.indentlist.startYear,document.indentlist.dateFrom);
				if(no_of_days2 < 0){
					alert("* Report From date wrongly selected. Please correct and submit again.\n");
					return false;
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.indentlist.dateTo,document.indentlist.endYear);
					if(no_of_days3 < 0){
						alert("* Report To date wrongly selected. Please correct and submit again.\n");
						return false;
					} else {
						return true;
					}
				}
			}
		}
	}
}

function paging_pilist()
{
	window.location="ind2po.php?lid="+document.getElementById("location").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+"&sm="+document.getElementById("date1").value+"&em="+document.getElementById("date2").value;
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
function funPrint(){
   var var1=document.getElementById("printTable").outerHTML;
var varOpen=window.open("");
varOpen.window.document.write(var1);
varOpen.print();
varOpen.close();;
}
</script>
</head>

<body>
<?php echo date("d-m-Y, h:i:s");?>
<form name="indentlist" id="indentlist" method="post" action="ind2po.php" onsubmit="return validate_dateselection()">
<table align="center" border="0" cellpadding="2" cellspacing="1" width="1175px">
<tbody>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
	<td>Indent v/s Purchase Order Report</td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;" >
	<td><?php if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
		echo 'Select Location: ';
		echo '<select name="location" id="location" style="width:200px" >';
		echo '<option value="0">All Locations</option>';
		$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
		while($row_location=mysql_fetch_array($sql_location)){
			if($row_location["location_id"]==$lid)
				echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
			else
				echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
		}
		echo '</select>';
	} elseif($_SESSION['stores_utype']=="U"){
		$lid = $_SESSION['stores_locid'];
		echo 'Location: ';
		echo '<input name="locationName" id="locationName" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" />';
	}?>
	</td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;" >
    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">new tcal ({"formname": "indentlist", "controlname": "dateFrom"});</script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">new tcal ({"formname": "indentlist", "controlname": "dateTo"});</script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show" value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" onclick="window.location='menu.php'" /><input type="image" src="images/print.gif" onclick="funPrint()"/><input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/></td>
</tr>
<tr><td>
	<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" id="printTable" cellspacing="0" width="100%">
	<tbody>
	<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:20px;">
		<td width="5%" rowspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
		<td width="10%" valign="middle" style="border-top:none; border-left:none">Indent No.</td>
		<td width="5%" valign="middle" style="border-top:none; border-left:none">Date</td>
		<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">Location</td>
		<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">Order By</td>
		<td width="5%" valign="middle" style="border-top:none; border-left:none">Required Date</td>
		<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">&nbsp;</td>
		<td width="10%" colspan="2" valign="middle" style="border-top:none; border-left:none">&nbsp;</td>
		<td width="10%" valign="middle" style="border-top:none; border-left:none">&nbsp;</td>
		<td width="10%" valign="middle" style="border-top:none; border-left:none">&nbsp;</td>
                <td width="10%" valign="middle" style="border-top:none; border-left:none">&nbsp;</td>
	</tr>
	<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:20px;">
		<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
		<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Indent&nbsp;</td>
		<td width="7%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
		<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Approved&nbsp;</td>
		<td width="7%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
		<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Approval Status</td>
		<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Order&nbsp;</td>
		<td width="7%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
		<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">P.O. No.</td>
		<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Date</td>
		<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Party Name</td>
		<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Company Name</td>
                <td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Generate PO</td>
	</tr>
	<?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	
	$sql = "SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE ind_status='S' AND (indent_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql .= " AND order_from=".$lid;
	}
	$sql .= " ORDER BY location_name, indent_date, indent_id LIMIT ".$start.",".$end;
	$res = mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($res)){
		$ctr++;
		$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
		if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
		
		echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: bold; color:#000000; height:20px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.$indent_number.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.date("d-m-y",strtotime($row['indent_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['location_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['staff_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.date("d-m-y",strtotime($row['supply_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">&nbsp;</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%" colspan="2">&nbsp;</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
                echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
		echo '</tr>';
		
		$sqlItem = mysql_query("SELECT tbl_indent_item.*, item_name, unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE indent_id=".$row['indent_id']." ORDER BY seq_no") or die(mysql_error());
		while($rowItem=mysql_fetch_array($sqlItem)){
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$rowItem['item_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">'.($rowItem['qnty']==0?"&nbsp;":$rowItem['qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;'.($rowItem['qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">'.($rowItem['aprvd_qnty']==0?"&nbsp;":$rowItem['aprvd_qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;'.($rowItem['aprvd_qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="5%" align="center">'.($rowItem['aprvd_status']==1?"Yes":"No").'</td>';
			
			$sql_po = mysql_query("SELECT tblpo_item.*, tblpo.*, unit_name, party_name, company_name FROM tblpo_item INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id WHERE indent_id=".$row['indent_id']." AND item_id=".$rowItem['item_id']." ORDER BY rec_id") or die(mysql_error());
			$i = 0; $j=1;
			while($row_po=mysql_fetch_array($sql_po)){
				$po_number = ($row_po['po_no']>999 ? $row_po['po_no'] : ($row_po['po_no']>99 && $row_po['po_no']<1000 ? "0".$row_po['po_no'] : ($row_po['po_no']>9 && $row_po['po_no']<100 ? "00".$row_po['po_no'] : "000".$row_po['po_no'])));
				$i++;
				if($i>1){
					echo '</tr>';
					echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:20px;">';
					echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;</td>';
					echo '<td style="border-left:none; border-bottom:none" width="5%" align="center">&nbsp;</td>';
                                        
				}
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">'.($row_po['qnty']==0?"&nbsp;":$row_po['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;'.($row_po['qnty']==0?"&nbsp;":$row_po['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.$po_number.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.date("d-m-y",strtotime($row_po['po_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.$row_po['party_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.$row_po['company_name'].'</td>';
                                echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">';   
if($j>=1){ echo '<a href="#" onclick=window.open("newpurchaseorder.php?po_id='.$row_po['po_id'].'","_blank","scrollbars=yes,resizable=yes,width=800,height=600") style="font-size:12px;" >click</a>';  } echo '</td>'; 
               
$j++;
                                
			}						// end of while($row_po=mysql_fetch_array($sql_po))
			if($i==0){
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="8%" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
			}
			echo '</tr>'; 
		}							// end of while($rowItem=mysql_fetch_array($sqlItem))
	}								// end of while($row=mysql_fetch_array($res))
	?>
	</tbody></table>
	</td>
</tr>
<tr><td align="center"><?php 
	$sql_total = "SELECT * FROM tbl_indent WHERE ind_status='S' AND (indent_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql_total .= " AND order_from=".$lid;
	}
	$res_total = mysql_query($sql_total) or die(mysql_error());
	$tot_row=mysql_num_rows($res_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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
	
	echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="date1" id="date1" value="'.$sm.'" /><input type="hidden" name="date2" id="date2" value="'.$em.'" />';
	if($total_page>1 && $pg>1)
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pilist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pilist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pilist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pilist()" />'; ?>
	</td>
</tr>
</tbody>
</table>
</form>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>
