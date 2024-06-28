<?php 
 
require_once 'db_connect.php';
 
//if form is submitted
if($_POST) {    
 
    $validator = array('success' => false, 'messages' => array());
 
    $staff_id = $_POST['editStaff_id'];
    $staff_name = $_POST['editStaffName'];
    $designation = $_POST['editDesignation'];
    $location   = $_POST['editLocation'];
   
 
    $sql = "UPDATE staff SET staff_name = '$staff_name', post_id = '$designation', location_id = '$location' WHERE staff_id = $staff_id";
    $query = $connect->query($sql);
 
    if($query === TRUE) {           
        $validator['success'] = true;
        $validator['messages'] = "Successfully Updated";      
    } else {        
        $validator['success'] = false;
        $validator['messages'] = "Error while updating the location information";
    }
 
    // close the database connection
    $connect->close();
 
    echo json_encode($validator);
 
}