<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*-----------------------------*/
$sql = mysql_query("SELECT users.*, location_name FROM users INNER JOIN location ON users.location_id = location.location_id WHERE users.uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
if($row["user_status"]=="A"){
	$_SESSION['stores_uname'] = $row["user_id"];
	$_SESSION['stores_utype'] = $row["user_type"];
	$_SESSION['stores_locid'] = $row["location_id"];
	$_SESSION['stores_lname'] = $row["location_name"];
}
/*-----------------------------*/
if(isset($_POST['submit'])){
	if($_POST['userstatus']=="Deactive")
		header("location: login.php");
	elseif($_POST['userstatus']=="Active")
		header("location: dashboard.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body onload="document.getElementById('btnNext').focus();">
<form name="userstatus" method="post">
<table align="center" height="500px" width="100%" border="0">
<tr>
	<td valign="middle">
		<table align="center" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top">
			<table class="Header" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
				<td class="th"><strong>Logged User Status</strong></td>
				<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
			</tr>
			</table>
			
			<table class="Record" cellspacing="0" cellpadding="0">
			<tr class="Controls">
				<td class="th" nowrap>User ID:</td>
				<td><input name="userid" id="userid" maxlength="50" size="30" readonly="true" value="<?php echo $row["user_id"];?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			</tr>
 			
			<tr class="Controls">
				<td class="th" nowrap>Password:</td>
				<td><input type="password" name="userpwd" id="userpwd" maxlength="50" size="30" readonly="true" value="<?php echo $row["user_pwd"];?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			</tr>
 			
			<tr class="Controls">
				<td class="th" nowrap>User Type:</td>
				<td><input name="usertype" id="usertype" maxlength="50" size="30" readonly="true" value="<?php if($row["user_type"]=="S"){ echo "Super User";} elseif($row["user_type"]=="A"){ echo "Admin";} elseif($row["user_type"]=="U"){ echo "User";} ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			</tr>
 			
			<tr class="Controls">
				<td class="th" nowrap>User Status:</td>
				<td><input name="userstatus" id="userstatus" maxlength="50" size="30" readonly="true" value="<?php if($row["user_status"]=="A"){echo "Active";} elseif($row["user_status"]=="D"){ echo "Deactive";} else {echo "";}?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			</tr>
 			
			<tr class="Controls">
				<td class="th" nowrap>User Location:</td>
				<td><input name="userlocation" id="userlocation" maxlength="50" size="30" readonly="true" value="<?php echo $row["location_name"];?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			</tr>
 			
			<tr class="Bottom">
				<td align="left" colspan="2"><input type="image" name="btnNext" id="btnNext" src="images/next.gif" alt="next"><input type="hidden" name="submit" value="submit"/></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</form>
</body>
</html>