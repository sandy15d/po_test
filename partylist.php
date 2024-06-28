<?php 
include("menu.php");
/* SELECT party.party_name,party.address1,party.address2,party.address3,city.city_name,party.contact_person,party.email_id,party.mobile_no,party.pan,party.tin,party.gstno,party.op_balance,party.credit_days FROM `party`
JOIN city ON city.city_id = party.city_id
*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <script language="javascript" type="text/javascript">
    function paging_partylist() {
        window.location = "partylist.php?pg=" + document.getElementById("page").value + "&tr=" + document
            .getElementById("displayTotalRows").value;
    }

    function firstpage_partylist() {
        document.getElementById("page").value = 1;
        paging_partylist();
    }

    function previouspage_partylist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_partylist();
    }

    function nextpage_partylist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_partylist();
    }

    function lastpage_partylist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_partylist();
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
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="1175px" id="mytable">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold ; color: #000000">
                <td>Party Master List<input type="image" src="images/print.gif" style="float: right"
                        onclick="funPrint()" /></td>
            </tr>
            <tr>
                <td>
                    <div id="print_area">
                        <table align="center" border="1" bordercolorlight="#7ECD7A" id="printTable" cellpadding="5"
                            cellspacing="0" width="100%">
                            <tbody>
                                <tr bgcolor="#E6E1B0" align="center"
                                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Sl.No.
                                    </td>
                                    <td width="25%" style="border-top:none; border-left:none; border-bottom:none">Party
                                        Name
                                    </td>
                                    <td width="10%" style="border-top:none; border-left:none; border-bottom:none">City
                                        Name
                                    </td>
                                    <td width="10%" style="border-top:none; border-left:none; border-bottom:none">State
                                    </td>
                                    <td width="20%" style="border-top:none; border-left:none; border-bottom:none">
                                        Address
                                    </td>
                                    <td width="15%" style="border-top:none; border-left:none; border-bottom:none">
                                        Contact
                                        Person</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Mobile
                                        No.
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Email
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">PAN
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">TIN
                                    </td>

                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">GST
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Op.
                                        Bal.
                                    </td>

                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Category
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Credit
                                        Days
                                    </td>
                                </tr>
                                <?php 
	$cnt = 0;
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	
	$sql = mysql_query("SELECT party.*,city_name,state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id ORDER BY party_name LIMIT ".$start.",".$end) or die(mysql_error());
	while($row=mysql_fetch_array($sql)){
		$category="";
		if($row['category']==1)
			$category = "Preferencial";
		elseif($row['category']==2)
			$category = "blank-1";
		elseif($row['category']==3)
			$category = "blank-2";
		elseif($row['category']==4)
			$category = "blank-3";
		
		if($cnt==1){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 0;
		} else {
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 1;
		}
		$ctr += 1;
		echo '<td style="border-left:none; border-bottom:none;">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['party_name'].'</a></td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['city_name'].'</a></td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['state_name'].'</td>';
echo '<td style="border-left:none; border-bottom:none;">'.$row['address1'].', '.$row['address2'].' '.$row['address3'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['contact_person']==null?"&nbsp;":$row['contact_person']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['mobile_no']==null?"&nbsp;":$row['mobile_no']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['email_id']==null?"&nbsp;":$row['email_id']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['pan']==null?"&nbsp;":$row['pan']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['tin']==null?"&nbsp;":$row['tin']).'</td>';

                echo '<td style="border-left:none; border-bottom:none;">'.($row['gstno']==null?"&nbsp;":$row['gstno']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['op_balance']==null?"&nbsp;":$row['op_balance']).'</td>';

		echo '<td style="border-left:none; border-bottom:none;">'.($category==""?"&nbsp;":$category).'</td>';
		echo '<td style="border-left:none; border-bottom:none; text-align:center;">'.$row['credit_days'].'</td>';
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
	$sql_total = mysql_query("SELECT * FROM party WHERE 1") or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_partylist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_partylist()" style="vertical-align:middle">';
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
	if($total_page>1 && $pg>1){
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_partylist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_partylist()" />&nbsp;&nbsp;';}
	if($total_page>1 && $pg<$total_page){
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_partylist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_partylist()" />';}?>
                    &nbsp;&nbsp;<img src="images/back.gif" onclick="window.location='menu.php'" width="72" height="22"
                        style="display:inline;cursor:hand;" border="0" />
                </td>
            </tr>
        </tbody>
    </table>
    

</body>

</html>