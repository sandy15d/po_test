<?php
include 'menu.php';
include 'db_connect.php';

$startDate = date("Y-m-d", strtotime($_SESSION['stores_syr']));
$endDate = date("Y-m-d", strtotime($_SESSION['stores_eyr']));


$po_generated = "SELECT COUNT(*) as total FROM tblpo
WHERE po_status = 'S' AND (po_date BETWEEN '$startDate' AND '$endDate')";
$po = $connect->query($po_generated);
$totalPO = $po->fetch_assoc()['total'];


$pending_indent_approval = "SELECT COUNT(*) as total FROM tbl_indent WHERE ind_status='S' AND appr_status='U' AND  (indent_date BETWEEN '$startDate' AND '$endDate')";
$indent = $connect->query($pending_indent_approval);
$totalPenIndent = $indent->fetch_assoc()["total"];

/* $pending_po = "SELECT COUNT(DISTINCT(tbl_indent.indent_id)) as total FROM tbl_indent 
LEFT JOIN tbl_indent_item ON tbl_indent_item.indent_id = tbl_indent.indent_id
WHERE appr_status='S' AND  indent_id
                                 IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1) AND  (tbl_indent.indent_date BETWEEN '$startDate' AND '$endDate')";

$pen_po = $connect->query($pending_po);
$totalPendingPO = $pen_po->fetch_assoc()['total']; */
?>
<html>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">

    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }

        .align-items-center {
            align-items: center !important;
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1.25rem;
        }

        .shadow {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15) !important;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow  py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Pending for PO Generation</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPendingPO;?></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Pending for Indent Approval</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPenIndent; ?></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total PO Generated</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPO; ?></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>