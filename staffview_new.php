<?php
   include 'menu.php';
    require_once 'db_connect.php'; 
    $location_id = $_REQUEST[LocationId];
    $sql = "SELECT * from designation";
    $query = $connect->query($sql);
    
    
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
    </style>
</head>

<body>
    <input type="hidden" id="hidden_location_id" name="hidden_location_id" class="form-control"
        value="<?php echo $location_id;?>">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="removeMessages"></div>
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#addStaff"
                    id="addStaffModalBtn">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add Staff
                </button>
                <br /> <br /> <br />
                <table class="table" id="stafflist" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Location</th>
                            <th>Staff Name</th>
                            <th>Designation</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="addStaff">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Staff</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <input type="hidden" id="location_id" name="location_id" value="<?php echo $location_id;?>">
                        <div class="form-group">
                            <label for="staff_name" class="col-sm-2 control-label">Staff Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="staff_name" name="staff_name"
                                    placeholder="Enter Staff Name">
                                <div id="staff_name_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="designation" class="col-sm-2 control-label">Designation</label>
                            <div class="col-sm-10">
                                <select id="designation" name="designation" class="form-control">
                                    <option value="">Select Designation</option>
                                    <?php   while ($row = $query->fetch_assoc()) {
                                        echo '<option value="'.$row['post_id'].'">'.$row['post_name'].'</option>';
                                }  
                               ?>
                                </select>
                                <div id="designation_err" style="color:red; display:none;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="save_staff" class="btn btn-primary">Save changes</button>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="removeStaffModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Staff</h4>
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

    <!-- edit modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editStaffModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-edit"></span> Edit Staff Information</h4>
                </div>

                <form class="form-horizontal" action="staff_update.php" method="POST" id="updateStaffForm">

                    <div class="modal-body">

                        <div class="edit-messages"></div>
                        <div class="form-group">
                            <label for="editDesignation" class="col-sm-2 control-label">Designation</label>
                            <div class="col-sm-10">
                                <select id="editLocation" name="editLocation" class="form-control">

                                    <?php  
                                        $sql = "SELECT * from location";
                                    $query = $connect->query($sql); 
                                    while ($row = $query->fetch_assoc()) {
                                        echo '<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
                                }  
                               ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <!--/here teh addclass has-error will appear -->
                            <label for="editName" class="col-sm-2 control-label">Staff_Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editStaffName" name="editStaffName"
                                    placeholder="Staff Name">
                                <!-- here the text will apper  -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editDesignation" class="col-sm-2 control-label">Designation</label>
                            <div class="col-sm-10">
                                <select id="editDesignation" name="editDesignation" class="form-control">

                                    <?php  
                                        $sql = "SELECT * from designation";
                                    $query = $connect->query($sql); 
                                    while ($row = $query->fetch_assoc()) {
                                        echo '<option value="'.$row['post_id'].'">'.$row['post_name'].'</option>';
                                }  
                               ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10">
                                <input type="hidden" class="form-control" id="editStaff_id" name="editStaff_id">

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer editMemberModal">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- /edit modal -->

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    manageStaffTable = $("#stafflist").DataTable({
        "ajax": {
            'url': 'staff_list.php',
            'data': function(data) {
                data.locationid = $("#hidden_location_id").val();
            }
        },
        "order": []
    });
    //--------------------Add Location-----------------------//
    $(document).on('click', '#addStaffModalBtn', function() {
        $('#addStaff').modal('show');
    });

    //---------add staff-----------------
    $(document).on('click', '#save_staff', function() {
        var staff_name = $('#staff_name').val();
        var designation = $('#designation').val();
        var formValid = true;
        if (staff_name == '') {
            $('#staff_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#staff_name_err').css('display', 'none');
        }

        if (designation == '') {
            $('#designation_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#designation_err').css('display', 'none');
        }

        if (formValid) {
            $.ajax({
                type: 'POST',
                url: 'staff_create.php',
                data: {
                    'staff_name': staff_name,
                    'designation': designation,
                    'location_id': $('#location_id').val(),
                    'action': 'add_staff'
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.success == true) {
                        $('#staff_name').val('');
                        $('#designation').prop('selectedIndex', '');
                        $("#addStaff").modal('hide');

                        $(".removeMessages").html(
                            '<div class="alert alert-success alert-dismissible" role="alert">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                            response.messages +
                            '</div>');

                        // refresh the table
                        manageStaffTable.ajax.reload(null, false);
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



    //------------------------Remove Location
    function removeStaff(staff_id = null) {
        if (staff_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'staff_remove.php',
                    type: 'post',
                    data: {
                        staff_id: staff_id
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

                            // refresh the table
                            manageStaffTable.ajax.reload(null, false);

                            // close the modal
                            $("#removeStaffModal").modal('hide');

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
    //--------------Edit Location Data------------------------
    function editStaff(staff_id = null) {
        if (staff_id) {

            // remove the error 
            $(".form-group").removeClass('has-error').removeClass('has-success');
            $(".text-danger").remove();
            // empty the message div
            $(".edit-messages").html("");

            // remove the id
            $("#staff_id").remove();

            // fetch the member data
            $.ajax({
                url: 'staff_selected.php',
                type: 'post',
                data: {
                    staff_id: staff_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#editStaffName").val(response.staff_name);

                    $('#editDesignation').prepend("<option value='" + response.post_id + "' selected>" +
                        response.post_name + "</option>");
                    $('#editLocation').prepend("<option value='" + response.location_id + "' selected>" +
                        response.location_name + "</option>");
                    // $("#editDesignation").val(response.post_id);


                    $("#editStaff_id").val(response.staff_id);


                    // here update the member data
                    $("#updateStaffForm").unbind('submit').bind('submit', function() {
                        // remove error messages
                        $(".text-danger").remove();

                        var form = $(this);

                        // validation
                        var editStaffName = $("#editStaffName").val();
                        var editDesignation = $("#editDesignation").val();
                        var editLocation = $("#editLocation").val();



                        if (editStaffName == "") {
                            $("#editStaffName").closest('.form-group').addClass('has-error');
                            $("#editStaffName").after(
                                '<p class="text-danger">The Location Name field is required</p>'
                                );
                        } else {
                            $("#editStaffName").closest('.form-group').removeClass('has-error');
                            $("#editStaffName").closest('.form-group').addClass('has-success');
                        }



                        if (editStaffName) {
                            $.ajax({
                                url: form.attr('action'),
                                type: form.attr('method'),
                                data: form.serialize(),
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success == true) {
                                        $(".edit-messages").html(
                                            '<div class="alert alert-success alert-dismissible" role="alert">' +
                                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                                            response.messages +
                                            '</div>');

                                        // reload the datatables
                                        manageStaffTable.ajax.reload(null, false);
                                        // this function is built in function of datatables;

                                        // remove the error 
                                        $(".form-group").removeClass('has-success')
                                            .removeClass('has-error');
                                        $(".text-danger").remove();
                                    } else {
                                        $(".edit-messages").html(
                                            '<div class="alert alert-warning alert-dismissible" role="alert">' +
                                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                            '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                                            response.messages +
                                            '</div>')
                                    }
                                } // /success
                            }); // /ajax
                        } // /if

                        return false;
                    });

                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>