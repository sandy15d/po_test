<?php include 'menu.php';
include 'db_connect.php';
/*-------------------------------*/
$sql_user = mysql_query("SELECT po1,po2,po3,po4 FROM users WHERE uid=" . $_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
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

        .modal-lg {
            width: 1200px;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 3px;
        }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">
            <input type="hidden" id="po_id" name="po_id">
            <div class="row">
                <div class="col-md-12">
                    <div class="removeMessages"></div>
                    <?php if ($row_user['po1'] == 1) { ?>
                        <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#pomodal">
                            <span class="glyphicon glyphicon-plus-sign"></span> Add New PO</button>
                    <?php } ?>
                    <div class="col-md-2 pull pull-right">
                        <select id="search" name="search" class="form-control">
                            <option value="unsent" selected>Unsent Items</option>
                            <option value="sent">Sent Items</option>
                        </select>
                        <div style="padding:5px;"></div>
                    </div>


                    <table class="table" id="puchaseordertable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>PO No.</th>
                                <th>PO Date</th>
                                <th>Party Name</th>
                                <th>Company Name</th>
                                <th>Action</th>
                                <th id="p_process">Print</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>
        </div>
    </section>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="pomodal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add New Purchase Order
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <label>PO No.</label>
                                <input type="text" id="po_no" name="po_no" class="form-control" readonly />
                            </div>
                            <div class="col-md-4">
                                <label>PO Date<b style="color:red;font-size:20px;">*</b></label>
                                <input type="date" id="po_date" name="po_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" />
                                <div id="po_date_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="new_indent_id">Indent<b style="color:red;font-size:20px;">*</b></label>
                                <select id="new_indent_id" class="form-control">
                                    <option value="">Select Indent</option>
                                    <?php
                                    $sql = "SELECT * FROM tbl_indent
                                 INNER JOIN location ON tbl_indent.order_from = location.location_id
                                 INNER JOIN staff ON tbl_indent.order_by = staff.staff_id
                                 WHERE (indent_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "'
                                 AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')
                                 AND appr_status='S' AND indent_id
                                 IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1)
                                 ORDER BY location_name, indent_date, indent_id";
                                    $query = $connect->query($sql);
                                    while ($row = $query->fetch_assoc()) {
                                        if ($row['indent_no'] > 99 && $row['indent_no'] < 1000) {
                                            $in_no = "0" . $row['indent_no'];
                                        } else {
                                            if ($row['indent_no'] > 9 && $row['indent_no'] < 100) {
                                                $in_no = "00" . $row['indent_no'];
                                            } else {
                                                $in_no = "000" . $row['indent_no'];
                                            }
                                        }
                                        $indent_no = $row['ind_prefix'] . '/' . $in_no;
                                        echo '<option value="' . $row['indent_id'] . '">' . $indent_no .'  ~~  ' .date('d-m-Y',strtotime($row['indent_date'])).'</option>';
                                    }
                                    ?>
                                </select>
                                <div id="new_indent_id_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="row" id="item_detail">
                            <div class="col-lg-12" style="max-height:200px;overflow:scroll;">
                                <table class="table table-bordered table-striped" id="item_list" style="width: 100%;">
                                    <thead>

                                        <th>Item Name</th>
                                        <th>Item Description</th>
                                        <th>Indent Qty</th>
                                        <th>Unit</th>

                                    </thead>
                                    <tbody id="itmlist"></tbody>
                                </table>
                            </div>
                        </div>
                        <div id="company_detail_div">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Party Name<b style="color:red;font-size:20px;">*</b></label>
                                    <select id="party_name" name="party_name" class="form-control">
                                        <option value="">---Select Party---</option>
                                        <?php
                                        $sql = "SELECT * FROM party ORDER BY party_name";
                                        $query = $connect->query($sql);
                                        while ($row = $query->fetch_assoc()) {

                                            echo '<option value="' . $row['party_id'] . '">' . $row['party_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="party_name_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>Company Name<b style="color:red;font-size:20px;">*</b></label>
                                    <select id="company_name" name="company_name" class="form-control">
                                        <option value="">---Select Company---</option>
                                        <?php
                                        $sql = "SELECT * FROM company ORDER BY company_name";
                                        $query = $connect->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['company_id'] . '">' . $row['company_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="company_name_err" style="color:red; display:none;"></div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-3">
                                    <label>Address 1:</label>
                                    <input type="text" id="address1" name="address1" class="form-control" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>Address 2:</label>
                                    <input type="text" id="address2" name="address2" class="form-control" readonly />

                                </div>
                                <div class="col-md-6">
                                    <label>Ship To:<b style="color:red;font-size:20px;">*</b></label>
                                    <select id="ship_to" name="ship_to" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1">Itself</option>
                                        <option value="2">At Branch</option>
                                    </select>
                                    <div id="ship_to_err" style="color:red; display:none;"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Address 3:</label>
                                    <input type="text" id="address3" name="address3" class="form-control" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>City</label>
                                    <input type="text" id="city" name="city" class="form-control" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label>Shipping Name</label>
                                    <select id="shipping_name1" id="shipping_name1" class="form-control" readonly>

                                    </select>
                                    <select id="shipping_name" name="shipping_name" class="form-control" style="display:none">
                                        <?php
                                        $sql = "SELECT * FROM  company ORDER BY company_name";
                                        $query = $connect->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['company_id'] . '">' . $row['company_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>State :</label>
                                    <input type="text" id="state" name="state" class="form-control" readonly />

                                </div>
                                <div class="col-md-3">
                                    <label>Contact :</label>
                                    <input type="text" id="contact" name="contact" class="form-control" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>Shipping Address 1:</span></label>
                                    <input type="text" id="ship_address1" name="ship_address1" class="form-control" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>Shipping Address 2:</span></label>
                                    <input type="text" id="ship_address2" name="ship_address2" class="form-control" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>TIN No.</label>
                                    <input type="text" id="tin" name="tin" class="form-control" readonly />
                                </div>
                                <div class="col-md-3">
                                    <label>Vendor Reference</label>
                                    <input type="text" id="ref" name="ref" class="form-control" />
                                </div>


                                <div class="col-md-3">
                                    <label>Shipping Address 3:</label>
                                    <input type="text" id="ship_address3" name="ship_address3" class="form-control" readonly />

                                </div>

                                <div class="col-md-3">

                                    <label>City</label>
                                    <input type="text" id="company_city" name="company_city" class="form-control" readonly />

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Terms & Condition:</label>
                                    <?php
                                    $sql = "SELECT * FROM tac";
                                    $query = $connect->query($sql);
                                    $tac = mysqli_fetch_array($query);
                                    ?>
                                    <textarea rows="4" cols="57" name="terms" id="terms" class="form-control"><?php echo $tac['tac_detail']; ?> </textarea>
                                </div>

                                <div class="col-md-2">
                                    <label>State</label>
                                    <input type="text" id="company_state" name="company_state" class="form-control" readonly />
                                </div>
                                <div class="col-md-2">
                                    <label>Shipping Method</label>
                                    <input type="text" id="ship_method" name="ship_method" class="form-control" />

                                </div>
                                <div class="col-md-2">
                                    <label>Work Order:</label>
                                    <select id="work_order" name="work_order" class="form-control">
                                        <option value="N">No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Delivery At:<b style="color:red;font-size:20px;">*</b></label>
                                    <select id="delivery_at" name="delivery_at" class="form-control">
                                        <option value="">----Select----</option>
                                        <?php
                                        $sql = "SELECT * FROM location ORDER BY location_name";
                                        $query = $connect->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['location_id'] . '">' . $row['location_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="delivery_at_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Delivery Date<b style="color:red;font-size:20px;">*</b></label>
                                    <input type="date" id="ship_date" name="ship_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" />
                                    <div id="ship_date_err" style="color:red; display:none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <?php if ($row_user['po1'] == 1) { ?>
                            <button type="button" class="btn btn-primary" name="save" id="save">Save changes</button>
                        <?php } ?>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="removePOModal">
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

    <!-------------------Modal-------------------------->
    <div class="modal fade" tabindex="-1" role="dialog" id="IndentModal">
        <div class="modal-dialog modal-lg" role="document" style="max-height:500px; overflow:scroll;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Process Purchase Order <span class="glyphicon glyphicon-arrow-right"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="messages"></div>
                    <input type="hidden" id="indent_id" name="indent_id">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Indent No.</label>
                            <input type="text" id="ind_no" name="ind_no" class="form-control" readonly style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent Date</label>
                            <input type="text" id="ind_date" name="ind_date" class="form-control" readonly style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent From</label>
                            <input type="text" id="ind_from" name="ind_from" class="form-control" readonly style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Est. Supply Date</label>
                            <input type="text" id="ind_supply" name="ind_supply" class="form-control" readonly style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent By</label>
                            <input type="text" id="ind_by" name="ind_by" class="form-control" readonly style="background:white; border:none" />
                        </div>
                        <div class="col-md-2">
                            <label>Indent Approver</label>
                            <input type="text" id="ind_appr" name="ind_appr" class="form-control" readonly style="background:white; border:none" />
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
                                                <input type="text" id="percent" name="percent" style="width:80px;" onchange="calculate()">
                                            </td>
                                            <td>
                                                <input type="text" id="on_amt" name="on_amt" style="width:80px;" readonly>
                                            </td>
                                            <td>
                                                <input type="text" id="amt" name="amt" style="width:80px;" disabled onchange="calculate()">
                                            </td>
                                            <td>
                                                <input type="text" id="tot_amt" name="tot_amt" style="width:80px;" readonly>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="pull pull-right">
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                    <button type="button" id="dutyntax" name="dutyntax" class="btn btn-success" style="margin-right:20px;">Add New</button>
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

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        var search = $('#search').val();
        getList(search);

        $(document).on('change', '#new_indent_id', function() {
            var indent_id = $(this).val();
            getItemList(indent_id);

        });

        function getItemList(indent_id) {
            $.ajax({
                type: 'post',
                url: 'indent_approval_curd.php',
                data: {
                    action: 'get_item_list',
                    indent_id: indent_id
                },
                dataType: 'json',
                success: function(response) {
                    var x = '';
                    $('#indent_id').val(response.data[0].indent_id);
                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +

                            '<td>' + value.item_name + '</td>' +
                            '<td>' + value.desc + '</td>' +
                            '<td>' + value.qty + '</td>' +
                            '<td>' + value.unit_name + '</td>' +

                            '</tr>';
                    });
                    $('#itmlist').html(x);
                }
            });
        }


        function getList(search) {
            $('#puchaseordertable').DataTable({
                "bLengthChange": false,
                "searching": true,
                destroy: true,
                'ajax': {
                    'url': 'purchase_order_curd.php',
                    'data': {
                        'action': 'get_po',
                        'search': search
                    },
                    type: 'post'
                },
                paging: true,

                "order": []

            });
            $('#purchaseordertable').DataTable().destroy();
        }
        //-----------------------------
        $(document).on('change', '#search', function() {
            var search = $(this).val();
            getList(search);
        });
        //------------------------------
        $(document).on('change', '#ship_to', function() {
            var ship = $(this).val();
            if (ship == 2) {
                $('#shipping_name').css('display', 'block');
                $('#shipping_name1').css('display', 'none');

            }
            var company_name = $('#company_name').val();
            $.ajax({
                type: 'post',
                url: 'purchase_order_curd.php',
                data: {
                    ship: ship,
                    company_name: company_name,
                    action: 'get_company_detail'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#shipping_name1').append("<option value='" + response.company_id +
                        "' selected>" +
                        response.company_name + "</option>");
                    $('#ship_address1').val(response.c_address1);
                    $('#ship_address2').val(response.c_address2);
                    $('#ship_address3').val(response.c_address3);
                    $('#company_city').val(response.city_name);
                    $('#company_state').val(response.state_name);
                }
            });

        });

        //----------------------------------
        $(document).on('change', '#shipping_name', function() {
            var company_name = $('#shipping_name').val();
            $.ajax({
                type: 'post',
                url: 'purchase_order_curd.php',
                data: {
                    company_name: company_name,
                    action: 'get_company_detail1'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#shipping_name1').append("<option value='" + response.company_id +
                        "' selected>" +
                        response.company_name + "</option>");
                    $('#ship_address1').val(response.c_address1);
                    $('#ship_address2').val(response.c_address2);
                    $('#ship_address3').val(response.c_address3);
                    $('#company_city').val(response.city_name);
                    $('#company_state').val(response.state_name);
                }
            });

        });
        //--------------------------------

        $(document).on('change', '#party_name', function() {
            var party_name = $(this).val();

            $.ajax({
                type: 'post',
                url: 'purchase_order_curd.php',
                data: {
                    party_name: party_name,
                    action: 'get_party_detail'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#address1').val(response.address1);
                    $('#address2').val(response.address2);
                    $('#address3').val(response.address3);
                    $('#city').val(response.city_name);
                    $('#state').val(response.state_name);
                    $('#tin').val(response.tin);
                    $('#contact').val(response.contact_person);
                }
            });

        });
        //------------------------
        $(document).on('click', '#save', function() {
            var po_id = $('#po_id').val();
            var indent_id = $('#new_indent_id').val();
            var po_no = $('#po_no').val();
            var po_date = $('#po_date').val();
            var party_name = $('#party_name').val();
            var ref = $('#ref').val();
            var terms = $('#terms').val();
            var company_name = $('#company_name').val();
            var ship_to = $('#ship_to').val();
            var shipping_name1 = $('#shipping_name1').val();
            var shipping_name = $('#shipping_name').val();
            var ship_method = $('#ship_method').val();
            var work_order = $('#work_order').val();
            var delivery_at = $('#delivery_at').val();
            var ship_date = $('#ship_date').val();
            formValid = true;
            if (po_date == '') {
                $("#po_date_err")
                    .html("PO is Required..!")
                    .css("display", "block");
                formValid = false;
            } else {
                $("#po_date_err").css("display", "none");
            }
            if (indent_id == '') {
                $('#new_indent_id_err').html("Please Select Indent...!!").css("display", "block");
                formValid = false;
            } else {
                $('#new_indent_id_err').css("display", "none");
            }
            if (party_name == '') {
                $('#party_name_err').html("Please Select Party...!!").css("display", "block");
                formValid = false;
            } else {
                $('#party_name_err').css("display", "none");
            }
            if (company_name == '') {
                $('#company_name_err').html("Please Select Company..!!").css("display", "block");
                formValid = false;
            } else {
                $('#company_name_err').css("display", "none");
            }
            if (ship_to == '') {
                $('#ship_to_err').html("Please Select Ship to..!!").css("display", "block");
                formValid = false;
            } else {
                $('#ship_to_err').css("display", "none");
            }
            if (delivery_at == '') {
                $('#delivery_at_err').html("Please Select Delivery Location..!!").css("display", "block");
                formValid = false;
            } else {
                $('#delivery_at_err').css("display", "none");
            }
            if (formValid) {
                $.ajax({
                    type: 'POST',
                    url: 'purchase_order_curd.php',
                    data: {
                        'action': 'save_po',
                        'po_id': po_id,
                        'indent_id': indent_id,
                        'po_no': po_no,
                        'po_date': po_date,
                        'party_name': party_name,
                        'ref': ref,
                        'terms': terms,
                        'company_name': company_name,
                        'ship_to': ship_to,
                        'shipping_name1': shipping_name1,
                        'shipping_name': shipping_name,
                        'ship_method': ship_method,
                        'work_order': work_order,
                        'delivery_at': delivery_at,
                        'ship_date': ship_date
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {

                            $("#pomodal").modal('hide');
                            $('#po_id').val(response.po_id)
                            // getList(search);
                            $('#IndentModal').modal('show');
                            processPO(indent_id);

                        } else {
                            $("#pomodal").modal('hide');
                            $(".removeMessages").html(
                                '<div class="alert alert-warning alert-dismissible" role="alert">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                                response.messages +
                                '</div>');
                        }
                    }

                });
            }
        });

        //------------------------Edit PO------------------

        function editPO(po_id = null) {
            if (po_id) {
                // fetch the member data
                $.ajax({
                    url: 'purchase_order_curd.php',
                    type: 'post',
                    data: {
                        'action': 'get_view',
                        'po_id': po_id
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

                        if (response.indent_no > 999) {
                            var indent_no = response.indent_no;
                        } else {
                            if (response.indent_no > 99 && response.indent_no < 1000) {
                                var indent_no = "0" + response.indent_no;
                            } else {
                                if (response.indent_no > 9 && response.indent_no < 100) {
                                    var indent_no = "00" + response.indent_no;
                                } else {
                                    var indent_no = "000" + response.indent_no;
                                }
                            }
                        }

                        var ino = response.ind_prefix + '/' + indent_no;
                        $('#po_no').val(po_no);
                        $('#po_id').val(response.po_id);

                        $('#po_date').val(response.po_date);
                        $('#new_indent_id').prepend("<option value='" + response.indent_id + "' selected>" +
                            ino + "</option>");
                        $('#party_name').prepend("<option value='" + response.party_id + "' selected>" +
                            response.party_name + "</option>");
                        $('#address1').val(response.party_address1);
                        $('#address2').val(response.party_address2);
                        $('#address3').val(response.party_address3);
                        $('#city').val(response.party_city);
                        $('#state').val(response.party_state);
                        $('#contact').val(response.party_contact);
                        $('#tin').val(response.party_tin);
                        $('#ref').val(response.vendor_ref);
                        $('#terms').val(response.terms_condition);
                        $('#company_name').prepend("<option value='" + response.company_id + "' selected>" +
                            response.company_name + "</option>");
                        $('#ship_to').val(response.shipto);
                        $('#shipping_name').prepend("<option value='" + response.ship_id + "' selected>" +
                            response.ship_name + "</option>");
                        $('#shipping_name1').prepend("<option value='" + response.ship_id + "' selected>" +
                            response.ship_name + "</option>");
                        $('#ship_address1').val(response.cmp_address1);
                        $('#ship_address2').val(response.cmp_address2);
                        $('#ship_address3').val(response.cmp_address3);
                        $('#company_city').val(response.cmp_city);
                        $('#company_state').val(response.cmp_state);
                        $('#ship_method').val(response.ship_method);
                        $('#work_order').val(response.work_order);
                        $('#delivery_at').prepend("<option value='" + response.delivery_at + "' selected>" +
                            response.delivery_city + "</option>");
                        $('#ship_date').val(response.delivery_date);
                        getItemList(response.indent_id);

                    } // /success
                }); // /fetch selected member info

            } else {
                alert("Error : Refresh the page again");
            }
        }

        //------------------------------Delete PO-------------------------

        function removePO(po_id = null) {
            if (po_id) {
                // click on remove button
                $("#removeBtn").unbind('click').bind('click', function() {
                    $.ajax({
                        url: 'purchase_order_curd.php',
                        type: 'post',
                        data: {
                            'action': 'delete_po',
                            po_id: po_id
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

                                getList(search);
                                // close the modal
                                $("#removePOModal").modal('hide');

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

        function recallPO(po_id = null) {
            if (po_id) {
                $.ajax({
                    type: 'post',
                    url: 'purchase_order_curd.php',
                    data: {
                        action: 'po_recall',
                        po_id: po_id
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

                            getList(search);
                            // close the modal
                            $("#removePOModal").modal('hide');

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

            } else {
                alert('Error: Refresh the page again');
            }
        }

        //--------------------------Get Indent Details and Item Details---------------------------------//
        function processPO(indent_id) {
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
                            if (value.aprvd_status != 0) {
                                x = x + '<tr>' +
                                    '<td><input checked id="' + parseInt(i) +
                                    '" type="checkbox" data-indent_id="' + value.indent_id +
                                    '" data-item_id="' + value.item_id +
                                    '"data-unit_id="' + value.unit_id +
                                    '"data-remark ="' + value.remark +
                                    '"data-category_id ="' + value.category_id +
                                    '" class="select_item"></td>' +
                                    '<td>' + value.item_name + '<br>'+value.remark+'&emsp;'+value.AnyOther+'</td>' +
                                    '<td>' + value.appr_qnty + ' ' + value.unit_name + '</td>' +
                                    '<td>' + value.pre_rate + '</td>' +
                                    '<td><input type="number" class="form-control qty" id="cur_qty_' +
                                    parseInt(
                                        i) +
                                    '" required="true" value="' + value.appr_qnty + '"></td>' +
                                    '<td><input type="number" class="form-control rate" value="' + value.cur_rate + '" id="cur_rate_' +
                                    parseInt(
                                        i) + '" required="true"></td>' +
                                    '</tr>';
                                i++;
                            }
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
                        'category_id': $(this).data('category_id'),
                        'cur_qty': $('#cur_qty_' + id).val(),
                        'cur_rate': $('#cur_rate_' + id).val()
                    });
                }
            });

            console.log(checkedValue);

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
                        $('#dtm_div').css('display', 'block');
                        //Change the processBtn Id for next phase work//
                        $('#processBtn').attr('id', 'optionBtn');
                        getDtmList();
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
                        getDtmList();
                        get_amt1();
                    }


                },
                complete: function() {

                }
            });
        });

        function getDtmList() {
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

        function removeItem(Id) {
            $.ajax({
                type: 'post',
                url: 'purchase_order_curd.php',
                data: {
                    action: 'removeDtm',
                    Id: Id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        getDtmList();
                        get_amt();
                    } else {
                        alert('Something went wrong. Please try again');
                    }
                }
            });
        }
    </script>

</body>

</html>