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
                    <button class="btn btn-default pull pull-right " data-toggle="modal"
                        data-target="#purchase_bill_modal">
                        <span class="glyphicon glyphicon-plus-sign"></span> New Purchase Bill</button>
                </div>

            </div>
            <div class="row" style="padding-top: 10px;">

                <div class="col-md-1"></div>
                <div class="col-md-10 ">
                    <div class="removeMessages"></div>

                    <table class="table" id="bill_list_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Bill No.</th>
                                <th>Bill Date</th>
                                <th>Bill Amount</th>
                                <th style="width:300px;">Company Name</th>
                                <th style="width:300px;">Party Name</th>
                                <th>Bill Return</th>
                                <th>Bill Paid</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </section>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="purchase_bill_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Purchase Bill
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="bill_id">
                                <label for="bill_no">Bill No.<b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="bill_no" class="form-control" style="color:blue">
                                <div id="bill_no_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="bill_date">Bill Date:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="date" id="bill_date" class="form-control" style="color:blue">
                                <div id="bill_date_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="bill_amt">Bill Amount:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="bill_amt" class="form-control">
                                <div id="bill_amt_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="po_id">PO No.<b style="color:red;font-size:14px;">*</b></label>
                                <select id="po_id" class="form-control" style="color:blue">
                                    <option value="">Select PO</option>
                                </select>
                                <div id="po_id_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="po_date">PO Date</label>
                                <input type="text" id="po_date" class="form-control" disabled style="color:blue">
                            </div>
                            <div class="col-md-4">
                                <label for="mr_id">Material Recpt No.<b style="color:red;font-size:14px;">*</b></label>
                                <select id="mr_id" class="form-control" style="color:blue">
                                    <option value="">Select MR No</option>
                                </select>
                                <div id="mr_id_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="mr_date">MR Date</label>
                                <input type="text" id="mr_date" class="form-control" disabled style="color:blue">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="party_id">
                                <label for="party">Party Name:</label>
                                <input type="text" id="party" class="form-control" disabled style="color:blue">
                            </div>
                            <div class="col-md-3">
                                <label for="address1">Address1</label>
                                <input type="text" class="form-control" id="address1" disabled style="color:blue">
                            </div>
                            <div class="col-md-3">
                                <label for="address2">Address2:</label>
                                <input type="text" class="form-control" id="address2" disabled style="color:blue">
                            </div>
                            <div class="col-md-3">
                                <label for="address3">Address3:</label>
                                <input type="text" class="form-control" id="address3" disabled style="color:blue">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" disabled style="color:blue">
                            </div>
                            <div class="col-md-3">
                                <label for="state">State</label>
                                <input type="text" class="form-control" id="state" disabled style="color:blue">
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" id="company_id">
                                <label for="company">Company:</label>
                                <input type="text" id="company" class="form-control" disabled style="color:blue">
                            </div>
                        </div>
                        <div class="row" id="item_div" style="display:none">
                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped" id="item_list">
                                    <thead>
                                        <th><input type="checkbox" id="select_all" checked></th>
                                        <th>Item Name</th>

                                        <th>Recd Qty</th>
                                        <th>Billing Qty</th>
                                        <th>Rate</th>
                                    </thead>
                                    <tbody id="recordList"></tbody>
                                </table>
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



    <!-- Edit modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="edit_bill_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Purchase Bill Edit
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="hidden" id="edit_bill_id">
                                <input type="hidden" id="edit_po_id">

                                <label for="edit_bill_no">Bill No.:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="edit_bill_no" class="form-control" style="color:blue;">
                            </div>
                            <div class="col-md-2">
                                <label for="edit_bill_amt">Bill Amount:<b
                                        style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="edit_bill_amt" class="form-control" style="color:blue;">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_party">Party Name:</label>
                                <input type="text" id="edit_party" class="form-control" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_company">Company Name:</label>
                                <input type="text" id="edit_company" class="form-control" disabled>
                            </div>
                        </div>
                        <hr style="border-top: 1px dashed red;">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped" id="item_list_edit">
                                <thead>
                                    <th><input type="checkbox" id="select_all_edit" checked></th>
                                    <th>Material Recpt No.</th>
                                    <th>Item Name</th>
                                    <th>Billing Qty</th>
                                    <th>Rate</th>
                                </thead>
                                <tbody id="recordList_edit"></tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                            id="close_modal">Close</button>
                        <button type="button" class="btn btn-primary" name="update" id="update">Save changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /Edit modal -->


    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {


        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        getList(start_date, end_date);

        function getList(start_date, end_date) {
            $('#bill_list_table').DataTable({
                "bLengthChange": false,
                "searching": false,
                destroy: true,
                'ajax': {
                    'url': 'purchase_bill_curd.php',
                    'data': {
                        'action': 'get_bill',
                        'start_date': start_date,
                        'end_date': end_date
                    },
                    type: 'post'
                },
                paging: true,

                "order": []

            });
            $('#bill_list_table').DataTable().destroy();
        }

        $(document).on('click', '#search', function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            getList(start_date, end_date);
        });
        //-----------------------------


        $(document).on('change', '#bill_date', function() {
            var bill_date = $(this).val();
            get_po(bill_date);

        });

        function get_po(bill_date) {
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    bill_date: bill_date,

                    'action': 'get_po'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.status == 200) {
                        var b = '<option value="">Select PO</option>';
                        $.each(response.data.data, function(k, v) {
                            var po_no = v.po_no;
                            if (po_no > 999) {
                                po_no = po_no;
                            } else {
                                if (po_no > 99 && po_no < 1000) {
                                    po_no = "0" + po_no;
                                } else {
                                    if (po_no > 9 && po_no < 100) {
                                        po_no = "00" + po_no;
                                    } else {
                                        po_no = "000" + po_no;
                                    }
                                }
                            }

                            b += '<option ' + ' value="' + v.po_id + '">' + v
                                .CCode + '/' + v.ind_prefix + '/' + po_no + '</option>';
                        });
                        $('#po_id').html(b);
                    }
                }
            });
        }
        //=========================Get PO Detail On Change of PO ===================
        $(document).on('change', '#po_id', function() {
            var po_id = $(this).val();
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    po_id: po_id,
                    'action': 'get_po_detail'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.status == 200) {
                        $('#po_date').val(response.data.po_date);
                        $('#party_id').val(response.data.party_id);
                        $('#party').val(response.data.party_name);
                        $('#address1').val(response.data.address1);
                        $('#address2').val(response.data.address2);
                        $('#address3').val(response.data.address3);
                        $('#city').val(response.data.city_name);
                        $('#state').val(response.data.state_name);
                        $('#company_id').val(response.data.company_id);
                        $('#company').val(response.data.company_name);
                        getMR(po_id);
                    }
                }
            });
        });
        //===============================Get Material Receipt No. =====================

        function getMR(po_id) {
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    po_id: po_id,
                    'action': 'get_mr_no'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.status == 200) {
                        var b = '<option>Select Recpt No</option>';
                        $.each(response.data.data, function(k, v) {
                            var receipt_no = v.receipt_no;
                            if (receipt_no > 999) {
                                receipt_no = receipt_no;
                            } else {
                                if (receipt_no > 99 && receipt_no < 1000) {
                                    receipt_no = "0" + receipt_no;
                                } else {
                                    if (receipt_no > 9 && receipt_no < 100) {
                                        receipt_no = "00" + receipt_no;
                                    } else {
                                        receipt_no = "000" + receipt_no;
                                    }
                                }
                            }
                            b += '<option ' + ' value="' + v.receipt_id + '">' + v
                                .receipt_prefix + '/' + receipt_no + '</option>';
                        });
                        $('#mr_id').html(b);
                    }
                }
            });
        }
        //===============================Get Material Receipt Date===================//
        $(document).on('change', '#mr_id', function() {
            var mr_id = $(this).val();

            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    mr_id: mr_id,
                    'action': 'get_mr_date'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#mr_date').val(response);
                }
            });
        });
        //=============================Save Bill=====================
        $(document).on('click', '#save', function() {
            var bill_id = $('#bill_id').val();
            var bill_no = $('#bill_no').val();
            var bill_date = $('#bill_date').val();
            var bill_amt = $('#bill_amt').val();
            var party_id = $('#party_id').val();
            var company_id = $('#company_id').val();
            var po_id = $('#po_id').val();
            var mr_id = $('#mr_id').val();
            var formvalid = true;
            if (bill_no == '') {
                $("#bill_no_err")
                    .html("Bill No. is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#bill_no_err").css("display", "none");
            }
            if (bill_date == '') {
                $("#bill_date_err")
                    .html("Bill Date is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#bill_date_err").css("display", "none");
            }
            if (bill_amt == '') {
                $("#bill_amt_err")
                    .html("Bill Amount is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#bill_amt_err").css("display", "none");
            }
            if (po_id == '') {
                $("#po_id_err")
                    .html("PO is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#po_id_err").css("display", "none");
            }
            if (mr_id == '') {
                $("#mr_id_err")
                    .html("MR No. is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#mr_id_err").css("display", "none");
            }
            if (formvalid == true) {
                $.ajax({
                    type: 'POST',
                    url: 'purchase_bill_curd.php',
                    data: {
                        action: 'save_bill',
                        bill_id: bill_id,
                        bill_no: bill_no,
                        bill_date: bill_date,
                        bill_amt: bill_amt,
                        party_id: party_id,
                        company_id: company_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            alert('successfull');
                            $('#bill_id').val(response.bill_id);
                            $('#bill_no').prop('disabled', true);
                            $('#bill_date').prop('disabled', true);
                            $('#bill_amt').prop('disabled', true);
                            $('#po_id').prop('disabled', true);
                            $('#mr_id').prop('disabled', true);
                            $('#save').attr('id', 'save_item');
                            getItem(mr_id);
                            $('#item_div').css('display', 'block');
                        } else {
                            alert('Something went wrong...!!');
                        }

                    }
                });
            }
        });
        //========================Get Item===========================

        function getItem(mr_id) {
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    'mr_id': mr_id,
                    'action': 'get_item_list'

                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    var x = '';

                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.rec_id + '" data-item_id="' + value
                            .item_id + '" data-category_id="' + value
                            .category_id + '" data-unit_id="' + value
                            .unit_id +
                            '" type="checkbox" name="approved" class="approved" checked></td>' +
                            '<td>' + value.item_name + '</td>' +

                            '<td>' + value.receipt_qnty + value.unit_name + '</td>' +
                            '<td><input type="text" id="billing_qty_' + value.item_id +
                            value.category_id +
                            '" value="' + value.receipt_qnty +
                            '" class="form-control"></td>' +
                            '<td><input type="text" id="rate_' + value.item_id + value
                            .category_id +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList').html(x);
                }
            });
        }
        //=====================================
        $(document).on('click', '#select_all', function() {
            if ($(this).prop('checked') == true) {
                $('.approved').prop('checked', true);
            } else {
                $('.approved').prop('checked', false);
            }
        });
        //=============================Save Item ===================
        $(document).on('click', '#save_item', function() {
            var list_array = [];
            var po_id = $('#po_id').val();
            var mr_id = $('#mr_id').val();
            var bill_id = $('#bill_id').val();
            $('.approved').each(function() {
                if ($(this).prop('checked') == true) {
                    var item_id = $(this).data('item_id');
                    var category_id = $(this).data('category_id');
                    var unit_id = $(this).data('unit_id');
                    var billing_qty = $('#billing_qty_' + item_id + category_id).val();
                    var rate = $('#rate_' + item_id + category_id).val();

                    list_array.push({
                        'item_id': item_id,
                        'category_id': category_id,
                        'unit_id': unit_id,
                        'billing_qty': billing_qty,
                        'rate': rate

                    });
                }
            });
            console.log(list_array);
            // return false;
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    'list_array': list_array,
                    'po_id': po_id,
                    'mr_id': mr_id,
                    'bill_id': bill_id,
                    'action': 'save_item'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        alert('Purchase Bill With Selected Item Created Successfully..');
                        $('#item_div').css('display', 'none');
                        $('#mr_id').prop('disabled', false);
                        $('#mr_id').prop('selectedIndex', '');
                        $('#mr_date').val('');
                        $('#save_item').attr('id', 'next');
                    } else {
                        alert('Somthing Went Wrong..Please try again..!!');
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        });
        //================================Next=============
        $(document).on('click', '#next', function() {
            var mr_id = $('#mr_id').val();
            getItem(mr_id);
            $('#next').attr('id', 'save_item');
            $('#item_div').css('display', 'block');
        });
        //===================================================
        //------------------------------Delete Billl-------------------------
        $(document).on('click', '.delete_record', function() {

            var bill_id = $(this).attr('id');
            if (confirm("Are you confirm to Delete")) {
                $.ajax({
                    url: 'purchase_bill_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_record',
                        bill_id: bill_id,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            alert(response.messages);
                            getList(start_date, end_date);
                        } else {
                            alert(response.messages);
                        }
                    }
                });
            }
        });

        //==============Close Modal And Referesh Page=================//
        $(document).on('click', '#close_modal', function() {
            $('#purchase_bill_modal').modal('hide');
            location.reload();
        });
        //==================================
        $(document).on('click', '.edit_record', function() {

            var bill_id = $(this).attr('id');
            $('#edit_bill_modal').modal('show');
            $.ajax({
                url: 'purchase_bill_curd.php',
                type: 'post',
                data: {
                    'action': 'get_data_edit',
                    bill_id: bill_id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 200) {
                        $('#edit_bill_no').val(response.bill_detail.data[0].bill_no);
                        $('#edit_bill_amt').val(response.bill_detail.data[0].bill_amt);
                        $('#edit_party').val(response.bill_detail.data[0].party_name);
                        $('#edit_company').val(response.bill_detail.data[0].company_name);
                        $('#edit_po_id').val(response.bill_detail.data[0].po_id);
                        $('#edit_bill_id').val(response.bill_detail.data[0].bill_id);

                        var x = '';

                        $.each(response.bill_detail.data, function(key, value) {
                            x = x + '<tr>' +
                                '<td><input id="' + value.item_id +
                                '" data-unit_id="' + value.unit_id +
                                '" data-receipt_id="' + value.receipt_id +
                                '" data-category_id="' + value.category_id +
                                '" type="checkbox" name="edit_r" class="edit_r" data-unique="' +
                                key + '" checked></td>' +
                                '<td>' + value.mr_no + '</td>' +
                                '<td>' + value.item_name + '</td>' +
                                '<td style="width:100px"><input type="text" id="billing_qty_' +
                                key +
                                '" value="' + value.bill_qnty +
                                '" class="form-control"></td>' +
                                '<td><input type="text" id="rate_' + key +
                                '" value="' + value.rate +
                                '" class="form-control"></td>' +
                                '</tr>';
                        });
                        $('#recordList_edit').html(x);
                    }
                }
            });
        });

        //========================
        $(document).on('click', '#select_all_edit', function() {
            if ($(this).prop('checked') == true) {
                $('.edit_r').prop('checked', true);
            } else {
                $('.edit_r').prop('checked', false);
            }
        });

        //=============================Update Item ===================
        $(document).on('click', '#update', function() {
            var list_array = [];
            var po_id = $('#edit_po_id').val();
            var bill_id = $('#edit_bill_id').val();
            var bill_no = $('#edit_bill_no').val();
            var bill_amt = $('#edit_bill_amt').val();
            $('.edit_r').each(function() {
                if ($(this).prop('checked') == true) {
                    var key = $(this).data('unique');
                    var item_id = $(this).attr('id');
                    var unit_id = $(this).data('unit_id');
                    var receipt_id = $(this).data('receipt_id');
                    var category_id = $(this).data('category_id');
                    var billing_qty = $('#billing_qty_' + key).val();
                    var rate = $('#rate_' + key).val();

                    list_array.push({
                        'item_id': item_id,
                        'category_id': category_id,
                        'unit_id': unit_id,
                        'receipt_id': receipt_id,
                        'billing_qty': billing_qty,
                        'rate': rate

                    });
                }
            });
            console.log(list_array);
            $.ajax({
                type: 'POST',
                url: 'purchase_bill_curd.php',
                data: {
                    'list_array': list_array,
                    'po_id': po_id,
                    'bill_id': bill_id,
                    'bill_no': bill_no,
                    'bill_amt': bill_amt,
                    'action': 'update_item'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        alert('Purchase Bill With Selected Item Update Successfully..');
                        location.reload();
                    } else {
                        alert('Somthing Went Wrong..Please try again..!!');
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        });

    });
    </script>

</body>

</html>