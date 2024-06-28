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
                        <span class="glyphicon glyphicon-plus-sign"></span> Material Issue</button>
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
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Material Receipt
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="issue_id" name="issue_id">
                                <label for="issue_no">Issue No.:</label>
                                <input type="text" id="issue_no" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="issue_date">Issue Date:</label>
                                <input type="date" id="issue_date" class="form-control"
                                    value="<?php echo date('Y-m-d');?>">
                            </div>
                            <div class="col-md-4">
                                <label for="location">Location:</label>
                                <select id="location" class="form-control">
                                    <option value="">Select Location</option>
                                    <?php
                                            $sql= "SELECT * FROM location ORDER BY location_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                            }
                                        ?>
                                </select>
                                <div id="location_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="issue_by">Issue By:</label>
                                <select id="issue_by" class="form-control">
                                    <option value="">--Select--</option>
                                </select>
                                <div id="issue_by_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="issue_to">Issue To:</label>
                                <input type="text" id="issue_to" class="form-control">
                                <div id="issue_to_err" style="color:red; display:none;"></div>
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
                                            <th>Issue Qty</th>
                                            <th>Unit</th>
                                            <th>Plot No</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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

                            <div class="col-md-4">
                                <label for="plot">Plot No.:</label>
                                <select id="plot" class="form-control">
                                    <option value="">Select</option>
                                    <?php
                                            $sql ="SELECT * FROM plot ORDER BY plot_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value='.$row['plot_id'].'>'.$row['plot_name'].'</option>';
                                            }
                                        ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="qty">Issue Qty:</label>
                                <input type="text" id="qty" class="form-control">
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
                'url': 'material_issue_curd.php',
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



    //-===----====--------=====---------====------------------------
    $(document).on('change', '#location', function() {
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
                var x = '<option value="">Select Issue By</option>';
                if (response.status == 200) {
                    $.each(response.data, function(key, value) {
                        x = x + '<option value="' + value.staff_id + '">' + value
                            .staff_name +
                            '</option>';
                    });
                }
                $('#issue_by').html(x);
            }
        });
    });

    //==================================================//

    $(document).on("click", "#save", function() {
        var issue_id = $('#issue_id').val();
        var issue_date = $('#issue_date').val();
        var location = $('#location').val();
        var issue_by = $('#issue_by').val();
        var issue_to = $('#issue_to').val();

        var formvalid = true;
        if (location == "") {
            $("#location_err").html("Location is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#location_err").css("display", "none");
        }
        if (issue_by == "") {
            $("#issue_by_err").html("Issue By is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#issue_by_err").css("display", "none");
        }
        if (issue_to == "") {
            $('#issue_to_err').html('Issue To required..!').css('display', 'block');
            formvalid = false;
        } else {
            $('#issue_to_err').css('display', 'none');
        }

        if (formvalid == true) {
            $.ajax({
                type: 'POST',
                url: 'material_issue_curd.php',
                data: {
                    issue_id: issue_id,
                    issue_date: issue_date,
                    location: location,
                    issue_by: issue_by,
                    issue_to: issue_to,
                    action: 'save_issue'
                },
                async: false,
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#issue_no').val(response.issue_no);
                        $('#issue_id').val(response.issue_id);

                        $('#next-phase').css('display', 'block');
                        //Change the save btn Id for next phase work//
                        $('#save').attr('id', 'nextbtn');
                        get_item_list(response.issue_id);
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
        var location = $('#location').val();
        var entry_date = $('#issue_date').val();

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
        var issue_id = $('#issue_id').val();
        var item = $('#item').val();
        var item_category = $('#item_category').val();
        var unit = $('#unit').val();
        var qty = $('#qty').val();
        var plot = $('#plot').val();

        $.ajax({
            type: 'POST',
            url: 'material_issue_curd.php',
            data: {
                'rec_id': rec_id,
                'issue_id': issue_id,
                'item': item,
                'item_category': item_category,
                'unit': unit,
                'qty': qty,
                'plot': plot,
                'action': 'save_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    get_item_list(issue_id);
                    $('#qty').val('');
                    $('#stock').val('');
                    $('#unit').val('');
                    $('#unit_name').val('');
                    $('#item_category').prop('selectedIndex', '');
                    $('#item').prop('selectedIndex', '');
                    $('#plot').prop('selectedIndex', '');

                } else {
                    alert('Somthing Went Wrong..Please try again..!!');
                }
            },
            error: function() {},
            complete: function() {
                get_item_list(issue_id);
            },
        });
    });
    //==========================================//

    function get_item_list(issue_id) {
        $('#get_item_list').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'material_issue_curd.php',
                'data': {
                    'action': 'get_item_list',
                    'issue_id': issue_id
                },
                type: 'post'
            },
            paging: true,

            "order": []

        });
        $('#get_item_list').DataTable().destroy();
    }
    //======================================//
    function removeMaterial(issue_id = null) {
        if (issue_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {

                $.ajax({
                    url: 'material_issue_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_record',
                        issue_id: issue_id
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
            }); // click remove btn
        } else {
            alert('Error: Refresh the page again');
        }
    }

    //===================================================
    function editMaterial(issue_id = null) {
        if (issue_id) {

            $.ajax({
                url: 'material_issue_curd.php',
                type: 'post',
                data: {
                    'action': 'edit_material_issue',
                    'issue_id': issue_id
                },
                dataType: 'json',
                success: function(response) {
                    var issue_no = response.issue_no;
                    if (issue_no > 999) {
                        issue_no = issue_no;
                    } else {
                        if (issue_no > 99 && issue_no < 1000) {
                            issue_no = "0" + issue_no;
                        } else {
                            if (issue_no > 9 && issue_no < 100) {
                                issue_no = "00" + issue_no;
                            } else {
                                issue_no = "000" + issue_no;
                            }
                        }
                    }
                    issue_no = response.issue_prefix +
                        '/' + issue_no;

                    $('#issue_id').val(response.issue_id);
                    $('#issue_no').val(issue_no);
                    $('#issue_date').val(response.issue_date);

                    $('#location').prepend("<option value='" + response.location_id + "' selected>" +
                        response.location_name + "</option>");
                    $('#issue_by').prepend("<option value='" + response.issue_by + "' selected>" +
                        response.staff_name + "</option>");
                    $('#issue_to').val(response.issue_to);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }


    $(document).on('click', '.delete', function() {
        var rec_id = $(this).attr('id');
        var issue_id = $('#issue_id').val();
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
                    get_item_list(issue_id);
                } else {
                    alert('Something Went Wrong....!!!');
                }

            }
        });
    });

    $(document).on('click', '.edit', function() {
        var rec_id = $(this).attr('id');
        $.ajax({

            url: 'material_issue_curd.php',
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
                $('#plot').prepend("<option value='" + response.plot_id +
                    "' selected>" + response.plot_name + "</option>");
                $('#unit').val(response.issue_unit);
                $('#unit_name').val(response.unit_name);
                $('#qty').val(response.issue_qnty);
            }
        });
    });
    </script>

</body>

</html>