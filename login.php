<?php
session_start();
require_once('config/config.php');
$_SESSION['stores_login'] = false;
$msg = "";
if (isset($_REQUEST['m']) && $_REQUEST['m'] != "") {
	$msg = "You have successfully logged out...";
}
/*-----------------------------*/
if (isset($_POST['submit'])) {
	$userId = addslashes($_POST['userid']);
	
	$pwd_hash = base64_encode($_POST['userpwd']);
	$sql = mysql_query("SELECT * FROM users WHERE user_id='" . $userId . "' AND user_pwd='" . $pwd_hash . "'");
	$row = mysql_fetch_assoc($sql);
	if (mysql_num_rows($sql) == 1) {
		$sqlyear = mysql_query("SELECT * FROM year WHERE year_id=" . $_POST['speriod']);
		$rowyear = mysql_fetch_assoc($sqlyear);
		$syear = date("d-m-Y", strtotime($rowyear['start_year']));
		$eyear = date("d-m-Y", strtotime("31-03-" . substr(date("Y", strtotime($rowyear['start_year'])) + 1, 0, 4)));
		$_SESSION['stores_login'] = true;
		$_SESSION['stores_uid'] = $row["uid"];
		$_SESSION['stores_yid'] = $_POST['speriod'];
		$_SESSION['stores_syr'] = $syear;
		$_SESSION['stores_eyr'] = $eyear;
		$_SESSION['stores_call_from_other'] = false;
		$_SESSION['appr_auth'] = $row["appr_auth"];
		header('Location:ustat.php');
	} else
		$msg = "Invalid username or password...";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Purchase Order</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/common.js"></script>
	<script language="javascript" type="text/javascript">
		function validate_login() {
			var err = "";
			if (document.getElementById("userid").value == "")
				err = "* please input user name!\n";
			if (document.getElementById("userpwd").value == "")
				err += "* please input user password!\n";
			if (document.getElementById("speriod").value == 0)
				err += "* please select/create financial period!\n";
			if (err == "")
				return true;
			else {
				alert("Error: \n" + err);
				return false;
			}
		}
	</script>
</head>


<body onload="document.getElementById('userid').focus()">
	<table align="center" height="500px" width="100%" border="0">
		<tr>
			<td valign="middle">
				<form name="users" method="post" onsubmit="return validate_login()">
					<table align="center" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td valign="top">
								<table class="Header" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
										<td class="th"><strong>User Login</strong></td>
										<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
									</tr>
								</table>

								<table class="Record" cellspacing="0" cellpadding="0">
									<tr class="Controls">
										<td class="th" nowrap>User ID:<span style="color:#FF0000">*</span></td>
										<td><input name="userid" id="userid" maxlength="50" size="30"></td>
									</tr>

									<tr class="Controls">
										<td class="th" nowrap>Password:<span style="color:#FF0000">*</span></td>
										<td><input type="password" name="userpwd" id="userpwd" maxlength="50" size="30"></td>
									</tr>

									<tr class="Controls">
										<td class="th">Period:<span style="color:#FF0000">*</span></td>
										<td><select name="speriod" id="speriod" style="width:150px">
												<?php

												$sql_year = mysql_query("SELECT * FROM year ORDER BY start_year");
												$count = mysql_num_rows($sql_year);
												if ($count == 0) {
													echo '<option value="0">-- Select --</option>';
												}
												$ctr = 0;
												while ($row_year = mysql_fetch_array($sql_year)) {
													$ctr++;
													if ($ctr == $count)
														echo '<option selected value="' . $row_year["year_id"] . '">' . $row_year["period"] . '</option>';
													else
														echo '<option value="' . $row_year["year_id"] . '">' . $row_year["period"] . '</option>';
												} ?>
											</select>
											<?php if ($count == 0) {
												echo '&nbsp;&nbsp;<a href="year.php" target="_blank"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a>';
											} ?>
										</td>
									</tr>

									<tr class="Bottom">
										<td colspan="2" style="text-align:right"><input type="image" src="images/login.jpg"><input type="hidden" name="submit" value="submit" /><input type="button" name="refresh" value=" Refresh " onclick="javascript:window.location='login.php'" /></td>
									</tr>
								</table>
							</td>
							<?php if ($msg != "") {
								echo '<tr align="center"><td style="color:#FF0000; font-weight:bold">' . $msg . '</td></tr>';
							} ?>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</body>

</html>