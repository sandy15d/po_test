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
        if (checkdate(document.prlist.dateFrom)) {
            if (checkdate(document.prlist.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.prlist.dateFrom, document.prlist.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.prlist.startYear, document.prlist.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.prlist.dateTo, document.prlist.endYear);
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

    function paging_prlist() {
        window.location = "prlist.php?pg=" + document.getElementById("page").value + "&tr=" + document.getElementById(
                "displayTotalRows").value + "&sm=" + document.getElementById("date1").value + "&em=" + document
            .getElementById("date2").value;
    }

    function firstpage_prlist() {
        document.getElementById("page").value = 1;
        paging_prlist();
    }

    function previouspage_prlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_prlist();
    }

    function nextpage_prlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_prlist();
    }

    function lastpage_prlist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_prlist();
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
    <form name="prlist" id="prlist" method="post" action="prlist.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="875px">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Purchase Return List</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "prlist",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "prlist",
                            "controlname": "dateTo"
                        });
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show"
                            src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show"
                            value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22"
                            style="display:inline;cursor:hand;" border="0" onclick="window.location='menu.php'" /><input
                            type="image" src="images/print.gif" onclick="funPrint()"><input type="hidden"
                            name="startYear" id="startYear"
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
                                        <td width="5%" rowspan="2" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Bill
                                            No.
                                        </td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Date
                                        </td>
                                        <td width="15%" valign="middle" style="border-top:none; border-left:none"
                                            colspan="2">Party Name</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Bill
                                            Amount</td>
                                        <td width="20%" valign="middle"
                                            style="border-top:none; border-left:none; border-right:none;" colspan="2">
                                            Bill
                                            to Company</td>
                                    </tr>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="20%" colspan="2" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;"
                                            align="right">Item&nbsp;</td>
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" align="left">
                                            Qnty.
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Item Value
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Against MR No.
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;">
                                            Against PO</td>
                                    </tr>
                                    <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	
	$sql = mysql_query("SELECT tblbill.*, party_name, company_name FROM tblbill INNER JOIN party ON tblbill.party_id = party.party_id INNER JOIN company ON tblbill.company_id = company.company_id  WHERE bill_return=1 AND (bill_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') ORDER BY bill_date, bill_id LIMIT ".$start.",".$end) or die(mysql_error());
	while($row=mysql_fetch_array($sql)){
		$ctr += 1;
		echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.$row['bill_no'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['bill_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%" colspan="2">'.$row['party_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($row['bill_amt']==""?"&nbsp;":$row['bill_amt']).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="20%" colspan="2">'.$row['company_name'].'</td>';
		echo '</tr>';
		
		$sql_item = mysql_query("SELECT tblbill_item.*, po_no, receipt_no, receipt_prefix, item_name, unit_name,ic.category FROM tblbill_item INNER JOIN tblpo ON tblbill_item.po_id = tblpo.po_id INNER JOIN tblreceipt1 ON tblbill_item.receipt_id = tblreceipt1.receipt_id INNER JOIN item ON tblbill_item.item_id = item.item_id INNER JOIN unit ON tblbill_item.unit_id = unit.unit_id INNER JOIN item_category ic ON ic.category_id = tblbill_item.item_category WHERE bill_id=".$row['bill_id']." ORDER BY seq_no") or die(mysql_error());
		while($row_item=mysql_fetch_array($sql_item)){
			$itemvalue = number_format($row_item['bill_qnty'] * $row_item['rate'],2,'.','');
			$poNo = ($row_item['po_no']>999 ? $row_item['po_no'] : ($row_item['po_no']>99 && $row_item['po_no']<1000 ? "0".$row_item['po_no'] : ($row_item['po_no']>9 && $row_item['po_no']<100 ? "00".$row_item['po_no'] : "000".$row_item['po_no'])));
			$mrNo = ($row_item['receipt_no']>999 ? $row_item['receipt_no'] : ($row_item['receipt_no']>99 && $row_item['receipt_no']<1000 ? "0".$row_item['receipt_no'] : ($row_item['receipt_no']>9 && $row_item['receipt_no']<100 ? "00".$row_item['receipt_no'] : "000".$row_item['receipt_no'])));
			if($row_item['receipt_prefix']!=null){$mrNo = $row_item['receipt_prefix']."/".$mrNo;}
			
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row_item['item_name'].' ~~'.$row_item['category'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row_item['bill_qnty']==0?"&nbsp;":$row_item['bill_qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;'.($row_item['bill_qnty']==0?"&nbsp;":$row_item['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.$mrNo.'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="10%" align="center">'.$poNo.'</td>';
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
	$sql_total = mysql_query("SELECT * FROM tblbill WHERE bill_return=1 AND (bill_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')") or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_prlist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_prlist()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_prlist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_prlist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_prlist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_prlist()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>