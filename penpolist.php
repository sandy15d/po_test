<?php 
include("menu.php");
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <script language="javascript" type="text/javascript">
    function paging_polist() {
        window.location = "penpolist.php?pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value;
    }

    function firstpage_polist() {
        document.getElementById("page").value = 1;
        paging_polist();
    }

    function previouspage_polist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_polist();
    }

    function nextpage_polist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_polist();
    }

    function lastpage_polist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_polist();
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
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="1000px">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                <td>Pending Purchase Order List</td>
                <td><input type="image" src="images/print.gif" onclick="funPrint()" /></td>
            </tr>
            <tr>
                <td>
                    <div id="print_area">
                        <table align="center" border="1" bordercolorlight="#7ECD7A" id="printTable" cellpadding="2"
                            cellspacing="0" width="100%">
                            <tbody>
                                <tr bgcolor="#E6E1B0" align="center"
                                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
                                    <td width="3%" valign="middle" style="border-top:none; border-left:none;">Sl.No.
                                    </td>
                                    <td width="15%" valign="middle" style="border-top:none; border-left:none;">Indent
                                        No.
                                    </td>
                                    <td width="10%" valign="middle" style="border-top:none; border-left:none;">Date</td>
                                    <td width="20%" valign="middle" style="border-top:none; border-left:none;">Item Name
                                    </td>
                                    <td width="10%" valign="middle"
                                        style="border-top:none; border-left:none; border-right:none;" align="right">
                                        Indent
                                    </td>
                                    <td width="4%" valign="middle" style="border-top:none; border-left:none;"
                                        align="left">
                                        &nbsp;Qnty.</td>
                                    <td width="10%" valign="middle"
                                        style="border-top:none; border-left:none; border-right:none;" align="right">
                                        Approved
                                    </td>
                                    <td width="4%" valign="middle" style="border-top:none; border-left:none;"
                                        align="left">
                                        &nbsp;Qnty.</td>
                                    <td width="10%" valign="middle" style="border-top:none; border-left:none;">Expected
                                        Delivery</td>
                                    <td width="14%" valign="middle"
                                        style="border-top:none; border-left:none; border-right:none;">Order For</td>
                                </tr>
                                <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg=$_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr = $start;
		
/*		$sql = mysql_query("SELECT tblpo.*, tblpo_dtm.total_amount, party_name, company_name, location_name FROM tblpo INNER JOIN ".DATABASE1.".party ON tblpo.party_id = party.party_id INNER JOIN ".DATABASE1.".company ON tblpo.company_id = company.company_id INNER JOIN ".DATABASE1.".location ON tblpo.delivery_at = location.location_id LEFT OUTER JOIN tblpo_dtm ON tblpo.po_id = tblpo_dtm.po_id WHERE po_status='U' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') ORDER BY po_date, po_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
			
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.$poNo.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['po_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%" colspan="2">'.$row['party_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['company_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="right">'.(($row['total_amount']==NULL || $row['total_amount']==0)?"&nbsp;":$row['total_amount']).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%">'.date("d-m-Y",strtotime($row['delivery_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none;" width="15%">'.$row['location_name'].'</td>';
			echo '</tr>';*/
			
		$sql = mysql_query("SELECT * FROM tbl_indent INNER JOIN tbl_indent_item ON tbl_indent.indent_id = tbl_indent_item.indent_id WHERE item_ordered='N' AND indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."' ORDER BY indent_date, tbl_indent.indent_id, seq_no LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$ctr += 1;
			$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
			if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
			
			$itemName = "";
			if($row['item_id']>0){
				$sqlItem = mysql_query("SELECT item_name FROM item WHERE item_id=".$row['item_id']) or die(mysql_error());
				if(mysql_num_rows($sqlItem)>0){
					$rowItem = mysql_fetch_assoc($sqlItem);
					$itemName = $rowItem["item_name"];
				}
            }
            
            $category_name ="";
            if($row['item_category']>0){
                $sqlCategory =mysql_query("SELECT category FROM item_category WHERE category_id =".$row['item_category'])or die (mysql_error());
                if(mysql_num_rows($sqlCategory)>0){
					$rowCategory = mysql_fetch_assoc($sqlCategory);
					$category_name = $rowCategory["category"];
				}
            }
			
			$unitName = "";
			if($row['unit_id']>0){
				$sqlUnit1 = mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row['unit_id']) or die(mysql_error());
				if(mysql_num_rows($sqlUnit1)>0){
					$rowUnit1 = mysql_fetch_assoc($sqlUnit1);
					$unitName = $rowUnit1["unit_name"];
				}
			}
			
			$orderFrom = "";
			if($row['order_from']>0){
				$sqlLoc1 = mysql_query("SELECT location_name FROM location WHERE location_id=".$row['order_from']) or die(mysql_error());
				if(mysql_num_rows($sqlLoc1)>0){
					$rowLoc1 = mysql_fetch_assoc($sqlLoc1);
					$orderFrom = $rowLoc1["location_name"];
				}
			}
			
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:20px;">';
			echo '<td style="border-top:none; border-left:none;">'.$ctr.'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$indent_number.'</td>';
			echo '<td style="border-top:none; border-left:none;" align="center">'.date("d-m-Y",strtotime($row['indent_date'])).'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$itemName.' ~~'.$category_name.'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none;" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
			echo '<td style="border-top:none; border-left:none;">&nbsp;'.($row['qnty']==0?"&nbsp;":$unitName).'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none;" align="right">'.($row['aprvd_qnty']==0?"&nbsp;":$row['aprvd_qnty']).'</td>';
			echo '<td style="border-top:none; border-left:none;">&nbsp;'.($row['aprvd_qnty']==0?"&nbsp;":$unitName).'</td>';
			echo '<td style="border-top:none; border-left:none;" align="center">'.date("d/m/Y",strtotime($row['supply_date'])).'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none;">'.$orderFrom.'</td>';
			echo '</tr>';
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
		$sql_total = mysql_query("SELECT * FROM tbl_indent INNER JOIN tbl_indent_item ON tbl_indent.indent_id = tbl_indent_item.indent_id WHERE item_ordered='N' AND indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."'") or die(mysql_error());
		$tot_row=mysql_num_rows($sql_total);
		$total_page=0;
		echo 'Total <span style="color:red">'.$tot_row.'</span> PO.Pending &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_polist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if($tot_row>$end){
			echo "Page number: ";
			$total_page=ceil($tot_row/$end);
			echo '<select name="page" id="page" onchange="paging_polist()" style="vertical-align:middle">';
			for($i=1;$i<=$total_page;$i++)
			{
				//if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)
				if($pg==$i)
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
			echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_polist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_polist()" />&nbsp;&nbsp;';
		if($total_page>1 && $pg<$total_page)
			echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_polist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_polist()" />';?>
                    &nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72"
                            height="22" style="display:inline;cursor:hand;" border="0" /></a>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>