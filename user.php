<?php
include("menu.php");
/*-----------------------------*/
$msg = "";
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == "edit") {
		$userid = $_REQUEST['sno'];
		$title = "Edit User Profile";
	}
	if ($_REQUEST["action"] == "change") {
		$userid = $_SESSION['stores_uid'];
		$title = "Change User Profile";
	}
	if ($_REQUEST["action"] == "new") {
		$userid = "";
		$title = "New User Creation";
	}
}
/*-----------------------------*/
if (isset($_REQUEST["action"])) {
	if ($_REQUEST["action"] == "edit" || $_REQUEST["action"] == "change") {
		$sql = mysql_query("SELECT users.*,location_name FROM users INNER JOIN location ON users.location_id = location.location_id WHERE users.uid=" . $userid);
		$row = mysql_fetch_assoc($sql);
	}
}
/*-----------------------------*/
if (isset($_POST['submit'])) {
	$user_id = addslashes($_POST['userId']);
	$preSalt = "AKA";
	$postSalt = "AAA";
	$pwd_hash = md5($preSalt . md5(addslashes($_POST['userPwd']) . $postSalt));
	/*-----------------------------*/
	$sql = mysql_query("SELECT uid FROM users WHERE user_id='" . $user_id . "'") or die(mysql_error());
	$row_user = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-----------------------------*/
	if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "edit" || $_REQUEST["action"] == "change")) {
		if ($count > 0) {
			//if($row_user['uid']!=$_SESSION['stores_uid'])
			//$msg = "Duplication Error! can&prime;t update user profile.";
			//elseif($row_user['uid']==$_SESSION['stores_uid']){
			if ($_SESSION['stores_utype'] == "U") {
				$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_pwd='" . $pwd_hash . "' WHERE uid=" . $userid) or die(mysql_error());
				echo '<script>window.location="menu.php";</script>';
				//					header("Location: menu.php");
			} elseif ($_SESSION['stores_utype'] == "S" || $_SESSION['stores_utype'] == "A") {
				$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_type='" . $_POST['userType'] . "',user_status='" . $_POST['userStatus'] . "',location_id=" . $_POST['userLocation'] . ", repuser_id=" . $_POST['repuser_id'] . ", repuser2_id=" . $_POST['repuser2_id'] . ", repuser3_id=" . $_POST['repuser3_id'] . ", repuser4_id=" . $_POST['repuser4_id'] . ", repuser5_id=" . $_POST['repuser5_id'] . " WHERE uid=" . $userid) or die(mysql_error());
				if ($_POST['userPwd'] != '') {
					$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_pwd='" . $pwd_hash . "',email_id='" . $_POST['emailId'] . "' WHERE uid=" . $userid) or die(mysql_error());
				}

				//					if($_REQUEST["action"]=="edit"){header("Location: lstuser.php");} elseif($_REQUEST["action"]=="change"){header("Location: login.php");}
				if ($_REQUEST["action"] == "edit") {
					echo '<script>window.location="lstuser.php";</script>';
				} elseif ($_REQUEST["action"] == "change") {
					echo '<script>window.location="login.php";</script>';
				}
			}
			//}
		} elseif ($count == 0) {
			if ($_SESSION['stores_utype'] == "U") {
				$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_pwd='" . $pwd_hash . "' WHERE uid=" . $userid) or die(mysql_error());
				echo '<script>window.location="menu.php";</script>';
			} elseif ($_SESSION['stores_utype'] == "S" || $_SESSION['stores_utype'] == "A") {

				$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_type='" . $_POST['userType'] . "',email_id='" . $_POST['emailId'] . "',user_status='" . $_POST['userStatus'] . "',location_id=" . $_POST['userLocation'] . ", repuser_id=" . $_POST['repuser_id'] . ", repuser2_id=" . $_POST['repuser2_id'] . ", repuser3_id=" . $_POST['repuser3_id'] . ", repuser4_id=" . $_POST['repuser4_id'] . ", repuser5_id=" . $_POST['repuser5_id'] . " WHERE uid=" . $userid) or die(mysql_error());
				if ($_POST['userPwd'] != '') {
					$res = mysql_query("UPDATE users SET user_id='" . $user_id . "',user_pwd='" . $pwd_hash . "' WHERE uid=" . $userid) or die(mysql_error());
				}

				if ($_REQUEST["action"] == "edit") {
					echo '<script>window.location="lstuser.php";</script>';
				} elseif ($_REQUEST["action"] == "change") {
					echo '<script>window.location="login.php";</script>';
				}
			}
		}
	} elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "new") {
		if ($count > 0)
			$msg = "Duplication Error! can&prime;t insert user profile.";
		else {
			$sql = mysql_query("SELECT Max(uid) as maxid FROM users");
			$row = mysql_fetch_assoc($sql);
			$_SESSION['stores_uid'] = $row["maxid"] + 1;
			$sql = "INSERT INTO users (uid,user_id,user_pwd,user_type,user_status,location_id,repuser_id,aid,email_id) VALUES(" . $_SESSION['stores_uid'] . ",'" . $user_id . "','" . $pwd_hash . "','" . $_POST['userType'] . "','" . $_POST['userStatus'] . "'," . $_POST['userLocation'] . "," . $_POST['repuser_id'] . "," . $_SESSION["stores_uid"] . ",'" . $_POST['emailId'] . "')";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script>window.location="user.php?action=new";</script>';
		}
	}
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
		function validate_user() {
			var err = "";
			if (document.getElementById("userId").value == "")
				err = "* please input user name!\n";
			if (document.getElementById("userPwd").value == "")
				err += "* please input user password!\n";
			if (document.getElementById("userRepwd").value == "")
				err += "* please input user confirm password!\n";
			if (document.getElementById("userPwd").value != document.getElementById("userRepwd").value)
				err += "* user password and confirm password mismatch!\n";
			if (document.getElementById("userType").value == "S")
				err += "* please select user's type!\n";
			if (document.getElementById("userStatus").value == "S")
				err += "* please select user's status!\n";
			if (document.getElementById("userLocation").value == 0)
				err += "* please select user's location!\n";
			if (err == "")
				return true;
			else {
				alert("Error: \n" + err);
				return false;
			}
		}
	</script>
