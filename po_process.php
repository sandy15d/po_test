<?php
    require_once'db_connect.php';
    include 'menu.php';
    $po_id =$_REQUEST['po_id'];
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

    tr {
        height: 2px;
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

    .modal-lg {
        width: 1200px;
    }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4" id="form1">
                    <div class="left-box">
                        <center>
                            <h4>Purchase Order Details</h4>
                        </center>
                        <hr>
                        <div class="removeMessages"></div>


                        <div class="shadow p-3 mb-5 bg-white rounded">
                            <div class="row">
                                <?php
                                $sql ="SELECT po_no,po_id,po_date, p.party_name,c.company_name,c1.company_name as shipping_name,l.location_name as delivery from tblpo 
                                JOIN party p ON p.party_id=tblpo.party_id
                                JOIN company c ON c.company_id = tblpo.company_id
                                JOIN company c1 ON c1.company_id =tblpo.shipping_id
                                JOIN location l ON l.location_id = tblpo.delivery_at
                                WHERE po_id=$po_id";
                                $query =$connect->query($sql);
                                $query =mysqli_fetch_assoc($query);
                                if ($query['po_no'] > 999) {
                                    $po_no = $query['po_no'];
                                } else {
                                    if ($query['po_no'] > 99 && $query['po_no'] < 1000) {
                                        $po_no = "0" . $query['po_no'];
                                    } else {
                                        if ($query['po_no'] > 9 && $query['po_no'] < 100) {
                                            $po_no = "00" .$query['po_no'];
                                        } else {
                                            $po_no = "000" .$query['po_no'];
                                        }
                                    }
                                }
                            ?>

                                <div class="col-md-6 mb-3">
                                    <label for="po_no">PO No.:</label>
                                    <input type="text" name="po_no" id="po_no" class="form-control"
                                        value="<?php echo $po_no;?>" style="color:#900C3F; font-weight:bold;" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="po_date">PO Date</label>
                                    <input type="text" name="po_date" id="po_date" class="form-control"
                                        value="<?php echo $query['po_date'];?>"
                                        style="color:#900C3F; font-weight:bold;" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="party_name">Party Name:</label>
                                    <input type="text" name="party_name" id="party_name" class="form-control"
                                        value="<?php echo $query['party_name'];?>"
                                        style="color:#900C3F; font-weight:bold;" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company_name">Company Name:</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control"
                                        value="<?php echo $query['company_name'];?>"
                                        style="color:#900C3F; font-weight:bold;" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_name">Shipping Name:</label>
                                    <input type="text" name="shipping_name" id="shipping_name" class="form-control"
                                        value="<?php echo $query['shipping_name'];?>"
                                        style="color:#900C3F; font-weight:bold;" />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="delivery_at">Delivery At:</label>
                                    <input type="text" name="delivery_at" id="delivery_at" class="form-control"
                                        value="<?php echo $query['delivery'];?>"
                                        style="color:#900C3F; font-weight:bold;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8" id="table1">
                    <table class="table" id="indent_list">
                        <thead>
                            <th>S.no</th>
                            <th>Indent No.</th>
                            <th style="width:100px;">Date</th>
                            <th style="width:120px;">Indent From</th>
                            <th>Supply Date</th>
                            <th>Indent By</th>
                            <th>Select</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" style="text-align:center">
                                    <h4>No Record Found, Plese Create Indent First, Then add Item.</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <button class="btn btn-lg pull pull-right btn-success" id="order_now" name="order_now"
                            style="display:none">Order Now</button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-------------------Modal-------------------------->
    <div class="modal fade" tabindex="-1" role="dialog" id="IndentModal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Process Purchase Order <span class="glyphicon glyphicon-arrow-right"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="messages"></div>
                    <input type="hidden" id="indent_id" name="indent_id">
                    <input type="hidden" id="po_id" name="po_id" value="<?php echo $po_id;?>">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Indent No.</label>
                            <input type="text" id="ind_no" name="ind_no" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent Date</label>
                            <input type="text" id="ind_date" name="ind_date" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent From</label>
                            <input type="text" id="ind_from" name="ind_from" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Est. Supply Date</label>
                            <input type="text" id="ind_supply" name="ind_supply" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent By</label>
                            <input type="text" id="ind_by" name="ind_by" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent Approver</label>
                            <input type="text" id="ind_appr" name="ind_appr" class="form-control" readonly
                                style="background:white; border:none" />
                        </div>
                    </div>
                    <hr style="border-top: 1px dashed red;">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="indent_item" class="table">
                                <thead>
                                    <th><input type="checkbox" id="select_all" checked></th>
                                    <th style="width:350px;">Item Name</th>
                                    <th style="width:150px;">Indent Qty</th>
                                    <th>Previous Unit Price</th>
                                    <th>Cur. PO. Qty</th>
                                    <th>Cur. PO. Rate</th>
                                </thead>
                                <tbody id="recordList"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="line" style="display:none;">
                        <hr style="border-top: 1px dashed red;">
                    </div>
                    <div class="row" id="dtm_div" style="display:none;">
                        <div class="col-md-12">
                            <table class="table" id="dtmlist">
                                <thead>
                                    <th>S.No</th>
                                    <th>SSATR</th>
                                    <th>Feeding</th>
                                    <th>Calc</th>
                                    <th>Description</th>
                                    <th>%age</th>
                                    <th>On Amount</th>
                                    <th>Amount</th>
                                    <th>Sub Total</th>
                                    <th>Action</th>
                                </thead>

                            </table>
                        </div>
                    </div>
                    <div class="row" id="next-phase" style="display:none;">
                        <form>
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>SSATR</th>
                                            <th>Feeding</th>
                                            <th>Calc</th>
                                            <th>Description</th>
                                            <th>%age</th>
                                            <th>On Amount</th>
                                            <th>Amount</th>
                                            <th>Total Amt.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select id="ssat" name="ssat">
                                                    <option value=""></option>
                                                    <option value="Y">Yes</option>
                                                    <option value="N">No</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="feeding" name="feeding" onchange="calculate()">
                                                    <option value="S"></option>
                                                    <option value="A">Auto</option>
                                                    <option value="M">Manual</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="calc" name="calc" onchange="calculate()">
                                                    <option value="S"></option>
                                                    <option value="P">Plus</option>
                                                    <option value="M">Minus</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="desc" name="desc" style="width:100px;">
                                            </td>
                                            <td>
                                                <input type="text" id="percent" name="percent" style="width:80px;"
                                                    onchange="calculate()">
                                            </td>
                                            <td>
                                                <input type="text" id="on_amt" name="on_amt" style="width:80px;"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="text" id="amt" name="amt" style="width:80px;" disabled
                                                    onchange="calculate()">
                                            </td>
                                            <td>
                                                <input type="text" id="tot_amt" name="tot_amt" style="width:80px;"
                                                    readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="pull pull-right">
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                    <button type="button" id="dutyntax" name="dutyntax" class="btn btn-success"
                                        style="margin-right:20px;">Add New</button>
                                </div>

                            </div>

                        </form>
                        <div id="line" style="display:none;">
                            <hr style="border-top: 1px dashed red;">
                        </div>
                        <div class="col-md-6">
                            <label>Specification</label>
                            <input type="text" id="spec" name="spec" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary" id="processBtn">Save changes</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removeItemModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Company</h4>
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
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    //-------------------Get Approved Indent List--------------------//
    manageIndentTable = $("#indent_list").DataTable({
        "ajax": {
            url: "purchase_order_curd.php",
            data: {
                action: 'getIndent'
            },
            type: 'post'
        },

        "order": []
    });

    //--------------------------Get Indent Details and Item Details---------------------------------//
    function processPO(indent_id = null) {
        if (indent_id) {
            $.ajax({
                type: 'post',
                url: 'purchase_order_curd.php',
                data: {
                    action: 'Get_Indent_Details',
                    indent_id: indent_id
                },
                dataType: 'json',
                success: function(response) {
                    //------------------------Indent Detail--------------------------
                    var a = response[0]['indent_detail']['indent_no'];
                    if (a > 999) {
                        var ind_no = a;
                    } else {
                        if (a > 99 && a < 1000) {
                            ind_no = "0" + a;
                        } else {
                            if (a > 9 && a < 100) {
                                ind_no = "00" + a;
                            } else {
                                ind_no = "000" + a;
                            }
                        }
                    }
                    ind_no = response[0]['indent_detail']['ind_prefix'] + '/' + ind_no;
                    $('#ind_no').val(ind_no);
                    $('#ind_date').val(response[0]['indent_detail']['indent_date']);
                    $('#ind_from').val(response[0]['indent_detail']['location_name']);
                    $('#ind_supply').val(response[0]['indent_detail']['supply_date']);
                    $('#ind_by').val(response[0]['indent_detail']['staff_name']);
                    $('#ind_appr').val(response[0]['indent_detail']['approver']);
                    $('#indent_id').val(response[0]['indent_detail']['indent_id']);
                    //-----------------------Item Detail----------------------------
                    var x = '';
                    var i = 1;
                    $.each(response[0]['item_detail'], function(key, value) {

                        x = x + '<tr>' +
                            '<td><input checked id="' + parseInt(i) +
                            '" type="checkbox" data-indent_id="' + value.indent_id +
                            '" data-item_id="' + value.item_id +
                            '"data-unit_id="' + value.unit_id +
                            '"data-remark ="' + value.remark +
                            '" class="select_item"></td>' +
                            '<td>' + value.item_name + '</td>' +
                            '<td>' + value.appr_qnty + ' ' + value.unit_name + '</td>' +
                            '<td>' + value.pre_rate + '</td>' +
                            '<td><input type="number" class="form-control qty" id="cur_qty_' +
                            parseInt(
                                i) +
                            '"></td>' +
                            '<td><input type="number" class="form-control rate" id="cur_rate_' +
                            parseInt(
                                i) + '"></td>' +
                            '</tr>';
                        i++;
                    });
                    $('#recordList').html(x);
                }
            });
        } else {
            alert('Error: Refresh the page again');
        }
    }


    //------------process btn----------------
    $(document).on('click', '#processBtn', function() {
        var checkedValue = [];
        var indent_id = $('#indent_id').val();
        var po_id = $('#po_id').val();

        $('.select_item').each(function() {
            if ($(this).prop('checked') == true) {
                var id = $(this).attr('id');
                checkedValue.push({
                    'indent_id': $(this).data('indent_id'),
                    'item_id': $(this).data('item_id'),
                    'unit_id': $(this).data('unit_id'),
                    'remark': $(this).data('remark'),
                    'cur_qty': $('#cur_qty_' + id).val(),
                    'cur_rate': $('#cur_rate_' + id).val()
                });
            }
        });

        $.ajax({
            type: 'post',
            url: 'purchase_order_curd.php',
            data: {
                'checkedValue': checkedValue,
                'indent_id': indent_id,
                'po_id': po_id,
                'action': 'save_po_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    $('.qty').prop('disabled', true);
                    $('.rate').prop('disabled', true);
                    $('#next-phase').css('display', 'block');
                    $('#line').css('display', 'block');
                    //Change the processBtn Id for next phase work//
                    $('#processBtn').attr('id', 'optionBtn');
                } else {
                    alert('something went wrong...!! please try again latter');
                }
            },
            complete: function() {
                get_amt();
            }
        });


    });


    $(document).on('click', '#select_all', function() {
        if ($(this).prop('checked') == true) {
            $('.select_item').prop('checked', true);
        } else {
            $('.select_item').prop('checked', false);
        }
    });

    $(document).on('change', '#feeding', function() {
        var feeding = $(this).val();
        if (feeding == 'A') {
            $('#amt').prop('disabled', true);
            $('#percent').prop('disabled', false);
        } else {
            $('#amt').prop('disabled', false);
            $('#percent').prop('disabled', true);
        }
    });

    function calculate() {
        var feeding = $('#feeding').val();
        var calc = $('#calc').val();
        var percent = parseFloat($('#percent').val());
        var on_amt = parseFloat($('#on_amt').val());
        var amt = parseFloat($('#amt').val());
        if (isNaN(percent)) {
            percent = 0;
        }
        if (isNaN(on_amt)) {
            on_amt = 0;
        }
        if (isNaN(amt)) {
            amt = 0;
        }


        if (feeding == 'A') {
            if (calc == 'P') {
                var getPerc = parseFloat(parseFloat(on_amt) * parseFloat(percent) / parseFloat(100));
                var total = parseFloat(parseFloat(on_amt) + parseFloat(getPerc));
                $('#amt').val(getPerc);
                $('#tot_amt').val(total);
            } else if (calc == 'M') {
                var getPerc = parseFloat(parseFloat(on_amt) * parseFloat(percent) / parseFloat(100));
                var total = parseFloat(parseFloat(on_amt) - parseFloat(getPerc));
                $('#amt').val(getPerc);
                $('#tot_amt').val(total);
            }
        } else if (feeding == 'M') {
            if (calc == 'P') {
                var total = parseFloat(parseFloat(on_amt) + parseFloat(amt));
                $('#tot_amt').val(total);
            } else if (calc == 'M') {
                var total = parseFloat(parseFloat(on_amt) - parseFloat(amt));
                $('#tot_amt').val(total);
            }
        }

    }

    function get_amt() {
        var po_id = $('#po_id').val();
        $.ajax({
            type: 'post',
            url: 'purchase_order_curd.php',
            data: {
                po_id: po_id,
                action: 'Get_Amt'
            },
            dataType: 'json',
            success: function(response) {
                $('#on_amt').val(response.amt);
                $('#tot_amt').val(response.amt);
            },
        });
    }

    function get_amt1() {
        var po_id = $('#po_id').val();
        $.ajax({
            type: 'post',
            url: 'purchase_order_curd.php',
            data: {
                po_id: po_id,
                action: 'Get_Amt'
            },
            dataType: 'json',
            success: function(response) {
                $('#ssat').prop('selectedIndex', '');
                $('#feeding').prop('selectedIndex', '');
                $('#calc').prop('selectedIndex', '');
                $('#percent').val('');
                $('#amt').val('');
                $('#desc').val('')
                $('#on_amt').val(response.amt);
                $('#tot_amt').val(response.amt);
            },
        });
    }
    $(document).on('click', '#dutyntax', function() {
        var ssat = $('#ssat').val();
        var feeding = $('#feeding').val();
        var calc = $('#calc').val();
        var percent = $('#percent').val();
        var on_amt = $('#on_amt').val();
        var amt = $('#amt').val();
        var total_amt = $('#tot_amt').val();
        var po_id = $('#po_id').val();
        var desc = $('#desc').val();
        $.ajax({
            type: 'post',
            url: 'purchase_order_curd.php',
            data: {
                action: 'save_dtm',
                po_id: po_id,
                ssat: ssat,
                feeding: feeding,
                calc: calc,
                desc: desc,
                percent: percent,
                on_amt: on_amt,
                amt: amt,
                total_amt: total_amt
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    $('#dtm_div').css('display', 'block');
                    getList();
                    get_amt1();
                }


            },
            complete: function() {

            }
        });
    });

    function getList() {
        var po_id = $('#po_id').val();
        $("#dtmlist").DataTable({
            "ajax": {
                url: "purchase_order_curd.php",
                data: {
                    action: 'get_dtm_list',
                    'po_id': po_id
                },
                type: 'post'
            },
            "oLanguage": {
                "sEmptyTable": "No Record Found.. Please select another location"
            },
            paging: true,

            "order": []
        });
        $('#dtmlist').DataTable().destroy();
    }

    $(document).on('click', '#optionBtn', function() {
        var po_id = $('#po_id').val();
        var indent_id = $('#indent_id').val();
        var spec = $('#spec').val();
        $.ajax({
            type: 'post',
            url: 'purchase_order_curd.php',
            data: {
                action: 'final_update',
                po_id: po_id,
                indent_id: indent_id,
                spec: spec
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    alert('Purchase Order Completed Successfully');
                    window.open("newpurchaseorder.php?po_id=" + po_id, '_blank');
                    window.location.href = "purchase_order.php";

                } else {
                    alert('Something went wrong. Please try again');
                    location.reload();
                }

            }
        });
    });

    function removeItem(rec_id = null) {
        if (rec_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {

                $.ajax({
                    url: 'purchase_order_curd.php',
                    type: 'post',
                    async: false,
                    data: {
                        'action': 'delete_dtm',
                        rec_id: rec_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            $("#removeItemModal").modal('hide');


                        } else {
                            alert('Something went wrong');
                        }

                    },
                    complete: function() {
                        getList();
                        get_amt();
                    }
                });
            }); // click remove btn
        } else {
            alert('Error: Refresh the page again');
        }
    }
    </script>