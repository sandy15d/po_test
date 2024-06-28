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
                <div class="col-md-2"></div>
                <div class="col-md-2 ">
                    <button class="btn btn-default pull pull-right " data-toggle="modal" data-target="#material_modal">
                        <span class="glyphicon glyphicon-plus-sign"></span> Add Physical Stock</button>
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
                                <th>P.S. No.</th>
                                <th>Date</th>
                                <th>Location</th>
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
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Physical Stock
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="ps_id">
                                <label for="ps_no">PS No.:</label>
                                <input type="text" id="ps_no" class="form-control" style="color:blue;" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="ps_date">PS Date:</label>
                                <input type="date" id="ps_date" class="form-control"
                                    value="<?php echo date("Y-m-d");?>">
                            </div>
                            <div class="col-md-3">
                                <label for="location">Location:</label>
                                <select id="location" class="form-control">
                                    <option value="">---Select Location---</option>
                                    <?php
                                   if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
                                        $sql ="SELECT * FROM location ORDER BY location_name";
                                   }else{
                                       $sql ="SELECT * FROM location WHERE location_id =".$_SESSION['stores_locid']."";
                                   }
                                  $query =$connect->query($sql);
                                  while($row=$query->fetch_assoc()){
                                    echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                }
                                  ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status">Stock Volume:</label>
                                <select id="status" class="form-control">
                                    <option value="I">Increased</option>
                                    <option value="D">Decreased</option>
                                </select>
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
                                            <th>Item Stock</th>
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
                                <label for="qty">Stock Physical:</label>
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
    getList(start_date, end_date);

    function getList(start_date, end_date) {
        $('#mrtable').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'physical_stock_curd.php',
                'data': {
                    'action': 'get_pstock',
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





    //==================================================//

    $(document).on("click", "#save", function() {
        var ps_id = $('#ps_id').val();
        var ps_date = $('#ps_date').val();
        var location = $('#location').val();
        var status = $('#status').val();

        var formvalid = true;
        /*     if (location == "") {
                $("#location_err").html("Location is Required..!").css("display", "block");
                formvalid = false;
            } else {
                $("#location_err").css("display", "none");
            } */

        if (formvalid == true) {
            $.ajax({
                type: 'POST',
                url: 'physical_stock_curd.php',
                data: {
                    ps_id: ps_id,
                    ps_date: ps_date,
                    location: location,
                    status: status,
                    action: 'save_ps'
                },
                async: false,
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#ps_no').val(response.ps_no);
                        $('#ps_id').val(response.ps_id);

                        $('#next-phase').css('display', 'block');
                        $('#save').attr('id', 'nextbtn');

                    } else {
                        alert('something went wrong..!');
                    }
                },
                complete: function() {
                    get_item_list(ps_id);
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
        var location = $('#location').val();
        var entry_date = $('#ps_date').val();

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
        var ps_id = $('#ps_id').val();
        var item = $('#item').val();
        var item_category = $('#item_category').val();
        var unit = $('#unit').val();
        var qty = $('#qty').val();
        var status = $('#status').val();

        $.ajax({
            type: 'POST',
            url: 'physical_stock_curd.php',
            data: {
                'rec_id': rec_id,
                'ps_id': ps_id,
                'item': item,
                'item_category': item_category,
                'unit': unit,
                'qty': qty,
                'status': status,
                'action': 'save_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    get_item_list(ps_id);
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
                get_item_list(ps_id);
            },
        });
    });
    //==========================================//

    function get_item_list(ps_id) {
        $('#get_item_list').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'physical_stock_curd.php',
                'data': {
                    'action': 'get_item_list',
                    'ps_id': ps_id
                },
                type: 'post'
            },
            paging: true,

            "order": []

        });
        $('#get_item_list').DataTable().destroy();
    }
    //======================================//
    function removeMaterial(ps_id = null) {
        if (ps_id) {
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'physical_stock_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_record',
                        ps_id: ps_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            $(".removeMessages").html(
                                '<div class="alert alert-success alert-dismissible" role="alert">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                                response.messages +
                                '</div>');
                            $("#removeMaterialModal").modal('hide');
                            getList(start_date, end_date);
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
            });
        } else {
            alert('Error: Refresh the page again');
        }
    }

    //===================================================
    function editMaterial(ps_id = null) {
        if (ps_id) {

            $.ajax({
                url: 'physical_stock_curd.php',
                type: 'post',
                data: {
                    'action': 'edit_physical_stock',
                    'ps_id': ps_id
                },
                dataType: 'json',
                success: function(response) {
                    var ps_no = response.ps_no;
                    if (ps_no > 999) {
                        ps_no = ps_no;
                    } else {
                        if (ps_no > 99 && ps_no < 1000) {
                            ps_no = "0" + ps_no;
                        } else {
                            if (ps_no > 9 && ps_no < 100) {
                                ps_no = "00" + ps_no;
                            } else {
                                ps_no = "000" + ps_no;
                            }
                        }
                    }
                    ps_no = response.ps_prefix +
                        '/' + ps_no;

                    $('#ps_id').val(response.ps_id);
                    $('#ps_no').val(ps_no);
                    $('#ps_date').val(response.ps_date);

                    $('#location').prepend("<option value='" + response.location_id + "' selected>" +
                        response.location_name + "</option>");
                    if (response.ps_type == 'I') {
                        $('#status').prepend("<option value='I' selected>" + 'Increased' + "</option>");
                    } else {
                        $('#status').prepend("<option value='D' selected>" + 'Decreased' + "</option>");
                    }

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
        var ps_id = $('#ps_id').val();
        $.ajax({
            url: 'material_issue_curd.php',
            type: 'post',
            data: {
                'action': 'delete_item',
                rec_id: rec_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    get_item_list(ps_id);
                } else {
                    alert('Something Went Wrong....!!!');
                }

            }
        });
    });

    $(document).on('click', '.edit', function() {
        var rec_id = $(this).attr('id');
        $.ajax({

            url: 'physical_stock_curd.php',
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
                $('#qty').val(response.ps_qnty);
            }
        });
    });
    </script>

</body>

</html>