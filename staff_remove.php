<?php 
 
require_once 'db_connect.php';
 
$output = array('success' => false, 'messages' => array());
 
$staffId = $_POST['staff_id'];
 
$sql = "DELETE FROM staff WHERE staff_id = {$staffId}";
$query = $connect->query($sql);
if($query === TRUE) {
    $output['success'] = true;
    $output['messages'] = 'Successfully removed';
} else {
    $output['success'] = false;
    $output['messages'] = 'Error while removing the staff information';
}
 
// close database connection
$connect->close();
 
echo json_encode($output);
?>