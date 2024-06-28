<?php include 'menu.php';
include 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <style>
        table,
        th,
        td {
            border: 1px solid black;
        }

        th {
            background: #D7BDE2;
        }


        th {
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #EBDEF0;
        }

        tr:nth-child(odd) {
            background-color: #E8DAEF;
        }


        /* Center the loader */
        #loader {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 9999;
            width: 120px;
            height: 120px;
            margin: -76px 0 0 -76px;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            text-align: center;
            /* Center text horizontally */
        }

        #loader::before {
            content: 'Loading...';
            /* Your desired text */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #000;
            /* Text color, change as needed */
            font-weight: bold;
            /* Adjust font weight as needed */
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Add animation to "page content" */
        .animate-bottom {
            position: relative;
            -webkit-animation-name: animatebottom;
            -webkit-animation-duration: 1s;
            animation-name: animatebottom;
            animation-duration: 1s;
        }

        @-webkit-keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0;
            }

            to {
                bottom: 0px;
                opacity: 1;
            }
        }

        @keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0;
            }

            to {
                bottom: 0;
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">
            <div class="row">
                <input type="hidden" id="uid" name="uid" value="<?php echo $_SESSION['stores_uid']; ?>">
                <div class="col-md-1"></div>
                <div class="col-md-10 " id="table1">
                    <div class="right-box">
                        <center>
                            <h4>Order Indent List</h4>
                        </center>
                        <hr>
                    </div>
                    <table id="indent_list" class="table">
                        <thead>
                            <tr>
                                <th style="width:30px">S.no</th>
                                <th style="width:100px">Indent No.</th>
                                <th>Date</th>
                                <th>Exp. Supply</th>
                                <th>Indent From</th>
                                <th>Order By</th>
                                <th>Quotation</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" style="text-align:center">
                                    <h4>No Record Found, Plese Create Indent First, Then add Item.</h4>
                                </td>
                            </tr>
                            <h3></h3>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>
    <div id="loader" style="display: none;"></div>
    <div class="modal fade" tabindex="-1" role="dialog" id="viewIndentModal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Indent Item Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="indent_id" name="indent_id">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped" id="item_list">
                                <thead>
                                    <th><input type="checkbox" id="select_all" checked></th>
                                    <th>Item Name</th>
                                    <th>Item Description</th>
                                    <th>Indent Qty</th>
                                    <th>Unit</th>
                                    <th>Approved Qty</th>
                                </thead>
                                <tbody id="recordList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="removeBtn">Approve Indnet</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        var uid = $('#uid').val();

        IndentTable = $("#indent_list").DataTable({
            "ajax": {
                url: "indent_approval_curd.php",
                data: {
                    action: 'indent_list',
                    uid: uid
                },
                type: 'post'
            },
            "oLanguage": {
                "sEmptyTable": "No Record Found..."
            },


            "order": []
        });


        //------------------------------View Indent Item List-------
        //---------------------------------------------------------
        function getRecord(indent_id) {
            $.ajax({
                type: 'POST',
                url: 'indent_approval_curd.php',
                data: {
                    'indent_id': indent_id,
                    'action': 'get_item_list'

                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    var x = '';
                    $('#indent_id').val(response.data[0].indent_id);
                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.rec_id + '" data-item_name="' + value.item_name +
                            '" data-desc="' + value.desc + '" data-qty="' + value.qty +
                            '" type="checkbox" name="approved" class="approved" checked></td>' +
                            '<td>' + value.item_name + '</td>' +
                            '<td>' + value.desc + '</td>' +
                            '<td>' + value.qty + '</td>' +
                            '<td>' + value.unit_name + '</td>' +
                            '<td><input type="text" id="appr_qty_' + value.rec_id + '" value="' + value
                            .qty +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList').html(x);
                }
            });
        }

        $(document).on('click', '#select_all', function() {
            if ($(this).prop('checked') == true) {
                $('.approved').prop('checked', true);
            } else {
                $('.approved').prop('checked', false);
            }
        });

        $(document).on('click', '#removeBtn', function() {
            var list_array = [];
            var indent_id = $('#indent_id').val();
            $('.approved').each(function() {
                if ($(this).prop('checked') == true) {
                    var rec_id = $(this).attr('id');
                    var appr_qty = $('#appr_qty_' + rec_id).val();

                    list_array.push({
                        'rec_id': rec_id,
                        'appr_qty': appr_qty

                    });
                }
            });
            // console.log(list_array);
            $.ajax({
                type: 'POST',
                url: 'indent_approval_curd.php',
                data: {
                    'list_array': list_array,
                    'indent_id': indent_id,
                    'action': 'approve_indent'
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#viewIndentModal').modal('hide');
                    //show loader
                    $("#loader").css('display', 'block');
                },
                success: function(response) {
                    $("#loader").css('display', 'none');
                    if (response.success == true) {
                        $('#viewIndentModal').modal('hide');
                        alert('Indent with Select Option Approved Successfully..');
                        location.reload();
                    } else {
                        $('#viewIndentModal').modal('hide');
                        alert('Somthing Went Wrong..Please try again..!!');
                        location.reload();
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        });
    </script>
</body>

</html>