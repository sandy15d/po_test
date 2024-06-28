<?php 
session_start();
require_once('config/config.php');
if(isset($_REQUEST['uid'])){
	$sql = mysql_query("SELECT users.*, location_name FROM users INNER JOIN location ON users.location_id = location.location_id WHERE uid=".$_REQUEST['uid']) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql)==1){
		$sqlyear = mysql_query("SELECT * FROM year WHERE year_id=".$_REQUEST['yid']);
		$rowyear = mysql_fetch_assoc($sqlyear);
		$syear = date("d-m-Y",strtotime($rowyear['start_year']));
		$eyear = date("d-m-Y",strtotime("31-03-".substr(date("Y",strtotime($rowyear['start_year']))+1,0,4)));
		$_SESSION['stores_login'] = true;
		$_SESSION['stores_uid'] = $_REQUEST['uid'];
		$_SESSION['stores_yid'] = $_REQUEST['yid'];
		$_SESSION['stores_syr'] = $syear;
		$_SESSION['stores_eyr'] = $eyear;
		$_SESSION['stores_call_from_other'] = true;
		
		if($row['user_status']=="A"){
			$_SESSION['stores_uname'] = $row['user_id'];
			$_SESSION['stores_utype'] = $row['user_type'];
			$_SESSION['stores_locid'] = $row['location_id'];
			$_SESSION['stores_lname'] = $row['location_name'];
		}
		header('Location:materialissue.php?action=new');
	} else {
		echo '<script language="javascript">alert("Sorry! you can not access stores.");window.close();</script>';
	}
}
?>