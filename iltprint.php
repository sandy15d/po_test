<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*-------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
</head>

<body>
<table align="center" border="0" cellspacing="0" style="width:800px; font-family:Verdana, Arial, Helvetica, sans-serif;">
<tr><td>
	<table style="width:70px;" align="left" border="0" cellspacing="0">
	<tr><td><img src="images/vnr_logo.png" style="display:inline;cursor:hand;" border="0" /></td></tr>
	</table>
	<table style="width:730px;" align="left" border="0" cellspacing="0">
	<tr><td align="left" style="font-size:22px; font-weight:bold;">&nbsp;</td></tr>
	<tr><td align="left" style="font-size:8px;">&nbsp;</td></tr>
	</table>
</td></tr>
<tr><td>
	<table style="width:800px;" align="center" border="1" cellspacing="0">
	<tr style="font-size:14px; font-weight:bold;"><td align="center" colspan="4">Inter Location Transfer (ILT)</td></tr>
<?php 
$sqlVouch = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, staff_name FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id WHERE ilt_id=".$_REQUEST['v']) or die(mysql_error());
$resVouch=mysql_fetch_assoc($sqlVouch);
$vouch_no = $resVouch['ilt_no'];
$VoucherNumber = ($vouch_no>999 ? $vouch_no : ($vouch_no>99 && $vouch_no<1000 ? "0".$vouch_no : ($vouch_no>9 && $vouch_no<100 ? "00".$vouch_no : "000".$vouch_no)));
if($resVouch['ilt_prefix']!=NULL){$VoucherNumber = $resVouch['ilt_prefix']."/".$VoucherNumber;}
if($resVouch['despatch_mode']=="1"){
	$dispMode = "Hand Delivery";
	$vehicleNumber = "&nbsp;";
} elseif($resVouch['despatch_mode']=="2"){
	$dispMode = "By Vehicle";
	$vehicleNumber = $resVouch['vehicle_num'];
}
?>
	<tr style="font-size:12px;"><td width="150px" align="right">ILT No. :</td>
		<td width="250px"><?php echo $VoucherNumber;?></td>
		<td width="150px" align="right">ILT Date :</td>
		<td width="250px"><?php echo date("d-m-Y",strtotime($resVouch['ilt_date']));?></td>
	</tr>
	<tr style="font-size:12px;"><td align="right">Dispatch From :</td>
		<td><?php echo $resVouch['sourceLocation'];?></td>
		<td align="right">To :</td>
		<td><?php echo $resVouch['destinationLocation'];?></td>
	</tr>
	<tr style="font-size:12px;"><td align="right">Dispatch By :</td>
		<td><?php echo $resVouch['staff_name'];?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr style="font-size:12px;"><td align="right">Dispatch Mode :</td>
		<td><?php echo $dispMode;?></td>
		<td align="right">Vehicle No. :</td>
		<td><?php echo $vehicleNumber;?></td>
	</tr>
	</table>
</td></tr>
<tr style="font-size:12px;"><td>&nbsp;</td></tr>
<tr style="font-size:12px;"><td>We acknowledge the receipt of following materials:
</td></tr>
<tr><td>
	<table style="width:800px; font-size:12px;" align="center" border="1" cellspacing="0">
	<tr><td width="10px" align="center">Sl.No.</td>
		<td width="490px" align="center">Item Name</td>
		<td width="150px" align="center">Dispatch Qnty.</td>
		<td width="150px" align="center">Receipt Qnty.</td>
	</tr>
<?php 
$sql_item = mysql_query("SELECT tblilt2.*, item_name, unit_name FROM tblilt2 INNER JOIN item ON tblilt2.item_id = item.item_id INNER JOIN unit ON tblilt2.unit_id = unit.unit_id WHERE ilt_id=".$_REQUEST['v']." ORDER BY seq_no") or die(mysql_error());
$j = 0;
while($row_item=mysql_fetch_array($sql_item)){
	$j++;
	echo '<tr>';
	echo '<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td align="right">'.$row_item['despatch_qnty'].' '.$row_item['unit_name'].'</td><td>&nbsp;</td>';
	echo '</tr>';
}?>
	</table>
</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>
	<table style="width:800px; font-size:12px;" align="center" border="0" cellspacing="0">
	<tr><td width="270px" align="center">Prepared By</td>
		<td width="270px" align="center">Verified By</td>
		<td width="260px" align="center">Received By</td>
	</tr>
	</table>
</td></tr>
</table>
</body>
</html>
<script>window.print();window.onfocus = function() { window.close(); }</script>