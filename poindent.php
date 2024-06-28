<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
$oid = $_REQUEST['oid'];
$sql1 = mysql_query("SELECT tblpo.*, company_name, party_name, address1, address2, address3, city_name, state_name, contact_person, tin, location_name FROM tblpo INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN location ON tblpo.delivery_at = location.location_id WHERE po_id=".$oid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$po_no = ($row1['po_no']>999 ? $row1['po_no'] : ($row1['po_no']>99 && $row1['po_no']<1000 ? "0".$row1['po_no'] : ($row1['po_no']>9 && $row1['po_no']<100 ? "00".$row1['po_no'] : "000".$row1['po_no'])));
$po_date = date("d-m-Y",strtotime($row1["po_date"]));
$delivery_date = date("d-m-Y",strtotime($row1["delivery_date"]));
$location_name = $row1['location_name'];
$party_name = $row1['party_name'];
$company_name = $row1['company_name'];
$address1 = $row1["address1"];
$address2 = $row1["address2"];
$address3 = $row1["address3"];
$city_name = $row1["city_name"];
$state_name = $row1["state_name"];
$shipto = $row1['shipto'];
$contact_person = $row1["contact_person"];
$tin = $row1["tin"];
$vendor_ref = $row1["vendor_ref"];
$ship_method = $row1["ship_method"];
$ship_terms = $row1["ship_terms"];
$terms_condition = $row1["terms_condition"];
/*--------------------------------*/
if($shipto==1 || $shipto==2){
	$sql2 = mysql_query("SELECT company_name AS ship_name, c_address1 AS ship_address1, c_address2 AS ship_address2, c_address3 AS ship_address3, city_name, state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$row1['shipping_id']) or die(mysql_error());
} elseif($shipto==3) {
	$sql2 = mysql_query("SELECT party_name AS ship_name, address1 AS ship_address1, address2 AS ship_address2, address3 AS ship_address3, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$row1['party_id']) or die(mysql_error());
}
$row2 = mysql_fetch_assoc($sql2);
$ship_name = $row2["ship_name"];
$ship_address1 = $row2["ship_address1"];
$ship_address2 = $row2["ship_address2"];
$ship_address3 = $row2["ship_address3"];
$ship_city_name = $row2["ship_city_name"];
$ship_state_name = $row2["ship_state_name"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/tigra_hints.js"></script>
<script language="javascript" type="text/javascript">
function paging_poind()
{
	window.location="poindent.php?action="+document.getElementById("xson").value+"&oid="+document.getElementById("poid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_poind()
{
	document.getElementById("page").value = 1;
	paging_poind();
}

function previouspage_poind()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_poind();
}

function nextpage_poind()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_poind();
}

function lastpage_poind()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_poind();
}

var HINTS_CFG = {
	'wise'       : true, // don't go off screen, don't overlap the object in the document
	'margin'     : 10, // minimum allowed distance between the hint and the window edge (negative values accepted)
	'gap'        : -10, // minimum allowed distance between the hint and the origin (negative values accepted)
	'align'      : 'brtl', // align of the hint and the origin (by first letters origin's top|middle|bottom left|center|right to hint's top|middle|bottom left|center|right)
	'show_delay' : 100, // a delay between initiating event (mouseover for example) and hint appearing
	'hide_delay' : 0 // a delay between closing event (mouseout for example) and hint disappearing
};
var myHint = new THints (null, HINTS_CFG);

// custom JavaScript function that updates the text of the hint before displaying it
function myShow(s_text, e_origin) {
	var e_hint = getElement('reusableHint');
	e_hint.innerHTML = s_text;
	myHint.show('reusableHint', e_origin);
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="450px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_no;?>"  style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>P.O. Date:</td>
			<td><input name="poDate" id="poDate" maxlength="10" size="15" readonly="true" value="<?php echo $po_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:</td>
			<td><input name="partyName" id="partyName" maxlength="50" size="45" readonly="true" value="<?php echo $party_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Company Name:</td>
			<td><input name="companyName" id="companyName" maxlength="50" size="45" readonly="true" value="<?php echo $company_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php echo $address1;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Ship To:</td>
			<?php if($shipto==1){?>
				<td><input type="radio" name="rdoShipto" id="rdoShipto" checked="true" disabled="true" value="1"/>&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="2"/>&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="3"/>&nbsp;Other</td>
			<?php } elseif($shipto==2){?>
				<td><input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="1"/>&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" checked="true" disabled="true" value="2"/>&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="3"/>&nbsp;Other</td>
			<?php } elseif($shipto==3){?>
				<td><input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="1"/>&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" disabled="true" value="2"/>&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" checked="true" disabled="true" value="3"/>&nbsp;Other</td>
			<?php }?>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Name:</td>
			<td><input name="shipName" id="shipName" maxlength="50" size="45" readonly="true" value="<?php echo $ship_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="45" readonly="true" value="<?php echo $address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Address1:</td>
			<td><input name="shippingAddress1" id="shippingAddress1" maxlength="50" size="45" readonly="true" value="<?php echo $ship_address1;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" readonly="true" value="<?php echo $city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Address2:</td>
			<td><input name="shippingAddress2" id="shippingAddress2" maxlength="50" size="45" readonly="true" value="<?php echo $ship_address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><input name="stateName" id="stateName" maxlength="50" size="45" readonly="true" value="<?php echo $state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Address3:</td>
			<td><input name="shippingAddress3" id="shippingAddress3" maxlength="50" size="45" readonly="true" value="<?php echo $ship_address3;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Contact To:</td>
			<td><input name="contactPerson" id="contactPerson" maxlength="50" size="45" readonly="true" value="<?php echo $contact_person;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>City:</td>
			<td><input name="shippingcityName" id="shippingcityName" maxlength="50" size="45" readonly="true" value="<?php echo $ship_city_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>TIN No.:</td>
			<td><input name="tinNumber" id="tinNumber" maxlength="15" size="45" readonly="true" value="<?php echo $tin;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>State:</td>
			<td><input name="shippingstateName" id="shippingstateName" maxlength="50" size="45" readonly="true" value="<?php echo $ship_state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Vendor Ref:</td>
			<td><input name="vendorRef" id="vendorRef" maxlength="30" size="45" readonly="true" value="<?php echo $vendor_ref;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Method:</td>
			<td><input name="shipMethod" id="shipMethod" maxlength="50" size="45" readonly="true" value="<?php echo $ship_method;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Terms &amp; Conditions:</td>
			<td rowspan="4"><textarea name="termsCondition" id="termsCondition" cols="35" rows="7" readonly="true" style="background-color:#E7F0F8; color:#0000FF"><?php echo $terms_condition;?></textarea></td>
			
			<td class="th" nowrap>Shipping Terms:</td>
			<td><input name="shipTerms" id="shipTerms" maxlength="50" size="45" readonly="true" value="<?php echo $ship_terms;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td class="th" nowrap>Delivery Date:</td>
			<td><input name="deliveryDate" id="deliveryDate" maxlength="10" size="10" value="<?php echo $delivery_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td class="th" nowrap>Delivery At:</td>
			<td><input name="deliveryAt" id="deliveryAt" maxlength="50" size="45" readonly="true" value="<?php echo $location_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td>Work Order</td>
			<td><input name="work_order" id="work_order" style="width:50px;background-color:#E7F0F8; color:#0000FF" value="<?php if($row1["work_order"]=='N'){echo 'No';}else{echo 'Yes';} ?>" readonly="true"></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [Selected Indent ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="center" width="5%">Sl.No.</th>
			<th align="center" width="20%">Indent No.</th>
			<th align="center" width="10%">Date</th>
			<th align="center" width="25%">Indent From</th>
			<th align="center" width="25%">Indent By</th>
			<th align="center" width="10%">Supply Date</th>
			<th align="center" width="5%">Select</th>
		</tr>
		
		<?php 
		$i = 0;
                
		$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id IN (SELECT DISTINCT indent_id FROM tblpo_item WHERE po_id=".$oid.") ORDER BY indent_date, indent_id") or die(mysql_error());
		while($row_ind=mysql_fetch_array($sql_ind)){
			$i++;
			echo '<tr class="Controls">';
			$selected_ref = "purchaseitem.php?action=new&oid=".$oid."&ino=".$row_ind['indent_id']."&mul=n";
			$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
			if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td>'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td>';
			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$selected_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$selected_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		}?>
		
 		<tr class="Bottom">
			<td align="right" colspan="7">
			<?php if($i >0){ ?>
			<a href="javascript:window.location='purchaseitem.php?action=new&oid=<?php echo $oid;?>&mul=y'" ><img src="images/next.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			<?php } else { ?>
			<a href="javascript:window.location='purchaseitem.php?action=new&oid=<?php echo $oid;?>'" ><img src="images/next.gif" width="72" height="22" style="display:none;cursor:hand;" border="0" /></a>
			<?php } ?>
			&nbsp;&nbsp;<a href="javascript:window.location='purchaseorder1.php?oid=<?php echo $oid;?>'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [ Indent List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
<!-- HTML code for the hint, make sure the hint has unique ID, it is positioned absolutely and initially invisible.
	The same hint element will be reused for all cells of the table. This approach simplifies and optimizes the
	implementation of the dynamically generated pages. -->

<div id="reusableHint" style="position:absolute;z-index:1;visibility:hidden;padding:10px;background-color:#FFFFCC;border:2px solid #CCCC00;"></div>
<!-- End of the HTML code for the hint -->

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="20%">Indent No.</th>
			<th width="10%">Date</th>
			<th width="25%">Indent From</th>
			<th width="25%">Indent By</th>
			<th width="10%">Supply Date</th>
			<th width="5%">Select</th>
		</tr>
		
		<?php 
		$start=0;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}
		$i = $start;
		
		$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND appr_status='S' AND indent_id IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1) ORDER BY location_name, indent_date, indent_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_ind=mysql_fetch_array($sql_ind)){
			$sql_item = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$row_ind['indent_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$select_ref = "purchaseitem.php?action=new&oid=".$oid."&ino=".$row_ind['indent_id']."&mul=n";
			$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
			if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is indent number '.$indent_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td>';
			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$select_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$select_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="7" align="center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM tbl_indent WHERE (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND appr_status='S' AND indent_id IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1)") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_poind()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="poid" id="poid" value="'.$oid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_poind()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_poind()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_poind()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pg"]<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_poind()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_poind()" />';
			?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</center>
</body>
</html>
