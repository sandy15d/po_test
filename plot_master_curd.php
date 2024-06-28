<?php
include 'db_connect.php';

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'getplot') {
        $output = array('data' => array());

        $sql = "SELECT plot_id,plot_name,location_name FROM plot LEFT JOIN location ON location.location_id = plot.location_id";
        $query = $connect->query($sql);

        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '
                
            
                <button type ="button" class="btn " data-toggle="modal" data-target="#plotmodal" onclick="editPlot(' . $row['plot_id'] . ')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                
                
                <button type ="button"  class="btn " data-toggle="modal" data-target="#removePlotModal" onclick="removePlot(' . $row['plot_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
                ';


            $output['data'][] = array(
                $x,
                $row['plot_name'],
                $row['location_name'],


                $actionButton

            );

            $x++;
        }

        // database connection close
        $connect->close();

        echo json_encode($output);
    } //-------------------Add/update city
    elseif ($action == 'addplot') {
        $plot_id = $_POST['plot_id'];
        $location_id = $_POST['location_id'];
        $plot_name = $_POST['plot_name'];


        if (!empty($plot_id)) {
            $sql = "UPDATE plot SET plot_name ='$plot_name',location_id ='$location_id' WHERE plot_id =$plot_id";
            $query = $connect->query($sql);
            if ($query === TRUE) {
                $validator['success'] = true;
                $validator['messages'] = "Successfully Updated";
            } else {
                $validator['success'] = false;
                $validator['messages'] = "Error while Updating the Plot information";
            }

            $connect->close();
            echo json_encode($validator);
        } else {
            $search = "SELECT * FROM plot WHERE plot_name ='" . $plot_name . "'";
            $result = $connect->query($search);
            if (mysqli_num_rows($result) > 0) {
                $validator['success'] = false;
                $validator['messages'] = "Plot Already Exist";
                $connect->close();
                echo json_encode($validator);
            } else {
                $sql = "INSERT INTO plot(plot_name,location_id) VALUES('$plot_name','$location_id')";
                $query = $connect->query($sql);
                if ($query === TRUE) {
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Added";
                } else {
                    $validator['success'] = false;
                    $validator['messages'] = "Error while adding the Plot information";
                }

                // close the database connection
                $connect->close();
                echo json_encode($validator);
            }


        }
    } //------------------------Delete City
    elseif ($action == 'delete_plot') {
        # code...
        $plot_id = $_POST['plot_id'];
        $output = array('success' => false, 'messages' => array());

        $sql = "DELETE FROM plot WHERE plot_id = {$plot_id}";
        $query = $connect->query($sql);
        if ($query === TRUE) {
            $output['success'] = true;
            $output['messages'] = 'Successfully removed';
        } else {
            $output['success'] = false;
            $output['messages'] = 'Error while removing the plot information';
        }

        // close database connection
        $connect->close();

        echo json_encode($output);
    } //------------------get single City-----------------

    elseif ($action == 'get_single_plot') {

        $plot_id = $_POST['plot_id'];

        $sql = "SELECT * FROM plot WHERE plot_id=$plot_id";
        $query = $connect->query($sql);
        $output = array('data' => array());
        while ($row = $query->fetch_assoc()) {

            $output = array(
                'plot_id' => $row['plot_id'],
                'location_id' => $row['location_id'],
                'plot_name' => $row['plot_name']


            );
        }
        $connect->close();
        echo json_encode($output);
    }
}
?>