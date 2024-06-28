<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------*/
$lid = $_REQUEST['lid'];
$sm = $_REQUEST['sm'];
$em = $_REQUEST['em'];
$itemid = $_REQUEST['iid'];
/*--------------------*/
$lname = "";
$sql = mysql_query("SELECT * FROM location WHERE location_id=".$lid) or die(mysql_error());
if(mysql_num_rows($sql)>0){
	$row = mysql_fetch_assoc($sql);
	$lname = $row['location_name'];
}
/*--------------------*/
$itemName = "";
$unitName = "";
$sql = mysql_query("SELECT item_name,unit_name FROM item INNER JOIN unit ON item.unit_id=unit.unit_id WHERE item_id=".$itemid) or die(mysql_error());
if(mysql_num_rows($sql)>0){
	$row = mysql_fetch_assoc($sql);
	$itemName = $row['item_name'];
	$unitName = $row['unit_name'];
}
/*--------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
} else {
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
}
/*--------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Item Stock Register</title>
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_dateselection()
{
	if(checkdate(document.itemregister.dateFrom)){
		if(checkdate(document.itemregister.dateTo)){
			var no_of_days1 = getDaysbetween2Dates(document.itemregister.dateFrom,document.itemregister.dateTo);
			if(no_of_days1 < 0){
				alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
				return false;
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.itemregister.startYear,document.itemregister.dateFrom);
				if(no_of_days2 < 0){
					alert("* Report From date wrongly selected. Please correct and submit again.\n");
					return false;
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.itemregister.dateTo,document.itemregister.endYear);
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
</script>
</head>

<body background="images/hbox21.jpg">
<?php echo date("d-m-Y, h:i:s");?>
<form name="itemregister" id="itemregister" method="post" onsubmit="return validate_dateselection()">
<table align="center" border="0" cellpadding="2" cellspacing="1" width="975">
<tbody>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933" >
	<td><?php echo $itemName; ?></td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold ; color: #000000" >
	<td>Item Register</td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;" >
	<td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">new tcal ({"formname": "itemregister", "controlname": "dateFrom"});</script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">new tcal ({"formname": "itemregister", "controlname": "dateTo"});</script>&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show" value="show" />&nbsp;&nbsp;<input type="button" name="CloseMe" value="Close Window" onclick="window.close()" style="vertical-align:top;" /><input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/></td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;" >
	<td>Location:&nbsp;<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="<?php echo $lname; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
</tr>
<tr><td>
	<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="5" cellspacing="0" width="100%">
	<tbody>
	<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
		<td width="10%" style="border-top:none; border-left:none; border-bottom:none">Trn.Date</td>
		<td width="30%" style="border-top:none; border-left:none; border-bottom:none">Particulars</td>
		<td width="10%" style="border-top:none; border-left:none; border-bottom:none">Trn.Type</td>
		<td width="5%" style="border-top:none; border-left:none; border-bottom:none">Trn.No.</td>
		<td width="15%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Inwards</td>
		<td width="15%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Outwards</td>
		<td width="15%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Closing</td>
	</tr>
	<?php 
	$cnt=0;
	$opqnty = 0;
	$inwardTotal = 0;
	$outwardTotal = 0;
	$ClosingFinal = 0;
	
	$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty FROM stock_register WHERE item_id=".$itemid." AND location_id=".$lid." AND (entry_date<'".date("Y-m-d",$sm)."' OR (entry_date='".date("Y-m-d",$sm)."' AND entry_mode='O+'))") or die(mysql_error());
	$row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr);
	$opqnty = $row_stk_rgstr['qty'];
	$closing = $row_stk_rgstr['qty'];
	$closingFinal = $closing;
	
	echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
	$cnt = 1;
	echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",$sm).'</td>';
	echo '<td style="border-left:none; border-bottom:none" width="30%">Opening Balance</td>';
	echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($opqnty==0?"&nbsp;":number_format($opqnty,3,".","")).'</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">'.($opqnty==0? "&nbsp;" : $unitName).'</td>';
	echo '</tr>';
	
	$sql_stk_rgstr = mysql_query("SELECT * FROM stock_register WHERE item_id=".$itemid." AND location_id=".$lid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') AND entry_mode!='O+' ORDER BY entry_date") or die(mysql_error());
	while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
		$particulars = "&nbsp;";
		$trnType = "";
		$trnNumber = "";
		$inward = 0;
		$outward = 0;
		
		$trnDate = date("d-m-Y",strtotime($row_stk_rgstr['entry_date']));
		if($row_stk_rgstr['entry_mode']=="R+"){
			$trnType = "Mtrl.Receipt";
			$inward = $row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT party_name FROM tblreceipt1 INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id WHERE receipt_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? $row['party_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="R-"){
			$trnType = "ReceiptReturn";
			$outward = 0-$row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT party_name FROM tblreceipt_return1 INNER JOIN tblreceipt1 ON tblreceipt_return1.receipt_id = tblreceipt1.receipt_id INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id WHERE return_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? $row['party_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="I+"){
			$trnType = "Mtrl.Issue";
			$outward = 0-$row_stk_rgstr['item_qnty'];
//			$sql = mysql_query("SELECT leader_name FROM tblissue1 INNER JOIN leader ON tblissue1.issue_to=leader.leader_id WHERE issue_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$sql = mysql_query("SELECT plot_name FROM tblissue2 INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id=".$row_stk_rgstr['entry_id']." AND item_id=".$itemid) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "To :Plot No. ".$row['plot_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="I-"){
			$trnType = "IssueReturn";
			$inward = $row_stk_rgstr['item_qnty'];
//			$sql = mysql_query("SELECT leader_name FROM tblissue1 INNER JOIN leader ON tblissue1.issue_to=leader.leader_id WHERE issue_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$sql = mysql_query("SELECT plot_name FROM tblissue2 INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id=".$row_stk_rgstr['entry_id']." AND item_id=".$itemid) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "From :".$row['plot_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="T+"){
			$trnType = "ILT Receipt";
			$inward = $row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT location_name FROM tblilt1 INNER JOIN location ON tblilt1.despatch_from = location.location_id WHERE ilt_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "From :".$row['location_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="T-"){
			$trnType = "ILT Despatch";
			$outward = 0-$row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT location_name FROM tblilt1 INNER JOIN location ON tblilt1.receive_at = location.location_id WHERE ilt_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "To :".$row['location_name'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="X+"){
			$trnType = "XLT Receipt";
			$inward = $row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT tfr_location FROM tblxlt WHERE xlt_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "From :".$row['tfr_location'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="X-"){
			$trnType = "XLT Despatch";
			$outward = 0-$row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT tfr_location FROM tblxlt WHERE xlt_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "To :".$row['tfr_location'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="C+"){
			$trnType = "Cash Pur.";
			$inward = $row_stk_rgstr['item_qnty'];
			$sql = mysql_query("SELECT particulars FROM tblcashmemo WHERE txn_id=".$row_stk_rgstr['entry_id']) or die(mysql_error());
			$row = mysql_fetch_assoc($sql);
			$particulars = (mysql_num_rows($sql)>0 ? "From :".$row['particulars'] : "&nbsp;");
		} elseif($row_stk_rgstr['entry_mode']=="P+"){
			$trnType = "Physical Stock";
			$inward = $row_stk_rgstr['item_qnty'];
			$particulars = "Physical Verification&nbsp;";
		} elseif($row_stk_rgstr['entry_mode']=="P-"){
			$trnType = "Physical Stock";
			$outward = 0-$row_stk_rgstr['item_qnty'];
			$particulars = "Physical Verification&nbsp;";
		}
		$closing += $inward - $outward;
		$inwardTotal += $inward;
		$outwardTotal += $outward;
		$closingFinal = $closing;
		
		$trnNumber = ($row_stk_rgstr['entry_id']>999 ? $row_stk_rgstr['entry_id'] : ($row_stk_rgstr['entry_id']>99 && $row_stk_rgstr['entry_id']<1000 ? "0".$row_stk_rgstr['entry_id'] : ($row_stk_rgstr['entry_id']>9 && $row_stk_rgstr['entry_id']<100 ? "00".$row_stk_rgstr['entry_id'] : "000".$row_stk_rgstr['entry_id'])));
		
		if($cnt==1){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 0;
		} else {
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 1;
		}
		echo '<td style="border-left:none; border-bottom:none;" width="10%">'.$trnDate.'</td>';
		echo '<td style="border-left:none; border-bottom:none;" width="30%">'.$particulars.'</a></td>';
		echo '<td style="border-left:none; border-bottom:none;" width="10%">'.$trnType.'</td>';
		echo '<td style="border-left:none; border-bottom:none;" width="5%">'.$trnNumber.'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($inward==0? "&nbsp;" : number_format($inward,3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" width="5%">'.($inward==0? "&nbsp;" : $unitName).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($outward==0? "&nbsp;" : number_format($outward,3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.($outward==0? "&nbsp;" : $unitName).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($closing==0? "&nbsp;" : number_format($closing,3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.($closing==0? "&nbsp;" : $unitName).'</td>';
		echo '</tr>';
	}
	echo '<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">';
	echo '<td width="10%" style="border-left:none; border-right:none">&nbsp;</td>';
	echo '<td width="30%" style="border-left:none; border-right:none">Totals :</td>';
	echo '<td width="10%" style="border-left:none; border-right:none">&nbsp;</td>';
	echo '<td width="5%" style="border-left:none;">&nbsp;</td>';
	echo '<td width="10%" style="border-left:none; border-right:none" align="right">'.($inwardTotal==0? "&nbsp;" : number_format($inwardTotal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($inwardTotal==0? "&nbsp;" : $unitName).'</td>';
	echo '<td width="10%" style="border-left:none; border-right:none" align="right">'.($outwardTotal==0? "&nbsp;" : number_format($outwardTotal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($outwardTotal==0? "&nbsp;" : $unitName).'</td>';
	echo '<td width="10%" style="border-left:none; border-right:none" align="right">'.($closingFinal==0? "&nbsp;" : number_format($closingFinal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($closingFinal==0? "&nbsp;" : $unitName).'</td>';
	echo '</tr>';
	?>
	</tbody></table>
</td></tr>
</tbody>
</table>
</form>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>