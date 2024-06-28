<link rel="stylesheet" href="css/bootstrap.min.css">
<?php
include 'config/config.php';

$data=mysql_query("select po_date,po_no,party_name,company_name from tblpo join party join company on tblpo.party_id=party.party_id and tblpo.company_id=company.company_id where po_id=".$_REQUEST['po_id']);
$rec=  mysql_fetch_array($data);
?>
<table border='1px' class="table" style="border-collapse: collapse">
    <th>po_date</th>
    <th>po_no</th>
    <th>party_name</th>
    <th>company_name</th>
    <tr>
        <td>
           <?php echo $rec[0]?> 
        </td>
        <td>
           <?php echo $rec[1]?> 
        </td>
        <td>
           <?php echo $rec[2]?> 
        </td>
        <td>
           <?php echo $rec[3]?> 
        </td>
    </tr>
</table>

