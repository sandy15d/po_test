        <?php 
include("menu.php");
/*--------------------*/
$lid = 0;
$listFor = "U";
if(isset($_REQUEST['lid'])){$lid = $_REQUEST['lid'];}
if(isset($_REQUEST['rf'])){$listFor = $_REQUEST['rf'];}
/*--------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$lid = $_POST['location'];
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
                window.location = "polist.php?lid=" + document.getElementById("location").value + "&rf=" + document
                    .getElementById("listFor").value + "&pg=" + document.getElementById("page").value + "&tr=" +
                    document.getElementById("displayTotalRows").value + "&sm=" + document.getElementById("date1")
                    .value + "&em=" + document.getElementById("date2").value;
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
            <form name="polist" id="polist" method="post" action="polist.php"
                onsubmit="return validate_dateselection()">
                <table align="center" border="0" cellpadding="2" cellspacing="1" width="1075px">
                    <tbody>
                        <tr align="center"
                            style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                            <td>Purchase Order List</td>
                        </tr>
                        <tr align="center"
                            style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                            <td><?php if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
		echo 'Select Location: ';
		echo '<select name="location" id="location" style="width:200px" >';
		echo '<option value="0">All Locations</option>';
		$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
		while($row_location=mysql_fetch_array($sql_location)){
			if($row_location["location_id"]==$lid)
				echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
			else
				echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
		}
		echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo 'List For:&nbsp;&nbsp;<select name="listFor" id="listFor" style="width:110px">';
		if($listFor=="U"){
			echo '<option selected value="U">Unsent PO</option><option value="S">Sent PO</option>';
		} elseif($listFor=="S"){
			echo '<option value="U">Unsent PO</option><option selected value="S">Sent PO</option>';
		}
		echo '</select>';
	} elseif($_SESSION['stores_utype']=="U"){
		$lid = $_SESSION['stores_locid'];
		echo 'Location: ';
		echo '<input name="locationName" id="locationName" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo 'List For:&nbsp;&nbsp;<input name="listType" id="listType" readonly="true" value="Unsent Indent" style="background-color:#E7F0F8; color:#0000FF" /><input type="hidden" name="listFor" id="listFor" value="U" />';
	}?>
                            </td>
                        </tr>
                        <tr align="center"
                            style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                            <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input
                                    name="dateFrom" id="dateFrom" maxlength="10" size="10"
                                    value="<?php echo date("d-m-Y",$sm); ?>" style="vertical-align:top;">&nbsp;<script
                                    language="JavaScript">
                                new tcal({
                                    "formname": "polist",
                                    "controlname": "dateFrom"
                                });
                                </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                                    name="dateTo" id="dateTo" maxlength="10" size="10"
                                    value="<?php echo date("d-m-Y",$em); ?>" style="vertical-align:top;">&nbsp;<script
                                    language="JavaScript">
                                new tcal({
                                    "formname": "polist",
                                    "controlname": "dateTo"
                                });
                                </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show"
                                    src="images/show.gif" width="72" height="22" alt="show"><input type="hidden"
                                    name="show" value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72"
                                    height="22" style="display:inline;cursor:hand;" border="0"
                                    onclick="window.location='menu.php'" /><input type="image" src="images/print.gif"
                                    onclick="funPrint()"><input type="hidden" name="startYear" id="startYear"
                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                                    type="hidden" name="endYear" id="endYear"
                                    value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /></td>
                        </tr>
                        <tr>
                            <td>
                                <div id="print_area">
                                    <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2"
                                        cellspacing="0" width="100%" id="printTable">
                                        <tbody>
                                            <tr bgcolor="#E6E1B0" align="center"
                                                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:20px;">
                                                <td width="5%" rowspan="2" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none">Sl.
                                                    No.
                                                </td>
                                                <td width="15%" valign="middle"
                                                    style="border-top:none; border-left:none">PO
                                                    No.</td>
                                                <td width="10%" valign="middle"
                                                    style="border-top:none; border-left:none">
                                                    Date</td>
                                                <td width="25%" colspan="2" valign="middle"
                                                    style="border-top:none; border-left:none">Party Name</td>
                                                <td width="10%" valign="middle"
                                                    style="border-top:none; border-left:none">PO
                                                    Value</td>
                                                <td width="15%" valign="middle"
                                                    style="border-top:none; border-left:none">
                                                    Expected Delivery</td>
                                                <td width="20%" valign="middle"
                                                    style="border-top:none; border-left:none; border-right:none;">
                                                    Order-in-Company</td>
                                            </tr>
                                            <tr bgcolor="#E6E1B0" align="center"
                                                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:20px;">
                                                <td width="25%" colspan="2" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none">Item
                                                    Name
                                                </td>
                                                <td width="15%" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none; border-right:none;"
                                                    align="right">Ordered&nbsp;</td>
                                                <td width="10%" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none"
                                                    align="left">Qnty.</td>
                                                <td width="10%" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none">Item
                                                    Value
                                                </td>
                                                <td width="15%" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none">
                                                    Against
                                                    Indent</td>
                                                <td width="20%" valign="middle"
                                                    style="border-top:none; border-left:none; border-bottom:none; border-right:none;">
                                                    Indent Date</td>
                                            </tr>
                                            <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	$locationNo = 0;
	
	$sql = "SELECT tblpo.*, tblpo_dtm.total_amount, party_name, company_name, location_name FROM tblpo INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN location ON tblpo.delivery_at = location.location_id LEFT OUTER JOIN tblpo_dtm ON tblpo.po_id = tblpo_dtm.po_id WHERE po_status='".$listFor."' AND (po_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql .= " AND delivery_at=".$lid;
	}
	$sql .= " ORDER BY location_name, po_date, po_id LIMIT ".$start.",".$end;
	$res = mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($res)){
		$ctr++;
		$poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
		if($row['delivery_at']!=$locationNo){
			echo '<tr bgcolor="#C2FBFE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">Place:</td>';
			echo '<td style="border-left:none; border-bottom:none" width="95%" colspan="7">'.$row['location_name'].'</td>';
			echo '</tr>';
			$locationNo=$row['delivery_at'];
		}
		echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%">'.$poNo.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['po_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="25%" colspan="2">'.$row['party_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.(($row['total_amount']==NULL || $row['total_amount']==0)?"&nbsp;":$row['total_amount']).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%">'.date("d-m-Y",strtotime($row['delivery_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="20%">'.$row['company_name'].'</td>';
		echo '</tr>';
		
		$sqlItem = mysql_query("SELECT tblpo_item.*, indent_no, ind_prefix, indent_date, item_name, unit_name,ic.category FROM tblpo_item INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN item_category ic ON ic.category_id = tblpo_item.item_category INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE po_id=".$row['po_id']." ORDER BY seq_no") or die(mysql_error());
		while($rowItem=mysql_fetch_array($sqlItem)){
			$indent_number = ($rowItem['indent_no']>999 ? $rowItem['indent_no'] : ($rowItem['indent_no']>99 && $rowItem['indent_no']<1000 ? "0".$rowItem['indent_no'] : ($rowItem['indent_no']>9 && $rowItem['indent_no']<100 ? "00".$rowItem['indent_no'] : "000".$rowItem['indent_no'])));
			if($rowItem['ind_prefix']!=null){$indent_number = $rowItem['ind_prefix']."/".$indent_number;}
			$itemvalue = number_format($rowItem['qnty'] * $rowItem['rate'],2,'.','');
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="25%" colspan="2">'.$rowItem['seq_no']."&nbsp;&nbsp;".$rowItem['item_name'].' ~~'.$rowItem['category'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="15%" align="right">'.($rowItem['qnty']==0?"&nbsp;":$rowItem['qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">&nbsp;'.($rowItem['qnty']==0?"&nbsp;":$rowItem['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.($itemvalue==0?"&nbsp;":$itemvalue).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%" align="center">'.$indent_number.'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="20%" align="center">'.date("d-m-Y",strtotime($rowItem['indent_date'])).'</td>';
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
	$sql_total = "SELECT * FROM tblpo WHERE po_status='".$listFor."' AND (po_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql_total .= " AND delivery_at=".$lid;
	}
	$res_total = mysql_query($sql_total) or die(mysql_error());
	$tot_row=mysql_num_rows($res_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pilist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="3" size="3"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
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