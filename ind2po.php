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
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
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

    <form name="indentlist" id="indentlist" method="post" action="ind2po.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
            <tbody>
                <tr align="center">
                    <td>
                        <span style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                            Indent v/s Purchase Order Report
                        </span>
                    </td>
                </tr>
                <tr align="center">
                    <td>
                        <?php if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S" || $_SESSION['stores_utype'] == "U") : ?>
                            Select Location:
                            <select name="location" id="location" style="width:200px">
                                <option value="0">All Locations</option>
                                <?php
                                $sql_location = mysql_query("SELECT * FROM location ORDER BY location_name");
                                while ($row_location = mysql_fetch_array($sql_location)) : ?>
                                    <option <?= ($row_location["location_id"] == $lid) ? 'selected' : '' ?> value="<?= $row_location["location_id"] ?>"><?= $row_location["location_name"] ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php elseif ($_SESSION['stores_utype'] == "U") :
                            $chk = mysql_query("SELECT * FROM staff WHERE userid=" . $_SESSION["stores_uid"]);
                            $rowchk = mysql_num_rows($chk);
                            if ($rowchk > 0) : ?>
                                Select Location:
                                <select name="location" id="location" style="width:200px">
                                    <option value="0">All Locations</option>
                                    <?php
                                    $sql_location = mysql_query("SELECT s.location_id, location_name FROM staff s INNER JOIN location l ON s.location_id = l.location_id WHERE userid=" . $_SESSION["stores_uid"] . " ORDER BY location_name");
                                    while ($row_location = mysql_fetch_array($sql_location)) : ?>
                                        <option <?= ($row_location["location_id"] == $lid) ? 'selected' : '' ?> value="<?= $row_location["location_id"] ?>"><?= $row_location["location_name"] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else :
                                $lid = $_SESSION['stores_locid'];
                            ?>
                                Location:
                                <input name="locationName" id="locationName" size="45" readonly="true" value="<?= $_SESSION['stores_lname'] ?>" style="background-color:#E7F0F8; color:#0000FF">
                                <input type="hidden" name="location" id="location" value="<?= $lid ?>" />
                        <?php endif;
                        endif; ?>
                    </td>
                </tr>
                <tr align="center">
                    <td>
                        <span style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">From:</span>
                        <input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?= date("d-m-Y", $sm) ?>">
                        <script language="JavaScript">
                            new tcal({
                                "formname": "indentlist",
                                "controlname": "dateFrom"
                            });
                        </script>
                        <span style="vertical-align:top;">To:</span>
                        <input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?= date("d-m-Y", $em) ?>">
                        <script language="JavaScript">
                            new tcal({
                                "formname": "indentlist",
                                "controlname": "dateTo"
                            });
                        </script>
                        <input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show">
                        <input type="hidden" name="show" value="show" />
                        <input type="hidden" name="startYear" id="startYear" value="<?= date("d-m-Y", strtotime($_SESSION['stores_syr'])) ?>" />
                        <input type="hidden" name="endYear" id="endYear" value="<?= date("d-m-Y", strtotime($_SESSION['stores_eyr'])) ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

<?php 


