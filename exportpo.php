<?php
session_start();
require_once('config/config.php');
date_default_timezone_set('Asia/Calcutta');

$xls_filename = 'PO_Reports'.$_REQUEST['df'].'_to_'.$_REQUEST['dt'].'.xls';
 

header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=$xls_filename");
header("Pragma: no-cache");
header("Expires: 0");
$sep = "\t"; 
echo "Sn\tItem Name\tPO No\tDate\tParty Name\tOrder-in-Company\tLocation\tDelivery Date\tOrdered Qnty\tItem Value\tAgainst Indent\tIndent Date\tIndent Order By";
print("\n");

  $sql = "SELECT tblpo.*, tblpo_item.*, party_name, company_name, location_name, item_name, unit_name, indent_no, ind_prefix, indent_date, order_by,item_category.category FROM tblpo_item INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id INNER JOIN party ON tblpo.party_id = party.party_id INNER JOIN company ON tblpo.company_id = company.company_id INNER JOIN location ON tblpo.delivery_at = location.location_id INNER JOIN item ON tblpo_item.item_id = item.item_id INNER JOIN unit ON tblpo_item.unit_id = unit.unit_id INNER JOIN item_category ON item_category.category_id = tblpo_item.item_category INNER JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id WHERE po_status='".$_REQUEST['lf']."' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_REQUEST['df']))."' AND '".date("Y-m-d",strtotime($_REQUEST['dt']))."')";
  if($_REQUEST['prd']!=0){ $sql .= " AND tblpo_item.item_id=".$_REQUEST['prd']; }
  $sql .= " ORDER BY item_name, po_date, tblpo_item.po_id";
 
  $res = mysql_query($sql) or die(mysql_error());  
  $no=1;
  while($row=mysql_fetch_array($res))
  {
  
  //$sCrBy=mysql_query("select user_id from users where uid=".$row['order_by']); $rCrBy=mysql_fetch_assoc($sCrBy);
  $sCrBy=mysql_query("select staff_name from staff where staff_id=".$row['order_by']); $rCrBy=mysql_fetch_assoc($sCrBy);
  
  $poNo = ($row['po_no']>999 ? $row['po_no'] : ($row['po_no']>99 && $row['po_no']<1000 ? "0".$row['po_no'] : ($row['po_no']>9 && $row['po_no']<100 ? "00".$row['po_no'] : "000".$row['po_no'])));
  $indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
  if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
  $itemvalue = number_format($row['qnty'] * $row['rate'],2,'.','');
  
 
  $schema_insert = "";
  $schema_insert .= $no.$sep;
  $schema_insert .= $row['item_name'].' ~~ '.$row['category'].$sep;
  $schema_insert .= $poNo.$sep;
  $schema_insert .= $row['po_date'].$sep;
  $schema_insert .= $row['party_name'].$sep;		
  $schema_insert .= $row['company_name'].$sep;
  $schema_insert .= $row['location_name'].$sep;
  $schema_insert .= $row['delivery_date'].$sep;
  $schema_insert .= ($row['qnty']==0?"&nbsp;":$row['qnty']).' '.($row['qnty']==0?"&nbsp;":$row['unit_name']).$sep;
  $schema_insert .= ($itemvalue==0?"&nbsp;":$itemvalue).$sep;	
  $schema_insert .= $indent_number.$sep;	
  $schema_insert .= $row['indent_date'].$sep;	
  $schema_insert .= ucwords($rCrBy['staff_name']).$sep;	
  		  
  $schema_insert = str_replace($sep."$", "", $schema_insert);
  $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
  $schema_insert .= "\t";
  print(trim($schema_insert));
  print "\n";
  $no++;
  
  }

?>

