<?php include'menu.php';
include 'db_connect.php';
if($_SESSION['stores_utype']=="U"){$location_id = $_SESSION['stores_locid'];} else {$location_id = 0;}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <style>
    .form-control[disabled],
    .form-control[readonly],
    fieldset[disabled] .form-control {
        background-color: #ebf3fd;
        opacity: 1;
    }

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
                        <span class="glyphicon glyphicon-plus-sign"></span> New Return Receipt</button>
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
                                <th>Return No.</th>
                                <th>Return Date</th>
                                <th style="width:100px;">Receipt No</th>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Receipt Return
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="hidden" id="return_id">
                            <label for="return_no">Return No.</label>
                            <input type="text" id="return_no" class="form-control" disabled>
                        </div>

                        <div class="col-md-2">
                            <label for="receipt_no">Receipt No.<span style="color:red;">*</span></label>
                            <select id="receipt_no" class="form-control">
                                <option value="">Select...</option>
                                <?php 
                                if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
                                    $sql ="SELECT * FROM tblreceipt1 ORDER BY receipt_id";
                                    $query =$connect->query($sql);
                                }else{
                                    $sql ="SELECT * FROM tblreceipt1 WHERE recd_at =$location_id ORDER BY receipt_id";
                                    $query =$connect->query($sql);
                                }
                                        while($row=$query->fetch_assoc()){
                                            if ($row['receipt_no'] > 999)
                                            {
                                                $receipt_no = $row['receipt_no'];
                                            }
                                            else
                                            {
                                                if ($row['receipt_no'] > 99 && $row['receipt_no'] < 1000)
                                                {
                                                    $receipt_no = "0" . $row['receipt_no'];
                                                }
                                                else
                                                {
                                                    if ($row['receipt_no'] > 9 && $row['receipt_no'] < 100)
                                                    {
                                                        $receipt_no = "00" . $row['receipt_no'];
                                                    }
                                                    else
                                                    {
                                                        $receipt_no = "000" . $row['receipt_no'];
                                                    }
                                                }
                                            }
                                            $rec_no = $row['receipt_prefix'].'/'.$receipt_no;
                                            echo '<option value="'.$row['receipt_id'].'">'.$rec_no.'</option>';
                                        }
                                    ?>
                            </select>
                            <div id="receipt_no_err" style="color:red; display:none;"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="return_by">Return By<span style="color:red;">*</span></label>
                            <select id="return_by" class="form-control">
                                <option value="">Choose..</option>
                            </select>
                            <div id="return_by_err" style="color:red; display:none;"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="return_date">Return Date<span style="color:red;">*</span></label>
                            <input type="date" id="return_date" class="form-control"
                                value="<?php echo date('Y-m-d');?>">
                            <div id="return_date_err" style="color:red; display:none;"></div>
                        </div>
                        <div class="col-md-2">
                            <label for="receipt_date">Receipt Date</label>
                            <input type="text" id="receipt_date" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="po_no">PO No.</label>
                            <input type="text" id="po_no" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="po_date">PO Date</label>
                            <input type="text" id="po_date" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="chalan_no">Challan No.</label>
                            <input type="text" id="chalan_no" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="chalan_date">Challan Date</label>
                            <input type="text" id="chalan_date" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="transit_point">Transit Point</label>
                            <input type="text" id="transit_point" class="form-control" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="delivery_date">Delivery Date</label>
                            <input type="text" id="delivery_date" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="delivery_at">Delivery At</label>
                            <input type="text" id="delivery_at" class="form-control" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="received_at">Received At</label>
                            <input type="text" id="received_at" class="form-control" disabled>
                        </div>
                        <div class="col-md-2">
                            <label for="received_by">Received By</label>
                            <input type="text" id="received_by" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="party_name">Party Name</label>
                            <input type="text" id="party_name" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="address1">Address-1</label>
                            <input type="text" id="address1" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="address2">Address-2</label>
                            <input type="text" id="address2" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="address3">Address-3</label>
                            <input type="text" id="address3" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="city">City</label>
                            <input type="text" id="city" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="state">State</label>
                            <input type="text" id="state" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="f_paid">Freight Paid</label>
                            <input type="text" id="f_paid" class="form-control" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="f_amount">Freight Amount</label>
                            <input type="text" id="f_amount" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="row" id="next-phase" style="display:none;">
                        <hr style="border-top: 1px dashed red;">
                        <div class="col-md-12">
                            <input type="hidden" id="" name="">
                            <table id="material_receipt_item" class="table">
                                <thead>
                                    <th><input type="checkbox" id="select_all"></th>
                                    <th style="width:200px;">Item Name</th>
                                    <th>Received Qty</th>
                                    <th>Return Qty</th>
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
                'url': 'return_receipt_curd.php',
                'data': {
                    'action': 'get_receipt',
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

    //=========================Get Staff List on Receipt No. Change=================

    $(document).on('change', '#receipt_no', function() {
        var receipt_id = $(this).val();
        $.ajax({
            url: 'return_receipt_curd.php',
            type: 'POST',
            data: {
                'action': 'get_detail',
                'receipt_id': receipt_id
            },
            dataType: 'json',
            success: function(response) {
                var x = '<option value="">Select </option>';
                if (response.status == 200) {
                    $.each(response.staff, function(key, value) {
                        x = x + '<option value="' + value.staff_id + '">' + value
                            .staff_name + '</option>';
                    });
                }
                $('#return_by').html(x);
                $('#receipt_date').val(response.detail[0].receipt_date);

                var po_no = response.detail[0].po_no;
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
                $('#po_no').val(po_no);
                $('#po_date').val(response.detail[0].receipt_date);
                $('#chalan_no').val(response.detail[0].challan_no);
                $('#chalan_date').val(response.detail[0].challan_date);
                $('#transit_point').val(response.detail[0].transit_point);
                $('#delivery_date').val(response.detail[0].delivery_date);
                $('#delivery_at').val(response.detail[0].delivery_location);
                $('#received_at').val(response.detail[0].recd_at);
                $('#received_by').val(response.detail[0].staff_name);
                $('#party_name').val(response.detail[0].party_name);
                $('#address1').val(response.detail[0].address1);
                $('#address2').val(response.detail[0].address2);
                $('#address3').val(response.detail[0].address3);
                $('#city').val(response.detail[0].city_name);
                $('#state').val(response.detail[0].state_name);
                $('#f_paid').val(response.detail[0].freight_paid);
                $('#f_amount').val(response.detail[0].freight_amt);
            }
        });
    });

    $(document).on('click', '#save', function() {
        var return_id = $('#return_id').val();
        var return_no = $('#return_no').val();
        var receipt_no = $('#receipt_no').val();
        var return_by = $('#return_by').val();
        var return_date = $('#return_date').val();
        var formValid = true;

        if (receipt_no == "") {
            $("#receipt_no_err").html("Receipt No is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#receipt_no_err").css("display", "none");
        }
        if (return_by == "") {
            $("#return_by_err").html("Return By is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#return_by_err").css("display", "none");
        }
        if (return_date == "") {
            $("#return_date_err").html("Return Date is Required..!").css("display", "block");
            formvalid = false;
        } else {
            $("#return_date_err").css("display", "none");
        }
        if (formValid) {
            $.ajax({
                type: 'POST',
                url: 'return_receipt_curd.php',
                data: {
                    'action': 'save_receipt',
                    'return_id': return_id,
                    'return_no': return_no,
                    'receipt_no': receipt_no,
                    'return_by': return_by,
                    'return_date': return_date

                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        $('#return_id').val(response.return_id);
                        $('#return_no').val(response.return_no);
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
    //=================Get Item List================

    function get_item() {

        var receipt_no = $('#receipt_no').val();
        $.ajax({
            type: 'post',
            url: 'return_receipt_curd.php',
            data: {
                receipt_no: receipt_no,
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
                        '" type="checkbox" name="select_item" class="select_item" ></td>' +
                        '<td>' + value.item_name + '</td>' +

                        '<td>' + value.qty + ' ' + value.unit_name + '</td>' +

                        '<td><input type="text" id="return_qty_' + value.item_id + value
                        .category_id +
                        '" class="form-control"></td>' +
                        '</tr>';
                });
                $('#recordList').html(x);
            },
        });
    }

    //===============================================//
    $(document).on('click', '#select_all', function() {
        if ($(this).prop('checked') == true) {
            $('.select_item').prop('checked', true);
        } else {
            $('.select_item').prop('checked', false);
        }
    });

    $(document).on('click', '#nextbtn', function() {
        var list_array = [];
        var return_id = $('#return_id').val();
        var return_date = $('#return_date').val();
        $('.select_item').each(function() {
            if ($(this).prop('checked') == true) {
                var item_id = $(this).attr('id');
                var category_id = $(this).data('category_id');
                var return_qty = $('#return_qty_' + item_id + category_id).val();

                list_array.push({
                    'item_id': item_id,
                    'return_qty': return_qty,
                    'category_id': category_id,
                    'unit_id': $(this).data('unit_id'),
                });
            }
        });
        $.ajax({
            type: 'POST',
            url: 'return_receipt_curd.php',
            data: {
                'list_array': list_array,
                'return_id': return_id,
                'return_date': return_date,
                'action': 'save_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                    $('#material_modal').modal('hide');
                    alert('Successfully..');
                    location.reload();
                } else {
                    $('#material_modal').modal('hide');
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

    function removeMaterial(return_id = null) {
        if (return_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {

                $.ajax({
                    url: 'return_receipt_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_record',
                        return_id: return_id
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
    function editMaterial(return_id = null) {
        if (return_id) {

            $.ajax({
                url: 'return_receipt_curd.php',
                type: 'post',
                data: {
                    'action': 'edit_return_receipt',
                    'return_id': return_id
                },
                dataType: 'json',
                success: function(response) {
                    var po_no = response.po_no;
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

                    var return_no = response.return_no;
                    if (return_no > 999) {
                        return_no = return_no;
                    } else {
                        if (return_no > 99 && return_no < 1000) {
                            return_no = "0" + return_no;
                        } else {
                            if (return_no > 9 && return_no < 100) {
                                return_no = "00" + return_no;
                            } else {
                                return_no = "000" + return_no;
                            }
                        }
                    }
                    var receipt_no = response.receipt_no;
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
                    r_no = response.receipt_prefix + '/' + receipt_no;
                    $('#po_no').val(po_no);
                    $('#return_no').val(return_no);
                    $('#return_id').val(return_id);
                    $('#receipt_no').prepend("<option value='" + response.receipt_id + "' selected>" +
                        r_no + "</option>");
                    $('#po_date').val(response.po_date);
                    $('#receipt_date').val(response.receipt_date);
                    $('#chalan_no').val(response.challan_no);
                    $('#chalan_date').val(response.challan_date);
                    $('#transit_point').val(response.transit_point_name);
                    $('#delivery_date').val(response.delivery_date);
                    $('#delivery_at').val(response.delivery_location);
                    $('#received_at').val(response.received_at);
                    $('#received_by').val(response.received_by);
                    $('#party_name').val(response.party_name);
                    $('#address1').val(response.address1);
                    $('#address2').val(response.address2);
                    $('#address3').val(response.address3);
                    $('#city').val(response.city_name);
                    $('#state').val(response.state_name);
                    $('#f_paid').val(response.freight_paid);
                    $('#f_amount').val(response.freight_amt);
                    $('#return_by').prepend("<option value='" + response.return_by + "' selected>" +
                        response.return_by_name + "</option>");
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>

</body>

</html>