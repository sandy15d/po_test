<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$lid = $_POST['id'];?>
	<select name="staffName" id="staffName" style="width:300px"><option value="0">-- Select --</option><?php 
	$sqlStaff=mysql_query("SELECT * FROM staff WHERE location_id=".$lid." ORDER BY staff_name");
	while($rowStaff=mysql_fetch_array($sqlStaff))
	{
		echo '<option value="'.$rowStaff["staff_id"].'">'.$rowStaff["staff_name"].'</option>';
	}
	echo '</select>';
}
?>