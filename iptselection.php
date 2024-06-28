<?php 
include("menu.php");
/*------------------------------*/
$sql_user = mysql_query("SELECT ipt1,ipt2,ipt3,ipt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*------------------------------*/
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
/*------------------------------*/
$msg = "";
$mid = "";
if(isset($_REQUEST['mid'])){$mid = $_REQUEST['mid'];}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$sql = mysql_query("SELECT tblipt.*, tblissue1.location_id, issue_date, location_name, staff_name, leader_name FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id = tblissue1.issue_id INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id INNER JOIN leader ON tblissue1.issue_to = leader.leader_id WHERE tblipt.ipt_id=".$mid) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$issue_location = $row['location_id'];
	$ipt_number = $row['ipt_no'];
}
/*------------------------------*/
if(isset($_POST['submit'])){
	$dateIPT=substr($_POST['iptDate'],6,4)."-".substr($_POST['iptDate'],3,2)."-".substr($_POST['iptDate'],0,2);
	$particulars = "For ".$_POST['locationName'];
	$sql_loc = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['location']) or die(mysql_error());
	$row_loc = mysql_fetch_assoc($sql_loc);
	/*------------------------------*/
	$sql = mysql_query("SELECT ipt_id FROM tblipt WHERE ipt_date='".$dateIPT."' AND issue_id=".$_POST['issueNo']) or die(mysql_error());
	$row_ipt = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_ipt['ipt_id']!=$mid)
				$msg = "Duplication Error! can&prime;t update into inter plot transfer record.";
			elseif($row_ipt['ipt_id']==$mid){
				/* first we check, if there are any data existent for different material issue no.
				 then we first delete them from tblipt_item, and then update the table; and in case 
				 if there are any data existent for the same material issue no., then we simply update the table. */
				if($row_ipt['issue_id']!=$row['issue_id']){
					$res = mysql_query("DELETE FROM tblipt_item WHERE ipt_id=".$mid) or die(mysql_error());
				}
				if($_POST['location']!=$issue_location){
					$sql = mysql_query("SELECT Max(ipt_no) as maxno FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id = tblissue1.issue_id WHERE location_id=".$_POST['location']." AND (ipt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
					$row = mysql_fetch_assoc($sql);
					$ino = $row["maxno"] + 1;
				} else {
					$ino = $ipt_number;
				}
				$sql = "UPDATE tblipt SET ipt_date='".$dateIPT."',ipt_no=".$ino.",";
				if($row_loc['location_prefix']==null){$sql .= "ipt_prefix=null, ";} else {$sql .= "ipt_prefix='".$row_loc['location_prefix']."', ";}
				$sql .= "issue_id=".$_POST['issueNo']." WHERE ipt_id=".$mid;
				$res = mysql_query($sql) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = $row["maxid"] + 1;
				$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIPT."','I.P.T.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				header('Location:iptitem.php?action=new&mid='.$mid);
			}
		} elseif($count==0){
			$res = mysql_query("DELETE FROM tblipt_item WHERE ipt_id=".$mid) or die(mysql_error());
			if($_POST['location']!=$issue_location){
				$sql = mysql_query("SELECT Max(ipt_no) as maxno FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id = tblissue1.issue_id WHERE location_id=".$_POST['location']." AND (ipt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
				$row = mysql_fetch_assoc($sql);
				$ino = $row["maxno"] + 1;
			} else {
				$ino = $ipt_number;
			}
			$sql = "UPDATE tblipt SET ipt_date='".$dateIPT."',ipt_no=".$ino.",";
			if($row_loc['location_prefix']==null){$sql .= "ipt_prefix=null, ";} else {$sql .= "ipt_prefix='".$row_loc['location_prefix']."', ";}
			$sql .= "issue_id=".$_POST['issueNo']." WHERE ipt_id=".$mid;
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIPT."','I.P.T.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			header('Location:iptitem.php?action=new&mid='.$mid);
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblipt WHERE ipt_id=".$mid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblipt_item WHERE ipt_id=".$mid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = $row["maxid"] + 1;
		$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIPT."','I.P.T.','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		header('Location:iptselection.php?action=new');
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into inter plot transfer record.";
		else {
			$sql = mysql_query("SELECT Max(ipt_id) as maxid FROM tblipt");
			$row = mysql_fetch_assoc($sql);
			$mid = $row["maxid"] + 1;
			$sql = mysql_query("SELECT Max(ipt_no) as maxno FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id = tblissue1.issue_id WHERE location_id=".$_POST['location']." AND (ipt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
			$row = mysql_fetch_assoc($sql);
			$ino = $row["maxno"] + 1;
			$sql = "INSERT INTO tblipt (ipt_id,ipt_date,ipt_no,ipt_prefix,issue_id) VALUES(".$mid.",'".$dateIPT."',".$ino.",";
			if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
			$sql .= $_POST['issueNo'].")";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">function show_message_ipt_number(value1,value2){
				alert("IPT No. = "+value2);
				window.location="iptitem.php?action=new&mid="+value1;}
				show_message_ipt_number('.$mid.','.$ino.');</script>';
//			header('Location:iptitem.php?action=new&mid='.$mid);
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
    function validate_iptdata() {
        var err = "";
        if (document.getElementById("iptDate").value != "") {
            if (!checkdate(document.iptselection.iptDate)) {
                return false;
            } else {
                var no_of_days1 = getDaysbetween2Dates(document.iptselection.iptDate, document.iptselection.endYear);
                if (no_of_days1 < 0) {
                    err += "* IPT date wrongly selected. Please correct and submit again.\n";
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.iptselection.startYear, document.iptselection
                        .iptDate);
                    if (no_of_days2 < 0) {
                        err += "* IPT date wrongly selected. Please correct and submit again.\n";
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.iptselection.issueDate, document.iptselection
                            .iptDate);
                        if (no_of_days3 < 0) {
                            err += "* IPT date wrongly selected. Please correct and submit again.\n";
                        }
                    }
                }
            }
        } else if (document.getElementById("iptDate").value == "") {
            err = "* please select/input IPT date!\n";
        }
        if (document.getElementById("issueNo").value == 0)
            err += "* please select the material issue number!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function validate_iptlist() {
        if (checkdate(document.iptlist.rangeFrom)) {
            if (checkdate(document.iptlist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.iptlist.rangeFrom, document.iptlist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function paging_iplot() {
        if (document.getElementById("xson").value == "new") {
            window.location = "iptselection.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        } else {
            window.location = "iptselection.php?action=" + document.getElementById("xson").value + "&mid=" + document
                .getElementById("plotid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&sd=" + document.getElementById("sd").value + "&ed=" +
                document.getElementById("ed").value;
        }
    }

    function firstpage_iplot() {
        document.getElementById("page").value = 1;
        paging_iplot();
    }

    function previouspage_iplot() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_iplot();
    }

    function nextpage_iplot() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_iplot();
    }

    function lastpage_iplot() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_iplot();
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
        <table align="center" cellspacing="0" cellpadding="0" height="250px" width="825px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <form name="iptselection" method="post" onsubmit="return validate_iptdata()">
                        <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Inter Plot Transfer - [ Main ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" nowrap>IPT No.:</td>
                                            <?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$ipt_number = ($row['ipt_no']>999 ? $row['ipt_no'] : ($row['ipt_no']>99 && $row['ipt_no']<1000 ? "0".$row['ipt_no'] : ($row['ipt_no']>9 && $row['ipt_no']<100 ? "00".$row['ipt_no'] : "000".$row['ipt_no'])));
				if($row['ipt_prefix']!=null){$ipt_number = $row['ipt_prefix']."/".$ipt_number;}
			} else {
				$ipt_number = "";
			}
			?>
                                            <td><input name="iptNo" id="iptNo" maxlength="15" size="20" readonly="true"
                                                    value="<?php echo $ipt_number; ?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>IPT Date:<span style="color:#FF0000">*</span></td>
                                            <td><input name="iptDate" id="iptDate" maxlength="10" size="10"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["ipt_date"]));} else echo date("d-m-Y");?>">&nbsp;
                                                <script language="JavaScript">
                                                new tcal({
                                                    'formname': 'iptselection',
                                                    'controlname': 'iptDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Issue No.:<span style="color:#FF0000">*</span></td>
                                            <td><select name="issueNo" id="issueNo" style="width:300px"
                                                    onchange="get_issue_detail(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_issue=mysql_query("SELECT tblissue1.*, location_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id ORDER BY location_name,issue_date,issue_no");
			elseif($_SESSION['stores_utype']=="U")
				$sql_issue=mysql_query("SELECT tblissue1.*, location_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id WHERE location_id=".$_SESSION['stores_locid']." ORDER BY location_name,issue_date,issue_no");
			while($row_issue=mysql_fetch_array($sql_issue))
			{
				$issue_number = ($row_issue['issue_no']>999 ? $row_issue['issue_no'] : ($row_issue['issue_no']>99 && $row_issue['issue_no']<1000 ? "0".$row_issue['issue_no'] : ($row_issue['issue_no']>9 && $row_issue['issue_no']<100 ? "00".$row_issue['issue_no'] : "000".$row_issue['issue_no'])));
				if($row_issue['issue_prefix']!=null){$issue_number = $row_issue['issue_prefix']."/".$issue_number;}
				$issue_no_with_date_n_location = $issue_number.",  Dt.".date("d-m-Y",strtotime($row_issue['issue_date'])).",  ".$row_issue['location_name'];
				if($row_issue["issue_id"]==$row["issue_id"])
					echo '<option selected value="'.$row_issue["issue_id"].'">'.$issue_no_with_date_n_location.'</option>';
				else
					echo '<option value="'.$row_issue["issue_id"].'">'.$issue_no_with_date_n_location.'</option>';
			}
			?>
                                                </select></td>

                                            <td class="th" nowrap>Issue Date:</td>
                                            <td><input name="issueDate" id="issueDate" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["issue_date"]));}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Location:</td>
                                            <td><input name="locationName" id="locationName" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row['location_name'];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"><input type="hidden"
                                                    name="location" id="location"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row['location_id'];} else {echo 0;}?>">
                                            </td>

                                            <td>&nbsp;<input type="hidden" name="startYear" id="startYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                                                    type="hidden" name="endYear" id="endYear"
                                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" />
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Issue By:</td>
                                            <td><input name="issueBy" id="issueBy" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row['staff_name'];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Issue To:</td>
                                            <td><input name="issueTo" id="issueTo" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row['leader_name'];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['ipt1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['ipt1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.iptselection.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptselection.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptselection.php?action=new'"><img
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
                    <form name="iptlist" method="post" onsubmit="return validate_iptlist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Inter Plot Transfer - [ List ]</strong></td>
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
                                            <th colspan="9">List Range From:&nbsp;&nbsp;<input name="rangeFrom"
                                                    id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'iptlist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'iptlist',
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
                                            <th width="15%">IPT No.</th>
                                            <th width="10%">IPT Date</th>
                                            <th width="15%">Issue No.</th>
                                            <th width="10%">Issue Date</th>
                                            <th width="20%">Location</th>
                                            <th width="15%">Issue By</th>
                                            <th width="5%">Edit</th>
                                            <th width="5%">Del</th>
                                        </tr>

                                        <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
			$sql_ipt = mysql_query("SELECT tblipt.*, issue_no, issue_prefix, issue_date, location_name, staff_name FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id =  tblissue1.issue_id INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE ipt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY ipt_date, ipt_id LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_ipt = mysql_query("SELECT tblipt.*, issue_no, issue_prefix, issue_date, location_name, staff_name FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id =  tblissue1.issue_id INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE tblissue1.location_id=".$_SESSION['stores_locid']." AND ipt_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY ipt_date, ipt_id LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_ipt=mysql_fetch_array($sql_ipt))
		{
			$sql_item = mysql_query("SELECT tblipt_item.*, item_name, unit_name, source.plot_name AS transferfrom, target.plot_name AS transferto FROM tblipt_item INNER JOIN item ON tblipt_item.item_id = item.item_id INNER JOIN unit ON tblipt_item.unit_id = unit.unit_id INNER JOIN plot AS source ON tblipt_item.transfer_from = source.plot_id INNER JOIN plot AS target ON tblipt_item.transfer_to = target.plot_id WHERE ipt_id='".$row_ipt['ipt_id']."' ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item))
			{
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['qnty_transfer'].' '.$row_item['unit_name'].'</td><td>'.$row_item['transferfrom'].'</td><td>'.$row_item['transferto'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "iptselection.php?action=delete&mid=".$row_ipt['ipt_id'];
			$edit_ref = "iptselection.php?action=edit&mid=".$row_ipt['ipt_id'];
			
			$ipt_number = ($row_ipt['ipt_no']>999 ? $row_ipt['ipt_no'] : ($row_ipt['ipt_no']>99 && $row_ipt['ipt_no']<1000 ? "0".$row_ipt['ipt_no'] : ($row_ipt['ipt_no']>9 && $row_ipt['ipt_no']<100 ? "00".$row_ipt['ipt_no'] : "000".$row_ipt['ipt_no'])));
			if($row_ipt['ipt_prefix']!=null){$ipt_number = $row_ipt['ipt_prefix']."/".$ipt_number;}
			
			$issue_number = ($row_ipt['issue_no']>999 ? $row_ipt['issue_no'] : ($row_ipt['issue_no']>99 && $row_ipt['issue_no']<1000 ? "0".$row_ipt['issue_no'] : ($row_ipt['issue_no']>9 && $row_ipt['issue_no']<100 ? "00".$row_ipt['issue_no'] : "000".$row_ipt['issue_no'])));
			if($row_ipt['issue_prefix']!=null){$issue_number = $row_ipt['issue_prefix']."/".$issue_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is ipt number '.$ipt_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$ipt_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ipt['ipt_date'])).'</td><td>'.$issue_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_ipt['issue_date'])).'</td><td>'.$row_ipt['location_name'].'</td><td>'.$row_ipt['staff_name'].'</td>';
			if($row_user['ipt2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ipt2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ipt3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ipt3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>

                                        <tr class="Footer">
                                            <td colspan="9" align="center">
                                                <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblipt WHERE ipt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id =  tblissue1.issue_id WHERE location_id=".$_SESSION['stores_locid']." AND ipt_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_iplot()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="plotid" id="plotid" value="'.$mid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_iplot()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_iplot()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_iplot()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_iplot()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_iplot()" />';
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