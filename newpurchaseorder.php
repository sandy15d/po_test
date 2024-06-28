<?php include"config/config.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <script>
    function FunPrint(id) {
        window.print();
    }
    </script>
    <style>
    section {
        font-size: 12px;
    }

    table {
        font-size: 12px;
    }
    </style>
</head>

<body>
    <?php /*
	<input type="button" onClick="funPrint()" value="print">
	<input type="button" onClick="window.close()" value="Close">
*/ ?>
    <?php
$datapo=mysql_query("select tblpo.*,ti.ind_prefix from tblpo JOIN tbl_indent ti on ti.indent_id = tblpo.indent_id where tblpo.po_id=".$_REQUEST['po_id']);
$recpo=  mysql_fetch_array($datapo);
$dataParty=mysql_query('select * from party where party_id='.$recpo['party_id']);
$recParty=  mysql_fetch_array($dataParty);
?>
    <?php $dataCompany=mysql_query('select * from company where company_id='.$recpo['company_id']); $recCompany=  mysql_fetch_array($dataCompany); 
$data2Company=mysql_query('select * from company where company_id='.$recpo['shipping_id']); $rec2Company=  mysql_fetch_array($data2Company); 
?>
    <table width="100%" id="tblPrint" style="align:center" border="0">
        <tr>
            <td width="55%" valign="top">
                <table border="0">
                    <tr>
                        <td style="width:5px;"></td>
                        <td><?php if($recCompany['CCode']=='VSPL'){?><img src="images/vnr_logo.png"><?php } ?></td>
                    </tr>
                    <tr>
                        <td style="width:5px;"></td>
                        <td>
                            <font style="font-size:18px;font-family:Times New Roman;">
                                <b><?php echo $recCompany['company_name'] ?></b>
                            </font><br>
                            <font style="font-size:14px;font-family:Times New Roman;">
                                <?php echo $recCompany['c_address1']; ?>, <?php echo $recCompany['c_address2']; ?><br />
                                <?php echo $recCompany['c_address3']; ?><br><br>
                                CIN No - &nbsp;<?php echo strtoupper($recCompany['c_cin']); ?><br>
                                GST No - &nbsp;<?php echo strtoupper($recCompany['c_gst']); ?><br>
                                PAN No - &nbsp;<?php echo strtoupper($recCompany['c_pan']); ?>
                            </font>
                            <p>

                                <font style="font-size:14px;font-family:Times New Roman;"><b>VENDOR
                                        :&nbsp;<?php echo $recParty['party_name']; ?></b></font><br>
                                <font style="font-size:14px;font-family:Times New Roman;">
                                    <?php echo $recParty['address1'].' '.$recParty['address2']; ?><br />
                                    <?php echo $recParty['address3']; ?><br>
                                    <?php $dataCity=mysql_query("select city_name from city where city_id=".$recParty['city_id']); $recCity=mysql_fetch_array($dataCity); ?>
                                    <?php echo strtoupper($recCity['city_name']); ?><br>
                                    Phone No-<?php echo $recParty['mobile_no']; ?><br>
                                </font>
                        </td>
                    </tr>
                </table>
            </td>
            <?php $y2=date('y',strtotime($recpo['po_date'])); $y2_o=$y2-1; $y2_n=$y2+1;
        $Y=date('Y',strtotime($recpo['po_date'])); $Y_o=$Y-1; $Y_n=$Y+1; 
        $m=date('m',strtotime($recpo['po_date']));
		if($m==01 OR $m==02 OR $m==03){$yer=$Y_o.'-'.$y2;}
		else{$yer=$Y.'-'.$y2_n;} 

                $len=strlen($recpo['po_no']);
		if($len==1){$pon='000'.$recpo['po_no'];}
		else if($len==2){$pon='00'.$recpo['po_no'];}
		else if($len==3){$pon='0'.$recpo['po_no'];}
		else {$pon=$recpo['po_no'];}


