<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['oid']) && $_POST['oid']!=""){
	$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
	$row_user = mysql_fetch_assoc($sql_user);
	echo '<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
	<tr class="Caption">
		<th width="5%">Sl.No.</th>
		<th width="50%">Item Name</th>
		<th width="20%">Quantity</th>
		<th width="15%">Unit</th>
		<th width="5%">Edit</th>
		<th width="5%">Del</th>
	</tr>';
	$i = 0;
	$sql_order = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE indent_id=".$_POST['oid']." ORDER BY seq_no") or die(mysql_error());
	while($row_order=mysql_fetch_array($sql_order)){
		$i++;
		echo '<tr class="Row">';
		echo '<td align="center">'.$i.'.</td><td>'.$row_order['item_name'].'</td><td align="center">'.$row_order['qnty'].'</td><td>'.$row_order['unit_name'].'</td>';
		if($row_order['item_ordered']=="N"){
			if($row_user['oi2']==1)
				echo '<td align="center"><img src="images/edit.gif" style="display:inline;cursor:hand;" onclick="get_indent_item_edit('.$row_order['rec_id'].')"/></td>';
			elseif($row_user['oi2']==0)
				echo '<td align="center">&nbsp;</td>';
			if($row_user['oi3']==1)
				echo '<td align="center"><img src="images/cancel.gif" title="Delete" style="display:inline;cursor:hand;" onclick="get_indent_item_delete('.$row_order['rec_id'].')"></td>';
			elseif($row_user['oi3']==0)
				echo '<td align="center">&nbsp;</td>';
		} elseif($row_order['item_ordered']=="Y"){
			echo '<td align="center">&nbsp;</td>';
			echo '<td align="center">&nbsp;</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}
?>