</head>


<body>
	<table align="center" height="300px" width="400px" border="0">
		<tr>
			<td valign="middle">
				<form name="users" method="post" onsubmit="return validate_user()">
					<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td valign="top">
								<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="HeaderLeft"><img src="spacer.gif" border="0" alt=""></td>
										<td class="th"><strong><?php echo $title; ?></strong></td>
										<td class="HeaderRight"><img src="spacer.gif" border="0" alt=""></td>
									</tr>
								</table>

								<table class="Record" width="100%" cellspacing="0" cellpadding="0">
									<tr class="Controls">
										<td class="th" nowrap>User ID :<span style="color:#FF0000">*</span></td>
										<td><input name="userId" id="userId" maxlength="50" size="30" value="<?php if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "edit" || $_REQUEST["action"] == "change")) {
																													echo $row["user_id"];
																												} ?>"></td>
									</tr>

									<tr class="Controls">
										<td class="th" nowrap>Password :<span style="color:#FF0000">*</span></td>
										<td><input type="password" name="userPwd" id="userPwd" maxlength="50" size="30" value=""></td>
									</tr>

									<tr class="Controls">
										<td class="th" nowrap>Retype Password :<span style="color:#FF0000">*</span></td>
										<td><input type="password" name="userRepwd" id="useRepwd" maxlength="50" size="30" value=""></td>
									</tr>
									<tr class="Controls">
										<td class="th" nowrap>Email:<span style="color:#FF0000">*</span></td>
										<td><input type="email" name="emailId" id="emailId" value="<?php if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "edit" || $_REQUEST["action"] == "change")) {
																										echo $row["email_id"];
																									} ?>"></td>
									</tr>
									<?php if ($_SESSION['stores_utype'] == "U") { ?>
										<tr class="Controls">
											<td class="th" nowrap>User Type :</td>
											<td><input name="userType1" id="userType1" maxlength="50" size="30" readonly="true" value="User" style="background-color:#f7f7f7"><input type="hidden" name="userType" id="userType" value="U"></td>
										</tr>

										<tr class="Controls">
											<td class="th" nowrap>User Status :</td>
											<?php if ($row['user_status'] == "A") { ?>
												<td><input name="userStatus1" id="userStatus1" maxlength="50" size="30" readonly="true" value="Active" style="background-color:#dfdfdf"><input type="hidden" name="userStatus" id="userStatus" value="A"></td>
											<?php } elseif ($row['user_status'] == "D") { ?>
												<td><input name="userStatus1" id="userStatus1" maxlength="50" size="30" readonly="true" value="Deactive" style="background-color:#dfdfdf"><input type="hidden" name="userStatus" id="userStatus" value="D"></td>
											<?php } ?>
										</tr>

										<tr class="Controls">
											<td class="th" nowrap>User Location :</td>
											<td><input name="userLocation1" id="userLocation1" maxlength="50" size="30" readonly="true" value="<?php echo $row['location_name']; ?>" style="background-color:#e7f0f8"><input type="hidden" name="userLocation" id="userLocation" value="<?php echo $row['location_id']; ?>"></td>
										</tr>
										<?php } elseif ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {
										if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "change") { ?>
											<tr class="Controls">
												<td class="th" nowrap>User Type :</td>
												<?php if ($row['user_type'] == "S") { ?>
													<td><input name="userType1" id="userType1" maxlength="50" size="30" readonly="true" value="Super User" style="background-color:#f7f7f7"><input type="hidden" name="userType" id="userType" value="S"></td>
												<?php } elseif ($row['user_type'] == "A") { ?>
													<td><input name="userType1" id="userType1" maxlength="50" size="30" readonly="true" value="Admin" style="background-color:#f7f7f7"><input type="hidden" name="userType" id="userType" value="A"></td>
												<?php } elseif ($row['user_type'] == "U") { ?>
													<td><input name="userType1" id="userType1" maxlength="50" size="30" readonly="true" value="User" style="background-color:#f7f7f7"><input type="hidden" name="userType" id="userType" value="U"></td>
												<?php } ?>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Status :</td>
												<?php if ($row['user_status'] == "A") { ?>
													<td><input name="userStatus1" id="userStatus1" maxlength="50" size="30" readonly="true" value="Active" style="background-color:#dfdfdf"><input type="hidden" name="userStatus" id="userStatus" value="A"></td>
												<?php } elseif ($row['user_status'] == "D") { ?>
													<td><input name="userStatus1" id="userStatus1" maxlength="50" size="30" readonly="true" value="Deactive" style="background-color:#dfdfdf"><input type="hidden" name="userStatus" id="userStatus" value="D"></td>
												<?php } ?>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Location :</td>
												<td><input name="userLocation1" id="userLocation1" maxlength="50" size="30" readonly="true" value="<?php echo $row['location_name']; ?>" style="background-color:#e7f0f8"><input type="hidden" name="userLocation" id="userLocation" value="<?php echo $row['location_id']; ?>"></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-1 :</td>
												<td><select name="repuser_id" id="repuser_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep = mysql_fetch_array($sql_rep)) {
															echo '<option value="' . $row_rep['uid'] . '">' . $row_rep['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-2 :</td>
												<td><select name="repuser2_id" id="repuser2_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep2 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep2 = mysql_fetch_array($sql_rep2)) {
															echo '<option value="' . $row_rep2['uid'] . '">' . $row_rep2['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-3 :</td>
												<td><select name="repuser3_id" id="repuser3_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep3 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep3 = mysql_fetch_array($sql_rep3)) {
															echo '<option value="' . $row_rep3['uid'] . '">' . $row_rep3['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-4 :</td>
												<td><select name="repuser4_id" id="repuser4_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep4 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep4 = mysql_fetch_array($sql_rep4)) {
															echo '<option value="' . $row_rep4['uid'] . '">' . $row_rep4['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-5 :</td>
												<td><select name="repuser5_id" id="repuser5_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep5 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep5 = mysql_fetch_array($sql_rep5)) {
															echo '<option value="' . $row_rep5['uid'] . '">' . $row_rep5['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>



										<?php } elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "edit") { ?>
											<tr class="Controls">
												<td class="th" nowrap>User Type :<span style="color:#FF0000">*</span></td>
												<td><select name="userType" id="userType" style="width:150px">
														<option value="S">Select Type</option>
														<?php if ($row["user_type"] == "A") { ?>
															<option selected value="A">Admin</option>
															<option value="U">User</option>
														<?php } elseif ($row["user_type"] == "U") { ?>
															<option value="A">Admin</option>
															<option selected value="U">User</option>
														<?php } ?>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Status :<span style="color:#FF0000">*</span></td>
												<td><select name="userStatus" id="userStatus" style="width:150px">
														<option value="S">Select Status</option>
														<?php if ($row["user_status"] == "A") { ?>
															<option selected value="A">Active</option>
															<option value="D">Deactive</option>
														<?php } elseif ($row["user_status"] == "D") { ?>
															<option value="A">Active</option>
															<option selected value="D">Deactive</option>
														<?php } ?>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Location :<span style="color:#FF0000">*</span></td>
												<td><select name="userLocation" id="userLocation" style="width:150px">
														<option value="0">Select Location</option>
														<?php $sql_location = mysql_query("SELECT * FROM location ORDER BY location_name");
														while ($row_location = mysql_fetch_array($sql_location)) {
															if ($row["location_id"] == $row_location["location_id"])
																echo '<option selected value="' . $row_location['location_id'] . '">' . $row_location['location_name'] . '</option>';
															else
																echo '<option value="' . $row_location['location_id'] . '">' . $row_location['location_name'] . '</option>';
														} ?>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-1 :</td>
												<td><select name="repuser_id" id="repuser_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep = mysql_fetch_array($sql_rep)) {
															if ($row["repuser_id"] == $row_rep["uid"])
																echo '<option selected value="' . $row['repuser_id'] . '">' . $row_rep['user_id'] . '</option>';
															else
																echo '<option value="' . $row_rep['uid'] . '">' . $row_rep['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-2 :</td>
												<td><select name="repuser2_id" id="repuser2_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep2 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep2 = mysql_fetch_array($sql_rep2)) {
															if ($row["repuser2_id"] == $row_rep2["uid"])
																echo '<option selected value="' . $row['repuser2_id'] . '">' . $row_rep2['user_id'] . '</option>';
															else
																echo '<option value="' . $row_rep2['uid'] . '">' . $row_rep2['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-3 :</td>
												<td><select name="repuser3_id" id="repuser3_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep3 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep3 = mysql_fetch_array($sql_rep3)) {
															if ($row["repuser3_id"] == $row_rep3["uid"])
																echo '<option selected value="' . $row['repuser3_id'] . '">' . $row_rep3['user_id'] . '</option>';
															else
																echo '<option value="' . $row_rep3['uid'] . '">' . $row_rep3['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-4 :</td>
												<td><select name="repuser4_id" id="repuser4_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep4 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep4 = mysql_fetch_array($sql_rep4)) {
															if ($row["repuser4_id"] == $row_rep4["uid"])
																echo '<option selected value="' . $row['repuser4_id'] . '">' . $row_rep4['user_id'] . '</option>';
															else
																echo '<option value="' . $row_rep4['uid'] . '">' . $row_rep4['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-5 :</td>
												<td><select name="repuser5_id" id="repuser5_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep5 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep5 = mysql_fetch_array($sql_rep5)) {
															if ($row["repuser5_id"] == $row_rep5["uid"])
																echo '<option selected value="' . $row['repuser5_id'] . '">' . $row_rep5['user_id'] . '</option>';
															else
																echo '<option value="' . $row_rep5['uid'] . '">' . $row_rep5['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>


										<?php } elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "new") { ?>
											<tr class="Controls">
												<td class="th" nowrap>User Type :<span style="color:#FF0000">*</span></td>
												<td><select name="userType" id="userType" style="width:150px">
														<option value="S">Select Type</option>
														<option value="A">Admin</option>
														<option value="U">User</option>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Status :<span style="color:#FF0000">*</span></td>
												<td><select name="userStatus" id="userStatus" style="width:150px">
														<option value="S">Select Status</option>
														<option value="A">Active</option>
														<option value="D">Deactive</option>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Location :<span style="color:#FF0000">*</span></td>
												<td><select name="userLocation" id="userLocation" style="width:200px">
														<option value="0">Select Location</option>
														<?php $sql_location = mysql_query("SELECT * FROM location ORDER BY location_name");
														while ($row_location = mysql_fetch_array($sql_location)) {
															echo '<option value="' . $row_location['location_id'] . '">' . $row_location['location_name'] . '</option>';
														} ?>
													</select>
												</td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-1 :</td>
												<td><select name="repuser_id" id="repuser_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep = mysql_fetch_array($sql_rep)) {
															echo '<option value="' . $row_rep['uid'] . '">' . $row_rep['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-2 :</td>
												<td><select name="repuser2_id" id="repuser2_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep2 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep2 = mysql_fetch_array($sql_rep2)) {
															echo '<option value="' . $row_rep2['uid'] . '">' . $row_rep2['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-3 :</td>
												<td><select name="repuser3_id" id="repuser3_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep3 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep3 = mysql_fetch_array($sql_rep3)) {
															echo '<option value="' . $row_rep3['uid'] . '">' . $row_rep3['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-4 :</td>
												<td><select name="repuser4_id" id="repuser4_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep4 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep4 = mysql_fetch_array($sql_rep4)) {
															echo '<option value="' . $row_rep4['uid'] . '">' . $row_rep4['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>

											<tr class="Controls">
												<td class="th" nowrap>User Reporting-5 :</td>
												<td><select name="repuser5_id" id="repuser5_id" style="width:150px">
														<option value="0">Select Reporting</option>
														<?php $sql_rep5 = mysql_query("SELECT * FROM users ORDER BY user_id");
														while ($row_rep5 = mysql_fetch_array($sql_rep5)) {
															echo '<option value="' . $row_rep5['uid'] . '">' . $row_rep5['user_id'] . '</option>';
														} ?>
													</select></td>
											</tr>


									<?php }
									}

									if ($msg != "") {
										echo '<tr class="Controls"><td colspan="2" align="center" style="color:#FF0000; font-weight:bold">' . $msg . '</td></tr>';
									} ?>

									<tr class="Bottom">
										<td align="left" colspan="2"><input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update" />&nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</body>

</html>