<?php 
include("menu.php");
/*----------------------------------------*/
$sql_user = mysql_query("SELECT mi1,mi2,mi3,mi4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*----------------------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblissue1.*, location_name, staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE issue_id=".$mid);
$row1 = mysql_fetch_assoc($sql1);
$location_id = $row1['location_id'];
$issue_number = ($row1['issue_no']>999 ? $row1['issue_no'] : ($row1['issue_no']>99 && $row1['issue_no']<1000 ? "0".$row1['issue_no'] : ($row1['issue_no']>9 && $row1['issue_no']<100 ? "00".$row1['issue_no'] : "000".$row1['issue_no'])));
if($row1['issue_prefix']!=null){$issue_number = $row1['issue_prefix']."/".$issue_number;}
$issue_date = date("d-m-Y",strtotime($row1["issue_date"]));
$location_name = $row1['location_name'];
$staff_name = $row1['staff_name'];
$issue_to = $row1['issue_to'];
/*----------------------------------------*/
$rid = 0;
$item_id = 0;
$category_id =0;
$issue_qnty = "";
$issue_unit = 0;
$plot_id = 0;
$clqnty_prime = 0;
$clqnty_alt = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
if(isset($_REQUEST['rid'])){
	$rid = $_REQUEST['rid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2=mysql_query("SELECT tblissue2.*, unit_name FROM tblissue2 INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE rec_id=".$rid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
        $item_id = $row2["item_id"];
        $category_id =$row['item_category'];
		$issue_qnty = $row2["issue_qnty"];
		$issue_unit = $row2['issue_unit'];
		$plot_id = $row2["plot_id"];
		/*----------------------------------------*/
		$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
		$row4=mysql_fetch_assoc($sql4);
		$prime_unit_id = $row4['prime_unit_id'];
		$prime_unit_name = $row4['prime_unit_name'];
		$alt_unit = $row4['alt_unit'];
		$alt_unit_id = $row4['alt_unit_id'];
		$alt_unit_num = $row4['alt_unit_num'];
		
		if($alt_unit=="A" && $alt_unit_id!="0"){
			$sql5=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$alt_unit_id);
			$row5=mysql_fetch_assoc($sql5);
			$alt_unit_name = $row5['alt_unit_name'];
		}
		/*----------------------------------------*/
		$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$item_id." AND location_id=".$location_id." AND entry_date<='".date("Y-m-d",strtotime($issue_date))."' GROUP BY unit_id") or die(mysql_error());
		while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
			if($row_stk_rgstr['unit_id']==$prime_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'];
				$clqnty_alt += $row_stk_rgstr['qty'] * $alt_unit_num;
			} elseif($row_stk_rgstr['unit_id']==$alt_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'] / $alt_unit_num;
				$clqnty_alt += $row_stk_rgstr['qty'];
			}
		}
	}
}
/*----------------------------------------*/
if(isset($_POST['submit'])){
	$sql=mysql_query("SELECT item_name, item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname=$row["item_name"];
    /*-------------------------------*/
    /* --------------------------------------------- */
     $sql = mysql_query("SELECT category FROM item_category WHERE category_id =".$_POST['category_name']);
     $row =mysql_fetch_assoc($sql);
     $category_name =$row["category"];
    /* --------------------------------------------- */
	if($row['prime_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'];
	} elseif($row['alt_unit_id']==$_POST['unit']){
		$unitid = $row['prime_unit_id'];
		$itemQnty = $_POST['itemQnty'] / $row['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$sql = mysql_query("SELECT plot_name FROM plot WHERE plot_id=".$_POST['plot']) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	/*-------------------------------*/
	$particulars = $row1['location_name']."/".$row["plot_name"];
	$dateIssue=$row1["issue_date"];
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	/*-------------------------------*/
	$sql = mysql_query("SELECT * FROM tblissue2 WHERE issue_id=".$mid." AND item_id=".$_POST['item']." AND item_category =".$_POST['category_name']." AND issue_qnty=".$_POST['itemQnty']." AND plot_id=".$_POST['plot']) or die(mysql_error());
	$row_issue = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_issue['rec_id']!=$rid)
				echo '<script language="javascript">alert("Duplication Error! can\'t update into material issue record.");</script>';
			elseif($row_issue['rec_id']==$rid){
				$res = mysql_query("UPDATE tblissue2 SET item_id=".$_POST['item'].",issue_unit=".$_POST['unit'].",issue_qnty=".$_POST['itemQnty'].",plot_id=".$_POST['plot']." WHERE rec_id=".$rid) or die(mysql_error());
				$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=0-".$itemQnty." WHERE entry_mode='I+' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="issueitem.php?action=new&mid='.$mid.'";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE tblissue2 SET item_id=".$_POST['item'].",issue_unit=".$_POST['unit'].",issue_qnty=".$_POST['itemQnty'].",plot_id=".$_POST['plot']." WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=0-".$itemQnty." WHERE entry_mode='I+' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="issueitem.php?action=new&mid='.$mid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblissue2 WHERE rec_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='I+' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="issueitem.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			echo '<script language="javascript">alert("Duplication Error! can\'t insert into material issue record.");</script>';
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblissue2");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblissue2 WHERE issue_id=".$mid);
			$row = mysql_fetch_assoc($sql);
			$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
                        // echo "INSERT INTO tblissue2 (rec_id,issue_id,seq_no,item_id,item_category,issue_unit,issue_qnty,plot_id) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$_POST['category_name'].",".$_POST['unit'].",".$_POST['itemQnty'].",".$_POST['plot'].")";
			$sql = "INSERT INTO tblissue2 (rec_id,issue_id,seq_no,item_id,item_category,issue_unit,issue_qnty,plot_id) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$_POST['category_name'].",".$_POST['unit'].",".$_POST['itemQnty'].",".$_POST['plot'].")";
			$res = mysql_query($sql) or die(mysql_error());
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt) VALUES(".$sid.",'I+',".$mid.",'".$dateIssue."',".$sno.",".$row1['location_id'].",".$_POST['item'].",".$_POST['category_name'].",".$unitid.",0-".$itemQnty.",0,0)";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','Mtrl.Issue','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="issueitem.php?action=new&mid='.$mid.'";</script>';
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
    <script language="javascript" type="text/javascript">
    function validate_issue() {
        var err = "";
        if (document.getElementById("item").value == 0)
            err = "* please select an item to issue!\n";
        if (document.getElementById("itemQnty").value != "" && !IsNumeric(document.getElementById("itemQnty").value))
            err += "* please input valid quantity of the item!\n";
        if (parseFloat(document.getElementById("itemQnty").value) == 0 || document.getElementById("itemQnty").value ==
            "")
            err += "* Item's quantity is mandatory field!\n";
        if (document.getElementById("plot").value == 0)
            err += "* please select a plot against issue!\n";
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
        <table align="center" cellspacing="0" cellpadding="0" height="320px" width="675px" border="0">
            <tr>
                <td valign="top" colspan="3">
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
                                        <td><input name="issueNo" id="issueNo" maxlength="15" size="20" readonly="true"
                                                value="<?php echo $issue_number; ?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Issue Date:</td>
                                        <td><input name="issueDate" id="issueDate" maxlength="10" size="10"
                                                readonly="true"
                                                value="<?php echo date('Y-m-d',strtotime($issue_date));?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th">Location:</td>
                                        <td><input name="locationName" id="locationName" maxlength="50" size="45"
                                                readonly="true" value="<?php echo $location_name;?>"
                                                style="background-color:#E7F0F8; color:#0000FF"><input type="hidden"
                                                name="location" id="location" value="<?php echo $location_id;?>"></td>

                                        <td class="th">Issue By:</td>
                                        <td><input name="staffName" id="staffName" maxlength="50" size="45"
                                                readonly="true" value="<?php echo $staff_name;?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Issue To:</td>
                                        <td><input name="issueTo" id="issueTo" maxlength="50" size="45" readonly="true"
                                                value="<?php echo $issue_to;?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <form name="issueitem" method="post" onsubmit="return validate_issue()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Grid" cellspacing="0" cellpadding="0" width="100%">
                                        <tr class="Controls">
                                            <td class="th" width="10%" nowrap>Item Name:<span
                                                    style="color:#FF0000">*</span></td>
                                            <td width="40%"><select name="item" id="item" style="width:300px">
                                                    <option value="0">-- Select --</option><?php 
			$sql_item=mysql_query("SELECT * FROM item ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($row_item["item_id"]==$item_id)
					echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
				else
					echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			}?>
                                                </select></td>

                                            <td class="th" width="10%" nowrap>Item Category:<span
                                                    style="color:#FF0000">*</span></td>
                                            <td width="30%"><select name="category_name" id="category_name"
                                                    style="width:200px">
                                                    <?php 
			$sql_category=mysql_query("SELECT * FROM item_category WHERE  item_id =$item_id");
			while($row_category=mysql_fetch_array($sql_category)){
				if($row_category["category_id"]==$category_id)
					echo '<option selected value="'.$row_category["category_id"].'">'.$row_category["category"].'</option>';
				else
					echo '<option value="'.$row_category["category_id"].'">'.$row_category["category"].'</option>';
			}?>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Issue Qnty.:<span style="color:#FF0000">*</span></td>
                                            <td><input name="itemQnty" id="itemQnty" maxlength="10" size="10"
                                                    value="<?php echo $issue_qnty;?>"></td>

                                            <td class="th" nowrap>Unit :</td>
                                            <td id="tblcol1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($alt_unit=="N") || ($alt_unit=="A" && $alt_unit_id==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option></select>';
				} elseif($alt_unit=="A" && $alt_unit_id!=0){
					if($issue_unit==$prime_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					} elseif($issue_unit==$alt_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option selected value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"></select>';
			}?></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Plot No.:<span style="color:#FF0000">*</span></td>
                                            <td><select name="plot" id="plot" style="width:200px">
                                                    <option value="0">-- Select --</option><?php 
			$sql_plot=mysql_query("SELECT * FROM plot ORDER BY plot_name"); //WHERE location_id=".$location_id." 
			while($row_plot=mysql_fetch_array($sql_plot)){
				if($row_plot["plot_id"]==$plot_id)
					echo '<option selected value="'.$row_plot["plot_id"].'">'.$row_plot["plot_name"].'</option>';
				else
					echo '<option value="'.$row_plot["plot_id"].'">'.$row_plot["plot_name"].'</option>';
			}?>
                                                </select></td>

                                            <td class="th" width="10%" nowrap>Stock On Date:</td>
                                            <td width="40%"><input name="itemStock" id="itemStock" maxlength="10"
                                                    size="10" readonly="true"
                                                    value="<?php echo number_format($clqnty_prime,3,".","");?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span
                                                    id="spanUnit1"><?php echo $prime_unit_name; if($alt_unit=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';} else {echo "";}?></span>
                                            </td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['mi1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['mi1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.issueitem.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='issueitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='issueitem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='materialissue1.php?mid=<?php echo $mid;?>'"><img
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
                                        <td class="th"><strong>Material Issue - [ Item List ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Caption">
                                        <th width="5%">Sl.No.</th>
                                        <th width="35%">Item Name</th>
                                        <th width="15%">Issue Qnty.</th>
                                        <th width="10%">Unit</th>
                                        <th width="25%">Plot No.</th>
                                        <th width="5%">Edit</th>
                                        <th width="5%">Del</th>
                                    </tr>

                                    <?php 
		$i = 0;
		$sql_issue = mysql_query("SELECT tblissue2.*, item_name, unit_name, plot_name,ic.category FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id INNER JOIN plot ON tblissue2.plot_id = plot.plot_id
        INNER JOIN item_category ic ON ic.category_id = tblissue2.item_category
        WHERE issue_id=".$mid." ORDER BY seq_no") or die(mysql_error());

//$sql_issue = mysql_query("SELECT tblissue2.*, item_name, unit_name FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id WHERE issue_id=".$mid." ORDER BY seq_no") or die(mysql_error());

		while($row_issue=mysql_fetch_array($sql_issue)){
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "issueitem.php?action=delete&mid=".$mid."&rid=".$row_issue['rec_id'];
			$edit_ref = "issueitem.php?action=edit&mid=".$mid."&rid=".$row_issue['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_issue['item_name'].' ~~'.$row_issue['category'].'</td><td align="center">'.$row_issue['issue_qnty'].'</td><td>'.$row_issue['unit_name'].'</td><td>'.$row_issue['plot_name'].'</td>';
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

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>
<script src="js/jquery.min.js"></script>
<script>
$(document).on('change', '#item', function() {
    var item = $(this).val();
    $.ajax({
        type: 'post',
        url: 'order_indent_curd.php',
        data: {
            action: 'get_category',
            'item': item

        },
        dataType: 'json',
        success: function(response) {

            var x = '<option value="">Select Category</option>';
            if (response.status == 200) {
                $.each(response.data, function(key, value) {
                    x = x + '<option value="' + value.category_id + '">' + value
                        .category +
                        '</option>';
                });
            }
            $('#category_name').html(x);

        }

    });
});

//==========================on change item category====================
$(document).on('change', '#category_name', function() {
    var category = $(this).val();
    var item = $('#item').val();
    var location = $('#location').val();
    var entry_date = $('#issueDate').val();

    $.ajax({
        type: 'post',
        url: 'order_indent_curd.php',
        data: {
            action: 'get_stock',
            'item': item,
            'location': location,
            'entry_date': entry_date,
            'category': category
        },
        dataType: 'json',
        success: function(response) {
            if (response.success == true) {
                if (response.data.qty == 0) {
                    $('#itemStock').val('0.00');

                } else {
                    $('#itemStock').val(response.data.qty);
                    $('#unit').prepend("<option value=" + response.data.unit_id + ">" + response
                        .data.unit_name + "</option>");
                }
            } else {
                alert('Something went wrong...!!!');
            }


        }

    });
});
</script>

</html>