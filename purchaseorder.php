<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
$sql = mysql_query("SELECT * FROM tac") or die(mysql_error());
$row = mysql_fetch_assoc($sql);
$termscondition = $row['tac_detail'];
/*-------------------------------*/
$msg = "";
$oid = "";
$po_number = "";
$po_date = date("d-m-Y");
$delivery_date = date("d-m-Y");
$delivery_at = 0;
$company_id = 0;
$party_id = 0;
$address1 = "";
$address2 = "";
$address3 = "";
$city_name = "";
$state_name = "";
$contact_person = "";
$tin = "";
$vendor_ref = "";
$ship_method = "";
$ship_terms = "";
$shipping_id = 0;
$shipto = 1;
$ship_name = "";
$ship_address1 = "";
$ship_address2 = "";
$ship_address3 = "";
$ship_city_name = "";
$ship_state_name = "";
$work_order = "N";
/*-------------------------------*/
if(isset($_REQUEST["oid"])){
	$oid = $_REQUEST['oid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT tblpo.*, party_name, address1, address2, address3, city_name, state_name, contact_person, tin, location_name FROM tblpo INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE po_id=".$oid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$po_number = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
		$po_date = date("d-m-Y",strtotime($row["po_date"]));
		$delivery_date = date("d-m-Y",strtotime($row["delivery_date"]));
		$delivery_at = $row["delivery_at"];
		$company_id = $row["company_id"];
		$party_id = $row["party_id"];
		$address1 = $row["address1"];
		$address2 = $row["address2"];
		$address3 = $row["address3"];
		$city_name = $row["city_name"];
		$state_name = $row["state_name"];
		$shipping_id = $row['shipping_id'];
		$shipto = $row['shipto'];
		$contact_person = $row["contact_person"];
		$tin = $row["tin"];
		$vendor_ref = $row["vendor_ref"];
		$ship_method = $row["ship_method"];
		$ship_terms = $row["ship_terms"];
                $work_order = $row["work_order"];
		
		if($shipto==1){
			$sql1 = mysql_query("SELECT company_name AS ship_name, c_address1 AS ship_address1, c_address2 AS ship_address2, c_address3 AS ship_address3, city_name, state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$company_id) or die(mysql_error());
		} elseif($shipto==2) {
			$sql1 = mysql_query("SELECT company_name AS ship_name, c_address1 AS ship_address1, c_address2 AS ship_address2, c_address3 AS ship_address3, city_name, state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$shipping_id) or die(mysql_error());
		} elseif($shipto==3) {
			$sql1 = mysql_query("SELECT party_name AS ship_name, address1 AS ship_address1, address2 AS ship_address2, address3 AS ship_address3, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$party_id) or die(mysql_error());
		}
		$row1 = mysql_fetch_assoc($sql1);
		$ship_name = $row1["ship_name"];
		$ship_address1 = $row1["ship_address1"];
		$ship_address2 = $row1["ship_address2"];
		$ship_address3 = $row1["ship_address3"];
		$ship_city_name = $row1["city_name"];
		$ship_state_name = $row1["state_name"];
	} elseif($_REQUEST["action"]=="recall"){
		$sql = mysql_query("SELECT tblpo.*, party_name FROM tblpo INNER JOIN party ON tblpo.party_id = party.party_id WHERE po_id=".$oid);
		$row = mysql_fetch_assoc($sql);
		$datePOrder = $row['po_date'];
		$particulars = "From ".$row['party_name'];
		$res = mysql_query("UPDATE tblpo SET po_status='U' WHERE po_id=".$oid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$_SESSION["stores_lname"]."','Recall','".$_SESSION["stores_uname"]."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="purchaseorder.php?action=new";</script>';
	}
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	$datePOrder=substr($_POST['poDate'],6,4)."-".substr($_POST['poDate'],3,2)."-".substr($_POST['poDate'],0,2);
	$dateDelivery=substr($_POST['deliveryDate'],6,4)."-".substr($_POST['deliveryDate'],3,2)."-".substr($_POST['deliveryDate'],0,2);
	/*-------------------------------*/
	$sql = mysql_query("SELECT party_name FROM party WHERE party_id=".$_POST['partyName']);
	$row = mysql_fetch_assoc($sql);
	$particulars = "From ".$row['party_name'];
	/*-------------------------------*/
	if($_POST['submit']=="update"){
        

        if($_POST['ActualComN']!=$_POST['companyName'])
	{
	 $sqlc = mysql_query("SELECT Max(po_no) as maxno FROM tblpo WHERE company_id=".$_POST['companyName']." AND po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."' ");		
	 $rowc = mysql_fetch_assoc($sqlc);
	 $pnoc = ($rowc["maxno"]==null ? 1 : $rowc["maxno"] + 1);
	
		$res = mysql_query("UPDATE tblpo SET po_no=".$pnoc.",po_date='".$datePOrder."',party_id=".$_POST['partyName'].",company_id=".$_POST['companyName'].",shipto=".$_POST['rdoShipto'].",shipping_id=".$_POST['shippingName'].",ship_method='".$_POST['shipMethod']."',ship_terms='".$_POST['shipTerms']."',delivery_date='".$dateDelivery."',delivery_at=".$_POST['location'].",vendor_ref='".$_POST['vendorRef']."',terms_condition='".$_POST['termsCondition']."', work_order='".$_POST['work_order']."' WHERE po_id=".$oid) or die(mysql_error());
	}
	else
	{
	    $res = mysql_query("UPDATE tblpo SET po_date='".$datePOrder."',party_id=".$_POST['partyName'].",company_id=".$_POST['companyName'].",shipto=".$_POST['rdoShipto'].",shipping_id=".$_POST['shippingName'].",ship_method='".$_POST['shipMethod']."',ship_terms='".$_POST['shipTerms']."',delivery_date='".$dateDelivery."',delivery_at=".$_POST['location'].",vendor_ref='".$_POST['vendorRef']."',terms_condition='".$_POST['termsCondition']."', work_order='".$_POST['work_order']."' WHERE po_id=".$oid) or die(mysql_error());
	} 

		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$_SESSION["stores_lname"]."','Change','".$_SESSION["stores_uname"]."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="poindent.php?action=new&oid='.$oid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblpo WHERE po_id=".$oid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblpo_item WHERE po_id=".$oid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblpo_dtm WHERE po_id=".$oid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePOrder."','Pur.Order','".date("Y-m-d")."','".$particulars."','".$_SESSION["stores_lname"]."','Delete','".$_SESSION["stores_uname"]."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="purchaseorder.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT po_id FROM tblpo WHERE po_date='".$datePOrder."' AND party_id=".$_POST['partyName']." AND company_id=".$_POST['companyName']." AND shipto=".$_POST['rdoShipto']." AND shipping_id=".$_POST['shippingName']." AND delivery_date='".$dateDelivery."' AND delivery_at=".$_POST['location']." AND vendor_ref='".$_POST['vendorRef']."'") or die(mysql_error());
		$row_order = mysql_fetch_assoc($sql);
		$count = mysql_num_rows($sql);
		if($count>5)  //if($count>0)
			$msg = "To much same data! can&prime;t insert into purchase order record.";
		else {
			$sql = mysql_query("SELECT Max(po_id) as maxid FROM tblpo");
			$row = mysql_fetch_assoc($sql);
			$oid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(po_no) as maxno FROM tblpo WHERE po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."' AND company_id=".$_POST['companyName']."");
			$row = mysql_fetch_assoc($sql);
			$pno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tblpo(po_id,po_date,po_no,party_id,company_id,shipto,shipping_id,delivery_date,delivery_at,vendor_ref,terms_condition,work_order) VALUES(".$oid.",'".$datePOrder."',".$pno.",".$_POST['partyName'].",".$_POST['companyName'].",".$_POST['rdoShipto'].",".$_POST['shippingName'].",'".$dateDelivery."',".$_POST['location'].",'".$_POST['vendorRef']."','".$_POST['termsCondition']."','".$_POST['work_order']."')";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">function show_message_porder_number(value1,value2){
				alert("P.O. No. = "+value2);
				window.location="poindent.php?action=new&oid="+value1;}
				show_message_porder_number('.$oid.','.$pno.');</script>';
//			header('Location:poindent.php?action=new&oid='.$oid);
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
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/tigra_hints.js"></script>
<script language="javascript" type="text/javascript">
function validate_porder()
{
	var err="";
	if(document.getElementById("poDate").value!=""){
		if(!checkdate(document.purchaseorder.poDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.purchaseorder.poDate,document.purchaseorder.endYear);
			if(no_of_days1 < 0){
				err += "* Purchase Order date wrongly selected. Please correct and submit again.\n";
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.purchaseorder.startYear,document.purchaseorder.poDate);
				if(no_of_days2 < 0){
					err += "* Purchase Order date wrongly selected. Please correct and submit again.\n";
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.purchaseorder.maxDate,document.purchaseorder.poDate);
					if(no_of_days3 < 0){
						err += "* Purchase Order date wrongly selected. Please correct and submit again.\n"+
						"Last PO date was "+document.getElementById("maxDate").value+", so lower date is not acceptable.\n";
					}
				}
			}
		}
	} else
		err += "* please input/select purchase order date!\n";
	if(document.getElementById("deliveryDate").value!=""){
		if(checkdate(document.purchaseorder.deliveryDate)){
			var no_of_days = getDaysbetween2Dates(document.purchaseorder.poDate,document.purchaseorder.deliveryDate);
			if(no_of_days < 0){
				err += "* Delivery order date wrongly selected. Please correct and submit again.\n";
			}
		}
	}
	if(document.getElementById("partyName").value==0)
		err += "* please select a party!\n";
	if(document.getElementById("companyName").value==0)
		err += "* please select a company!\n";
	if(document.getElementById("location").value==0)
		err += "* please select a delivery place (location)!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function get_combo_n_radio_value()
{
	var v1 = document.getElementById("companyName").value;
	var index = document.getElementById("companyName").selectedIndex;
	var v2 = 0;
	for(var i=0; i < document.purchaseorder.rdoShipto.length; i++){
		if(document.purchaseorder.rdoShipto[i].checked){
			v2 = document.purchaseorder.rdoShipto[i].value;
		}
	}
	if(v2==1){
		document.getElementById('ship2control').innerHTML = '<input name="shipName" id="shipName" maxlength="50" size="45" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="shippingName" id="shippingName" value=""/>';
		document.purchaseorder.shipName.value = document.purchaseorder.companyName.options[index].text;
		document.purchaseorder.shippingName.value = document.purchaseorder.companyName.value;
	} else if(v2==2 || v2==3){
		v1 = document.getElementById("shippingName").value;
	}
	getshipping_detail(v1,v2);
}

function listrange(me)
{
	document.getElementById("rf").value = me;
	paging_po("");
}

function paging_po(value1)
{
	if(document.getElementById("xson").value=="new"){
		window.location="purchaseorder.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("pagePO").value+"&tr="+document.getElementById("displayTotalRowsPO").value+"&pgi="+document.getElementById("pageind").value+"&tri="+document.getElementById("displayTotalRowsInd").value+"&rf="+document.getElementById("rf").value+value1;
	} else {
		window.location="purchaseorder.php?action="+document.getElementById("xson").value+"&oid="+document.getElementById("poid").value+"&pg="+document.getElementById("pagePO").value+"&tr="+document.getElementById("displayTotalRowsPO").value+"&pgi="+document.getElementById("pageind").value+"&tri="+document.getElementById("displayTotalRowsInd").value+"&rf="+document.getElementById("rf").value+value1;
	}
}

function firstpage_po()
{
	document.getElementById("pagePO").value = 1;
	paging_po("");
}

function previouspage_po()
{
	var cpage = parseInt(document.getElementById("pagePO").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("pagePO").value = cpage;
	}
	paging_po("");
}

function nextpage_po()
{
	var cpage = parseInt(document.getElementById("pagePO").value);
	if(cpage<parseInt(document.getElementById("totalPagePO").value)){
		cpage = cpage + 1;
		document.getElementById("pagePO").value = cpage;
	}
	paging_po("");
}

function lastpage_po()
{
	document.getElementById("pagePO").value = document.getElementById("totalPagePO").value;
	paging_po("");
}

function paging_poind()
{
	if(document.getElementById("xson").value=="new"){
		window.location="purchaseorder.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("pagePO").value+"&tr="+document.getElementById("displayTotalRowsPO").value+"&pgi="+document.getElementById("pageind").value+"&tri="+document.getElementById("displayTotalRowsInd").value;
	} else {
		window.location="purchaseorder.php?action="+document.getElementById("xson").value+"&oid="+document.getElementById("poid").value+"&pg="+document.getElementById("pagePO").value+"&tr="+document.getElementById("displayTotalRowsPO").value+"&pgi="+document.getElementById("pageind").value+"&tri="+document.getElementById("displayTotalRowsInd").value;
	}
}

function firstpage_poind()
{
	document.getElementById("pageind").value = 1;
	paging_poind();
}

function previouspage_poind()
{
	var cpage = parseInt(document.getElementById("pageind").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("pageind").value = cpage;
	}
	paging_poind();
}

function nextpage_poind()
{
	var cpage = parseInt(document.getElementById("pageind").value);
	if(cpage<parseInt(document.getElementById("totalPageind").value)){
		cpage = cpage + 1;
		document.getElementById("pageind").value = cpage;
	}
	paging_poind();
}

function lastpage_poind()
{
	document.getElementById("pageind").value = document.getElementById("totalPageind").value;
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


function FunAddParty()
{
var win = window.open("openaddpartyfile.php?action=new","AddPForm","menubar=no,scrollbars=yes,resizable=no,directories=no,width=900,height=500");
var timer = setInterval( function() { if(win.closed){ clearInterval(timer); 
window.location.href="purchaseorder.php?action=new"; } }, 1000);
}

function FunAddCom()
{
var win = window.open("openaddcomfile.php?action=new","AddPForm","menubar=no,scrollbars=yes,resizable=no,directories=no,width=900,height=500");
var timer = setInterval( function() { if(win.closed){ clearInterval(timer); 
window.location.href="purchaseorder.php?action=new"; } }, 1000);
}

</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="450px" width="875px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="purchaseorder"  method="post" onsubmit="return validate_porder()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>P.O. No.:</td>
			<td><input name="poNo" id="poNo" maxlength="15" size="20" readonly="true" value="<?php echo $po_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>P.O. Date:<span style="color:#FF0000">*</span></td>
			<td><input name="poDate" id="poDate" maxlength="10" size="10" value="<?php echo $po_date;?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'purchaseorder', 'controlname': 'poDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Party Name:<span style="color:#FF0000">*</span></td>
			<td><select name="partyName" id="partyName" style="width:270px" onchange="get_partydetail_of_po(this.value)" ><option value="0">-- Select --</option><?php 
			$sql_party=mysql_query("SELECT * FROM party ORDER BY party_name");
			while($row_party=mysql_fetch_array($sql_party)){
				if($row_party["party_id"]==$party_id)
					echo '<option selected value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
				else
					echo '<option value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
			}?>
			</select>&nbsp;&nbsp;<img src="images/Plus.gif" border="0" onclick="FunAddParty()"/></td>
			
			<td class="th" nowrap>Company Name:<span style="color:#FF0000">*</span></td>
			<td><input type="hidden" name="ActualComN" id="ActualComN" value="<?php echo $company_id; ?>" />
<select name="companyName" id="companyName" style="width:270px" onchange="get_combo_n_radio_value()"><option value="0">-- Select --</option><?php 
			$sql_company=mysql_query("SELECT * FROM company ORDER BY company_name");
			while($row_company=mysql_fetch_array($sql_company)){
				if($row_company["company_id"]==$company_id)
					echo '<option selected value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
				else
					echo '<option value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
			}?>
			</select>&nbsp;&nbsp;<img src="images/Plus.gif" border="0" onclick="FunAddCom()"/></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="45" readonly="true" value="<?php echo $address1;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Ship To:</td>
			<?php if($shipto==1){?>
					<td><input type="radio" name="rdoShipto" id="rdoShipto" checked="true" value="1" onclick="get_combo_n_radio_value()">&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" value="2" onclick="get_ship_control(this.value)">&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" value="3" onclick="get_ship_control(this.value)">&nbsp;Other</td>
			<?php } elseif($shipto==2){?>
					<td><input type="radio" name="rdoShipto" id="rdoShipto" value="1" onclick="get_combo_n_radio_value()">&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" checked="true" value="2" onclick="get_ship_control(this.value)">&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" value="3" onclick="get_ship_control(this.value)">&nbsp;Other</td>
			<?php } elseif($shipto==3){?>
					<td><input type="radio" name="rdoShipto" id="rdoShipto" value="1" onclick="get_combo_n_radio_value()">&nbsp;Itself&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" value="2" onclick="get_ship_control(this.value)">&nbsp;At Branch&nbsp;&nbsp;&nbsp;<input type="radio" name="rdoShipto" id="rdoShipto" checked="true" value="3" onclick="get_ship_control(this.value)">&nbsp;Other</td>
			<?php }?>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="45" readonly="true" value="<?php echo $address2;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Shipping Name:</td>
			<?php if($shipto==1){?>
				<td><span id="ship2control"><input name="shipName" id="shipName" maxlength="50" size="45" readonly="true" value="<?php echo $ship_name;?>" style="background-color:#E7F0F8; color:#0000FF"><input type="hidden" name="shippingName" id="shippingName" value="<?php echo $shipping_id;?>"/></span></td>
			<?php } elseif($shipto==2){?>
				<td><select name="shippingName" id="shippingName" style="width:300px"><option value="0">-- Select --</option><?php 
				$sql_ship=mysql_query("SELECT * FROM company ORDER BY company_name");
				while($row_ship=mysql_fetch_array($sql_ship)){
					if($row_ship["company_id"]==$shipping_id)
						echo '<option selected value="'.$row_ship["company_id"].'">'.$row_ship["company_name"].'</option>';
					else
						echo '<option value="'.$row_ship["company_id"].'">'.$row_ship["company_name"].'</option>';
				}?>
				</select></td>
			<?php } elseif($shipto==3){?>
				<td><select name="shippingName" id="shippingName" style="width:300px"><option value="0">-- Select --</option><?php 
				$sql_ship=mysql_query("SELECT * FROM party ORDER BY party_name");
				while($row_ship=mysql_fetch_array($sql_ship)){
					if($row_ship["party_id"]==$shipping_id)
						echo '<option selected value="'.$row_ship["party_id"].'">'.$row_ship["party_name"].'</option>';
					else
						echo '<option value="'.$row_ship["party_id"].'">'.$row_ship["party_name"].'</option>';
				}?>
				</select></td>
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
			<td><input name="vendorRef" id="vendorRef" maxlength="30" size="45" value="<?php echo $vendor_ref;?>" ></td>
			
			<td class="th" nowrap>Shipping Method:</td>
			<td><input name="shipMethod" id="shipMethod" maxlength="50" size="45" value="<?php echo $ship_method;?>" ></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Terms &amp; Conditions:</td>
			<td rowspan="4"><textarea name="termsCondition" id="termsCondition" cols="35" rows="7"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["terms_condition"];} else { echo $termscondition;}?></textarea></td>
			
			<td class="th" nowrap>Shipping Terms:</td>
			<td><input name="shipTerms" id="shipTerms" maxlength="50" size="45" value="<?php echo $ship_terms;?>" ></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td class="th" nowrap>Delivery Date:<span style="color:#FF0000">*</span></td>
			<td><input name="deliveryDate" id="deliveryDate" maxlength="10" size="10" value="<?php echo $delivery_date;?>" >&nbsp;<script language="JavaScript">new tcal ({'formname': 'purchaseorder', 'controlname': 'deliveryDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;</td>
			<td class="th" nowrap>Delivery At:<span style="color:#FF0000">*</span></td>
			<td><select name="location" id="location" style="width:300px" onchange="get_max_date(this.value, document.getElementById('startYear').value, document.getElementById('endYear').value, document.forms[0].name)"><option value="0">-- Select --</option><?php 
			$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
			while($row_location=mysql_fetch_array($sql_location)){
				if($row_location["location_id"]==$delivery_at)
					echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
				else
					echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
			}?>
			</select></td>
		</tr>
		
		<tr class="Controls">
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>"/><input type="hidden" name="maxDate" id="maxDate" value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>"/></td>
			<td>Work Order</td>
			<td><select name="work_order" id="work_order" style="width:50px">
			<option value="<?php echo $work_order; ?>"><?php if($work_order=='N'){echo 'No';}else{echo 'Yes';} ?></option><option value="<?php if($work_order=='N'){echo 'Y';}else{echo 'N';} ?>"><?php if($work_order=='N'){echo 'Yes';}else{echo 'No';} ?></option></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['po1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['po1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
			&nbsp;&nbsp;<a href="javascript:document.purchaseorder.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='poindent.php?action=new&oid=<?php echo $oid;?>'"><img src="images/next.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='purchaseorder.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='purchaseorder.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ ?>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Purchase Order - [Indent Selected]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" cellspacing="0" cellpadding="0">
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
			&nbsp;&nbsp;<a href="javascript:window.location='purchaseorder.php?action=new'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<?php } ?>
<tr>
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Pending Purchase Order List</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
<!-- HTML code for the hint, make sure the hint has unique ID, it is positioned absolutely and initially invisible.
	The same hint element will be reused for all cells of the table. This approach simplifies and optimizes the
	implementation of the dynamically generated pages. -->

<div id="reusableHint" style="position:absolute;z-index:1;visibility:hidden;padding:10px;background-color:#FFFFCC;border:2px solid #CCCC00;"></div>
<!-- End of the HTML code for the hint -->

		<table class="Grid" cellspacing="0" cellpadding="0">
		<?php 
		if($_SESSION["stores_utype"]=="A" || $_SESSION["stores_utype"]=="S"){
		echo '<tr class="Caption">';
			echo '<th align="right" colspan="7">List Range:&nbsp;&nbsp;<select name="rangeFor" id="rangeFor" style="width:110px" onchange="listrange(this.value)">';
			if(isset($_REQUEST['rf'])){
				if($_REQUEST['rf']=="U"){
					echo '<option selected value="U">Unsent items</option><option value="S">Sent items</option>';
				} elseif($_REQUEST['rf']=="S"){
					echo '<option value="U">Unsent items</option><option selected value="S">Sent items</option>';
				}
			} else {
				echo '<option selected value="U">Unsent items</option><option value="S">Sent items</option>';
			}
		echo '</tr>';
		} ?>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="15%">P.O. No.</th>
			<th width="15%">P.O. Date</th>
			<th width="25%">Party Name</th>
			<th width="25%">Company Name</th>
			<?php 
			if(isset($_REQUEST['rf'])){
				if($_REQUEST['rf']=="U"){
					echo '<th width="5%">Edit</th>';
				} elseif($_REQUEST['rf']=="S"){
					echo '<th width="5%">Recall</th>';
				}
			} else {
				echo '<th width="5%">Edit</th>';
			}
			?>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$start1=0;
		if(isset($_REQUEST['rf'])){$rangeFor=$_REQUEST['rf'];} else {$rangeFor="U";}
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end1=$_REQUEST['tr'];} else {$end1=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start1=($_REQUEST['pg']-1)*$end1;}
		
		$i = $start1;
		$sql = "SELECT tblpo.*, party_name, company_name FROM tblpo INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id WHERE po_status='".$rangeFor."' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')";
		$sql .= " ORDER BY po_date, po_id ";
		$sql .= " LIMIT ".$start1.",".$end1;
		$sql_order = mysql_query($sql) or die(mysql_error());
		while($row_order=mysql_fetch_array($sql_order)){
			$sql_item = mysql_query("SELECT tblpo_item.*, item_name, unit_name FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE po_id=".$row_order['po_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['qnty'].' '.$row_item['unit_name'].'</td><td>'.$row_item['rate'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			if($row_order['po_status']=='S'){
				$edit_ref = "purchaseorder.php?action=recall&oid=".$row_order['po_id'];
			} elseif($row_order['po_status']=='U'){
				$edit_ref = "purchaseorder.php?action=edit&oid=".$row_order['po_id'];
			}
			$delete_ref = "purchaseorder.php?action=delete&oid=".$row_order['po_id'];
			
			$po_number = ($row_order['po_no']>999 ? $row_order['po_no'] : ($row_order['po_no']>99 && $row_order['po_no']<1000 ? "0".$row_order['po_no'] : ($row_order['po_no']>9 && $row_order['po_no']<100 ? "00".$row_order['po_no'] : "000".$row_order['po_no'])));
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is P.O. number '.$po_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$po_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_order['po_date'])).'</td><td>'.$row_order['party_name'].'</td><td>'.$row_order['company_name'].'</td>';
			if($row_user['po2']==1){
				if($row_order['po_status']=='S'){
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/undo.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				} elseif($row_order['po_status']=='U'){
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				}
			} elseif($row_user['po2']==0)
				echo '<td align="center">&nbsp;</td>';
			if($row_user['po3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po3']==0)
				echo '<td align="center">&nbsp;</td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="7" align="center">
			<?php 
			$sql_total_po = mysql_query("SELECT * FROM tblpo WHERE po_status='".$rangeFor."' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')") or die(mysql_error());
			$tot_row_po=mysql_num_rows($sql_total_po);
			$total_page_po=0;
			$strg = "";
			echo 'Total <span style="color:red">'.$tot_row_po.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPO" id="showPO" value="Show:" onclick="paging_po('.$strg.')" />&nbsp;&nbsp;<input name="displayTotalRowsPO" id="displayTotalRowsPO" value="'.$end1.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="poid" id="poid" value="'.$oid.'" />';
			if($tot_row_po>$end1){
				echo "Page number: ";
				$total_page_po=ceil($tot_row_po/$end1);
				echo '<select name="pagePO" id="pagePO" onchange="paging_po('.$strg.')" style="vertical-align:middle">';
				for($i=1;$i<=$total_page_po;$i++)
				{
					if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)
						echo '<option selected value="'.$i.'">'.$i.'</option>';
					else
						echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}else {
				echo '<input type="hidden" name="pagePO" id="pagePO" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			echo '<input type="hidden" name="totalPagePO" id="totalPagePO" value="'.$total_page_po.'" /><input type="hidden" name="rf" id="rf" value="'.$rangeFor.'" />';
			if($total_page_po>1 && $_REQUEST["pg"]>1)
				echo '<input type="button" name="fistPagePO" id="firstPagePO" value=" << " onclick="firstpage_po()" />&nbsp;&nbsp;<input type="button" name="prevPagePO" id="prevPagePO" value=" < " onclick="previouspage_po()" />&nbsp;&nbsp;';
			if($total_page_po>1 && $_REQUEST["pg"]<$total_page_po)
				echo '<input type="button" name="nextPagePO" id="nextPagePO" value=" > " onclick="nextpage_po()" />&nbsp;&nbsp;<input type="button" name="lastPagePO" id="lastPagePO" value=" >> " onclick="lastpage_po()" />';
			?>
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
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Pending Indent List</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="20%">Indent No.</th>
			<th width="10%">Date</th>
			<th width="25%">Indent From</th>
			<th width="25%">Indent By</th>
			<th width="10%">Supply Date</th>
			<th width="5%">&nbsp;</th>
		</tr>
		
		<?php //echo 'tri='.$_REQUEST['tri'];
		$start1=0;
		if(isset($_REQUEST['tri']) && $_REQUEST['tri']!=""){$end1=$_REQUEST['tri'];} else {$end1=PAGING;}
		if(isset($_REQUEST['pgi']) && $_REQUEST['pgi']!=""){$start1=($_REQUEST['pgi']-1)*$end1;}
		$i = $start1;
		
		$sql_ind = mysql_query("SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND appr_status='S' AND indent_id IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1) ORDER BY location_name, indent_date, indent_id LIMIT ".$start1.",".$end1) or die(mysql_error());
		while($row_ind=mysql_fetch_array($sql_ind)){
			$sql_item = mysql_query("SELECT tbl_indent_item.*, item_name, unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$row_ind['indent_id']." AND item_ordered='N' AND aprvd_status=1 ORDER BY seq_no") or die(mysql_error());
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
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is indent number '.$indent_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["indent_date"])).'</td><td>'.$row_ind['location_name'].'</td><td>'.$row_ind['staff_name'].'</td><td align="center">'.date("d-m-Y",strtotime($row_ind["supply_date"])).'</td><td>&nbsp;</td>';
/*			if($row_user['po2']==1)
				echo '<td align="center"><a href="'.$select_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['po2']==0)
				echo '<td align="center"><a href="'.$select_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';*/
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="7" align="center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM tbl_indent WHERE (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND appr_status='S' AND indent_id IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1)") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showInd" id="showInd" value="Show:" onclick="paging_poind()" />&nbsp;&nbsp;<input name="displayTotalRowsInd" id="displayTotalRowsInd" value="'.$end1.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end1){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end1);
				echo '<select name="pageind" id="pageind" onchange="paging_poind()" style="vertical-align:middle">';
				for($i=1;$i<=$total_page;$i++)
				{
					if(isset($_REQUEST["pgi"]) && $_REQUEST["pgi"]==$i)
						echo '<option selected value="'.$i.'">'.$i.'</option>';
					else
						echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}else {
				echo '<input type="hidden" name="pageind" id="pageind" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			echo '<input type="hidden" name="totalPageind" id="totalPageind" value="'.$total_page.'" />';
			if($total_page>1 && $_REQUEST["pgi"]>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_poind()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_poind()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pgi"]<$total_page)
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
