<?php 
include("menu.php");
/*---------------------------*/
$sql_user = mysql_query("SELECT ilt1,ilt2,ilt3,ilt4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*---------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, staff_name FROM tblilt1 INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id WHERE ilt_id=".$mid) or die(mysql_error());
$row1 = mysql_fetch_assoc($sql1);
$ilt_number = ($row1['ilt_no']>999 ? $row1['ilt_no'] : ($row1['ilt_no']>99 && $row1['ilt_no']<1000 ? "0".$row1['ilt_no'] : ($row1['ilt_no']>9 && $row1['ilt_no']<100 ? "00".$row1['ilt_no'] : "000".$row1['ilt_no'])));
if($row1['ilt_prefix']!=null){$ilt_number = $row1['ilt_prefix']."/".$ilt_number;}
$ilt_date = date("d-m-Y",strtotime($row1["ilt_date"]));
$location_id = $row1['despatch_from'];
$sourceLocation = $row1['sourceLocation'];
$destinationLocation = $row1['destinationLocation'];
$staff_name = $row1['staff_name'];
$despatch_mode = ($row1['despatch_mode']==1 ? "Hand Delivery" : "By Vehicle");
$vehicle_num = $row1['vehicle_num'];
/*---------------------------*/
if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="send"){
	$sql2 = mysql_query("SELECT * FROM tblilt2 WHERE ilt_id=".$mid);
	$row2 = mysql_fetch_assoc($sql2);
	$count = mysql_num_rows($sql2);
	if($count>0){
		$res = mysql_query("UPDATE tblilt1 SET despatch_status='S' WHERE ilt_id=".$mid) or die(mysql_error());
		echo '<script language="javascript">window.location="iltdespatch.php?action=new";</script>';
	} elseif($count==0){
		$msg = "Sorry! This transfer can not send, since having no item! Please retry....";
		echo '<script language="javascript">window.location="iltditem.php?action=new&mid='.$mid.'&msg='.$msg.'";</script>';
	}
}
/*---------------------------*/
if(isset($_REQUEST["msg"])){
	$msg = $_REQUEST['msg'];
	unset($_REQUEST['msg']);
}
/*---------------------------*/
$rid = 0;
$item_id = 0;
$despatch_qnty = "";
$unit_id = 0;
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
		$sql2 = mysql_query("SELECT tblilt2.*, unit_name FROM tblilt2 INNER JOIN unit ON tblilt2.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_id = $row2["item_id"];
		$despatch_qnty = $row2["despatch_qnty"];
		$unit_id = $row2['unit_id'];
		/*---------------------------*/
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
		/*---------------------------*/
		$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$item_id." AND location_id=".$location_id." AND entry_date<='".date("Y-m-d",strtotime($ilt_date))."' GROUP BY unit_id") or die(mysql_error());
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
/*---------------------------*/
if(isset($_POST['submit'])){
	$dateILT=$row1["ilt_date"];
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	$particulars = "From ".$row1['sourceLocation']." To ".$row1['destinationLocation'];
	/*-------------------------------*/
	$sql=mysql_query("SELECT item_name, item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname=$row["item_name"];
	/*-------------------------------*/
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
	$sql = mysql_query("SELECT * FROM tblilt2 WHERE ilt_id=".$mid." AND item_id=".$_POST['item']) or die(mysql_error());
	$row_ilt = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_ilt['rec_id']!=$rid)
				$msg = "Duplication Error! can&prime;t update into inter location transfer record.";
			elseif($row_ilt['rec_id']==$rid){
				$res=mysql_query("UPDATE tblilt2 SET item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",despatch_qnty=".$_POST['itemQnty']." WHERE rec_id=".$rid) or die(mysql_error());
				$res=mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".(-1*$itemQnty)." WHERE entry_mode='T-' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['despatch_from']) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Dspch.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query($sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="iltditem.php?action=new&mid='.$mid.'";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE tblilt2 SET item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",despatch_qnty=".$_POST['itemQnty']." WHERE rec_id=".$rid) or die(mysql_error());
			$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['item'].",unit_id=".$unitid.",item_qnty=".(-1*$itemQnty)." WHERE entry_mode='T-' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['despatch_from']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Dspch.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="iltditem.php?action=new&mid='.$mid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblilt2 WHERE rec_id=".$rid) or die(mysql_error());
		$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='T-' AND entry_id=".$mid." AND entry_date='".$dateILT."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['despatch_from']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Dspch.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="iltditem.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into inter location transfer record.";
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblilt2");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblilt2 WHERE ilt_id=".$mid);
			$row = mysql_fetch_assoc($sql);
			$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tblilt2 (rec_id,ilt_id,seq_no,item_id,unit_id,despatch_qnty,receive_qnty) VALUES(".$rid.",".$mid.",".$sno.",".$_POST['item'].",".$_POST['unit'].",".$_POST['itemQnty'].",0)";
			$res = mysql_query($sql) or die(mysql_error());
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,unit_id,item_qnty,item_rate) VALUES(".$sid.",'T-',".$mid.",'".$dateILT."',".$sno.",".$row1['despatch_from'].",".$_POST['item'].",".$unitid.",".(-1*$itemQnty).",0)";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateILT."','ILT.Dspch.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['itemQnty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="iltditem.php?action=new&mid='.$mid.'";</script>';
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
    function validate_ilt_item() {
        var err = "";
        if (document.getElementById("item").value == 0)
            err = "* please select an item to issue!\n";
        if (document.getElementById("itemQnty").value != "" && !IsNumeric(document.getElementById("itemQnty").value))
            err += "* please input valid (numeric only) quantity of the item!\n";
        if (parseFloat(document.getElementById("itemQnty").value) == 0 || document.getElementById("itemQnty").value ==
            "")
            err += "* Item's quantity is mandatory field!\n";
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
        <table align="center" cellspacing="0" cellpadding="0" height="250px" width="825px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td valign="top">
                                <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Inter Location Transfer - [ Despatch Main ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Controls">
                                        <td class="th" nowrap>ILT No.:</td>
                                        <td><input name="iltNo" id="iltNo" maxlength="15" size="20" readonly="true"
                                                value="<?php echo $ilt_number; ?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>ILT Date:</td>
                                        <td><input name="iltDate" id="iltDate" maxlength="10" size="10" readonly="true"
                                                value="<?php echo $ilt_date;?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th">Despatch From:</td>
                                        <td><input name="despatchFrom" id="despatchFrom" maxlength="50" size="45"
                                                readonly="true" value="<?php echo $sourceLocation;?>"
                                                style="background-color:#E7F0F8; color:#0000FF"><input type="hidden"
                                                name="location" id="location" value="<?php echo $location_id;?>"></td>

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

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
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
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" colspan="3">
                    <form name="iltditem" method="post" onsubmit="return validate_ilt_item()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Grid" cellspacing="0" cellpadding="0" width="100%">
                                        <tr class="Controls">
                                            <td width="14%" nowrap class="th">Item Name:</td>
                                            <td width="38%"><select name="item" id="item"
                                                    onchange="get_curent_stock_of_item(this.value)" style="width:300px">
                                                    <option value="0">-- Select --</option><?php 
			$sql_item=mysql_query("SELECT * FROM item ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($row_item["item_id"]==$item_id)
					echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
				else
					echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			}?>
                                                </select></td>

                                            <td class="th" width="12%" nowrap>Stock On Date:</td>
                                            <td width="36%"><input name="itemStock" id="itemStock" maxlength="10"
                                                    size="10" readonly="true"
                                                    value="<?php echo number_format($clqnty_prime,3,".","");?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span
                                                    id="spanUnit1"><?php echo $prime_unit_name; if($alt_unit=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';} else {echo "";}?></span>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Despatch Qnty.:</td>
                                            <td><input name="itemQnty" id="itemQnty" maxlength="10" size="15"
                                                    value="<?php echo $despatch_qnty;?>"></td>

                                            <td class="th" nowrap>Unit :</td>
                                            <td id="tblcol1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($alt_unit=="N") || ($alt_unit=="A" && $alt_unit_id==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option></select>';
				} elseif($alt_unit=="A" && $alt_unit_id!=0){
					if($unit_id==$prime_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					} elseif($unit_id==$alt_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option selected value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"><option value="0">&nbsp;</option></select>';
			}?></td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['ilt1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['ilt1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.ilt.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltditem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltditem.php?action=new&mid=<?php echo $mid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltdespatch1.php?mid=<?php echo $mid;?>'"><img
                                                        src="images/back.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='iltditem.php?action=send&mid=<?php echo $mid;?>'"><img
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
                <td valign="top" colspan="3">
                    <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td valign="top">
                                <table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Inter Location Transfer - [ Despatch Item List ]</strong>
                                        </td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Caption">
                                        <th width="5%">Sl.No.</th>
                                        <th width="45%">Item Name</th>
                                        <th width="20%">Despatch Qnty.</th>
                                        <th width="20%">Received Qnty.</th>
                                        <th width="5%">Edit</th>
                                        <th width="5%">Del</th>
                                    </tr>

                                    <?php 
		$i = 0;
		$sql_ilt = mysql_query("SELECT tblilt2.*, item_name, unit_name FROM tblilt2 INNER JOIN item ON tblilt2.item_id = item.item_id INNER JOIN unit ON tblilt2.unit_id = unit.unit_id WHERE ilt_id=".$mid." ORDER BY seq_no") or die(mysql_error());
		while($row_ilt=mysql_fetch_array($sql_ilt)){
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "iltditem.php?action=delete&mid=".$mid."&rid=".$row_ilt['rec_id'];
			$edit_ref = "iltditem.php?action=edit&mid=".$mid."&rid=".$row_ilt['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_ilt['item_name'].'</td><td>'.$row_ilt['despatch_qnty']." ".$row_ilt['unit_name'].'</td><td>'.$row_ilt['receive_qnty']." ".$row_ilt['unit_name'].'</td>';
			if($row_user['ilt2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ilt2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ilt3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ilt3']==0)
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