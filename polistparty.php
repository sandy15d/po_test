<?php
include("menu.php");
/*--------------------*/
$pid = 0;
$listFor = "U";
if (isset($_REQUEST['pid'])) {
    $pid = $_REQUEST['pid'];
}
if (isset($_REQUEST['rf'])) {
    $listFor = $_REQUEST['rf'];
}
/*--------------------*/
if (isset($_POST['show'])) {
    $sm = strtotime($_POST['dateFrom']);
    $em = strtotime($_POST['dateTo']);
    $pid = $_POST['party'];
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <title>Purchase Order</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link href="css/calendar.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="js/calendar_eu.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script language="javascript" type="text/javascript">
        function validate_dateselection() {
            if (checkdate(document.polist.dateFrom)) {
                if (checkdate(document.polist.dateTo)) {
                    var no_of_days1 = getDaysbetween2Dates(document.polist.dateFrom, document.polist.dateTo);
                    if (no_of_days1 < 0) {
                        alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days2 = getDaysbetween2Dates(document.polist.startYear, document.polist.dateFrom);
                        if (no_of_days2 < 0) {
                            alert("* Report From date wrongly selected. Please correct and submit again.\n");
                            return false;
                        } else {
                            var no_of_days3 = getDaysbetween2Dates(document.polist.dateTo, document.polist.endYear);
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
    <form name="polist" id="polist" method="post" action="polistparty.php" onsubmit="return validate_dateselection()">
        <table align="center" border="0" cellpadding="2" cellspacing="1" width="100%">
            <tbody>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                    <td>Party wise Order List</td>
                </tr>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td>Select Party: <select name="party" id="party" style="width:200px">
                            <option value="0">All Parties</option>
                            <?php
                            $sql_party = mysql_query("SELECT * FROM party ORDER BY party_name");
                            while ($row_party = mysql_fetch_array($sql_party)) {
                                if ($row_party["party_id"] == $pid)
                                    echo '<option selected value="' . $row_party["party_id"] . '">' . $row_party["party_name"] . '</option>';
                                else
                                    echo '<option value="' . $row_party["party_id"] . '">' . $row_party["party_name"] . '</option>';
                            } ?>
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        List For:&nbsp;&nbsp;<select name="listFor" id="listFor" style="width:110px">
                            <?php
                            if ($listFor == "U") {
                                echo '<option selected value="U">Unsent PO</option><option value="S">Sent PO</option>';
                            } elseif ($listFor == "S") {
                                echo '<option value="U">Unsent PO</option><option selected value="S">Sent PO</option>';
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                    <td align="center"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y", $sm); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                            new tcal({
                                "formname": "polist",
                                "controlname": "dateFrom"
                            });
                        </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y", $em); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                            new tcal({
                                "formname": "polist",
                                "controlname": "dateTo"
                            });
                        </script>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show" value="show" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" onclick="window.location='menu.php'" /><input type="image" src="images/print.gif" onclick="funPrint()" /><input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_syr'])); ?>" /><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_eyr'])); ?>" /></td>
                </tr>
            </tbody>
        </table>
    </form>


    <table class="table table-bordered" id="mytable" width="100%" style="background:white">
                 <thead>
                      <tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:20px;">
        <th>Sl. No.</th>
        <th>Party Name</th>
        <th>PO No.</th>
        <th>PO Date</th>
        <th>Order-in-Company</th>
        <th>PO Value</th>
        <th>Expected Delivery</th>
        <th>Order For</th>
        <th>Item Name</th>
        <th>Item Packing Size/Type</th>
        <th>Ordered Qnty.</th>
        <th>Item Value</th>
        <th>Against Indent</th>
        <th>Indent Date</th>
    </thead>
    <tbody>
        <?php
        $ctr = 0;
        $sql = "SELECT
            tblpo.po_id,
            po_date,
            po_no,
            delivery_date,
            party.party_name,
            company.company_name,
            location.location_name,
            item.item_name,
            item_category.category,
            ROUND(tblpo_item.qnty, 2) as qty,
            ROUND(tblpo_item.rate, 2) as rate,
            ROUND((tblpo_item.qnty * tblpo_item.rate), 2) AS amount,
            (SELECT ROUND(total_amount, 2)
            FROM tblpo_dtm dtm
            WHERE dtm.po_id = tblpo.po_id
            ORDER BY dtm.dtm_id DESC
            LIMIT 1) AS total_amount,
            unit.unit_name,
            tbl_indent.indent_no,tbl_indent.ind_prefix,tbl_indent.indent_date
            FROM tblpo
            LEFT JOIN company ON company.company_id = tblpo.company_id
            LEFT JOIN party ON party.party_id = tblpo.party_id
            LEFT JOIN location ON location.location_id = tblpo.delivery_at
            LEFT JOIN tblpo_item ON tblpo_item.po_id = tblpo.po_id
            LEFT JOIN item ON item.item_id = tblpo_item.item_id
            LEFT JOIN item_category ON item_category.category_id = tblpo_item.item_category
            LEFT JOIN unit ON tblpo_item.unit_id = unit.unit_id 
            LEFT JOIN tbl_indent ON tblpo_item.indent_id = tbl_indent.indent_id
            WHERE po_status='" . $listFor . "' AND (po_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "')";
        
        if ($pid != 0) {
            $sql .= " AND tblpo.party_id=" . $pid;
        }
        
        $sql .= " ORDER BY party_name, po_date, tblpo.po_id ";

        $res = mysql_query($sql) or die(mysql_error());
        while ($row = mysql_fetch_array($res)) {
            $ctr++;
            $poNo = str_pad($row['po_no'], 4, '0', STR_PAD_LEFT);
            $indent_number = str_pad($row['indent_no'], 4, '0', STR_PAD_LEFT);
            
            if ($row['ind_prefix'] != null) {
                $indent_number = $row['ind_prefix'] . "/" . $indent_number;
            }
            
            echo '<tr>';
            echo '<td>' . $ctr . '</td>';
            echo '<td>' . $row['party_name'] . '</td>';
            echo '<td>' . $poNo . '</td>';
            echo '<td>' . date("d-m-Y", strtotime($row['po_date'])) . '</td>';
            echo '<td>' . $row['company_name'] . '</td>';
            echo '<td>' . (($row['total_amount'] == NULL || $row['total_amount'] == 0) ? "&nbsp;" : $row['total_amount']) . '</td>';
            echo '<td>' . date("d-m-Y", strtotime($row['delivery_date'])) . '</td>';
            echo '<td>' . $row['location_name'] . '</td>';
            echo '<td>' . $row['seq_no'] . "&nbsp;&nbsp;" . $row['item_name']  . '</td>';
            echo '<td>' .  $row['category'] . '</td>';
            echo '<td>' . ($row['qty'] == 0 ? "&nbsp;" : $row['qty']) . ' '  . ($row['qty'] == 0 ? "&nbsp;" : $row['unit_name']) . '</td>';
            echo '<td>' .  $row['amount'] . '</td>';
            echo '<td>' . $indent_number . '</td>';
            echo '<td>' . date("d-m-Y", strtotime($row['indent_date'])) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>


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
    $(document).ready(function () {
        $('#mytable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'i><'col-sm-4 text-center'l><'col-sm-4'p>>",
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    title: 'Item Stock List',
                },

                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'Item Stock List',
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Item Stock List',
                }
            ]
        });
    });
</script>
</body>

</html>