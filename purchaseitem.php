<?php 
session_start();
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
if(isset($_REQUEST['oid'])){$oid = $_REQUEST['oid'];}
if(isset($_REQUEST['ino'])){$ino = $_REQUEST['ino'];}
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
/*-------------------------------*/
if($shipto==1 || $shipto==2){
	$sql3 = mysql_query("SELECT company_name AS ship_name, c_address1 AS ship_address1, c_address2 AS ship_address2, c_address3 AS ship_address3, city_name, state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$row1['shipping_id']) or die(mysql_error());
} elseif($shipto==3) {
	$sql3 = mysql_query("SELECT party_name AS ship_name, address1 AS ship_address1, address2 AS ship_address2, address3 AS ship_address3, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$row1['party_id']) or die(mysql_error());
}
$row3 = mysql_fetch_assoc($sql3);
$ship_name = $row3["ship_name"];
$ship_address1 = $row3["ship_address1"];
$ship_address2 = $row3["ship_address2"];
$ship_address3 = $row3["ship_address3"];
$ship_city_name = $row3["ship_city_name"];
$ship_state_name = $row3["ship_state_name"];
/*-------------------------------*/
if(isset($_REQUEST["msg"])){
	$msg = $_REQUEST['msg'];
	unset($_REQUEST['msg']);
}
/*-------------------------------*/
$rid = 0;
$item_name = "";
$qnty = "";
$rate = "";
$unit_id = 0;
$unit_name = "";
$alt_unit = "";
$alt_unit_id = 0;
$item_description = "";
$rate_required = 1;
$item_make = "";
if(isset($_REQUEST["rid"])){
	$rid = $_REQUEST['rid'];
	$iid = $_REQUEST['iid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		if($rid!=0){
			$sql2 = mysql_query("SELECT tblpo_item.*,tbl_indent_item.*, item_name, item.alt_unit, item.alt_unit_id, unit_name FROM tblpo_item INNER JOIN item join tbl_indent_item ON tblpo_item.item_id = item.item_id and tblpo_item.item_id= tbl_indent_item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id WHERE tblpo_item.rec_id=".$rid." and tbl_indent_item.indent_id=".$_REQUEST['ino']) ;
                     
		} elseif($rid==0){
			$sql2 = mysql_query("SELECT tbl_indent_item.*,item_name,item.alt_unit,item.alt_unit_id,unit_name,0 AS qnty,0 AS rate FROM item INNER JOIN unit join tbl_indent_item ON  tbl_indent_item.item_id=item.item_id and item.unit_id = unit.unit_id WHERE item.item_id=".$iid." and indent_id=".$_REQUEST['ino']);
                        
		}
		$row2 = mysql_fetch_assoc($sql2);
		$item_name = $row2["item_name"];
		$qnty = $row2["qnty"];
		$rate = $row2["rate"];
		$unit_id = $row2['unit_id'];
		$unit_name = $row2['unit_name'];
		$alt_unit = $row2['alt_unit'];
                 $item_description = $row2["remark"];
                
    
		$alt_unit_id = $row2['alt_unit_id'];
		$rate_required = $row2["rate_required"];
		$item_make = $row2["item_make"];
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT * FROM item WHERE item_id=".$iid);
	$row = mysql_fetch_assoc($sql);
	$itemname = $row["item_name"];
	/*-------------------------------*/
	if($row['alt_unit']=="N"){$unitid = $row['unit_id'];} elseif($row['alt_unit']=="A"){$unitid = $_POST['unit'];}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$unitid);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$datePOrder = date("Y-m-d",strtotime($row1['po_date']));
	$particulars = "From ".$row1['party_name'];
	$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
	/*-------------------------------*/
	if($_POST['submit']=="update"){
            echo $_SESSION['stores_uid'];
		if($rid==0){
			$sql = mysql_query("SELECT * FROM tblpo_item WHERE po_id=".$oid." AND indent_id=".$ino." AND item_id=".$iid." AND qnty=".$_POST['itemQnty']." AND rate=".$_POST['itemRate']) or die(mysql_error());
			$count = mysql_num_rows($sql);
			if($count>0)
				$msg = "Duplicate data! can't insert into purchase order record.";
			else {
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblpo_item");
				$row = mysql_fetch_assoc($sql);
				$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblpo_item WHERE po_id=".$oid);
				$row = mysql_fetch_assoc($sql);
				$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
				echo $sql = "INSERT INTO tblpo_item (rec_id,po_id,indent_id,seq_no,item_id,unit_id,qnty,rate,rate_required,item_description,item_make) VALUES(".$rid.",".$oid.",".$ino.",".$sno.",".$iid.",".$unitid.",".$_POST['itemQnty'].",".$_POST['itemRate'].",".(isset($_POST['rateRequired'])?1:0).",'".$_POST['itemDescription']."','".$_POST['itemMake']."')";
                                
				$res = mysql_query($sql) or die(mysql_error());
                                
//				if($_POST['orderedQnty'] + $_POST['itemQnty'] >= $_POST['indentQnty'] - $_POST['indentQnty']*0.10)
				$res = mysql_query("UPDATE tbl_indent_item SET item_ordered='".$_POST['confirmQnty']."',remark='".$_POST['itemDescription']."' WHERE indent_id=".$ino." AND item_id=".$iid) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['itemRate'].",'".$lname."','New','".$uname."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
			}
		} elseif($rid!=0) {
			$res = mysql_query("UPDATE tblpo_item SET item_id=".$iid.",unit_id=".$unitid.",qnty=".$_POST['itemQnty'].",rate=".$_POST['itemRate'].",rate_required=".(isset($_POST['rateRequired'])?1:0).",item_description='".$_POST['itemDescription']."',item_make='".$_POST['itemMake']."' WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE tbl_indent_item SET item_ordered='".$_POST['confirmQnty']."',remark='".$_POST['itemDescription']."' WHERE indent_id=".$ino." AND item_id=".$iid) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['itemRate'].",'".$lname."','Change','".$uname."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
		if($_REQUEST['mul']=="n")
			echo '<script language="javascript">window.location="purchaseitem.php?action=new&oid='.$oid.'&ino='.$ino.'&mul=n";</script>';
		elseif($_REQUEST['mul']=="y")
			echo '<script language="javascript">window.location="purchaseitem.php?action=new&oid='.$oid.'&mul=y";</script>';
	} elseif($_POST['submit']=="delete"){
		if($rid!=0) {
			$res = mysql_query("DELETE FROM tblpo_item WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE tbl_indent_item SET item_ordered='N' WHERE indent_id=".$ino." AND item_id=".$iid) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",".$_POST['itemRate'].",'".$lname."','Delete','".$uname."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			if($_REQUEST['mul']=="n")
				echo '<script language="javascript">window.location="purchaseitem.php?action=new&oid='.$oid.'&ino='.$ino.'&mul=n";</script>';
			elseif($_REQUEST['mul']=="y")
				echo '<script language="javascript">window.location="purchaseitem.php?action=new&oid='.$oid.'&mul=y";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function set_focus_on_rate(me)
{
	if(me.checked){
		document.getElementById('rateSpan').innerHTML = '<input name="itemRate" id="itemRate" maxlength="10" size="15" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["rate"];}?>" >';
	} else if(!me.checked){
		document.getElementById('rateSpan').innerHTML = '<input name="itemRate" id="itemRate" maxlength="10" size="15" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["rate"];}?>" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
	}
}

function validate_purchase()
{
	var err="";
	if(document.getElementById("itemQnty").value!="" && ! IsNumeric(document.getElementById("itemQnty").value))
		err = "* please input valid quantity for the item!\n";
	if(document.getElementById("itemRate").value!="" && ! IsNumeric(document.getElementById("itemRate").value))
		err += "* please input valid rate for the item!\n";
	if(document.getElementById("itemQnty").value=="" || document.getElementById("itemQnty").value==0)
		err += "* Item quantity is madatory!\n";
	if(document.getElementById("rateRequired").checked){
		if(document.getElementById("itemRate").value=="" || document.getElementById("itemRate").value==0)
			err += "* Item rate is madatory!\n";
	}
	if(parseFloat(document.getElementById("itemQnty").value)>(parseFloat(document.getElementById("indentQnty").value)-parseFloat(document.getElementById("orderedQnty").value)))
		err += "* Invalid Item quantity! excess quantity not acceptable.\n";
	          
	if(err==""){
		var b = parseFloat(document.getElementById("indentQnty").value) - parseFloat(document.getElementById("itemQnty").value) - parseFloat(document.getElementById("orderedQnty").value);
		var i = parseFloat(document.getElementById("indentQnty").value);
		var p = b / i * 100;
		if(p>0 && p<=10){
			var r=confirm("Balance Quantity is "+p+"% of Indent Quantity.\n" + 
			"To keep this Quantity click OK button else click Cancel button.");
			if(r==true)
				document.getElementById("confirmQnty").value = "N";
			else
				document.getElementById("confirmQnty").value = "Y";
		} else if(p>10)
			document.getElementById("confirmQnty").value = "N";
		else if(p==0)
			document.getElementById("confirmQnty").value = "Y";
	}
	
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
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
		if($_REQUEST['mul']=="n"){
			$i = 1;
			$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$ino) or die(mysql_error());
			$row_ind = mysql_fetch_assoc($sql_ind);
			echo '<tr class="Controls">';
			$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
			if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td>'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td>';
			echo '</tr>';
		} elseif($_REQUEST['mul']=="y") {
			$i = 0;
			$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id IN (SELECT DISTINCT indent_id FROM tblpo_item WHERE po_id=".$oid.") ORDER BY indent_date, indent_id") or die(mysql_error());
			while($row_ind=mysql_fetch_array($sql_ind)){
				$i++;
				echo '<tr class="Controls">';
				$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
				if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
			
				echo '<td align="center">'.$i.'.</td><td>'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td>';
				echo '</tr>';
			}
		} ?>
		
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="purchaseitem" method="post" onsubmit="return validate_purchase()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [ Item ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td width="15%" nowrap class="th">Item Name:</td>
			<td width="35%"><input name="itemName" id="itemName" maxlength="50" size="45" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td width="15%" nowrap class="th">Quantity:</td>
			<td width="35%"><input name="itemQnty" id="itemQnty" maxlength="10" size="15" value="<?php echo $qnty;?>" >&nbsp;<?php if($alt_unit=="N"){echo $unit_name;} elseif($alt_unit=="A"){echo "&nbsp;";} else {echo "";}?></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Item's Description:</td>
                        <td rowspan="4"><textarea rows="8" cols="50" type="text" name="itemDescription" id="itemDescription"><?php  echo $item_description;?></textarea></td>
			
			<td class="th" nowrap>Rate Required:</td>
			<?php if($rate_required==1){?>
				<td><input type="checkbox" name="rateRequired" id="rateRequired" checked="checked" value="1" onclick="set_focus_on_rate(this)">
                                      
                                </td>
			<?php } else {?>
				<td><input type="checkbox" name="rateRequired" id="rateRequired" value="0" onclick="set_focus_on_rate(this)">
                                        
                               </td>
			<?php } ?>
		</tr>
		
		<tr class="Controls">
			<td class="th">&nbsp;</td>
			<td class="th"><?php if($alt_unit=="N"){echo "&nbsp;";} elseif($alt_unit=="A"){echo "Unit:";} else {echo "&nbsp;";}?></td>
			<td class="th"><?php 
			if($alt_unit=="N"){
				echo "&nbsp;";
			} elseif($alt_unit=="A" && $alt_unit_id!=0){
				$sql3=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$alt_unit_id);
				$row3=mysql_fetch_assoc($sql3);
				echo '<select name="unit" id="unit" style="width:115px"><option value="'.$unit_id.'">'.$unit_name.'</option><option value="'.$alt_unit_id.'">'.$row3['alt_unit_name'].'</option></select>';
			} else {
				echo "&nbsp;";
			}?>
			</td>
		</tr>
		
		<tr class="Controls">
			<td class="th">&nbsp;</td>
			<td class="th" nowrap>Unit Price:</td>
			<?php if($rate_required==1){?>
				<td><span id="rateSpan"><input name="itemRate" id="itemRate" maxlength="10" size="15" value="<?php echo $rate;?>" ></span></td>
			<?php } else {?>
				<td><span id="rateSpan"><input name="itemRate" id="itemRate" maxlength="10" size="15" value="<?php echo $rate;?>" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></span></td>
			<?php } ?>
		</tr>
		<tr class="Controls">
			<td class="th">&nbsp;</td>
			<td class="th" nowrap>Prev unit Price</td>
                      
			<td><input name="itemMake" id="itemMake"   value="<?php 
                        if($_REQUEST['iid']){
                        $dataVal=mysql_query("select po_id,rate from tblpo_item where item_id=".$_REQUEST['iid']." and rec_id=(select max(rec_id) from tblpo_item where rec_id<(select max(rec_id) from tblpo_item where item_id=".$_REQUEST['iid'].") and item_id=".$_REQUEST['iid'].")");
                        $recVal=mysql_fetch_array($dataVal);
                        echo $recVal['rate'];
                        }
                                ?>" /><?php if($recVal['rate']){?> <a href="#" onclick='window.open("purchaseitemnew.php?po_id=<?php echo $recVal['po_id'] ?>","_blank","toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=500, height=130")'>Detail</a><?php };?></td>
		</tr>
		<tr class="Controls">
			<td class="th">&nbsp;</td>
                        <td class="th">&nbsp;</td>
			<td class="th" nowrap>Item Make:</td>
			<td><input name="itemMake" id="itemMake" maxlength="50" size="40" value="<?php echo $item_make;?>" ></td>
		</tr>
		<?php 
		$indent_qnty = 0;
		$ordered_qnty = 0;
		if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
			$sql_order = mysql_query("SELECT aprvd_qnty FROM tbl_indent_item WHERE indent_id=".$ino." AND item_id=".$iid) or die(mysql_error());
			$row_order = mysql_fetch_assoc($sql_order);
			if(mysql_num_rows($sql_order)>0){$indent_qnty = $row_order['aprvd_qnty'];}
			
			$sql3 = mysql_query("SELECT Sum(qnty) AS qnty FROM tblpo_item WHERE indent_id=".$ino." AND item_id=".$iid." AND po_id!=".$oid) or die(mysql_error());
			$row3 = mysql_fetch_assoc($sql3);
			if(mysql_num_rows($sql3)>0){$ordered_qnty = ($row3['qnty']==NULL ? 0 : $row3['qnty']);}
		}?>
		<input type="hidden" name="indentQnty" id="indentQnty" value="<?php echo $indent_qnty;?>" /><input type="hidden" name="orderedQnty" id="orderedQnty" value="<?php echo $ordered_qnty;?>" /><input type="hidden" name="confirmQnty" id="confirmQnty" value="N" />
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['po1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['po1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.purchaseitem.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='purchaseitem.php?action=new&oid=<?php echo $oid;?>&ino=<?php echo $ino;?>&mul=n'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"/><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='purchaseitem.php?action=new&oid=<?php echo $oid;?>&ino=<?php echo $ino;?>&mul=n'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='podutyntaxes.php?action=new&oid=<?php echo $oid;?>'" ><img src="images/next.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='poindent.php?action=new&oid=<?php echo $oid;?>'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
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
                        <th width="25%">Item Description</th>
			<th width="18%">Indent Qnty.</th>
			<th width="17%">Ordered Qnty.</th>
			<th width="17%">Cur.PO.Qnty.</th>
			<th width="12%">Cur.PO.Rate</th>
			<th width="3%">Edit</th>
			<th width="3%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		if($_REQUEST['mul']=="n"){
			$sql_order = mysql_query("SELECT tbl_indent_item.*, item_name, item.unit_id AS itemUnitId, alt_unit, alt_unit_id, alt_unit_num, indentUnit.unit_name AS indentUnitName, itemUnit.unit_name AS itemUnitName FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit AS indentUnit ON tbl_indent_item.unit_id = indentUnit.unit_id INNER JOIN unit AS itemUnit ON item.unit_id = itemUnit.unit_id WHERE indent_id=".$ino." AND aprvd_status=1 ORDER BY seq_no") or die(mysql_error());
		} elseif($_REQUEST['mul']=="y") {
			$sql_order = mysql_query("SELECT tbl_indent_item.*, item_name, item.unit_id AS itemUnitId, alt_unit, alt_unit_id, alt_unit_num, indentUnit.unit_name AS indentUnitName, itemUnit.unit_name AS itemUnitName FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit AS indentUnit ON tbl_indent_item.unit_id = indentUnit.unit_id INNER JOIN unit AS itemUnit ON item.unit_id = itemUnit.unit_id WHERE indent_id IN (SELECT DISTINCT indent_id FROM tblpo_item WHERE po_id=".$oid.") AND aprvd_status=1 ORDER BY indent_id, seq_no") or die(mysql_error());
		}
		while($row_order=mysql_fetch_array($sql_order)){
			$indentid = $row_order['indent_id'];
			$show_primary_unit_of_indent_qnty = "no";
			$show_primary_unit_of_current_qnty = "no";
			if($row_order['unit_id']!=$row_order['itemUnitId']){
				$show_primary_unit_of_indent_qnty = "yes";
				$show_indent_qnty = "(".number_format($row_order['aprvd_qnty'] / $row_order['alt_unit_num'],3,'.','')." ".$row_order['itemUnitName'].")";
			}
			
			$ordered_qnty = 0;
			if($row_order['alt_unit']=="N"){
				$sql3 = mysql_query("SELECT Sum(qnty) AS qnty FROM tblpo_item WHERE indent_id=".$indentid." AND item_id=".$row_order['item_id']." AND po_id!=".$oid) or die(mysql_error());
				$row3 = mysql_fetch_assoc($sql3);
				if(mysql_num_rows($sql3)>0){$ordered_qnty = ($row3['qnty']==NULL ? 0 : $row3['qnty']);}
			} elseif($row_order['alt_unit']=="A"){
				$sql3 = mysql_query("SELECT * FROM tblpo_item WHERE indent_id=".$indentid." AND item_id=".$row_order['item_id']." AND po_id!=".$oid) or die(mysql_error());
				while($row3=mysql_fetch_array($sql3)){
					if($row3['unit_id']==$row_order['itemUnitId'])
						$ordered_qnty += $row3['qnty'];
					else 
						$ordered_qnty += number_format($row3['qnty'] / $row_order['alt_unit_num'],3,'.','');
				}
			}
			$ordered_unit = $row_order['itemUnitName'];
			
			$recid = 0;
			$cur_po_qnty = 0;
			$cur_po_rate = 0;
			$sql4 = mysql_query("SELECT tblpo_item.*, unit_name FROM tblpo_item INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id WHERE indent_id=".$indentid." AND item_id=".$row_order['item_id']." AND po_id=".$oid) or die(mysql_error());
			$row4 = mysql_fetch_assoc($sql4);
			if(mysql_num_rows($sql4)>0){
				$recid =  $row4['rec_id'];
				$cur_po_qnty =  $row4['qnty'];
				$cur_po_rate =  $row4['rate'];
				if($row4['unit_id']!=$row_order['itemUnitId']){
					$show_primary_unit_of_current_qnty = "yes";
					$show_current_qnty = "(".number_format($row4['qnty'] / $row_order['alt_unit_num'],3,'.','')." ".$row_order['itemUnitName'].")";
				}
			}
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "purchaseitem.php?action=delete&oid=".$oid."&ino=".$indentid."&mul=".$_REQUEST['mul']."&rid=".$recid."&iid=".$row_order['item_id'];
			$edit_ref = "purchaseitem.php?action=edit&oid=".$oid."&ino=".$indentid."&mul=".$_REQUEST['mul']."&rid=".$recid."&iid=".$row_order['item_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td>'.$row_order['remark'].'</td><td align="center">'.$row_order['aprvd_qnty']." ".$row_order['indentUnitName'];
                        
			if($show_primary_unit_of_indent_qnty == "yes"){echo '<br/><span style="font-size:10px;">'.$show_indent_qnty.'</span>';}
			echo '</td><td>'.($ordered_qnty==0?$ordered_qnty:$ordered_qnty." ".$ordered_unit).'</td><td>'.$cur_po_qnty." ".$row4['unit_name'];
			if($show_primary_unit_of_current_qnty == "yes"){echo '<br/><span style="font-size:10px;">'.$show_current_qnty.'</span>';}
			echo '</td><td>'.$cur_po_rate.'</td>';
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
