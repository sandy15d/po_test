<?php
include("menu.php");
/*--------------------*/
$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=" . $_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------*/
$lid = 0;
$listFor = "U";
if (isset($_REQUEST['lid'])) {
    $lid = $_REQUEST['lid'];
}
if (isset($_REQUEST['rf'])) {
    $listFor = $_REQUEST['rf'];
}
/*--------------------*/
if (isset($_POST['show'])) {
    $sm = strtotime($_POST['dateFrom']);
    $em = strtotime($_POST['dateTo']);
    $lid = $_POST['location'];
    $listFor = $_POST['listFor'];
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
        function ConfirmDelete(me) {
            var intent = confirm('Do you really want to delete this record?');
            if (intent) {
                window.location = me;
            }
        }

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

    <form name="indentlist" id="indentlist" method="post" action="indlist.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="1000px">
            <tbody>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Indent List</td>
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
                            echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            echo 'List For:&nbsp;&nbsp;<select name="listFor" id="listFor" style="width:110px">';
                            if ($listFor == "U") {
                                echo '<option selected value="U">Unsent Indent</option><option value="S">Sent Indent</option>';
                            } elseif ($listFor == "S") {
                                echo '<option value="U">Unsent Indent</option><option selected value="S">Sent Indent</option>';
                            }
                            echo '</select>';
                        } elseif ($_SESSION['stores_utype'] == "U") {
                            $lid = $_SESSION['stores_locid'];
                            echo 'Location: ';
                            echo '<input name="locationName" id="locationName" size="45" readonly="true" value="' . $_SESSION['stores_lname'] . '" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="location" id="location" value="' . $lid . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            echo 'List For:&nbsp;&nbsp;<input name="listType" id="listType" readonly="true" value="Unsent Indent" style="background-color:#E7F0F8; color:#0000FF" /><input type="hidden" name="listFor" id="listFor" value="U" />';
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
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show">
                        <input type="hidden" name="show" value="show" />&nbsp;&nbsp;

                        <input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_syr'])); ?>" /><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_eyr'])); ?>" />
                    </td>
                </tr>


            </tbody>
        </table>
    </form>

    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-bordered" id="mytable" width="100%" style="background:white">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Indent No.</th>
                        <th>Indent Date</th>
                        <th>Location</th>
                        <th>Order By</th>
                        <th>Required Date</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Indent Qnty.</th>
                        <th>Approved Qnty.</th>
                        <th>Approval Status</th>
                        <th>Quotation</th>
                        <th><?php if ($listFor == "U") {
                                echo 'Edit';
                            } elseif ($listFor == "S") {
                                echo 'Recall';
                            } ?></th>
                        <th>Delete</th>


                    </tr>
                </thead>
                <tbody>
                    <?php

                    $ctr = 1;
                    $sql = "SELECT 
                    tbl_indent.indent_id,tbl_indent.indent_no,tbl_indent.indent_date,tbl_indent.supply_date,
                    tbl_indent.ind_prefix,tbl_indent.ind_status,tbl_indent.appr_status,tbl_indent.appr_date,tbl_indent.attachment,
                    location_name, ic.category, u.unit_name, 
                    staff_name, 
                    i.item_name,ti.qnty,ti.aprvd_qnty 
                FROM 
                    tbl_indent 
                    LEFT JOIN location ON tbl_indent.order_from = location.location_id 
                    LEFT JOIN staff ON tbl_indent.order_by = staff.staff_id 
                    LEFT JOIN tbl_indent_item ti ON ti.indent_id = tbl_indent.indent_id 
                    LEFT JOIN item i ON i.item_id = ti.item_id 
                    LEFT JOIN unit u ON u.unit_id = i.unit_id 
                    LEFT JOIN item_category ic ON ic.category_id = ti.item_category 
                    WHERE tbl_indent.ind_status='" . $listFor . "' 
                    AND (tbl_indent.indent_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "')";
                    if ($lid != 0) {
                        $sql .= " AND tbl_indent.order_from=" . $lid;
                    }
                    $sql .= " ORDER BY location_name, indent_date, indent_id";
                    $res = mysql_query($sql) or die(mysql_error());
                    $previous_indent_id = null;
                    while ($row = mysql_fetch_array($res)) {
                        $indent_number = ($row['indent_no'] > 999 ? $row['indent_no'] : ($row['indent_no'] > 99 && $row['indent_no'] < 1000 ? "0" . $row['indent_no'] : ($row['indent_no'] > 9 && $row['indent_no'] < 100 ? "00" . $row['indent_no'] : "000" . $row['indent_no'])));
                        if ($row['ind_prefix'] != null) {
                            $indent_number = $row['ind_prefix'] . "/" . $indent_number;
                        }
                    ?>
                        <tr>
                            <td><?php echo $ctr++; ?></td>
                            <td>
                                <?php
                                if ($row['indent_id'] != $previous_indent_id) {
                                    echo $indent_number;
                                }
                                ?>
                            </td>
                            <td><?= date("d-m-y", strtotime($row['indent_date'])); ?></td>
                            <td><?= $row['location_name']; ?></td>
                            <td><?= $row['staff_name']; ?></td>
                            <td><?= date("d-m-y", strtotime($row['supply_date'])); ?></td>
                            <td><?= $row['item_name']; ?></td>
                            <td><?= $row['category']; ?></td>
                            <td><?= $row['qnty'] . ' ' . $row['unit_name']; ?></td>
                            <td><?= $row['aprvd_qnty'] . ' ' . $row['unit_name']; ?></td>
                            <td><?= ($row['appr_status'] == 'S' ? 'Approved' : 'Not Approved'); ?></td>
                            <td>
                                <?php
                                if ($row['indent_id'] != $previous_indent_id) {
                                    echo '<a href="uploads/' . $row['attachment'] . '" target="_blank" download style="font-size:13px;">' . $row['attachment'] . '</a>';
                                }
                                ?>
                            </td>


                            <td>
                                <?php

                                if ($row['ind_status'] == 'S') {
                                    $recall_ref = "indlist1.php?xn=R&oid=" . $row['indent_id'] . "&lid=" . $lid . "&rf=" . $listFor . "&sm=" . $sm . "&em=" . $em;
                                    if ($row['appr_status'] != 'S') {
                                        if ($row['indent_id'] != $previous_indent_id) {
                                            echo '<img src="images/undo.gif" style="display:inline;cursor:hand;" border="0" title="Recall" onclick="window.location=\'' . $recall_ref . '\'" />';
                                        }
                                    }
                                } elseif ($row['ind_status'] == 'U') {
                                    $edit_ref = "order_indent_edit.php?oid=" . $row['indent_id'];
                                    if ($row['indent_id'] != $previous_indent_id) {
                                        echo '<img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" title="Edit" onclick="window.location=\'' . $edit_ref . '\'" />';
                                    }
                                }

                                ?>

                            </td>


                            <td class="text-center">
                                <?php

                                $check_po = "SELECT * FROM tblpo WHERE indent_id=" . $row['indent_id'] . "";
                                $chk = mysql_query($check_po) or die(mysql_error());

                                $delete_ref = "indlist1.php?xn=D&oid=" . $row['indent_id'] . "&lid=" . $lid . "&rf=" . $listFor . "&sm=" . $sm . "&em=" . $em;
                                if ($row['indent_id'] != $previous_indent_id) {
                                    if (mysql_num_rows($chk) <= 0) {
                                ?>
                                        <img src="images/cancel.gif" style="display:inline;cursor:pointer;" border="0" title="Delete Indent" onclick="ConfirmDelete('<?php echo $delete_ref; ?>')" />
                                <?php  }
                                } ?>
                            </td>

                        </tr>
                    <?php
                        $previous_indent_id = $row['indent_id'];
                    } ?>
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

        function ConfirmDelete(me) {
            var intent = confirm('Do you really want to delete this record ?');
            if (intent) {
                window.location = me;
            }
        }
    </script>
</body>

</html>