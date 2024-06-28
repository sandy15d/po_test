<?php
    require_once('db_connect.php');
    //-----------------------------//Insert Location-------------------//

 
    $location_name = $_POST['location_name'];
    $location_prefix = $_POST['location_prefix'];
    $location_suffix = $_POST['location_suffix'];

    $check_location = "SELECT * FROM location WHERE location_name='".$location_name."'";
    $result = $connect->query($check_location);
    if(mysqli_num_rows($result)>0){
        $validator['success'] = false;
        $validator['messages'] = "Location Already Exist"; 
        $connect->close();
        echo json_encode($validator);
    }else{
            $sql = "INSERT INTO location (location_name, location_prefix, location_suffix) VALUES ('$location_name', '$location_prefix', '$location_suffix')";
    $query = $connect->query($sql);
 
    if($query === TRUE) {           
        $validator['success'] = true;
        $validator['messages'] = "Successfully Added"; 
    } else {        
        $validator['success'] = false;
        $validator['messages'] = "Error while adding the location information";
    }
 
    // close the database connection
    $connect->close();
    echo json_encode($validator);
    }
    
    
?>