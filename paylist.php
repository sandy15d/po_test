<?php 
include("menu.php");
/*--------------------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
} elseif(isset($_REQUEST['sm'])){
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
} else {
	$sm = strtotime(date("Y-m-d"));
	$em = strtotime(date("Y-m-d"));
}
/*--------------------------------*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/calendar_eu.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script language="javascript" type="text/javascript">
    function validate_dateselection() {
        if (checkdate(document.paylist.dateFrom)) {
            if (checkdate(document.paylist.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.paylist.dateFrom, document.paylist.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.paylist.startYear, document.paylist.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.paylist.dateTo, document.paylist.endYear);
                        if (no_of_days3 < 0) {
                            alert("* Report To date wrongly selected. Please correct and submit again.\n");
                            return false;
                        } else {
                            return true;
                        }
                    }
                }
            }
        }
    }

    function paging_paylist() {
        window.location = "paylist.php?pg=" + document.getElementById("page").value + "&tr=" + document.getElementById(
                "displayTotalRows").value + "&sm=" + document.getElementById("date1").value + "&em=" + document
            .getElementById("date2").value;
    }

    function firstpage_paylist() {
        document.getElementById("page").value = 1;
        paging_paylist();
    }

    function previouspage_paylist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_paylist();
    }

    function nextpage_paylist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_paylist();
    }

    function lastpage_paylist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_paylist();
    }

    function funPrint() {
        var divContents = document.getElementById("print_area").innerHTML;
        var a = window.open('', '', 'height=500, width=500');
        a.document.write('<html>');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        a.print();
    }
    </script>
</head>

<body>
    <?php echo date("d-m-Y, h:i:s");?>
    <form name="paylist" id="paylist" method="post" action="paylist.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1125px">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Payment List</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "paylist",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "paylist",
                            "controlname": "dateTo"
                        });
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show"
                            src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show"
                            value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22"
                            style="display:inline;cursor:hand;" border="0"
                            onclick="window.location='menu.php'" />&nbsp;&nbsp;<input type="image"
                            src="images/print.gif" onclick="funPrint()" /><input type="hidden" name="startYear"
                            id="startYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                            type="hidden" name="endYear" id="endYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="print_area">
                            <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" id="printTable"
                                cellspacing="0" width="100%">
                                <tbody>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" rowspan="2">
                                            Sl. No.</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Pay
                                            Date</td>
                                        <td width="15%" valign="middle" style="border-top:none; border-left:none">Party
                                            Name</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Pay
                                            type</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Pay
                                            Amount</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">
                                            Chq/DD/ePay No.</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">
                                            Chq/DD/ePay Date</td>
                                        <td width="15%" valign="middle" style="border-top:none; border-left:none">Bank
                                            Name</td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-right:none;">Company Name
                                        </td>
                                    </tr>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Pay Mode</td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Bill No.</td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Bill Date</td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Bill Amount
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Paid Amount
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Deduction</td>
                                        <td width="30%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;"
                                            colspan="2">&nbsp;</td>
                                    </tr>
                                    <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr_found = "false";
	$ctr = 0;
	$payNo=0;
	
	$sql_pay = mysql_query("SELECT * FROM tblpayment1 WHERE (pay_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') ORDER BY pay_date, pay_id") or die(mysql_error());
	$sql = mysql_query("SELECT tblpayment1.*, tblpayment2.*, party_name, company_name FROM tblpayment1 INNER JOIN tblpayment2 ON tblpayment1.pay_id = tblpayment2.pay_id INNER JOIN party ON tblpayment1.party_id = party.party_id INNER JOIN company ON tblpayment1.company_id = company.company_id WHERE (pay_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') ORDER BY pay_date, tblpayment1.pay_id, bill_id LIMIT ".$start.",".$end) or die(mysql_error());
	while($row=mysql_fetch_array($sql)){
		if($row["pay_type"]=="1")
			$paytype = "Cash";
		elseif($row["pay_type"]=="2")
			$paytype = "Cheque";
		elseif($row["pay_type"]=="3")
			$paytype = "Draft";
		elseif($row["pay_type"]=="4")
			$paytype = "ePay";
		
		if($row["pay_mode"]=="1")
			$paymode = "On Account";
		elseif($row["pay_mode"]=="2")
			$paymode = "Against Bill";
		elseif($row["pay_mode"]=="3")
			$paymode = "Advance";
		
		$bankname = "";
		if($row['bank_id']>0){
			$sql_bank = mysql_query("SELECT * FROM bank WHERE bank_id=".$row['bank_id']) or die(mysql_error());
			$row_bank=mysql_fetch_assoc($sql_bank);
			$bankname = $row_bank['bank_name'];
		}
		
		$billnumber = "";
		$billdate = "";
		$billamount = 0;
		if($row['bill_id']>0){
			$sql_bill = mysql_query("SELECT * FROM tblbill WHERE bill_id=".$row['bill_id']) or die(mysql_error());
			$row_bill=mysql_fetch_assoc($sql_bill);
			$billnumber = $row_bill['bill_no'];
			$billdate = $row_bill['bill_date'];
			$billamount = $row_bill['bill_amt'];
		}
		
		if($row['pay_id']==$payNo){
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$paymode.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.($billnumber==""?"&nbsp;":$billnumber).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.($billdate==""?"&nbsp;":date("d/m/Y",strtotime($billdate))).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($billamount==0?"&nbsp;":$billamount).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['paid_amt']==""?"&nbsp;":$row['paid_amt']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['deduct']==""?"&nbsp;":$row['deduct']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="30%" colspan="2">&nbsp;</td>';
			echo '</tr>';
		} elseif($row['pay_id']!=$payNo){
			$payNo = $row['pay_id'];
//			$payNo = ($row['pay_id']>999 ? $row['pay_id'] : ($row['pay_id']>99 && $row['pay_id']<1000 ? "0".$row['pay_id'] : ($row['pay_id']>9 && $row['pay_id']<100 ? "00".$row['pay_id'] : "000".$row['pay_id'])));
			$ctr += 1;
			if($ctr_found=="false"){
				while($row_pay=mysql_fetch_array($sql_pay)){
					if($row_pay['pay_id']==$row['pay_id']){
						$ctr_found="true";
						break;
					}
					$ctr += 1;
				}
			}
			
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
//			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$payNo.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['pay_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['party_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$paytype.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['pay_amt']==""?"&nbsp;":$row['pay_amt']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.($row['chq_no']==""?"&nbsp;":$row['chq_no']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.($row['chq_date']==""?"&nbsp;":date("d/m/Y",strtotime($row['chq_date']))).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.($bankname==""?"&nbsp;":$bankname).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="15%">'.$row['company_name'].'</td>';
			echo '</tr>';
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$paymode.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%">'.($billnumber==""?"&nbsp;":$billnumber).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.($billdate==""?"&nbsp;":date("d/m/Y",strtotime($billdate))).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($billamount==0?"&nbsp;":$billamount).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['paid_amt']==""?"&nbsp;":$row['paid_amt']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['deduct']==""?"&nbsp;":$row['deduct']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="30%" colspan="2">&nbsp;</td>';
			echo '</tr>';
		}
	} ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php 
	$sql_total = mysql_query("SELECT * FROM tblpayment1 WHERE (pay_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')") or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_paylist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_paylist()" style="vertical-align:middle">';
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
	
	echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="date1" id="date1" value="'.$sm.'" /><input type="hidden" name="date2" id="date2" value="'.$em.'" />';
	if($total_page>1 && $pg>1)
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_paylist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_paylist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_paylist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_paylist()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>