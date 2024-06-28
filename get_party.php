<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$bid = $_POST['id'];
	echo '<select name="despatchBy" id="despatchBy" style="width:300px"><option value="0">-- Select --</option>';
	$sqlLeader=mysql_query("SELECT * FROM leader WHERE location_id=".$lid." ORDER BY leader_name");
//	$sql1 = mysql_query(DATABASE2,"SELECT tblbill1.*,party_name,address1,address2,address3,city_name,state_name FROM tblbill1 INNER JOIN tblorder1 ON tblbill1.order_id=tblorder1.order_id INNER JOIN party ON tblorder1.party_id=party.party_id INNER JOIN city ON party.city_id=city.city_id INNER JOIN state ON city.state_id=state.state_id WHERE bill_id=".$bid) or die(mysql_error());
	while($rowLeader=mysql_fetch_array($sqlLeader))
	{
		echo '<option value="'.$rowLeader["leader_id"].'">'.$rowLeader["leader_name"].'</option>';
	}
	echo '</select>';
}
?>