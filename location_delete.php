<?php
include 'config/config.php';
mysql_query("delete from location where location_id=".$_REQUEST['id']);
?>

