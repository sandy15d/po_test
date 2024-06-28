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
        padding: 1px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">

                <div class="removeMessages"></div>
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#postmodal" />
                <span class="glyphicon glyphicon-plus-sign"></span> Add New Post
                </button>
                <br /> <br /> <br />
                <table class="table" id="postlist" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Post Name</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="postmodal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add New Post</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">

                    <div class="modal-body">
                        <div class="messages"></div>

                        <div class="form-group">
                            <label for="post_name" class="col-sm-3 control-label">Post Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="post_name" name="post_name"
                                    placeholder="Enter Post Name">
                                <div id="post_name_err" style="color:red; display:none;"></div>

                            </div>
                        </div>

                        <input type="hidden" class="form-control" id="post_id" name="post_id">

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
    <div class="modal fade" tabindex="-1" role="dialog" id="removePostModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Remove Post</h4>
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
    managePostTable = $("#postlist").DataTable({
        "ajax": {
            url: "designation_master_curd.php",
            data: {
                action: 'getpost'
            },
            type: 'post'
        },

        "order": []
    });

    $(document).on('click', '#save', function() {
        var post_id = $('#post_id').val();
        var post_name = $('#post_name').val();

        var formValid = true;

        if (post_name == '') {
            $('#post_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#post_name_err').css('display', 'none');
        }

        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'designation_master_curd.php',
                data: {
                    'action': 'addpost',
                    'post_id': post_id,
                    'post_name': post_name
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
                        managePostTable.ajax.reload(null, false);

                        // close the modal
                        $("#postmodal").modal('hide');
                    } else {
                        $("#postmodal").modal('hide');
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
    function removePost(post_id = null) {
        if (post_id) {
            // click on remove button
            $("#removeBtn").unbind('click').bind('click', function() {
                $.ajax({
                    url: 'designation_master_curd.php',
                    type: 'post',
                    data: {
                        'action': 'delete_post',
                        post_id: post_id
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
                            managePostTable.ajax.reload(null, false);

                            // close the modal
                            $("#removePostModal").modal('hide');

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

    function editPost(post_id = null) {
        if (post_id) {
            // fetch the member data
            $.ajax({
                url: 'designation_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_post',
                    'post_id': post_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#post_id").val(response.post_id);
                    $("#post_name").val(response.post_name);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>