?>


            <td width="45%" valign="top">
                <table border="0" style="font-size:14px;font-family:Times New Roman;">
                    <?php if($recpo['work_order']=='N'){ $Tit='PURCHASE ORDER'; $Tn='P'; }else{ $Tit='WORK ORDER'; $Tn='W'; } ?>
                    <tr>
                        <td colspan="3">
                            <font style="font-size:24px;"><b><?php echo $Tit; ?></b></font>
                            <!--"000".$recpo['po_no']."/-->
                            &nbsp;&nbsp;&nbsp;&nbsp;<a href="#"
                                onClick="FunPrint(<?php echo $_REQUEST['po_id']; ?>)">print</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;"><b><?php echo $Tn; ?>.O. NO</b></td>
                        <td>:</td>
                        <td><b><?php echo $recCompany['CCode'].'/'.$recpo['ind_prefix'].'/'.$pon."/".$yer; ?></b></td>
                    </tr>
                    <tr>
                        <td style="width:100px;"><b>DATE</b></td>
                        <td>:</td>
                        <td><b><?php echo date('d-m-Y',strtotime($recpo['po_date'])) ?></b></td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="width:100px;">VENDOR ID</td>
                        <td>:</td>
                        <td><?php echo $recpo['party_id']; ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;">VENDOR REF</td>
                        <td>:</td>
                        <td><?php echo ''; ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;">Vendor's GST</td>
                        <td>:</td>
                        <td><?php echo strtoupper($recParty['gstno']); ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;">Vendor's PAN</td>
                        <td>:</td>
                        <td><?php echo strtoupper($recParty['pan']); ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;">No</td>
                        <td>:</td>
                        <td></td>
                    </tr>

                    <?php $data2City=mysql_query("select city_name from city where city_id=".$rec2Company['c_cityid']); $rec2City=mysql_fetch_array($data2City); ?>
                    <tr>
                        <td style="width:100px;" valign="top"><b>SHIP TO</b></td>
                        <td valign="top">:</td>
                        <td valign="top"><b><?php echo $rec2Company['company_name']; ?></b></td>
                    </tr>
                    <tr>
                        <td style="width:100px;" colspan="2"></td>
                        <td><?php echo $rec2Company['c_address1'].', '.$rec2Company['c_address2']; ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;" colspan="2"></td>
                        <td><?php echo $rec2Company['c_address3']; ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;" colspan="2"></td>
                        <td><?php echo strtoupper($rec2City['city_name']); ?></td>
                    </tr>
                    <tr>
                        <td style="width:100px;" colspan="2"></td>
                        <td>Phone No-<?php echo $rec2Company['c_phone']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>

        <?php $schk=mysql_query("select pi.indent_id from tblpo_item pi inner join tbl_indent_item ii on pi.indent_id=ii.indent_id where ii.AnyOther!='' AND pi.po_id=".$recpo['po_id']); $rrow=mysql_num_rows($schk); $rchk=mysql_fetch_assoc($schk); ?>

        <tr>
            <td colspan='2'>
                <table width='100%' border="1px" style="border-collapse:collapse;text-align:center;font-size:11px;">
                    <tr style="background-color:#DCDCBA;">
                        <th colspan="<?php if($rrow>0){echo 3;}else{echo 2;}?>">SHIPPING METHOD</th>
                        <th colspan="2">SHIPPING TERMS</th>
                        <th colspan="2">DELIVERY DATE</th>
                    </tr>
                    <tr style="background-color:#FFFFFF;font-size:12px;">
                        <td colspan="2"><?php if($recpo["ship_method"]) echo $recpo['ship_method']; else echo"Nil"?>
                        </td>
                        <td colspan="2"><?php if($recpo['ship_terms']) echo $recpo['ship_terms']; else echo "Nil"?></td>
                        <td colspan="2">
                            <b><?php if($recpo['delivery_date']) echo date("d-m-Y",strtotime($recpo['delivery_date'])); else echo "Immediately"; ?></b>
                        </td>
                    </tr>
                    <tr style="background-color:#DCDCBA;">
                        <th style="width:40px;">QNo</th>
                        <th style="width:120px;">ITEM NAME</th>
                        <?php if($rrow>0){ ?><th style="width:200px;">DESCRIPTION</th><?php } ?>
                        <th style="width:300px;">REMARK</th>
                        <th style="width:60px;">QTY</th>
                        <th style="width:60px;">UNIT PRICE</th>
                        <th style="width:80px;">LINE TOTAL</th>
                    </tr>
                    <?php $i=0; $dataItem=mysql_query("select * from tblpo_item where po_id=".$recpo['po_id']); $sum=0; 
                    $seq =1;
                    while($recItem=  mysql_fetch_array($dataItem)){
                        
 echo "<tr>";
  echo "<td align='center'>".++$i."</td>";
  $dataItemName=mysql_query("select item_name,u.unit_name from item JOIN unit u ON u.unit_id = item.unit_id where item_id=".$recItem['item_id']); $recItemName=  mysql_fetch_array($dataItemName);
  
  
  
  $getUnitData = mysql_query("SELECT unit_name from unit WHERE unit_id =".$recItem['unit_id']);
  
  $unitName = mysql_fetch_assoc($getUnitData);
  
  $dataItemCategory=mysql_query("select * from item_category WHERE category_id=".$recItem['item_category']); $recItemCategory=  mysql_fetch_array($dataItemCategory);
  echo "<td align='left'>".$recItemName['item_name'].' ~~'.$recItemCategory['category']."</td>";

  if($rrow>0){
     
      $sqOth=mysql_query("select AnyOther from tbl_indent_item where indent_id=".$rchk['indent_id']." AND item_id=".$recItem['item_id']." AND seq_no =$seq"); $rqOth=mysql_fetch_assoc($sqOth);  echo "<td align='left'>".$rqOth['AnyOther']."</td>"; }

  echo "<td align='left'>".$recItem['item_description']."</td>";
  echo "<td align='right'>".floatval($recItem['qnty']).' '.  $unitName['unit_name'].'  '."</td>";
  echo "<td align='right'><b>".floatval($recItem['rate'])."&nbsp;</b></td>";
  echo "<td align='right'>".number_format(($recItem['qnty']*$recItem['rate']),2)."&nbsp;</td>";
 echo "</tr>";
  $sum+=$recItem['qnty']*$recItem['rate'];
  $seq++;
}
?>
                    <tr>
                        <td colspan="<?php if($rrow>0){echo 4;}else{echo 3;}?>" rowspan="10"
                            style="border-left:hidden;">
                            <table width="100%" style="font-size:12px;">
                                <?php $spec=mysql_query("select * from tblpo_spec where  po_id=".$_REQUEST["po_id"]); while($rec=mysql_fetch_array($spec)){
 echo '<tr><td align="center" style="width:2px;">*</td><td align="left">&nbsp;'.$rec["specification"].'</td></tr>'; } ?>
                            </table>
                        </td>
                        <td colspan="2" align="right" style="font-size:11px;background-color:#DCDCBA;"><b>SUM
                                AMOUNT&nbsp;<b></td>
                        <td align="right" style="font-size:12px;"><b><?php echo number_format($sum,2); ?></b>&nbsp;</td>
                    </tr>
                    <?php $dataDtm=mysql_query("select * from tblpo_dtm where po_id=".$recpo['po_id']); $rown=mysql_num_rows($dataDtm);
      if(mysql_num_rows($dataDtm)){ $sumDtm=0; $i=1; while($recDtm=  mysql_fetch_array($dataDtm)){ ?>

                    <tr>
                        <td colspan="2" align="right" style="font-size:11px;background-color:#DCDCBA;">
                            <b><?php echo $recDtm['dtm_id']; if($recDtm['dtm_percent']>0){ echo ' ('.$recDtm['dtm_percent'].'%)'; }?>&nbsp;<b>
                        </td>
                        <td align="right" style="font-size:12px;">
                            <?php if($recDtm['calc']=='M') echo "- ".number_format($recDtm['dtm_amount'],2); else echo number_format($recDtm['dtm_amount'],2); ?>&nbsp;
                        </td>
                    </tr>
                    <?php if($rown==$i){ ?>
                    <tr>
                        <td colspan="2" align="right" style="font-size:12px;background-color:#DCDCBA;"><b>TOTAL NET
                                AMOUNT&nbsp;<b></td>
                        <td align="right" style="font-size:12px;">
                            <b><?php echo number_format($recDtm['total_amount'],2); ?></b>&nbsp;
                        </td>
                    </tr>
                    <?php } $i++; } } ?>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="height:5px;"></td>
        </tr>
        <tr>
            <td colspan="3" valign="top" width="100%">
                <table border="0">
                    <tr>
                        <td valign="top" width="75%">
                            <b>Terms & Condition</b>
                            <section>
                                <?php $strnew=$recpo['terms_condition'];                     //for($i=1;$i<=9;$i++){ $strnew=str_replace("$i.","<br>$i.",$strnew );   }
     echo "<pre style='font-size:11px'>".$strnew."</pre>"; ?>
                            </section>
                        </td>
                        <td style="width:300px;bottom:10px;" align="center" valign="bottom">

                            <b style="font-size:15px;">Authorized By</b><br>
                            <?php $dataOrderBy=mysql_query("select staff_name,post_name from staff join designation on staff.post_id=designation.post_id join tbl_indent on tbl_indent.order_by=staff.staff_id join tblpo_item on tbl_indent.indent_id=tblpo_item.indent_id where po_id=".$_REQUEST['po_id']); $recOrderBy=  mysql_fetch_array($dataOrderBy); ?>
                            <font style="font-size:15px;"><?php echo $recOrderBy[0]; ?><br>
                                <?php echo '('.$recOrderBy[1].')'; ?></font>
                            <p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
<p>This Order is computer generated and no  signature is required.</p>
</body>

</html>