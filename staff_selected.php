<?php 
 
require_once 'db_connect.php';
 
$staffId = $_POST['staff_id'];
 
$sql = "SELECT * FROM staff s JOIN designation d ON d.post_id = s.post_id JOIN location l ON l.location_id = s.location_id WHERE staff_id = $staffId";
$query = $connect->query($sql);
$result = $query->fetch_assoc();
 
$connect->close();
 
echo json_encode($result);
 
?>