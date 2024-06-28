<?php

 require_once 'db_connect.php'; 
 
//----------------------------//Retrive Location------------------//
 
 
$output = array('data' => array());
 
$sql = "SELECT * FROM location";
$query = $connect->query($sql);
 
$x = 1;
while ($row = $query->fetch_assoc()) {
$actionButton = '
    

    <button type ="button" class="btn" data-toggle="modal" data-target="#editLocationModal" onclick="editLocation('.$row['location_id'].')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
    
    
    <button type ="button"  class="btn" data-toggle="modal" data-target="#removeLocationModal" onclick="removeLocation('.$row['location_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
    ';
$staff = '<a class="btn " href="staffview_new.php?LocationId='.$row['location_id'].'"><span class="glyphicon glyphicon-link" style="color:blue"></a>';
    
 
    $output['data'][] = array(
        $x,
        $row['location_name'],
        $row['location_prefix'],
        $row['location_suffix'],
        $actionButton,
        $staff
    );
 
    $x++;
}
 
// database connection close
$connect->close();
 
echo json_encode($output);