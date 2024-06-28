<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['nm']) && $_POST['nm']!=""){
	echo '<select name="matchingUsability" id="matchingUsability" size="10" style="width:265px" >';
	$sqlUse=mysql_query("SELECT * FROM usability WHERE usability_name LIKE '".$_POST['nm']."%' ORDER BY usability_name") or die(mysql_error());
	while($rowUse=mysql_fetch_array($sqlUse))
	{
		echo '<option value="'.$rowUse["usability_id"].'">'.$rowUse["usability_name"].'</option>';
	}
	echo '</select>';
} else {
	echo '';
}
?>