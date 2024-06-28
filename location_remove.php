<?php 
 
require_once 'db_connect.php';
 
$output = array('success' => false, 'messages' => array());
 
$locationId = $_POST['location_id'];
 
$sql = "DELETE FROM location WHERE location_id = {$locationId}";
$query = $connect->query($sql);
if($query === TRUE) {
    $output['success'] = true;
    $output['messages'] = 'Successfully removed';
} else {
    $output['success'] = false;
    $output['messages'] = 'Error while removing the member information';
}
 
// close database connection
$connect->close();
 
echo json_encode($output);
?>