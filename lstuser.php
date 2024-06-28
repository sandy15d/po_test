<?php
include("menu.php");
/*-----------------------------*/
if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "delete") {
	$res = mysql_query("UPDATE users SET user_status='D' WHERE uid=" . $_REQUEST['sno']);
	echo "<script>location=lstuser.php</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Purchase Order</title>
	<script type="text/javascript" src="js/common.js"></script>
	<script language="javascript" type="text/javascript">
		function paging_user() {
			window.location = "lstuser.php?pg=" + document.getElementById("page").value + "&tr=" + document.getElementById("displayTotalRows").value;
		}

		function firstpage_user() {
			document.getElementById("page").value = 1;
			paging_user();
		}

		function previouspage_user() {
			var cpage = parseInt(document.getElementById("page").value);
			if (cpage > 1) {
				cpage = cpage - 1;
				document.getElementById("page").value = cpage;
			}
			paging_user();
		}

		function nextpage_user() {
			var cpage = parseInt(document.getElementById("page").value);
			if (cpage < parseInt(document.getElementById("totalPage").value)) {
				cpage = cpage + 1;
				document.getElementById("page").value = cpage;
			}
			paging_user();
		}

		function lastpage_user() {
			document.getElementById("page").value = document.getElementById("totalPage").value;
			paging_user();
		}
	</script>
</head>

<body>
	<table align="center" border="0" cellpadding="2" cellspacing="0" width="875px">
		<tr align="center" style="font-family:Times New Roman;font-size:24px;font-weight:normal;color:#007700">
			<td>USER LIST</td>
		</tr>
		<tr>
			<td>
				<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
					<tr bgcolor="#00CCFF" align="center" style="font-family:Times New Roman;font-size:14px; height:24px; font-weight:bold; color:#996600">
						<td width="5%">Sl.No.</td>
						<td width="20%">User Name</td>
						<td width="10%">Type</td>
						<td width="8%">Status</td>
						<td width="15%">Location</td>
						<td width="15%">Reporting</td>
						<td width="8%">Appr.Auth.</td>
						<td width="10%">Action</td>
					</tr>
					<?php
					$start = 0;
					$pg = 1;
					if (isset($_REQUEST['tr']) && $_REQUEST['tr'] != "") {
						$end = $_REQUEST['tr'];
					} else {
						$end = PAGING;
					}
					if (isset($_REQUEST['pg']) && $_REQUEST['pg'] != "") {
						$pg = $_REQUEST['pg'];
						$start = ($pg - 1) * $end;
					}

					$ctr = $start;
					if ($_SESSION['stores_utype'] == "A") {

						$sql = mysql_query("SELECT users.*,location_name FROM users INNER JOIN location ON users.location_id = location.location_id WHERE user_type='U' ORDER BY location_name, user_id limit " . $start . "," . $end) or die(mysql_error());
					} elseif ($_SESSION['stores_utype'] == "S")
						$sql = mysql_query("SELECT users.*,location_name FROM users INNER JOIN location ON users.location_id = location.location_id ORDER BY location_name, user_id limit " . $start . "," . $end) or die(mysql_error());
					$cnt = 1;
					while ($row = mysql_fetch_array($sql)) {
						if ($cnt == 1) {
							echo '<tr bgcolor="#C4FFD7" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
							$cnt = 0;
						} else {
							echo '<tr bgcolor="#CCCCFF" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000">';
							$cnt = 1;
						}
						$ctr++;
						echo '<td align="center">' . $ctr . '</td>';
						echo '<td>' . $row['user_id'] . '</td>';
						if ($row['user_type'] == "S") {
							echo '<td align="center">Super User</td>';
						} elseif ($row['user_type'] == "A") {
							echo '<td align="center">Admin</td>';
						} elseif ($row['user_type'] == "U") {
							echo '<td align="center">User</td>';
						}
						if ($row['user_status'] == "A") {
							echo '<td align="center">Active</td>';
						} elseif ($row['user_status'] == "D") {
							echo '<td align="center">Deactive</td>';
						}
						echo '<td>' . $row['location_name'] . '</td>';

						if ($row['repuser_id'] > 0) {
							$sql2 = mysql_query("select * from users where uid=" . $row['repuser_id']);
							$row2 = mysql_fetch_assoc($sql2);
							echo '<td>' . $row2['user_id'] . '</td>';
						} else {
							echo '<td></td>';
						}


						if ($row['appr_auth'] == 1) {
							echo '<td align="center" style="color:#FF0000">Yes</td>';
						} elseif ($row['appr_auth'] == 0) {
							echo '<td align="center">No</td>';
						}

						$edit_ref = "user.php?action=edit&sno=" . $row['uid'];
						$delete_ref = "lstuser.php?action=delete&sno=" . $row['uid'];
						echo '<td align="center"><a href=' . $edit_ref . '><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href=' . $delete_ref . '><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
						echo '</tr>';
					}

					if ($_SESSION['stores_utype'] == "S")
						$sql = mysql_query("SELECT * FROM users") or die(mysql_error());
					elseif ($_SESSION['stores_utype'] == "A")
						$sql = mysql_query("SELECT * FROM users WHERE user_type='U'") or die(mysql_error());
					$total_row = mysql_num_rows($sql);
					$total_page = 0;
					echo '<tr><td colspan="2" align="left">Total <span style="color:red">' . $total_row . '</span> records</td>';
					echo '<td colspan="4" align="center"><input type="button" name="show" id="show" value="Show:" onclick="paging_user()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="' . $end . '" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					if ($total_row > $end) {
						echo "Page number: ";
						$total_page = ceil($total_row / $end);
						echo '<select name="page" id="page" onchange="paging_user()" style="vertical-align:middle">';
						for ($i = 1; $i <= $total_page; $i++) {
							if (isset($_REQUEST["pg"]) && $_REQUEST["pg"] == $i)
								echo '<option selected value="' . $i . '">' . $i . '</option>';
							else
								echo '<option value="' . $i . '">' . $i . '</option>';
						}
						echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					} else {
						echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					}

					echo '<input type="hidden" name="totalPage" id="totalPage" value="' . $total_page . '" />';
					if ($total_page > 1 && $pg > 1)
						echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_user()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_user()" />&nbsp;&nbsp;';
					if ($total_page > 1 && $pg < $total_page)
						echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_user()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_user()" />';
					echo '</td>';
					?>

					<td align="right"><img src="images/back.gif" style="display:inline; cursor:hand" onclick="javascript:window.location='menu.php'" /></td>
		</tr>
	</table>
	</td>
	</tr>
	</table>
</body>

</html>