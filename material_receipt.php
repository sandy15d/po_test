<?php include 'menu.php';
include 'db_connect.php';
$sql_user = mysql_query("SELECT mr1,mr2,mr3,mr4 FROM users WHERE uid=" . $_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
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
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="search" name="search">Search</button>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2 ">
                    <?php if ($row_user['mr1'] == 1) { ?>
                        <button class="btn btn-default pull pull-right " data-toggle="modal" data-target="#material_modal">
                            <span class="glyphicon glyphicon-plus-sign"></span> New Material Receipt</button>
                    <?php } ?>
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
                                <th>Rcpt. No.</th>
                                <th>Rcpt. Date</th>
                                <th style="width:100px;">DC No</th>
                                <th>DC Date</th>
                                <th>Received At</th>
                                <th>Party Name</th>
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
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Material Receipt
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <input type="hidden" id="receipt_id">
                                <label for="receipt_no">Receipt No</label>
                                <input type="text" id="receipt_no" name="receipt_no" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="receipt_date">Receipt Date<span style="color:red;">*</span></label>
                                <input type="date" id="receipt_date" name="receipt_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="background:white; " />
                                <div id="receipt_date_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="dc_no">DC No<span style="color:red;">*</span></label>
                                <select class="form-control" id="dc_no" name="dc_no">
                                    <option value="">Select DC No</option>
                                    <?php
                                    $sql = "SELECT tbl1.dc_id, tbl1.dc_no, ti.ind_prefix FROM tbldelivery1 tbl1
                                        JOIN tbldelivery2 tbl2 ON tbl2.dc_id =tbl1.dc_id
                                        JOIN tblpo tp ON tp.po_id = tbl1.po_id
                                        JOIN tbl_indent ti ON ti.indent_id = tp.indent_id
                                        WHERE tbl2.item_received='N' GROUP BY tbl1.dc_id ORDER BY dc_no";
                                    $query = $connect->query($sql);
                                    while ($row = $query->fetch_assoc()) {
                                        if ($row['dc_no'] > 999) {
                                            $dc_no = $row['dc_no'];
                                        } else {
                                            if ($row['dc_no'] > 99 && $row['dc_no'] < 1000) {
                                                $dc_no = "0" . $row['dc_no'];
                                            } else {
                                                if ($row['dc_no'] > 9 && $row['dc_no'] < 100) {
                                                    $dc_no = "00" . $row['dc_no'];
                                                } else {
                                                    $dc_no = "000" . $row['dc_no'];
                                                }
                                            }
                                        }
                                        $dc_no = $row['ind_prefix'] . '/' . $dc_no;
                                        echo '<option value="' . $row['dc_id'] . '">' . $dc_no . '</option>';
                                    }
                                    ?>
                                </select>
                                <div id="dc_no_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="dc_date">DC Date</label>
                                <input type="text" id="dc_date" name="dc_date" class="form-control" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="chalan">Chalan No</label>
                                <input type="text" id="chalan" name="chalan" class="form-control" />
                                <div id="chalan_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="chalan_date">Chalan Date</label>
                                <input type="date" id="chalan_date" name="chalan_date" class="form-control">
                                <div id="chalan_date_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="invoice_no" class="data-heading">Invoice No</label>
                                <input type="text" id="invoice_no" name="invoice_no" class="form-control" />
                                <div id="invoice_no_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="invoice_date" class="data-heading">Invoice Date</label>
                                <input type="date" id="invoice_date" name="invoice_date" class="form-control">
                                <div id="invoice_date_err" style="color:red; display:none;"></div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="delivery_at">Delivery At</label>
                                <input type="text" id="delivery_at" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="req_date">Required Date</label>
                                <input type="text" id="req_date" name="req_date" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label for="transit_point">Transit Point<span style="color:red;">*</span></label>
                                <select id="transit_point" name="transit_point" class="form-control">
                                    <option value="">Choose...</option>
                                    <?php
                                    $location = "SELECT * FROM location ORDER BY location_name";
                                    $query1 = $connect->query($location);
                                    while ($row1 = $query1->fetch_assoc()) {
                                        echo '<option value="' . $row1['location_id'] . '">' . $row1['location_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <div id="transit_point_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="rec_at">Received At<span style="color:red;">*</span></label>
                                <select class="form-control" id="rec_at">
                                    <option value="">Choose...</option>
                                    <?php
                                    $location = "SELECT * FROM location ORDER BY location_name";
                                    $query1 = $connect->query($location);
                                    while ($row1 = $query1->fetch_assoc()) {
                                        echo '<option value="' . $row1['location_id'] . '">' . $row1['location_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <div id="rec_at_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="rec_by">Received By<span style="color:red;">*</span></label>
                                <select class="form-control" id="rec_by"></select>
                                <div id="rec_by_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="f_paid">Fright Paid<span style="color:red;">*</span></label>
                                <select class="form-control" id="f_paid">
                                    <option value="N">No</option>
                                    <option value="Y">Yes</option>
                                </select>
                                <div id="f_paid_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="f_amount">Fright Amount</label>
                                <input type="number" id="f_amount" name="f_amount" class="form-control" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="po_id" name="po_id">
                                <label for="po_no">PO No</label>
                                <input type="text" id="po_no" name="po_no" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="po_date">PO Date</label>
                                <input type="text" id="po_date" name="po_date" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="party_name">Party Name</label>
                                <input type="text" id="party_name" name="party_name" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="address1">Address 1</label>
                                <input type="text" name="address1" id="address1" class="form-control" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="address2">Address 2</label>
                                <input type="text" name="address2" id="address2" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="address3">Address 3</label>
                                <input type="text" name="address3" id="address3" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" disabled />
                            </div>
                            <div class="col-md-3">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" class="form-control" disabled />
                            </div>
                        </div>

                        <div class="row" id="next-phase" style="display:none;">
                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-12">
                                <input type="hidden" id="" name="">
                                <table id="material_receipt_item" class="table">
                                    <thead>
                                        <th><input type="checkbox" id="select_all" checked></th>
                                        <th style="width:200px;">Item Name</th>
                                        <th>Order Qty</th>
                                        <th>Cur. Recd. Qty</th>

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
        getList(start_date, end_date);

        function getList(start_date, end_date) {
            $('#mrtable').DataTable({
                "bLengthChange": false,
                "searching": false,
                destroy: true,
                'ajax': {
                    'url': 'material_receipt_curd.php',
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
        //-----------------------------

        $(document).on('change', '#dc_no', function() {
            var dc_id = $(this).val();
            $.ajax({
                url: 'material_receipt_curd.php',
                type: 'POST',
                data: {
                    'action': 'get_detail',
                    'dc_id': dc_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.po_no > 999) {
                        var po_no = response.po_no;
                    } else {
                        if (response.po_no > 99 && response.po_no < 1000) {
                            var po_no = "0" + response.po_no;
                        } else {
                            if (response.po_no > 9 && response.po_no < 100) {
                                var po_no = "00" + response.po_no;
                            } else {
                                var po_no = "000" + response.po_no;
                            }
                        }
                    }
                    po_no = response.CCode + '/' + response.ind_prefix + '/' + po_no;
                    $('#dc_date').val(response.dc_date);
                    $('#delivery_at').val(response.location_name);
                    $('#req_date').val(response.delivery_date);
                    $('#po_no').val(po_no);
                    $('#po_id').val(response.po_id);
                    $('#po_date').val(response.po_date);
                    $('#party_name').val(response.party_name);
                    $('#address1').val(response.address1);
                    $('#address2').val(response.address2);
                    $('#address3').val(response.address3);
                    $('#city').val(response.city_name);
                    $('#state').val(response.state_name);

                }
            });
        });

        //-===----====--------=====---------====------------------------
        $(document).on('change', '#rec_at', function() {
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
                    var x = '<option value="">Select Order By</option>';
                    if (response.status == 200) {
                        $.each(response.data, function(key, value) {
                            x = x + '<option value="' + value.staff_id + '">' + value
                                .staff_name +
                                '</option>';
                        });
                    }
                    $('#rec_by').html(x);
                }
            });
        });
        //==========================================
        $(document).on('change', '#f_paid', function() {
            var f_paid = $(this).val();
            if (f_paid == 'Y') {
                $('#f_amount').removeAttr("disabled");
            } else {
                $('#f_amount').prop("disabled", true);
            }
        });

        //==================================================//

        $(document).on("click", "#save", function() {
            var receipt_id = $('#receipt_id').val();
            var receipt_date = $('#receipt_date').val();
            var dc_no = $('#dc_no').val();
            var dc_date = $("#dc_date").val();
            var chalan = $('#chalan').val();
            var chalan_date = $('#chalan_date').val();
            var delivery_at = $('#delivery_at').val();
            var req_date = $('#req_date').val();
            var transit_point = $('#transit_point').val();
            var rec_at = $('#rec_at').val();
            var rec_by = $('#rec_by').val();
            var f_paid = $('#f_paid').val();
            var f_amount = $('#f_amount').val();
            var po_id = $('#po_id').val();
            var invoice_no = $("#invoice_no").val();
            var invoice_date = $("#invoice_date").val();
            var formvalid = true;
            if (receipt_date == "") {
                $("#receipt_date_err").html("Receipt Date is Required..!").css("display", "block");
                formvalid = false;
            } else {
                $("#receipt_date_err").css("display", "none");
            }
            if (dc_no == "") {
                $("#dc_no_err").html("DC No is Required..!").css("display", "block");
                formvalid = false;
            } else {
                $("#dc_no_err").css("display", "none");
            }
            if (transit_point == "") {
                $('#transit_point_err').html('Transit Point is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#transit_point_err').css('display', 'none');
            }
            if (rec_at == "") {
                $('#rec_at_err').html('Received At is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#rec_at_err').css('display', 'none');
            }
            if (rec_by == "") {
                $('#rec_by_err').html('Received By is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#rec_by_err').css('display', 'none');
            }
            if (f_paid == "") {
                $('#f_paid_err').html('Fright Paid is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#f_paid_err').css('display', 'none');
            }

            if (chalan == "") {
                $('#chalan_err').html('Chalan No is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#chalan_err').css('display', 'none');
            }
            if (chalan_date == "") {
                $('#chalan_date_err').html('Chalan Date is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#chalan_date_err').css('display', 'none');
            }

            if (invoice_no == "") {
                $('#invoice_no_err').html('invoice No is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#invoice_no_err').css('display', 'none');
            }
            if (invoice_date == "") {
                $('#invoice_date_err').html('Chalan Date is required..!').css('display', 'block');
                formvalid = false;
            } else {
                $('#invoice_date_err').css('display', 'none');
            }

            if (formvalid == true) {
                $.ajax({
                    type: 'POST',
                    url: 'material_receipt_curd.php',
                    data: {
                        receipt_id: receipt_id,
                        receipt_date: receipt_date,
                        dc_no: dc_no,
                        dc_date: dc_date,
                        chalan: chalan,
                        chalan_date: chalan_date,
                        invoice_no: invoice_no,
                        invoice_date: invoice_date,
                        delivery_at: delivery_at,
                        req_date: req_date,
                        transit_point: transit_point,
                        rec_at: rec_at,
                        rec_by: rec_by,
                        f_paid: f_paid,
                        f_amount: f_amount,
                        po_id: po_id,
                        action: 'save_material_receipt'
                    },
                    async: false,
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.success == true) {
                            $('#receipt_no').val(response.receipt_no);
                            $('#receipt_id').val(response.receipt_id);
                            $('#receipt_date').prop('disabled', true);
                            $('#dc_no').prop('disabled', true);
                            $('#next-phase').css('display', 'block');
                            //Change the save btn Id for next phase work//
                            $('#save').attr('id', 'nextbtn');
                        } else {
                            alert('something went wrong..!');
                        }
                    },
                    complete: function() {
                        get_item();
                    }

                });
            }
        });

        //===============================================//
        $(document).on('click', '#select_all', function() {
            if ($(this).prop('checked') == true) {
                $('.select_item').prop('checked', true);
            } else {
                $('.select_item').prop('checked', false);
            }
        });

        //=============================================//
        function get_item() {
            var dc_id = $('#dc_no').val();
            $.ajax({
                type: 'post',
                url: 'material_receipt_curd.php',
                data: {
                    dc_id: dc_id,
                    action: 'Get_Item'
                },
                dataType: 'json',
                success: function(response) {
                    var x = '';

                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.item_id + '" data-item_name="' + value.item_name +
                            '" data-qty="' + value.qty + '"data-unit_id="' + value.unit_id +
                            '"data-category_id="' + value.category_id +
                            '" type="checkbox" name="select_item" class="select_item" checked></td>' +
                            '<td>' + value.item_name + '</td>' +

                            '<td>' + value.qty + ' ' + value.unit_name + '</td>' +

                            '<td><input type="text" id="recd_qty_' + value.item_id + value.category_id +
                            '" value="' + value
                            .qty +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList').html(x);
                },
            });
        }

        //======================================
        $(document).on('click', '#nextbtn', function() {
            var list_array = [];
            var receipt_id = $('#receipt_id').val();
            var dc_id = $('#dc_no').val();
            $('.select_item').each(function() {
                if ($(this).prop('checked') == true) {
                    var item_id = $(this).attr('id');
                    var category_id = $(this).data('category_id');
                    var recd_qty = $('#recd_qty_' + item_id + category_id).val();

                    list_array.push({
                        'item_id': item_id,
                        'recd_qty': recd_qty,
                        'category_id': category_id,
                        'unit_id': $(this).data('unit_id'),
                    });
                }
            });
            console.log(list_array);
            //return false;
            $.ajax({
                type: 'POST',
                url: 'material_receipt_curd.php',
                data: {
                    'list_array': list_array,
                    'receipt_id': receipt_id,
                    'dc_id': dc_id,
                    'action': 'save_item'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#dcmodal').modal('hide');
                        alert('Successfully..');
                        location.reload();
                    } else {
                        $('#dcmodal').modal('hide');
                        alert('Somthing Went Wrong..Please try again..!!');
                        location.reload();
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        });

        //======================================//
        function removeMaterial(rec_id = null) {
            if (rec_id) {
                // click on remove button
                $("#removeBtn").unbind('click').bind('click', function() {

                    $.ajax({
                        url: 'material_receipt_curd.php',
                        type: 'post',
                        data: {
                            'action': 'delete_record',
                            rec_id: rec_id
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
        function editMaterial(rec_id = null) {
            if (rec_id) {

                $.ajax({
                    url: 'material_receipt_curd.php',
                    type: 'post',
                    data: {
                        'action': 'edit_material_receipt',
                        'rec_id': rec_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        var rec_no = response.receipt_no;
                        if (rec_no > 999) {
                            rec_no = rec_no;
                        } else {
                            if (rec_no > 99 && rec_no < 1000) {
                                rec_no = "0" + rec_no;
                            } else {
                                if (rec_no > 9 && rec_no < 100) {
                                    rec_no = "00" + rec_no;
                                } else {
                                    rec_no = "000" + rec_no;
                                }
                            }
                        }
                        rec_no = response.receipt_prefix +
                            '/' + rec_no;

                        var dc_no = response.dc_no;
                        if (dc_no > 999) {
                            dc_no = dc_no;
                        } else {
                            if (dc_no > 99 && dc_no < 1000) {
                                dc_no = "0" + dc_no;
                            } else {
                                if (dc_no > 9 && dc_no < 100) {
                                    dc_no = "00" + dc_no;
                                } else {
                                    dc_no = "000" + dc_no;
                                }
                            }
                        }
                        $('#receipt_id').val(response.receipt_id);
                        $('#receipt_no').val(rec_no);
                        $('#receipt_date').val(response.receipt_date);
                        $('#dc_no').prepend("<option value='" + response.dc_id + "' selected>" + dc_no +
                            "</option>");
                        $('#dc_date').val(response.dc_date);
                        $('#chalan').val(response.challan_no);
                        $('#chalan_date').val(response.challan_date);
                        $('#invoice_no').val(response.invoice_no);
                        $('#invoice_date').val(response.invoice_date);
                        $('#delivery_at').val(response.delivery_location);
                        $('#req_date').val(response.delivery_date);
                        $('#transit_point').val(response.transit_point);
                        $('#rec_at').val(response.recd_at);
                        $('#rec_by').prepend("<option value='" + response.recd_by + "' selected>" + response
                            .received_by + "</option>");
                        $('#f_paid').val(response.freight_paid);
                        if (response.freight_paid == 'Y') {
                            $('#f_amount').removeAttr("disabled");
                        }
                        $('#f_amount').val(response.freight_amt);
                        $('#po_id').val(response.po_id);
                        $('#po_no').val(response.po_no);
                        $('#po_date').val(response.po_date);
                        $('#party_name').val(response.party_name);
                        $('#address1').val(response.address1);
                        $('#address2').val(response.address2);
                        $('#address3').val(response.address3);
                        $('#city').val(response.city_name);
                        $('#state').val(response.state_name);
                    } // /success
                }); // /fetch selected member info

            } else {
                alert("Error : Refresh the page again");
            }
        }
    </script>

</body>

</html>