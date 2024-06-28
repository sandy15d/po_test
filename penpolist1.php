<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<script language="javascript" type="text/javascript">
function paging_polist()
{
	window.location="penpolist1.php?pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_polist()
{
	document.getElementById("page").value = 1;
	paging_polist();
}

function previouspage_polist()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_polist();
}

function nextpage_polist()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_polist();
}

function lastpage_polist()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_polist();
}
</script>
</head>

<body>
<?php echo date("d-m-Y, h:i:s");?>
<table align="center" border="0" cellpadding="2" cellspacing="1" width="1000px">
<tbody>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal; color: #CC9933;" >
		<td>Pending Purchase Order List</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #000000;" >
		<td><?php echo "From : ".date("d-m-Y",strtotime($_SESSION['stores_syr']))." To : ".date("d-m-Y",strtotime($_SESSION['stores_eyr']));?></td>
	</tr>
	<tr><td>
		<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%">
		<tbody>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="5%" rowspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">PO No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Date</td>
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none">Party Name</td>
				<td width="20%" valign="middle" style="border-top:none; border-left:none">Order-in-Company</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">PO Value</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Expected Delivery</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-right:none;">Order For</td>
			</tr>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
				<td width="12%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Ordered&nbsp;</td>
				<td width="8%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="20%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Rate / UOM</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Value</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Against Indent</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;">Indent Date</td>
			</tr>
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr = $start;
		
		$sql = mysql_query("SELECT tblpo.*, tblpo_dtm.total_amount, party_name, company_name, location_name FROM tblpo INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN location ON tblpo.delivery_at = location.location_id LEFT OUTER JOIN tblpo_dtm ON tblpo.po_id = tblpo_dtm.po_id WHERE po_status='U' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') ORDER BY po_date, po_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$ctr++;
			$poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
			
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$poNo.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['po_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['party_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['company_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.(($row['total_amount']==NULL || $row['total_amount']==0)?"&nbsp;":$row['total_amount']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['delivery_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="15%">'.$row['location_name'].'</td>';
			echo '</tr>';
			
			$sqlItem = mysql_query("SELECT tblpo_item.*, indent_no, ind_prefix, indent_date, item_name, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE po_id=".$row['po_id']." ORDER BY seq_no") or die(mysql_error());
			while($rowItem=mysql_fetch_array($sqlItem))
			{
				$indent_number = ($rowItem['indent_no']>999 ? $rowItem['indent_no'] : ($rowItem['indent_no']>99 && $rowItem['indent_no']<1000 ? "0".$rowItem['indent_no'] : ($rowItem['indent_no']>9 && $rowItem['indent_no']<100 ? "00".$rowItem['indent_no'] : "000".$rowItem['indent_no'])));
				if($rowItem['ind_prefix']!=null){$indent_number = $rowItem['ind_prefix']."/".$indent_number;}
				$itemvalue = number_format($rowItem['qnty'] * $rowItem['rate'],2,'.','');
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$rowItem['seq_no']."&nbsp;&nbsp;".$rowItem['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="12%" align="right">'.($rowItem['qnty']==0?"&nbsp;":$rowItem['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="8%">&nbsp;'.($rowItem['qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" align="right">'.($rowItem['rate']==0?"&nbsp;":$rowItem['rate']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.$indent_number.'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="15%" align="center">'.date("d-m-Y",strtotime($rowItem['indent_date'])).'</td>';
				echo '</tr>';
			}
		}
		?>
		</tbody></table>
	</td></tr>
	<tr><td align="center"><?php 
		$sql_total=mysql_query("SELECT * FROM tblpo WHERE po_status='U' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')") or die(mysql_error());
		$tot_row=mysql_num_rows($sql_total);
		$total_page=0;
		echo 'Total <span style="color:red">'.$tot_row.'</span> PO.Pending &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_polist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($tot_row>$end){
			echo "Page number: ";
			$total_page=ceil($tot_row/$end);
			echo '<select name="page" id="page" onchange="paging_polist()" style="vertical-align:middle">';
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
			echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_polist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_polist()" />&nbsp;&nbsp;';
		if($total_page>1 && $pg<$total_page)
			echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_polist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_polist()" />';?>
		&nbsp;&nbsp;<input type="button" name="CloseMe" value="Close Window" onclick="javascript:window.close()" />
	</td></tr>
</tbody>
</table>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>