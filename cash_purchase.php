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
                        data-target="#cash_purchase_modal">
                        <span class="glyphicon glyphicon-plus-sign"></span> New Cash Purchase</button>
                </div>

            </div>
            <div class="row" style="padding-top: 10px;">

                <div class="col-md-1"></div>
                <div class="col-md-10 ">
                    <div class="removeMessages"></div>

                    <table class="table" id="cash_purchase_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Memo No.</th>
                                <th>Memo Date</th>
                                <th>Memo Amount</th>
                                <th style="width:300px;">Company</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="cash_purchase_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Cash Purchase
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="hidden" id="txn_id">
                                <label for="txn_no">Txn No.:</label>
                                <input type="text" id="txn_no" class="form-control" style="color:blue;" disabled>
                            </div>
                            <div class="col-md-2">
                                <label for="memo_no">Memo No.:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="memo_no" class="form-control" style="color:blue;"
                                    autocomplete="off">
                                <div id="memo_no_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="memo_date">Memo Date:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="date" id="memo_date" class="form-control"
                                    value="<?php echo date('Y-m-d');?>" style="color:blue;">

                            </div>
                            <div class="col-md-3">
                                <label for="particulars">Particulars:<b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="particulars" class="form-control" autocomplete="off"
                                    style="color:blue;">
                                <div id="particulars_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="purchase_amount">Pur. Amount:<b
                                        style="color:red;font-size:14px;">*</b></label>
                                <input type="text" id="purchase_amount" class="form-control" style="color:blue;"
                                    autocomplete="off">
                                <div id="purchase_amount_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="company">Company:<b style="color:red;font-size:14px;">*</b></label>
                                <select id="company" class="form-control" style="color:blue;">
                                    <option value="">Select..</option>
                                    <?php 
                                    
                                        $sql="SELECT * FROM company ORDER BY company_name";
                                        $query =$connect->query($sql);
                                        foreach ($query as $row) {
                                            echo'<option value="'.$row['company_id'].'">'.$row['company_name'].'</option>';
                                        }
                                    ?>
                                </select>
                                <div id="company_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="location">Location:<b style="color:red;font-size:14px;">*</b></label>
                                <select id="location" class="form-control" style="color:blue;">
                                    <option value="">Select..</option>
                                    <?php 
                                    
                                        $sql="SELECT * FROM location ORDER BY location_name";
                                        $query =$connect->query($sql);
                                        foreach ($query as $row) {
                                            echo'<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                        }
                                    ?>
                                </select>
                                <div id="location_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="indent_no">Indent No:<b style="color:red;font-size:14px;">*</b></label>
                                <select id="indent_no" class="form-control">
                                    <option>Select..</option>
                                </select>
                            </div>
                            <!--     <div class="col-md-2">
                                <label for="indent_date">Indent Date:</label>
                                <input type="text" id="indent_date" class="form-control" disabled>
                            </div> -->
                        </div>
                        <div id="line" style="display:none;">

                            <div class="row" id="div_table" style="display:none">
                                <hr style="border-top: 1px dashed red;">
                                <div class="col-md-1"></div>
                                <div class="col-md-10">
                                    <table class="table nowrap" id="cash_item_list">
                                        <thead>
                                            <th><input type="checkbox" id="select_all" checked></th>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                        </thead>
                                        <tbody id="recordList"></tbody>
                                    </table>
                                </div>
                            </div>
                            <hr style="border-top: 1px dashed red;">
                            <!--                             <div class="row">

                                <div class="col-md-5">
                                    <label for="item">Item:<b style="color:red;font-size:14px;">*</b></label>
                                    <select id="item" class="form-control">
                                        <option>Select..</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" id="unit">
                                    <label for="unit_name">Unit:</label>
                                    <input type="text" id="unit_name" class="form-control" disabled>
                                </div>

                            </div> -->
                            <!--                             <div class="row">
                                <div class="col-md-2">
                                    <label for="stock">Stock On Date</label>
                                    <input type="text" id="stock" class="form-control" autocomplete='off' disabled>
                                </div>
                                <div class="col-md-3">
                                    <label for="qty">Item Qty:<b style="color:red;font-size:14px;">*</b></label>
                                    <input type="text" id="qty" class="form-control" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <label for="rate">Rate:<b style="color:red;font-size:14px;">*</b></label>
                                    <input type="text" id="rate" class="form-control">
                                </div>
                            </div> -->
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

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {

        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        getCashMemoList(start_date, end_date);

        function getCashMemoList(start_date, end_date) {
            $('#cash_purchase_table').DataTable({
                "bLengthChange": false,
                "searching": false,
                destroy: true,
                'ajax': {
                    'url': 'cash_purchase_curd.php',
                    'data': {
                        'action': 'get_cash_memo',
                        'start_date': start_date,
                        'end_date': end_date
                    },
                    type: 'post'
                },
                paging: true,

                "order": []

            });
            $('#cash_purchase_table').DataTable().destroy();
        }

        $(document).on('click', '#search', function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            getCashMemoList(start_date, end_date);
        });

        //Create Cash Memo//
        $(document).on('click', '#save', function() {
            var txn_id = $('#txn_id').val();
            var memo_no = $('#memo_no').val();
            var memo_date = $('#memo_date').val();
            var particulars = $('#particulars').val();
            var purchase_amount = $('#purchase_amount').val();
            var company = $('#company').val();
            var location = $('#location').val();
            var indent_id = $('#indent_no').val();
            formvalid = true;

            if (memo_no == "") {
                $("#memo_no_err")
                    .html("Memo No. is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#memo_no_err").css("display", "none");
            }
            if (particulars == "") {
                $("#particulars_err")
                    .html("Purticulars  is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#particulars_err").css("display", "none");
            }
            if (purchase_amount == "") {
                $("#purchase_amount_err")
                    .html("Purchase Amount is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#purchase_amount_err").css("display", "none");
            }
            if (company == "") {
                $("#company_err")
                    .html("Company Name is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#company_err").css("display", "none");
            }
            if (location == "") {
                $("#location_err")
                    .html("Location is Required..!")
                    .css("display", "block");
                formvalid = false;
            } else {
                $("#location_err").css("display", "none");
            }
            if (formvalid) {
                $.ajax({
                    type: 'POST',
                    url: 'cash_purchase_curd.php',
                    data: {
                        'action': 'create',
                        'txn_id': txn_id,
                        'memo_no': memo_no,
                        'memo_date': memo_date,
                        'particulars': particulars,
                        'purchase_amount': purchase_amount,
                        'company': company,
                        'location': location
                    },
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.success == true) {
                            if (response.txn_id > 999) {
                                var txn_no = response.txn_id;
                            } else {
                                if (response.txn_id > 99 && response.txn_id < 1000) {
                                    var txn_no = "0" + response.txn_id;
                                } else {
                                    if (response.txn_id > 9 && response.txn_id < 100) {
                                        var txn_no = "00" + response.txn_id;
                                    } else {
                                        var txn_no = "000" + response.txn_id;
                                    }
                                }
                            }
                            $('#txn_id').val(response.txn_id);
                            $('#txn_no').val(txn_no);
                            $('#line').css('display', 'block');
                            $('#div_table').css('display', 'block');

                            /*    //  getIndent(memo_date, location);
                               getList(response.txn_id); */
                            getIndentItem(indent_id);
                            $('#save').attr('id', 'save_item');

                        } else {
                            alert(response.messages)
                        }
                    },
                    error: function() {},
                    complete: function() {

                    },
                });
            }
        });
        //==============================Get Indent (Location Base and Memo Date Based)==========================//

        $(document).on('change', '#location', function() {
            var memo_date = $('#memo_date').val();
            var location = $('#location').val();
            $.ajax({
                type: 'POST',
                url: 'cash_purchase_curd.php',
                data: {
                    memo_date: memo_date,
                    location: location,
                    'action': 'get_indent'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.status == 200) {
                        var b = '<option value="">Select Indent</option>';
                        $.each(response.data.data, function(k, v) {
                            var in_no = v.indent_no;
                            if (in_no > 999) {
                                in_no = in_no;
                            } else {
                                if (in_no > 99 && in_no < 1000) {
                                    in_no = "0" + in_no;
                                } else {
                                    if (in_no > 9 && in_no < 100) {
                                        in_no = "00" + in_no;
                                    } else {
                                        in_no = "000" + in_no;
                                    }
                                }
                            }
                            b += '<option ' + ' value="' + v.indent_id + '">' + v
                                .ind_prefix + '/' + in_no + ' ~~ ' + v
                                .indent_date +
                                '</option>';
                        });
                        $('#indent_no').html(b);
                    }
                }
            });
        });


        //====================================Get Indent Item List on Indent Change===========================//
        function getIndentItem(indent_id) {
            $.ajax({
                type: 'POST',
                url: 'cash_purchase_curd.php',
                data: {
                    indent_id: indent_id,
                    'action': 'get_indent_item'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    var x = '';
                    // $('#indent_id').val(response.data[0].indent_id);
                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.item_id + '" data-category_id="' +
                            value.category_id + '" data-unit_id="' +
                            value.unit_id +
                            '" type="checkbox" name="collect_all" class="collect_all" checked></td>' +
                            '<td>' + value.item_name + '</td>' +
                            '<td style="width:100px"><input type="text" id="item_qty_' +
                            value.item_id + value.category_id + '" value="' + value.qty +
                            '" class="form-control"></td>' +
                            '<td style="width:100px"><input type="text" id="item_rate_' +
                            value.item_id + value.category_id +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList').html(x);
                }
            });
        }


        //=======================================================================

        $(document).on('click', '#select_all', function() {
            if ($(this).prop('checked') == true) {
                $('.collect_all').prop('checked', true);
            } else {
                $('.collect_all').prop('checked', false);
            }
        });
        //==========================Save Item====================//
        $(document).on('click', '#save_item', function() {
            var txn_id = $('#txn_id').val();
            var indent_id = $('#indent_no').val();
            var location = $('#location').val();
            var memo_date = $('#memo_date').val();

            var list_array = [];
            $('.collect_all').each(function() {
                if ($(this).prop('checked') == true) {
                    var item_id = $(this).attr('id');
                    var category_id = $(this).data('category_id');
                    var unit_id = $(this).data('unit_id');
                    var item_qty = $('#item_qty_' + item_id + category_id).val();
                    var item_rate = $('#item_rate_' + item_id + category_id).val();

                    list_array.push({
                        'item_id': item_id,
                        'category_id': category_id,
                        'unit_id': unit_id,
                        'item_qty': item_qty,
                        'item_rate': item_rate


                    });
                }
            });

            // console.log(list_array);
            //return false;
            $.ajax({
                type: 'post',
                url: 'cash_purchase_curd.php',
                data: {
                    'list_array': list_array,
                    'action': 'save_item',
                    'txn_id': txn_id,
                    'indent_id': indent_id,
                    'location': location,
                    'memo_date': memo_date
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {


                        alert('successfull');
                        //location.reload();
                        // $('#div_table').css('display', 'block');

                    } else {
                        alert(response.messages)
                    }

                },
                error: function() {},
                complete: function() {
                    // getList(txn_id);
                },

            });

        });

        //=====================Get Cash Purchase Item List============//
        function getList(txn_id) {
            $('#cash_item_list').DataTable({
                "bLengthChange": false,
                "searching": true,
                destroy: true,
                'ajax': {
                    'url': 'cash_purchase_curd.php',
                    'data': {
                        'action': 'item_list',
                        'txn_id': txn_id
                    },
                    type: 'post'
                },
                "oLanguage": {
                    "sEmptyTable": "No Record Found.. Please select another location"
                },
                paging: true,

                "order": []

            });
            $('#cash_item_list').DataTable().destroy();
        }
        //==============Close Modal And Referesh Page=================//
        $(document).on('click', '#close_modal', function() {
            $('#cash_purchase_modal').modal('hide');
            location.reload();
        });
        //============================Remove Item==================//

        $(document).on('click', '.delete', function() {
            var rec_id = $(this).attr('id');
            var txn_id = $('#txn_id').val();
            var memo_date = $('#memo_date').val();
            var location = $('#location').val();
            $.ajax({
                url: 'cash_purchase_curd.php',
                type: 'post',
                data: {
                    'action': 'delete_item',
                    rec_id: rec_id,
                    txn_id: txn_id,
                    memo_date: memo_date,
                    location: location
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        getList(txn_id);
                    } else {
                        alert('Something went wrong...Please Try Again...!!!');
                    }
                }
            });
        });
        //------------------------------Delete Cash Memo-------------------------
        $(document).on('click', '.delete_record', function() {

            var txn_id = $(this).attr('id');
            if (confirm("Are you confirm to Delete")) {
                $.ajax({
                    url: 'cash_purchase_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_record',
                        txn_id: txn_id,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            alert(response.messages);
                            getCashMemoList(start_date, end_date);
                        } else {
                            alert(response.messages);
                        }
                    }
                });
            }
        });


        //------------------Edit Cash Purchase---------------------------//
        $(document).on('click', '.edit', function() {
            var txn_id = $(this).attr('id');
            $('#cash_purchase_modal').modal('show');
            $.ajax({
                url: 'cash_purchase_curd.php',
                type: 'post',
                data: {
                    'action': 'edit_cash_purchase',
                    'txn_id': txn_id
                },
                dataType: 'json',
                success: function(response) {
                    var txn_no = response.txn_id;
                    if (txn_no > 999) {
                        var txn_no = txn_no;
                    } else {
                        if (txn_no > 99 && txn_no < 1000) {
                            var txn_no = "0" + txn_no;
                        } else {
                            if (txn_no > 9 && txn_no < 100) {
                                var txn_no = "00" + txn_no;
                            } else {
                                var txn_no = "000" + txn_no;
                            }
                        }
                    }
                    $('#txn_id').val(response.txn_id);
                    $('#txn_no').val(txn_no);
                    $('#memo_no').val(response.memo_no);
                    $('#memo_date').val(response.memo_date);
                    $('#particulars').val(response.particulars);
                    $('#purchase_amount').val(response.memo_amt);
                    $('#company').prepend("<option value='" + response.company_id +
                        "' selected>" + response
                        .company_name + "</option>");
                    $('#location').prepend("<option value='" + response.location_id +
                        "' selected>" + response
                        .location_name + "</option>");
                } // /success
            }); // /fetch selected member info
        });
    });
    </script>

</body>

</html>