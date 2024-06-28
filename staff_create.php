<?php
    require_once('db_connect.php');
    //-----------------------------//Insert Location-------------------//
    
    if(isset($_POST['action']) && $_POST['action'] == 'add_staff'){
        $location_id = $_POST['location_id'];
        $staff_name = $_POST['staff_name'];
        $designation = $_POST['designation'];

        $sql = "INSERT INTO staff (location_id, staff_name, post_id) VALUES ('$location_id', '$staff_name', '$designation')";
        $query = $connect->query($sql);
        if($query === TRUE) {           
            $validator['success'] = true;
            $validator['messages'] = "Successfully Added"; 
        } else {        
            $validator['success'] = false;
            $validator['messages'] = "Error while adding the Staff information";
        }

        // close the database connection
        $connect->close();
        echo json_encode($validator);
    }
?>