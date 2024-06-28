<?php
session_start();
include"config/config.php";
$dataMail=mysql_query("select email_id from users where mr2=1 and user_type='U' and user_status='A'");
$recMail=  mysql_fetch_array($dataMail);
if($_SESSION['stores_uid']!=$recMail['email_id']){
        $to=$recMail["email_id"];
        $sub="Mail regarding Material Reciept";
        $mailMsg="Please complete purchase order";
        $header="From:admin@vnrseeds.com";
        if($to){
        if(mail($to,$sub,$mailMsg,$header))
                echo"Mail sent to $to for further processing";
        else echo"Mail not sent";
        }
        else {
        echo"No user specified";    
        }
}
else echo"You can proceed for further processing";  
?>

