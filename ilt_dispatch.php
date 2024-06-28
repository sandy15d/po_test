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
                <div class="col-md-2 ">
                    <select id="status" name="status" class="form-control">
                        <option value="U" selected>Unsent Items</option>
                        <option value="S">Sent Items</option>
                    </select>
                    <div style="padding:5px;"></div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="search" name="search">Search</button>
                </div>

                <div class="col-md-2 ">
                    <button class="btn btn-default pull pull-right " data-toggle="modal" data-target="#material_modal">
                        <span class="glyphicon glyphicon-plus-sign"></span> ILT Dispatch</button>
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
                                <th>ILT Date</th>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Inter Location Transfer
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="ilt_id" name="ilt_id">
                                <label for="ilt_no">ILT No.:</label>
                                <input type="text" id="ilt_no" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="ilt_date">ILT Date:</label>
                                <input type="date" id="ilt_date" class="form-control"
                                    value="<?php echo date('Y-m-d');?>">
                            </div>
                            <div class="col-md-3">
                                <label for="dispatch">Dispatch From:</label>
                                <select id="dispatch" class="form-control">
                                    <option value="">Select Location</option>
                                    <?php
                                            $sql= "SELECT * FROM location ORDER BY location_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                            }
                                        ?>
                                </select>
                                <div id="dispatch_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="destination">Destination:</label>
                                <select id="destination" class="form-control">
                                    <option value="">Select Location</option>
                                    <?php
                                            $sql= "SELECT * FROM location ORDER BY location_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                            }
                                        ?>
                                </select>
                                <div id="destination_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="dispatch_by">Dispatched By:</label>
                                <select id="dispatch_by" class="form-control">
                                    <option value="">--Select--</option>
                                </select>
                                <div id="dispatch_by_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="dispatch_mode">Dispatch Mode:</label>
                                <select id="dispatch_mode" class="form-control">
                                    <option value="">Select</option>
                                    <option value="1">Hand Delivery</option>
                                    <option value="2">By Vehicle</option>
                                </select>
                                <div id="dispatch_mode_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4" id="vehicle_div" style="display:none">
                                <label for="vehicle_no">Vehicle No.:</label>
                                <input type="text" id="vehicle_no" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="next-phase" style="display:none;">
                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-12">
                                <table class="table" id="get_item_list" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.no</th>
                                            <th style="width:300px;">Item Name</th>
                                            <th>Dispatch Qty</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>

                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-3">
                                <input type="hidden" id="rec_id">
                                <label for="item">Item:</label>
                                <select id="item" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                            $sql ="SELECT * FROM item ORDER BY item_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value='.$row['item_id'].'>'.$row['item_name'].'</option>';
                                            }
                                        ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="item_category">Item Category:</label>
                                <select id="item_category" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="unit">Unit:</label>
                                <input type="hidden" id="unit">
                                <input type="text" id="unit_name" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="stock">Cur Stock:</label>
                                <input type="text" id="stock" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="qty">Dispatch Qty:</label>
                                <input type="text" id="qty" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal"
                            id="close_modal">Close</button>
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
    var status = $('#status').val();
    getList(start_date, end_date, status);

    function getList(start_date, end_date, status) {
        $('#mrtable').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'ilt_dispatch_curd.php',
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
        $('#mrtable').DataTable().destroy();
    }

    $(document).on('click', '#search', function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var status = $('#status').val();
        getList(start_date, end_date, status);
    });



    //-===----====--------=====---------====------------------------
    $(document).on('change', '#dispatch', function() {
        var location = $(this).val();
        $.ajax({
            url: 'order_indent_curd.php',
            type: 'post',
            data: {
                action: 'get_staff',
                location: location,
            },
            dataType: 'json',
            success: function(response) {
                var x = '<option value="">Select Dispatched By</option>';
                if (response.status == 200) {
                    $.each(response.data, function(key, value) {
                        x = x + '<option value="' + value.staff_id + '">' + value
                            .staff_name +
                            '</option>';
                    });
                }
                $('#dispatch_by').html(x);
            }
        });
    });

    //==================================================//
    $(document).on('change', '#dispatch_mode', function() {
        var d_mode = $(this).val();
        if (d_mode == 1) {
            $('#vehicle_div').css('display', 'none');
        } else {
            $('#vehicle_div').css('display', 'block');
        }
    });
    //================================//
    $(document).on("click", "#save", function() {
        var ilt_id = $('#ilt_id').val();
        var ilt_date = $('#ilt_date').val();
        var dispatch = $('#dispatch').val();
        var destination = $('#destination').val();
        var dispatch_by = $('#dispatch_by').val();
        var dispatch_mode = $('#dispatch_mode').val();
        var vehicle_no = $('#vehicle_no').val();

        var formvalid = true;
        if (dispatch == "") {
            $("#dispatch_err").html("Dispatch From is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#dispatch_err").css("display", "none");
        }


        if (formvalid == true) {
            $.ajax({
                type: 'POST',
                url: 'ilt_dispatch_curd.php',
                data: {
                    ilt_id: ilt_id,
                    ilt_date: ilt_date,
                    dispatch: dispatch,
                    destination: destination,
                    dispatch_by: dispatch_by,
                    dispatch_mode: dispatch_mode,
                    vehicle_no: vehicle_no,
                    action: 'save_ilt'
                },
                async: false,
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#ilt_no').val(response.ilt_no);
                        $('#ilt_id').val(response.ilt_id);

                        $('#next-phase').css('display', 'block');
                        //Change the save btn Id for next phase work//
                        $('#save').attr('id', 'nextbtn');
                        get_item_list(response.ilt_id);
                    } else {
                        alert('something went wrong..!');
                    }
                },
                complete: function() {

                }

            });
        }
    });
    //================================================================

    $(document).on('change', '#item', function() {
        var item = $(this).val();
        $.ajax({
            type: 'post',
            url: 'order_indent_curd.php',
            data: {
                action: 'get_category',
                'item': item

            },
            dataType: 'json',
            success: function(response) {

                var x = '<option value="">Select Category</option>';
                if (response.status == 200) {
                    $.each(response.data, function(key, value) {
                        x = x + '<option value="' + value.category_id + '">' + value
                            .category +
                            '</option>';
                    });
                }
                $('#item_category').html(x);

            }

        });
    });

    //==========================on change item category====================
    $(document).on('change', '#item_category', function() {
        var category = $(this).val();
        var item = $('#item').val();
        var location = $('#dispatch').val();
        var entry_date = $('#ilt_date').val();

        $.ajax({
            type: 'post',
            url: 'order_indent_curd.php',
            data: {
                action: 'get_stock',
                'item': item,
                'location': location,
                'entry_date': entry_date,
                'category': category
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    if (response.data.qty == 0) {
                        $('#stock').val('0.00');
                        $('#unit').val(response.data.unit_id);
                        $('#unit_name').val(response.data.unit_name);
                    } else {
                        $('#stock').val(response.data.qty);
                        $('#unit_name').val(response.data.unit_name);
                        $('#unit').val(response.data.unit_id);
                    }
                } else {
                    alert('Something went wrong...!!!');
                }


            }

        });
    });
    //===============================================//



    //======================================
    $(document).on('click', '#nextbtn', function() {
        var rec_id = $('#rec_id').val();
        var ilt_id = $('#ilt_id').val();
        var item = $('#item').val();
        var item_category = $('#item_category').val();
        var unit = $('#unit').val();
        var qty = $('#qty').val();


        $.ajax({
            type: 'POST',
            url: 'ilt_dispatch_curd.php',
            data: {
                'rec_id': rec_id,
                'ilt_id': ilt_id,
                'item': item,
                'item_category': item_category,
                'unit': unit,
                'qty': qty,
                'action': 'save_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    get_item_list(ilt_id);
                    $('#qty').val('');
                    $('#stock').val('');
                    $('#unit').val('');
                    $('#unit_name').val('');
                    $('#item_category').prop('selectedIndex', '');
                    $('#item').prop('selectedIndex', '');

                } else {
                    alert('Somthing Went Wrong..Please try again..!!');
                }
            },
            error: function() {},
            complete: function() {
                get_item_list(ilt_id);
            },
        });
    });
    //==========================================//

    function get_item_list(ilt_id) {
        $('#get_item_list').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'ilt_dispatch_curd.php',
                'data': {
                    'action': 'get_item_list',
                    'ilt_id': ilt_id
                },
                type: 'post'
            },
            paging: true,

            "order": []

        });
        $('#get_item_list').DataTable().destroy();
    }
    //======================================//
    function removeMaterial(ilt_id = null) {
        if (ilt_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {

                $.ajax({
                    url: 'ilt_dispatch_curd.php',
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

    //===================================================
    function editMaterial(ilt_id = null) {
        if (ilt_id) {

            $.ajax({
                url: 'ilt_dispatch_curd.php',
                type: 'post',
                data: {
                    'action': 'edit_ilt_dispatch',
                    'ilt_id': ilt_id
                },
                dataType: 'json',
                success: function(response) {
                    var ilt_no = response.ilt_no;
                    if (ilt_no > 999) {
                        ilt_no = ilt_no;
                    } else {
                        if (ilt_no > 99 && ilt_no < 1000) {
                            ilt_no = "0" + ilt_no;
                        } else {
                            if (ilt_no > 9 && ilt_no < 100) {
                                ilt_no = "00" + ilt_no;
                            } else {
                                ilt_no = "000" + ilt_no;
                            }
                        }
                    }
                    ilt_no = response.ilt_prefix +
                        '/' + ilt_no;

                    $('#ilt_id').val(response.ilt_id);
                    $('#ilt_no').val(ilt_no);
                    $('#ilt_date').val(response.ilt_date);

                    $('#dispatch').prepend("<option value='" + response.despatch_from + "' selected>" +
                        response.dispatch_location + "</option>");
                    $('#destination').prepend("<option value='" + response.receive_at + "' selected>" +
                        response.received_location + "</option>");
                    $('#dispatch_by').prepend("<option value='" + response.despatch_by + "' selected>" +
                        response.staff_name + "</option>");
                    if (response.despatch_mode == 1) {
                        $('#dispatch_mode').prepend("<option value='" + 1 + "' selected>" +
                            'Hand Delivery' + "</option>");
                        $('#vehicle_div').css('display', 'none');
                    } else {
                        $('#dispatch_mode').prepend("<option value='" + 2 + "' selected>" +
                            'By Vehicle' + "</option>");
                        $('#vehicle_div').css('display', 'block');
                    }
                    $('#vehicle_no').val(response.vehicle_num);

                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }

    $(document).on('click', '#close_modal', function() {
        $('#material_modal').modal('hide');
        location.reload();
    });

    $(document).on('click', '.delete', function() {
        var rec_id = $(this).attr('id');
        var ilt_id = $('#ilt_id').val();
        $.ajax({
            url: 'ilt_dispatch_curd.php',
            type: 'post',
            data: {
                'action': 'delete_item',
                rec_id: rec_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    get_item_list(ilt_id);
                } else {
                    alert('Something Went Wrong....!!!');
                }

            }
        });
    });

    $(document).on('click', '.edit', function() {
        var rec_id = $(this).attr('id');
        $.ajax({

            url: 'ilt_dispatch_curd.php',
            type: 'post',
            data: {
                'action': 'get_edit_data',
                'rec_id': rec_id
            },
            dataType: 'json',
            success: function(response) {
                $('#rec_id').val(response.rec_id);
                $('#item').prepend("<option value='" + response.item_id + "' selected>" +
                    response.item_name + "</option>");
                $('#item_category').prepend("<option value='" + response.item_category +
                    "' selected>" + response.category + "</option>");

                $('#unit').val(response.unit_id);
                $('#unit_name').val(response.unit_name);
                $('#qty').val(response.despatch_qnty);
            }
        });
    });

    $(document).on('click', '.sent', function() {
        var ilt_id = $(this).attr('id');
        $.ajax({
            url: 'ilt_dispatch_curd.php',
            type: 'post',
            data: {
                'action': 'sent_ilt',
                'ilt_id': ilt_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    alert('Successfully Sent Item to the Destination');
                    location.reload();
                } else {
                    alert('Something Went Wrong...!!!');
                }
            }
        });
    });
    </script>

</body>

</html>