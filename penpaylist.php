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
    function paging_paylist() {
        window.location = "penpaylist.php?pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value;
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
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="875px">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                <td>Pending Payment List&nbsp;&nbsp;&nbsp;<input type="image" src="images/print.gif"
                        onclick="funPrint()" /></td>
            </tr>
            <tr>
                <td>
                    <div id="print_area">
                        <table align="center" border="1" id="printTable" bordercolorlight="#7ECD7A" cellpadding="2"
                            cellspacing="0" width="100%">
                            <tbody>
                                <tr bgcolor="#E6E1B0" align="center"
                                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600">
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Sl.No.
                                    </td>
                                    <td width="20%" style="border-top:none; border-left:none; border-bottom:none">Bill
                                        No.
                                    </td>
                                    <td width="10%" style="border-top:none; border-left:none; border-bottom:none">Date
                                    </td>
                                    <td width="25%" style="border-top:none; border-left:none; border-bottom:none">Party
                                        Name
                                    </td>
                                    <td width="15%" style="border-top:none; border-left:none; border-bottom:none">Bill
                                        Amount</td>
                                    <td width="25%"
                                        style="border-top:none; border-left:none; border-bottom:none; border-right:none">
                                        Bill to Company</td>
                                </tr>
                                <?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		$ctr = $start;
		$cnt = 0;
		
		$sql = mysql_query("SELECT tblbill.*, party_name, company_name FROM tblbill INNER JOIN party ON tblbill.party_id = party.party_id INNER JOIN company ON tblbill.company_id = company.company_id WHERE bill_return = 0 AND bill_paid = 'N' ORDER BY bill_date, bill_no LIMIT ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			if($cnt==1){
				echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000;">';
				$cnt = 0;
			} else {
				echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000;">';
				$cnt = 1;
			}
			$ctr += 1;
			echo '<td style="border-left:none; border-bottom:none" width="5%">'.$ctr.'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="20%">'.$row['bill_no'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="10%" align="center">'.date("d/m/Y",strtotime($row['bill_date'])).'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="25%">'.$row['party_name'].'</td>';
			echo '<td style="border-left:none; border-bottom:none" width="15%" align="right">'.$row['bill_amt'].'</td>';
			echo '<td style="border-left:none; border-bottom:none; border-right:none" width="25%">'.$row['company_name'].'</td>';
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
			$sql_total = mysql_query("SELECT * FROM tblbill WHERE bill_return = 0 AND bill_paid = 'N'") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> Bills Pending for Payment &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_paylist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($tot_row>$end)
			{
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
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';
			if($total_page>1 && $pg>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_paylist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_paylist()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_paylist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_paylist()" />';?>
                    &nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72"
                            height="22" style="display:inline;cursor:hand;" border="0" /></a>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>