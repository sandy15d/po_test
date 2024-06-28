<?php 
include("menu.php");
/*------------------------------------------*/
$sql_user = mysql_query("SELECT pb1,pb2,pb3,pb4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*------------------------------------------*/
$msg = "";
$bid = $_REQUEST['bid'];
$sql = mysql_query("SELECT tblbill.*, party_name, address1, address2, address3, city_name, state_name, company_name FROM tblbill INNER JOIN party ON tblbill.party_id = party.party_id INNER JOIN company ON tblbill.company_id = company.company_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE bill_id=".$bid) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
/*------------------------------------------*/
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$rid = $_REQUEST['rid'];
	$sql2 = mysql_query("SELECT tblbill_item.*, unit_name FROM tblbill_item INNER JOIN unit ON tblbill_item.unit_id = unit.unit_id WHERE rec_id=".$rid) or die(mysql_error());
	$row2 = mysql_fetch_assoc($sql2);
	/*------------------------------------------*/
	$sql1 = mysql_query("SELECT tblpo.*, location_name AS order_location FROM tblpo INNER JOIN location ON tblpo.delivery_at = location.location_id WHERE po_id=".$row2['po_id']) or die(mysql_error());
	$row1 = mysql_fetch_assoc($sql1);
	/*------------------------------------------*/
	$sql3 = mysql_query("SELECT tblreceipt1.*, location_name AS received_location FROM tblreceipt1 INNER JOIN location ON tblreceipt1.recd_at = location.location_id WHERE receipt_id=".$row2['receipt_id']) or die(mysql_error());
	$row3 = mysql_fetch_assoc($sql3);
	/*------------------------------------------*/
	$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
	$row4=mysql_fetch_assoc($sql4);
	if($row4['alt_unit']=="A" && $row4['alt_unit_id']!="0"){
		$sql5=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$row4['alt_unit_id']);
		$row5=mysql_fetch_assoc($sql5);
	}
}
/*------------------------------------------*/
if(isset($_POST['submit'])){
	$dateBill = $row1["bill_date"];
	$sql=mysql_query("SELECT item_name FROM item WHERE item_id=".$_POST['item']);
	$row = mysql_fetch_assoc($sql);
	$itemname=$row["item_name"];
	/*-------------------------------*/
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$itemamt = number_format($_POST['billQnty'] * $_POST['itemRate'],2,".","");
	$particulars = "From ".$row1['party_name'];
	$voucherid = ($bid>999 ? $bid : ($bid>99 && $bid<1000 ? "0".$bid : ($bid>9 && $bid<100 ? "00".$bid : "000".$bid)));
	$count = mysql_num_rows($sql4);
	/*-------------------------------*/
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblbill_item SET bill_id=".$bid.",po_id=".$_POST['poNo'].",receipt_id=".$_POST['mrNo'].",item_id=".$_POST['item'].",unit_id=".$_POST['unit'].",bill_qnty=".$_POST['billQnty'].",rate=".$_POST['itemRate'].",amt=".$itemamt." WHERE rec_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location, action, user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['billQnty'].",".$_POST['itemRate'].",".$itemamt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM tblbill_item WHERE rec_id=".$rid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['billQnty'].",".$_POST['itemRate'].",".$itemamt.",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
	} elseif($_POST['submit']=="new"){
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tblbill_item");
		$row = mysql_fetch_assoc($sql);
		$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tblbill_item WHERE bill_id=".$bid);
		$row = mysql_fetch_assoc($sql);
		$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tblbill_item (rec_id,bill_id,po_id,receipt_id,seq_no,item_id,unit_id,bill_qnty,rate,amt) VALUES(".$rid.",".$bid.",".$_POST['poNo'].",".$_POST['mrNo'].",".$sno.",".$_POST['item'].",".$_POST['unit'].",".$_POST['billQnty'].",".$_POST['itemRate'].",".$itemamt.")";
		$res = mysql_query($sql) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,item_rate,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateBill."','Pur.Bill','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['billQnty'].",".$_POST['itemRate'].",".$itemamt.",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="pbitem.php?action=new&bid='.$bid.'";</script>';
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
    <script language="javascript" type="text/javascript">
    function validate_bill() {
        var err = "";
        if (document.getElementById("poNo").value == 0)
            err = "* please select a Purchase Order number!\n";
        if (document.getElementById("mrNo").value == 0)
            err += "* please select a Material Receipt number!\n";
        if (document.getElementById("item").value == 0)
            err += "* please select an item of the bill!\n";
        if (document.getElementById("billQnty").value != "" && !IsNumeric(document.getElementById("billQnty").value))
            err += "* please input valid quantity of the item!\n";
        if (document.getElementById("billQnty").value == "" || document.getElementById("billQnty").value == 0)
            err += "* Item's Billing quantity is madatory!\n";
        //if(parseFloat(document.getElementById("billQnty").value) > parseFloat(document.getElementById("receivedQnty").value))
        //	err += "* Invalid Item's Billing quantity! excess quantity not acceptable.\n";
        if (document.getElementById("itemRate").value == "" || document.getElementById("itemRate").value == 0)
            err += "* Item's Billing rate is mandatory!\n";
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
        <table align="center" cellspacing="0" cellpadding="0" height="300px" width="875px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td valign="top">
                                <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Purchase Bill - [ Main ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Controls">
                                        <td class="th" nowrap>Bill No.:</td>
                                        <td><input name="billNo" id="billNo" maxlength="15" size="20" readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["bill_no"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Bill Date:</td>
                                        <td><input name="billDate" id="billDate" maxlength="10" size="10"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){echo date("d-m-Y",strtotime($row["bill_date"]));}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Party Name:</td>
                                        <td><input name="partyName" id="partyName" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["party_name"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Bill Amount:</td>
                                        <td><input name="billAmount" id="billAmount" maxlength="10" size="20"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["bill_amt"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Address-1:</td>
                                        <td><input name="address1" id="address1" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["address1"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td class="th" nowrap>Company Name:</td>
                                        <td><input name="company" id="company" maxlength="50" size="45" readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["company_name"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Address-2:</td>
                                        <td><input name="address2" id="address2" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["address2"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>Address-3:</td>
                                        <td><input name="address3" id="address3" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["address3"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>City:</td>
                                        <td><input name="cityName" id="cityName" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["city_name"];}?>"
                                                style="background-color:#E7F0F8; color:#0000FF"></td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr class="Controls">
                                        <td class="th" nowrap>State:</td>
                                        <td><input name="stateName" id="stateName" maxlength="50" size="45"
                                                readonly="true"
                                                value="<?php if(isset($_REQUEST["action"])){ echo $row["state_name"];}?>"
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
                    <form name="billitem" method="post" onsubmit="return validate_bill()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Purchase Bill - [ Item ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" width="10%" nowrap>P.O. No.:<span
                                                    style="color:#FF0000">*</span></td>
                                            <td width="38%">

                                                <select name="poNo" id="poNo" style="width:150px"
                                                    onchange="get_podetails_on_pbill(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			$sql_order=mysql_query("SELECT * FROM tblpo WHERE po_status='S' AND party_id=".$row['party_id']." AND company_id=".$row['company_id']." AND po_date<='".$row['bill_date']."' ORDER BY po_id") or die(mysql_error());
			while($row_order=mysql_fetch_array($sql_order)){
				$po_number = ($row_order['po_no']>999 ? $row_order['po_no'] : ($row_order['po_no']>99 && $row_order['po_no']<1000 ? "0".$row_order['po_no'] : ($row_order['po_no']>9 && $row_order['po_no']<100 ? "00".$row_order['po_no'] : "000".$row_order['po_no'])));
				if($row_order["po_id"]==$row1["po_id"])
					echo '<option selected value="'.$row_order["po_id"].'">'.$po_number.'</option>';
				else
					echo '<option value="'.$row_order["po_id"].'">'.$po_number.'</option>';
			}?>
                                                </select>
                                            </td>

                                            <td class="th" width="14%" nowrap>P.O. Date:</td>
                                            <td width="40%"><input name="poDate" id="poDate" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo ($row1["po_date"]==NULL ? "" : date("d-m-Y",strtotime($row1["po_date"])));} else {echo "";}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>M.R. No.:<span style="color:#FF0000">*</span></td>
                                            <td id="tdMRN"><select name="mrNo" id="mrNo" style="width:150px"
                                                    onchange="get_mrdetails_on_pbill(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new")
				$sql_rcpt=mysql_query("SELECT * FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id WHERE tbldelivery1.po_id=0 ORDER BY receipt_id") or die(mysql_error());
			elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"))
				$sql_rcpt=mysql_query("SELECT * FROM tblreceipt1 INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id WHERE tbldelivery1.po_id=".$row1['po_id']." ORDER BY receipt_id") or die(mysql_error());
			while($row_rcpt=mysql_fetch_array($sql_rcpt)){
				$receipt_number = ($row_rcpt['receipt_no']>999 ? $row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>99 && $row_rcpt['receipt_no']<1000 ? "0".$row_rcpt['receipt_no'] : ($row_rcpt['receipt_no']>9 && $row_rcpt['receipt_no']<100 ? "00".$row_rcpt['receipt_no'] : "000".$row_rcpt['receipt_no'])));
				if($row_rcpt['receipt_prefix']!=null){$receipt_number = $row_rcpt['receipt_prefix']."/".$receipt_number;}
				if($row_rcpt["receipt_id"]==$row3["receipt_id"])
					echo '<option selected value="'.$row_rcpt["receipt_id"].'">'.$receipt_number.'</option>';
				else
					echo '<option value="'.$row_rcpt["receipt_id"].'">'.$receipt_number.'</option>';
			}?>
                                                </select></td>

                                            <td class="th" nowrap>M.R. Date:</td>
                                            <td><input name="mrDate" id="mrDate" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo ($row3["receipt_date"]==NULL ? "" : date("d-m-Y",strtotime($row3["receipt_date"])));} else {echo "";}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Order For :</td>
                                            <td><input name="orderFor" id="orderFor" size="45" readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row1["order_location"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Received At :</td>
                                            <td><input name="recdAt" id="recdAt" size="45" readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row3["received_location"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"><input type="hidden"
                                                    name="locationID" id="locationID"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row3["recd_at"];}?>" /><input
                                                    type="hidden" name="dateBill" id="dateBill"
                                                    value="<?php echo strtotime($row["bill_date"]);?>" /></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Item Name:<span style="color:#FF0000">*</span></td>
                                            <td id="tdITEM"><select name="item" id="item"
                                                    onchange="get_stockNqnty_of_item_on_pbill(this.value, document.getElementById('locationID').value, document.getElementById('dateBill').value, document.getElementById('poNo').value, document.getElementById('mrNo').value)"
                                                    style="width:295px">
                                                    <option value="0">-- Select --</option>
                                                    <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new")
				$sql_item=mysql_query("SELECT * FROM item WHERE item_id=0 ORDER BY item_name");
			elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"))
				$sql_item=mysql_query("SELECT * FROM item WHERE item_id=".$row2['item_id']." ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($row_item["item_id"]==$row2["item_id"])
					echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
				else
					echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			}?>
                                                </select></td>

                                            <?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				$clqnty_prime = 0;
				$clqnty_alt = 0;
				$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$row2["item_id"]." AND location_id=".$row3['recd_at']." AND entry_date<='".$row["bill_date"]."' GROUP BY unit_id") or die(mysql_error());
				while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
					if($row_stk_rgstr['unit_id']==$row4['prime_unit_id']){
						$clqnty_prime += $row_stk_rgstr['qty'];
						$clqnty_alt += $row_stk_rgstr['qty'] * $row4['alt_unit_num'];
					} elseif($row_stk_rgstr['unit_id']==$row4['alt_unit_id']){
						$clqnty_prime += $row_stk_rgstr['qty'] / $row4['alt_unit_num'];
						$clqnty_alt += $row_stk_rgstr['qty'];
					}
				}
				
				$ordqnty_prime = 0;
				$ordqnty_alt = 0;
				$sql_ord = mysql_query("SELECT Sum(qnty) AS qty, unit_id FROM tblpo_item WHERE po_id=".$row1['po_id']." AND item_id=".$row2["item_id"]." GROUP BY unit_id") or die(mysql_error());
				while($row_ord=mysql_fetch_array($sql_ord)){
					if($row_ord['unit_id']==$row4['prime_unit_id']){
						$ordqnty_prime += $row_ord['qty'];
						$ordqnty_alt += $row_ord['qty'] * $row4['alt_unit_num'];
					} elseif($row_ord['unit_id']==$row4['alt_unit_id']){
						$ordqnty_prime += $row_ord['qty'] / $row4['alt_unit_num'];
						$ordqnty_alt += $row_ord['qty'];
					}
				}
				
				$rcdqnty_prime = 0;
				$rcdqnty_alt = 0;
				$sql_rcd = mysql_query("SELECT Sum(receipt_qnty) AS qty, unit_id FROM tblreceipt2 WHERE receipt_id=".$row3['receipt_id']." AND item_id=".$row2["item_id"]." GROUP BY unit_id") or die(mysql_error());
				while($row_rcd=mysql_fetch_array($sql_rcd)){
					if($row_rcd['unit_id']==$row4['prime_unit_id']){
						$rcdqnty_prime += $row_rcd['qty'];
						$rcdqnty_alt += $row_rcd['qty'] * $row4['alt_unit_num'];
					} elseif($row_rcd['unit_id']==$row4['alt_unit_id']){
						$rcdqnty_prime += $row_rcd['qty'] / $row4['alt_unit_num'];
						$rcdqnty_alt += $row_rcd['qty'];
					}
				}
			}?>
                                            <td class="th" nowrap>Stock On Date:</td>
                                            <td><input name="itemStock" id="itemStock" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo number_format($clqnty_prime,3,".","");}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span
                                                    id="spanUnit1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row4['prime_unit_name']; if($row4['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$row5['alt_unit_name'].')</span>';}} else {echo "";}?></span>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Order Qnty.:</td>
                                            <td><input name="orderQnty" id="orderQnty" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo number_format($ordqnty_prime,3,".","");}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span
                                                    id="spanUnit2"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row4['prime_unit_name']; if($row4['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($ordqnty_alt,3,".","")." ".$row5['alt_unit_name'].')</span>';}} else {echo "";}?></span>
                                            </td>

                                            <td class="th" nowrap>Recd.Qnty.:</td>
                                            <td><input name="receivedQnty" id="receivedQnty" maxlength="10" size="10"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo number_format($rcdqnty_prime,3,".","");}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span
                                                    id="spanUnit3"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo $row4['prime_unit_name']; if($row4['alt_unit']=="A"){echo '<br><span style="font-size: 10px;">('.number_format($rcdqnty_alt,3,".","")." ".$row5['alt_unit_name'].')</span>';}} else {echo "";}?></span>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Billing Qnty.:<span style="color:#FF0000">*</span>
                                            </td>
                                            <td><input name="billQnty" id="billQnty" maxlength="10" size="10"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["bill_qnty"];}?>">
                                            </td>

                                            <td class="th" nowrap>Unit :<span style="color:#FF0000">*</span></td>
                                            <td id="tblcol1"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($row4['alt_unit']=="N") || ($row4['alt_unit']=="A" && $row4['alt_unit_id']==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option></select>';
				} elseif($row4['alt_unit']=="A" && $row4['alt_unit_id']!=0){
					if($row2['unit_id']==$row4['prime_unit_id']){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option><option value="'.$row4['alt_unit_id'].'">'.$row5['alt_unit_name'].'</option></select>';
					} elseif($row2['unit_id']==$row4['alt_unit_id']){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$row4['prime_unit_id'].'">'.$row4['prime_unit_name'].'</option><option selected value="'.$row4['alt_unit_id'].'">'.$row5['alt_unit_name'].'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"><option value="0">&nbsp;</option></select>';
			}?></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Rate :<span style="color:#FF0000">*</span></td>
                                            <td><input name="itemRate" id="itemRate" maxlength="10" size="10"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row2["rate"];}?>">
                                            </td>

                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['pb1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['pb1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.billitem.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="82"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='pbitem.php?action=new&bid=<?php echo $bid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='pbitem.php?action=new&bid=<?php echo $bid;?>'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline;cursor:hand;" border="0" /></a>
                                                <?php }?>
                                                &nbsp;&nbsp;<a
                                                    href="javascript:window.location='purchasebill.php?action=new'"><img
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
                                <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                        <td class="th"><strong>Purchase Bill - [ Item List ]</strong></td>
                                        <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                    </tr>
                                </table>

                                <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                    <tr class="Caption">
                                        <th width="4%">Sl.No.</th>
                                        <th width="30%">Item Name</th>
                                        <th width="10%">P.O. No.</th>
                                        <th width="15%">M.R. No.</th>
                                        <th width="15%">Item Qnty.</th>
                                        <th width="10%">Rate</th>
                                        <th width="10%">Amount</th>
                                        <th width="3%">Edit</th>
                                        <th width="3%">Del</th>
                                    </tr>

                                    <?php 
		$i = 0;
                
		$sql_bill = mysql_query("SELECT tblbill_item.*, item_name, unit_name, po_no, receipt_no, receipt_prefix FROM tblbill_item INNER JOIN item ON tblbill_item.item_id = item.item_id INNER JOIN unit ON tblbill_item.unit_id = unit.unit_id INNER JOIN tblpo ON tblbill_item.po_id = tblpo.po_id INNER JOIN tblreceipt1 ON tblbill_item.receipt_id = tblreceipt1.receipt_id WHERE bill_id=".$bid." ORDER BY seq_no") or die(mysql_error());
		while($row_bill=mysql_fetch_array($sql_bill)){
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "pbitem.php?action=delete&bid=".$bid."&rid=".$row_bill['rec_id'];
			$edit_ref = "pbitem.php?action=edit&bid=".$bid."&rid=".$row_bill['rec_id'];
			$pur_order_number = ($row_bill['po_no']>999 ? $row_bill['po_no'] : ($row_bill['po_no']>99 && $row_bill['po_no']<1000 ? "0".$row_bill['po_no'] : ($row_bill['po_no']>9 && $row_bill['po_no']<100 ? "00".$row_bill['po_no'] : "000".$row_bill['po_no'])));
			$rcpt_number = ($row_bill['receipt_no']>999 ? $row_bill['receipt_no'] : ($row_bill['receipt_no']>99 && $row_bill['receipt_no']<1000 ? "0".$row_bill['receipt_no'] : ($row_bill['receipt_no']>9 && $row_bill['receipt_no']<100 ? "00".$row_bill['receipt_no'] : "000".$row_bill['receipt_no'])));
			if($row_bill['receipt_prefix']!=null){$rcpt_number = $row_bill['receipt_prefix']."/".$rcpt_number;}
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_bill['item_name'].'</td><td>'.$pur_order_number.'</td><td>'.$rcpt_number.'</td><td>'.$row_bill['bill_qnty']." ".$row_bill['unit_name'].'</td><td>'.$row_bill['rate'].'</td><td>'.$row_bill['amt'].'</td>';
			if($row_user['pb2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pb2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['pb3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pb3']==0)
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