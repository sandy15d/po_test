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
$period_from_to_date = 'From: '.date("d-m-Y",$sm).'&nbsp;To :&nbsp;'.date("d-m-Y",$em);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Monthly Stock</title>
</head>

<body background="images/hbox21.jpg">
<?php echo date("d-m-Y, h:i:s");?>
<table align="center" border="0" cellpadding="2" cellspacing="1" width="850px">
<tbody>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal; color: #CC9933;" >
	<td><?php echo $itemName; ?></td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; color: #000000;" >
	<td>Monthly Summary</td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #0000FF; height:25px;" >
	<td><?php echo $period_from_to_date; ?></td>
</tr>
<tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; color: #000000; height:25px;" >
	<td>Location:&nbsp;<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="<?php echo $lname; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
</tr>
<tr><td>
	<table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="5" cellspacing="0" width="100%">
	<tbody>
	<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
		<td width="25%" style="border-top:none; border-left:none; border-bottom:none">Particulars</td>
		<td width="25%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Inwards</td>
		<td width="25%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Outwards</td>
		<td width="25%" style="border-top:none; border-left:none; border-bottom:none" colspan="2">Closing Balance</td>
	</tr>
	<?php 
	$cnt = 0;
	$mthName = array("April","May","June","July","August","September","October","November","December","January","February","March");
	$inward = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$outward = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$closing = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$year1 = date("Y",strtotime($_SESSION['stores_syr']));
	$year2 = date("Y",strtotime($_SESSION['stores_eyr']));
	$mthStart = array(mktime(0, 0, 0, 4, 1, $year1), mktime(0, 0, 0, 5, 1, $year1), mktime(0, 0, 0, 6, 1, $year1), mktime(0, 0, 0, 7, 1, $year1), mktime(0, 0, 0, 8, 1, $year1), mktime(0, 0, 0, 9, 1, $year1), mktime(0, 0, 0, 10, 1, $year1), mktime(0, 0, 0, 11, 1, $year1), mktime(0, 0, 0, 12, 1, $year1), mktime(0, 0, 0, 1, 1, $year2), mktime(0, 0, 0, 2, 1, $year2), mktime(0, 0, 0, 3, 1, $year2));
	$mthEnd = array(mktime(0, 0, 0, 4, 30, $year1), mktime(0, 0, 0, 5, 31, $year1), mktime(0, 0, 0, 6, 30, $year1), mktime(0, 0, 0, 7, 31, $year1), mktime(0, 0, 0, 8, 31, $year1), mktime(0, 0, 0, 9, 30, $year1), mktime(0, 0, 0, 10, 31, $year1), mktime(0, 0, 0, 11, 30, $year1), mktime(0, 0, 0, 12, 31, $year1), mktime(0, 0, 0, 1, 31, $year2), mktime(0, 0, 0, 3, 0, $year2), mktime(0, 0, 0, 3, 31, $year2));
	
	$opqnty = 0;
	$inwardTotal = 0;
	$outwardTotal = 0;
	$ClosingFinal = 0;
	
	$sql_opstk = mysql_query("SELECT Sum(item_qnty) AS qty FROM stock_register WHERE item_id=".$itemid." AND location_id=".$lid." AND (entry_date<'".date("Y-m-d",$sm)."' OR (entry_date='".date("Y-m-d",$sm)."' AND entry_mode='O+'))") or die(mysql_error());
	if(mysql_num_rows($sql_opstk)>0){
		$row_opstk = mysql_fetch_assoc($sql_opstk);
		$opqnty = $row_opstk['qty'];
	}
	echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
	$cnt = 1;
	echo '<td style="border-left:none; border-bottom:none" width="25%">Opening Balance</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
	echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">'.($opqnty==0?"&nbsp;":number_format($opqnty,3,".","")).'</td>';
	echo '<td style="border-left:none; border-bottom:none" width="5%">'.($opqnty==0? "&nbsp;" : $unitName).'</td>';
	echo '</tr>';
	
	$sql_stk_rgstr = mysql_query("SELECT entry_mode, Month(entry_date) AS entry_month, Sum(item_qnty) AS qty FROM stock_register WHERE item_id=".$itemid." AND location_id=".$lid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') GROUP BY entry_mode, Month(entry_date) ORDER BY entry_month") or die(mysql_error());
	while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
		if($row_stk_rgstr['entry_month']>=4 && $row_stk_rgstr['entry_month']<=12)
			$i = $row_stk_rgstr['entry_month'] - 4;
		elseif($row_stk_rgstr['entry_month']>=1 && $row_stk_rgstr['entry_month']<=3)
			$i = $row_stk_rgstr['entry_month'] + 8;
		if($row_stk_rgstr['entry_mode']=='R+' || $row_stk_rgstr['entry_mode']=='I-' || $row_stk_rgstr['entry_mode']=='T+' || $row_stk_rgstr['entry_mode']=='X+' || $row_stk_rgstr['entry_mode']=='P+' || $row_stk_rgstr['entry_mode']=='C+'){
			$inward[$i] += $row_stk_rgstr['qty'];
		} elseif($row_stk_rgstr['entry_mode']=='R-' || $row_stk_rgstr['entry_mode']=='I+' || $row_stk_rgstr['entry_mode']=='T-' || $row_stk_rgstr['entry_mode']=='X-' || $row_stk_rgstr['entry_mode']=='P-' ){
			$outward[$i] += (0-$row_stk_rgstr['qty']);
		}
	}
	
	$minMonth = date("m",$sm);
	$maxMonth = date("m",$em);
	if($minMonth>=4 && $minMonth<=12){$minMonth -= 4;} elseif($minMonth>=1 && $minMonth<=3){$minMonth += 8;}
	if($maxMonth>=4 && $maxMonth<=12){$maxMonth -= 4;} elseif($maxMonth>=1 && $maxMonth<=3){$maxMonth += 8;}
	for($i=$minMonth; $i<=$maxMonth; $i++){
		$inwardTotal += $inward[$i];
		$outwardTotal += $outward[$i];
		$closing[$i] = $opqnty + $inwardTotal - $outwardTotal;
		$closingFinal = $closing[$i];
		
		if($cnt==1){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 0;
		} else {
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 1;
		}
		//$x = "itemregister.php?lid=".$lid."&gid=".$gid."&iid=".$iid."&iid1=".$itemid."&flt=".$flt."&sm=".$sm."&em=".$em;
		$x = "window.open('itemregister.php?lid=$lid&iid=$itemid&sm=$mthStart[$i]&em=$mthEnd[$i]', 'itemregister', 'width=1075, height=650, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no, copyhistory=no')";
		echo '<td style="border-left:none; border-bottom:none;" width="25%"><a onclick="'.$x.'" style="display:inline; cursor:hand; text-decoration:none; color:#0000FF;">'.$mthName[$i].'</a></td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">'.($inward[$i]==0? "&nbsp;" : number_format($inward[$i],3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" width="5%">'.($inward[$i]==0? "&nbsp;" : $unitName).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">'.($outward[$i]==0? "&nbsp;" : number_format($outward[$i],3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.($outward[$i]==0? "&nbsp;" : $unitName).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" align="right">'.($closing[$i]==0? "&nbsp;" : number_format($closing[$i],3,".","")).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.($closing[$i]==0? "&nbsp;" : $unitName).'</td>';
		echo '</tr>';
	}
	echo '<tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">';
	echo '<td width="25%" style="border-left:none;">Grand Total</td>';
	echo '<td width="20%" style="border-left:none; border-right:none" align="right">'.($inwardTotal==0? "&nbsp;" : number_format($inwardTotal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($inwardTotal==0? "&nbsp;" : $unitName).'</td>';
	echo '<td width="20%" style="border-left:none; border-right:none" align="right">'.($outwardTotal==0? "&nbsp;" : number_format($outwardTotal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($outwardTotal==0? "&nbsp;" : $unitName).'</td>';
	echo '<td width="20%" style="border-left:none; border-right:none" align="right">'.($closingFinal==0? "&nbsp;" : number_format($closingFinal,3,".","")).'</td>';
	echo '<td width="5%" style="border-left:none;">'.($closingFinal==0? "&nbsp;" : $unitName).'</td>';
	echo '</tr>';
	?>
	</tbody></table>
</td></tr>
<tr><td align="right">&nbsp;&nbsp;<input type="button" name="CloseMe" value="Close Window" onclick="javascript:window.close()" /></td></tr>
</tbody>
</table>
<?php echo date("d-m-Y, h:i:s");?>
</body>
</html>