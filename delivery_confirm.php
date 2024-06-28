<?php include'menu.php';
include 'db_connect.php';
/*--------------------------------*/
$sql_user = mysql_query("SELECT dc1,dc2,dc3,dc4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*--------------------------------*/
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

    #control {
        margin-bottom: 5px;
    }
    </style>
</head>

<body>
    <section class="main-section">
        <div class="row" id="control">
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
            <?php 	if($row_user['dc1']==1){?>
            <div class="col-md-4">
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#dcmodal">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add New DC</button>
            </div>
            <?php }?>
        </div>

        <div class="row">

            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="removeMessages"></div>

                <table class="table" id="dctable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.no</th>
                            <th>DC No.</th>
                            <th>DC Date</th>
                            <th style="width:180px;">PO No</th>
                            <th>PO Date</th>
                            <th style="width:200px;">Party Name</th>
                            <th style="width:200px;">Company Name</th>
                            <th>Edit</th>
                            <th>Deleet</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
        </div>
    </section>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="dcmodal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Delivery Confirmation
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>

                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" id="dcid">
                                <label>DC No.</label>
                                <input type="text" id="dc_no" name="dc_no" class="form-control" readonly />
                            </div>
                            <div class="col-md-3">
                                <label>DC Date<span style="color:red;">*</span></label>
                                <input type="date" id="dc_date" name="dc_date" class="form-control"
                                    value="<?php echo date('Y-m-d');?>" />
                            </div>
                            <div class="col-md-3">
                                <label>PO No.<b style="color:red;font-size:14px;">*</b></label>
                                <select id="po_no" name="po_no" class="form-control">
                                    <option value="">---Select PO---</option>
                                    <?php
                                            $sql ="SELECT Distinct tblpo.po_id, tblpo.po_no,CCode,ti.ind_prefix FROM tblpo_item 
                                            INNER JOIN tblpo ON tblpo_item.po_id = tblpo.po_id 
                                            JOIN tbl_indent ti ON ti.indent_id =tblpo_item.indent_id
                                            INNER JOIN company ON tblpo.company_id = company.company_id 
                                            WHERE po_status='S' AND order_received='N' AND (po_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' 
                AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')
                                            ORDER BY CCode,po_date, po_no";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                if ($row['po_no'] > 999)
                                                {
                                                    $po_no = $row['po_no'];
                                                }
                                                else
                                                {
                                                    if ($row['po_no'] > 99 && $row['po_no'] < 1000)
                                                    {
                                                        $po_no = "0" . $row['po_no'];
                                                    }
                                                    else
                                                    {
                                                        if ($row['po_no'] > 9 && $row['po_no'] < 100)
                                                        {
                                                            $po_no = "00" . $row['po_no'];
                                                        }
                                                        else
                                                        {
                                                            $po_no = "000" . $row['po_no'];
                                                        }
                                                    }
                                                } 
                                                $po_no = $row['CCode'].' /'.$row['ind_prefix'].' /'.$po_no;
                                                echo '<option value="'.$row['po_id'].'">'.$po_no.'</option>';
                                            }
                                        ?>
                                </select>
                                <div id="po_no_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label>PO Date</label>
                                <input type="text" name="po_date" id="po_date" class="form-control" disabled>
                            </div>
                        </div>
                        <div>

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Party Name</label>
                                    <input type="text" id="party_name" name="party_name" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Address 1</label>
                                    <input type="text" id="address1" name="address1" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Address 2</label>
                                    <input type="text" id="address2" name="address2" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Address 3</label>
                                    <input type="text" id="address3" name="address3" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>City</label>
                                    <input type="text" id="city" name="city" class="form-control" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label>State</label>
                                    <input type="text" id="state" name="state" class="form-control" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Company Name</label>
                                    <input type="text" id="company_name" name="company_name" class="form-control"
                                        disabled>
                                </div>
                                <div class="col-md-3">
                                    <label>Delivery At</label>
                                    <input type="text" id="delivery_at" name="delivery_at" class="form-control"
                                        disabled>
                                </div>
                                <div class="col-md-2">
                                    <label>Reqired Date</label>
                                    <input type="text" id="req_date" name="req_date" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="next-phase" style="display:none;">
                            <hr style="border-top: 1px dashed red;">
                            <div class="col-md-12">
                                <input type="hidden" id="dc_id" name="dc_id">
                                <table id="po_item" class="table">
                                    <thead>
                                        <th><input type="checkbox" id="select_all" checked></th>
                                        <th style="width:450px;">Item Name</th>
                                        <th>Order Qty</th>
                                        <th>Cur. Dlry. Qty</th>
                                    </thead>
                                    <tbody id="recordList"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <?php 	if($row_user['dc1']==1){?>
                        <button type="button" class="btn btn-primary" name="save" id="save">Save changes</button>
                        <?php }?>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="dcmodal_edit" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-pencil"></span> Edit Delivery Confirmation
                    </h4>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12">

                            <table id="tbl_item" class="table">
                                <thead>
                                    <th><input type="checkbox" id="select_all" checked></th>
                                    <th style="width:450px;">Item Name</th>
                                    <th>Order Qty</th>
                                    <!--   <th>Pre.Dlr. Qty</th> -->
                                    <th>Cur. Dlry. Qty</th>
                                </thead>
                                <tbody id="recordList1"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edtBtn">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /Edit modal -->

    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removeDCModal">
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
        $('#dctable').DataTable({
            "bLengthChange": false,
            "searching": false,
            destroy: true,
            'ajax': {
                'url': 'delivery_curd.php',
                'data': {
                    'action': 'get_dc',
                    'start_date': start_date,
                    'end_date': end_date
                },
                type: 'post'
            },
            paging: true,

            "order": []

        });
        $('#dctable').DataTable().destroy();
    }

    $(document).on('click', '#search', function() {
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        getList(start_date, end_date);
    });
    //-----------------------------
    $(document).on('change', '#po_no', function() {
        var po_id = $(this).val();
        $.ajax({
            type: 'post',
            url: 'delivery_curd.php',
            data: {
                'action': 'get_po_detail',
                po_id: po_id
            },
            dataType: 'json',
            success: function(response) {
                $('#po_date').val(response.po_date);
                $('#party_name').val(response.party_name);
                $('#address1').val(response.address1);
                $('#address2').val(response.address2);
                $('#address3').val(response.address3);
                $('#city').val(response.city);
                $('#state').val(response.state_name);
                $('#company_name').val(response.company_name);
                $('#delivery_at').val(response.location_name);
                $('#req_date').val(response.delivery_date);
            }
        });
    });
    //------------------------------------Create a new DC----------------------
    $(document).on('click', '#save', function() {
        var dc_date = $('#dc_date').val();
        var po_id = $('#po_no').val();
        formValid = true;
        if (po_id == '') {
            $("#po_no_err")
                .html("PO is Required..!")
                .css("display", "block");
            formValid = false;
        } else {
            $("#po_no_err").css("display", "none");
        }
        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'delivery_curd.php',
                data: {
                    'action': 'create_dc',
                    dc_date: dc_date,
                    po_id: po_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        $('#dc_no').val(response.dc_no);
                        $('#dcid').val(response.dc_id);
                        $('#dc_date').prop('disabled', true);
                        $('#po_no').prop('disabled', true);
                        $('#next-phase').css('display', 'block');
                        //Change the save btn Id for next phase work//
                        $('#save').attr('id', 'nextbtn');
                    } else {
                        alert(response.messages);
                    }

                },
                complete: function() {
                    get_item();
                }
            });
        }

    });

    $(document).on('click', '#select_all', function() {
        if ($(this).prop('checked') == true) {
            $('.select_item').prop('checked', true);
        } else {
            $('.select_item').prop('checked', false);
        }
    });

    function get_item() {
        var po_id = $('#po_no').val();
        var dc_id = $('#dcid').val();
        $.ajax({
            type: 'post',
            url: 'delivery_curd.php',
            data: {
                po_id: po_id,
                dc_id: dc_id,
                action: 'Get_Item'
            },
            dataType: 'json',
            success: function(response) {
                var x = '';
                $('#dc_id').val(response.data[0].dc_id);
                $.each(response.data, function(key, value) {
                    x = x + '<tr>' +
                        '<td><input id="' + value.item_id + '" data-item_name="' + value.item_name +
                        '" data-qty="' + value.qty + '"data-unit_id="' + value.unit_id +
                        '"data-category_id="' + value.category_id +
                        '" type="checkbox" name="select_item" class="select_item" checked></td>' +
                        '<td>' + value.item_name + '</td>' +

                        '<td>' + value.qty + ' ' + value.unit_name + '</td>' +

                        '<td><input type="text" id="dlr_qty_' + value.item_id + value.category_id +
                        '" value="' + value
                        .qty +
                        '" class="form-control"></td>' +
                        '</tr>';
                });
                $('#recordList').html(x);
            },
        });
    }

    $(document).on('click', '#nextbtn', function() {
        var list_array = [];
        var dc_id = $('#dc_id').val();
        $('.select_item').each(function() {
            if ($(this).prop('checked') == true) {
                var item_id = $(this).attr('id');
                var category_id = $(this).data('category_id');
                var dlr_qty = $('#dlr_qty_' + item_id + category_id).val();

                list_array.push({
                    'item_id': item_id,
                    'dlr_qty': dlr_qty,
                    'unit_id': $(this).data('unit_id'),
                    'category_id': category_id,

                });
            }
        });
        // console.log(list_array);
        $.ajax({
            type: 'POST',
            url: 'delivery_curd.php',
            data: {
                'list_array': list_array,
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

    //========================Edit Delivery Confirmation===================

    function editDC(dc_id = null) {
        if (dc_id) {
            // fetch the member data
            $.ajax({
                url: 'delivery_curd.php',
                type: 'post',
                data: {
                    'action': 'get_view',
                    'dc_id': dc_id
                },
                dataType: 'json',
                success: function(response) {
                    var x = '';

                    $.each(response.data, function(key, value) {
                        x = x + '<tr>' +
                            '<td><input id="' + value.item_id + '" data-item_name="' + value
                            .item_name +
                            '" data-qty="' + value.qty + '"data-unit_id="' + value.unit_id +
                            '"data-category_id="' + value.category_id +
                            '"data-rec_id="' + value.rec_id +
                            '" type="checkbox" name="select_item" class="select_item" checked></td>' +
                            '<td>' + value.item_name + '</td>' +

                            '<td>' + value.ord_qty + ' ' + value.unit_name + '</td>' +

                            '<td><input type="text" id="dlr_qty_' + value.item_id + value
                            .category_id +
                            '" value="' + value
                            .qty +
                            '" class="form-control"></td>' +
                            '</tr>';
                    });
                    $('#recordList1').html(x);

                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }


    $(document).on('click', '#edtBtn', function() {
        var list_array = [];
        $('.select_item').each(function() {
            if ($(this).prop('checked') == true) {
                var item_id = $(this).attr('id');
                var category_id = $(this).data('category_id');
                var dlr_qty = $('#dlr_qty_' + item_id + category_id).val();

                list_array.push({
                    'item_id': item_id,
                    'dlr_qty': dlr_qty,
                    'category_id': category_id,
                    'unit_id': $(this).data('unit_id'),
                    'rec_id': $(this).data('rec_id')

                });
            }
        });
        //console.log(list_array);
        $.ajax({
            type: 'POST',
            url: 'delivery_curd.php',
            data: {
                'list_array': list_array,
                'action': 'update_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    $('#dcmodal_edit').modal('hide');
                    alert('Successfully..');
                    location.reload();
                } else {
                    $('#dcmodal_edit').modal('hide');
                    alert('Somthing Went Wrong..Please try again..!!');
                    location.reload();
                }
            },
            error: function() {},
            complete: function() {

            },
        });

    });

    function removeDC(rec_id = null) {
        if (rec_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {

                $.ajax({
                    url: 'delivery_curd.php',
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


                            $("#removeDCModal").modal('hide');
                            getList(start_date, end_date);
                        } else {
                            $("#removeDCModal").modal('hide');
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
    </script>

</body>

</html>