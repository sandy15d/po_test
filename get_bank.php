<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$bid = $_POST['id'];
	if($bid==1){
		echo '<select name="bankName" id="bankName" style="background-color:#E7F0F8; color:#0000FF; width:300px"><option value="0">-- Select --</option>';
	} else {
		echo '<select name="bankName" id="bankName" style="width:300px"><option value="0">-- Select --</option>';
		$sql_bank=mysql_query("SELECT * FROM bank ORDER BY bank_name");
		while($row_bank=mysql_fetch_array($sql_bank))
		{
			echo '<option value="'.$row_bank["bank_id"].'">'.$row_bank["bank_name"].'</option>';
		}
	}
	echo '</select>';
}
?>