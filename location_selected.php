<?php 
 
require_once 'db_connect.php';
 
$locationId = $_POST['location_id'];
 
$sql = "SELECT * FROM location WHERE location_id = $locationId";
$query = $connect->query($sql);
$result = $query->fetch_assoc();
 
$connect->close();
 
echo json_encode($result);
 
?>