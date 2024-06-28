<?php 
include("menu.php");
/*--------------------------------*/
$pid = 0;
if(isset($_REQUEST['pid'])){$pid = $_REQUEST['pid'];}
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$pid = $_POST['product'];
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
        if (checkdate(document.rrlist.dateFrom)) {
            if (checkdate(document.rrlist.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.rrlist.dateFrom, document.rrlist.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.rrlist.startYear, document.rrlist.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.rrlist.dateTo, document.rrlist.endYear);
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

    function paging_rrlist() {
        window.location = "rrlistitem.php?pid=" + document.getElementById("product").value + "&pg=" + document
            .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sm=" +
            document.getElementById("date1").value + "&em=" + document.getElementById("date2").value;
    }

    function firstpage_rrlist() {
        document.getElementById("page").value = 1;
        paging_rrlist();
    }

    function previouspage_rrlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_rrlist();
    }

    function nextpage_rrlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_rrlist();
    }

    function lastpage_rrlist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_rrlist();
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
    <form name="rrlist" id="rrlist" method="post" action="rrlistitem.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1150px">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Item wise Receipt Return List</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td>Select Product: <select name="product" id="product" style="width:200px">
                            <option value="0">All Products</option>
                            <?php 
		$sql_item=mysql_query("SELECT * FROM Item ORDER BY item_name");
		while($row_item=mysql_fetch_array($sql_item)){
			if($row_item["item_id"]==$pid)
				echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			else
				echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
		} ?>
                        </select>
                    </td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "rrlist",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "rrlist",
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
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Sl. No.</td>
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">RR No.</td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">RR Date</td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Party Name
                                        </td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Received At
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">MR No.</td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">MR Date</td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" colspan="2">
                                            Received Qnty.</td>
                                        <td width="15%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" colspan="2">
                                            Returned Qnty.</td>
                                    </tr>
                                    <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	$itemNo = 0;
	
	$sql = "SELECT tblreceipt_return2.*, tblreceipt_return1.*, receipt_no, receipt_prefix, receipt_date, party_name, location_name, item_name, unit_name FROM tblreceipt_return2 INNER JOIN tblreceipt_return1 ON tblreceipt_return2.return_id = tblreceipt_return1.return_id INNER JOIN tblreceipt1 ON tblreceipt_return1.receipt_id = tblreceipt1.receipt_id INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN location ON tblreceipt1.recd_at = location.location_id INNER JOIN item ON tblreceipt_return2.item_id = item.item_id INNER JOIN unit ON tblreceipt_return2.unit_id = unit.unit_id WHERE (return_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($pid!=0){
		$sql .= " AND tblreceipt_return2.item_id=".$pid;
	}
	$sql .= " ORDER BY item_name, return_date, tblreceipt_return2.return_id LIMIT ".$start.",".$end;
	$res = mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($res)){
		$ctr += 1;
		$rrNo = ($row['return_no']>999 ? $row['return_no'] : ($row['return_no']>99 && $row['return_no']<1000 ? "0".$row['return_no'] : ($row['return_no']>9 && $row['return_no']<100 ? "00".$row['return_no'] : "000".$row['return_no'])));
		$mrNo = ($row['receipt_no']>999 ? $row['receipt_no'] : ($row['receipt_no']>99 && $row['receipt_no']<1000 ? "0".$row['receipt_no'] : ($row['receipt_no']>9 && $row['receipt_no']<100 ? "00".$row['receipt_no'] : "000".$row['receipt_no'])));
		if($row['receipt_prefix']!=null){$mrNo = $row['receipt_prefix']."/".$mrNo;}
		
		$sql_recd = mysql_query("SELECT tblreceipt2.*, unit_name FROM tblreceipt2 INNER JOIN unit ON tblreceipt2.unit_id = unit.unit_id WHERE receipt_id=".$row['receipt_id']." AND item_id=".$row['item_id']) or die(mysql_error());
		$row_recd = mysql_fetch_assoc($sql_recd);
		if($row['item_id']!=$itemNo){
			$itemNo = $row['item_id'];
			echo '<tr bgcolor="#C2FBFE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none; font-weight:bold" width="5%">Item: </td>';
			echo '<td style="border-left:none; border-bottom:none; font-weight:bold" colspan="10">'.$row['item_name'].'</td>';
			echo '</tr>';
		}
		echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$rrNo.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['return_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['party_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['location_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.$mrNo.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d/m/Y",strtotime($row['receipt_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row_recd['receipt_qnty']==0?"&nbsp;":$row_recd['receipt_qnty']).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%" align="left">&nbsp;'.($row_recd['receipt_qnty']==0?"&nbsp;":$row_recd['unit_name']).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['return_qnty']==0?"&nbsp;":$row['return_qnty']).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%" align="left">&nbsp;'.($row['return_qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
		echo '</tr>';
	}?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php 
	$sql = "SELECT * FROM tblreceipt_return2 INNER JOIN tblreceipt_return1 ON tblreceipt_return2.return_id = tblreceipt_return1.return_id WHERE (return_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($pid!=0){
		$sql .= " AND item_id=".$pid;
	}
	$sql_total = mysql_query($sql) or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_rrlist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_rrlist()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_rrlist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_rrlist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_rrlist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_rrlist()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>