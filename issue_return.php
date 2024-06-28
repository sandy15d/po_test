<?php include'menu.php';
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
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="<?php echo date('Y-m-d');?>">
                </div>
                <div class="col-md-2">
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="<?php echo date('Y-m-d');?>">
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
                                <th>Issue. No.</th>
                                <th>Date</th>
                                <th style="width:100px;">Location</th>
                                <th>Issue By</th>
                                <th>Issue To</th>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Material Return
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="issue_id" name="issue_id">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped" id="item_list">
                                    <thead>
                                        <th><input type="checkbox" id="select_all"></th>
                                        <th>Plot Name</th>
                                        <th>Item Name</th>

                                        <th>Issue Qty</th>
                                        <th>Prev. Return</th>
                                        <th>Return Qty</th>
                                    </thead>
                                    <tbody id="recordList"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
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
    getList(start_date, end_date);

    function getList(start_date, end_date) {
        $('#mrtable').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'issue_return_curd.php',
                'data': {
                    'action': 'get_mr',
                    'start_date': start_date,
                    'end_date': end_date
                },
                type: 'post'
            },
            paging: true,

            "order": []

        });
        $('#mrtable').DataTable().destroy();
    }

    $(document).on('click', '#search', function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        getList(start_date, end_date);
    });

    //===================================================
    function editMaterial(issue_id = null) {
        if (issue_id) {

            $.ajax({
                url: 'issue_return_curd.php',
                type: 'post',
                data: {
                    'action': 'get_item_list',
                    'issue_id': issue_id
                },
                dataType: 'json',
                success: function(response) {
                    var x = '';
                    $('#issue_id').val(response.data[0].issue_id);
                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.rec_id + '" data-item_id="' + value
                            .item_id + '" data-unit="' + value
                            .unit +
                            '" data-item_category="' + value.item_category +
                            '" type="checkbox" name="return" class="return" ></td>' +
                            '<td>' + value.plot_name + '</td>' +
                            '<td>' + value.item_name + '</td>' +

                            '<td>' + value.issue_qty + ' ' + value.unit_name + '</td>' +
                            '<td>' + value.return_qnty + '</td>' +


                            '<td><input type="text" id="return_qty_' + value.rec_id +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList').html(x);
                } // /success
            });
        } else {
            alert("Error : Refresh the page again");
        }
    }

    $(document).on('click', '#select_all', function() {
        if ($(this).prop('checked') == true) {
            $('.return').prop('checked', true);
        } else {
            $('.return').prop('checked', false);
        }
    });
    //===============---------------========================--------------------=====================//
    $(document).on('click', '#save', function() {
        var list_array = [];
        var issue_id = $('#issue_id').val();
        $('.return').each(function() {
            if ($(this).prop('checked') == true) {
                var rec_id = $(this).attr('id');
                var item_id = $(this).data('item_id');
                var item_category = $(this).data('item_category');
                var unit = $(this).data('unit');
                var return_qty = $('#return_qty_' + rec_id).val();


                list_array.push({
                    'rec_id': rec_id,
                    'item_id': item_id,
                    'item_category': item_category,
                    'unit': unit,
                    'return_qty': return_qty

                });
            }
        });

        $.ajax({
            type: 'POST',
            url: 'issue_return_curd.php',
            data: {
                'list_array': list_array,
                'issue_id': issue_id,
                'action': 'return_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    $('#material_modal').modal('hide');
                    alert('Selectd Item with Return successfully..');
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
    </script>

</body>

</html>