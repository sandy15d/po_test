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

    tr {
        height: 10px;
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
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">

                <div class="removeMessages"></div>
                <button class="btn btn-default pull pull-right" data-toggle="modal" data-target="#category_modal" />
                <span class="glyphicon glyphicon-plus-sign"></span> Add Item Category
                </button>
                <br /> <br /> <br />
                <table class="table" id="category_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width:30px">S.no</th>
                            <th>Category Name</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
    </div>
    <!-- add modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="category_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-plus-sign"></span> Add Item Category</h4>
                </div>
                <form class="form-horizontal" action="javascript:void(0);">

                    <div class="modal-body">
                        <div class="messages"></div>

                        <div class="form-group">
                            <label for="category_name" class="col-md-3">Category Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="category_name" name="category_name"
                                    placeholder="Enter Item Group Name">
                                <div id="category_name_err" style="color:red; display:none;"></div>

                            </div>
                        </div>

                        <input type="hidden" class="form-control" id="category_id" name="category_id">

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



    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    manageGroupTable = $("#category_list").DataTable({
        "ajax": {
            url: "category_master_curd.php",
            data: {
                action: 'get_category'
            },
            type: 'post'
        },

        "order": []
    });

    $(document).on('click', '#save', function() {
        var category_id = $('#category_id').val();
        var category_name = $('#category_name').val();

        var formValid = true;

        if (category_name == '') {
            $('#category_name_err').html('This field is required.').css('display', 'block');
            formValid = false;
        } else {
            $('#category_name_err').css('display', 'none');
        }

        if (formValid) {
            $.ajax({
                type: 'post',
                url: 'category_master_curd.php',
                data: {
                    'action': 'addcategory',
                    'category_id': category_id,
                    'category_name': category_name

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
                        manageGroupTable.ajax.reload(null, false);

                        // close the modal
                        $("#category_modal").modal('hide');
                    } else {
                        $("#category_modal").modal('hide');
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



    function editCategory(category_id = null) {
        if (category_id) {
            // fetch the member data
            $.ajax({
                url: 'category_master_curd.php',
                type: 'post',
                data: {
                    'action': 'get_single_category',
                    'category_id': category_id
                },
                dataType: 'json',
                success: function(response) {
                    $("#category_id").val(response.itcategory_id);
                    $("#category_name").val(response.itcategory_name);
                } // /success
            }); // /fetch selected member info

        } else {
            alert("Error : Refresh the page again");
        }
    }
    </script>
</body>

</html>