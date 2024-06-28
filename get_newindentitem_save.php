<?php
include"config/config.php";
if(!mysql_num_rows(mysql_query("select * from tbl_po_remark where rval='".$_REQUEST['id']."'")))
mysql_query("insert into tbl_po_remark (rval) values('".$_REQUEST['id']."')");
?>

