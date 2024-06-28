<?php 
include("menu.php");
/*--------------------------------*/
$sql_user = mysql_query("SELECT mi1,mi2,mi3,mi4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*----------------------------------------*/
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
/*----------------------------------------*/
$msg = "";
$mid = "";
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
$issue_number = "";
$issue_no = 0;
$issue_date = date("d-m-Y");
$issue_by = 0;
$issue_to = "";
if(isset($_REQUEST['mid'])){
	$mid = $_REQUEST['mid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT tblissue1.*,location_name,staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE issue_id=".$mid);
		$row = mysql_fetch_assoc($sql);
		$location_id = $row['location_id'];
		$issue_no = $row['issue_no'];
		$issue_number = ($issue_no>999 ? $issue_no : ($issue_no>99 && $issue_no<1000 ? "0".$issue_no : ($issue_no>9 && $issue_no<100 ? "00".$issue_no : "000".$issue_no)));
		if($row['issue_prefix']!=null){$issue_number = $row['issue_prefix']."/".$issue_number;}
		$issue_date = date("d-m-Y",strtotime($row["issue_date"]));
		$issue_by = $row["issue_by"];
		$issue_to = $row["issue_to"];
	}
}
/*----------------------------------------*/
if(isset($_POST['submit'])){
	$dateIssue=substr($_POST['issueDate'],6,4)."-".substr($_POST['issueDate'],3,2)."-".substr($_POST['issueDate'],0,2);
	$sql = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['location']) or die(mysql_error());
	$row_loc = mysql_fetch_assoc($sql);
	$particulars = $row_loc['location_name'];
	/*--------------------------------*/
	if($mid!=""){	
		$sql_ipt = mysql_query("SELECT * FROM tblipt WHERE issue_id=".$mid) or die(mysql_error());
		$count1 = mysql_num_rows($sql_ipt);
	}
	if($_POST['submit']=="update"){
		if($count1>0)
			echo '<script language="javascript">alert("Sorry dear! updation not possible due to material for the selected issue has been used in plots.");</script>';
		else {
			if($_POST['location']!=$location_id){
				$sql = mysql_query("SELECT Max(issue_no) as maxno FROM tblissue1 WHERE location_id=".$_POST['location']." AND (issue_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
				$row = mysql_fetch_assoc($sql);
				$ino = $row["maxno"] + 1;
			} else {
				$ino = $issue_no;
			}
			$sql = "UPDATE tblissue1 SET issue_no=".$ino.", issue_date='".$dateIssue."', location_id=".$_POST['location'].", ";
			if($row_loc['location_prefix']==null){$sql .= "issue_prefix=null, ";} else {$sql .= "issue_prefix='".$row_loc['location_prefix']."', ";}
			$sql .= "issue_by=".$_POST['staffName'].", issue_to='".$_POST['issueTo']."' WHERE issue_id=".$mid;
			$res = mysql_query($sql) or die(mysql_error());
			
			$res = mysql_query("UPDATE stock_register SET entry_date='".$dateIssue."',location_id=".$_POST['location']." WHERE entry_mode='I+' AND entry_id=".$mid) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="issueitem.php?action=new&mid='.$mid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		if($count1>0)
			echo '<script language="javascript">alert("Sorry dear! deletion not possible due to material for the selected issue has been used in plots.");</script>';
		else {
			$res = mysql_query("DELETE FROM tblissue1 WHERE issue_id=".$mid) or die(mysql_error());
			$res = mysql_query("DELETE FROM tblissue2 WHERE issue_id=".$mid) or die(mysql_error());
			$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='I+' AND entry_id=".$mid." AND entry_date='".$dateIssue."'") or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="materialissue.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT Max(issue_id) as maxid FROM tblissue1");
		$row = mysql_fetch_assoc($sql);
		$mid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(issue_no) as maxno FROM tblissue1 WHERE location_id=".$_POST['location']." AND (issue_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
		$row = mysql_fetch_assoc($sql);
		$ino = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tblissue1(issue_id,issue_no,issue_date,issue_prefix,location_id,issue_by,issue_to) VALUES(".$mid.",".$ino.",'".$dateIssue."',";
		if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
		$sql .= $_POST['location'].",".$_POST['staffName'].",'".$_POST['issueTo']."')";
		$res = mysql_query($sql) or die(mysql_error());
//		header('Location:issueitem.php?action=new&mid='.$mid);
		echo '<script language="javascript">function show_message_mi_number(value1,value2){
			alert("Issue No. = "+value2);
			window.location="issueitem.php?action=new&mid="+value1;}
			show_message_mi_number('.$mid.','.$ino.');</script>';
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
    function validate_issue() {
        var err = "";
        if (document.getElementById("issueDate").value != "") {
            if (!checkdate(document.materialissue.issueDate)) {
                return false;
            } else {
                var no_of_days1 = getDaysbetween2Dates(document.materialissue.issueDate, document.materialissue
                    .endYear);
                if (no_of_days1 < 0) {
                    err += "* Material Issue date wrongly selected. Please correct and submit again.\n";
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.materialissue.startYear, document.materialissue
                        .issueDate);
                    if (no_of_days2 < 0) {
                        err += "* Material Issue date wrongly selected. Please correct and submit again.\n";
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.materialissue.maxDate, document.materialissue
                            .issueDate);
                        if (no_of_days3 < 0) {
                            err += "* Material Issue date wrongly selected. Please correct and submit again.\n" +
                                "Last issue date was " + document.getElementById("maxDate").value +
                                ", so lower date is not acceptable.\n";
                        }
                    }
                }
            }
        } else if (document.getElementById("issueDate").value == "") {
            err += "* please select/input material issue date!\n";
        }
        if (document.getElementById("location").value == 0)
            err += "* please select location, where material being issued from!\n";
        if (document.getElementById("staffName").value == 0)
            err += "* please select staff, by whom the material being issued\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function validate_issuelist() {
        if (checkdate(document.milist.rangeFrom)) {
            if (checkdate(document.milist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.milist.rangeFrom, document.milist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function paging_missue() {
        if (document.getElementById("xson").value == "new") {
            window.location = "materialissue.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        } else {
            window.location = "materialissue.php?action=" + document.getElementById("xson").value + "&mid=" + document
                .getElementById("miid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&sd=" + document.getElementById("sd").value + "&ed=" +
                document.getElementById("ed").value;
        }
    }

    function firstpage_missue() {
        document.getElementById("page").value = 1;
        paging_missue();
    }

    function previouspage_missue() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_missue();
    }

    function nextpage_missue() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_missue();
    }

    function lastpage_missue() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_missue();
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
        <table align="center" cellspacing="0" cellpadding="0" height="250px" width="675px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <form name="materialissue" method="post" onsubmit="return validate_issue()">
                        <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Material Issue - [ Main ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" nowrap>Issue No.:</td>
                                            <td><input name="issueNo" id="issueNo" maxlength="15" size="20"
                                                    readonly="true" value="<?php echo $issue_number; ?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Issue Date:<span style="color:#FF0000">*</span></td>
                                            <td><input name="issueDate" id="issueDate" maxlength="10" size="10"
                                                    value="<?php echo $issue_date;?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'materialissue',
                                                    'controlname': 'issueDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Location:<span style="color:#FF0000">*</span></td>
                                            <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
				echo '<td><select name="location" id="location" style="width:300px" onchange="get_staffs(this.value)">';
				echo '<option value="0">-- Select --</option>';
				$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
				while($row_location=mysql_fetch_array($sql_location)){
					if($row_location["location_id"]==$location_id)
						echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
					else
						echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
				}
				echo '</select></td>';
			} elseif($_SESSION['stores_utype']=="U"){
				echo '<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$_SESSION['stores_locid'].'" /></td>';
			}
			?>

                                            <td class="th" nowrap>Issue By:<span style="color:#FF0000">*</span></td>
                                            <td><span id="staffOption"><select name="staffName" id="staffName"
                                                        style="width:300px">
                                                        <option value="0">-- Select --</option><?php 
			$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$location_id." ORDER BY staff_name");
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$issue_by)
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
                                                    </select></span></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Issue To:</td>
                                            <td><input name="issueTo" id="issueTo" maxlength="50" size="45"
                                                    value="<?php echo $issue_to; ?>"></td>

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
                                                <?php 
		if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['mi1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['mi1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.materialissue.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='issueitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='materialissue.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='materialissue.php?action=new'"><img
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
                    <form name="milist" method="post" onsubmit="return validate_issuelist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Material Issue - [ List ]</strong></td>
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
                                            <th align="right" colspan="8">List Range From:&nbsp;&nbsp;<input
                                                    name="rangeFrom" id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'milist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'milist',
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
                                            <th width="15%">Issue No.</th>
                                            <th width="10%">Date</th>
                                            <th width="20%">Location</th>
                                            <th width="20%">Issue By</th>
                                            <th width="20%">Issue To</th>
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
			$sql_issue = mysql_query("SELECT tblissue1.*, location_name, staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE issue_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY issue_date, issue_id LIMIT ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="U")
			$sql_issue = mysql_query("SELECT tblissue1.*, location_name, staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE tblissue1.location_id=".$_SESSION['stores_locid']." AND issue_date BETWEEN '".$fromDate."' AND '".$toDate."' ORDER BY issue_date, issue_id LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_issue=mysql_fetch_array($sql_issue)){
			$sql_item = mysql_query("SELECT tblissue2.*, item_name, unit_name, plot_name,ic.category FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id INNER JOIN plot ON tblissue2.plot_id = plot.plot_id INNER JOIN item_category ic ON ic.category_id = tblissue2.item_category WHERE issue_id=".$row_issue['issue_id']." ORDER BY seq_no") or die(mysql_error());
			$j = 0;
			$stext = '<table cellspacing=1 border=1 cellpadding=5>';
			while($row_item=mysql_fetch_array($sql_item)){
				$stext .='<tr>';
				$j++;
				$stext .='<td>'.$j.'</td><td>'.$row_item['item_name'].' ~~'.$row_item['category'].'</td><td>'.$row_item['issue_qnty'].' '.$row_item['unit_name'].'</td><td>'.$row_item['plot_name'].'</td>';
				$stext .='</tr>';
			}
			$stext .='</table>';
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "materialissue.php?action=delete&mid=".$row_issue['issue_id'];
			$edit_ref = "materialissue.php?action=edit&mid=".$row_issue['issue_id'];
			
			$issue_no = ($row_issue['issue_no']>999 ? $row_issue['issue_no'] : ($row_issue['issue_no']>99 && $row_issue['issue_no']<1000 ? "0".$row_issue['issue_no'] : ($row_issue['issue_no']>9 && $row_issue['issue_no']<100 ? "00".$row_issue['issue_no'] : "000".$row_issue['issue_no'])));
			if($row_issue['issue_prefix']!=null){$issue_no = $row_issue['issue_prefix']."/".$issue_no;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is issue number '.$issue_no.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$issue_no.'</td><td align="center">'.date("d-m-Y",strtotime($row_issue['issue_date'])).'</td><td>'.$row_issue['location_name'].'</td><td>'.$row_issue['staff_name'].'</td><td>'.$row_issue['issue_to'].'</td>';
			if($row_user['mi2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['mi2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['mi3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['mi3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>

                                        <tr class="Footer">
                                            <td colspan="8" align="center">
                                                <?php 
			if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S")
				$sql_total = mysql_query("SELECT * FROM tblissue1 WHERE issue_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="U")
				$sql_total = mysql_query("SELECT * FROM tblissue1 WHERE location_id=".$_SESSION['stores_locid']." AND issue_date BETWEEN '".$fromDate."' AND '".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_missue()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="miid" id="miid" value="'.$mid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_missue()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_missue()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_missue()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_missue()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_missue()" />';
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