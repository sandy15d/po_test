<?php 
include("menu.php");
/*-------------------------------*/
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
/*-------------------------------*/
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
        if (checkdate(document.logbook.dateFrom)) {
            if (checkdate(document.logbook.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.logbook.dateFrom, document.logbook.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.logbook.startYear, document.logbook.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.logbook.dateTo, document.logbook.endYear);
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

    function paging_logbook() {
        window.location = "logbook.php?pg=" + document.getElementById("page").value + "&tr=" + document.getElementById(
                "displayTotalRows").value + "&sm=" + document.getElementById("date1").value + "&em=" + document
            .getElementById("date2").value;
    }

    function firstpage_logbook() {
        document.getElementById("page").value = 1;
        paging_logbook();
    }

    function previouspage_logbook() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_logbook();
    }

    function nextpage_logbook() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_logbook();
    }

    function lastpage_logbook() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_logbook();
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
    <form name="logbook" id="logbook" method="post" action="logbook.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="1000px">
            <tbody>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold ; color: #000000">
                    <td>Stores Log Book</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom"
                            id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y",$sm); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "logbook",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "logbook",
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
                            <table border="1" bordercolorlight="#7ECD7A" cellpadding="5" cellspacing="0" id="printTable"
                                width="100%">
                                <tbody>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Voucher
                                            ID</td>
                                        <td width="8%" style="border-top:none; border-left:none; border-bottom:none">
                                            Voucher
                                            Date</td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Voucher
                                            Type</td>
                                        <td width="8%" style="border-top:none; border-left:none; border-bottom:none">
                                            Entry
                                            Date</td>
                                        <td width="20%" style="border-top:none; border-left:none; border-bottom:none">
                                            Particulars</td>
                                        <td width="15%" style="border-top:none; border-left:none; border-bottom:none">
                                            Item
                                            Name</td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Quantity</td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">UOM
                                        </td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Rate
                                        </td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Amount
                                        </td>
                                        <td width="9%" style="border-top:none; border-left:none; border-bottom:none">
                                            User
                                            Location</td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            Action
                                        </td>
                                        <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                            User
                                        </td>
                                    </tr>
                                    <?php 
	$cnt = 0;
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	
	$ctr = $start;
	$sql = mysql_query("SELECT * FROM logbook WHERE (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') ORDER BY entry_date, rec_id LIMIT ".$start.",".$end) or die(mysql_error());
	while($row=mysql_fetch_array($sql)){
		if($cnt==1){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 0;
		} else {
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 1;
		}
		$ctr++;
		echo '<td style="border-left:none; border-bottom:none;">'.$row['voucher_id'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.date("d-m-Y",strtotime($row['voucher_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['voucher_type'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.date("d-m-Y",strtotime($row['entry_date'])).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['particulars'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['item_name']==NULL? "&nbsp;" : $row['item_name']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="right">'.($row['item_qnty']==0? "&nbsp;" : $row['item_qnty']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['unit']==NULL? "&nbsp;" : $row['unit']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="right">'.($row['item_rate']==0? "&nbsp;" : $row['item_rate']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="right">'.($row['item_amount']==0? "&nbsp;" : $row['item_amount']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['location']==NULL? "&nbsp;" : $row['location']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['action']==NULL? "&nbsp;" : $row['action']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['user']==NULL? "&nbsp;" : $row['user']).'</td>';
		echo '</tr>';
	} ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php 
	$sql_total = mysql_query("SELECT * FROM logbook WHERE entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."'") or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_logbook()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_logbook()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_logbook()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_logbook()" />&nbsp;&nbsp;';
	if($total_page>1 && $pg<$total_page)
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_logbook()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_logbook()" />'; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>