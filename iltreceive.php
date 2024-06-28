<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT ilt1,ilt2,ilt3,ilt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
if(isset($_POST['rangeFrom'])){
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
/*--------------------------------*/
$msg = "";
$mid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$ilt_number = "";
$ilt_no = 0;
$ilt_date = "";
$receive_date = date("d-m-Y");
$receive_by = 0;
$despatch_mode = "";
$sourceLocation = "";
$destinationLocation = "";
$staff_name = "";
$vehicle_num = "";
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, staff_name FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id WHERE ilt_id=".$mid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$location_id = $row['receive_at'];
		$ilt_no = $row['ilt_no'];
		$ilt_number = ($ilt_no>999 ? $ilt_no : ($ilt_no>99 && $ilt_no<1000 ? "0".$ilt_no : ($ilt_no>9 && $ilt_no<100 ? "00".$ilt_no : "000".$ilt_no)));
		if($row['ilt_prefix']!=null){$ilt_number = $row['ilt_prefix']."/".$ilt_number;}
		$ilt_date = date("d-m-Y",strtotime($row["ilt_date"]));
		$receive_date = date("d-m-Y",strtotime($row["receive_date"]));
		$sourceLocation = $row['sourceLocation'];
		$destinationLocation = $row['destinationLocation'];
		$staff_name = $row['staff_name'];
		$receive_by = $row["receive_by"];
		if($row['despatch_mode']==1){$despatch_mode = "Hand Delivery";} elseif($row['despatch_mode']==2){$despatch_mode = "By Vehicle";}
		$vehicle_num = $row['vehicle_num'];
	} elseif($_REQUEST["action"]=="recall"){
		$sql = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id WHERE ilt_id=".$mid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$dateILT = $row['receive_date'];
		$particulars = "From ".$row['sourceLocation']." To ".$row['destinationLocation'];
		$res = mysql_query("UPDATE tblilt1 SET receive_status='U' WHERE ilt_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Recall','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="iltreceive.php";</script>';
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$dateILT=substr($_POST['receiveDate'],6,4)."-".substr($_POST['receiveDate'],3,2)."-".substr($_POST['receiveDate'],0,2);
	$particulars = "From ".$row['sourceLocation']." To ".$row['destinationLocation'];
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblilt1 SET receive_date='".$dateILT."', receive_by=".$_POST['staffName']." WHERE ilt_id=".$mid) or die(mysql_error());
		$res = mysql_query("UPDATE stock_register SET entry_date='".$dateILT."',location_id=".$location_id." WHERE entry_mode='T+' AND entry_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="iltritem.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("UPDATE tblilt1 SET receive_by=0, receive_status='U' WHERE ilt_id=".$mid) or die(mysql_error());
		$res = mysql_query("UPDATE tblilt2 SET receive_qnty=0 WHERE ilt_id=".$mid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='T+' AND entry_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Rcvd.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">function show_message_ilt_number(value1){
			alert("ILT Receipt No. = "+value1+"\n successfully deleted....");
			window.location="iltreceive.php";}
			show_message_ilt_number('.$ilt_no.');</script>';
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
    function validate_iltdata() {
        var err = "";
        if (document.getElementById("receiveDate").value != "") {
            if (!checkdate(document.iltreceive.receiveDate)) {
                return false;
            } else {
                var no_of_days = getDaysbetween2Dates(document.iltreceive.despatchDate, document.iltreceive
                .receiveDate);
                if (no_of_days < 0) {
                    err += "* Receiving date wrongly selected. Please correct and submit again.\n";
                }
            }
        } else if (document.getElementById("receiveDate").value == "")
            err = "* please input/select receive date!\n";
        if (document.getElementById("staffName").value == 0)
            err += "* please select a staff, by whom the material being received!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function validate_iltlist() {
        if (checkdate(document.iltlist.rangeFrom)) {
            if (checkdate(document.iltlist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.iltlist.rangeFrom, document.iltlist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function listrange(me) {
        document.getElementById("rf").value = me;
        //	paging_iltr();
    }

    function paging_iltr() {
        if (document.getElementById("xson").value == "new") {
            window.location = "iltreceive.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&rf=" +
                document.getElementById("rf").value + "&sd=" + document.getElementById("sd").value + "&ed=" + document
                .getElementById("ed").value;
        } else {
            window.location = "iltreceive.php?action=" + document.getElementById("xson").value + "&mid=" + document
                .getElementById("miid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&rf=" + document.getElementById("rf").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        }
    }

    function firstpage_iltr() {
        document.getElementById("page").value = 1;
        paging_iltr();
    }

    function previouspage_iltr() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_iltr();
    }

    function nextpage_iltr() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_iltr();
    }

    function lastpage_iltr() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_iltr();
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
        <table align="center" cellspacing="0" cellpadding="0" height="250px" width="875px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <form name="iltreceive" method="post" onsubmit="return validate_iltdata()">
                        <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Inter Location Transfer - [ Receive Main ]</strong>
                                            </td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" nowrap>ILT No.:</td>
                                            <td><input name="iltNo" id="iltNo" maxlength="15" size="20" readonly="true"
                                                    value="<?php echo $ilt_number; ?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Despatch Date:</td>
                                            <td><input name="despatchDate" id="despatchDate" maxlength="10" size="10"
                                                    readonly="true" value="<?php echo $ilt_date;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th">Receive Date:<span style="color:#FF0000">*</span></td>
                                            <td><input name="receiveDate" id="receiveDate" maxlength="10" size="10"
                                                    value="<?php echo $receive_date;?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'iltreceive',
                                                    'controlname': 'receiveDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Despatch From:</td>
                                            <td><input name="despatchFrom" id="despatchFrom" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $sourceLocation;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th">Destination:</td>
                                            <td><input name="destination" id="destination" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $destinationLocation;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Despatched By:</td>
                                            <td><input name="despatchBy" id="despatchBy" maxlength="50" size="45"
                                                    readonly="true" value="<?php echo $staff_name;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Received By:<span style="color:#FF0000">*</span></td>
                                            <td><span id="staffOption"><select name="staffName" id="staffName"
                                                        style="width:300px">
                                                        <option value="0">-- Select --</option><?php 
			$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$location_id." ORDER BY staff_name");
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$receive_by)
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
                                                    </select></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Mode of Despatch:</td>
                                            <td><input name="despatchMode" id="despatchMode" maxlength="50" size="20"
                                                    readonly="true" value="<?php echo $despatch_mode;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Vehicle No.:</td>
                                            <td><input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20"
                                                    readonly="true" value="<?php echo $vehicle_num;?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltritem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltreceive.php'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltreceive.php'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img
                                                        src="images/back.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
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
                    <form name="iltlist" method="post" onsubmit="return validate_iltlist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Inter Location Transfer - [ Receive List ]</strong>
                                            </td>
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
                                            <th align="right" colspan="9">List Range:&nbsp;&nbsp;<select name="rangeFor"
                                                    id="rangeFor" style="width:140px" onchange="listrange(this.value)">
                                                    <?php 
			if(isset($_REQUEST['rf'])){
				if($_REQUEST['rf']=="U"){
					echo '<option selected value="U">Unreceived items</option><option value="R">Received items</option>';
				} elseif($_REQUEST['rf']=="R"){
					echo '<option value="U">Unreceived items</option><option selected value="R">Received items</option>';
				}
			} else {
				echo '<option selected value="U">Unreceived items</option><option value="R">Received items</option>';
			}?>
                                                </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From:&nbsp;&nbsp;<input
                                                    name="rangeFrom" id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'iltlist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'iltlist',
                                                    'controlname': 'rangeTo'
                                                });
                                                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image"
                                                    name="search" src="images/search.gif" width="82" height="22"
                                                    alt="search"><input type="hidden" name="sd" id="sd"
                                                    value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed"
                                                    value="<?php echo $ed;?>" /></th>
                                        </tr>
                                        <tr class="Caption">
                                            <th width="5%">Sl.No.</th>
                                            <th width="20%">ILT No.</th>
                                            <th width="10%">Dspch.Date</th>
                                            <th width="20%">Despatch From</th>
                                            <th width="20%">Despatch To</th>
                                            <th width="15%">Despatch By</th>
                                            <?php 
			if(isset($_REQUEST['rf'])){
				if($_REQUEST['rf']=="U"){
					echo '<th width="5%">Edit</th>';
				} elseif($_REQUEST['rf']=="R"){
					echo '<th width="5%">Recall</th>';
				}
			} else {
				echo '<th width="5%">Edit</th>';
			}?>
                                            <th width="5%">Del</th>
                                        </tr>

                                        <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['rf'])){$rangeFor=$_REQUEST['rf'];} else {$rangeFor="U";}
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
			$sql_ilt = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, despatchStaff.staff_name AS sourceStaff FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff AS despatchStaff ON tblilt1.despatch_by = despatchStaff.staff_id WHERE despatch_status='S' AND receive_status='".$rangeFor."' AND ilt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY destinationLocation,ilt_date,ilt_id LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_ilt = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, despatchStaff.staff_name AS sourceStaff FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff AS despatchStaff ON tblilt1.despatch_by = despatchStaff.staff_id WHERE receive_at=".$_SESSION['stores_locid']." AND despatch_status='S' AND receive_status='".$rangeFor."' AND ilt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY destinationLocation,ilt_date,ilt_id LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_ilt=mysql_fetch_array($sql_ilt)){
			$sql_item = mysql_query("SELECT tblilt2.*, item_name, unit_name FROM tblilt2 INNER JOIN item ON tblilt2.item_id = item.item_id INNER JOIN unit ON tblilt2.unit_id = unit.unit_id WHERE ilt_id=".$row_ilt['ilt_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['despatch_qnty'].' '.$row_item['unit_name'].'</td><td>'.$row_item['receive_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			if($row_ilt['receive_status']=='R'){
				$edit_ref = "iltreceive.php?action=recall&mid=".$row_ilt['ilt_id'];
			} elseif($row_ilt['receive_status']=='U'){
				$edit_ref = "iltreceive.php?action=edit&mid=".$row_ilt['ilt_id'];
			}
			$delete_ref = "iltreceive.php?action=delete&mid=".$row_ilt['ilt_id'];
			
			$ilt_number = ($row_ilt['ilt_no']>999 ? $row_ilt['ilt_no'] : ($row_ilt['ilt_no']>99 && $row_ilt['ilt_no']<1000 ? "0".$row_ilt['ilt_no'] : ($row_ilt['ilt_no']>9 && $row_ilt['ilt_no']<100 ? "00".$row_ilt['ilt_no'] : "000".$row_ilt['ilt_no'])));
			if($row_ilt['ilt_prefix']!=null){$ilt_number = $row_ilt['ilt_prefix']."/".$ilt_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is ILT number '.$ilt_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$ilt_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ilt['ilt_date'])).'</td><td>'.$row_ilt['sourceLocation'].'</td><td>'.$row_ilt['destinationLocation'].'</td><td>'.$row_ilt['sourceStaff'].'</td>';
			if($row_user['ilt2']==1){
				if($row_ilt['receive_status']=='R'){
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/undo.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				} elseif($row_ilt['receive_status']=='U'){
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				}
			} elseif($row_user['ilt2']==0){
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			}
			if($row_user['ilt3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ilt3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>

                                        <tr class="Footer">
                                            <td colspan="9" align="center">
                                                <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblilt1 WHERE despatch_status='S' AND receive_status='".$rangeFor."' AND ilt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblilt1 WHERE receive_at=".$_SESSION['stores_locid']." AND despatch_status='S' AND receive_status='".$rangeFor."' AND ilt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_iltr()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="miid" id="miid" value="'.$mid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_iltr()" style="vertical-align:middle">';
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
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="rf" id="rf" value="'.$rangeFor.'" />';
			if($total_page>1 && $pg>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_iltr()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_iltr()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_iltr()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_iltr()" />';
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