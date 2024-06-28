
<?php
include"config/config.php";
$val=$_REQUEST["val"];
$data=mysql_query("select * from tbl_po_remark where rval like '$val%'");

while($rec=  mysql_fetch_array($data)){
echo"<li value='$rec1' onmouseover=this.style.backgroundColor='rgba(200,200,200,1)' onmouseout=this.style.backgroundColor='white' onclick=fClick('$rec[1]')>$rec[1]</li>";    
}
?>
