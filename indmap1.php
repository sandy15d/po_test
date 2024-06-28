<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------*/
$oid = 0;
if(isset($_REQUEST['oid'])){$oid = $_REQUEST['oid'];}
/*--------------------*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
</head>

<body background="images/hbox21.jpg">
    <?php echo date("d-m-Y, h:i:s");?>
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="875px">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                <td colspan="6">Indent Mapping Report</td>
            </tr>
            <?php 
$sql = "SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE tbl_indent.indent_id=".$oid;
$res = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($res);
$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
?>
            <tr align="left"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                <td width="10%">Indent No.</td>
                <td width="2%">:</td>
                <td width="38%"><b><?php echo $indent_number;?></b></td>
                <td width="15%">Indent Date</td>
                <td width="2%">:</td>
                <td width="33%"><b><?php echo date("d-m-Y",strtotime($row['indent_date']));?></b></td>
            </tr>
            <tr align="left"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                <td>Location</td>
                <td>:</td>
                <td><b><?php echo $row['location_name'];?></b></td>
                <td>Required Date</td>
                <td>:</td>
                <td><b><?php echo date("d-m-Y",strtotime($row['supply_date']));?></b></td>
            </tr>
            <tr align="left"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                <td>Order By</td>
                <td>:</td>
                <td><b><?php echo $row['staff_name'];?></b></td>
                <td colspan="3">&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="1500px">
        <tbody>
            <tr bgcolor="#E6E1B0" align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; font-weight:bold; color: #006600; height:20px;">
                <td width="1%" style="border-top:none; border-left:none; border-bottom:none">Sl.No.</td>
                <td width="10%" style="border-top:none; border-left:none">Indent Item</td>
                <td width="5%" style="border-top:none; border-left:none; border-right:none;" align="right">Indent&nbsp;
                </td>
                <td width="2%" style="border-top:none; border-left:none" align="left">Qnty.</td>
                <td width="5%" style="border-top:none; border-left:none; border-right:none;" align="right">
                    Approved&nbsp;</td>
                <td width="2%" style="border-top:none; border-left:none" align="left">Qnty.</td>
                <td width="5%" style="border-top:none; border-left:none; border-right:none;" align="right">Order&nbsp;
                </td>
                <td width="2%" style="border-top:none; border-left:none" align="left">Qnty.</td>
                <td width="4%" style="border-top:none; border-left:none">P.O. No.</td>
                <td width="6%" style="border-top:none; border-left:none">P.O. Date</td>
                <td width="10%" style="border-top:none; border-left:none">Party Name</td>
                <td width="10%" style="border-top:none; border-left:none">Order taken-in Company</td>
                <td width="5%" style="border-top:none; border-left:none; border-right:none;" align="right">
                    Received&nbsp;</td>
                <td width="2%" style="border-top:none; border-left:none" align="left">Qnty.</td>
                <td width="4%" style="border-top:none; border-left:none">Rcpt./Memo No.</td>
                <td width="4%" style="border-top:none; border-left:none">Date</td>
                <td width="5%" style="border-top:none; border-left:none; border-right:none;" align="right">Billing&nbsp;
                </td>
                <td width="2%" style="border-top:none; border-left:none" align="left">Qnty.</td>
                <td width="4%" style="border-top:none; border-left:none">Bill No.</td>
                <td width="4%" style="border-top:none; border-left:none">Bill Date</td>
                <td width="4%" style="border-top:none; border-left:none">Pmnt.Voucher No.</td>
                <td width="4%" style="border-top:none; border-left:none">Voucher Date</td>
            </tr>
            <?php 
$ctr = 0;

$sql = "SELECT tbl_indent_item.*, item_name, unit_name,ic.category FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN item_category ic ON ic.category_id = tbl_indent_item.item_category INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE tbl_indent_item.indent_id=".$oid." ORDER BY seq_no";
$res = mysql_query($sql) or die(mysql_error());
while($row=mysql_fetch_array($res)){
	$ctr++;
	
	echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; font-weight: normal; color:#000000; height:20px;">';
	echo '<td style="border-left:none; border-bottom:none">'.$ctr.'</td>';
	echo '<td style="border-left:none; border-bottom:none">'.$row['item_name'].' ~~'.$row['category'].'</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
	echo '<td style="border-left:none; border-bottom:none">&nbsp;'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">'.($row['aprvd_qnty']==0?"&nbsp;":$row['aprvd_qnty']).'</td>';
	echo '<td style="border-left:none; border-bottom:none">&nbsp;'.($row['aprvd_qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
	
	$sql_po = mysql_query("SELECT tblpo_item.*, tblpo.*, unit_name, party_name, company_name FROM tblpo_item INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id WHERE tblpo.indent_id=".$oid." AND item_id=".$row['item_id']." ORDER BY rec_id") or die(mysql_error());
	$i = 0;
	while($row_po=mysql_fetch_array($sql_po)){
		$po_number = ($row_po['po_no']>999 ? $row_po['po_no'] : ($row_po['po_no']>99 && $row_po['po_no']<1000 ? "0".$row_po['po_no'] : ($row_po['po_no']>9 && $row_po['po_no']<100 ? "00".$row_po['po_no'] : "000".$row_po['po_no'])));
		$i++;
		if($i>1){
			echo '</tr>';
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
		}
		echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">'.($row_po['qnty']==0?"&nbsp;":$row_po['qnty']).'</td>';
		echo '<td style="border-left:none; border-bottom:none">&nbsp;'.($row_po['qnty']==0?"&nbsp;":$row_po['unit_name']).'</td>';
		echo '<td style="border-left:none; border-bottom:none">'.$po_number.'</td>';
		echo '<td style="border-left:none; border-bottom:none">'.date("d-m-Y",strtotime($row_po['po_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none">'.$row_po['party_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none">'.$row_po['company_name'].'</td>';
		
		$sql_rcpt = mysql_query("SELECT tblreceipt2.*, unit_name, receipt_no, receipt_prefix, receipt_date FROM tblreceipt2 INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id INNER JOIN tblreceipt1 ON tblreceipt2.receipt_id = tblreceipt1.receipt_id WHERE po_id=".$row_po['po_id']." AND item_id=".$row['item_id']." ORDER BY rec_id") or die(mysql_error());
		$j = 0;
		while($row_rcpt=mysql_fetch_array($sql_rcpt)){
			$receipt_number = ($row_rcpt['receipt_no']>999 ? $row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>99 && $row_rcpt['receipt_no']<1000 ? "0".$row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>9 && $row_rcpt['receipt_no']<100 ? "00".$row_rcpt['receipt_no'] : "000".$row_rcpt['receipt_no'])));
			if($row_rcpt['receipt_prefix']!=null){$receipt_number = $row_rcpt['receipt_prefix']."/".$receipt_number;}
			$j++;
			if($j>1){
				echo '</tr>';
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; font-weight: normal; color:#000000; height:20px;">';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			}
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">'.($row_rcpt['receipt_qnty']==0?"&nbsp;":$row_rcpt['receipt_qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;'.($row_rcpt['receipt_qnty']==0?"&nbsp;":$row_rcpt['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none">'.$receipt_number.'</td>';
			echo '<td style="border-left:none; border-bottom:none">'.date("d-m-Y",strtotime($row_rcpt['receipt_date'])).'</td>';
		}					// end of while($row_rcpt=mysql_fetch_array($sql_rcpt))
		if($j==0){
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
		}					// end of if($j==0)
	}						// end of while($row_po=mysql_fetch_array($sql_po))
	if($i==0){
		$sql_cash = mysql_query("SELECT tblcash_item.*, unit_name, memo_no, memo_date, particulars, company_name FROM tblcash_item INNER JOIN unit ON tblcash_item.unit_id = unit.unit_id INNER JOIN tblcashmemo ON tblcash_item.txn_id = tblcashmemo.txn_id INNER JOIN company ON tblcashmemo.company_id = company.company_id WHERE indent_id=".$row['indent_id']." AND item_id=".$row['item_id']." ORDER BY rec_id") or die(mysql_error());
		$k = 0;
		while($row_cash=mysql_fetch_array($sql_cash)){
			$k++;
			if($k>1){
				echo '</tr>';
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; font-weight: normal; color:#000000; height:20px;">';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
				echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			}
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">'.$row_cash['company_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">'.($row_cash['memo_qnty']==0?"&nbsp;":$row_cash['memo_qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;'.($row_cash['memo_qnty']==0?"&nbsp;":$row_cash['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none">'.$row_cash['memo_no'].'</td>';
			echo '<td style="border-left:none; border-bottom:none">'.date("d-m-Y",strtotime($row_cash['memo_date'])).'</td>';
		}					// end of while($row_cash=mysql_fetch_array($sql_cash))
		if($k==0){
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" align="right">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none">&nbsp;</td>';
		}				// end of if($k==0)
	}					// end of if($i==0)
	echo '</tr>';
} ?>
            <tr bgcolor="#E6E1B0" align="left"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:20px;">
                <td colspan="22">&nbsp;&nbsp;<input type="button" name="CloseMe" value="Close Window"
                        onclick="javascript:window.close()" /></td>
            </tr>
        </tbody>
    </table>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>