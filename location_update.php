<?php 
 
require_once 'db_connect.php';
 
//if form is submitted
if($_POST) {    
 
    $validator = array('success' => false, 'messages' => array());
 
    $location_id = $_POST['editLocation_id'];
    $location_name = $_POST['editLocationName'];
    $location_prefix = $_POST['editLocationPrefix'];
    $location_suffix = $_POST['editLocationSuffix'];
   
 
    $sql = "UPDATE location SET location_name = '$location_name', location_prefix = '$location_prefix', location_suffix = '$location_suffix' WHERE location_id = $location_id";
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