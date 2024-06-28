<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingParty" id="matchingParty" size="10" style="width:265px" >';
	$sqlParty=mysql_query("SELECT * FROM party WHERE party_name LIKE '".$_POST['nm']."%' ORDER BY party_name") or die(mysql_error());
	while($rowParty=mysql_fetch_array($sqlParty))
	{
		echo '<option value="'.$rowParty["party_id"].'">'.$rowParty["party_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>