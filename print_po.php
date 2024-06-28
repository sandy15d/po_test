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
//Sql Query for information details of 1st step (screen) display
if(isset($_REQUEST['oid'])){$oid = $_REQUEST['oid'];}
$sql1 = mysql_query("SELECT tblpo.*, party_name, address1, address2, address3, pcity.city_name AS pcityname, pstate.state_name AS pstatename, contact_person, tin, mobile_no, location_name, cmp.company_name AS companyName, cmp.c_address1 AS companyAddress1, cmp.c_address2 AS companyAddress2, cmp.c_address3 AS companyAddress3, cmpcity.city_name AS companyCityName, cmpstate.state_name AS companyStateName, cmp.c_phone AS companyPhone, cmp.c_fax AS companyFax, cmp.c_email AS companyEmail, cmp.c_tin AS companyTIN, cmp.c_cst AS companyCST, ship.company_name AS shippingName, ship.c_address1 AS shipAddress1, ship.c_address2 AS shipAddress2, ship.c_address3 AS shipAddress3, shipcity.city_name AS shipCityName, shipstate.state_name AS shipStateName, ship.c_phone AS shipPhone, cmp.CCode FROM tblpo INNER JOIN company AS cmp ON tblpo.company_id = cmp.company_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN city AS pcity ON party.city_id = pcity.city_id INNER JOIN state AS pstate ON pcity.state_id = pstate.state_id INNER JOIN city AS cmpcity ON cmp.c_cityid = cmpcity.city_id INNER JOIN state AS cmpstate ON cmpcity.state_id = cmpstate.state_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN company AS ship ON tblpo.shipping_id = ship.company_id INNER JOIN city AS shipcity ON ship.c_cityid = shipcity.city_id INNER JOIN state AS shipstate ON shipcity.state_id = shipstate.state_id WHERE po_id=".$oid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<style type="text/css">
<!--
.style1 {
	font-size: 28px;
	font-weight: bold;
	color: #25357E;
}
.style3 {font-size: 14px}
-->
</style>
</head>

<body>
<table width="800" height="1100" border="0" align="center" cellpadding="0" cellspacing="0">
<tr height="10">
	<td width="20%">&nbsp;</td>
	<td width="20%">&nbsp;</td>
	<td width="20%">&nbsp;</td>
	<td width="20%">&nbsp;</td>
	<td width="20%">&nbsp;</td>
	<td width="20%">&nbsp;</td>
</tr>
<tr height="260">
	<td valign="top" colspan="6">
	<table border="0" align="center" cellpadding="0" cellspacing="0">
	<tr height="65" style="background-image:url(images/ubg.png); background-repeat:repeat-x">
		<td width="10">&nbsp;</td>
		<td width="80" style="vertical-align:top"><?php if($row1['CCode']=='VSPL'){?><img height="64" src="images/vnr_logo.png" /><?php }?></td>
		<td width="250">&nbsp;</td>
		<td width="150">&nbsp;</td>
		<td width="10">&nbsp;</td>
		<td width="300" align="left" colspan="3"><span class="style1">PURCHASE ORDER</span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="5" align="left" valign="top"><strong><?php echo strtoupper($row1['companyName']); ?></strong></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo $row1['companyAddress1']; ?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>	
		<td>&nbsp;</td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo $row1['companyAddress2']; ?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>	
		<td>&nbsp;</td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo $row1['companyAddress3']; ?></span></td>
		<td align="right" class="style3">P.O. No.</td>
		<td>:</td>
		<td><strong><?php echo ($row1['po_id']>999 ? $row1['po_id'] : ($row1['po_id']>99 && $row1['po_id']<1000 ? "0".$row1['po_id'] : ($row1['po_id']>9 && $row1['po_id']<100 ? "00".$row1['po_id'] : "000".$row1['po_id'])));?></strong></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo $row1['companyCityName']." (".$row1['companyStateName'].")";?></span></td>
		<td align="right" class="style3">DATE</td>
		<td>:</td>
		<td><?php echo date("d-m-Y",strtotime($row1["po_date"]));?></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo "Tel: ".$row1['companyPhone']."    Fax: ".$row1['companyFax'];?></span></td>
		<td align="right" class="style3">VENDOR ID</td>
		<td>:</td>
		<td><?php echo $row1['party_id'];?></td>
	</tr>	
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo "E-mail: ".$row1['companyEmail'];?></span></td>
		<td align="right" class="style3">VENDOR REF</td>
		<td>:</td>
		<td><?php echo $row1['vendor_ref'];?></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo "TIN No. : ".$row1['companyTIN'];?></span></td>
		<td align="right" class="style3">Vendor's TIN No.</td>
		<td>:</td>
		<td><?php echo $row1['tin'];?></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td colspan="2"><span class="style3"><?php echo "CST No. : ".$row1['companyCST'];?></span></td>
		<td align="right" class="style3">Vendor's CST No.</td>
		<td>:</td>
		<td>&nbsp;</td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td align="left"><strong class="style3">VENDOR : </strong></td>
		<td><strong class="style3"><?php echo $row1['party_name'];?></strong></td>
		<td align="right"><strong class="style3">SHIP TO </strong></td>
		<td>:</td>
		<td><strong class="style3"><?php echo $row1['shippingName'];?></strong></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['address1'];?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['shipAddress1'];?></span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['address2'];?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['shipAddress2'];?></span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['address3'];?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['shipAddress3'];?></span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['pcityname']." (".$row1['pstatename'].")";?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo $row1['shipCityName']." (".$row1['shipStateName'].")";?></span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo "Mob.: ".$row1['mobile_no'];?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo "Tel.: ".$row1['shipPhone'];?></span></td>
	</tr>
	<tr height="13">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><span class="style3"><?php echo "Contact Person: ".$row1['contact_person'];?></span></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" colspan="6">
		<table align="center" cellspacing="0" cellpadding="5" border="1" bordercolorlight="#7ECD7A" width="100%">
		<tr align="center" bgcolor="#FFECEC">
			<td style="border-left:none; border-right:none;"><strong class="style3">SHIPPING METHOD</strong></td>
			<td style="border-right:none;"><strong class="style3">SHIPPING TERMS</strong></td>
			<td style="border-right:none;"><strong class="style3">DELIVERY DATE</strong></td>
		</tr>
		<tr align="center">
			<td style="border-top:none; border-bottom:none; border-left:none; border-right:none;"><?php echo ($row1['ship_method']==null?"Nil":$row1['ship_method']);?></td>
			<td style="border-top:none; border-bottom:none; border-right:none;"><?php echo ($row1['ship_terms']==null ? "Door Delivery" : $row1['ship_terms']);?></td>
			<td style="border-top:none; border-bottom:none; border-right:none;"><?php echo date("d-m-Y",strtotime($row1["delivery_date"]));?></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td valign="top" colspan="6">
		<table align="center" cellspacing="0" cellpadding="0" border="1" bordercolorlight="#7ECD7A" width="100%" style="border-left:none; border-bottom:none; border-right:none;">
		<tr align="center" bgcolor="#C4FFD7">
			<td width="5%" style="border-top:none; border-right:none;"><strong class="style3">S.NO.</strong></td>
			<td width="15%" style="border-top:none; border-right:none;"><strong class="style3">QNTY.</strong></td>
			<td width="35%" style="border-top:none; border-right:none;"><strong class="style3">ITEM DESCRIPTION</strong></td>
			<td width="20%" style="border-top:none; border-right:none;"><strong class="style3">MAKE</strong></td>
			<td width="10%" style="border-top:none; border-right:none;"><strong class="style3">UNIT PRICE</strong></td>
			<td width="15%" style="border-top:none;"><strong class="style3">ITEM TOTAL</strong></td>
		</tr>
		<?php 
		$i = 0;
		$line = 0;
		$item_total = 0;
		$sql2 = mysql_query("SELECT tblpo_item.*, item_name, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE po_id=".$oid." ORDER BY seq_no") or die(mysql_error());
		while($row2=mysql_fetch_array($sql2))
		{
			$cur_qnty =  $row2['qnty'];
			$cur_rate =  $row2['rate'];
			$cur_amount = $cur_qnty * $cur_rate;
			$item_total += $cur_amount;
			
			$i += 1;
			$line += 1;
			echo '<tr height="20" style="font-family:\'Times New Roman\', Times, serif; font-size:14px">';
			echo '<td align="center" style="border-top:none; border-right:none;">'.$i.'.</td>';
			echo '<td align="center" style="border-top:none; border-right:none;">'.$row2['qnty']." ".$row2['unit_name'].'</td>';
			echo '<td style="border-top:none; border-right:none;">'.$row2['item_name'].'</td>';
			echo '<td style="border-top:none; border-right:none;">'.($row2['item_make']==null ? "&nbsp;" : $row2['item_make']).'</td>';
			echo '<td align="right" style="border-top:none; border-right:none;">'.$row2['rate'].'</td>';
			echo '<td align="right" style="border-top:none;">'.number_format($cur_amount,2,".",",").'</td>';
			echo '</tr>';
			if($row2['item_description']!=null){
				$line += 1;
				echo '<tr style="font-family:\'Times New Roman\', Times, serif; font-size:14px">';
				echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
				echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
				echo '<td style="border-top:none; border-right:none;">'.$row2['item_description'].'</td>';
				echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
				echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
				echo '<td align="center" style="border-top:none;">&nbsp;</td>';
				echo '</tr>';
			}
		}
		//process for printing blank lines
		for($j=$line+1; $j<=8; $j++)
		{
			echo '<tr height="20" style="font-family:\'Times New Roman\', Times, serif; font-size:14px">';
			echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
			echo '<td align="center" style="border-top:none; border-right:none;">&nbsp;</td>';
			echo '<td style="border-top:none; border-right:none;">&nbsp;</td>';
			echo '<td style="border-top:none; border-right:none;">&nbsp;</td>';
			echo '<td align="right" style="border-top:none; border-right:none;">&nbsp;</td>';
			echo '<td align="right" style="border-top:none;">&nbsp;</td>';
			echo '</tr>';
		}
		?>
		<tr>
			<td valign="top" colspan="3" style="border-left:none; border-bottom:none; border-right:none;">
			<table cellspacing="0" cellpadding="0" border="0" bordercolorlight="#7ECD7A" width="100%">
			<tr>
				<td><strong class="style3">Terms & Conditions :-</strong></td>
			</tr>
			<tr>
				<td><span class="style3"><?php echo ($row1['terms_condition']==null?"&nbsp;":nl2br($row1['terms_condition']));?></span></td>
			</tr>
			</table></td>
			<td valign="top" colspan="3" style="border-left:none; border-bottom:none; border-right:none;">
			<table cellspacing="0" cellpadding="2" border="1" bordercolorlight="#7ECD7A" width="100%" style="border-left:none; border-top:none; border-bottom:none;">
			<tr>
				<td width="244px" align="right" style="border-left:none; border-top:none; border-bottom:none;"><strong class="style3">SUB-TOTAL:</strong></td>
				<td width="116px" align="right" style="border-left:none; border-top:none; border-right:none;"><strong class="style3"><?php echo number_format($item_total,2,".",","); ?></strong></td>
			</tr>
			<?php 
			$net_total = $item_total;
			$sql3 = mysql_query("SELECT tblpo_dtm.*, dtm_name FROM tblpo_dtm INNER JOIN dtm ON tblpo_dtm.dtm_id = dtm.dtm_id WHERE po_id=".$oid." ORDER BY seq_no") or die(mysql_error());
			while($row3=mysql_fetch_array($sql3))
			{
				if($row3['dtm_percent']==0)
					$dtmname = $row3['dtm_name'];
				else
					$dtmname = $row3['dtm_name']." ".$row3['dtm_percent']."%";
				$net_total = $row3['total_amount'];
				echo '<tr style="font-family:\'Times New Roman\', Times, serif; font-size:14px">';
				echo '<td style="border-left:none; border-bottom:none; border-top:none;">'.$dtmname.'</td>';
				echo '<td align="right" style="border-left:none; border-top:none; border-right:none;">'.number_format($row3['dtm_amount'],2,".",",").'</td>';
				echo '</tr>';
				if($row3['ssat']=="Y"){
					echo '<tr style="font-family:\'Times New Roman\', Times, serif; font-size:14px">';
					echo '<td style="border-left:none; border-bottom:none; border-top:none;">&nbsp;</td>';
					echo '<td align="right" style="border-left:none; border-top:none; border-right:none;">'.number_format($row3['total_amount'],2,".",",").'</td>';
					echo '</tr>';
				}
			} ?>
			<tr>
				<td align="right" style="border-left:none; border-top:none; border-bottom:none;"><strong class="style3">TOTAL:</strong></td>
				<td align="right"style="border-left:none; border-top:none; border-right:none;"><strong class="style3"><?php echo number_format($net_total,2,".",","); ?></strong></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
	<td align="center">Authorized by</td>
	<td>&nbsp;</td>
	<td align="center">_________________</td>
</tr>
<tr>
	<td colspan="5">&nbsp;</td>
	<td align="center"><?php echo "Date ".date("d-m-Y",strtotime($row1["po_date"]));?></td>
</tr>
<tr height="50" style="background-image:url(images/lbg.png); background-repeat:repeat-x; vertical-align:bottom">
	<td colspan="6">&nbsp;</td>
</tr>
</table>
</body>
</html>
