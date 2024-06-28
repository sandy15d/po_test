<?php 
include("menu.php");
/*--------------------*/
if(isset($_REQUEST['lid'])){$lid = $_REQUEST['lid'];}
if(isset($_REQUEST['iid'])){$iid = $_REQUEST['iid'];}
if(isset($_REQUEST['flt'])){$flt = $_REQUEST['flt'];}
/*--------------------*/
if(isset($_POST['show'])){
	$sm=strtotime($_POST['dateFrom']);
	$em=strtotime($_POST['dateTo']);
	$lid = $_POST['location'];
	$iid = $_POST['itemName'];
	$flt = $_POST['filterData'];
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
        if (checkdate(document.itemstock1.dateFrom)) {
            if (checkdate(document.itemstock1.dateTo)) {
                var no_of_days1 = getDaysbetween2Dates(document.itemstock1.dateFrom, document.itemstock1.dateTo);
                if (no_of_days1 < 0) {
                    alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                    return false;
                } else {
                    var no_of_days2 = getDaysbetween2Dates(document.itemstock1.startYear, document.itemstock1.dateFrom);
                    if (no_of_days2 < 0) {
                        alert("* Report From date wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days3 = getDaysbetween2Dates(document.itemstock1.dateTo, document.itemstock1.endYear);
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

    function paging_list() {
        window.location = "itemstock1.php?lid=" + document.getElementById("location").value + "&iid=" + document
            .getElementById("itemName").value + "&flt=" + document.getElementById("filterData").value + "&pg=" +
            document.getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value +
            "&sm=" + document.getElementById("date1").value + "&em=" + document.getElementById("date2").value;
    }

    function firstpage_list() {
        document.getElementById("page").value = 1;
        paging_list();
    }

    function previouspage_list() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_list();
    }

    function nextpage_list() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_list();
    }

    function lastpage_list() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_list();
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
    <form name="itemstock1" id="itemstock1" method="post" action="itemstock1.php"
        onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1150px">
            <tbody>
                <tr>
                    <td width="30%"><?php echo date("d-m-Y, h:i:s");?></td>
                    <td width="15%">&nbsp;</td>
                    <td width="25%">&nbsp;</td>
                    <td width="30%">&nbsp;</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td colspan="4">Item Stock List</td>
                </tr>
                <tr align="center">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:20px;">
                    <td>&nbsp;</td><?php 
	echo '<td align="right">Select Item:</td>';
	echo '<td align="left"><select name="itemName" id="itemName" style="width:200px" >';
//	echo '<option selected value="0">All Items</option>';
	$sql_items=mysql_query("SELECT * FROM item ORDER BY item_name") or die(mysql_error());
	while($row_items=mysql_fetch_array($sql_items)){
		if($row_items["item_id"]==$iid)
			echo '<option selected value="'.$row_items["item_id"].'">'.$row_items["item_name"].'</option>';
		else
			echo '<option value="'.$row_items["item_id"].'">'.$row_items["item_name"].'</option>';
	}
	echo '</select></td>';?>
                    <td>&nbsp;</td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:20px;">
                    <td>&nbsp;</td><?php 
	if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
		echo '<td align="right">Select Location :</td>';
		echo '<td align="left"><select name="location" id="location" style="width:200px" >';
		echo '<option selected value="0">All Locations</option>';
		$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name") or die(mysql_error());
		while($row_location=mysql_fetch_array($sql_location)){
			if($row_location["location_id"]==$lid)
				echo '<option selected value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
			else
				echo '<option value="'.$row_location["location_id"].'">'.$row_location["location_name"].'</option>';
		}
		echo '</select></td>';
	} elseif($_SESSION['stores_utype']=="U"){
		echo '<td align="right">Location :</td>';
		echo '<td align="left"><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="'.$_SESSION['stores_lname'].'" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="'.$lid.'" /></td>';
	}?>
                    <td align="right">Filter:&nbsp;<select name="filterData" id="filterData"
                            style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; width:175px; height:17px; vertical-align:middle;">
                            <?php 
	if(isset($_REQUEST['flt'])){
		if($flt==0){
			echo '<option selected value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
		} elseif($flt==1){
			echo '<option value="0">&nbsp;</option><option selected value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
		} elseif($flt==2){
			echo '<option value="0">&nbsp;</option><option value="1">Only +ve stock</option><option selected value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
		} elseif($flt==3){
			echo '<option value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option selected value="3">Opening but no transactions</option>';
		}
	} else {
		echo '<option selected value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
	}?>
                        </select></td>
                </tr>
                <tr align="center"
                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td>&nbsp;<input type="hidden" name="startYear" id="startYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_syr']));?>" /><input
                            type="hidden" name="endYear" id="endYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION['stores_eyr']));?>" /></td>
                    <td align="center" colspan="2"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input
                            name="dateFrom" id="dateFrom" maxlength="10" size="10"
                            value="<?php echo date("d-m-Y",$sm); ?>" style="vertical-align:top;">&nbsp;<script
                            language="JavaScript">
                        new tcal({
                            "formname": "itemstock1",
                            "controlname": "dateFrom"
                        });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input
                            name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y",$em); ?>"
                            style="vertical-align:top;">&nbsp;<script language="JavaScript">
                        new tcal({
                            "formname": "itemstock1",
                            "controlname": "dateTo"
                        });
                        </script>
                    </td>
                    <td><input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input
                            type="hidden" name="show" value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72"
                            height="22" style="display:inline;cursor:hand;" border="0"
                            onclick="window.location='menu.php'" /><input type="image" src="images/print.gif"
                            onclick="funPrint()" /></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div id="print_area">
                            <table align="center" id="printTable" border="1" bordercolorlight="#7ECD7A" cellpadding="5"
                                cellspacing="0" width="100%">
                                <tbody>
                                    <tr bgcolor="#E6E1B0" align="center"
                                        style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                        <td width="4%" style="border-top:none; border-left:none; border-bottom:none">
                                            Sl.No.
                                        </td>
                                        <td width="40%" style="border-top:none; border-left:none; border-bottom:none">
                                            Location Name</td>
                                        <td width="14%" style="border-top:none; border-left:none; border-bottom:none"
                                            colspan="2">Op.Stock</td>
                                        <td width="14%" style="border-top:none; border-left:none; border-bottom:none"
                                            colspan="2">Incoming</td>
                                        <td width="14%" style="border-top:none; border-left:none; border-bottom:none"
                                            colspan="2">Outgoing</td>
                                        <td width="14%" style="border-top:none; border-left:none; border-bottom:none"
                                            colspan="2">Cl.Stock</td>
                                    </tr>
                                    <?php 
	if(isset($_POST['show']) || isset($_REQUEST['pg'])){
		$cnt=0;
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$ctr = $start;
		if($lid>0){													//if(location == single location)
			$sql = mysql_query("SELECT stock_register.location_id, location_name, unit_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN location ON stock_register.location_id = location.location_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE stock_register.item_id=".$iid." AND stock_register.location_id=".$lid." AND entry_date<='".date("Y-m-d",$em)."' LIMIT ".$start.",".$end) or die(mysql_error());
		} elseif($lid==0){											//if(location == all locations)
			if($flt==0){											//if(filter == no filetr)
				$sql = mysql_query("SELECT stock_register.location_id, location_name, unit_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN location ON stock_register.location_id = location.location_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE stock_register.item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name LIMIT ".$start.",".$end) or die(mysql_error());
			} elseif($flt==1){										//if(filter == only +ve stock)
				$sql = mysql_query("SELECT stock_register.location_id, location_name, unit_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN location ON stock_register.location_id = location.location_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE stock_register.item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name HAVING qty>=0 LIMIT ".$start.",".$end) or die(mysql_error());
			} elseif($flt==2){										//if(filter == only -ve stock)
				$sql = mysql_query("SELECT stock_register.location_id, location_name, unit_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN location ON stock_register.location_id = location.location_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE stock_register.item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name HAVING qty<0 LIMIT ".$start.",".$end) or die(mysql_error());
			} elseif($flt==3){										//if(filter == only opening and no transactions during the period)
				$sql = mysql_query("SELECT stock_register.location_id, location_name, unit_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN location ON stock_register.location_id = location.location_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE stock_register.item_id=".$iid." AND (entry_date<'".date("Y-m-d",$sm)."' OR (entry_date='".date("Y-m-d",$sm)."' AND entry_mode ='O+' )) GROUP BY location_name HAVING qty>0 AND stock_register.location_id NOT IN (SELECT location_id FROM  stock_register WHERE item_id=".$iid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') AND entry_mode !='O+' GROUP BY location_id) LIMIT ".$start.",".$end) or die(mysql_error());
			}
		}
		
		while($row=mysql_fetch_array($sql))
		{
			$opqnty = 0;
			$clqnty = 0;
			$incoming = 0;
			$outgoing = 0;
			$sql_opstk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE location_id=".$row['location_id']." AND item_id=".$iid." AND (entry_date<'".date("Y-m-d",$sm)."' OR (entry_date='".date("Y-m-d",$sm)."' AND entry_mode='O+'))") or die(mysql_error());
			$row_opstk = mysql_fetch_assoc($sql_opstk);
			$opqnty = $row_opstk['itemqnty'];
			
			$sql_instk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE location_id=".$row['location_id']." AND item_id=".$iid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') AND (entry_mode='R+' OR entry_mode='I-' OR entry_mode='T+' OR entry_mode='X+' OR entry_mode='P+' OR entry_mode='C+')") or die(mysql_error());
			$row_instk = mysql_fetch_assoc($sql_instk);
			$incoming = $row_instk['itemqnty'];
			
			$sql_outstk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE location_id=".$row['location_id']." AND item_id=".$iid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') AND (entry_mode='R-' OR entry_mode='I+' OR entry_mode='T-' OR entry_mode='X-' OR entry_mode='P-')") or die(mysql_error());
			$row_outstk = mysql_fetch_assoc($sql_outstk);
			$outgoing = 0-$row_outstk['itemqnty'];
			
			if($cnt==1){
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
				$cnt = 0;
			} else {
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
				$cnt = 1;
			}
			$ctr++;
			//$x = "mthstock1.php?lid=".$lid."&iid=".$iid."&lid1=".$row['location_id']."&pid=".$pid."&flt=".$flt;
			$locationid = $row['location_id'];
			$x = "window.open('mthstock.php?lid=$locationid&iid=$iid&sm=$sm&em=$em', 'monthlystock', 'width=1075, height=650, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no, copyhistory=no')";
			echo '<td style="border-left:none; border-bottom:none" width="4%">'.$ctr.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="40%"><a onclick="'.$x.'" style="display:inline; cursor:hand; text-decoration:none; font-size:14px;color:#0000FF;">'.$row['location_name'].'</a></td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($opqnty==0?"&nbsp;":number_format($opqnty,3,".","")).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="4%">'.($opqnty==0?"&nbsp;":$row['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($incoming==0?"&nbsp;":number_format($incoming,3,".","")).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="4%">'.($incoming==0?"&nbsp;":$row['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($outgoing==0?"&nbsp;":number_format($outgoing,3,".","")).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="4%">'.($outgoing==0?"&nbsp;":$row['unit_name']).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">'.($row['qty']==0?"&nbsp;":number_format($row['qty'],3,".","")).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="4%">'.($row['qty']==0?"&nbsp;":$row['unit_name']).'</td>';
			echo '</tr>';
		}					//  end of while($row=mysql_fetch_array($sql)) statement
	}						//	end of if(isset($_REQUEST['pid'])) statement
	?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="right" colspan="4">
                        <?php 
	if(isset($_POST['show']) || isset($_REQUEST['pg'])){
		if($lid>0){													//if(location == single location)
			$sql_total = mysql_query("SELECT item_id, Sum(item_qnty) AS qty FROM  stock_register WHERE location_id=".$lid." AND item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."'") or die(mysql_error());
		} elseif($lid==0){											//if(location == all locations)
			if($flt==0){											//if(filter == no filetr)
				$sql_total = mysql_query("SELECT item_id, location_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN location ON stock_register.location_id = location.location_id WHERE item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name") or die(mysql_error());
			} elseif($flt==1){										//if(filter == only +ve stock)
				$sql_total = mysql_query("SELECT item_id, location_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN location ON stock_register.location_id = location.location_id WHERE item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name HAVING qty>=0") or die(mysql_error());
			} elseif($flt==2){										//if(filter == only -ve stock)
				$sql_total = mysql_query("SELECT item_id, location_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN location ON stock_register.location_id = location.location_id WHERE item_id=".$iid." AND entry_date<='".date("Y-m-d",$em)."' GROUP BY location_name HAVING qty<0") or die(mysql_error());
			} elseif($flt==3){										//if(filter == only opening and no transactions during the period)
				$sql_total = mysql_query("SELECT item_id, location_name, Sum(item_qnty) AS qty FROM  stock_register INNER JOIN location ON stock_register.location_id = location.location_id WHERE item_id=".$iid." AND (entry_date<'".date("Y-m-d",$sm)."' OR (entry_date='".date("Y-m-d",$sm)."' AND entry_mode ='O+')) GROUP BY location_name HAVING qty>0 AND stock_register.location_id NOT IN (SELECT location_id FROM  stock_register WHERE item_id=".$iid." AND (entry_date BETWEEN '".date("Y-m-d",$sm)."' AND '".date("Y-m-d",$em)."') AND entry_mode !='O+' GROUP BY location_id)") or die(mysql_error());
			}
		}
		$tot_row=mysql_num_rows($sql_total);
		$total_page=0;
		echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_list()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($tot_row>$end)
		{
			echo "Page number: ";
			$total_page=ceil($tot_row/$end);
			echo '<select name="page" id="page" onchange="paging_list()" style="vertical-align:middle">';
			for($i=1;$i<=$total_page;$i++)
			{
				if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)
					echo '<option selected value="'.$i.'">'.$i.'</option>';
				else
					echo '<option value="'.$i.'">'.$i.'</option>';
			}
			echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		} else {
			echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		
		echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" /><input type="hidden" name="date1" id="date1" value="'.$sm.'" /><input type="hidden" name="date2" id="date2" value="'.$em.'" />';
		if($total_page>1 && $pg>1)
			echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_list()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_list()" />&nbsp;&nbsp;';
		if($total_page>1 && $pg<$total_page)
			echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_list()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_list()" />';
	}
	?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo date("d-m-Y, h:i:s");?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </form>
</body>

</html>