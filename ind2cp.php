<?php
include("menu.php");
/*--------------------*/
$lid = 0;
if (isset($_REQUEST['lid'])) {
    $lid = $_REQUEST['lid'];
}
/*--------------------*/
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
/*--------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/calendar_eu.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script language="javascript" type="text/javascript">
        function validate_dateselection() {
            if (checkdate(document.indentlist.dateFrom)) {
                if (checkdate(document.indentlist.dateTo)) {
                    var no_of_days1 = getDaysbetween2Dates(document.indentlist.dateFrom, document.indentlist.dateTo);
                    if (no_of_days1 < 0) {
                        alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days2 = getDaysbetween2Dates(document.indentlist.startYear, document.indentlist.dateFrom);
                        if (no_of_days2 < 0) {
                            alert("* Report From date wrongly selected. Please correct and submit again.\n");
                            return false;
                        } else {
                            var no_of_days3 = getDaysbetween2Dates(document.indentlist.dateTo, document.indentlist.endYear);
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

    <form name="indentlist" id="indentlist" method="post" action="ind2cp.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1175px">
            <tbody>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Indent v/s Cash Purchase Report</td>
                </tr>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td><?php if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S" || $_SESSION['stores_utype'] == "U") {
                            echo 'Select Location: ';
                            echo '<select name="location" id="location" style="width:200px" >';
                            echo '<option value="0">All Locations</option>';
                            $sql_location = mysql_query("SELECT * FROM location ORDER BY location_name");
                            while ($row_location = mysql_fetch_array($sql_location)) {
                                if ($row_location["location_id"] == $lid)
                                    echo '<option selected value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                else
                                    echo '<option value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                            }
                            echo '</select>';
                        } elseif ($_SESSION['stores_utype'] == "U") {
                            $lid = $_SESSION['stores_locid'];
                            echo 'Location: ';
                            echo '<input name="locationName" id="locationName" size="45" readonly="true" value="' . $_SESSION['stores_lname'] . '" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="' . $lid . '" />';
                        } ?>
                    </td>
                </tr>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y", $sm); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                            new tcal({
                                "formname": "indentlist",
                                "controlname": "dateFrom"
                            });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y", $em); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                            new tcal({
                                "formname": "indentlist",
                                "controlname": "dateTo"
                            });
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show" value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" onclick="window.location='menu.php'" /><input type="image" src="images/print.gif" onclick="funPrint()" /><input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_syr'])); ?>" /><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_eyr'])); ?>" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="print_area">

                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </form>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table align="center" border="1" bordercolorlight="#7ECD7A" cellpadding="2" cellspacing="0" width="100%" id="printTable">
                        <thead>
                            <tr>
                                <td>Sl. No.</td>
                                <td>Indent</td>
                                <td>Indent Date </td>
                                <td>Location</td>
                                <td>Order By</td>
                                <td>Required Date</td>
                                <td>Item Name</td>
                                <td>Indent Qnty. </td>
                                <td>Approved Qnty. </td>
                                <td>Approval Status</td>
                                <td>Received Qnty.</td>
                                <td>Memo No.</td>
                                <td>Memo Date</td>
                                <td>Particulars Name</td>
                                <td>Company Name</td>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            $ctr = 0;
                            $sql = "SELECT tbl_indent.*, location_name, staff_name,tbl_indent_item.*, item_name, unit_name,ic.category,tblcash_item.*,  memo_no, memo_date, particulars, company_name FROM tbl_indent 
                                        LEFT JOIN location ON tbl_indent.order_from = location.location_id 
                                        LEFT JOIN staff ON tbl_indent.order_by = staff.staff_id 
                                        LEFT JOIN tbl_indent_item ON tbl_indent_item.indent_id = tbl_indent.indent_id
                                        LEFT JOIN item ON tbl_indent_item.item_id = item.item_id 
                                        LEFT JOIN item_category ic ON ic.category_id = tbl_indent_item.item_category 
                                        LEFT JOIN unit ON item.unit_id = unit.unit_id
                                    
                                        LEFT JOIN tblcash_item ON tblcash_item.indent_id = tbl_indent.indent_id
                                        LEFT JOIN tblcashmemo ON tblcash_item.txn_id = tblcashmemo.txn_id 
                                                LEFT JOIN company ON tblcashmemo.company_id = company.company_id
                                        WHERE ind_status='S' AND (indent_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "')";
                            if ($lid != 0) {
                                $sql .= " AND order_from=" . $lid;
                            }
                            $sql .= " ORDER BY location_name, indent_date, tbl_indent.indent_id";
                            $res = mysql_query($sql) or die(mysql_error());
                            while ($row = mysql_fetch_array($res)) {
                                $ctr++;
                                $indent_number = ($row['indent_no'] > 999 ? $row['indent_no'] : ($row['indent_no'] > 99 && $row['indent_no'] < 1000 ? "0" . $row['indent_no'] : ($row['indent_no'] > 9 && $row['indent_no'] < 100 ? "00" . $row['indent_no'] : "000" . $row['indent_no'])));
                                if ($row['ind_prefix'] != null) {
                                    $indent_number = $row['ind_prefix'] . "/" . $indent_number;
                                }
                            ?>
                                <tr>
                                    <td><?= $ctr ?></td>
                                    <td><?= $indent_number; ?></td>
                                    <td><?= date("d-m-y", strtotime($row['indent_date'])); ?></td>
                                    <td><?= $row['location_name']; ?></td>
                                    <td><?= $row['staff_name']; ?></td>
                                    <td><?= date("d-m-y", strtotime($row['supply_date'])); ?></td>
                                    <td><?= $row['item_name'] . ' ~~' . $row['category']; ?></td>
                                    <td><?= $row['qnty']; ?></td>
                                    <td><?= $row['aprvd_qnty']; ?></td>
                                    <td><?= ($row['aprvd_status'] == 1 ? "Yes" : "No"); ?></td>
                                    <td><?= $row['memo_qnty']; ?></td>

                                    <td><?= $row['memo_no']; ?></td>
                                    <td><?= (!empty($row['memo_date']) ? date("d-m-y", strtotime($row['memo_date'])) : ''); ?>
                                    <td>
                                    <td><?= $row['particulars']; ?></td>
                                    <td><?= $row['company_name']; ?></td>

                                </tr>
                            <?php }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>