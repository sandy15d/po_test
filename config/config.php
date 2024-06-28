<?php 
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
define('HOST','localhost');
define('USER','root');
define('PASS','');
define('DATABASE2','po');
define("PAGING","30");
	
$link_stores = mysql_connect(HOST,USER,PASS);
$dblink2_stores = mysql_select_db(DATABASE2,$link_stores);
?>
