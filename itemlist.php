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
    function paging_itemlist() {
        window.location = "itemlist.php?pg=" + document.getElementById("page").value + "&tr=" + document.getElementById(
            "displayTotalRows").value;
    }

    function firstpage_itemlist() {
        document.getElementById("page").value = 1;
        paging_itemlist();
    }

    function previouspage_itemlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage > 1) {
            cpage = cpage - 1;
            document.getElementById("page").value = cpage;
        }
        paging_itemlist();
    }

    function nextpage_itemlist() {
        var cpage = parseInt(document.getElementById("page").value);
        if (cpage < parseInt(document.getElementById("totalPage").value)) {
            cpage = cpage + 1;
            document.getElementById("page").value = cpage;
        }
        paging_itemlist();
    }

    function lastpage_itemlist() {
        document.getElementById("page").value = document.getElementById("totalPage").value;
        paging_itemlist();
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
    <table align="center" border="0" cellpadding="2" cellspacing="1" width="1200px">
        <tbody>
            <tr align="center"
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold ; color: #000000">
                <td>Item Master List<input type="image" src="images/print.gif" style="float: right"
                        onclick="funPrint()"></td>
            </tr>
            <tr>
                <td>
                    <div id="print_area">
                        <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="5" id="printTable"
                            cellspacing="0" width="100%">
                            <tbody>
                                <tr bgcolor="#E6E1B0" align="center"
                                    style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:25px;">
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Sl.No.
                                    </td>
                                    <td width="20%" style="border-top:none; border-left:none; border-bottom:none">Item
                                        Name
                                    </td>
                                    <td width="15%" style="border-top:none; border-left:none; border-bottom:none">Item
                                        Group
                                    </td>
                                    <td width="6%" style="border-top:none; border-left:none; border-bottom:none">UOM
                                    </td>
                                    <td width="6%" style="border-top:none; border-left:none; border-bottom:none">Water
                                        Rqrmnt.(per Acre)</td>
                                    <td width="6%" style="border-top:none; border-left:none; border-bottom:none">
                                        Recomnd.
                                        Dose (per Acre)</td>
                                    <td width="6%" style="border-top:none; border-left:none; border-bottom:none">
                                        Max.Dose
                                        (per Acre)</td>
                                    <td width="6%" style="border-top:none; border-left:none; border-bottom:none">
                                        Min.Dose
                                        (per Acre)</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Appl.Method
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Reorder
                                        Level</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Lead-Time(in days)</td>
                                    <td width="10%" style="border-top:none; border-left:none; border-bottom:none">
                                        Technical
                                        Name</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Useful-In-1
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Useful-In-2
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">
                                        Useful-In-3
                                    </td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Rqrmnt.
                                        Period1</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Rqrmnt.
                                        Period2</td>
                                    <td width="5%" style="border-top:none; border-left:none; border-bottom:none">Rqrmnt.
                                        Period3</td>
                                </tr>
                                <?php 
	$cnt = 0;
	$start = 0;
	$pg = 1;
	if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
	if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
	$ctr = $start;
	$arr = array(1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec');
	
	$sql = mysql_query("SELECT item.*,itgroup_name,unit_name,ic.category FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id INNER JOIN unit ON item.unit_id=unit.unit_id INNER JOIN item_category ic ON ic.item_id = item.item_id ORDER BY itgroup_name,item_name LIMIT ".$start.",".$end) or die(mysql_error());
	while($row=mysql_fetch_array($sql)){
		$appmethod="";
		if($row['app_method']==1)
			$appmethod = "Direct";
		elseif($row['app_method']==2)
			$appmethod = "Drip";
		elseif($row['app_method']==3)
			$appmethod = "Spray";
		
		$requireperiod1 = "";
		$requireperiod2 = "";
		$requireperiod3 = "";
		if($row['rp_from1']>0 && $row['rp_to1']>0){$requireperiod1 = $arr[$row['rp_from1']].'-'.$arr[$row['rp_to1']];}
		if($row['rp_from2']>0 && $row['rp_to2']>0){$requireperiod2 = $arr[$row['rp_from2']].'-'.$arr[$row['rp_to2']];}
		if($row['rp_from3']>0 && $row['rp_to3']>0){$requireperiod3 = $arr[$row['rp_from3']].'-'.$arr[$row['rp_to3']];}
		
		$usefulin1 = "";
		$sql_use1=mysql_query("SELECT * FROM usability WHERE usability_id=".$row['usability_id1']) or die(mysql_error());
		$row_use1=mysql_fetch_assoc($sql_use1);
		if(mysql_num_rows($sql_use1)>0){$usefulin1 = $row_use1['usability_name'];}
		$usefulin2 = "";
		$sql_use2=mysql_query("SELECT * FROM usability WHERE usability_id=".$row['usability_id2']) or die(mysql_error());
		$row_use2=mysql_fetch_assoc($sql_use2);
		if(mysql_num_rows($sql_use2)>0){$usefulin2 = $row_use2['usability_name'];}
		$usefulin3 = "";
		$sql_use3=mysql_query("SELECT * FROM usability WHERE usability_id=".$row['usability_id3']) or die(mysql_error());
		$row_use3=mysql_fetch_assoc($sql_use3);
		if(mysql_num_rows($sql_use3)>0){$usefulin3 = $row_use3['usability_name'];}
		
		if($cnt==1){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 0;
		} else {
			echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight: normal; color:#000000; height:25px;">';
			$cnt = 1;
		}
		$ctr += 1;
		echo '<td style="border-left:none; border-bottom:none;">'.$ctr.'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['item_name'].' ~~'.$row['category'].'</a></td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['itgroup_name'].'</a></td>';
		echo '<td style="border-left:none; border-bottom:none;">'.$row['unit_name'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['water_require'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['recomend_dose'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['max_dose'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['min_dose'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($appmethod==""?"&nbsp;":$appmethod).'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['reorder_level'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;" align="center">'.$row['lead_time'].'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($row['tech_name']==null?"&nbsp;":$row['tech_name']).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($usefulin1==""?"&nbsp;":$usefulin1).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($usefulin2==""?"&nbsp;":$usefulin2).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($usefulin3==""?"&nbsp;":$usefulin3).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($requireperiod1==""?"&nbsp;":$requireperiod1).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($requireperiod2==""?"&nbsp;":$requireperiod2).'</td>';
		echo '<td style="border-left:none; border-bottom:none;">'.($requireperiod3==""?"&nbsp;":$requireperiod3).'</td>';
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
	$sql_total = mysql_query("SELECT * FROM item WHERE 1") or die(mysql_error());
	$tot_row=mysql_num_rows($sql_total);
	$total_page=0;
	echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input type="button" name="showPage" id="showPage" value="Show:" onclick="paging_itemlist()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($tot_row>$end){
		echo "Page number: ";
		$total_page=ceil($tot_row/$end);
		echo '<select name="page" id="page" onchange="paging_itemlist()" style="vertical-align:middle">';
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
		echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_itemlist()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_itemlist()" />&nbsp;&nbsp;';}
	if($total_page>1 && $pg<$total_page){
		echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_itemlist()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_itemlist()" />';}?>
                    &nbsp;&nbsp;<img src="images/back.gif" onclick="window.location='menu.php'" width="72" height="22"
                        style="display:inline;cursor:hand;" border="0" />
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo date("d-m-Y, h:i:s");?>
</body>

</html>