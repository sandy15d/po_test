<?php include'menu.php';
include 'db_connect.php';
/*-------------------------------*/
$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION["stores_uid"]) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/

$oid = $_REQUEST['oid'];
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
    </style>
</head>

<body>
    <section class="main-section">
        <div class="container-fluid">

            <div class="col-md-12" id="form1">
                <div class="left-box">

                    <div class="removeMessages"></div>
                    <form id="add_order_form" action="javascript:void(0);">
                        <input type="hidden" name="startYear" id="startYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION["stores_syr"]));?>" />
                        <input type="hidden" name="endYear" id="endYear"
                            value="<?php echo date("d-m-Y",strtotime($_SESSION["stores_eyr"]));?>" />
                        <input type="hidden" name="maxDate" id="maxDate"
                            value="<?php echo date("Y-m-d",strtotime($_SESSION["stores_syr"]));?>" />
                        <div class="shadow p-3 mb-5 bg-white rounded">
                            <div class="row">

                                <div class="col-md-2 mb-3">
                                    <label for="indent_no">Indent No.:</label>
                                    <input type="text" name="indent_no" id="indent_no" class="form-control" readonly />

                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="location">Indent From:</label>
                                    <select id="location" name="location" class="form-control">
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
                                <div class="col-md-2 mb-3">
                                    <label for="indent_date">Indent Date</label>
                                    <input type="date" name="indent_date" id="indent_date" class="form-control"
                                        value="<?php echo date('Y-m-d');?>" />
                                    <div id="indent_date_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="supply_date">Estimated Supply Date:</label>
                                    <input type="date" name="supply_date" id="supply_date" class="form-control"
                                        value="<?php echo date('Y-m-d');?>" />
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="order_by">Order By:</label>
                                    <select id="order_by" name="order_by" class="form-control">
                                        <option value="">Select Staff</option>
                                    </select>
                                    <div id="order_by_err" style="color:red; display:none;"></div>
                                </div>

                                <div class="col-md-12 mb-12" id="insert" name="insert">
                                    <div style="padding:5px;">
                                    </div>
                                    <?php if($row_user['oi1']==1){?>
                                    <button class="btn btn-md btn-primary pull pull-right" id="create_indent"
                                        name="create_indent">Create
                                        Indent</button>
                                    <?php }?>
                                    <button type="reset" class="btn btn-md btn-danger pull pull-right"
                                        style="margin-right:2px;">Reset</button>
                                    <div style="padding:5px;"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row" id="item-insert" >
                <div class="col-md-4">
                    <div>
                        <div class="row">
                            <div class="col-md-12">
                                <center>
                                    <h4>Add New Intent Item</h4>
                                </center>
                                <hr>
                            </div>
                            <form id="add_new_item" action="javascript:void(0);">

                                <input type="hidden" id="indent_order_id" name="indent_order_id" value="<?=$oid;?>">
                                <input type="hidden" id="rec_id" name="rec_id">
                                <div class="col-md-8">
                                    <label for="item">Item Name:</label>
                                    <select id="item" name="item" class="form-control">
                                        <option value=""> Select Item</option>
                                        <?php
                                            $sql ="SELECT * FROM item ORDER BY item_name";
                                            $query =$connect->query($sql);
                                            while($row=$query->fetch_assoc()){
                                                echo'<option value='.$row['item_id'].'>'.$row['item_name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <div id="item_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="category">Item Category:</label>
                                    <select id="category" class="form-control"></select>
                                    <div class="err" id="category_err" style="display:none"></div>
                                </div>
                                <input type="hidden" id="unit" name="unit">
                                <div class="col-md-6">
                                    <label for="stock">Current Stock:</label>
                                    <input type="text" id="stock" name="stock" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="qty">Quantity:</label>
                                    <input type="text" id="qty" name="qty" class="form-control">
                                    <div id="qty_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-12">
                                    <label for="desc">Description:</label>
                                    <textarea rows="2" cols="57" name="desc" id="desc" class="form-control"
                                        spellcheck="false"> </textarea>
                                </div>


                                <div class="col-md-12">
                                    <label for="remar">Any Other</label>
                                    <input type="text" id="remark" name="remark" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <div style="padding:5px;"></div>

                                    <button class="btn btn-md btn-primary pull pull-right" id="add_item"
                                        name="add_item">Save Changes</button>

                                    <button type="reset" class="btn btn-md btn-danger pull pull-right"
                                        style="margin-right:2px;">Reset</button>
                                    <div style="padding:15px;"></div>
                                </div>
                            </form>
                        </div>
                        <div style="padding:15px;"></div>
                    </div>
                </div>
                <div class="col-md-8" id="table1">
                    <div class="right-box">
                        <center>
                            <h4>Indent Item List</h4>
                        </center>
                        <hr>
                    </div>
                    <table class="table" id="indent_item_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:30px">S.no</th>
                                <th style="width:300px">Item Name</th>
                                <th style="width:200x">Item Remark</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Edit</th>
                                <th>Del</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" style="text-align:center">
                                    <h4>No Record Found, Plese Create Indent First, Then add Item.</h4>
                                </td>
                            </tr>
                            <h3></h3>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <button class="btn btn-lg pull pull-right btn-success" id="order_now" name="order_now"
                            style="display:none">Order
                            Now</button>
                    </div>
                </div>
            </div>




        </div>
    </section>
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
    <input type="hidden" id="start_year" value="<?php echo date("Y-m-d",strtotime($_SESSION["stores_syr"]));?>">
    <input type="hidden" id="end_year" value="<?php echo date("Y-m-d",strtotime($_SESSION["stores_eyr"]));?>">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        var oid = $('#indent_order_id').val();
        getOrderIndent(oid);
        getList(oid);
        function getOrderIndent(oid){
            var oid = oid
                      $.ajax({
                type: 'post',
                url: 'order_indent_curd.php',
                data: {
                    'action': 'get_order_indent',
                    'oid': oid,
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#order_by')
                        .empty()
                        .attr("disabled", true)
                        .append('<option selected="selected" value=' + response.order_by + '>' +
                            response.staff_name + '</option>');
                    var indent_no = response.indent_no;
                    var ino = '';
                    if (indent_no > 999) {
                        ino = indent_no;
                    } else {
                        if (indent_no > 99 && indent_no < 1000) {
                            ino = "0" + indent_no;
                        } else {
                            if (indent_no > 9 && indent_no < 100) {
                                ino = "00" + indent_no;
                            } else {
                                ino = "000" + indent_no;
                            }
                        }
                    }
                    $("#indent_no").val(response.ind_prefix + "/" + ino);
                    $('#indent_order_id').val(response.indent_id);
                    $('#location')
                        .empty()
                        .attr("disabled", true)
                        .append('<option selected="selected" value=' + response.location_id + '>' +
                            response.orderfrom + '</option>');
                    $('#indent_date').val(response.indent_date).attr("disabled", true);
                    $('#supply_date').val(response.supply_date).attr("disabled", true);
                    $('#insert').css('display', 'none');
                    $('#item-insert').css('display', 'block');
                }


            });
        }
    });
    
    function getList(indent_id) {
        $("#indent_item_list").DataTable({
            destroy: true,
            "ajax": {
                url: "order_indent_curd.php",
                data: {
                    action: 'get_item_list',
                    'indent_id': indent_id
                },
                type: 'post'
            },
            "oLanguage": {
                "sEmptyTable": "No Record Found.."
            },
            paging: false,

            "order": []
        });
        $('#indent_item_list').DataTable().destroy();
    }

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
                var x = '<option value="">Select Order By</option>';
                if (response.status == 200) {
                    $.each(response.data, function(key, value) {
                        x = x + '<option value="' + value.staff_id + '">' + value
                            .staff_name +
                            '</option>';
                    });
                }
                $('#order_by').html(x);
            }
        });
    });

    $(document).on('click', '#create_indent', function() {

        var indent_date = $('#indent_date').val(); // 14/09/2020
        var supply_date = $('#supply_date').val();
        var location = $('#location').val();
        var order_by = $('#order_by').val()
        var startYear = $('#startYear').val();
        var endYear = $('#endYear').val();
        var maxDate = $('#maxDate').val();
        var start_date = $('#start_year').val();
        var end_date = $('#end_year').val();
        formValid = true;
        if (indent_date == '') {
            $("#indent_date_err")
                .html("Transit Mode is Required..!")
                .css("display", "block");
            formValid = false;
        } else {
            $("#indent_date_err").css("display", "none");
        }
        if (indent_date < start_date) {
            $("#indent_date_err")
                .html("Wrongly Selected Indent Date...!")
                .css("display", "block");
            formValid = false;
        } else {
            $("#indent_date_err").css("display", "none");
        }
        if (location == "") {
            $('#location_err').html("Please Select Indent Location").css("display", 'block');
            formValid = false;
        } else {
            $('#location_err').css('dispaly', 'none');
        }
        if (order_by == "") {
            $('#order_by_err').html("Order By is Required...!!").css("display", 'block');
            formValid = false;
        } else {
            $('#order_by_err').css('dispaly', 'none');
        }
        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'order_indent_curd.php',
                data: {
                    'action': 'add_order_indent',
                    'indent_date': indent_date,
                    'supply_date': supply_date,
                    'location': location,
                    'order_by': order_by
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    $('#order_by')
                        .empty()
                        .attr("disabled", true)
                        .append('<option selected="selected" value=' + response.order_by + '>' +
                            response.staff_name + '</option>');
                    var indent_no = response.indent_no;
                    var ino = '';
                    if (indent_no > 999) {
                        ino = indent_no;
                    } else {
                        if (indent_no > 99 && indent_no < 1000) {
                            ino = "0" + indent_no;
                        } else {
                            if (indent_no > 9 && indent_no < 100) {
                                ino = "00" + indent_no;
                            } else {
                                ino = "000" + indent_no;
                            }
                        }
                    }
                    $("#indent_no").val(response.ind_prefix + "/" + ino);
                    $('#indent_order_id').val(response.indent_id);
                    $('#location')
                        .empty()
                        .attr("disabled", true)
                        .append('<option selected="selected" value=' + response.location_id + '>' +
                            response.orderfrom + '</option>');
                    $('#indent_date').val(response.indent_date).attr("disabled", true);
                    $('#supply_date').val(response.supply_date).attr("disabled", true);
                    $('#insert').css('display', 'none');
                    $('#item-insert').css('display', 'block');
                }


            });
        }

    });

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
                $('#category').html(x);

            }

        });
    });


    //==========================on change item category====================
    $(document).on('change', '#category', function() {
        var category = $(this).val();
        var item = $('#item').val();
        var location = $('#location').val();
        var entry_date = $('#indent_date').val();

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

                    } else {
                        $('#stock').val(response.data.qty);
                        $('#unit').val(response.data.unit_id);

                    }
                } else {
                    alert('Something went wrong...!!!');
                }


            }

        });
    });
    //=============================================
    $(document).on('click', '#add_item', function() {
        var indent_order_id = $('#indent_order_id').val();
        var indent_date = $('#indent_date').val();
        var location = $('#location').val();
        var item = $('#item').val();
        var item_category = $('#category').val();
        var unit = $('#unit').val();
        var desc = $('#desc').val();
        var qty = $('#qty').val();
        var remark = $('#remark').val();
        var rec_id = $('#rec_id').val();
        formValid = true;
        if (item == '') {
            $("#item_err")
                .html("Item is Required..!")
                .css("display", "block");
            formValid = false;
        } else {
            $("#item_err").css("display", "none");
        }
        if (qty == '') {
            $("#qty_err")
                .html("Quantity is Required..!")
                .css("display", "block");
            formValid = false;
        } else {
            $("#qty_err").css("display", "none");
        }
        if (item_category == '') {
            $('#category_err').css('display', 'block');
            formValid = false;
        } else {
            $('#category_err').css('display', 'none');
        }
        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'order_indent_curd.php',
                data: {
                    'action': 'additem',
                    indent_order_id: indent_order_id,
                    indent_date: indent_date,
                    location: location,
                    rec_id: rec_id,
                    item: item,
                    item_category,
                    unit: unit,
                    desc: desc,
                    qty: qty,
                    remark: remark
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        getList(indent_order_id);
                        $('#order_now').css('display', 'block');
                        $('#item').prop('selectedIndex', '');
                        $('#category').prop('selectedIndex', '');
                        $('#desc').val('');
                        $('#qty').val('');
                        $('#remark').val('');
                        $('#rec_id').val('');
                        $('#stock').val('');
                    } else {
                        $(".removeMessages").html(
                            '<div class="alert alert-warning alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                            response.messages +
                            '</div>');
                    }
                },

            });
        }
    });

    function removeItem(rec_id = null) {
        if (rec_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {


                $.ajax({
                    url: 'order_indent_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_item',
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


                            $("#removeItemModal").modal('hide');
                            $('#rec_id').val('');
                            getList(indent_order_id);
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

    function editItem(rec_id = null) {
        if (rec_id) {
            // fetch the member data
            $.ajax({
                url: 'order_indent_curd.php',
                type: 'post',
                data: {
                    'action': 'get_item',
                    'rec_id': rec_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#rec_id").val(response.rec_id);
                    $("#qty").val(response.qnty);
                    $("#unit").val(response.unit_id);
                    $('#item').prepend("<option value='" + response.item_id + "' selected>" +
                        response.item_name + "</option>");
                    $('#category').prepend("<option value='" + response.category_id + "' selected>" +
                        response.category + "</option>");
                    $('#remark').val(response.remark);
                    $('#desc').val(response.AnyOther);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }

    $(document).on('click', '#order_now', function() {
        var indent_order_id = $('#indent_order_id').val();
        $.ajax({
            type: 'post',
            url: 'order_indent_curd.php',
            data: {
                action: 'update_indent',
                indent_order_id: indent_order_id
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {
                  
                    alert('Indent Ordered Successfully..!!!');
                    
                  window.location.href = 'https://po.vnragri.co.in/menu.php';
                } else {
                    alert('Something get wrong! Please try again.');
                }
            }
        });
    });
    

    </script>

</body>

</html>