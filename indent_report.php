<?php
include("menu.php");
include 'db_connect.php';
$lid = $_SESSION['stores_locid'];
// Get the current year and month
$currentYear = date('Y');
$currentMonth = date('m');

// Get the start date of the current month
$MonthStartDate = date('Y-m-01', strtotime("$currentYear-$currentMonth-01"));

// Get the end date of the current month
$MonthEndDate = date('Y-m-t', strtotime("$currentYear-$currentMonth-01"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <script type="text/javascript" src="js/common.js"></script>
</head>

<body>
    <div class="container-fluid">
        
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= $MonthStartDate ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= $MonthEndDate ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="">Location</label>
                            <select name="location" id="location" class="form-control">
                                <option value="">Select Location</option>
                                <?php if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {

                                    $sql_location = mysqli_query($connect, "SELECT * FROM location ORDER BY location_name");
                                    while ($row_location = mysqli_fetch_array($sql_location)) {

                                        echo '<option value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                    }
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="">&nbsp;</label>
                            <button class="btn btn-primary" style="margin-top: 25px;" id="search">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 25px;">
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered" id="myTable" width="100%" style="background:white">
                                <thead>
                                    <tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10px; font-weight:bold; color: #006600; height:20px;">
                                        <th>#</th>
                                        <th>Indent No.</th>
                                        <th>Indent Date</th>
                                        <th>Location</th>
                                        <th>OrderBy</th>
                                        <th>Approval</th>
                                        <th>Appr. Date</th>
                                        <th>Appr. By</th>
                                        <th>PO No.</th>
                                        <th>PO Date</th>
                                        <th>PO Status</th>
                                        <th>DC No</th>
                                        <th>DC Date</th>
                                        <th>Recpt. No.</th>
                                        <th>Recpt. Date</th>
                                        <th>Received By</th>
                                        <th>Received Loc.</th>
                                        <th>Company Name</th>
                                        <th>Party Name</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


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

        let from_date = $("#from_date").val();
        let to_date = $("#to_date").val();
        let location = $("#location").val();
        getIndentList(from_date, to_date, location);

        function getIndentList(from_date, to_date, location) {
            $("#myTable").DataTable({
                destroy: true,
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
                        title: 'Indent Report',
                    },

                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Indent Report',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Indent Report',
                    }
                ],
                "ajax": {
                    url: "indent_report_curd.php",
                    data: {
                        'from_date': from_date,
                        'to_date': to_date,
                        'location': location,
                        'action': 'get_indent_list'
                    },
                    type: 'post',
                }
            })
        }

        $("#search").click(function() {
            let from_date = $("#from_date").val();
            let to_date = $("#to_date").val();
            let location = $("#location").val();
            getIndentList(from_date, to_date, location);
        })
    });
</script>