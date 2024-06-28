<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
if(isset($_POST['show'])){
	$fromDate=substr($_POST['rangeFrom'],6,4)."-".substr($_POST['rangeFrom'],3,2)."-".substr($_POST['rangeFrom'],0,2);
	$toDate=substr($_POST['rangeTo'],6,4)."-".substr($_POST['rangeTo'],3,2)."-".substr($_POST['rangeTo'],0,2);
	$sd = strtotime($fromDate);
	$ed = strtotime($toDate);
} elseif(isset($_REQUEST['sd'])){
	$sd = $_REQUEST['sd'];
	$ed = $_REQUEST['ed'];
	$fromDate = date("Y-m-d",$sd);
	$toDate = date("Y-m-d",$ed);
} else {
	$sd = strtotime(date("Y-m-d"));
	$ed = strtotime(date("Y-m-d"));
	$fromDate = date("Y-m-d");
	$toDate = date("Y-m-d");
}
/*----------------------------------------*/
$msg = "";
$did = "";
$dc_number = "";
$dc_date = date("d-m-Y");
$location_id = 0;
$po_id = 0;
$po_date = "";
$party_name = "";
$company_name = "";
$address1 = "";
$address2 = "";
$address3 = "";
$location_name = "";
$delivery_date = "";
$city_name = "";
$state_name = "";
/*----------------------------------------*/
if(isset($_REQUEST['did'])){
	$did = $_REQUEST['did'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM tbldelivery1 WHERE dc_id=".$did) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$dc_number = ($row['dc_no']>999 ? $row['dc_no'] : ($row['dc_no']>99 && $row['dc_no']<1000 ? "0".$row['dc_no'] : ($row['dc_no']>9 && $row['dc_no']<100 ? "00".$row['dc_no'] : "000".$row['dc_no'])));
		$dc_date = date("d-m-Y",strtotime($row["dc_date"]));
		$po_id = $row["po_id"];
		/*----------------------------------------*/
		$sqlPO = mysql_query("SELECT * FROM tblpo WHERE po_id=".$po_id) or die(mysql_error());
		$rowPO = mysql_fetch_assoc($sqlPO);
		$po_date = date("d-m-Y",strtotime($rowPO["po_date"]));
		$location_id = $rowPO["delivery_at"];
		$delivery_date = date("d-m-Y",strtotime($rowPO["delivery_date"]));
		/*----------------------------------------*/
		$sqlParty = mysql_query("SELECT party.*, city_name, state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$rowPO['party_id']) or die(mysql_error());
		$rowParty = mysql_fetch_assoc($sqlParty);
		$party_name = $rowParty["party_name"];
		$address1 = $rowParty["address1"];
		$address2 = $rowParty["address2"];
		$address3 = $rowParty["address3"];
		$city_name = $rowParty["city_name"];
		$state_name = $rowParty["state_name"];
		/*----------------------------------------*/
		$sqlComp = mysql_query("SELECT * FROM company WHERE company_id=".$rowPO['company_id']) or die(mysql_error());
		$rowComp = mysql_fetch_assoc($sqlComp);
		$company_name = $rowComp["company_name"];
		/*----------------------------------------*/
		$sqlLoc = mysql_query("SELECT * FROM location WHERE location_id=".$location_id) or die(mysql_error());
		$rowLoc = mysql_fetch_assoc($sqlLoc);
		$location_name = $rowLoc["location_name"];
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateDelivery=substr($_POST['dcDate'],6,4)."-".substr($_POST['dcDate'],3,2)."-".substr($_POST['dcDate'],0,2);
	$particulars = "From ".$_POST['partyName'];
	/*--------------------------------*/
	$sql = mysql_query("SELECT dc_id FROM tbldelivery1 WHERE dc_date='".$dateDelivery."' AND po_id=".$_POST['poNo']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		$sql = "UPDATE tbldelivery1 SET dc_date='".$dateDelivery."', po_id=".$_POST['poNo']." WHERE dc_id=".$did;
		$res = mysql_query($sql) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($did>999 ? $did : ($did>99 && $did<1000 ? "0".$did : ($did>9 && $did<100 ? "00".$did : "000".$did)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateDelivery."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="deliveryitem.php?action=new&did='.$did.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tbldelivery1 WHERE dc_id=".$did) or die(mysql_error());
		$res = mysql_query("DELETE FROM tbldelivery2 WHERE dc_id=".$did) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($did>999 ? $did : ($did>99 && $did<1000 ? "0".$did : ($did>9 && $did<100 ? "00".$did : "000".$did)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateDelivery."','Dlry.Conf.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="deliveryconfirm.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into delivery confirmation record.";
		else {
			$sql = mysql_query("SELECT Max(dc_id) as maxid FROM tbldelivery1");
			$row = mysql_fetch_assoc($sql);
			$did = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(dc_no) as maxno FROM tbldelivery1 WHERE (dc_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
			$row = mysql_fetch_assoc($sql);
			$dno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tbldelivery1(dc_id, dc_date, dc_no, po_id) VALUES(".$did.",'".$dateDelivery."',".$dno.",".$_POST['poNo'].")";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">function show_message_dc_number(value1,value2){
				alert("DC No. = "+value2);
				window.location="deliveryitem.php?action=new&did="+value1;}
				show_message_dc_number('.$did.','.$dno.');</script>';
		}
	}
}
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    function validate_delivery() {
        if (document.getElementById("poNo").value == 0) {
            alert("* Please select purchase order number!");
            return false;
        }
        if (document.getElementById("dcDate").value != "") {
            if (!checkdate(document.deliveryconfirm.dcDate)) {
                return false;
            } else {
                var no_of_days1 = getDaysbetween2Dates(document.deliveryconfirm.dcDate, document.deliveryconfirm
                    .endYear);
                if (no_of_days1 < 0) {
                    alert("* DC Date wrongly selected. Please correct and submit again.");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.deliveryconfirm.startYear, document.deliveryconfirm
                        .dcDate);
                    if (no_of_days2 < 0) {
                        alert("* DC Date wrongly selected. Please correct and submit again.");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.deliveryconfirm.poDate, document.deliveryconfirm
                            .dcDate);
                        if (no_of_days3 < 0) {
                            alert("* DC Date can't before PO date. Please correct and submit again.");
                            return false;
                        } else {
                            var no_of_days4 = getDaysbetween2Dates(document.deliveryconfirm.maxDate, document
                                .deliveryconfirm.dcDate);
                            if (no_of_days4 < 0) {
                                alert("* DC Date wrongly selected. Please correct and submit again.\n" +
                                    "Last DC Date was " + document.getElementById("maxDate").value +
                                    ", so lower date is not acceptable.");
                                return false;
                            }
                        }
                    }
                }
            }
        } else {
            alert("* please select/input DC Date!");
            return false;
        }
        return true;
    }

    function validate_deliverylist() {
        if (checkdate(document.dclist.rangeFrom)) {
            if (checkdate(document.dclist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.dclist.rangeFrom, document.dclist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function paging_dc() {
        if (document.getElementById("xson").value == "new") {
            window.location = "deliveryconfirm.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        } else {
            window.location = "deliveryconfirm.php?action=" + document.getElementById("xson").value + "&did=" + document
                .getElementById("dcid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&sd=" + document.getElementById("sd").value + "&ed=" +
                document.getElementById("ed").value;
        }
    }

    function firstpage_dc() {
        document.getElementById("page").value = 1;
        paging_dc();
    }

    function previouspage_dc() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_dc();
    }

    function nextpage_dc() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_dc();
    }

    function lastpage_dc() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_dc();
    }

    var HINTS_CFG = {
        'wise': true, // don't go off screen, don't overlap the object in the document
        'margin': 10, // minimum allowed distance between the hint and the window edge (negative values accepted)
        'gap': -10, // minimum allowed distance between the hint and the origin (negative values accepted)
        'align': 'brtl', // align of the hint and the origin (by first letters origin's top|middle|bottom left|center|right to hint's top|middle|bottom left|center|right)
        'show_delay': 100, // a delay between initiating event (mouseover for example) and hint appearing
        'hide_delay': 0 // a delay between closing event (mouseout for example) and hint disappearing
    };
    var myHint = new THints(null, HINTS_CFG);

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
        <table align="center" cellspacing="0" cellpadding="0" height="400px" width="950px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <form name="deliveryconfirm" method="post" onsubmit="return validate_delivery()">
                        <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Delivery Confirmation - [ Main ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" width="10%" nowrap>DC No.:</td>
                                            <td width="40%"><input name="dcNo" id="dcNo" maxlength="15" size="20"
                                                    readonly="true" value="<?php echo $dc_number; ?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" width="15%" nowrap>DC Date:<span
                                                    style="color:#FF0000">*</span></td>
                                            <td width="35%"><input name="dcDate" id="dcDate" maxlength="10" size="10"
                                                    value="<?php echo $dc_date;?>">&nbsp;<script language="JavaScript">
                                                new tcal({
                                                    'formname': 'deliveryconfirm',
                                                    'controlname': 'dcDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>P.O. No.:<span style="color:#FF0000">*</span></td>
                                            <td>
                                                <select name="poNo" id="poNo" style="width:300px"
                                                    onchange="get_podetails_of_dc(this.value)">
                                                    <option value="0">-- Select --</option> <?php 
			
                        
                        $sql_order = mysql_query("SELECT Distinct tblpo.po_id, po_no, CCode FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN company ON tblpo.company_id = company.company_id WHERE (po_status='S' AND order_received='N') OR tblpo.po_id=".$po_id." ORDER BY CCode,po_date, po_no") or die(mysql_error());
			while($row_order=mysql_fetch_array($sql_order)){
				$po_number = ($row_order['po_no']>999 ? $row_order['po_no'] : ($row_order['po_no']>99 && $row_order['po_no']<1000 ? "0".$row_order['po_no'] : ($row_order['po_no']>9 && $row_order['po_no']<100 ? "00".$row_order['po_no'] : "000".$row_order['po_no'])));
				if($row_order["po_id"]==$po_id)
					echo '<option selected value="'.$row_order["po_id"].'">'.$row_order['CCode'].'-'.$po_number.'</option>';
				else
					echo '<option value="'.$row_order["po_id"].'">'.$row_order['CCode'].'-'.$po_number.'</option>';
			}?>
                                                </select>
                                            </td>

                                            <td class="th" nowrap>P.O. Date:</td>
                                            <td><input name="poDate" id="poDate" maxlength="10" size="10"
                                                    readonly="true" value="<?php echo $po_date;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Party Name:</td>
                                            <td><input name="partyName" id="partyName" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $party_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Company Name:</td>
                                            <td><input name="companyName" id="companyName" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $company_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-1:</td>
                                            <td><input name="address1" id="address1" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $address1;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Delivery At:</td>
                                            <td><input name="deliveryAt" id="deliveryAt" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $location_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"><input type="hidden"
                                                    name="location" id="location" value="<?php echo $location_id;?>" />
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-2:</td>
                                            <td><input name="address2" id="address2" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $address2;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Required Date:</td>
                                            <td><input name="deliveryDate" id="deliveryDate" maxlength="10" size="10"
                                                    readonly="true" value="<?php echo $delivery_date;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-3:</td>
                                            <td><input name="address3" id="address3" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $address3;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>City:</td>
                                            <td><input name="cityName" id="cityName" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $city_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>State:</td>
                                            <td><input name="stateName" id="stateName" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $state_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td>&nbsp;<input type="hidden" name="startYear" id="startYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                                                    type="hidden" name="endYear" id="endYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /><input
                                                    type="hidden" name="maxDate" id="maxDate"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" />
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['po1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['po1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.deliveryconfirm.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='deliveryitem.php?action=new&did=<?php echo $did;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='deliveryconfirm.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='deliveryconfirm.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<img src="images/back.gif" width="72" height="22"
                                                    style="display:inline; cursor:hand;" border="0"
                                                    onclick="window.location='menu.php'" />
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
                    <form name="dclist" method="post" onsubmit="return validate_deliverylist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Delivery Confirmation - [ List ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <!-- HTML code for the hint, make sure the hint has unique ID, it is positioned absolutely and initially invisible.
	The same hint element will be reused for all cells of the table. This approach simplifies and optimizes the
	implementation of the dynamically generated pages. -->

                                    <div id="reusableHint"
                                        style="position:absolute;z-index:1;visibility:hidden;padding:10px;background-color:#FFFFCC;border:2px solid #CCCC00;">
                                    </div>
                                    <!-- End of the HTML code for the hint -->

                                    <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Caption">
                                            <th align="right" colspan="9">List Range From:&nbsp;&nbsp;<input
                                                    name="rangeFrom" id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'dclist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'dclist',
                                                    'controlname': 'rangeTo'
                                                });
                                                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image"
                                                    name="search" src="images/search.gif" width="82" height="22"
                                                    alt="search"><input type="hidden" name="show" value="show" /><input
                                                    type="hidden" name="sd" id="sd" value="<?php echo $sd;?>" /><input
                                                    type="hidden" name="ed" id="ed" value="<?php echo $ed;?>" /></th>
                                        </tr>
                                        <tr class="Caption">
                                            <th width="5%">Sl.No.</th>
                                            <th width="10%">DC No.</th>
                                            <th width="10%">DC Date</th>
                                            <th width="10%">PO No.</th>
                                            <th width="10%">PO Date</th>
                                            <th width="25%">Party Name</th>
                                            <th width="25%">Company Name</th>
                                            <th width="3%">Edit</th>
                                            <th width="2%">Del</th>
                                        </tr>

                                        <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql = "SELECT * FROM tbldelivery1 WHERE dc_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY dc_date, dc_id LIMIT ".$start.",".$end;
		//print_r($sql);die;
		$sql_dcnf = mysql_query($sql) or die(mysql_error());
		while($row_dcnf=mysql_fetch_array($sql_dcnf)){
			$sql_item = mysql_query("SELECT tbldelivery2.*,item_name,unit_name FROM tbldelivery2 INNER JOIN item ON tbldelivery2.item_id = item.item_id INNER JOIN unit ON tbldelivery2.unit_id = unit.unit_id WHERE dc_id=".$row_dcnf['dc_id']." ORDER BY seq_no") or die(mysql_error());

			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['delivery_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "deliveryconfirm.php?action=delete&did=".$row_dcnf['dc_id']."&sd=".$sd."&ed=".$ed;
			$edit_ref = "deliveryconfirm.php?action=edit&did=".$row_dcnf['dc_id']."&sd=".$sd."&ed=".$ed;
			
			$dc_number = ($row_dcnf['dc_no']>999 ? $row_dcnf['dc_no'] : ($row_dcnf['dc_no']>99 && $row_dcnf['dc_no']<1000 ? "0".$row_dcnf['dc_no'] : ($row_dcnf['dc_no']>9 && $row_dcnf['dc_no']<100 ? "00".$row_dcnf['dc_no'] : "000".$row_dcnf['dc_no'])));
			
			$sqlPONum = mysql_query("SELECT * FROM tblpo WHERE po_id=".$row_dcnf['po_id']) or die(mysql_error());
			$rowPONum = mysql_fetch_assoc($sqlPONum);
			$po_number = ($rowPONum['po_no']>999 ? $rowPONum['po_no'] : ($rowPONum['po_no']>99 && $rowPONum['po_no']<1000 ? "0".$rowPONum['po_no'] : ($rowPONum['po_no']>9 && $rowPONum['po_no']<100 ? "00".$rowPONum['po_no'] : "000".$rowPONum['po_no'])));
			
			$sqlParty = mysql_query("SELECT * FROM party WHERE party_id=".$rowPONum['party_id']) or die(mysql_error());
			$rowParty = mysql_fetch_assoc($sqlParty);
			
			$sqlCompany = mysql_query("SELECT * FROM company WHERE company_id=".$rowPONum['company_id']) or die(mysql_error());
			$rowCompany = mysql_fetch_assoc($sqlCompany);
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is DC Number '.$dc_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$dc_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_dcnf['dc_date'])).'</td><td>'.$po_number.'</td><td align="center">'.date("d-m-Y",strtotime($rowPONum['po_date'])).'</td><td>'.$rowParty['party_name'].'</td><td>'.$rowCompany['company_name'].'</td>';
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

                                        <tr class="Footer">
                                            <td colspan="9" align="center">
                                                <?php 
			$sql_total = mysql_query("SELECT * FROM tbldelivery1 WHERE dc_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_dc()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="dcid" id="dcid" value="'.$did.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_dc()" style="vertical-align:middle">';
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
			if($total_page>1 && $pg>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_dc()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_dc()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_dc()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_dc()" />';
			?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>