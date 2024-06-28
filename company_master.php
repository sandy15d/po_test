<?php
   include 'menu.php';
   include 'db_connect.php';
   ?>
<html>

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

    td {
        color: black;
        weight: bold;
    }

    td,
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
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="removeMessages"></div>
                <button class="btn btn-default pull pull-right" data-toggle="modal" id="addCompanyModalBtn">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add New Company
                </button>
                <br /> <br /> <br />
                <table class="table" id="companylist" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Company Name</th>
                            <th>Code</th>
                            <th>City Name</th>
                            <th>State Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div style="padding:10px;">
        </div>
    </div>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="addCompany">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Company</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" id="company_id" name="company_id">
                                <label>Company Name <b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                                <div id="company_name_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label>Company Code</label>
                                <input type="text" class="form-control" id="company_code" name="company_code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address1:</label>
                                <input type="text" class="form-control" id="address1" name="address1">
                            </div>
                            <div class="col-md-6">
                                <label>Address 2:</label>
                                <input type="text" class="form-control" id="address2" name="address2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address 3:</label>
                                <input type="text" class="form-control" id="address3" name="address3">
                            </div>
                            <div class="col-md-6">
                                <label>Phone No:</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>City Name</label>
                                <select class="col-md-6 form-control" id="city" name="city">
                                    <option>Select City</option>
                                    <?php 
                                       $sql ="SELECT * FROM city";
                                       $query = $connect->query($sql);
                                       while ($row = $query->fetch_assoc()) {
                                          echo '<option value="'.$row['city_id'].'">'.$row['city_name'].'</option>';
                                         }  
                                    ?>
                                </select>
                                <div id="city_err" style="color:red; display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label>State</label>
                                <input type="text" class="form-control" readonly id="state" name="state">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>E-mail:</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="col-md-6">
                                <label>Fax No:</label>
                                <input type="text" class="form-control" name="fax" id="fax">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>P.A.N.</label>
                                <input type="text" class="form-control" id="pan" name="pan">
                            </div>
                            <div class="col-md-6">
                                <label>G.S.T.</label>
                                <input type="text" class="form-control" id="gst" name="gst">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label>C.I.N.</label>
                                <input type="text" class="form-control" id="cin" name="cin">
                            </div>
                            <div class="col-md-4">
                                <label>T.I.N.:</label>
                                <input type="text" class="form-control" id="tin" name="tin">
                            </div>
                            <div class="col-md-4">
                                <label>C.S.T.</label>
                                <input type="text" class="form-control" id="cst" name="cst">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save_company" name="save_company">Save
                            changes</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /add modal -->

    <!-- view modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="viewCompanyModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> View Company Details
                    </h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Company Name <b style="color:red;font-size:14px;">*</b></label>
                                <input type="text" class="form-control" id="viewcompany_name" name="viewcompany_name"
                                    disabled>

                            </div>
                            <div class="col-md-6">
                                <label>Company Code</label>
                                <input type="text" class="form-control" id="viewcompany_code" name="viewcompany_code"
                                    disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address1:</label>
                                <input type="text" class="form-control" id="viewaddress1" name="viewaddress1" disabled>
                            </div>
                            <div class="col-md-6">
                                <label>Address 2:</label>
                                <input type="text" class="form-control" id="viewaddress2" name="viewaddress2" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address 3:</label>
                                <input type="text" class="form-control" id="viewaddress3" name="viewaddress3" disabled>
                            </div>
                            <div class="col-md-6">
                                <label>Phone No:</label>
                                <input type="text" class="form-control" id="viewphone" name="viewphone" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>City Name</label>
                                <input type="text" class="form-control" id="viewcity" name="viewcity" disabled>
                            </div>
                            <div class="col-md-6">
                                <label>State</label>
                                <input type="text" class="form-control" readonly id="viewstate" name="viewstate"
                                    disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>E-mail:</label>
                                <input type="email" class="form-control" id="viewemail" name="viewemail" disabled>
                            </div>
                            <div class="col-md-6">
                                <label>Fax No:</label>
                                <input type="text" class="form-control" name="viewfax" id="viewfax" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>P.A.N.</label>
                                <input type="text" class="form-control" id="viewpan" name="viewpan" disabled>
                            </div>
                            <div class="col-md-6">
                                <label>G.S.T.</label>
                                <input type="text" class="form-control" id="viewgst" name="viewgst" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label>C.I.N.</label>
                                <input type="text" class="form-control" id="viewcin" name="viewcin" disabled>
                            </div>
                            <div class="col-md-4">
                                <label>T.I.N.:</label>
                                <input type="text" class="form-control" id="viewtin" name="viewtin" disabled>
                            </div>
                            <div class="col-md-4">
                                <label>C.S.T.</label>
                                <input type="text" class="form-control" id="viewcst" name="viewcst" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    <!-- /view modal -->

    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removeComapanyModal">
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
    <!-- /.modal -->
    <!-- /remove modal -->


    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    //----------------Get Company List-----------------
    manageCompanyTable = $("#companylist").DataTable({
        "ajax": {
            url: "company_master_curd.php",
            data: {
                action: 'getCompany'
            },
            type: 'post'
        },

        "order": []
    });
    //--------------------Modal Popup--------------------
    $(document).on('click', '#addCompanyModalBtn', function() {
        $('#addCompany').modal('show');
    });
    //--------------------Get State Name on City Change-------------------
    $(document).on('change', '#city', function() {
        var city = $(this).val();
        $.ajax({
            type: 'post',
            url: "company_master_curd.php",

            data: {
                action: 'getState',
                city: city
            },
            dataType: 'json',
            success: function(response) {
                $('#state').val(response.state_name);
            }

        });
    });
    //---------------------Add New Company Details---------------------------
    $(document).on('click', '#save_company', function() {
        var company_id = $('#company_id').val();
        var company_name = $('#company_name').val();
        var company_code = $('#company_code').val();
        var address1 = $('#address1').val();
        var address2 = $('#address2').val();
        var address3 = $('#address3').val();
        var phone = $('#phone').val();
        var city = $('#city').val();
        var email = $('#email').val();
        var fax = $('#fax').val();
        var pan = $('#pan').val();
        var gst = $('#gst').val();
        var cin = $('#cin').val();
        var tin = $('#tin').val();
        var cst = $('#cst').val();

        var formvalid = true;
        if (company_name == '') {
            $('#company_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#company_name_err').css('display', 'none');
        }
        if (city == '') {
            $('#city_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#city_err').css('display', 'none');
        }

        if (formvalid) {
            $.ajax({
                type: 'post',
                url: 'company_master_curd.php',
                data: {
                    'action': 'addCompany',
                    'company_id': company_id,
                    'company_name': company_name,
                    'company_code': company_code,
                    'address1': address1,
                    'address2': address2,
                    'address3': address3,
                    'phone': phone,
                    'city': city,
                    'email': email,
                    'fax': fax,
                    'pan': pan,
                    'gst': gst,
                    'cin': cin,
                    'tin': tin,
                    'cst': cst,

                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#company_name').val('');
                        $('#company_code').val('');
                        $('#address1').val('');
                        $('#address2').val('');
                        $('#address3').val('');
                        $('#phone').val('');
                        $('#city').prop('selectedIndex', '');
                        $('#email').val('');
                        $('#fax').val('');
                        $('#pan').val('');
                        $('#gst').val('');
                        $('#cin').val('');
                        $('#tin').val('');
                        $('#cst').val('');
                        $("#addCompany").modal('hide');

                        $(".removeMessages").html(
                            '<div class="alert alert-success alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                            response.messages +
                            '</div>');

                        // refresh the table
                        manageCompanyTable.ajax.reload(null, false);

                        // close the modal
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

    //-----------------------Delete Company --------------------
    function removeCompany(company_id = null) {
        if (company_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'company_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_company',
                        company_id: company_id
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

                            // refresh the table
                            manageCompanyTable.ajax.reload(null, false);

                            // close the modal
                            $("#removeComapanyModal").modal('hide');

                        } else {
                            $("#removeComapanyModal").modal('hide');
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
    //------------------------------View Company--------------------------
    function viewCompany(company_id = null) {
        if (company_id) {


            $("#company_id").remove();

            // fetch the member data
            $.ajax({
                url: 'company_master_curd.php',
                type: 'post',
                data: {
                    action: 'get_view',
                    company_id: company_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#viewcompany_name").val(response.company_name);
                    $("#viewcompany_code").val(response.CCode);
                    $("#viewaddress1").val(response.c_address1);
                    $("#viewaddress2").val(response.c_address2);
                    $("#viewaddress3").val(response.c_address3);
                    $("#viewcity").val(response.city_name);
                    $("#viewstate").val(response.state_name);
                    $("#viewphone").val(response.c_phone);
                    $("#viewfax").val(response.c_fax);
                    $("#viewemail").val(response.c_email);
                    $("#viewtin").val(response.c_tin);
                    $("#viewcst").val(response.c_cst);
                    $("#viewcin").val(response.c_cin);
                    $("#viewpan").val(response.c_pan);
                    $("#viewgst").val(response.c_gst);



                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }

    function editCompany(company_id = null) {
        if (company_id) {
            // fetch the member data
            $.ajax({
                url: 'company_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_view',
                    'company_id': company_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#company_id").val(response.company_id);
                    $("#company_name").val(response.company_name);
                    $("#company_code").val(response.CCode);
                    $("#address1").val(response.c_address1);
                    $("#address2").val(response.c_address2);
                    $("#address3").val(response.c_address3);
                    $('#city').prepend("<option value='" + response.city_id + "' selected>" +
                        response.city_name + "</option>");
                    $("#state").val(response.state_name);
                    $("#phone").val(response.c_phone);
                    $("#fax").val(response.c_fax);
                    $("#email").val(response.c_email);
                    $("#tin").val(response.c_tin);
                    $("#cst").val(response.c_cst);
                    $("#cin").val(response.c_cin);
                    $("#pan").val(response.c_pan);
                    $("#gst").val(response.c_gst);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>