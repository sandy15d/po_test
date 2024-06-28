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
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#plotmodal" />
                <span class="glyphicon glyphicon-plus-sign"></span> Add New Plot
                </button>
                <br /> <br /> <br />
                <table class="table" id="citylist" class="table table-bordered table-striped">
                    <thead>
                        <tr class="table-primary">
                            <th style="width:30px">S.no</th>
                            <th>Plot Name</th>
                            <th>Location</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="plotmodal" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Plot</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">

                    <div class="modal-body">
                        <div class="messages"></div>
                        <div class="data-heading">
                            <div class="form-group mb-3">
                               
                                <div class="col-md-12">
                                <label for="plot_name">Plot Name</label>
                                    <input type="text" class="form-control" id="plot_name" name="plot_name" placeholder="Enter Plot Name">
                                    <div id="plot_name_err" style="color:red; display:none;"></div>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label for="location_id">Location:</label>
                                    <select name="location_id" id="location_id" class="form-control form-control-sm">
                                        <option value="">Select Location</option>
                                        <?php
                                        $sql = "SELECT * FROM location ORDER BY location_name";
                                        $query = $connect->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            echo '<option value="' . $row['location_id'] . '">' . $row['location_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div id="location_id_err" style="color:red; display:none;"></div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="plot_id" name="plot_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="removePlotModal" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-header">

                    <h4 class="text-center mb-0">Remove Plot</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to remove ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="removeBtn">Yes, Delete it</button>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        managePlotTable = $("#citylist").DataTable({
            "ajax": {
                url: "plot_master_curd.php",
                data: {
                    action: 'getplot'
                },
                type: 'post'
            },

            "order": []
        });

        $(document).on('click', '#save', function () {
        var plot_id = $('#plot_id').val();
        var location_id = $('#location_id').val();
        var plot_name = $('#plot_name').val();

        var formValid = true;

        if (plot_name == '') {
            $('#plot_name_err').html('Plot Name is required').css('display', 'block');
            formValid = false;
        } else {
            $('#plot_name_err').css('display', 'none');
        }

        if (location_id == '') {
            $('#location_id_err').html('Location is required').css('display', 'block');
            formValid = false;
        } else {
            $('#location_id_err').css('display', 'none');
        }


        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'plot_master_curd.php',
                data: {
                    'action': 'addplot',
                    'plot_id': plot_id,
                    'location_id': location_id,
                    'plot_name': plot_name
                },
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (response) {
                    if (response.success == true) {

                       alert(response.messages);
                        // refresh the table
                        managePlotTable.ajax.reload(null, false);

                        // close the modal
                        $("#plotmodal").modal('hide');
                    } else {
                        $("#plotmodal").modal('hide');
                        alert(response.messages);
                    }
                },

            });
        }

    });

    //------------------Remove City--------------------
    function removePlot(plot_id = null) {
        if (plot_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function () {
                $.ajax({
                    url: 'plot_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_plot',
                        plot_id: plot_id
                    },
                    dataType: 'json',
                    success: function (response) {
                        // console.log(response);
                        if (response.success == true) {
                            alert(response.messages);

                            // refresh the table
                            managePlotTable.ajax.reload(null, false);

                            // close the modal
                            $("#removePlotModal").modal('hide');

                        } else {
                            alert(response.messages);
                        }
                    }
                });
            }); // click remove btn
        } else {
            alert('Error: Refresh the page again');
        }
    }

        function editPlot(plot_id = null) {
        if (plot_id) {
            // fetch the member data
            $.ajax({
                url: 'plot_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_plot',
                    'plot_id': plot_id
                },
                dataType: 'json',
                success: function (response) {
                    $("#plot_id").val(response.plot_id);
                    $("#location_id").val(response.location_id);
                    $("#plot_name").val(response.plot_name);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>