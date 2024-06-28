<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['sip2']) && $_POST['sip2']!=""){
	if($_POST['sip2']==2){
		echo '<select name="shippingName" id="shippingName" style="width:300px" onChange="get_combo_n_radio_value()"><option value="0">-- Select --</option>';
		$sql_ship=mysql_query("SELECT * FROM company ORDER BY company_name");
		while($row_ship=mysql_fetch_array($sql_ship))
		{
			if($row_ship["company_id"]==$row["shipping_id"])
				echo '<option selected value="'.$row_ship["company_id"].'">'.$row_ship["company_name"].'</option>';
			else
				echo '<option value="'.$row_ship["company_id"].'">'.$row_ship["company_name"].'</option>';
		}
		echo '</select>';
	} elseif($_POST['sip2']==3){
		echo '<select name="shippingName" id="shippingName" style="width:300px" onChange="get_combo_n_radio_value()"><option value="0">-- Select --</option>';
		$sql_ship=mysql_query("SELECT * FROM party ORDER BY party_name");
		while($row_ship=mysql_fetch_array($sql_ship))
		{
			if($row_ship["party_id"]==$row["shipping_id"])
				echo '<option selected value="'.$row_ship["party_id"].'">'.$row_ship["party_name"].'</option>';
			else
				echo '<option value="'.$row_ship["party_id"].'">'.$row_ship["party_name"].'</option>';
		}
		echo '</select>';
	}
}
?>