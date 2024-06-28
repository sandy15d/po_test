<?php 
include("menu.php");
/*----------------------------------------*/
$sql_user = mysql_query("SELECT pay1,pay2,pay3,pay4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
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
$pid = "";
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$pid = $_REQUEST['pid'];
	$sql = mysql_query("SELECT tblpayment1.*, party_name, address1, address2, address3, city_name, state_name, company_name FROM tblpayment1 INNER JOIN party ON tblpayment1.party_id = party.party_id INNER JOIN company ON tblpayment1.company_id = company.company_id INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE pay_id=".$pid) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
}
/*----------------------------------------*/
if(isset($_POST['submit'])){
	$datePayment=substr($_POST['paymentDate'],6,4)."-".substr($_POST['paymentDate'],3,2)."-".substr($_POST['paymentDate'],0,2);
	$dateCheque=substr($_POST['chequeDate'],6,4)."-".substr($_POST['chequeDate'],3,2)."-".substr($_POST['chequeDate'],0,2);
	$paidAmt = ($_POST['paidAmount']=="" ? 0 : $_POST['paidAmount']);
	$particulars = "To ".$_POST['partyName'];
	$itemname = $_POST['companyName'];
	/*----------------------------------------*/
	$sql = mysql_query("SELECT pay_id FROM tblpayment1 WHERE pay_date='".$datePayment."' AND party_id=".$_POST['party']." AND company_id=".$_POST['company']." AND pay_type=".$_POST['payType']) or die(mysql_error());
	$row_pay = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*----------------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_pay['pay_id']!=$pid)
				$msg = "Duplication Error! can&prime;t update into bill payment record.";
			elseif($row_pay['pay_id']==$pid) {
				if($row['party_id']!=$_POST['party']){
					$sqlpay = mysql_query("SELECT * FROM tblpayment2 WHERE pay_id=".$pid." ORDER BY bill_id") or die(mysql_error());
					while($rowpay=mysql_fetch_array($sqlpay))
					{
						$res = mysql_query("UPDATE tblbill SET bill_paid='N' WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
					}
					$res = mysql_query("DELETE FROM tblpayment2 WHERE pay_id=".$pid) or die(mysql_error());
				}
				$res = mysql_query("UPDATE tblpayment1 SET pay_date='".$datePayment."',party_id=".$_POST['party'].",company_id=".$_POST['company'].",pay_type=".$_POST['payType'].",chq_no='".$_POST['chequeNumber']."',chq_date='".$dateCheque."',bank_id=".$_POST['bankName'].",pay_amt=".$paidAmt.",remark='".$_POST['remarks']."' WHERE pay_id=".$pid) or die(mysql_error());
				//insert into logbook
				$sql = mysql_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
				$row = mysql_fetch_assoc($sql);
				$recordid = $row["maxid"] + 1;
				$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
				$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
				$res = mysql_query(DATABASE3,$sql) or die(mysql_error());
				//end of inserting record into logbook
				echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
			}
		} elseif($count==0){
			if($row['party_id']!=$_POST['party']){
				$sqlpay = mysql_query("SELECT * FROM tblpayment2 WHERE pay_id=".$pid." ORDER BY bill_id") or die(mysql_error());
				while($rowpay=mysql_fetch_array($sqlpay))
				{
					$res = mysql_query("UPDATE tblbill SET bill_paid='N' WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
				}
				$res = mysql_query("DELETE FROM tblpayment2 WHERE pay_id=".$pid) or die(mysql_error());
			}
			$res = mysql_query("UPDATE tblpayment1 SET pay_date='".$datePayment."',party_id=".$_POST['party'].",company_id=".$_POST['company'].",pay_type=".$_POST['payType'].",chq_no='".$_POST['chequeNumber']."',chq_date='".$dateCheque."',bank_id=".$_POST['bankName'].",pay_amt=".$paidAmt.",remark='".$_POST['remarks']."' WHERE pay_id=".$pid) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query(DATABASE3,"SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query(DATABASE3,$sql) or die(mysql_error());
			//end of inserting record into logbook
			echo '<script language="javascript">window.location="paybill.php?action=new&pid='.$pid.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$sqlpay = mysql_query("SELECT * FROM tblpayment2 WHERE pay_id=".$pid." ORDER BY bill_id") or die(mysql_error());
		while($rowpay=mysql_fetch_array($sqlpay))
		{
			$res = mysql_query("UPDATE tblbill SET bill_paid='N' WHERE bill_id=".$rowpay['bill_id']) or die(mysql_error());
		}
		$res = mysql_query("DELETE FROM tblpayment1 WHERE pay_id=".$pid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tblpayment2 WHERE pay_id=".$pid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = $row["maxid"] + 1;
		$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
		$sql = mysql_query("INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')");
		//$res = mysql_query(DATABASE3,$sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="payment.php?action=new";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into bill payment record.";
		else {
			$sql = mysql_query("SELECT Max(pay_id) as maxid FROM tblpayment1");
			$row = mysql_fetch_assoc($sql);
			$pid = $row["maxid"] + 1;
			$sql = "INSERT INTO tblpayment1 (pay_id,pay_date,party_id,company_id,pay_type,chq_no,chq_date,bank_id,pay_amt,remark) VALUES(".$pid.",'".$datePayment."',".$_POST['party'].",".$_POST['company'].",".$_POST['payType'].",'".$_POST['chequeNumber']."','".$dateCheque."',".$_POST['bankName'].",".$paidAmt.",'".$_POST['remarks']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = $row["maxid"] + 1;
			$voucherid = ($pid>999 ? $pid : ($pid>99 && $pid<1000 ? "0".$pid : ($pid>9 && $pid<100 ? "00".$pid : "000".$pid)));
			$sql = mysql_query("INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,item_amount,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$datePayment."','Payment','".date("Y-m-d")."','".$particulars."','".$itemname."',".$paidAmt.",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')");
			
			//end of inserting record into logbook
			echo '<script language="javascript">function show_message_pay_number(value1){
				alert("Payment No. = "+value1);
				window.location="paybill.php?action=new&pid="+value1;}
				show_message_pay_number('.$pid.');</script>';
//			header('Location:paymentitem.php?action=new&pid='.$pid);
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
    function validate_payment() {
        var err = "";
        if (document.getElementById("paymentDate").value != "") {
            if (!checkdate(document.payment.paymentDate)) {
                return false;
            }
        }
        if (document.getElementById("payType").value == 0)
            err = "* please select type of payment, it is mandatory field!\n";
        if (document.getElementById("party").value == 0)
            err += "* please select party, it is mandatory field!\n";
        if (document.getElementById("company").value == 0)
            err += "* please select company, it is mandatory field!\n";
        if (document.getElementById("paidAmount").value != "" && !IsNumeric(document.getElementById("paidAmount")
                .value))
            err += "* please input valid numeric data for paid amount!\n";
        if (err == "")
            return true;
        else {
            alert("Error: \n" + err);
            return false;
        }
    }

    function get_company_name(me) {
        var w = document.getElementById('company').selectedIndex;
        var selected_text = document.getElementById('company').options[w].text;
        document.getElementById('companyName').value = selected_text;
    }

    function validate_paymentlist() {
        if (checkdate(document.paymentlist.rangeFrom)) {
            if (checkdate(document.paymentlist.rangeTo)) {
                var no_of_days = getDaysbetween2Dates(document.paymentlist.rangeFrom, document.paymentlist.rangeTo);
                if (no_of_days < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else
                    return true;
            }
        }
    }

    function paging_pay() {
        if (document.getElementById("xson").value == "new") {
            window.location = "payment.php?action=" + document.getElementById("xson").value + "&pg=" + document
                .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sd=" +
                document.getElementById("sd").value + "&ed=" + document.getElementById("ed").value;
        } else {
            window.location = "payment.php?action=" + document.getElementById("xson").value + "&pid=" + document
                .getElementById("payid").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
                .getElementById("displayTotalRows").value + "&sd=" + document.getElementById("sd").value + "&ed=" +
                document.getElementById("ed").value;
        }
    }

    function firstpage_pay() {
        document.getElementById("page").value = 1;
        paging_pay();
    }

    function previouspage_pay() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_pay();
    }

    function nextpage_pay() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_pay();
    }

    function lastpage_pay() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_pay();
    }
    </script>
</head>


<body>
    <center>
        <table align="center" cellspacing="0" cellpadding="0" height="450px" width="875px" border="0">
            <tr>
                <td valign="top" colspan="3">
                    <form name="payment" method="post" onsubmit="return validate_payment()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Payment - [ Main ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Record" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Controls">
                                            <td class="th" nowrap>Payment No.:</td>
                                            <td><input name="paymentNo" id="paymentNo" maxlength="15" size="20"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo ($row['pay_id']>999 ? $row['pay_id'] : ($row['pay_id']>99 && $row['pay_id']<1000 ? "0".$row['pay_id'] : ($row['pay_id']>9 && $row['pay_id']<100 ? "00".$row['pay_id'] : "000".$row['pay_id'])));}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Payment Date:<span style="color:#FF0000">*</span></td>
                                            <td><input name="paymentDate" id="paymentDate" maxlength="10" size="10"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["pay_date"]));} else { echo date("d-m-Y");}?>">&nbsp;
                                                <script language="JavaScript">
                                                new tcal({
                                                    'formname': 'payment',
                                                    'controlname': 'paymentDate'
                                                });
                                                </script>
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Party Name:<span style="color:#FF0000">*</span></td>
                                            <td><select name="party" id="party" style="width:300px"
                                                    onchange="get_partydetail_on_payment(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			$sql_party=mysql_query("SELECT * FROM party ORDER BY party_name");
			while($row_party=mysql_fetch_array($sql_party))
			{
				if($row_party["party_id"]==$row["party_id"])
					echo '<option selected value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
				else
					echo '<option value="'.$row_party["party_id"].'">'.$row_party["party_name"].'</option>';
			}?>
                                                </select><input type="hidden" name="partyName" id="partyName"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["party_name"];}?>">
                                            </td>

                                            <td class="th" nowrap>Company Name:<span style="color:#FF0000">*</span></td>
                                            <td><select name="company" id="company" style="width:300px"
                                                    onchange="get_company_name(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			$sql_company=mysql_query("SELECT * FROM company ORDER BY company_name");
			while($row_company=mysql_fetch_array($sql_company))
			{
				if($row_company["company_id"]==$row["company_id"])
					echo '<option selected value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
				else
					echo '<option value="'.$row_company["company_id"].'">'.$row_company["company_name"].'</option>';
			}?>
                                                </select><input type="hidden" name="companyName" id="companyName"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["company_name"];}?>">
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-1:</td>
                                            <td><input name="address1" id="address1" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address1"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Payment Type:<span style="color:#FF0000">*</span></td>
                                            <td><select name="payType" id="payType" style="width:90px"
                                                    onchange="get_detail_on_paytype(this.value)">
                                                    <option value="0">-- Select --</option>
                                                    <?php 
			if($row["pay_type"]=="1")
				echo '<option selected value="1">Cash</option>';
			else
				echo '<option value="1">Cash</option>';
			if($row["pay_type"]=="2")
				echo '<option selected value="2">Cheque</option>';
			else
				echo '<option value="2">Cheque</option>';
			if($row["pay_type"]=="3")
				echo '<option selected value="3">Draft</option>';
			else
				echo '<option value="3">Draft</option>';
			if($row["pay_type"]=="4")
				echo '<option selected value="4">ePay</option>';
			else
				echo '<option value="4">ePay</option>';
			?>
                                                </select></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-2:</td>
                                            <td><input name="address2" id="address2" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address2"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Chq/DD/ePay No.:</td>
                                            <td><span id="chqnum"><input name="chequeNumber" id="chequeNumber"
                                                        maxlength="10" size="20" readonly="true"
                                                        value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["chq_no"];}?>"
                                                        style="background-color:#E7F0F8; color:#0000FF"></span></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Address-3:</td>
                                            <td><input name="address3" id="address3" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["address3"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Chq/DD/ePay Date:</td>
                                            <td><span id="chqdt"><input name="chequeDate" id="chequeDate" maxlength="10"
                                                        size="10" readonly="true"
                                                        value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){echo date("d-m-Y",strtotime($row["chq_date"]));} else { echo date("d-m-Y");}?>"
                                                        style="background-color:#E7F0F8; color:#0000FF"></span><span
                                                    id="calndr">&nbsp;<script language="JavaScript">
                                                    new tcal({
                                                        'formname': 'payment',
                                                        'controlname': 'chequeDate'
                                                    });
                                                    </script></span></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>City:</td>
                                            <td><input name="cityName" id="cityName" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["city_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Bank Name:</td>
                                            <td><span id="bnkname"><select name="bankName" id="bankName"
                                                        style="width:300px">
                                                        <option value="0">-- Select --</option>
                                                        <?php 
			$sql_bank=mysql_query("SELECT * FROM bank ORDER BY bank_name");
			while($row_bank=mysql_fetch_array($sql_bank))
			{
				if($row_bank["bank_id"]==$row["bank_id"])
					echo '<option selected value="'.$row_bank["bank_id"].'">'.$row_bank["bank_name"].'</option>';
				else
					echo '<option value="'.$row_bank["bank_id"].'">'.$row_bank["bank_name"].'</option>';
			}?>
                                                    </select></span></td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>State:</td>
                                            <td><input name="stateName" id="stateName" maxlength="50" size="45"
                                                    readonly="true"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["state_name"];}?>"
                                                    style="background-color:#E7F0F8; color:#0000FF"></td>

                                            <td class="th" nowrap>Pay Amount:<span style="color:#FF0000">*</span></td>
                                            <td><input name="paidAmount" id="paidAmount" maxlength="10" size="20"
                                                    value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["pay_amt"];}?>">
                                            </td>
                                        </tr>

                                        <tr class="Controls">
                                            <td class="th" nowrap>Remarks:</td>
                                            <td rowspan="3"><textarea name="remarks" id="remarks" cols="35"
                                                    rows="4"><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["remark"];}?></textarea>
                                            </td>

                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="Controls">
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <?php if($msg!=""){
		echo '<tr class="Controls">
			<td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td>
		</tr>';
		} ?>

                                        <tr class="Bottom">
                                            <td align="left" colspan="4">
                                                <?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
                    
			if($row_user['pay1']==1){?>
                                                <input type="image" name="submit" src="images/add.gif" width="72"
                                                    height="22" alt="new"><input type="hidden" name="submit"
                                                    value="new" />
                                                <?php } elseif($row_user['pay1']==0){?>
                                                <input type="image" name="submit" src="images/add.gif"
                                                    style="visibility:hidden" width="72" height="22" alt="new">
                                                <?php }?>
                                                &nbsp;&nbsp;<a href="javascript:document.payment.reset()"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
                                                <input type="image" name="submit" src="images/update.gif" width="72"
                                                    height="22" alt="update"><input type="hidden" name="submit"
                                                    value="update" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='paybill.php?action=new&pid=<?php echo $pid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='payment.php?action=new'"><img
                                                        src="images/reset.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;" border="0" /></a>
                                                <?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
                                                <input type="image" name="submit" src="images/delete.gif" width="72"
                                                    height="22" alt="delete"><input type="hidden" name="submit"
                                                    value="delete" />&nbsp;&nbsp;<a
                                                    href="javascript:window.location='paybill.php?action=new&pid=<?php echo $pid;?>'"><img
                                                        src="images/next.gif" width="72" height="22"
                                                        style="display:inline; cursor:hand;"
                                                        border="0" /></a>&nbsp;&nbsp;<a
                                                    href="javascript:window.location='payment.php?action=new'"><img
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
                    <form name="paymentlist" method="post" onsubmit="return validate_paymentlist()">
                        <table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td valign="top">
                                    <table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
                                            <td class="th"><strong>Payment - [ List ]</strong></td>
                                            <td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
                                        </tr>
                                    </table>

                                    <table class="Grid" width="100%" cellspacing="0" cellpadding="0">
                                        <tr class="Caption">
                                            <th align="right" colspan="9">List Range From:&nbsp;&nbsp;<input
                                                    name="rangeFrom" id="rangeFrom" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$sd);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'paymentlist',
                                                    'controlname': 'rangeFrom'
                                                });
                                                </script>&nbsp;&nbsp;Range To:&nbsp;&nbsp;<input name="rangeTo"
                                                    id="rangeTo" maxlength="10" size="10"
                                                    value="<?php echo date("d-m-Y",$ed);?>">&nbsp;<script
                                                    language="JavaScript">
                                                new tcal({
                                                    'formname': 'paymentlist',
                                                    'controlname': 'rangeTo'
                                                });
                                                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image"
                                                    name="show" src="images/search.gif" width="82" height="22"
                                                    alt="show"><input type="hidden" name="sd" id="sd"
                                                    value="<?php echo $sd;?>" /><input type="hidden" name="ed" id="ed"
                                                    value="<?php echo $ed;?>" /></th>
                                        </tr>
                                        <tr class="Caption">
                                            <th width="5%">Sl.No.</th>
                                            <th width="5%">Pay No.</th>
                                            <th width="10%">Pay Date</th>
                                            <th width="10%">Pay Type</th>
                                            <th width="25%">Party Name</th>
                                            <th width="10%">Pay Amount</th>
                                            <th width="25%">Company Name</th>
                                            <th width="5%">Edit</th>
                                            <th width="5%">Del</th>
                                        </tr>

                                        <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql_pmnt = mysql_query("SELECT tblpayment1.*, party_name, company_name FROM tblpayment1 INNER JOIN party ON tblpayment1.party_id = party.party_id INNER JOIN company ON tblpayment1.company_id = company.company_id WHERE pay_date>='".$fromDate."' AND pay_date<='".$toDate."' ORDER BY pay_date, pay_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_pmnt=mysql_fetch_array($sql_pmnt))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "payment.php?action=delete&pid=".$row_pmnt['pay_id'];
			$edit_ref = "payment.php?action=edit&pid=".$row_pmnt['pay_id'];
			
			$payment_number = ($row_pmnt['pay_id']>999 ? $row_pmnt['pay_id'] : ($row_pmnt['pay_id']>99 && $row_pmnt['pay_id']<1000 ? "0".$row_pmnt['pay_id'] : ($row_pmnt['pay_id']>9 && $row_pmnt['pay_id']<100 ? "00".$row_pmnt['pay_id'] : "000".$row_pmnt['pay_id'])));
			if($row_pmnt["pay_type"]=="1")
				$paytype = "Cash";
			elseif($row_pmnt["pay_type"]=="2")
				$paytype = "Cheque";
			elseif($row_pmnt["pay_type"]=="3")
				$paytype = "Draft";
			elseif($row_pmnt["pay_type"]=="4")
				$paytype = "ePay";
			
			echo '<td align="center">'.$i.'.</td><td>'.$payment_number.'</td><td align="center">'.date("d-m-Y",strtotime($row_pmnt['pay_date'])).'</td><td>'.$paytype.'</td><td>'.$row_pmnt['party_name'].'</td><td>'.$row_pmnt['pay_amt'].'</td><td>'.$row_pmnt['company_name'].'</td>';
			if($row_user['pay2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pay2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['pay3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['pay3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>

                                        <tr class="Footer">
                                            <td colspan="9" align="center">
                                                <?php 
			$sql_total = mysql_query("SELECT * FROM tblpayment1 WHERE pay_date>='".$fromDate."' AND pay_date<='".$toDate."'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_pay()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="payid" id="payid" value="'.$pid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pay()" style="vertical-align:middle">';
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
				echo '<input type="button" name="firstPage" id="firstPage" value=" << " onclick="firstpage_pay()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pay()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pay()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pay()" />';
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