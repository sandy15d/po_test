<?php

 require_once 'db_connect.php'; 
 
//----------------------------//Retrive Location------------------//
if(isset($_REQUEST["locationid"]))
  {
    $locationid = $_REQUEST["locationid"];

   }
 
$output = array('data' => array());
 
$sql = "SELECT * FROM staff s JOIN designation d ON d.post_id = s.post_id JOIN location l ON l.location_id=s.location_id WHERE l.location_id=$locationid";
$query = $connect->query($sql);
 
$x = 1;
while ($row = $query->fetch_assoc()) {
$actionButton = '
    

    <button type ="button" class="btn btn-sm" data-toggle="modal" data-target="#editStaffModal" onclick="editStaff('.$row['staff_id'].')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
    
    
    <button type ="button"  class="btn btn-sm" data-toggle="modal" data-target="#removeStaffModal" onclick="removeStaff('.$row['staff_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
    ';

    
 
    $output['data'][] = array(
        $x,
        $row['location_name'],
        $row['staff_name'],
        $row['post_name'],
        $actionButton,
    );
 
    $x++;
}
 
// database connection close
$connect->close();
 
echo json_encode($output);