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
    function paging_pblist() {
        window.location = "penpblist.php?pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value;
    }

    function firstpage_pblist() {
        document.getElementById("page").value = 1;
        paging_pblist();
    }

    function previouspage_pblist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_pblist();
    }

    function nextpage_pblist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_pblist();
    }

    function lastpage_pblist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_pblist();
    }

    function funPrint() {
        var var1 = document.getElementById("printTable").outerHTML;
        var varOpen = window.open("");
        varOpen.window.document.write(var1);
        varOpen.print();
        varOpen.close();;
    }
    </script>
</head>

<body>
    <?php echo date("d-m-Y, h:i:s");?>
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="1500px">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                <td>Pending Purchase Bill List&nbsp;&nbsp;&nbsp;<input type="image" src="images/print.gif"
                        onclick="funPrint()" /></td>
            </tr>
            <tr>
                <td>
                    <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" id="printTable"
                        cellspacing="0" width="100%">
                        <tbody>
                            <tr bgcolor="#E6E1B0" align="center"
                                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
                                <td width="3%" valign="middle" style="border-top:none; border-left:none;">Sl.No.</td>
                                <td width="7%" valign="middle" style="border-top:none; border-left:none">PO No.</td>
                                <td width="7%" valign="middle" style="border-top:none; border-left:none">Date</td>
                                <td width="15%" valign="middle" style="border-top:none; border-left:none">Party Name
                                </td>
                                <td width="15%" valign="middle" style="border-top:none; border-left:none">
                                    Order-in-Company</td>
                                <td width="15%" valign="middle" style="border-top:none; border-left:none;">Item Name
                                </td>
                                <td width="5%" valign="middle"
                                    style="border-top:none; border-left:none; border-right:none;" align="right">Ordered
                                </td>
                                <td width="3%" valign="middle" style="border-top:none; border-left:none;" align="left">
                                    &nbsp;Qnty.</td>
                                <td width="5%" valign="middle"
                                    style="border-top:none; border-left:none; border-right:none;" align="right">Received
                                </td>
                                <td width="3%" valign="middle" style="border-top:none; border-left:none;" align="left">
                                    &nbsp;Qnty.</td>
                                <td width="6%" valign="middle" style="border-top:none; border-left:none">Expected
                                    Delivery</td>
                                <td width="6%" valign="middle" style="border-top:none; border-left:none;">Received Date
                                </td>
                                <td width="5%" valign="middle" style="border-top:none; border-left:none;">Delivery At
                                </td>
                                <td width="5%" valign="middle"
                                    style="border-top:none; border-left:none; border-right:none;">Received At</td>
                            </tr>
                            <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg=$_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr = $start;
		$poId = 0;
		$poNo = 0;
		
		$sql = mysql_query("SELECT * FROM (SELECT table1.*, receipt_date, receipt_qnty, recd_at, IFNULL(tblbill_item.bill_id,0) AS billid FROM (SELECT tblpo.po_id, po_no, po_date, party_id, company_id, delivery_date, delivery_at, seq_no, item_id, qnty, unit_id,item_category FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y'  AND po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN (SELECT tblreceipt1.receipt_id, receipt_date, tbldelivery1.po_id, recd_at, seq_no, item_id, receipt_qnty FROM tblreceipt1 INNER JOIN tblreceipt2 ON tblreceipt1.receipt_id = tblreceipt2.receipt_id INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id) AS table2 ON (table1.po_id = table2.po_id AND table1.item_id = table2.item_id) LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid=0 ORDER BY po_date, po_id LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$poId = $row['po_id'];
			$poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
			$ctr += 1;
			
			$partyName = "";
			if($row['party_id']>0){
				$sqlParty = mysql_query("SELECT party_name FROM party WHERE party_id=".$row['party_id']) or die(mysql_error());
				if(mysql_num_rows($sqlParty)>0){
					$rowParty = mysql_fetch_assoc($sqlParty);
					$partyName = $rowParty["party_name"];
				}
			}
			
			$companyName = "";
			if($row['company_id']>0){
				$sqlComp = mysql_query("SELECT company_name FROM company WHERE company_id=".$row['company_id']) or die(mysql_error());
				if(mysql_num_rows($sqlComp)>0){
					$rowComp = mysql_fetch_assoc($sqlComp);
					$companyName = $rowComp["company_name"];
				}
			}
			
			$orderFrom = "";
			if($row['delivery_at']>0){
				$sqlLoc1 = mysql_query("SELECT location_name FROM location WHERE location_id=".$row['delivery_at']) or die(mysql_error());
				if(mysql_num_rows($sqlLoc1)>0){
					$rowLoc1 = mysql_fetch_assoc($sqlLoc1);
					$orderFrom = $rowLoc1["location_name"];
				}
			}
			
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
			
			$receivedAt = "";
			if($row['recd_at']>0){
				$sqlLoc2 = mysql_query("SELECT location_name FROM location WHERE location_id=".$row['recd_at']) or die(mysql_error());
				if(mysql_num_rows($sqlLoc2)>0){
					$rowLoc2 = mysql_fetch_assoc($sqlLoc2);
					$receivedAt = $rowLoc2["location_name"];
				}
			}
			
			echo '<tr bgcolor="#EEEEEE" height="22" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
			echo '<td style="border-top:none; border-left:none;">'.$ctr.'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$poNo.'</td>';
			echo '<td style="border-top:none; border-left:none;" align="center">'.date("d/m/Y",strtotime($row['po_date'])).'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$partyName.'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$companyName.'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$itemName.' ~~'.$category_name.'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none;" align="right">'.($row['qnty']==0?"&nbsp;":$row['qnty']).'</td>';
			echo '<td style="border-top:none; border-left:none;">&nbsp;'.($row['qnty']==0?"&nbsp;":$unitName).'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none" align="right">'.($row['receipt_qnty']==0?"&nbsp;":$row['receipt_qnty']).'</td>';
			echo '<td style="border-top:none; border-left:none;">&nbsp;'.($row['receipt_qnty']==0?"&nbsp;":$unitName).'</td>';
			echo '<td style="border-top:none; border-left:none;" align="center">'.date("d/m/Y",strtotime($row['delivery_date'])).'</td>';
			echo '<td style="border-top:none; border-left:none;" align="center">'.($row['receipt_date']==null ? "&nbsp;" : date("d/m/Y",strtotime($row['receipt_date']))).'</td>';
			echo '<td style="border-top:none; border-left:none;">'.$orderFrom.'</td>';
			echo '<td style="border-top:none; border-left:none; border-right:none;">'.$receivedAt.'</td>';
			echo '</tr>';
		}
		
		?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <?php 
			$sql_total = mysql_query("SELECT * FROM (SELECT table1.*, IFNULL(tblbill_item.bill_id,0) AS billid FROM (SELECT tblpo.po_id, po_date, item_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y' AND po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid=0") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> P.O. Pending for Bills &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_pblist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_pblist()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_pblist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_pblist()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_pblist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_pblist()" />';?>
                    &nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72"
                            height="22" style="display:inline;cursor:hand;" border="0" /></a>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>