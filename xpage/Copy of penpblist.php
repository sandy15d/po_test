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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Store Management System</title>
<script language="javascript" type="text/javascript">
function paging_pblist(){
	window.location="penpblist.php?pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_pblist(){
	document.getElementById("page").value = 1;
	paging_pblist();
}

function previouspage_pblist(){
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_pblist();
}

function nextpage_pblist(){
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_pblist();
}

function lastpage_pblist(){
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_pblist();
}
</script>
</head>

<body>
<?php echo date("d-m-Y, h:i:s",strtotime(now));?>
<table align="center" border="0" cellpadding="2" cellspacing="1" width="1000px">
<tbody>
	<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
		<td>Pending Purchase Bill List</td>
	</tr>
	<tr><td>
		<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%">
		<tbody>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="5%" rowspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">PO No.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Date</td>
				<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">Party Name</td>
				<td width="15%" colspan="2" valign="middle" style="border-top:none; border-left:none">Order-in-Company</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none">Expected Delivery</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none">Delivery At</td>
			</tr>
			<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
				<td width="20%" colspan="2" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Ordered </td>
				<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none; border-right:none;" align="right">Received </td>
				<td width="5%" valign="middle" style="border-top:none; border-left:none; border-bottom:none" align="left">Qnty.</td>
				<td width="10%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Received Date</td>
				<td width="15%" valign="middle" style="border-top:none; border-left:none; border-bottom:none">Received At</td>
			</tr>
		<?php 
		$start=0;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}
		$ctr_found = "false";
		$ctr = 0;
		$poNo=0;
		
		$sql_po = mysql_db_query(DATABASE2,"SELECT DISTINCT po_id FROM (SELECT table1.* , IFNULL(tblbill_item.bill_id, 0) AS billid FROM (SELECT tblpo.po_id, item_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid =0") or die(mysql_error());
		
		$sql = mysql_db_query(DATABASE2,"SELECT * FROM (SELECT table1.*, receipt_date, receipt_qnty, receivedat, IFNULL(tblbill_item.bill_id,0) AS billid FROM (SELECT tblpo.po_id, po_date, delivery_date, party_name, company_name, location_name AS orderfrom, seq_no, tblpo_item.item_id, item_name, qnty, unit_name FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN ".DATABASE1.".company ON tblpo.company_id = company.company_id INNER JOIN ".DATABASE1.".location ON tblpo.delivery_at = location.location_id INNER JOIN ".DATABASE1.".item ON tblpo_item.item_id = item.item_id INNER JOIN ".DATABASE1.".unit ON item.unit_id = unit.unit_id WHERE order_received = 'Y' ORDER BY po_date, tblpo.po_id, seq_no) AS table1 LEFT OUTER JOIN (SELECT tblreceipt1.receipt_id, receipt_date, po_id, recd_at, location_name AS receivedat, seq_no, item_id, receipt_qnty FROM tblreceipt1 INNER JOIN tblreceipt2 ON tblreceipt1.receipt_id = tblreceipt2.receipt_id INNER JOIN ".DATABASE1.".location ON tblreceipt1.recd_at = location.location_id) AS table2 ON (table1.po_id = table2.po_id AND table1.item_id = table2.item_id) LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid=0 LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			if($row['po_id']==$poNo){
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['receipt_qnty']==0?"&nbsp;":$row['receipt_qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['receipt_qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.date("d/m/Y",strtotime($row['receipt_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.$row['receivedat'].'</td>';
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
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.date("d/m/Y",strtotime($row['po_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['party_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['company_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.date("d/m/Y",strtotime($row['delivery_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['orderfrom'].'</td>';
				echo '</tr>';
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
				echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['seq_no'].")&nbsp;&nbsp;".$row['item_name'].'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['receipt_qnty']==0?"&nbsp;":$row['receipt_qnty']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="5%">'.($row['receipt_qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.date("d/m/Y",strtotime($row['receipt_date'])).'</td>';
				echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.$row['receivedat'].'</td>';
				echo '</tr>';
			}
		}
		
		?>
		</tbody></table></td></tr>
	<tr><td align="center"> <?php 
			$sql_total_po = mysql_db_query(DATABASE2,"SELECT DISTINCT po_id FROM (SELECT table1.* , IFNULL(tblbill_item.bill_id, 0) AS billid FROM (SELECT tblpo.po_id, item_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid =0") or die(mysql_error());
			$sql_total_rec = mysql_db_query(DATABASE2,"SELECT * FROM (SELECT table1.* , IFNULL(tblbill_item.bill_id, 0) AS billid FROM (SELECT tblpo.po_id, item_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid =0") or die(mysql_error());
			$tot_po=mysql_num_rows($sql_total_po);
			$tot_row=mysql_num_rows($sql_total_rec);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_po.'</span> P.O. Pending for Bills &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pblist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" tabindex="1" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pblist()" style="vertical-align:middle">';
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
			if($total_page>1 && $_REQUEST["pg"]>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pblist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pblist()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pg"]<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pblist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pblist()" />';?>
			&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" tabindex="2"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
	</td></tr>
</tbody></table>
<?php echo date("d-m-Y, h:i:s",strtotime(now));?>
</body>
</html>