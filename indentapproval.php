<?php 
include("menu.php");
/*-------------------------------*/
$msg = "";

$sql_user = mysql_query("SELECT appr_auth,oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*if($row_user['appr_auth']==0){echo "<script>location='login.php'</script>";}*/
/*-------------------------------*/
$oid = 0;
if(isset($_REQUEST["oid"])){$oid = $_REQUEST['oid'];}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$sql = mysql_query("SELECT tbl_indent.*,ordfrom.location_name AS orderfrom,staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$oid) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
}
/*-------------------------------*/
if(isset($_REQUEST["rid"])){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE rec_id=".$rid);
	$row2 = mysql_fetch_assoc($sql2);
	if($row2['aprvd_by']>0){
		$sql3 = mysql_query("SELECT uid,initial FROM users WHERE uid=".$row2['aprvd_by']);
		$row3 = mysql_fetch_assoc($sql3);
	} elseif($row2['aprvd_by']==0){
		$sql3 = mysql_query("SELECT uid,initial FROM users WHERE uid=".$_SESSION['stores_uid']);
		$row3 = mysql_fetch_assoc($sql3);
	}
}
/*-------------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="send"){
	$sql2 = mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$oid);
	$count_indent_items = mysql_num_rows($sql2);
	$sql3 = mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$oid." AND aprvd_by=0");
	$count_aprroved_items = mysql_num_rows($sql3);
	$diff = $count_indent_items - $count_aprroved_items;
        $dataMail=mysql_query("select email_id from users where po2=1 and user_type='U' and user_status='A'");
        $recMail=  mysql_fetch_array($dataMail);
	if($diff>0){
		$res = mysql_query("UPDATE tbl_indent SET appr_status='S', appr_by=".$_SESSION['stores_uid'].", appr_date='".date("Y-m-d")."' WHERE indent_id=".$oid) or die(mysql_error());
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
echo '<script language="javascript">window.location="indentapproval.php?action=new";</script>';
	} elseif($diff==0){
		$res = mysql_query("UPDATE tbl_indent_item SET aprvd_qnty=qnty, aprvd_status=1, aprvd_by=".$_SESSION['stores_uid'].", aprvd_date='".date("Y-m-d")."' WHERE indent_id=".$oid) or die(mysql_error());
		$res = mysql_query("UPDATE tbl_indent SET appr_status='S', appr_by=".$_SESSION['stores_uid'].", appr_date='".date("Y-m-d")."' WHERE indent_id=".$oid) or die(mysql_error());
 if($_SESSION['stores_uid']!=$recMail['email_id']){
        $to=$recMail["email_id"];
        $sub="Mail regarding Purchase Order";
        $mailMsg="Please complete purchase order";
        $header="From:admin@vnrseeds.com";
        if(mail($to,$sub,$mailMsg,$header))
                echo"<script>alert('Mail sent to $to')</script>";
        else echo"<script>alert('Mail not sent')</script>";
        }		
//echo '<script language="javascript">window.location="indentapproval.php?action=new";</script>';
	}
        $data=  mysql_query();
        $recMail=  mysql_fetch_array($data);
       
}
/*-------------------------------*/
if(isset($_POST['submit'])){
	if($_POST['approvedStatus']==0 || $_POST['approvedStatus']==2){
		$aprvdQnty = 0;
	} elseif($_POST['approvedStatus']==1){
		if($_POST['approvedQnty']==0){
			$aprvdQnty = $_POST['itemQnty'];
		} elseif($_POST['approvedQnty']!=0){
			$aprvdQnty = $_POST['approvedQnty'];
		}
	}
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tbl_indent_item SET aprvd_qnty=".$aprvdQnty.",aprvd_status=".$_POST['approvedStatus'].",aprvd_by=".$row3['uid'].", aprvd_date='".date("Y-m-d")."' WHERE rec_id=".$rid) or die(mysql_error());
//		header('Location:indentapproval.php?action=edit&oid='.$oid.'&rid='.$rid);
		echo '<script language="javascript">window.location="indentapproval.php?action=edit&oid='.$oid.'";</script>';
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
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/tigra_hints.js"></script>
    <script language="javascript" type="text/javascript">
    function validate_indent() {
        var err = "";
        if (document.getElementById("approvedQnty").value != "" && !IsNumeric(document.getElementById("approvedQnty")
                .value))
            err = "* please input valid quantity of the item!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function paging_indent(value1) {
        if (document.getElementById("xson").value == "new")
            window.location = "indentapproval.php?action=" + document.getElementById("xson").value + "&pg=" + document
            .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + value1;
        else
            window.location = "indentapproval.php?action=" + document.getElementById("xson").value + "&oid=" + document
            .getElementById("indid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value + value1;
    }

    function firstpage_indent() {
        document.getElementById("page").value = 1;
        paging_indent("");
    }

    function previouspage_indent() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_indent("");
    }

    function nextpage_indent() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_indent("");
    }

    function lastpage_indent() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_indent("");
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
        <table align="center" cellspacing="0" cellpadding="0" height="350px" width="825px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td valign="top">
                                <table class="Header" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Indent Approval - [ Main ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Controls">
                                        <td class="th" nowrap>Indent No.:</td>
                                        <?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
				if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
			} else {
				$indent_number = "";
			} ?>
                                        <td><input name="indentNo" id="indentNo" maxlength="15" size="20"
                                                value="<?php echo $indent_number; ?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Indent Date:</td>
                                        <td><input name="indentDate" id="indentDate" maxlength="10" size="10"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["indent_date"]));} else echo date("d-m-Y");?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td class="th" nowrap>Estmtd.Supply Date:</td>
                                        <td><input name="supplyDate" id="supplyDate" maxlength="10" size="10"
                                                value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["supply_date"]));} else echo date("d-m-Y");?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th">Indent From:</td>
                                        <td><input name="indentFrom" id="indentFrom" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row["orderfrom"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Indent By:</td>
                                        <td><input name="orderBy" id="orderBy" maxlength="50" size="45" readonly="true"
                                                value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row["staff_name"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="6">
                    <form name="indentapproval" method="post" onsubmit="return validate_indent()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Caption">
                                            <th align="center" width="40%">Item Name</th>
                                            <th align="center" width="40%">Item Decription</th>
                                            <th align="center" width="15%">Indent Qnty.</th>
                                            <th align="center" width="10%">Unit</th>
                                            <th align="center" width="15%">Approved Qnty.</th>
                                            <th align="center" width="10%">Approved</th>
                                            <th align="center" width="10%">Approved By</th>
                                        </tr>

                                        <tr class="Controls">
                                            <td><input name="itemName" id="itemName" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["item_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                            <td><input name="itemDescription" id="itemDescription" maxlength="50"
                                                    size="45" readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["remark"].'-'.$row2["AnyOther"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                            <td><input name="itemQnty" id="itemQnty" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["qnty"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                            <td><input name="unitName" id="unitName" maxlength="15" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["unit_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                            <td><input name="approvedQnty" id="approvedQnty" maxlength="10" size="10"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ if($row2['aprvd_status']==0){echo $row2["qnty"];}else{echo $row2["aprvd_qnty"];} }?>">
                                            </td>
                                            <td><select name="approvedStatus" id="approvedStatus" style="width:50px"
                                                    onchange="set_freight_focus(this.value)">
                                                    <?php 
			if($row2['aprvd_status']==0)
				echo '<option selected value="0">&nbsp;</option>';
			else
				echo '<option value="0">&nbsp;</option>';
			if($row2['aprvd_status']==1)
				echo '<option selected value="1">Yes</option>';
			else
				echo '<option value="1">Yes</option>';
			if($row2['aprvd_status']==2)
				echo '<option selected value="2">No</option>';
			else
				echo '<option value="2">No</option>';
			?>
                                                </select></td>
                                            <td><input name="approvedBy" id="approvedBy" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row3["initial"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="6" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="6">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['oi1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['oi1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.indentapproval.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='indentapproval.php?action=new&oid=<?php echo $oid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='indentapproval.php?action=new&oid=<?php echo $oid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img
                                                        src="images/back.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='indentapproval.php?action=send&oid=<?php echo $oid;?>'"><img
                                                        src="images/send.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
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
                <td valign="top" colspan="6">
                    <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td valign="top">
                                <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Indent Approval - [ Item List ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Caption">
                                        <th width="5%">Sl.No.</th>
                                        <th width="40%">Item Name</th>
                                        <th width="40%">Item Description</th>
                                        <th width="10%">Indent Qnty.</th>
                                        <th width="10%">Unit</th>
                                        <th width="10%">Approved Qnty.</th>
                                        <th width="10%">Approved</th>
                                        <th width="10%">Approved By</th>
                                        <th width="5%">Select</th>
                                    </tr>

                                    <?php 
		$i = 0;
		$sql_order = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$oid." ORDER BY seq_no") or die(mysql_error());
		while($row_order=mysql_fetch_array($sql_order)){
			$i++;
			$initialofApprovedBy = "&nbsp;";
			if($row_order['aprvd_by']>0){
				$sqluser = mysql_query("SELECT initial FROM users WHERE uid=".$row_order['aprvd_by']) or die(mysql_error());
				$rowuser=mysql_fetch_assoc($sqluser);
				$initialofApprovedBy = $rowuser['initial'];
			}
			
			echo '<tr class="Row">';
			$select_ref = "indentapproval.php?action=edit&oid=".$oid."&rid=".$row_order['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td>'.$row_order['remark'].'-'.$row_order['AnyOther'].'</td><td align="right">'.$row_order['qnty'].'</td><td>'.$row_order['unit_name'].'</td><td align="right">'.$row_order['aprvd_qnty'].'</td>';
			if($row_order['aprvd_status']==0)
				echo '<td align="center" >&nbsp;</td>';
			elseif($row_order['aprvd_status']==1)
				echo '<td align="center" ><img src="images/check.gif" style="display:inline;cursor:pointer" border="0" /></td>';
			elseif($row_order['aprvd_status']==2)
				echo '<td align="center" ><img src="images/cancel.gif" style="display:inline;cursor:pointer" border="0" /></td>';
			
			echo '<td align="center">'.$initialofApprovedBy .'</td>';
			echo '<td align="center"><a href="'.$select_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
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
                                <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Order Indent - [ List ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt="" /></td>
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
                                        <th width="5%">Sl.No.</th>
                                        <th width="10%">Indent No.</th>
                                        <th width="10%">Date</th>
                                        <th width="25%">Indent From</th>
                                        <th width="25%">Order By</th>
                                        <th width="5%">Select</th>
                                    </tr>

                                    <?php 


        $srep=mysql_query("select uid from users where (repuser_id=".$_SESSION['stores_uid']." OR repuser2_id=".$_SESSION['stores_uid']." OR repuser3_id=".$_SESSION['stores_uid']." OR repuser4_id=".$_SESSION['stores_uid']." OR repuser5_id=".$_SESSION['stores_uid'].")");
		$rrows=mysql_num_rows($srep);
		
		if($rrows>0)
		{
		 while($rrep=mysql_fetch_assoc($srep))
		 {
		   $array_u[]=$rrep['uid'];
		   $userids = implode(',', $array_u);
		 }
		}


		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
                
                //AND (tbl_indent.uid=".$_SESSION['stores_uid']." OR tbl_indent.uid in (".$userids."))  

//if($_SESSION['stores_uid']==7){ echo "SELECT tbl_indent.*,location_name,staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE ind_status='S' AND appr_status='U' AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND (tbl_indent.uid=".$_SESSION['stores_uid']." OR tbl_indent.uid in (".$userids.")) ORDER BY location_name,indent_date,indent_id LIMIT ".$start.",".$end;}


		$sql_indent = mysql_query("SELECT tbl_indent.*,location_name,staff_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE ind_status='S' AND appr_status='U' AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') AND (tbl_indent.uid=".$_SESSION['stores_uid']." OR tbl_indent.uid in (".$userids.")) ORDER BY location_name,indent_date,indent_id LIMIT ".$start.",".$end) or die(mysql_error());
		
		while($row_indent=mysql_fetch_array($sql_indent)){
			$sql_item = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$row_indent['indent_id']." ORDER BY seq_no") or die(mysql_error());
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
			$edit_ref = "indentapproval.php?action=edit&oid=".$row_indent['indent_id'];
			
			$indent_number = ($row_indent['indent_no']>999 ? $row_indent['indent_no'] : ($row_indent['indent_no']>99 && $row_indent['indent_no']<1000 ? "0".$row_indent['indent_no'] : ($row_indent['indent_no']>9 && $row_indent['indent_no']<100 ? "00".$row_indent['indent_no'] : "000".$row_indent['indent_no'])));
			if($row_indent['ind_prefix']!=null){$indent_number = $row_indent['ind_prefix']."/".$indent_number;}
			
			echo '<td align="center">'.$i.'.</td><td onmouseover="myShow(\'<b><u>This is indent number '.$indent_number.'</u></b><br/>'.$stext.'\', this)" onmouseout="myHint.hide()">'.$indent_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_indent['indent_date'])).'</td><td>'.$row_indent['location_name'].'</td><td>'.$row_indent['staff_name'].'</td>';
			echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>

                                    <tr class="Footer">
                                        <td colspan="6" align="center">
                                            <?php 
			$sql_total = mysql_query("SELECT * FROM tbl_indent WHERE ind_status='S' AND appr_status='U' AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			$strg = "";
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_indent('.$strg.')" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="indid" id="indid" value="'.$oid.'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_indent('.$strg.')" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_indent()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_indent()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_indent()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_indent()" />';
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