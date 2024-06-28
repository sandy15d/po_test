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
function paging_polist()
{
	window.location="penmrlist1.php?lid="+document.getElementById("location").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
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
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
		<td>Pending Delivery List</td>
	</tr>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000" >
		<td><?php 
		echo 'Location: ';
		echo '<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$lname.'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" />';
		?>
		</td>
	</tr>
	<tr><td>
		<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%">
		<tbody>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="5%" rowspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">PO No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Date</td>
				<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">Party Name</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none">Order-in-Company</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">PO Value</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Expected Delivery</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none">Delivery At</td>
				<td width="5%" valign="middle" style="border-top:none; border-left:none; border-right:none;">PO-2-Party</td>
			</tr>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Ordered </td>
				<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Rate / UOM</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Value</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Against Indent</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Order Booked By</td>
				<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;">Order Status</td>
			</tr>
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr_found = "false";
		$ctr = 0;
		$poNo=0;
		
		$sql_po = mysql_query("SELECT Distinct tblpo.po_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE delivery_at=".$lid." AND po_status='S' AND order_received='N' ORDER BY po_date, tblpo.po_id, tblpo_item.seq_no") or die(mysql_error());
		$sql = mysql_query("SELECT tblpo.*, tblpo_item.*, tblpo_dtm.total_amount, party_name, company_name, location_name, item_name, unit_name, user_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id LEFT OUTER JOIN tblpo_dtm ON tblpo.po_id = tblpo_dtm.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id INNER JOIN users ON tblpo_item.uid = users.uid WHERE delivery_at=".$lid." AND po_status='S' AND order_received='N' ORDER BY po_date, tblpo.po_id, tblpo_item.seq_no LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$indentNo = ($row['indent_id']>999 ? $row['indent_id'] : ($row['indent_id']>99 && $row['indent_id']<1000 ? "0".$row['indent_id'] : ($row['indent_id']>9 && $row['indent_id']<100 ? "00".$row['indent_id'] : "000".$row['indent_id'])));
			$itemvalue = number_format($row['qnty'] * $row['rate'],2,'.','');
			if($row['po_id']==$poNo){
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="right">'.($row['rate']==0?"&nbsp;":$row['rate']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.$indentNo.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.$row['user_id'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="5%" align="center">'.($row['order_received']=="Y"?"Received":"Not Received").'</td>';
				echo '</tr>';
			} elseif($row['po_id']!=$poNo){
				$poNo = ($row['po_id']>999 ? $row['po_id'] : ($row['po_id']>99 && $row['po_id']<1000 ? "0".$row['po_id'] : ($row['po_id']>9 && $row['po_id']<100 ? "00".$row['po_id'] : "000".$row['po_id'])));
				$ctr += 1;
				if($ctr_found=="false"){
					while($row_po=mysql_fetch_array($sql_po)){
						if($row_po['po_id']==$row['po_id']){
							$ctr_found="true";
							break;
						}
						$ctr += 1;
					}
				}
				
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.$poNo.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['po_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['party_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['company_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.(($row['total_amount']==NULL || $row['total_amount']==0)?"&nbsp;":$row['total_amount']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['delivery_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['location_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="5%">'.($row['po_status']=="S"?"Send":"Unsend").'</td>';
				echo '</tr>';
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="right">'.($row['rate']==0?"&nbsp;":$row['rate']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.$indentNo.'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.$row['user_id'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="5%" align="center">'.($row['order_received']=="Y"?"Received":"Not Received").'</td>';
				echo '</tr>';
			}
		}
		
		?>
		</tbody></table></td></tr>
	<tr><td align="center"> <?php 
			$sql_total_po = mysql_query("SELECT Distinct tblpo.po_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE delivery_at=".$lid." AND po_status='S' AND order_received='N'") or die(mysql_error());
			$sql_total_rec = mysql_query("SELECT * FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE delivery_at=".$lid." AND po_status='S' AND order_received='N'") or die(mysql_error());
			$tot_po=mysql_num_rows($sql_total_po);
			$tot_row=mysql_num_rows($sql_total_rec);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_po.'</span> PO.Pending &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_polist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
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
			&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
	</td></tr>
</tbody></table>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>