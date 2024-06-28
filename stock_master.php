<?php include'menu.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <style>
    table {
        margin-bottom: 0px;
    }

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

    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>tfoot>tr>td,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
        padding: 5px;
    }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">
            <div class="row">
                <div class="removeMessages"></div>
                <div class="col-md-12" id="form1">
                    <div class="left-box">
                        <form id="add_party_form" action="javascript:void(0);">
                            <div class="row">
                                <input type="hidden" id="stock_id" name="stock_id">
                                <div class="col-md-2 mb-3">
                                    <label for="location">Stock Location:</label>
                                    <select id="location" name="location" class="form-control">
                                        <option value=''>--Select Location--</option>
                                        <?php
                                        $sql="SELECT * FROM location ORDER BY location_name ASC";
                                        $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                        }
                                    ?>
                                    </select>
                                    <div id="location_err" class="error" style="display: none;"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label form="date">As On Date:</label>
                                    <input type="date" id="date" name="date" class="form-control"
                                        value="<?php echo date('Y-m-d');?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="item_name">Item Name:</label>
                                    <select id="item_name" name="item_name" class="form-control">
                                        <option value="">--Select Item--</option>
                                        <?php
                                                $sql="SELECT item_id, item_name FROM item";
                                                $query=$connect->query($sql);
                                                while($row=$query->fetch_assoc()){
                                                    echo'<option value="'.$row['item_id'].'">'.$row['item_name'].'</option>';
                                                }
                                            ?>
                                    </select>
                                    <div id="item_err" class="error" style="display: none;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label for="item_category">Item Category</label>
                                    <select id="item_category" class="form-control"></select>
                                    <div id="item_category_err" class="err" style="display:none"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="unit">Unit:</label>
                                    <select id="unit" name="unit" class="form-control">
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="op_quantity">Op Quantity:</label>
                                    <input type="text" name="op_quantity" id="op_quantity" class="form-control" />
                                    <div id="op_quantity_err" class="error" style="display: none;"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="rate">Rate:</label>
                                    <input type="text" name="rate" id="rate" class="form-control" />
                                    <div id="rate_err" class="error" style="display: none;"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="op_amount">Op Amount:</label>
                                    <input type="text" name="op_amount" id="op_amount" class="form-control" />
                                </div>
                                <div class="col-md-12 mb-12">
                                    <div style="padding:5px;"></div>
                                    <button type="reset" class="btn btn-mb btn-danger">Reset</button>
                                    <button class="btn btn-mb btn-primary" id="save" name="save">Save
                                        Changes</button>
                                    <div style="padding:5px;"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-12" id="table1">
                    <div class="right-box">

                        <table class="table" id="stock_list" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:30px">S.no</th>
                                    <th style="width:200px">Item Name</th>

                                    <th style="width:100px">Op Qty.</th>
                                    <th style="width:100px">Unit Name</th>
                                    <th style="width:100px">Rate</th>
                                    <th style="width:100px">Amount</th>
                                    <th style="width:100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" style="text-align:center">
                                        <h4>Plese Select the location to get stock list</h4>
                                    </td>
                                </tr>
                                <h3></h3>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removeStockModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Party</h4>
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
    function getList(location) {
        $("#stock_list").DataTable({
            "ajax": {
                url: "stock_master_curd.php",
                data: {
                    action: 'getStock',
                    'location': location
                },
                type: 'post'
            },
            "oLanguage": {
                "sEmptyTable": "No Record Found.. Please select another location"
            },

            "order": []
        });
        $('#stock_list').DataTable().destroy();
    }

    //-------------------Get Stock List on Location Change---------------
    $(document).on('change', '#location', function() {
        getList($(this).val());

    });
    //----------------Calculate Stock Amount--------------------
    $('#rate').keyup(function() {
        var rate = $(this).val();
        var quantity = $("#op_quantity").val();
        var amt = quantity * rate;
        $("#op_amount").val(amt); // sets the total amount input to the quantity * rate
    });
    $('#op_quantity').keyup(function() {
        var quantity = $(this).val();
        var rate = $("#rate").val();
        var amt = quantity * rate;
        $("#op_amount").val(amt); // sets the total amount input to the quantity * rate
    });

    $(document).on('change', '#item_name', function() {
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
        var item_id = $('#item_name').val();
        $.ajax({
            type: 'post',
            url: "stock_master_curd.php",

            data: {
                action: 'get_unit',
                item_id: item_id
            },
            dataType: 'json',
            success: function(response) {
                $('#unit').prepend("<option value='" + response.unit_id + "' selected>" +
                    response.unit_name + "</option>");
            }

        });
    });

    //-------------------------------Add New Stock / Edit Stock ---------
    $(document).on('click', '#save', function() {
        var stock_id = $('#stock_id').val();
        var location = $('#location').val();
        var date = $('#date').val();
        var item_name = $('#item_name').val();
        var unit = $('#unit').val();
        var op_quantity = $('#op_quantity').val();
        var rate = $('#rate').val();
        var op_amount = $('#op_amount').val();
        var item_category = $('#item_category').val();
        var formValid = true;
        if (location == '') {
            $('#location_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#location_err').css('display', 'none');
        }
        if (item_name == '') {
            $('#item_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#item_err').css('display', 'none');
        }
        if (op_quantity == '') {
            $('#op_quantity_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#op_quantity_err').css('display', 'none');
        }
        if (rate == '') {
            $('#rate_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#rate_err').css('display', 'none');
        }
        if (item_category == '') {
            $('#item_category_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#item_category_err').css('display', 'none');
        }
        if (formValid == true) {
            $.ajax({
                type: 'post',
                url: 'stock_master_curd.php',
                data: {
                    'action': 'addstock',
                    'stock_id': stock_id,
                    'location': location,
                    'date': date,
                    'item_name': item_name,
                    'unit': unit,
                    'op_quantity': op_quantity,
                    'rate': rate,
                    'op_amount': op_amount,
                    'item_category': item_category
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        alert(response.messages);
                        history.go(0);
                    } else {

                        alert(response.messages);
                    }
                }
            });
        }
    });

    function removeStock(stock_id = null) {
        if (stock_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'stock_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_srock',
                        stock_id: stock_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response);
                        if (response.success == true) {

                            $("#removeStockModal").modal('hide');
                            alert('deleted successfully');
                            history.go(0);
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

    function editStock(stock_id = null) {
        if (stock_id) {
            // fetch the member data
            $.ajax({
                url: 'stock_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_stock',
                    'stock_id': stock_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#stock_id").val(response.stock_id);
                    $('#location').prepend("<option value='" + response.location + "' selected>" +
                        response.location_name + "</option>");
                    $('#item_name').prepend("<option value='" + response.item_id + "' selected>" +
                        response.item_name + "</option>");
                    $('#unit').prepend("<option value='" + response.unit_id + "' selected>" +
                        response.unit_name + "</option>");
                    $('#date').val(response.date);
                    $('#op_quantity').val(response.op_quantity);
                    $('#op_amount').val(response.op_amount);
                    $('#rate').val(response.rate);
                } // /success
            }); // /fetch selected member info
        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>

</body>

</html>