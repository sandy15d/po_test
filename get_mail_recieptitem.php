<?php
session_start();
include"config/config.php";
$dataMail=mysql_query("select email_id,user_id from users where rr2=1 and user_type='U' and user_status='A'");
$recMail=  mysql_fetch_array($dataMail);

if($_SESSION['stores_uname']!=$recMail['user_id']){
        $to=$recMail["email_id"];
        $sub="Mail regarding  Reciept Return";
        $mailMsg="Please complete Reciept Return";
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
 else {
echo"You can proceed for further processing";    
}
?>

