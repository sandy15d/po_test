<?php 
include("menu.php");
/*--------------------*/
$pid = 0;
$listFor = "U";
if(isset($_REQUEST['pid'])){$pid = $_REQUEST['pid'];}
if(isset($_REQUEST['rf'])){$listFor = $_REQUEST['rf'];}
/*--------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$pid = $_POST['product'];
	$listFor = $_POST['listFor'];
} elseif(isset($_REQUEST['sm'])){
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
} else {
	$sm = strtotime(date("Y-m-d"));
	$em = strtotime(date("Y-m-d"));
}
/*--------------------*/
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
        if (checkdate(document.polist.dateFrom)) {
            if (checkdate(document.polist.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.polist.dateFrom, document.polist.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.polist.startYear, document.polist.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.polist.dateTo, document.polist.endYear);
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

    function paging_pilist() {
        window.location = "polistitem.php?pid=" + document.getElementById("product").value + "&rf=" + document
            .getElementById("listFor").value + "&pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value + "&sm=" + document.getElementById("date1").value + "&em=" +
            document.getElementById("date2").value;
    }

    function firstpage_pilist() {
        document.getElementById("page").value = 1;
        paging_pilist();
    }

    function previouspage_pilist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_pilist();
    }

    function nextpage_pilist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_pilist();
    }

    function lastpage_pilist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_pilist();
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
    <form name="polist" id="polist" method="post" action="polistitem.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1375px">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Item wise Order List</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td>Select Product: <select name="product" id="product" style="width:200px">
                            <option value="0">All Items</option>
                            <?php 
		$sql_item=mysql_query("SELECT * FROM item ORDER BY item_name");
		while($row_item=mysql_fetch_array($sql_item)){
			if($row_item["item_id"]==$pid)
				echo '<option selected value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
			else
				echo '<option value="'.$row_item["item_id"].'">'.$row_item["item_name"].'</option>';
		}?>
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        List For:&nbsp;&nbsp;<select name="listFor" id="listFor" style="width:110px">
                            <?php 
		if($listFor=="U"){
			echo '<option selected value="U">Unsent PO</option><option value="S">Sent PO</option>';
		} elseif($listFor=="S"){
			echo '<option value="U">Unsent PO</option><option selected value="S">Sent PO</option>';
		}?>
                        </select>
                    </td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "polist",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "polist",
                            "controlname": "dateTo"
                        });
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show"
                            src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show"
                            value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22"
                            style="display:inline;cursor:hand;" border="0" onclick="window.location='menu.php'" /><input
                            type="image" src="images/print.gif" onclick="funPrint()" /><input type="hidden"
                            name="startYear" id="startYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                            type="hidden" name="endYear" id="endYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" />

                        <input type="button" onclick="funExport()" value="Export" style="width:60px;cursor:pointer;" />
                        <script type="text/javascript">
                        function funExport() {
                            var df = document.getElementById("dateFrom").value;
                            var dt = document.getElementById("dateTo").value;
                            var prd = document.getElementById("product").value;
                            var lf = document.getElementById("listFor").value;
                            window.open("exportpo.php?df=" + df + "&dt=" + dt + "&prd=" + prd + "&lf=" + lf, "ExForm",
                                "menubar=no,scrollbars=yes,resizable=no,directories=no,width=50,height=50");
                        }
                        </script>

                    </td>

                </tr>
                <tr>
                    <td>
                        <div id="print_area">
                            <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0"
                                width="100%" id="printTable">
                                <tbody>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:20px;">
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Sl.No.</td>
                                        <td width="7%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">PO No.</td>
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Date</td>
                                        <td width="13%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Party Name
                                        </td>
                                        <td width="13%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">
                                            Order-in-Company
                                        </td>
                                        <td width="13%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Location</td>
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Delivery Date
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;"
                                            align="right">Ordered&nbsp;</td>
                                        <td width="7%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" align="left">
                                            Qnty.
                                        </td>
                                        <td width="7%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Item Value
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Against Indent
                                        </td>
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;">
                                            Indent Date</td>
                                    </tr>
                                    <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	$itemNo = 0;
	
	$sql = "SELECT tblpo.*, tblpo_item.*, party_name, company_name, location_name, item_name, unit_name, indent_no, ind_prefix, indent_date FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE po_status='".$listFor."' AND (po_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($pid!=0){
		$sql .= " AND tblpo_item.item_id=".$pid;
	}
	$sql .= " ORDER BY item_name, po_date, tblpo_item.po_id LIMIT ".$start.",".$end;
	$res = mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($res)){
		$ctr++;
		$poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
		$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
		if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
		$itemvalue = number_format($row['qnty'] * $row['rate'],2,'.','');
		if($row['item_id']!=$itemNo){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">Item:</td>';
			echo '<td style="border-left:none; border-bottom:none" width="95%" colspan="11">'.$row['item_name'].'</td>';
			echo '</tr>';
			$itemNo=$row['item_id'];
			$ctr = 1;
		}
		echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="7%">'.$poNo.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.date("d-m-y",strtotime($row['po_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="13%">'.$row['party_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="13%">'.$row['company_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="13%">'.$row['location_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.date("d-m-y",strtotime($row['delivery_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="7%">&nbsp;'.($row['qnty']==0?"&nbsp;":$row['unit_name']).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="7%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.$indent_number.'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="5%">'.date("d-m-y",strtotime($row['indent_date'])).'</td>';
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
	$sql_total = "SELECT * FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id WHERE po_status='".$listFor."' AND (po_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($pid!=0){
		$sql_total .= " AND item_id=".$pid;
	}
	$res_total = mysql_query($sql_total) or die(mysql_error());
	$tot_row=mysql_num_rows($res_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pilist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_pilist()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pilist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pilist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pilist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pilist()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>