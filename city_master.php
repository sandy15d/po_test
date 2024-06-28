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

    td {
        color: black;
        weight: bold;
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

    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>tfoot>tr>td,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
        padding: 1px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="removeMessages"></div>
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#citymodal" />
                <span class="glyphicon glyphicon-plus-sign"></span> Add City
                </button>
                <br /> <br /> <br />
                <table class="table" id="citylist" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>City Name</th>
                            <th>State</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="citymodal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add City</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">

                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="form-group">

                            <label for="name" class="col-sm-2 control-label">State</label>
                            <div class="col-sm-10">
                                <select id="state" name="state" class="form-control">

                                    <?php 
                                            $sql= "SELECT * FROM state";
                                            $query = $connect->query($sql);
                                            while($row =$query->fetch_assoc()){
                                                echo'<option value='.$row['state_id'].'>'.$row['state_name'].'</option>';
                                            }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">City Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="city_name" name="city_name"
                                    placeholder="Enter City Name">
                                <div id="city_name_err" style="color:red; display:none;"></div>

                            </div>
                        </div>

                        <input type="hidden" class="form-control" id="city_id" name="city_id">

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
    <div class="modal fade" tabindex="-1" role="dialog" id="removeCityModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove City</h4>
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
    manageCityTable = $("#citylist").DataTable({
        "ajax": {
            url: "city_master_curd.php",
            data: {
                action: 'getcity'
            },
            type: 'post'
        },

        "order": []
    });

    $(document).on('click', '#save', function() {
        var city_id = $('#city_id').val();
        var city_name = $('#city_name').val();
        var state = $('#state').val();

        var formValid = true;

        if (city_name == '') {
            $('#city_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#city_name_err').css('display', 'none');
        }

        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'city_master_curd.php',
                data: {
                    'action': 'addcity',
                    'city_id': city_id,
                    'city_name': city_name,
                    'state': state
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
                        manageCityTable.ajax.reload(null, false);

                        // close the modal
                        $("#citymodal").modal('hide');
                    } else {
                        $("#citymodal").modal('hide');
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

    //------------------Remove City--------------------
    function removeCity(city_id = null) {
        if (city_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'city_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_city',
                        city_id: city_id
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
                            manageCityTable.ajax.reload(null, false);

                            // close the modal
                            $("#removeCityModal").modal('hide');

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

    function editCity(city_id = null) {
        if (city_id) {
            // fetch the member data
            $.ajax({
                url: 'city_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_city',
                    'city_id': city_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#city_id").val(response.city_id);
                    $("#city_name").val(response.city_name);
                    $('#state').prepend("<option value='" + response.state_id + "' selected>" +
                        response.state_name + "</option>");
                } // /success
            }); // /fetch selected member info
        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>