<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) ;
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
if(isset($_REQUEST['oid'])){$oid = $_REQUEST['oid'];}
$sql1 = mysql_query("SELECT tblpo.*, company_name, party_name, address1, address2, address3, city_name, state_name, contact_person, tin, location_name FROM tblpo INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id INNER JOIN location ON tblpo.delivery_at = location.location_id WHERE po_id=".$oid) ;
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
/*-------------------------------*/
if($shipto==1 || $shipto==2){
	$sql3 = mysql_query("SELECT company_name AS ship_name, c_address1 AS ship_address1, c_address2 AS ship_address2, c_address3 AS ship_address3, city_name, state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$row1['shipping_id']) ;
} elseif($shipto==3) {
	$sql3 = mysql_query("SELECT party_name AS ship_name, address1 AS ship_address1, address2 AS ship_address2, address3 AS ship_address3, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$row1['party_id']) ;
}
$row3 = mysql_fetch_assoc($sql3);
$ship_name = $row3["ship_name"];
$ship_address1 = $row3["ship_address1"];
$ship_address2 = $row3["ship_address2"];
$ship_address3 = $row3["ship_address3"];
$ship_city_name = $row3["ship_city_name"];
$ship_state_name = $row3["ship_state_name"];
/*-------------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="calc"){
	$item_total = 0;
	$sql_order = mysql_query("SELECT * FROM tblpo_item WHERE po_id=".$oid." ORDER BY seq_no") ;
	while($row_order=mysql_fetch_array($sql_order))
	{
		$cur_qnty =  $row_order['qnty'];
		$cur_rate =  $row_order['rate'];
		$cur_amount = $cur_qnty * $cur_rate;
		$item_total += $cur_amount;
	}
	/*-------------------------------*/
	$i = 0;
	$sub_total = 0;
	$sql_order = mysql_query("SELECT * FROM tblpo_dtm WHERE po_id=".$oid." ORDER BY seq_no") ;
	while($row_order=mysql_fetch_array($sql_order))
	{
		$i++;
		$cur_record =  $row_order['rec_id'];
		$cur_rate =  $row_order['dtm_percent'];
		if($row_order['feed']=="A")
			$cur_amount = round($item_total * $cur_rate / 100,0);
		elseif($row_order['feed']=="M")
			$cur_amount = $row_order['dtm_amount'];
		if($row_order['calc']=="P")
			$sub_total = $item_total + $cur_amount;
		elseif($row_order['calc']=="M")
			$sub_total = $item_total - $cur_amount;
		if($row_order['feed']=="A")
			$res = mysql_query("UPDATE tblpo_dtm SET seq_no=".$i.",on_amount=".$item_total.",dtm_amount=".$cur_amount.",total_amount=".$sub_total." WHERE rec_id=".$cur_record) ;
		elseif($row_order['feed']=="M")
			$res = mysql_query("UPDATE tblpo_dtm SET seq_no=".$i.",on_amount=".$item_total.",total_amount=".$sub_total." WHERE rec_id=".$cur_record) ;
		$item_total = $sub_total;
	}
	echo '<script language="javascript">window.location="podutyntaxes.php?action=new&oid='.$oid.'";</script>';
}
/*-------------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="send"){
	$sql2 = mysql_query("SELECT * FROM tblpo_item WHERE po_id=".$oid);
	$row2 = mysql_fetch_assoc($sql2);
	$count = mysql_num_rows($sql2);
	/*-------------------------------*/
	if($count>0){
		$res = mysql_query("UPDATE tblpo SET po_status='S' WHERE po_id=".$oid) ;
                $dataMail=mysql_query("select email_id from users where dc2=1 and user_type='U' and user_status='A'");
$recMail=  mysql_fetch_array($dataMail);
if($_SESSION['stores_uid']!=$recMail['email_id']){
        $to=$recMail["email_id"];
        $sub="Mail regarding Purchase Order";
        $mailMsg="Please complete purchase order";
        $header="From:admin@vnrseeds.com";
        if($to){
        if(mail($to,$sub,$mailMsg,$header))
                echo"<script>alert('Mail sent to $to')</script>";
        else echo"<script>alert('Mail not sent')</script>";
        }
        else {
        echo"<script>alert('No user specified')</script>";    
        }
 }
		echo '<script language="javascript">window.location="purchaseorder.php?action=new";</script>';
	} elseif($count==0){
		$msg = "Sorry! This order can not send, since having no item! Please retry....";
		echo '<script language="javascript">window.location="podutyntaxes.php?action=new&oid='.$oid.'&msg='.$msg.'";</script>';
	}
}
/*-------------------------------*/
if(isset($_REQUEST["msg"])){
	$msg = $_REQUEST['msg'];
	unset($_REQUEST['msg']);
}
/*-------------------------------*/
$rid = 0;
$ssat = "";
$feed = "";
$calc = "";
$dtm_id = 0;
$dtm_percent = "";
if(isset($_REQUEST["rid"])){
	$rid = $_REQUEST['rid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2 = mysql_query("SELECT tblpo_spec.specification from tblpo_spec WHERE rec_id=".$rid);
		$row2 = mysql_fetch_assoc($sql2);
		 $spec = $row2['specification'];
		
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$dtmprcnt = ($_POST['Percentage']=="" ? 0 : $_POST['Percentage']);
	$dtmamt = ($_POST['dtmAmount']=="" ? 0 : $_POST['dtmAmount']);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblpo_spec SET specification='".$_POST['txtSpec']."' WHERE rec_id=".$rid) ;
		echo '<script language="javascript">window.location="pospec.php?action=new&oid='.$oid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblpo_spec WHERE rec_id=".$rid) ;
		echo '<script language="javascript">window.location="pospec.php?action=new&oid='.$oid.'";</script>';
	} elseif($_POST['submit']=="new"){
		
		$sql = "INSERT INTO tblpo_spec (rec_id,po_id,specification) VALUES('',".$oid.",'".$_POST['txtSpec']."')";
		$res = mysql_query($sql) ;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
    <script src="js/bootstrap.min.js"></script>">
    <script>
function validate_dutyNtaxes()
{
	var err="";
	if(document.getElementById("SSAT").value=="S")
		err = "* please select SSAT!\n";
	if(document.getElementById("Feeding").value=="S")
		err += "* please select Feeding!\n";
	if(document.getElementById("Calc").value=="S")
		err += "* please select Calc!\n";
	if(document.getElementById("dtmName").value==0)
		err += "* please select Duty or Taxes or Others!\n";
	if(document.getElementById("Percentage").value!="" && ! IsNumeric(document.getElementById("Percentage").value))
		err += "* please input valid numeric data!\n";
	
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function calculate_amount()
{
	if(document.getElementById("Feeding").value=="A")
		document.getElementById("dtmAmount").value = parseFloat(document.getElementById("onAmount").value) * parseFloat(document.getElementById("Percentage").value) / 100;
	if(document.getElementById("Calc").value=="P")
		document.getElementById("totalAmount").value = parseFloat(document.getElementById("onAmount").value) + parseFloat(document.getElementById("dtmAmount").value);
	else if(document.getElementById("Calc").value=="M")
		document.getElementById("totalAmount").value = parseFloat(document.getElementById("onAmount").value) - parseFloat(document.getElementById("dtmAmount").value);
}

function set_control_on_feeding(me)
{
	if(me=="A"){
		document.getElementById('prcnt').innerHTML = '<input name="Percentage" id="Percentage" maxlength="5" size="5" value="" onchange="calculate_amount()">';
		document.getElementById('dtmamt').innerHTML = '<input name="dtmAmount" id="dtmAmount" maxlength="15" size="10" value="" onchange="calculate_amount()" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
	} else if(me=="M"){
		document.getElementById('prcnt').innerHTML = '<input name="Percentage" id="Percentage" maxlength="5" size="5" value="" onchange="calculate_amount()" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById('dtmamt').innerHTML = '<input name="dtmAmount" id="dtmAmount" maxlength="15" size="10" value="" onchange="calculate_amount()">';
	}
}
function funPrint(id){
window.open("newpurchaseorder.php?po_id="+id,"_blank","scrollbars=yes,resizable=yes,width=800,height=600");
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
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_no;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
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
			<?php if(isset($_REQUEST["action"])){?>
				<td><input name="shipName" id="shipName" maxlength="50" size="45" readonly="true" value="<?php echo $ship_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			<?php } ?>
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
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="center" width="5%">Sl.No.</th>
			<th align="center" width="15%">Indent No.</th>
			<th align="center" width="15%">Date</th>
			<th align="center" width="25%">Indent From</th>
			<th align="center" width="25%">Indent By</th>
			<th align="center" width="15%">Supply Date</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id IN (SELECT DISTINCT indent_id FROM tblpo_item WHERE po_id=".$oid.") ORDER BY indent_date, indent_id") ;
		while($row_ind=mysql_fetch_array($sql_ind))
		{
			$i++;
			echo '<tr class="Controls">';
			$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
			if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td>'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td>';
			echo '</tr>';
		} ?>
		
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
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="25%">Item Name</th>
			<th width="15%">Unit</th>
			<th width="15%">Qnty.</th>
			<th width="15%">Unit Price</th>
			<th width="15%">Item Amount</th>
		</tr>
		
		<?php 
		$i = 0;
		$item_total = 0;
		$sql_order = mysql_query("SELECT tblpo_item.*, item_name, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id WHERE po_id=".$oid." ORDER BY seq_no") ;
		while($row_order=mysql_fetch_array($sql_order))
		{
			$cur_qnty =  $row_order['qnty'];
			$cur_rate =  $row_order['rate'];
			$cur_amount = $cur_qnty * $cur_rate;
			$item_total += $cur_amount;
			
			$i++;
			echo '<tr class="Row">';
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td>'.$row_order['unit_name'].'</td><td>'.$cur_qnty.'</td><td>'.$cur_rate.'</td><td>'.number_format($cur_amount,2,".","").'</td>';
			echo '</tr>';
		}
		//calculate net amount
		$total_amount = $item_total;
		$sql_order = mysql_query("SELECT total_amount FROM tblpo_dtm WHERE po_id=".$oid." ORDER BY seq_no") ;
		while($row_order=mysql_fetch_array($sql_order))
		{
			$total_amount = $row_order['total_amount'];
		}
		?>
		
		<tr class="Row">
			<td colspan="4">&nbsp;</td>
			<td style="font-weight:bold">SUBTOTAL:&nbsp;</td>
			<td style="font-weight:bold"><?php echo number_format($item_total,2,".","");?></td>
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
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		
		
		<?php 
		$i = 0;
		$sql_dtm = mysql_query("SELECT tblpo_dtm.*, dtm_name FROM tblpo_dtm INNER JOIN dtm ON tblpo_dtm.dtm_id = dtm.dtm_id WHERE po_id=".$oid." ORDER BY seq_no") ;
		while($row_dtm=mysql_fetch_array($sql_dtm))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "pospec.php?action=delete&oid=".$oid."&rid=".$row_dtm['rec_id'];
			$edit_ref = "pospec.php?action=edit&oid=".$oid."&rid=".$row_dtm['rec_id'];
			if($row_dtm['ssat']=="Y"){$ssat = "Yes";} elseif($row_dtm['ssat']=="N"){$ssat = "No";}
			if($row_dtm['feed']=="A"){$feed = "Auto";} elseif($row_dtm['feed']=="M"){$feed = "Manual";}
			if($row_dtm['calc']=="P"){$calc = "Plus";} elseif($row_dtm['calc']=="M"){$calc = "Minus";}
			
			echo '<td align="center">'.$i.'.</td><td align="center">'.$ssat.'</td><td align="center">'.$feed.'</td><td align="center">'.$calc.'</td><td>'.$row_dtm['dtm_name'].'</td><td align="center">'.$row_dtm['dtm_percent'].'%</td><td>'.$row_dtm['on_amount'].'</td><td>'.$row_dtm['dtm_amount'].'</td><td>'.$row_dtm['total_amount'].'</td>';
			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['po3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="podutyntaxes" method="post" onsubmit="return validate_dutyNtaxes()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [ Specification]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th align="center" width="7%">Specification</th>
			
		</tr>
		
		<tr class="Controls">
                    <td>
                        <input type="text" id="txtSpec" style="width:80%;border: 1px solid green"class="control" name="txtSpec" value="<?php  echo $spec ?>"/>
                    </td>
                </tr>
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="8" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="8">
		<?php ;if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="new" )){
                  
			if($row_user['po1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['po1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.pospec.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='pospec.php?action=new&oid=<?php echo $oid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='pospec.php?action=new&oid=<?php echo $oid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }
		$x = "window.open('print_po.php?oid='".$oid."','poprint','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no,status=yes, menubar=no,copyhistory=no')";?>
                            &nbsp;&nbsp;<a href="javascript:window.location='pospec.php?action=calc&oid=<?php echo $oid;?>'" ><img src="images/calculate.gif" width="93" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='purchaseitem.php?action=new&oid=<?php echo $oid;?>&mul=y'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location = 'pospec.php?action=send&oid=<?php echo $oid;?>'" ><img src="images/send.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a onclick="<?php echo $x;?>" ><img src="images/print.gif"  onclick=funPrint("<?php echo $oid ?>") /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
    <td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
                        <th width="80%">Specification</th>
                        <th width="5%">edit</th>
                        <th width="5%">delete</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_dtm = mysql_query("SELECT tblpo_spec.* FROM tblpo_spec  WHERE po_id=".$oid) ;
		while($row_dtm=mysql_fetch_array($sql_dtm))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "pospec.php?action=delete&oid=".$oid."&rid=".$row_dtm['rec_id'];
			$edit_ref = "pospec.php?action=edit&oid=".$oid."&rid=".$row_dtm['rec_id'];
			
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_dtm['specification'].'</td>';
			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['po3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
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
