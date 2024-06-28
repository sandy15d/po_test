<?php 
include("menu.php");
/*-----------------------------*/
$sql_user = mysql_query("SELECT xlt1,xlt2,xlt3,xlt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-----------------------------*/
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
/*-----------------------------*/
$msg = "";
$xid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$xlt_number = "";
$xlt_no = 0;
$xlt_date = date("d-m-Y");
$tfr_location = "";
$tfr_by = 0;
$despatch_mode = 0;
if(isset($_REQUEST['xid'])){
	$xid = $_REQUEST['xid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM tblxlt WHERE xlt_id=".$xid) or die(mysql_error());
		$row = mysql_fetch_assoc($sql);
		$location_id = $row['location_id'];
		$xlt_no = $row['xlt_no'];
		$xlt_number = ($xlt_no>999 ? $xlt_no : ($xlt_no>99 && $xlt_no<1000 ? "0".$xlt_no : ($xlt_no>9 && $xlt_no<100 ? "00".$xlt_no : "000".$xlt_no)));
		if($row['xlt_prefix']!=null){$xlt_number = $row['xlt_prefix']."/".$xlt_number;}
		$xlt_date = date("d-m-Y",strtotime($row["xlt_date"]));
		$tfr_location = $row["tfr_location"];
		$tfr_by = $row["tfr_by"];
		$despatch_mode = $row["despatch_mode"];
	}
}
/*-----------------------------*/
if(isset($_POST['submit'])){
	$dateXLT=substr($_POST['xltDate'],6,4)."-".substr($_POST['xltDate'],3,2)."-".substr($_POST['xltDate'],0,2);
	$sql_loc = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['location']) or die(mysql_error());
	$row_loc = mysql_fetch_assoc($sql_loc);
	$particulars = "From ".$row_loc['location_name']." To ".$_POST['destination'];
	/*-----------------------------*/
	if($_POST['submit']=="update"){
		if($_POST['location']!=$location_id){
			$sql = mysql_query("SELECT Max(xlt_no) as maxno FROM tblxlt WHERE location_id=".$_POST['location']." AND xlt_type='D' AND (xlt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
			$row = mysql_fetch_assoc($sql);
			$xno = $row["maxno"] + 1;
		} else {
			$xno = $xlt_no;
		}
		$sql = "UPDATE tblxlt SET xlt_date='".$dateXLT."',xlt_no=".$xno.",";
		if($row_loc['location_prefix']==null){$sql .= "xlt_prefix=null, ";} else {$sql .= "xlt_prefix='".$row_loc['location_prefix']."', ";}
		$sql .= "',location_id=".$_POST['location'].",tfr_location='".$_POST['destination']."',tfr_by=".$_POST['staffName'].",despatch_mode=".$_POST['despatchMode'].",vehicle_num='".$_POST['vehicleNumber']."' WHERE xlt_id=".$xid;
		$res = mysql_query($sql) or die(mysql_error());
		$res = mysql_query("UPDATE stock_register SET entry_date='".$dateXLT."',location_id=".$_POST['location']." WHERE entry_mode='X-' AND entry_id=".$xid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($xid>999 ? $xid : ($xid>99 && $xid<1000 ? "0".$xid : ($xid>9 && $xid<100 ? "00".$xid : "000".$xid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateXLT."','XLT.Dspch.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="xltditem.php?action=new&xid='.$xid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblxlt WHERE xlt_id=".$xid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblxlt_item WHERE xlt_id=".$xid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='X-' AND entry_id=".$xid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($xid>999 ? $xid : ($xid>99 && $xid<1000 ? "0".$xid : ($xid>9 && $xid<100 ? "00".$xid : "000".$xid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateXLT."','XLT.Dspch.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="xltdespatch.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT Max(xlt_id) as maxid FROM tblxlt");
		$row = mysql_fetch_assoc($sql);
		$xid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(xlt_no) as maxno FROM tblxlt WHERE location_id=".$_POST['location']." AND xlt_type='D' AND (xlt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
		$row = mysql_fetch_assoc($sql);
		$xno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tblxlt (xlt_id,xlt_no,xlt_prefix,xlt_date,xlt_type,location_id,tfr_location,tfr_by,despatch_mode,vehicle_num) VALUES(".$xid.",".$xno.",";
		if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
		$sql .= "'".$dateXLT."','D',".$_POST['location'].",'".$_POST['destination']."',".$_POST['staffName'].",".$_POST['despatchMode'].",'".$_POST['vehicleNumber']."')";
		$res = mysql_query($sql) or die(mysql_error());
		echo '<script language="javascript">function show_message_xlt_number(value1,value2){
			alert("XLT No. = "+value2);
			window.location="xltditem.php?action=new&xid="+value1;}
			show_message_xlt_number('.$xid.','.$xno.');</script>';
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
    function validate_xltdata() {
        var err = "";
        if (document.getElementById("xltDate").value != "") {
            if (!checkdate(document.xltdespatch.xltDate)) {
                return false;
            } else {
                var no_of_days1 = getDaysbetween2Dates(document.xltdespatch.xltDate, document.xltdespatch.endYear);
                if (no_of_days1 < 0) {
                    err += "* XLT date wrongly selected. Please correct and submit again.\n";
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.xltdespatch.startYear, document.xltdespatch
                    .xltDate);
                    if (no_of_days2 < 0) {
                        err += "* XLT date wrongly selected. Please correct and submit again.\n";
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.xltdespatch.maxDate, document.xltdespatch
                            .xltDate);
                        if (no_of_days3 < 0) {
                            err += "* XLT date wrongly selected. Please correct and submit again.\n" +
                                "Last date was " + document.getElementById("maxDate").value +
                                ", so lower date is not acceptable.\n";
                        }
                    }
                }
            }
        } else if (document.getElementById("xltDate").value == "")
            err = "* please input/select XLT date!\n";
        if (document.getElementById("location").value == 0)
            err += "* please select source location, where material being transferred from!\n";
        if (document.getElementById("destination").value == "")
            err += "* please input destination, where material being transferred to!\n";
        if (document.getElementById("staffName").value == 0)
            err += "* please select staff, who despatched the material!\n";
        if (document.getElementById("despatchMode").value == 0)
            err += "* please select the mode of despatch/transfer!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function set_vehicle_focus(value1) {
        if (value1 == "2") {
            document.getElementById('vehicle').innerHTML =
                '<input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20" value="">';
        } else if (value1 == "1") {
            document.getElementById("vehicleNumber").value == "";
            document.getElementById('vehicle').innerHTML =
                '<input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF">';
        }
    }

    function validate_xltlist() {
        if (checkdate(document.xltlist.rangeFrom)) {
            if (checkdate(document.xltlist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.xltlist.rangeFrom, document.xltlist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function paging_xltd() {
        if (document.getElementById("xson").value == "new") {
            window.location = "xltdespatch.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        } else {
            window.location = "xltdespatch.php?action=" + document.getElementById("xson").value + "&xid=" + document
                .getElementById("xlid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&sd=" + document.getElementById("sd").value + "&ed=" +
                document.getElementById("ed").value;
        }
    }

    function firstpage_xltd() {
        document.getElementById("page").value = 1;
        paging_xltd();
    }

    function previouspage_xltd() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_xltd();
    }

    function nextpage_xltd() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_xltd();
    }

    function lastpage_xltd() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_xltd();
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
                    <form name="xltdespatch" method="post" onsubmit="return validate_xltdata()">
                        <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>External Location Transfer - [ Despatch Main
                                                    ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" nowrap>XLT No.:</td>
                                            <td><input name="xltNo" id="xltNo" maxlength="15" size="20" readonly="true"
                                                    value="<?php echo $xlt_number; ?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>XLT Date:<span style="color:#FF0000">*</span></td>
                                            <td><input name="xltDate" id="xltDate" maxlength="10" size="10"
                                                    value="<?php echo $xlt_date;?>">&nbsp;<script language="JavaScript">
                                                new tcal({
                                                    'formname': 'xltdespatch',
                                                    'controlname': 'xltDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Despatch From:<span style="color:#FF0000">*</span></td>
                                            <?php if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
				echo '<td><select name="location" id="location" onchange="get_staffs(this.value)" style="width:300px">';
				echo '<option value="0">-- Select --</option>';
				$sql_source=mysql_query("SELECT * FROM location ORDER BY location_name");
				while($row_source=mysql_fetch_array($sql_source)){
					if($row_source["location_id"]==$location_id)
						echo '<option selected value="'.$row_source["location_id"].'">'.$row_source["location_name"].'</option>';
					else
						echo '<option value="'.$row_source["location_id"].'">'.$row_source["location_name"].'</option>';
				}
				echo '</select></td>';
			} elseif($_SESSION['stores_utype']=="U"){
				echo '<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$_SESSION['stores_locid'].'" /></td>';
			}?>

                                            <td class="th">Destination:<span style="color:#FF0000">*</span></td>
                                            <td><input name="destination" id="destination" maxlength="50" size="45"
                                                    value="<?php echo $tfr_location;?>"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Despatched By:<span style="color:#FF0000">*</span>
                                            </td>
                                            <td><span id="staffOption"><select name="staffName" id="staffName"
                                                        style="width:300px">
                                                        <option value="0">-- Select --</option><?php 
			$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$location_id." ORDER BY staff_name");
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$tfr_by)
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
                                                    </select></span></td>

                                            <td>&nbsp;<input type="hidden" name="startYear" id="startYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                                                    type="hidden" name="endYear" id="endYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /><input
                                                    type="hidden" name="maxDate" id="maxDate"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" />
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Mode of Despatch:<span style="color:#FF0000">*</span>
                                            </td>
                                            <td><select name="despatchMode" id="despatchMode" style="width:150px"
                                                    onchange="set_vehicle_focus(this.value)"><?php 
			if($despatch_mode==0){
				echo '<option selected value="0">-- Select --</option><option value="1">Hand Delivery</option><option value="2">By Vehicle</option>';
			} elseif($despatch_mode==1){
				echo '<option value="0">-- Select --</option><option selected value="1">Hand Delivery</option><option value="2">By Vehicle</option>';
			} elseif($despatch_mode==2){
				echo '<option value="0">-- Select --</option><option value="1">Hand Delivery</option><option selected value="2">By Vehicle</option>';
			}
			?>
                                                </select></td>

                                            <td class="th" nowrap>Vehicle No.:</td><?php 
			if($despatch_mode==2){
				echo '<td><span id="vehicle"><input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20" value="'.$row["vehicle_num"].'" ></span></td>';
			} else {
				echo '<td><span id="vehicle"><input name="vehicleNumber" id="vehicleNumber" maxlength="15" size="20" readonly="true" value="" style="background-color:#E7F0F8; color:#0000FF"></span></td>';
			}
			?>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['xlt1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['xlt1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.xlt.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='xltditem.php?action=new&xid=<?php echo $xid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='xltdespatch.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='xltdespatch.php?action=new'"><img
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
                    <form name="xltlist" method="post" onsubmit="return validate_xltlist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>External Location Transfer - [ Despatch List
                                                    ]</strong></td>
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
                                            <th align="right" colspan="8"></select>From:&nbsp;&nbsp;<input
                                                    name="rangeFrom" id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'xltlist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'xltlist',
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
                                            <th width="15%">XLT No.</th>
                                            <th width="10%">Date</th>
                                            <th width="20%">Despatch From</th>
                                            <th width="20%">Destination</th>
                                            <th width="20%">Despatch By</th>
                                            <th width="10%">Action</th>
                                        </tr>

                                        <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
			$sql_xlt = mysql_query("SELECT tblxlt.*, location_name, staff_name FROM tblxlt INNER JOIN location ON tblxlt.location_id = location.location_id INNER JOIN staff ON tblxlt.tfr_by = staff.staff_id WHERE xlt_type='D' AND xlt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name,xlt_date,xlt_id LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_xlt = mysql_query("SELECT tblxlt.*, location_name, staff_name FROM tblxlt INNER JOIN location ON tblxlt.location_id = location.location_id INNER JOIN staff ON tblxlt.tfr_by = staff.staff_id WHERE location_id=".$_SESSION['stores_locid']." AND xlt_type='D' AND xlt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY location_name,xlt_date,xlt_id LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_xlt=mysql_fetch_array($sql_xlt)){
			$sql_item = mysql_query("SELECT tblxlt_item.*, item_name, unit_name FROM tblxlt_item INNER JOIN item ON tblxlt_item.item_id = item.item_id INNER JOIN unit ON tblxlt_item.unit_id = unit.unit_id WHERE xlt_id=".$row_xlt['xlt_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['xlt_qnty'].' '.$row_item['unit_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$edit_ref = "xltdespatch.php?action=edit&xid=".$row_xlt['xlt_id'];
			$delete_ref = "xltdespatch.php?action=delete&xid=".$row_xlt['xlt_id'];
			$x = "window.open('printofpage.php?typ=xlt&vid=".$row_xlt['xlt_id']."', 'voucherPrint', 'width=300,height=200, resizable=no, scrollbars=no, toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no')";
			
			$xlt_number = ($row_xlt['xlt_no']>999 ? $row_xlt['xlt_no'] : ($row_xlt['xlt_no']>99 && $row_xlt['xlt_no']<1000 ? "0".$row_xlt['xlt_no'] : ($row_xlt['xlt_no']>9 && $row_xlt['xlt_no']<100 ? "00".$row_xlt['xlt_no'] : "000".$row_xlt['xlt_no'])));
			if($row_xlt['xlt_prefix']!=null){$xlt_number = $row_xlt['xlt_prefix']."/".$xlt_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is XLT number '.$xlt_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$xlt_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_xlt['xlt_date'])).'</td><td>'.$row_xlt['location_name'].'</td><td>'.$row_xlt['tfr_location'].'</td><td>'.$row_xlt['staff_name'].'</td>';
			echo '<td align="center">&nbsp;';
			if($row_user['xlt2']==1)
				echo '<a href="'.$edit_ref.'"><img src="images/edit.gif" title="Edit" style="display:inline;cursor:hand;" border="0" /></a>';
			elseif($row_user['xlt2']==0)
				echo '<a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>';
			echo '&nbsp;&nbsp;';
			if($row_user['xlt3']==1)
				echo '<a href="'.$delete_ref.'"><img src="images/cancel.gif" title="Delete" style="display:inline;cursor:hand;" border="0" /></a>';
			elseif($row_user['xlt3']==0)
				echo '<a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a>';
			echo '&nbsp;&nbsp;';
			echo '<a onclick="'.$x.'"><img src="images/print1.gif" title="Print" style="display:inline;cursor:hand;" border="0" /></a>';
			echo '&nbsp;</td></tr>';
		} ?>

                                        <tr class="Footer">
                                            <td colspan="8" align="center">
                                                <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblxlt WHERE xlt_type='D' AND xlt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblxlt WHERE location_id=".$_SESSION['stores_locid']." AND xlt_type='D' AND xlt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_xltd()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="xlid" id="xlid" value="'.$xid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_xltd()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_xltd()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_xltd()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_xltd()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_xltd()" />';
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