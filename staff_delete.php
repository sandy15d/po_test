<?php
include 'config/config.php';
mysql_query("delete from staff where staff_id=".$_REQUEST['id']);
?>
