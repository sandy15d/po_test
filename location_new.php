<?php
   include 'menu.php';
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
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#addLocation"
                    id="addLocationModalBtn">
                    <span class="glyphicon glyphicon-plus-sign"></span> Add Location
                </button>
                <br /> <br /> <br />
                <table class="table" id="locationList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Location</th>
                            <th>Prefix</th>
                            <th>Suffix</th>
                            <th>Action</th>
                            <th>Staff</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div style="padding:10px;">
        </div>
    </div>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="addLocation">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Location</h4>
                </div>
                <form class="form-horizontal" action="location_create.php" method="POST" id="createLocationForm">
                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Location Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="location_name" name="location_name"
                                    placeholder="Enter Location Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Location Prefix</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="location_prefix" name="location_prefix"
                                    placeholder="Location Prefix">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Location Suffix</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="location_suffix" name="location_suffix"
                                    placeholder="Location Suffix">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="removeLocationModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Location</h4>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="editLocationModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-edit"></span> Edit Location Data</h4>
                </div>

                <form class="form-horizontal" action="location_update.php" method="POST" id="updateLocationForm">

                    <div class="modal-body">

                        <div class="edit-messages"></div>

                        <div class="form-group">
                            <!--/here teh addclass has-error will appear -->
                            <label for="editName" class="col-sm-2 control-label">Location Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editLocationName" name="editLocationName"
                                    placeholder="Location Name">
                                <!-- here the text will apper  -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editAddress" class="col-sm-2 control-label">Location Prefix</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editLocationPrefix"
                                    name="editLocationPrefix" placeholder="Address">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editContact" class="col-sm-2 control-label">Location Suffix</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="editLocationSuffix"
                                    name="editLocationSuffix" placeholder="Contact">

                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-sm-10">
                                <input type="hidden" class="form-control" id="editLocation_id" name="editLocation_id">

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
    manageLocationTable = $("#locationList").DataTable({
        "ajax": "location_list.php",
        "order": []
    });
    //--------------------Add Location-----------------------//
    $("#addLocationModalBtn").on('click', function() {
        // reset the form 
        $("#createLocationForm")[0].reset();
        // remove the error 
        $(".form-group").removeClass('has-error').removeClass('has-success');
        $(".text-danger").remove();
        // empty the message div
        $(".messages").html("");

        // submit form
        $("#createLocationForm").unbind('submit').bind('submit', function() {

            $(".text-danger").remove();

            var form = $(this);

            // validation
            var location_name = $("#location_name").val();
            var location_prefix = $("#location_prefix").val();
            var location_suffix = $("#location_suffix").val();
            console.log(location_name);
            if (location_name == "") {

                $("#location_name").closest('.form-group').addClass('has-error');
                $("#location_name").after('The Location Name is required');

            } else {
                $("#location_name").after('');
                $("#location_name").closest('.form-group').removeClass('has-error');
                $("#location_name").closest('.form-group').addClass('has-success');
            }

            if (location_prefix == "") {
                $("#location_prefix").closest('.form-group').addClass('has-error');
                $("#location_prefix").after('The Location Prefix is required');
            } else {
                $("#location_prefix").closest('.form-group').removeClass('has-error');
                $("#location_prefix").closest('.form-group').addClass('has-success');
            }

            if (location_suffix == "") {
                $("#location_suffix").closest('.form-group').addClass('has-error');
                $("#location_suffix").after('The Location Suffix is required');
            } else {
                $("#location_suffix").closest('.form-group').removeClass('has-error');
                $("#location_suffix").closest('.form-group').addClass('has-success');
            }



            if (location_name && location_prefix && location_suffix) {
                //submi the form to server
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {

                        // remove the error 
                        $(".form-group").removeClass('has-error').removeClass(
                            'has-success');

                        if (response.success == true) {
                            $(".messages").html(
                                '<div class="alert alert-success alert-dismissible" role="alert">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>' +
                                response.messages +
                                '</div>');

                            // reset the form
                            $("#createLocationForm")[0].reset();

                            // reload the datatables
                            manageLocationTable.ajax.reload(null, false);
                            // this function is built in function of datatables;
                        } else {
                            $(".messages").html(
                                '<div class="alert alert-warning alert-dismissible" role="alert">' +
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>' +
                                response.messages +
                                '</div>');
                        } // /else
                    } // success 
                }); // ajax subit               
            } /// if


            return false;
        }); // 
    }); // /add modal
    //------------------------Remove Location
    function removeLocation(location_id = null) {
        if (location_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'location_remove.php',
                    type: 'post',
                    data: {
                        location_id: location_id
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
                            manageLocationTable.ajax.reload(null, false);

                            // close the modal
                            $("#removeLocationModal").modal('hide');

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
    function editLocation(location_id = null) {
        if (location_id) {

            // remove the error 
            $(".form-group").removeClass('has-error').removeClass('has-success');
            $(".text-danger").remove();
            // empty the message div
            $(".edit-messages").html("");

            // remove the id
            $("#location_id").remove();

            // fetch the member data
            $.ajax({
                url: 'location_selected.php',
                type: 'post',
                data: {
                    location_id: location_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#editLocationName").val(response.location_name);

                    $("#editLocationPrefix").val(response.location_prefix);

                    $("#editLocationSuffix").val(response.location_suffix);

                    $("#editLocation_id").val(response.location_id);


                    // location id 
                    // $(".editLocationModal").append('<input type="hidden" name="location_id" id="location_id" value="'+response.location_id+'"/>');

                    // here update the member data
                    $("#updateLocationForm").unbind('submit').bind('submit', function() {
                        // remove error messages
                        $(".text-danger").remove();

                        var form = $(this);

                        // validation
                        var editLocationName = $("#editLocationName").val();
                        var editLocationPrefix = $("#editLocationPrefix").val();
                        var editLocationSuffix = $("#editLocationSuffix").val();


                        if (editLocationName == "") {
                            $("#editLocationName").closest('.form-group').addClass('has-error');
                            $("#editLocationName").after(
                                '<p class="text-danger">The Location Name field is required</p>'
                            );
                        } else {
                            $("#editLocationName").closest('.form-group').removeClass('has-error');
                            $("#editLocationName").closest('.form-group').addClass('has-success');
                        }

                        if (editLocationPrefix == "") {
                            $("#editLocationPrefix").closest('.form-group').addClass('has-error');
                            $("#editLocationPrefix").after(
                                '<p class="text-danger">The Location Prefix field is required</p>'
                            );
                        } else {
                            $("#editLocationPrefix").closest('.form-group').removeClass(
                                'has-error');
                            $("#editLocationPrefix").closest('.form-group').addClass('has-success');
                        }

                        if (editLocationSuffix == "") {
                            $("#editLocationSuffix").closest('.form-group').addClass('has-error');
                            $("#editLocationSuffix").after(
                                '<p class="text-danger">The Location Suffix field is required</p>'
                            );
                        } else {
                            $("#editLocationSuffix").closest('.form-group').removeClass(
                                'has-error');
                            $("#editLocationSuffix").closest('.form-group').addClass('has-success');
                        }

                        if (editLocationName && editLocationPrefix && editLocationSuffix) {
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
                                        manageLocationTable.ajax.reload(null, false);
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