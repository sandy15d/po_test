<?php 
include("menu.php");
/*----------------------------*/
$sql_user = mysql_query("SELECT ipt1,ipt2,ipt3,ipt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*----------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblipt.*, issue_no, issue_prefix, issue_date, tblissue1.location_id, location_name, staff_name FROM tblipt INNER JOIN tblissue1 ON tblipt.issue_id = tblissue1.issue_id INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id  WHERE tblipt.ipt_id=".$mid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
/*----------------------------*/
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_query("SELECT tblipt_item.*, item.unit_id AS prime_unit_id, item.alt_unit, item.alt_unit_id, item.alt_unit_num, item_name, unit_name, plot_name FROM tblipt_item INNER JOIN item ON tblipt_item.item_id = item.item_id INNER JOIN unit ON tblipt_item.unit_id = unit.unit_id INNER JOIN plot ON tblipt_item.transfer_from = plot.plot_id WHERE rec_id=".$rid) or die(mysql_error());
	$row2 = mysql_fetch_assoc($sql2);
	$sql = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row2['prime_unit_id']);
	$row = mysql_fetch_assoc($sql);
	$prime_unit_name = $row['unit_name'];
	$alt_unit_name = "";
	if($row2['alt_unit_id']!=0){
		$sql = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row2['alt_unit_id']);
		$row = mysql_fetch_assoc($sql);
		$alt_unit_name = $row['unit_name'];
	}
}
/*----------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT * FROM item WHERE item_id=".$_POST['item']) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$itemname=$row["item_name"];
	/*----------------------------*/
	if($row['alt_unit']=="N"){$unitid = $row['unit_id'];} elseif($row['alt_unit']=="A"){$unitid = $_POST['unit'];}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$unitid);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*----------------------------*/
	$sql = mysql_query("SELECT plot_name FROM plot WHERE plot_id=".$_POST['plotFrom']) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$pfrom=$row["plot_name"];
	/*----------------------------*/
	$sql = mysql_query("SELECT plot_name FROM plot WHERE plot_id=".$_POST['plotTo']) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$pto=$row["plot_name"];
	/*----------------------------*/
	$dateIPT=$row1["ipt_date"];
	$particulars = $row1['location_name']."/".$pfrom." to ".$pto;
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	/*----------------------------*/
	$sql_ipt = mysql_query("SELECT * FROM tblipt_item WHERE ipt_id=".$mid." AND item_id=".$_POST['item']." AND qnty_inhand=".$_POST['itemQnty']." AND transfer_from=".$_POST['plotFrom']." AND transfer_to=".$_POST['plotTo']." AND qnty_transfer=".$_POST['tfrQnty']) or die(mysql_error());
	$row_ipt = mysql_fetch_assoc($sql_ipt);
	$count = mysql_num_rows($sql_ipt);
	/*----------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_ipt['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into material issue record.";
			elseif($row_ipt['rec_id']==$rid)
				$res = mysql_query("UPDATE tblipt_item SET qnty_transfer=".$_POST['tfrQnty'].",transfer_to=".$_POST['plotTo']." WHERE rec_id=".$rid) or die(mysql_error());
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblipt_item");
				$row = mysql_fetch_assoc($sql);
				$rid = $row["maxid"] + 1;
				$sql = mysql_query("SELECT Max(seq_no) as maxid FROM tblipt_item WHERE ipt_id=".$mid);
				$row = mysql_fetch_assoc($sql);
				$sno = $row["maxid"] + 1;
				$balQnty = $_POST['itemQnty'] - $_POST['tfrQnty'];
				$sql = "INSERT INTO tblipt_item (rec_id,ipt_id,seq_no,item_id,qnty_inhand,transfer_from) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$balQnty.",".$_POST['plotFrom'].")";
				$res = mysql_query($sql) or die(mysql_error());
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblipt_item");
				$row = mysql_fetch_assoc($sql);
				$rid = $row["maxid"] + 1;
				$sql = mysql_query("SELECT Max(seq_no) as maxid FROM tblipt_item WHERE ipt_id=".$mid);
				$row = mysql_fetch_assoc($sql);
				$sno = $row["maxid"] + 1;
				$sql = "INSERT INTO tblipt_item (rec_id,ipt_id,seq_no,item_id,qnty_inhand,transfer_from) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$_POST['tfrQnty'].",".$_POST['plotTo'].")";
				$res = mysql_query($sql) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = $row["maxid"] + 1;
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIPT."','I.P.T.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['tfrQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				header('Location:iptitem.php?action=new&mid='.$mid);
		} elseif($count==0){
			$res = mysql_query("UPDATE tblipt_item SET qnty_transfer=".$_POST['tfrQnty'].",transfer_to=".$_POST['plotTo']." WHERE rec_id=".$rid) or die(mysql_error());
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblipt_item");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = mysql_query("SELECT Max(seq_no) as maxid FROM tblipt_item WHERE ipt_id=".$mid);
			$row = mysql_fetch_assoc($sql);
			$sno = $row["maxid"] + 1;
			$balQnty = $_POST['itemQnty'] - $_POST['tfrQnty'];
			$sql = "INSERT INTO tblipt_item (rec_id,ipt_id,seq_no,item_id,qnty_inhand,transfer_from) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$balQnty.",".$_POST['plotFrom'].")";
			$res = mysql_query($sql) or die(mysql_error());
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblipt_item");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = mysql_query("SELECT Max(seq_no) as maxid FROM tblipt_item WHERE ipt_id=".$mid);
			$row = mysql_fetch_assoc($sql);
			$sno = $row["maxid"] + 1;
			$sql = "INSERT INTO tblipt_item (rec_id,ipt_id,seq_no,item_id,qnty_inhand,transfer_from) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$_POST['tfrQnty'].",".$_POST['plotTo'].")";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIPT."','I.P.T.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['tfrQnty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			header('Location:iptitem.php?action=new&mid='.$mid);
		}
	} elseif($_POST['submit']=="delete"){
//		$res = mysql_query("DELETE FROM tblipt_item WHERE rec_id=".$rid) or die(mysql_error());
		header('Location:iptitem.php?action=new&mid='.$mid);
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
    <script language="javascript" type="text/javascript">
    function validate_iptitem() {
        var err = "";
        if (document.getElementById("plotTo").value == 0)
            err = "* please select a plot to transfer!\n";
        if (document.getElementById("tfrQnty").value != "" && !IsNumeric(document.getElementById("tfrQnty").value))
            err += "* please input valid (numeric only) quantity of item!\n";
        if (parseFloat(document.getElementById("tfrQnty").value) == 0 || document.getElementById("tfrQnty").value == "")
            err += "* Item's quantity is mandatory field!\n";
        if (parseFloat(document.getElementById("tfrQnty").value) > parseFloat(document.getElementById("itemQnty")
                .value))
            err += "* Transfer quantity is too much than quantity in hand!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }
    </script>
</head>


<body>
    <center>
        <table align="center" cellspacing="0" cellpadding="0" height="250px" width="850px" border="0">
            <tr>
                <td valign="top" colspan="3">
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

                                <table width="100%" cellpadding="0" cellspacing="0" class="Record">
                                    <tr class="Controls">
                                        <td class="th" width="15%">IPT No.:</td>
                                        <?php if(isset($_REQUEST["action"])){
				$ipt_number = ($row1['ipt_no']>999 ? $row1['ipt_no'] : ($row1['ipt_no']>99 && $row1['ipt_no']<1000 ? "0".$row1['ipt_no'] : ($row1['ipt_no']>9 && $row1['ipt_no']<100 ? "00".$row1['ipt_no'] : "000".$row1['ipt_no'])));
				if($row1['ipt_prefix']!=null){$ipt_number = $row1['ipt_prefix']."/".$ipt_number;}
			}
			?>
                                        <td width="35%"><input name="iptNo" id="iptNo" maxlength="15" size="20"
                                                readonly="true" value="<?php echo $ipt_number; ?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" width="15%">IPT Date:</td>
                                        <td width="35%"><input name="iptDate" id="iptDate" maxlength="10" size="10"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo date("d-m-Y",strtotime($row1["ipt_date"]));}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Issue No.:</td>
                                        <?php if(isset($_REQUEST["action"])){
				$issue_number = ($row1['issue_no']>999 ? $row1['issue_no'] : ($row1['issue_no']>99 && $row1['issue_no']<1000 ? "0".$row1['issue_no'] : ($row1['issue_no']>9 && $row1['issue_no']<100 ? "00".$row1['issue_no'] : "000".$row1['issue_no'])));
				if($row1['issue_prefix']!=null){$issue_number = $row1['issue_prefix']."/".$issue_number;}
			}
			?>
                                        <td><input name="issueNo" id="issueNo" maxlength="15" size="20" readonly="true"
                                                value="<?php echo $issue_number; ?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Issue Date:</td>
                                        <td><input name="issueDate" id="issueDate" maxlength="10" size="10"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo date("d-m-Y",strtotime($row1["issue_date"]));}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th">Location:</td>
                                        <td><input name="location" id="location" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row1['location_name'];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th">Issue By:</td>
                                        <td><input name="issueBy" id="issueBy" maxlength="50" size="45" readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row1['staff_name'];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Issue To:</td>
                                        <td><input name="issueTo" id="issueTo" maxlength="50" size="45" readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row1['leader_name'];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <form name="iptitem" method="post" onsubmit="return validate_iptitem()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Record" cellspacing="0" cellpadding="0" width="100%">
                                        <tr class="Controls">
                                            <td class="th" width="13%">Item Name:</td>
                                            <td width="37%"><input name="itemName" id="itemName" maxlength="50"
                                                    size="45" readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["item_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input
                                                    type="hidden" name="item" id="item"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["item_id"];}?>" />
                                            </td>

                                            <td class="th" width="13%">Tfr.from Plot:</td>
                                            <td width="37%"><input name="tfrFrom" id="tfrFrom" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["plot_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input
                                                    type="hidden" name="plotFrom" id="plotFrom"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["transfer_from"];}?>" />
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Qnty.in Hand:</td>
                                            <td><input name="itemQnty" id="itemQnty" maxlength="10" size="15"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["qnty_inhand"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;&nbsp;<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["unit_name"];}?>
                                            </td>

                                            <td class="th">Tfr.to Plot:</td>
                                            <td><select name="plotTo" id="plotTo" style="width:300px">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			$sql_plot=mysql_query("SELECT * FROM plot WHERE location_id=".$row1['location_id']." AND plot_id!=".$row2["transfer_from"]." ORDER BY plot_name");
			while($row_plot=mysql_fetch_array($sql_plot))
			{
				if($row_plot["plot_id"]==$row2["transfer_to"])
					echo '<option selected value="'.$row_plot["plot_id"].'">'.$row_plot["plot_name"].'</option>';
				else
					echo '<option value="'.$row_plot["plot_id"].'">'.$row_plot["plot_name"].'</option>';
			}?>
                                                </select></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th">Tfr.Qnty.:</td>
                                            <td><input name="tfrQnty" id="tfrQnty" maxlength="10" size="15"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["qnty_transfer"];}?>">&nbsp;&nbsp;<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){if($row2['alt_unit']=="N"){echo $row2["unit_name"];} elseif($row2['alt_unit']=="A"){echo "&nbsp;";}} else {echo "";}?>
                                            </td>

                                            <td class="th" id="tblcol1" nowrap>
                                                <?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){if($row2['alt_unit']=="N"){echo "&nbsp;";} elseif($row2['alt_unit']=="A"){echo "Unit:";}} else {echo "&nbsp;";}?>
                                            </td>
                                            <td id="tblcol2"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if($row2['alt_unit']=="N"){ echo "&nbsp;";}
				elseif($row2['alt_unit']=="A" && $row2['alt_unit_id']!=0){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row2['prime_unit_id'].'">'.$prime_unit_name.'</option><option value="'.$row2['alt_unit_id'].'">'.$alt_unit_name.'</option></select>';}
			} else {echo "&nbsp;";}?></td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='iptselection1.php?mid=<?php echo $mid;?>'"><img
                                                        src="images/back.gif" width="72" height="22"
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
                <td valign="top" colspan="3">
                    <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td valign="top">
                                <table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Inter Plot Transfer - [ Item List ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Caption">
                                        <th width="5%">Sl.No.</th>
                                        <th width="25%">Item Name</th>
                                        <th width="15%">Qnty.in Hand</th>
                                        <th width="15%">Tfr.from Plot</th>
                                        <th width="15%">Tfr.to Plot</th>
                                        <th width="15%">Tfr.Qnty.</th>
                                        <th width="5%">Edit</th>
                                        <th width="5%">Del</th>
                                    </tr>

                                    <?php 
		$i = 0;
		$sql_issue = mysql_query("SELECT tblissue2.*, item_name, plot_name FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id  INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id=".$row1['issue_id']." ORDER BY seq_no") or die(mysql_error());
		while($row_issue=mysql_fetch_array($sql_issue))
		{
			$itemId = $row_issue['item_id'];
			$itemName = $row_issue['item_name'];
			$unitName = $row_issue['unit_name'];
			$transfer_from_plot = $row_issue['plot_name'];
			$itemName = $row_issue['item_name'];
			$qnty_in_hand = $row_issue['issue_qnty']-$row_issue['return_qnty'];
			
			$sql_ipt = mysql_query("SELECT tblipt_item.*, unit_name, plot_name FROM tblipt_item INNER JOIN unit ON tblipt_item.unit_id = unit.unit_id INNER JOIN plot ON tblipt_item.transfer_from = plot.plot_id WHERE ipt_id=".$mid." AND item_id=".$row_issue['item_id']." AND transfer_from=".$row_issue['plot_id']." ORDER BY seq_no") or die(mysql_error());
			if(mysql_num_rows($sql_ipt)>0){
				$row_ipt = mysql_fetch_assoc($sql_ipt);
			}
			
			$allow_selection = "yes";
			$transfer_to_plot = "- select -";
			if($row_ipt['transfer_to']!=0){
				$sql = mysql_query("SELECT * FROM plot WHERE plot_id=".$row_ipt['transfer_to']) or die(mysql_error());
				$row = mysql_fetch_assoc($sql);
				$transfer_to_plot = $row['plot_name'];
				$allow_selection = "no";
			}
			
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "iptitem.php?action=delete&mid=".$mid."&rid=".$row_ipt['rec_id'];
			$edit_ref = "iptitem.php?action=edit&mid=".$mid."&rid=".$row_ipt['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$item_name.'</td><td>'.$qnty_in_hand.' '.$unitName.'</td><td>'.$transfer_from_plot.'</td><td>'.$transfer_to_plot.'</td><td>'.$row_ipt['qnty_transfer'].' '.$row_ipt['unit_name'].'</td>';
			if($allow_selection == "yes"){
				if($row_user['ipt2']==1)
					echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				elseif($row_user['ipt2']==0)
					echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				if($row_user['ipt3']==1)
					echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				elseif($row_user['ipt3']==0)
					echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			} elseif($allow_selection == "no"){
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			}
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