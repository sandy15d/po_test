<?php
include("menu.php");
include 'db_connect.php';
/*-------------------------------*/
$lid = $_SESSION['stores_locid'];
if (isset($_REQUEST['lid'])) {
	$lid = $_REQUEST['lid'];
}
if (isset($_POST['show'])) {
	$sm = strtotime($_POST['dateFrom']);
	$em = strtotime($_POST['dateTo']);
	$lid = $_POST['location'];
} elseif (isset($_REQUEST['sm'])) {
	$sm = $_REQUEST['sm'];
	$em = $_REQUEST['em'];
} else {
	$sm = strtotime(date("Y-m-d"));
	$em = strtotime(date("Y-m-d"));
}
/*-------------------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Store Management System</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">

	<script language="javascript" type="text/javascript">
		function validate_dateselection() {
			if (checkdate(document.daybook.dateFrom)) {
				if (checkdate(document.daybook.dateTo)) {
					var no_of_days1 = getDaysbetween2Dates(document.daybook.dateFrom, document.daybook.dateTo);
					if (no_of_days1 < 0) {
						alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
						return false;
					} else {
						var no_of_days2 = getDaysbetween2Dates(document.daybook.startYear, document.daybook.dateFrom);
						if (no_of_days2 < 0) {
							alert("* Report From date wrongly selected. Please correct and submit again.\n");
							return false;
						} else {
							var no_of_days3 = getDaysbetween2Dates(document.daybook.dateTo, document.daybook.endYear);
							if (no_of_days3 < 0) {
								alert("* Report To date wrongly selected. Please correct and submit again.\n");
								return false;
							} else {
								return true;
							}
						}
					}
				}
			}
		}
	</script>
</head>

<body>

	<form name="daybook" id="daybook" method="post" action="daybook.php" onsubmit="return validate_dateselection()">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_syr'])); ?>" />
							<input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_eyr'])); ?>" />
							<input type="hidden" name="show" value="show" />
							<div class="col-md-3">
								<label for="" class="form-label">Location:</label>
								<?php
								if ($_SESSION['stores_utype'] === "A" || $_SESSION['stores_utype'] === "S") {

									echo '<select name="location" id="location" class="form-select">';
									$sql = "SELECT * FROM location ORDER BY location_name asc";
									$result = $connect->query($sql);
									while ($row = $result->fetch_assoc()) {
										$selected = ($row["location_id"] == $lid) ? 'selected' : '';
										echo '<option value="' . $row["location_id"] . '" ' . $selected . '>' . $row["location_name"] . '</option>';
									}
									echo '</select>';
								} elseif ($_SESSION['stores_utype'] === "U") {
									$lid = $locid;
									echo 'Location: ';
									echo '<input name="locationName" id="locationName" class="form-control" readonly="true" value="' . $_SESSION['stores_lname'] . '">';
									echo '<input type="hidden" name="location" id="location" value="' . $lid . '" />';
								}
								?>
							</div>
							<div class="col-md-2">
								<label for="" class="form-label">From Date <span class="text-danger">*</span></label>
								<input type="date" name="dateFrom" id="dateFrom" value="<?php echo date("Y-m-d", $sm); ?>">
							</div>
							<div class="col-md-2">
								<label for="" class="form-label">To Date <span class="text-danger">*</span></label>
								<input type="date" name="dateTo" id="dateTo" value="<?php echo date("Y-m-d", $em); ?>">
							</div>
							<div class="col-md-2">
								<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table" style="width: 100%;">
							<tbody>
								<tr>
									<th>
										Trn.No.</th>
									<th>
										Trn.Date</th>
									<th>
										Trn.Type</th>
									<th>
										Particulars</th>
									<th>
										Item Name</th>
									<th colspan="2">Qty.In</th>
									<th colspan="2">Qty.Out</th>
								</tr>
								<?php

								$sql_stk_rgstr = "SELECT stock_register.*,item_name,unit_name 
								FROM stock_register INNER JOIN item ON stock_register.item_id = item.item_id 
								INNER JOIN unit ON item.unit_id = unit.unit_id WHERE location_id=" . $lid . " 
								AND (entry_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "') ORDER BY entry_date";
								$res = $connect->query($sql_stk_rgstr);

								while ($row_stk_rgstr = $res->fetch_assoc()) {
									$particulars = "&nbsp;";
									$trnType = "";
									$trnNumber = "";
									$inward = 0;
									$outward = 0;
									$itemname = $row_stk_rgstr['item_name'];
									$unitname = $row_stk_rgstr['unit_name'];

									$trnDate = date("d-m-Y", strtotime($row_stk_rgstr['entry_date']));
									if ($row_stk_rgstr['entry_mode'] == "O+") {
										$trnType = "Opng.Stock";
										$inward = $row_stk_rgstr['item_qnty'];
										$particulars = "From: Opening Stock&nbsp;";
									} elseif ($row_stk_rgstr['entry_mode'] == "R+") {
										$trnType = "Mtrl.Rcpt.";
										$inward = $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];
										$sql = "SELECT party_name FROM tblreceipt1 INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id 
										INNER JOIN party ON tblpo.party_id = party.party_id WHERE receipt_id = $entry_id";
										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "From: " . $row['party_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "R-") {
										$trnType = "Rcpt.Rtrn.";
										$outward = 0 - $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];
										$sql = "SELECT party_name FROM tblreceipt_return1 
										INNER JOIN tblreceipt1 ON tblreceipt_return1.receipt_id = tblreceipt1.receipt_id 
										INNER JOIN tblpo ON tblreceipt1.po_id = tblpo.po_id 
										INNER JOIN party ON tblpo.party_id = party.party_id WHERE return_id = $entry_id";
										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? $row['party_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "I+") {
										$trnType = "Mtrl.Issue";
										$outward = 0 - $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];
										$item_id = $row_stk_rgstr['item_id'];

										$sql = "SELECT plot_name FROM tblissue2 INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id = $entry_id AND item_id = $item_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "To: Plot No. " . $row['plot_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "I-") {
										$trnType = "Issue Rtrn.";
										$inward = $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];
										$item_id = $row_stk_rgstr['item_id'];

										$sql = "SELECT plot_name FROM tblissue2 INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id = $entry_id AND item_id = $item_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "From: " . $row['plot_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "T+") {
										$trnType = "ILT Receipt";
										$inward = $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];

										$sql = "SELECT location_name FROM tblilt1 INNER JOIN location ON tblilt1.despatch_from = location.location_id WHERE ilt_id = $entry_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "From: " . $row['location_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "T-") {
										$trnType = "ILT Despatch";
										$outward = 0 - $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];

										$sql = "SELECT location_name FROM tblilt1 INNER JOIN location ON tblilt1.receive_at = location.location_id WHERE ilt_id = $entry_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "To: " . $row['location_name'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "X+") {
										$trnType = "XLT Receipt";
										$inward = $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];

										$sql = "SELECT tfr_location FROM tblxlt WHERE xlt_id = $entry_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "From: " . $row['tfr_location'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "X-") {
										$trnType = "XLT Despatch";
										$outward = 0 - $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];

										$sql = "SELECT tfr_location FROM tblxlt WHERE xlt_id = $entry_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "To: " . $row['tfr_location'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "C+") {
										$trnType = "Cash Pur.";
										$inward = $row_stk_rgstr['item_qnty'];
										$entry_id = $row_stk_rgstr['entry_id'];

										$sql = "SELECT particulars FROM tblcashmemo WHERE txn_id = $entry_id";

										$res_sql = $connect->query($sql);
										$row = $res_sql->fetch_assoc();
										$particulars = ($res_sql->num_rows > 0 ? "From: " . $row['particulars'] : "&nbsp;");
									} elseif ($row_stk_rgstr['entry_mode'] == "P+") {
										$trnType = "Physical Stock";
										$inward = $row_stk_rgstr['item_qnty'];
										$particulars = "Physical Verification&nbsp;";
									} elseif ($row_stk_rgstr['entry_mode'] == "P-") {
										$trnType = "Physical Stock";
										$outward = 0 - $row_stk_rgstr['item_qnty'];
										$particulars = "Physical Verification&nbsp;";
									}

									$trnNumber = ($row_stk_rgstr['entry_id'] > 999 ? $row_stk_rgstr['entry_id'] : ($row_stk_rgstr['entry_id'] > 99 && $row_stk_rgstr['entry_id'] < 1000 ? "0" . $row_stk_rgstr['entry_id'] : ($row_stk_rgstr['entry_id'] > 9 && $row_stk_rgstr['entry_id'] < 100 ? "00" . $row_stk_rgstr['entry_id'] : "000" . $row_stk_rgstr['entry_id'])));
								?>
									<tr>
										<td><?= $trnNumber; ?></td>
										<td><?= $trnDate; ?></td>
										<td><?= $trnType; ?></td>
										<td><?= $particulars; ?></td>
										<td><?= $itemname; ?></td>
										<td><?= ($inward == 0 ? "&nbsp;" : $inward); ?></td>
										<td><?= ($inward == 0 ? "" : $unitname); ?></td>
										<td><?= ($outward == 0 ? "&nbsp;" : $outward); ?></td>
										<td>&nbsp;<?= ($outward == 0 ? "&nbsp;" : $unitname); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</form>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>

</html>