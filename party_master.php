<?php include 'menu.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/calendar_eu.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
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
            <div class="row">
                <div class="col-md-4" id="form1">
                    <div class="left-box">
                        <center>
                            <h3>Party Master</h3>
                        </center>
                        <hr>
                        <form id="add_party_form" action="javascript:void(0);">
                            <div class="shadow p-3 mb-5 bg-white rounded">
                                <div class="row">
                                    <input type="hidden" id="party_id" name="party_id">
                                    <div class="col-md-12 mb-3">
                                        <label for="party_name">Party Name:</label>
                                        <input type="text" name="party_name" id="party_name" class="form-control" placeholder="Enter Party Name" />
                                        <div id="party_name_err" class="error" style="display: none;"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_person">Vendor Code:</label>
                                        <input type="text" name="vendor_code" id="vendor_code" class="form-control" placeholder="Enter Vendor Code" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_person">Contact Person:</label>
                                        <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Enter Contact Persona Name" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="party_type">Group:</label>
                                        <input type="text" name="party_type" id="party_type" class="form-control" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="sub_group">Sub Group:</label>
                                        <input type="text" name="sub_group" id="sub_group" class="form-control" />
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="address1">Address 1:</label>
                                        <input type="text" name="address1" id="address1" class="form-control" />
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="address2">Addres 2:</label>
                                        <input type="text" name="address2" id="address2" class="form-control" />
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="address3">Address 3:</label>
                                        <input type="text" name="address3" id="address3" class="form-control" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city">City:</label>
                                        <select id="city" name="city" class="form-control">
                                            <option value="">---Select City---</option>
                                            <?php $sql = "SELECT * FROM city";
                                            $query = $connect->query($sql);
                                            while ($row = $query->fetch_assoc()) {
                                                echo '<option value=' . $row['city_id'] . '>' . $row['city_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <div id="city_err" class="error" style="display: none;"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state">State:</label>
                                        <input type="text" name="state" id="state" disabled class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mobile">Mobile No.:</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email">E-Mail ID.:</label>
                                        <input type="email" name="email" id="email" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="opening_balance">Opening Balance:</label>
                                        <input type="text" name="opening_balance" id="opening_balance" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pan">P.A.N.:</label>
                                        <input type="text" name="pan" id="pan" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tin">T.I.N.:</label>
                                        <input type="text" name="tin" id="tin" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gst">G.S.T.:</label>
                                        <input type="text" name="gst" id="gst" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="credit_days">Credit Days:</label>
                                        <input type="text" name="credit_days" id="credit_days" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="category">Category:</label>
                                        <select id="category" name="category" class="form-control">
                                            <option value="1">Prefrencial</option>
                                            <option value="2">blank-1</option>
                                            <option value="3">blank-2</option>
                                            <option value="4">blank-3</option>
                                        </select>
                                        <div id="category_err" class="error" style="display: none;"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="msme">MSME:</label>
                                        <select id="msme" name="msme" class="form-control">
                                            <option value="N">No</option>
                                            <option value="Y">Yes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3" id="msme_div" style="display: none;">
                                        <label for="msme_number">MSME No:</label>
                                        <input type="text" name="msme_number" id="msme_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status">Status:</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="A">Active</option>
                                            <option value="D">Deactive</option>

                                        </select>
                                        <div id="status_err" class="error" style="display: none;"></div>
                                    </div>

                                    <div class="col-md-12 mb-12">
                                        <div style="padding:5px;"></div>
                                        <button type="reset" class="btn btn-lg btn-danger">Reset</button>
                                        <button class="btn btn-lg btn-primary" id="save" name="save">Save
                                            Changes</button>
                                        <div style="padding:5px;"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8 " id="table1">
                    <div class="right-box">
                        <center>
                            <h3>Party Master</h3>
                        </center>
                        <hr>

                        <div class="removeMessages"></div>
                        <table class="table" id="party_list" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width:30px">S.no</th>
                                    <th style="width:160px">Party Name</th>
                                    <th>Vendor Code</th>
                                    <th>Group</th>
                                    <th>Sub Group</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- remove modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="removePartyModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Party</h4>
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
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        //------------------Get Party List------------------
        managePartyTable = $("#party_list").DataTable({
            "processing": true,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-4'i><'col-sm-4 text-center'l><'col-sm-4'p>>",
            buttons: [{
                    extend: 'copy',
                    text: 'Copy',
                    title: 'User List',
                },

                {
                    extend: 'excel',
                    text: 'Excel',
                    title: 'User List',
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'User List',
                }
            ],
            "ajax": {
                url: "party_master_curd.php",
                data: {
                    action: 'getParty'
                },
                type: 'post'
            },

            "order": []
        });

        $(document).on('click', '#save', function() {

            var party_id = $('#party_id').val();
            var vendor_code = $('#vendor_code').val();
            var party_name = $('#party_name').val();
            var party_type = $('#party_type').val();
            var sub_group = $('#sub_group').val();
            var contact_person = $('#contact_person').val();
            var address1 = $('#address1').val();
            var address2 = $('#address2').val();
            var address3 = $('#address3').val();
            var city = $('#city').val();
            var mobile = $('#mobile').val();
            var email = $('#email').val();
            var opening_balance = $('#opening_balance').val();
            var pan = $('#pan').val();
            var tin = $('#tin').val();
            var gst = $('#gst').val();
            var credit_days = $('#credit_days').val();
            var category = $('#category').val();
            var status = $('#status').val();
            var msme = $("#msme").val();
            var msme_number = $("#msme_number").val();
            var formvalid = true;
            if (party_name == '') {
                $('#party_name_err').html('This field is required.').css('display', 'block');
                formValid = false;
            } else {
                $('#party_name_err').css('display', 'none');
            }


            if (formvalid) {
                $.ajax({
                    type: 'post',
                    url: 'party_master_curd.php',
                    data: {
                        action: 'addParty',
                        'party_id': party_id,
                        'party_name': party_name,
                        'party_type': party_type,
                        'sub_group': sub_group,
                        'vendor_code': vendor_code,
                        'contact_person': contact_person,
                        'address1': address1,
                        'address2': address2,
                        'address3': address3,
                        'city': city,
                        'mobile': mobile,
                        'email': email,
                        'opening_balance': opening_balance,
                        'pan': pan,
                        'tin': tin,
                        'gst': gst,
                        'credit_days': credit_days,
                        'category': category,
                        'msme': msme,
                        'msme_number': msme_number,
                        'status': status,
                    },
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.success == true) {
                            $("#party_name").val('');
                            $("#vendor_code").val('');
                            $("#party_type").val('');
                            $("#sub_group").val('');
                            $("#contact_person").val('');
                            $("#address1").val('');
                            $("#address2").val('');
                            $("#address3").val('');
                            $("#email").val('');
                            $("#mobile").val('');
                            $('#pan').val('');
                            $('#tin').val('');
                            $('#gst').val('');
                            $('#opening_balance').val('');
                            $('#credit_days').val('');
                            $('#category').val('');
                            $("#msme_number").val('');
                            $('#city').prop('selectedIndex', '');
                            $('#state').val('');


                            $(".removeMessages").html(
                                '<div class="alert alert-success alert-dismissible" role="alert">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                                response.messages +
                                '</div>');

                            // refresh the table
                            managePartyTable.ajax.reload(null, false);

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

        //--------------------Get State Name on City Change-------------------
        $(document).on('change', '#city', function() {
            var city = $(this).val();
            $.ajax({
                type: 'post',
                url: "party_master_curd.php",

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

        //-----------------------Get Data for edit----------------
        function editParty(party_id = null) {
            if (party_id) {

                $.ajax({
                    url: 'party_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'get_edit',
                        'party_id': party_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#party_id").val(response.party_id);
                        $("#party_name").val(response.party_name);
                        $("#vendor_code").val(response.code);
                        $("#party_type").val(response.party_type);
                        $("#sub_group").val(response.sub_group);
                        $("#contact_person").val(response.contact_person);
                        $("#address1").val(response.address1);
                        $("#address2").val(response.address2);
                        $("#address3").val(response.address3);
                        $("#email").val(response.email_id);
                        $("#mobile").val(response.mobile_no);
                        $('#pan').val(response.pan);
                        $('#tin').val(response.tin);
                        $('#gst').val(response.gstno);
                        $('#opening_balance').val(response.op_balance);
                        $('#credit_days').val(response.credit_days);
                        $('#category').val(response.category);
                        $('#city').prepend("<option value='" + response.city_id + "' selected>" +
                            response.city_name + "</option>");
                        $('#status').val(response.status);
                        $("#msme").val(response.msme);
                        if(response.msme == 'Y'){
                            $("#msme_number").val(response.msme_number);
                            $("#msme_div").css('display','block');
                        }
                        $('#state').val(response.state_name);

                    } // /success
                }); // /fetch selected member info

            } else {
                alert("Error : Refresh the page again");
            }
        }

        //----------------Delete Party-------------

        function removeParty(party_id = null) {
            if (party_id) {
                // click on remove button
                $("#removeBtn").unbind('click').bind('click', function() {
                    $.ajax({
                        url: 'party_master_curd.php',
                        type: 'post',
                        data: {
                            'action': 'delete_party',
                            party_id: party_id
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
                                managePartyTable.ajax.reload(null, false);

                                // close the modal
                                $("#removePartyModal").modal('hide');

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

        //=================================
        $(document).on('change', '#msme', function() {
            var msme = $(this).val();
            if (msme === 'Y') {
                $("#msme_div").css('display', 'block');
                $("#msme_number").attr('required', true);
            } else {
                $("#msme_div").css('display', 'none');
                $("#msme_number").removeAttr('required', false);
            }
        });
    </script>
</body>

</html>