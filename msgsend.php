<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*---------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
</head>

<body>
<br><h1>This message is sending now ...</h1>
<?php
$oid = $_REQUEST['oid'];
$ino=$_REQUEST['ino'];
$voucherid = ($ino>999 ? $ino : ($ino>99 && $ino<1000 ? "0".$ino : ($ino>9 && $ino<100 ? "00".$ino : "000".$ino)));
$sql = mysql_query("SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=".$oid);
$row = mysql_fetch_assoc($sql);
if($row['ind_prefix']!=null){$voucherid = $row['ind_prefix']."/".$voucherid;}
$dateIndent = date("d-m-Y",strtotime($row['indent_date']));
$i = 0;
$stext = "Indent No.: ".$voucherid." date: ".$dateIndent.'</br>';
$stext .= "From ".$row['location_name'].'</br>';
$sql = mysql_query("SELECT tbl_indent_item.*,item_name,unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON item.unit_id = unit.unit_id WHERE indent_id=".$oid." ORDER BY seq_no");
while($row = mysql_fetch_array($sql))
{
	$i++;
	$stext .= $i.')&nbsp;&nbsp;'.$row['item_name'].'&nbsp;&nbsp;'.$row['qnty'].' '.$row['unit_name'].'</br>';
}
$stext .= "kindly go through the website for approval of these items.".'</br>';
$stext .= "Thank you.".'</br>';
$stext .= "It is a system generated message".'</br>';
echo $stext;

$dataMail=mysql_query("select email_id from users where  oi2=1 and user_type='U' and user_status='A'");
$recMail=  mysql_fetch_array($dataMail);
$to = $recMail[0];
$subject = "My subject";
$txt = $stext;
$headers = "From: admin@vnrseeds.com" . "\r\n" .
"CC: somebodyelse@example.com";

if(mail($to,$subject,$txt,$headers))
        echo"Mail sent to $to";
else echo "mail not sent";
?>
<p><a href="newindent.php">click here to return back</a></p>
<?php 
$stext1 = str_replace("</br>", "%0A%0D", $stext);
$stext1 = str_replace("&nbsp;", "%20", $stext1);
//echo '<script language="javascript">function sendsms(value1){
//	window.open("http://69.50.198.112/lsend.php?usr=18480&pwd=vnr123&ph=9329570007,9981990330&sndr=softcornerind.com&text="+value1,"sendsms", "width=500,height=300,resizable=no,scrollbars=no,toolbar=no,location=no,directories=no,status=no, menubar=no,copyhistory=no");}
//	sendsms("'.$stext1.'")</script>';
//header('Location:http://69.50.198.112/send.php?usr=18480&pwd=vnr123&ph=9302730007,9922950454,9977796193&sndr=softcornerind.com&text='.$stext);
//	window.open("http://69.50.198.112/send.php?usr=18480&pwd=vnr123&ph=9302730007,9922950454,9977796193&sndr=softcornerind.com&text="+value1,"sendsms", "width=400,height=400,resizable=no,scrollbars=no,toolbar=no,location=no,directories=no,status=no, menubar=no,copyhistory=no");}
?>
</body>
</html>