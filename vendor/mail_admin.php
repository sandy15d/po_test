<?php
  require 'autoload.php';
  use PHPMailer\PHPMailer\PHPMailer;
  $mail = new PHPMailer(); // create a new object
  $mail->IsSMTP(); // enable SMTP
  //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
  $mail->SMTPAuth = true; // authentication enabled
  $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
  $mail->Host = "smtp.gmail.com";
  $mail->Port = 465; // or 587
  $mail->IsHTML(true);
  $mail->Username = "webadmin@vnrseeds.com";
  $mail->Password = "NewPwd32#Admin";  //"NewPwd32#Admin"
  $mail->SetFrom("webadmin@vnrseeds.com", 'ADMIN ESS');
 
  $mail->Subject = $subject;
  $mail->Body = $body;
  $mail->AddAddress($email_to);
  $ok=$mail->Send();
?>
