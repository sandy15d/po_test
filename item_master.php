<?php
   include 'menu.php';
   include 'db_connect.php';
   ?>
<html>

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

    td {
        color: black;
        weight: bold;
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
        padding: 0px;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="removeMessages"></div>
                 <?php if( $_SESSION["stores_utype"]=="S"){ ?>
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#itemmodal">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add New Item</button>
                <?php }?>
                <div class="col-md-2 pull-left">
                    <form>
                        <input type="text" id="search" name="search" placeholder="Search..."
                            class="pull-left form-control">
                    </form>
                </div>
                <br /><br />
                <table class="table" id="itemlist" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Item Name </th>
                            <th>Group Name</th>
                            <th>Unit</th>
                            <th>Alt. Unit</th>
                            <th>Category Name</th>
                            <th>Action</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody id="recordList"></tbody>
                </table>
              <div class="row">
                  <div class="col-sm-4"><h3 id="total_records"></h3></div>
              </div>
                <div class="pull-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end" id="pagination">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="itemmodal" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add New Item</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <input type="hidden" id="item_id" name="item_id">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Item Group Name <b style="color:red;font-size:14px;">*</b></label>
                                <select id="group_name" name="group_name" class="form-control">
                                    <option value=''>Select Item Group</option>
                                    <?php 
                                            $sql ="SELECT * FROM itemgroup";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['itgroup_id'].'">'.$row['itgroup_name'].'</option>';
                                        }
                                        ?>
                                </select>
                                <div id="group_name_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Item Name<b style="color:red;font-size:14px;">*</b></label>
                                    <input type="text" id="item_name" name="item_name" class="form-control"
                                        list="item_name_list">
                                    <datalist id="item_name_list">
                                        <?php 
                                            $sql ="SELECT * FROM item";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['item_name'].'">';
                                        }
                                        ?>
                                    </datalist>
                                    <div id="item_name_err" style="color:red; display:none;"></div>


                                </div>
                                <div class="col-md-6">
                                    <label>Technical Name</label>
                                    <input type="text" class="form-control" id="tech_name" name="tech_name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Technical Description</label>
                                    <textarea rows="4" cols="57" name="tech_desc" id="tech_desc"
                                        class="form-control"> </textarea>
                                </div>
                                <div class="col-md-3">
                                    <label>Measurement<b style="color:red;font-size:14px;">*</b></label>
                                    <select class="form-control" id="unit_name" id="unit_name">
                                        <option value="">--Select--</option>
                                        <?php 
                                            $sql ="SELECT * FROM unit";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['unit_id'].'">'.$row['unit_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="unit_name_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Is Alt. Measurement?<b style="color:red;font-size:14px;">*</b></label>
                                    <select class="form-control" id="alt_unit_apply" id="alt_unit_apply">
                                        <option value="N">Not Applicable</option>
                                        <option value="A">Applicable</option>
                                    </select>
                                    <div id="alt_unit_err" style="color:red; display:none;"></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Alternate Unit</label>
                                    <select class="form-control" id="alt_unit" id="alt_unit" disabled>
                                        <option value="">--Select--</option>
                                        <?php 
                                            $sql ="SELECT * FROM unit";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['unit_id'].'">'.$row['unit_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>1 <span id="alt_unit_name">Unit</span> =</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="alt_num" name="alt_num"
                                            aria-describedby="basic-addon2" disabled>
                                        <span class="input-group-addon" id="basic-addon2">Unit</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Water requiremnt</label>
                                    <input type="text" id="water_require" name="water_require" class="form-control"
                                        placeholder="Liter / Acer">
                                </div>
                                <div class="col-md-3">
                                    <label>Recommended Dose</label>
                                    <input type="text" id="recommended_dose" name="recommended_dose"
                                        class="form-control" placeholder=" / Acer">
                                </div>
                                <div class="col-md-3">
                                    <label>Maximum Dose</label>
                                    <input type="text" id="max_dose" name="max_dose" class="form-control"
                                        placeholder=" / Acer">
                                </div>
                                <div class="col-md-3">
                                    <label>Minimum Dose</label>
                                    <input type="text" id="min_dose" name="min_dose" class="form-control"
                                        placeholder="/ Acer">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Useful In :<span>&#9312;</span></label>
                                    <select id="usefull_1" name="usefull_1" class="form-control">
                                        <option value=''>--Select--</option>
                                        <?php 
                                            $sql ="SELECT * FROM usability";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['usability_id'].'">'.$row['usability_name'].'</option>';
                                        }
                                        ?>
                                    </select>

                                </div>
                                <div class="col-md-3">
                                    <label>Useful In :<span>&#9313;</span></label>
                                    <select id="usefull_2" name="usefull_2" class="form-control">
                                        <option value=''>--Select--</option>
                                        <?php 
                                            $sql ="SELECT * FROM usability";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['usability_id'].'">'.$row['usability_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Useful In :<span>&#9314;</span></label>
                                    <select id="usefull_3" name="usefull_3" class="form-control">
                                        <option value=''>--Select--</option>
                                        <?php 
                                            $sql ="SELECT * FROM usability";
                                            $query=$connect->query($sql);
                                        while($row=$query->fetch_assoc()){
                                            echo'<option value="'.$row['usability_id'].'">'.$row['usability_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Application Method</label>
                                    <select id="app_method" name="app_method" class="form-control">
                                        <option value="1">Direct</option>
                                        <option value="2">Drip</option>
                                        <option value="3">Spray</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Require Period:<span>&#9312;</span></label>
                                    <select id="rp_form1" name="rp_form1" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><label>To</label>
                                    <select id="rp_to1" name="rp_to1" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Require Period:<span>&#9313;</span></label>
                                    <select id="rp_form2" name="rp_form2" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><label>To</label>
                                    <select id="rp_to2" name="rp_to2" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Require Period:<span>&#9314;</span></label>
                                    <select id="rp_form3" name="rp_form3" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><label>To</label>
                                    <select id="rp_to3" name="rp_to3" class="form-control">
                                        <option value="">--Month--</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sept</option>
                                        <option value="10">Oct</option>
                                        <option value="11">Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Re-Order Level</label>
                                    <input type="text" id="re_order" name="re_order" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Lead Time</label>
                                    <input type="text" id="lead_time" name="lead_time" class="form-control"
                                        placeholder="days">
                                </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="removeItemModal">
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
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
    var page_no = 1;
    var search = '';
    $alt_num = 'unit';
    getRecord(page_no, search);

    function getRecord(page_no, search) {
        $.ajax({
            type: 'POST',
            url: 'item_master_curd.php',
            data: {
                'page_no': page_no,
                'search': search,
                'action': 'get_item_list'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                var s_no = parseInt(parseInt(page_no * 10) - 9);
                var x = '';
                $.each(response[0].data, function(key, value) {
                    x = x + '<tr>' +
                        '<td>' + parseInt(s_no++) + '</td>' +
                        '<td>' + value.item_name + '</td>' +
                        '<td>' + value.itgroup_name + '</td>' +
                        '<td>' + value.unit_name + '</td>' +
                        '<td>' + value.alt_unit_name + '</td>' +
                        '<td>' + value.category_name + '</td>' +
                        '<td>' + value.delete +' '+  value.edit+'</td>' +
                        '<td>' + value.add_category +
                        '</tr>' +
                        '<tr>' +
                        '<td colspan="7" style=" text-align:right">' +
                        '<div id="div_' + value.item_id + '" style="display:none"> ' +
                        '<table style="width:100%">' +
                        '<tr id="category_list_' + value.item_id + '"></tr>' +
                        '<tr>' +

                        '<td style="text-align:right; width:80%; padding-right:10px;">Enter Category Name:<input type="text" id="item_cat_' +
                        value.item_id + '" >' +
                        '<td style="padding:2px;"><button type="button" id="' +
                        value.item_id +
                        '" class="btn btn-primary btn-sm save_category" style="margin:2px;">save</button><button type="button" id="' +
                        value.item_id +
                        '" class="btn btn-danger btn-sm close_div">Close</button>' +
                        '</td>' +
                        '</div>' +
                        '</tr>' +
                        '</table>' +
                        '</div>' +
                        '</td>' +

                        '</tr>';

                });
                $('#recordList').html(x);

                var y =
                    '<li class="page-item"><a class="page-link " href="javascript:void(0);" data-page_no="1">First</a></li>';
                var last_no = parseInt(parseInt(response.total_record) / parseInt(10));
                var reminder = parseInt(parseInt(response.total_record) % parseInt(10))
                if (reminder > 0) {
                    last_no = parseInt(last_no) + parseInt(1);
                }
                $.each(response.page_link.link, function(key, val) {
                    if (page_no == val) {
                        var active = 'active';
                    } else {
                        var active = '';
                    }
                    y = y + '<li class="page-item ' + active + '">' +
                        '<a class="page-link" href="javascript:void(0);" data-page_no="' + val +
                        '">' + val + '</a>' +
                        '</li>';
                    //last_no = val;
                });
                y = y +
                    '<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page_no="' +
                    last_no + '">Last</a></li>';
                $('#pagination').html(y);
                z='Total Records: ';
                z= z + response.total_record;
                $('#total_records').html(z);
            },
        });
    }

    $(document).on('click', '.page-link', function() {
        var search = $('#search').val();
        page_no = $(this).data('page_no');
        getRecord(page_no, search);
    });
    $(document).on('keyup', '#search', function() {
        var search = $(this).val();

        getRecord(page_no, search);
    });
    $(document).on('change', '#alt_unit_apply', function() {
        var alt_unit_apply = $(this).val();
        var unit_name = $('#unit_name option:selected').html();

        if (alt_unit_apply == 'A' && $('#unit_name').val() != '') {
            $('#alt_unit_name').html(unit_name);
            $("#alt_unit").prop("disabled", false);
            $("#alt_num").prop("disabled", false);
        } else {
            $('#alt_unit_name').html('Unit');
            $("#alt_unit").prop('selectedIndex', '');
            $('#basic-addon2').html('Unit');
            $("#alt_unit").prop("disabled", true);
            $("#alt_num").prop("disabled", true);

        }
    });

    $(document).on('change', '#unit_name', function() {
        var unit_name = $('#unit_name option:selected').html();
        var alt_unit_apply = $('#alt_unit_apply').val();
        $('#max_dose').attr("placeholder", unit_name + ' / Acer');
        $('#min_dose').attr("placeholder", unit_name + ' / Acer');
        $('#recommended_dose').attr("placeholder", unit_name + ' / Acer');
        if (alt_unit_apply == 'A') {
            $('#alt_unit_name').html(unit_name);

        } else {
            $('#alt_unit_name').html('Unit');
        }
    });

    $(document).on('change', '#alt_unit', function() {
        var alt_unit_name = $('#alt_unit option:selected').html();
        $('#basic-addon2').html(alt_unit_name);
    });

    //-------------------Save/Update Item Data---------------------

    $(document).on('click', '#save', function() {
        var item_id = $('#item_id').val();
        var item_name = $('#item_name').val();
        var group_name = $('#group_name').val();
        var tech_name = $('#tech_name').val();
        var tech_desc = $('#tech_desc').val();
        var unit_name = $('#unit_name').val();
        var alt_unit_apply = $('#alt_unit_apply').val();
        var alt_unit = $('#alt_unit').val();
        var alt_num = $('#alt_num').val();
        var water_require = $('#water_require').val();
        var recommended_dose = $('#recommended_dose').val();
        var max_dose = $('#max_dose').val();
        var min_dose = $('#min_dose').val();
        var usefull_1 = $('#usefull_1').val();
        var usefull_2 = $('#usefull_2').val();
        var usefull_3 = $('#usefull_3').val();
        var app_method = $('#app_method').val();
        var rp_form1 = $('#rp_form1').val();
        var rp_form2 = $('#rp_form2').val();
        var rp_form3 = $('#rp_form3').val();
        var rp_to1 = $('#rp_to1').val();
        var rp_to2 = $('#rp_to2').val();
        var rp_to3 = $('#rp_to3').val();
        var re_order = $('#re_order').val();
        var lead_time = $('#lead_time').val();


        var formValid = true;

        if (item_name == '') {
            $('#item_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#item_name_err').css('display', 'none');
        }
        if (group_name == '') {
            $('#group_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#group_name_err').css('display', 'none');
        }
        if (unit_name == '') {
            $('#unit_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#unit_name_err').css('display', 'none');
        }
        if (alt_unit_apply == '') {
            $('#alt_unit_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#alt_unit_err').css('display', 'none');
        }
        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'item_master_curd.php',
                data: {
                    'action': 'additem',
                    'item_id': item_id,
                    'item_name': item_name,
                    'group_name': group_name,
                    'tech_name': tech_name,
                    'tech_desc': tech_desc,
                    'unit_name': unit_name,
                    'alt_unit_apply': alt_unit_apply,
                    'alt_unit': alt_unit,
                    'alt_num': alt_num,
                    'water_require': water_require,
                    'recommended_dose': recommended_dose,
                    'max_dose': max_dose,
                    'min_dose': min_dose,
                    'usefull_1': usefull_1,
                    'usefull_2': usefull_2,
                    'usefull_3': usefull_3,
                    'app_method': app_method,
                    'rp_form1': rp_form1,
                    'rp_form2': rp_form2,
                    'rp_form3': rp_form3,
                    'rp_to1': rp_to1,
                    'rp_to2': rp_to2,
                    'rp_to3': rp_to3,
                    're_order': re_order,
                    'lead_time': lead_time
                },

                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $(".removeMessages").html(
                            '<div class="alert alert-success alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                            response.messages +
                            '</div>');

                        // refresh the table


                        // close the modal
                        $("#itemmodal").modal('hide');
                        location.reload();
                    } else {
                        $("#itemmodal").modal('hide');
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

    function editItem(item_id = null) {
        if (item_id) {
            // fetch the member data
            $.ajax({
                url: 'item_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_item',
                    'item_id': item_id
                },
                dataType: 'json',
                success: function(response) {

                    $("#item_id").val(response.item_id);
                    $("#item_name").val(response.item_name);
                    $('#group_name').prepend("<option value='" + response.itgroup_id + "' selected>" +
                        response.itgroup_name + "</option>");
                    $('#tech_name').val(response.tech_name);
                    $('#unit_name').val(response.unit_id);
                    $('#alt_unit_apply').val(response.alt_unit);
                    $('#tech_desc').val(response.tech_details);
                    $('#alt_unit').val(response.alt_unit_id);
                    $('#alt_num').val(response.alt_unit_num);
                    $('#water_require').val(response.water_require);
                    $('#recommended_dose').val(response.recomend_dose);
                    $('#max_dose').val(response.max_dose);
                    $('#min_dose').val(response.min_dose);
                    $('#app_method').val(response.app_method);
                    $('#usefull_1').val(response.usability_id1);
                    $('#usefull_2').val(response.usability_id2);
                    $('#usefull_3').val(response.usability_id3);
                    $('#rp_form1').val(response.rp_from1);
                    $('#rp_to1').val(response.rp_to1);
                    $('#rp_form2').val(response.rp_from2);
                    $('#rp_to2').val(response.rp_to2);
                    $('#rp_form3').val(response.rp_from3);
                    $('#rp_to3').val(response.rp_to3);
                    $('#re_order').val(response.reorder_level);
                    $('#lead_time').val(response.lead_time);

                } // /success
            }); // /fetch selected member info
        } else {
            alert("Error : Refresh the page again");
        }
    }
    
    function deleteItem(Id){
        var Id = Id;
        if (confirm("Are you sure?")) {
        $.ajax({
            type: 'POST',
            url: 'item_master_curd.php',
            data: {

                'Id': Id,
                'action': 'delete_item'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {

                    alert('Item deleted Successfully..');
                    location.reload();
                } else {

                    alert('This Item is used in Indent.. Can not be delete...!!');
                    location.reload();
                }
            },
            error: function() {},
            complete: function() {

            },
        });
        }else {
            location.reload();
            return false;
        }
    } 

    function show_category(item_id = null) {
        if (item_id) {
            $('#div_' + item_id).css('display', 'block');
            getcategory(item_id);
        }
    }

    $(document).on('click', '.close_div', function() {
        var item_id = $(this).attr('id');
        $('#div_' + item_id).css('display', 'none');
    });

    function getcategory(item_id) {
        $.ajax({
            type: 'POST',
            url: 'item_master_curd.php',
            data: {
                'item_id': item_id,

                'action': 'get_category_list'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {

                var x = '';
                $.each(response.data, function(key, value) {
                    x = x +
                        '<tr style="padding:1px;  width:100%;" class="text-primary">' +

                        '<td style="padding:2px; width:900px;">' + value.category + '</td>' +
                        '<td style="padding:2px"><button type="button" class="btn btn-sm delete_cat" id="' +
                        value.category_id +
                        '"><span class="glyphicon glyphicon-trash" style="color:red"></span></button></td>' +
                        '</tr>';




                });
                $('#category_list_' + item_id).html(x);

            },
        });
    }

    $(document).on('click', '.save_category', function() {
        var item_id = $(this).attr('id');
        var category = $('#item_cat_' + item_id).val();
        if (category == '') {
            alert('Category name is required...!!');
        } else {
            $.ajax({
                type: 'POST',
                url: 'item_master_curd.php',
                data: {

                    'item_id': item_id,
                    'category': category,
                    'action': 'save_category'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {

                        alert('Item Category Created Successfully..');
                        location.reload();
                    } else {

                        alert('Somthing Went Wrong..Please try again..!!');
                        location.reload();
                    }
                },
                error: function() {},
                complete: function() {

                },
            });
        }

    });

    $(document).on('click', '.delete_cat', function() {
        var cat_id = $(this).attr('id');
        $.ajax({
            type: 'POST',
            url: 'item_master_curd.php',
            data: {

                'cat_id': cat_id,
                'action': 'delete_category'
            },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                if (response.success == true) {

                    alert('Item Category deleted Successfully..');
                    location.reload();
                } else {

                    alert('This Category is used in Indent.. Can not be delete...!!');
                    location.reload();
                }
            },
            error: function() {},
            complete: function() {

            },
        });
    });
    </script>
</body>

</html>