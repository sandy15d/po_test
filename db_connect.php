<?php 
 
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "po"; 
 
// create connection 
$connect = mysqli_connect($servername, $username, $password, $dbname); 
 
// check connection 
if($connect->connect_error) {
    die("Connection Failed : " . $connect->connect_error);
} else {
    // echo "Successfully Connected";
}
 
?>