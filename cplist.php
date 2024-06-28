<?php 
include("menu.php");
/*--------------------------------*/
$lid = 0;
if(isset($_REQUEST['lid'])){$lid = $_REQUEST['lid'];}
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$lid = $_POST['location'];
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
        if (checkdate(document.cplist.dateFrom)) {
            if (checkdate(document.cplist.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.cplist.dateFrom, document.cplist.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.cplist.startYear, document.cplist.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.cplist.dateTo, document.cplist.endYear);
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

    function paging_cplist() {
        window.location = "cplist.php?lid=" + document.getElementById("location").value + "&pg=" + document
            .getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sm=" +
            document.getElementById("date1").value + "&em=" + document.getElementById("date2").value;
    }

    function firstpage_cplist() {
        document.getElementById("page").value = 1;
        paging_cplist();
    }

    function previouspage_cplist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_cplist();
    }

    function nextpage_cplist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_cplist();
    }

    function lastpage_cplist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_cplist();
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
    <form name="cplist" id="cplist" method="post" action="cplist.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1000">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Cash Purchase List</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td><?php 
	if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
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
		echo '</select>';
	} elseif($_SESSION['stores_utype']=="U"){
		$lid = $locid;
		echo 'Location: ';
		echo '<input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" />';
	}?>
                    </td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "cplist",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "cplist",
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
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="print_area">
                            <table align="center" border="1" bordercolorlight="#7ECD7A" id="printTable" cellpadding="2"
                                cellspacing="0" width="100%">
                                <tbody>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="5%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none;" rowspan="2">
                                            Sl.
                                            No.</td>
                                        <td width="15%" valign="middle" style="border-top:none; border-left:none">Memo
                                            No.
                                        </td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Date
                                        </td>
                                        <td width="20%" valign="middle" style="border-top:none; border-left:none"
                                            colspan="2">Particulars</td>
                                        <td width="10%" valign="middle" style="border-top:none; border-left:none">Memo
                                            Amount</td>
                                        <td width="20%" valign="middle" style="border-top:none; border-left:none">
                                            Taken-in
                                            Company</td>
                                        <td width="20%" valign="middle"
                                            style="border-top:none; border-left:none; border-right:none;">Location</td>
                                    </tr>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="25%" colspan="2" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Item Name</td>
                                        <td width="12%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none; border-right:none;"
                                            align="right">Purchase&nbsp;</td>
                                        <td width="8%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none" align="left">
                                            Qnty.
                                        </td>
                                        <td width="10%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Item Value
                                        </td>
                                        <td width="20%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Indent No.
                                        </td>
                                        <td width="20%" valign="middle"
                                            style="border-top:none; border-left:none; border-bottom:none">Indent Date
                                        </td>
                                    </tr>
                                    <?php 
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	
	$sql = "SELECT tblcashmemo.*, company_name, location_name FROM tblcashmemo INNER JOIN company ON tblcashmemo.company_id = company.company_id INNER JOIN location ON tblcashmemo.location_id = location.location_id WHERE (memo_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql .= " AND tblcashmemo.location_id=".$lid;
	}
	$sql .= " ORDER BY memo_date, txn_id LIMIT ".$start.",".$end;
	$res = mysql_query($sql) or die(mysql_error());
	while($row=mysql_fetch_array($res)){
		$ctr++;
		echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
		echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="15%">'.$row['memo_no'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['memo_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['particulars'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="10%">'.$row['memo_amt'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['company_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['location_name'].'</td>';
		echo '</tr>';
		
		$sql_item = mysql_query("SELECT tblcash_item.*, item_name, unit_name,ic.category FROM tblcash_item INNER JOIN item ON tblcash_item.item_id = item.item_id INNER JOIN item_category ic ON ic.category_id = tblcash_item.item_category INNER JOIN unit ON tblcash_item.unit_id = unit.unit_id WHERE txn_id=".$row['txn_id']." ORDER BY seq_no") or die(mysql_error());
		while($row_item=mysql_fetch_array($sql_item)){
			$itemValue = number_format($row_item['memo_qnty'] * $row_item['rate'],2,".","");
			$indent_number = "";
			$indent_date = "";
			
			$sql_ind = mysql_query("SELECT * FROM tbl_indent WHERE indent_id=".$row_item['indent_id']) or die(mysql_error());
			if(mysql_num_rows($sql_ind)>0){
				$row_ind = mysql_fetch_assoc($sql_ind);
				$indent_number = ($row_ind['indent_no']>999 ? $row_ind['indent_no'] : ($row_ind['indent_no']>99 && $row_ind['indent_no']<1000 ? "0".$row_ind['indent_no'] : ($row_ind['indent_no']>9 && $row_ind['indent_no']<100 ? "00".$row_ind['indent_no'] : "000".$row_ind['indent_no'])));
				if($row_ind['ind_prefix']!=null){$indent_number = $row_ind['ind_prefix']."/".$indent_number;}
				$indent_date = date("d-m-Y", strtotime($row_ind['indent_date']));
			}
			
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			echo '<td style="border-left:none; border-bottom:none" width="5%">&nbsp;</td>';
			echo '<td style="border-left:none; border-bottom:none" width="25%" colspan="2">'.$row_item['seq_no'].")&nbsp;&nbsp;".$row_item['item_name'].' ~~'.$row_item['category'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="12%" align="right">'.($row_item['memo_qnty']==0?"&nbsp;":$row_item['memo_qnty']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="8%">&nbsp;'.($row_item['memo_qnty']==0?"&nbsp;":$row_item['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.($itemValue==0?"&nbsp;":$itemValue).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%">'.$indent_number.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%">'.$indent_date.'</td>';
			echo '</tr>';
		}
	}
	?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php 
	$sql_total = "SELECT * FROM tblcashmemo WHERE (memo_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."')";
	if($lid!=0){
		$sql_total .= " AND location_id=".$lid;
	}
	$res_total = mysql_query($sql_total) or die(mysql_error());
	$tot_row=mysql_num_rows($res_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_cplist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_cplist()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_cplist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_cplist()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_cplist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_cplist()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>