$sql = "SELECT tbl_indent.indent_date,tbl_indent.supply_date,tbl_indent.indent_no,tbl_indent.ind_prefix, location_name, staff_name,tbl_indent_item.qnty as indent_qnty,
tbl_indent_item.aprvd_qnty,tbl_indent_item.aprvd_status, item.item_name, unit_name,ic.category, party_name, company_name,
tblpo.po_id,tblpo.po_date,tblpo.po_no,tblpo_item.qnty as order_qnty,itemgroup.itgroup_name
FROM tbl_indent 
LEFT JOIN tbl_indent_item ON tbl_indent_item.indent_id = tbl_indent.indent_id
LEFT JOIN location ON tbl_indent.order_from = location.location_id
LEFT JOIN staff ON tbl_indent.order_by = staff.staff_id 
LEFT JOIN item ON tbl_indent_item.item_id = item.item_id 
LEFT JOIN itemgroup ON itemgroup.itgroup_id = item.itgroup_id
LEFT JOIN item_category ic ON ic.category_id = tbl_indent_item.item_category 
LEFT JOIN unit ON item.unit_id = unit.unit_id
LEFT JOIN tblpo ON tblpo.indent_id = tbl_indent.indent_id
LEFT JOIN tblpo_item ON tblpo_item.po_id = tblpo.po_id
LEFT JOIN party ON tblpo.party_id = party.party_id 
LEFT JOIN company ON tblpo.company_id = company.company_id
WHERE tbl_indent.ind_status='S' AND (tbl_indent.indent_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "')";
if ($lid != 0) {
$sql .= " AND tbl_indent.order_from=" . $lid;
}
$sql .= " ORDER BY location_name, tbl_indent.indent_date, tbl_indent.indent_id";
$res = mysql_query($sql) or die(mysql_error());
?>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered" id="mytable" width="100%" style="background:white">
                <thead>
                    <tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:20px;">
                        <td>Sl. No.</td>
                        <td>Indent No.</td>
                        <td>Date</td>
                        <td>Location</td>
                        <td>Order By</td>
                        <td>Required Date</td>
                        <td>Item Group</td>
                        <td>Item Name</td>
                        <td>Item Category</td>
                        <td>Indent Qnty.</td>
                        <td>Approved Qnty.</td>
                        <td>Approval Status </td>
                        <td>Order Qnty.</td>
                        <td>P.O. No.</td>
                        <td>PO Date</td>
                        <td>Party Name </td>
                        <td>Company Name </td>
                        <td>Generate PO </td>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    while ($row = mysql_fetch_array($res)) {
                        $ctr++;
                        $indent_number = ($row['indent_no'] > 999 ? $row['indent_no'] : ($row['indent_no'] > 99 && $row['indent_no'] < 1000 ? "0" . $row['indent_no'] : ($row['indent_no'] > 9 && $row['indent_no'] < 100 ? "00" . $row['indent_no'] : "000" . $row['indent_no'])));
                        if ($row['ind_prefix'] != null) {
                            $indent_number = $row['ind_prefix'] . "/" . $indent_number;
                        }
                        $po_number = ($row['po_no'] > 999 ? $row['po_no'] : ($row['po_no'] > 99 && $row['po_no'] < 1000 ? "0" . $row['po_no'] : ($row['po_no'] > 9 && $row['po_no'] < 100 ? "00" . $row['po_no'] : "000" . $row['po_no'])));

                        echo '<tr>';
                        echo '<td>' . $ctr . '</td>';
                        echo '<td>' . $indent_number . '</td>';
                        echo '<td>' . date("d-m-y", strtotime($row['indent_date'])) . '</td>';
                        echo '<td>' . $row['location_name'] . '</td>';
                        echo '<td>' . $row['staff_name'] . '</td>';
                        echo '<td>' . date("d-m-y", strtotime($row['supply_date'])) . '</td>';
                        echo '<td>' . $row['itgroup_name'] . '</td>';
                        echo '<td>' . $row['item_name'] . '</td>';
                        echo '<td>' . $row['category'] . '</td>';
                        echo '<td>' . $row['indent_qnty'] . '</td>';
                        echo '<td>' . $row['aprvd_qnty'] . '</td>';
                        echo '<td>' . ($row['aprvd_status'] ==1 ?'Approved':'Not Approved') . '</td>';
                        echo '<td>' . $row['order_qnty'] . '</td>';
                        echo '<td>' . $po_number . '</td>';
                        echo '<td>' . date("d-m-y", strtotime($row['po_date'])) . '</td>';
                        echo '<td>' . $row['party_name'] . '</td>';
                        echo '<td>' . $row['company_name'] . '</td>';
                        echo '<td>';
                        if ($row['po_id'] != '') {
                            echo '<a href="#" onclick=window.open("newpurchaseorder.php?po_id=' . $row['po_id'] . '","_blank","scrollbars=yes,resizable=yes,width=800,height=600") style="font-size:12px;" >click</a>';
                        } else {
                            echo '';
                        }
                        echo '</td>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mytable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-4'i><'col-sm-4 text-center'l><'col-sm-4'p>>",
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        title: 'Indent vs Purchase Order List',
                    },

                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Indent vs Purchase Order List',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Indent vs Purchase Order List',
                    }
                ]
            });
        });
    </script>
</body>

</html>