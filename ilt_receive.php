<?php include 'menu.php';
include 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <style>
        .dataTables_filter {
            float: left !important;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        td {
            height: 20px;
        }

        th {
            background: #D7BDE2;
        }


        th,
        td {
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #EBDEF0;
        }

        tr:nth-child(odd) {
            background-color: #E8DAEF;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 0px;
        }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-2 ">
                    <select id="status" name="status" class="form-control">
                        <option value="U" selected>Unreceived Item</option>
                        <option value="R">Received Items</option>
                    </select>
                    <div style="padding:5px;"></div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="search" name="search">Search</button>
                </div>


            </div>
            <div class="row" style="padding-top: 10px;">

                <div class="col-md-1"></div>
                <div class="col-md-10 ">
                    <div class="removeMessages"></div>

                    <table class="table" id="mrtable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>ILT No.</th>
                                <th>Dispatch Date</th>
                                <th>Dispatch From</th>
                                <th>Dispatch To</th>
                                <th>Dispatched By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </section>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="material_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Inter Location Receive
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="receive_date">Receive Date:</label>
                                <input type="date" id="receive_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="receive_by">Received By:</label>
                                <select id="receive_by" class="form-control">
                                    <option value="">----Select----</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <hr style="border-top: 1px dashed red;">
                            <input type="hidden" id="ilt_id" name="ilt_id">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped" id="item_list">
                                    <thead>
                                        <th><input type="checkbox" id="select_all" checked></th>
                                        <th>Item Name</th>
                                        <th>Dispatch Qty</th>
                                        <th>Receive Qty</th>
                                    </thead>
                                    <tbody id="recordList"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal" id="close_modal">Close
                        </button>
                        <button type="button" class="btn btn-primary" name="save" id="save">Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /add modal -->


    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removeMaterialModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Item</h4>
                </div>
                <div class="modal-body">
                    <p>Do you really want to remove ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="removeBtn">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /remove modal -->


    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var status = $('#status').val();
        getList(start_date, end_date, status);

        function getList(start_date, end_date, status) {
            $('#mrtable').DataTable({
                "bLengthChange": true,
                "searching": true,
                destroy: true,
                'ajax': {
                    'url': 'ilt_receive_curd.php',
                    'data': {
                        'action': 'get_ilt',
                        'start_date': start_date,
                        'end_date': end_date,
                        'status': status
                    },
                    type: 'post'
                },
                paging: true,

                "order": []

            });

        }

        $(document).on('click', '#search', function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var status = $('#status').val();
            getList(start_date, end_date, status);
        });
        //=========================================================//
        //===================================================
        function editMaterial(ilt_id = null) {
            if (ilt_id) {

                $.ajax({
                    url: 'ilt_receive_curd.php',
                    type: 'post',
                    data: {
                        'action': 'edit_ilt_receive',
                        'ilt_id': ilt_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        get_staff(ilt_id);
                        var x = '';
                        $('#ilt_id').val(response.data[0].ilt_id);
                        $("#receive_by").val(response.data[0].receive_by);
                        $("#receive_date").val(response.data[0].ilt_date);
                        $.each(response.data, function(key, value) {
                            x = x + '<tr>' +
                                '<td><input id="' + value.rec_id + '" data-item_name="' + value
                                .item_name + '" data-item_id="' + value
                                .item_id + '" data-unit_id="' + value
                                .unit_id + '" data-item_category="' + value
                                .item_category + '" data-seq_no="' + value
                                .seq_no +

                                '" type="checkbox" name="receive" class="receive" checked></td>' +
                                '<td style="width:400px">' + value.item_name + '</td>' +

                                '<td>' + value.qty + ' ' + value.unit_name + '</td>' +

                                '<td style="width:200px"><input type="text" id="receive_qty_' + value
                                .rec_id +
                                '" value="' +
                                value
                                .qty +
                                '" class="form-control"></td>' +
                                '</tr>';
                        });


                        $('#recordList').html(x);

                    }
                });

            } else {
                alert("Error : Refresh the page again");
            }
        }

        //===========================================
        $(document).on('click', '#close_modal', function() {
            $('#material_modal').modal('hide');
            location.reload();
        });

        //================================================

        function get_staff(ilt_id) {
            $.ajax({
                url: 'ilt_receive_curd.php',
                type: 'post',
                data: {
                    'action': 'get_staff',
                    'ilt_id': ilt_id
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    var x = '<option value="">Select Received By</option>';
                    if (response.status == 200) {
                        $.each(response.data, function(key, value) {
                            x = x + '<option value="' + value.staff_id + '">' + value
                                .staff_name +
                                '</option>';
                        });
                    }
                    $('#receive_by').html(x);
                }
            });
        }

        //========================================
        $(document).on('click', '#select_all', function() {
            if ($(this).prop('checked') == true) {
                $('.receive').prop('checked', true);
            } else {
                $('.receive').prop('checked', false);
            }
        });
        //======================================
        $(document).on('click', '#save', function() {

            var list_array = [];
            var ilt_id = $('#ilt_id').val();
            var receive_by = $('#receive_by').val();
            var receive_date = $("#receive_date").val();
            $('.receive').each(function() {
                if ($(this).prop('checked') == true) {
                    var rec_id = $(this).attr('id');
                    var item_id = $(this).data('item_id');
                    var item_category = $(this).data('item_category');
                    var unit = $(this).data('unit_id');
                    var receive_qty = $('#receive_qty_' + rec_id).val();
                    var seq_no = $(this).data('seq_no');

                    list_array.push({
                        'rec_id': rec_id,
                        'item_id': item_id,
                        'item_category': item_category,
                        'seq_no': seq_no,
                        'unit': unit,
                        'receive_qty': receive_qty
                    });
                }
            });

            $.ajax({
                type: 'POST',
                url: 'ilt_receive_curd.php',
                data: {
                    'list_array': list_array,
                    'ilt_id': ilt_id,
                    'receive_by': receive_by,
                    'receive_date': receive_date,
                    'action': 'receive_item'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#material_modal').modal('hide');
                        alert('Selectd Item Received successfully..');
                        location.reload();
                    } else {
                        $('#material_modal').modal('hide');
                        alert('Somthing Went Wrong..Please try again..!!');
                        location.reload();
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        });

        function removeMaterial(ilt_id = null) {
            if (ilt_id) {
                // click on remove button
                $("#removeBtn").unbind('click').bind('click', function() {

                    $.ajax({
                        url: 'ilt_receive_curd.php',
                        type: 'post',
                        data: {
                            'action': 'delete_record',
                            ilt_id: ilt_id
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            if (response.success == true) {
                                $(".removeMessages").html(
                                    '<div class="alert alert-success alert-dismissible" role="alert">' +
                                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                    '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                                    response.messages +
                                    '</div>');


                                $("#removeMaterialModal").modal('hide');
                                getList(start_date, end_date, status);
                            } else {
                                $(".removeMessages").html(
                                    '<div class="alert alert-warning alert-dismissible" role="alert">' +
                                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                    '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                                    response.messages +
                                    '</div>');
                            }
                        }
                    });
                }); // click remove btn
            } else {
                alert('Error: Refresh the page again');
            }
        }
    </script>

</body>

</html>