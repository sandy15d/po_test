<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
if(isset($_SESSION["stores_utype"])){
	$uid = $_SESSION["stores_uid"];
	$uname = $_SESSION["stores_uname"];
	$utype = $_SESSION["stores_utype"];
	$locid = $_SESSION["stores_locid"];
	$lname = $_SESSION["stores_lname"];
	$syear = $_SESSION["stores_syr"];
	$eyear = $_SESSION["stores_eyr"];
}
/*----------------------------------*/
$total_left_p1_num1 = 0;
$total_left_p1_num2 = 0;
$total_left_p1_num3 = 0;
$total_left_p1_num4 = 0;
$total_left_p1_num5 = 0;
$total_left_p1_num6 = 0;
$total_left_p1_num7 = 0;
$total_left_p1_num8 = 0;
$total_left_p1_num9 = 0;
$total_left_p1_num10 = 0;
$total_center_p1_num1 = 0;
$total_right_p1_num1 = 0;
$total_right_p1_num2 = 0;
$total_right_p1_num3 = 0;
$total_right_p1_num4 = 0;
$total_right_p1_num5 = 0;
$total_right_p1_num6 = 0;
$total_right_p1_num7 = 0;
$total_right_p1_num8 = 0;
$total_right_p1_num9 = 0;
$total_right_p1_num10 = 0;
/*----------------------------------*/
$sql_p1 = mysql_query("SELECT Count(*) AS numcount, order_from FROM tbl_indent WHERE ind_status='U' AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."') GROUP BY order_from") or die(mysql_error());
WHILE($row_p1=mysql_fetch_array($sql_p1)){
	if($row_p1['order_from']==16){			//Location = Anandgaon
		$total_left_p1_num1 = $row_p1['numcount'];
		$left_p1_link1 = "penindlist1.php?lid=16";
	} elseif($row_p1['order_from']==15){		//Location = Berla
		$total_left_p1_num2 = $row_p1['numcount'];
		$left_p1_link2 = "penindlist1.php?lid=15";
	} elseif($row_p1['order_from']==4){		//Location = Bhatapara
		$total_left_p1_num3 = $row_p1['numcount'];
		$left_p1_link3 = "penindlist1.php?lid=4";
	} elseif($row_p1['order_from']==8){		//Location = Bhumiya
		$total_left_p1_num4 = $row_p1['numcount'];
		$left_p1_link4 = "penindlist1.php?lid=8";
	} elseif($row_p1['order_from']==11){		//Location = Bohardih
		$total_left_p1_num5 = $row_p1['numcount'];
		$left_p1_link5 = "penindlist1.php?lid=11";
	} elseif($row_p1['order_from']==9){		//Location = Borsi
		$total_left_p1_num6 = $row_p1['numcount'];
		$left_p1_link6 = "penindlist1.php?lid=9";
	} elseif($row_p1['order_from']==17){		//Location = Dhaba
		$total_left_p1_num7 = $row_p1['numcount'];
		$left_p1_link7 = "penindlist1.php?lid=17";
	} elseif($row_p1['order_from']==31){		//Location = Jamali
		$total_left_p1_num8 = $row_p1['numcount'];
		$left_p1_link8 = "penindlist1.php?lid=31";
	} elseif($row_p1['order_from']==34){		//Location = Khamharmuda
		$total_left_p1_num9 = $row_p1['numcount'];
		$left_p1_link9 = "penindlist1.php?lid=34";
	} elseif($row_p1['order_from']==18){		//Location = Khudmuda
		$total_left_p1_num10 = $row_p1['numcount'];
		$left_p1_link10 = "penindlist1.php?lid=18";
	} elseif($row_p1['order_from']==1){		//Location = Gomchi
		$total_center_p1_num1 = $row_p1['numcount'];
		$center_p1_link1 = "penindlist1.php?lid=1";
	} elseif($row_p1['order_from']==35){		//Location = Khuteri
		$total_right_p1_num1 = $row_p1['numcount'];
		$right_p1_link1 = "penindlist1.php?lid=35";
	} elseif($row_p1['order_from']==2){		//Location = Kohadia
		$total_right_p1_num2 = $row_p1['numcount'];
		$right_p1_link2 = "penindlist1.php?lid=2";
	} elseif($row_p1['order_from']==21){		//Location = Lenjwara
		$total_right_p1_num3 = $row_p1['numcount'];
		$right_p1_link3 = "penindlist1.php?lid=21";
	} elseif($row_p1['order_from']==22){		//Location = Nevnara
		$total_right_p1_num4 = $row_p1['numcount'];
		$right_p1_link4 = "penindlist1.php?lid=22";
	} elseif($row_p1['order_from']==32){		//Location = Pacheda
		$total_right_p1_num5 = $row_p1['numcount'];
		$right_p1_link5 = "penindlist1.php?lid=32";
	} elseif($row_p1['order_from']==36){		//Location = Parsada
		$total_right_p1_num6 = $row_p1['numcount'];
		$right_p1_link6 = "penindlist1.php?lid=36";
	} elseif($row_p1['order_from']==23){		//Location = Surholi
		$total_right_p1_num7 = $row_p1['numcount'];
		$right_p1_link7 = "penindlist1.php?lid=23";
	} elseif($row_p1['order_from']==10){		//Location = Tarpongi
		$total_right_p1_num8 = $row_p1['numcount'];
		$right_p1_link8 = "penindlist1.php?lid=10";
	} elseif($row_p1['order_from']==33){		//Location = Tusda
		$total_right_p1_num9 = $row_p1['numcount'];
		$right_p1_link9 = "penindlist1.php?lid=33";
	} elseif($row_p1['order_from']==30){		//Location = Umarda
		$total_right_p1_num10 = $row_p1['numcount'];
		$right_p1_link10 = "penindlist1.php?lid=30";
	}
}
/*----------------------------------*/
$total_left_p3_num1 = 0;
$total_left_p3_num2 = 0;
$total_left_p3_num3 = 0;
$total_left_p3_num4 = 0;
$total_left_p3_num5 = 0;
$total_left_p3_num6 = 0;
$total_left_p3_num7 = 0;
$total_left_p3_num8 = 0;
$total_left_p3_num9 = 0;
$total_left_p3_num10 = 0;
$total_center_p3_num1 = 0;
$total_right_p3_num1 = 0;
$total_right_p3_num2 = 0;
$total_right_p3_num3 = 0;
$total_right_p3_num4 = 0;
$total_right_p3_num5 = 0;
$total_right_p3_num6 = 0;
$total_right_p3_num7 = 0;
$total_right_p3_num8 = 0;
$total_right_p3_num9 = 0;
$total_right_p3_num10 = 0;
/*----------------------------------*/
$sql_p3 = mysql_query("SELECT Count(Distinct tblpo.po_id) AS numcount, delivery_at FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE po_status='S' AND order_received='N' GROUP BY delivery_at") or die(mysql_error());
WHILE($row_p3=mysql_fetch_array($sql_p3))
{
	if($row_p3['delivery_at']==9)			//Location = Anandgaon
		$total_left_p3_num1 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==17)		//Location = Berla
		$total_left_p3_num2 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==6)		//Location = Bhatapara
		$total_left_p3_num3 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==18)		//Location = Bhumiya
		$total_left_p3_num4 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==11)		//Location = Bohardih
		$total_left_p3_num5 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==16)		//Location = Borsi
		$total_left_p3_num6 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==13)		//Location = Dhaba
		$total_left_p3_num7 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==29)		//Location = Jamali
		$total_left_p3_num8 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==49)		//Location = Khamharmuda
		$total_left_p3_num9 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==8)		//Location = Khudmuda
		$total_left_p3_num10 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==2)		//Location = Gomchi
		$total_center_p3_num1 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==14)		//Location = Khuteri
		$total_right_p3_num1 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==3)		//Location = Kohadia
		$total_right_p3_num2 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==31)		//Location = Lenjwara
		$total_right_p3_num3 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==26)		//Location = Nevnara
		$total_right_p3_num4 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==12)		//Location = Pacheda
		$total_right_p3_num5 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==50)		//Location = Parsada
		$total_right_p3_num6 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==44)		//Location = Surholi
		$total_right_p3_num7 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==7)		//Location = Tarpongi
		$total_right_p3_num8 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==52)		//Location = Tusda
		$total_right_p3_num9 = $row_p3['numcount'];
	elseif($row_p3['delivery_at']==25)		//Location = Umarda
		$total_right_p3_num10 = $row_p3['numcount'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Purchase Order</title>
</head>

<body>
<table border="0" width="1100px" height="850px">
<tr>
	<td width="25%" align="center" valign="top">
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:10px; left:18px; width:2px; height:866px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#000000; top:54px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:50px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:35px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num1>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=9','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num1;?></a>
		<?php } else { echo $total_left_p3_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:60px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Anandgaon</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:54px; left:280px; width:2px; height:763px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:54px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:50px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:35px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num1>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link1;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num1;?></a>
		<?php } else { echo $total_left_p1_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:40px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:134px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:130px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:115px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num2>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=17','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num2;?></a>
		<?php } else { echo $total_left_p3_num2;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:140px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location5.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Berla</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:134px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:130px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:115px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num2>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link2;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num2;?></a>
		<?php } else { echo $total_left_p1_num2;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:120px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:224px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:220px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:205px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num3>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=6','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num3;?></a>
		<?php } else { echo $total_left_p3_num3;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:230px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location3.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Bhatapara</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:224px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:220px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:205px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num3>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link3;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num3;?></a>
		<?php } else { echo $total_left_p1_num3;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:210px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:304px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:300px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:285px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num4>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=18','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num4;?></a>
		<?php } else { echo $total_left_p3_num4;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:310px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Bhumiya</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:304px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:300px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:285px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num4>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link4;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num4;?></a>
		<?php } else { echo $total_left_p1_num4;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:290px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:394px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:390px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:375px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num5>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=11','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num5;?></a>
		<?php } else { echo $total_left_p3_num5;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:400px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location5.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Bohardih</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:394px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:390px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:375px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num5>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link5;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num5;?></a>
		<?php } else { echo $total_left_p1_num5;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:380px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:474px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:470px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:455px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num6>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=16','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num6;?></a>
		<?php } else { echo $total_left_p3_num6;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:480px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location3.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Borsi</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:474px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:470px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:455px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num6>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link6;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num6;?></a>
		<?php } else { echo $total_left_p1_num6;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:460px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:554px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:550px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:535px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num7>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=13','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num7;?></a>
		<?php } else { echo $total_left_p3_num7;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:560px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Dhaba</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:554px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:550px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:535px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num7>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link7;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num7;?></a>
		<?php } else { echo $total_left_p1_num7;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:540px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:644px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:640px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:625px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num8>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=29','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num8;?></a>
		<?php } else { echo $total_left_p3_num8;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:650px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Jamali</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:644px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:640px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:625px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num8>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link8;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num8;?></a>
		<?php } else { echo $total_left_p1_num8;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:630px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:730px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:726px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:711px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num9>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=49','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num9;?></a>
		<?php } else { echo $total_left_p3_num9;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:736px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Khamharmuda</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:730px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:726px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:711px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num9>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link9;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num9;?></a>
		<?php } else { echo $total_left_p1_num9;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:716px; left:205px;">P1</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:814px; left:18px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:810px; left:88px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:795px; left:35px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p3_num10>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=8','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p3_num10;?></a>
		<?php } else { echo $total_left_p3_num10;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:820px; left:80px;">P3</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="font-size:12px; border:0px solid; width:100px; height:60px;"/><br>Khudmuda</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:814px; left:200px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:810px; left:270px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:795px; left:220px; width:40px; height:40px; background-color:#FF0000; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_left_p1_num10>0){ ?>
		<a onclick="window.open('<?php echo $left_p1_link10;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_left_p1_num10;?></a>
		<?php } else { echo $total_left_p1_num10;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:800px; left:205px;">P1</div>
		</td>
	</tr>
	</table>
	</td>
	
	<td width="25%" align="center" valign="top">
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr height="160px" align="left" valign="middle">
		<td width="35%" height="160">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:10px; left:18px; width:384px; height:2px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#000000; top:10px; left:400px; width:2px; height:15px;"></div>
		<div style="position:absolute; top:25px; left:396px;"><img src="images/blackdownarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:0px; left:300px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_center_p3_num1>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=2','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_center_p3_num1;?></a>
		<?php } else { echo $total_center_p3_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:14px; left:345px;">P3</div>
		</td>
		<td width="30%" colspan="3"><div style="border:1px solid; width:124px; height:124px; background-image:url(images/village.jpg); font-weight:bold; background-position:center; text-align:center">GOMCHI</div></td>
		<td width="35%">&nbsp;</td>
	</tr>
	<tr height="160px" align="left" valign="middle">
		<td width="35%" height="160">&nbsp;</td>
		<td width="30%" colspan="3">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:160px; left:420px; width:2px; height:65px;"></div>
		<div style="border:0px solid; position:absolute; top:225px; left:395px; width:51px; height:51px; background-color:#FF0000; z-index:1; background-image:url(images/circle1.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:14px;">
		<?php if($total_center_p1_num1>0){ ?>
		<a onclick="window.open('<?php echo $center_p1_link1;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_center_p1_num1;?></a>
		<?php } else { echo $total_center_p1_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:280px; left:425px;">P1</div>
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:275px; left:420px; width:2px; height:70px;"></div>
		<div style="position:absolute; top:345px; left:416px;"><img src="images/reddownarrow.png" style="width:10px; height:10px;"/></div>
		</td>
		<td width="35%">&nbsp;</td>
	</tr>
	<tr height="160px" align="left" valign="middle">
		<td width="35%" height="160">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:414px; left:280px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:410px; left:350px;"><img src="images/redrightarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; background-color:#0000FF; top:434px; left:320px; width:2px; height:110px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#0000FF; top:434px; left:320px; width:40px; height:2px;"></div>
		<div style="position:absolute; top:430px; left:350px;"><img src="images/blackrightarrow.png" style="width:10px; height:10px;"/></div>
		</td>
		<td width="30%" colspan="3"><div style="border:1px solid; width:124px; height:124px; background-image:url(images/office.jpg); font-weight:bold; color:#FFFFFF; text-align:center">OFFICE</div></td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:414px; left:492px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:410px; left:485px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; background-color:#00FF00; top:434px; left:485px; width:40px; height:2px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#00FF00; top:434px; left:525px; width:2px; height:110px;"></div>
		</td>
	</tr>
	<tr height="160px" align="left" valign="middle">
		<td width="35%" height="160">
		<?php
		$sql_p4 = mysql_query("SELECT DISTINCT po_id FROM (SELECT table1.* , IFNULL(tblbill_item.bill_id, 0) AS billid FROM (SELECT tblpo.po_id, item_id FROM tblpo INNER JOIN tblpo_item ON tblpo.po_id = tblpo_item.po_id WHERE order_received = 'Y' ORDER BY po_date, tblpo.po_id) AS table1 LEFT OUTER JOIN tblbill_item ON (table1.po_id = tblbill_item.po_id AND table1.item_id = tblbill_item.item_id)) AS table3 WHERE billid =0") or die(mysql_error());
		$total_p4_num = mysql_num_rows($sql_p4);
		?>
		<div style="border:0px solid; position:absolute; top:545px; left:295px; width:51px; height:51px; background-color:#FF0000; z-index:1; background-image:url(images/circle1.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:14px;">
		<?php if($total_p4_num>0){ ?>
		<a onclick="window.open('penpblist.php','pendingPurchaseBills','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_p4_num;?></a>
		<?php } else { echo $total_p4_num;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:600px; left:325px;">P4</div>
		</td>
		<td width="30%" colspan="3">
		<?php 
		$sql_p2 = mysql_query("SELECT * FROM tblpo WHERE po_status='U' AND (po_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')") or die(mysql_error());
		$total_p2_num = mysql_num_rows($sql_p2);
		?>
		<div style="border:0px solid; position:absolute; background-color:#000000; top:480px; left:420px; width:2px; height:65px;"></div>
		<div style="border:0px solid; position:absolute; top:545px; left:395px; width:51px; height:51px; background-color:#FF0000; z-index:1; background-image:url(images/circle1.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:14px;">
		<?php if($total_p2_num>0){ ?>
		<a onclick="window.open('penpolist1.php','pendingPO','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_p2_num;?></a>
		<?php } else { echo $total_p2_num;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:600px; left:425px;">P2</div>
		<div style="border:0px solid; position:absolute; background-color:#000000; top:595px; left:420px; width:2px; height:70px;"></div>
		<div style="position:absolute; top:665px; left:416px;"><img src="images/blackdownarrow.png" style="width:10px; height:10px;"/></div>
		</td>
		<td width="35%">
		<?php 
		$sql_p5 = mysql_query("SELECT * FROM tblbill WHERE bill_return = 0 AND bill_paid = 'N'") or die(mysql_error());
		$total_p5_num = mysql_num_rows($sql_p5);
		?>
		<div style="border:0px solid; position:absolute; top:545px; left:497px; width:51px; height:51px; background-color:#FF0000; z-index:1; background-image:url(images/circle1.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:14px;">
		<?php if($total_p5_num>0){ ?>
		<a onclick="window.open('penpaylist.php','pendingPayment','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_p5_num;?></a>
		<?php } else { echo $total_p5_num;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:600px; left:530px;">P5</div>
		</td>
	</tr>
	<tr height="160px" align="left" valign="middle">
		<td width="35%" height="160">
		<div style="border:0px solid; position:absolute; background-color:#0000FF; top:595px; left:320px; width:2px; height:137px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#0000FF; top:730px; left:320px; width:40px; height:2px;"></div>
		</td>
		<td width="30%" colspan="3">
		<div style="border:1px solid; width:124px; height:124px; background-image:url(images/supplier.jpg); color:#FFFFFF; font-weight:bold; text-align:center">SUPPLIER</div>
		</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#00FF00; top:730px; left:485px; width:40px; height:2px;"></div>
		<div style="position:absolute; top:726px; left:485px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; background-color:#00FF00; top:595px; left:525px; width:2px; height:137px;"></div>
		</td>
	</tr>
	<tr height="50px" align="left" valign="middle">
		<td width="35%" height="50">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:875px; left:18px; width:384px; height:2px;"></div>
		</td>
		<td width="10%" align="right">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:800px; left:400px; width:2px; height:75px;"></div>
		</td>
		<td width="10%">&nbsp;</td>
		<td width="10%" align="left">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:800px; left:450px; width:2px; height:75px;"></div>
		</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:875px; left:450px; width:376px; height:2px;"></div>
		</td>
	</tr>
	</table>
	</td>
	
	<td width="25%" align="center" valign="top">
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:54px; left:562px; width:2px; height:763px;"></div>
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:54px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:50px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:35px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num1>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link1;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num1;?></a>
		<?php } else { echo $total_right_p1_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:40px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Khuteri</td>
		<td width="35%">
		<div style="border:0px solid; background-color:#000000; position:absolute; top:54px; left:825px; width:2px; height:822px;"></div>
		<div style="border:0px solid; background-color:#000000; position:absolute; top:54px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:50px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:35px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num1>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=14','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num1;?></a>
		<?php } else { echo $total_right_p3_num1;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:60px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:134px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:130px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:115px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num2>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link2;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num2;?></a>
		<?php } else { echo $total_right_p1_num2;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:120px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location5.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Kohadia</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:134px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:130px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:115px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num2>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=3','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num2;?></a>
		<?php } else { echo $total_right_p3_num2;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:140px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:224px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:220px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:205px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num3>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link3;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num3;?></a>
		<?php } else { echo $total_right_p1_num3;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:210px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location3.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Lenjwara</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:224px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:220px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:205px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num3>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=31','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num3;?></a>
		<?php } else { echo $total_right_p3_num3;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:230px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:304px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:300px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:285px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num4>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link4;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num4;?></a>
		<?php } else { echo $total_right_p1_num4;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:290px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Nevnara</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:304px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:300px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:285px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num4>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=26','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num4;?></a>
		<?php } else { echo $total_right_p3_num4;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:310px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:394px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:390px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:375px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num5>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link5;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num5;?></a>
		<?php } else { echo $total_right_p1_num5;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:380px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location5.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Pacheda</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:394px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:390px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:375px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num5>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=12','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num5;?></a>
		<?php } else { echo $total_right_p3_num5;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:400px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:474px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:470px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:455px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num6>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link6;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num6;?></a>
		<?php } else { echo $total_right_p1_num6;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:460px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location3.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Parsada</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:474px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:470px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:455px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num6>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=50','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num6;?></a>
		<?php } else { echo $total_right_p3_num6;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:480px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:564px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:560px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:545px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num7>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link7;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num7;?></a>
		<?php } else { echo $total_right_p1_num7;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:550px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Surholi</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:564px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:560px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:545px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num7>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=44','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num7;?></a>
		<?php } else { echo $total_right_p3_num7;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:570px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:644px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:640px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:625px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num8>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link8;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num8;?></a>
		<?php } else { echo $total_right_p1_num8;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:630px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location5.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Tarpongi</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:644px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:640px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:625px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num8>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=7','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num8;?></a>
		<?php } else { echo $total_right_p3_num8;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:650px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:730px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:726px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:711px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num9>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link9;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num9;?></a>
		<?php } else { echo $total_right_p1_num9;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:716px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location3.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Tusda</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:730px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:726px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:711px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num9>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=52','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num9;?></a>
		<?php } else { echo $total_right_p3_num9;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:736px; left:755px;">P3</div>
		</td>
	</tr>
	<tr height="85px" align="left" valign="middle">
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#FF0000; top:814px; left:575px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:810px; left:565px;"><img src="images/redleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:795px; left:585px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p1_num10>0){ ?>
		<a onclick="window.open('<?php echo $right_p1_link10;?>','pendingIndent','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no, location=no,directories=no,status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p1_num10;?></a>
		<?php } else { echo $total_right_p1_num10;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:800px; left:630px;">P1</div>
		</td>
		<td width="30%"><img src="images/location4.jpg" style="border:0px solid; width:100px; height:60px;"/><br>Umarda</td>
		<td width="35%">
		<div style="border:0px solid; position:absolute; background-color:#000000; top:814px; left:755px; width:70px; height:2px;"></div>
		<div style="position:absolute; top:810px; left:747px;"><img src="images/blackleftarrow.png" style="width:10px; height:10px;"/></div>
		<div style="border:0px solid; position:absolute; top:795px; left:770px; width:40px; height:40px; background-color:#FFFFFF; z-index:1; background-image:url(images/circle2.jpg)"><div style="font-size:12px; font-weight:bold; text-align:center; position:relative; top:10px;">
		<?php if($total_right_p3_num10>0){ ?>
		<a onclick="window.open('penmrlist1.php?lid=25','pendingDelivery','width=1075,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes, menubar=no,copyhistory=no')" style="display:inline; cursor:hand; text-decoration:underline; color:#990000;"><?php echo $total_right_p3_num10;?></a>
		<?php } else { echo $total_right_p3_num10;} ?>
		</div></div>
		<div style="font-size:9px; position:absolute; top:820px; left:755px;">P3</div>
		</td>
	</tr>
	</table>
	</td>
	
	<td width="25%" align="left" valign="top">
		<a href="menu.php"><img src="images/IconMenu.gif" border="0" title="Menu" style="vertical-align:bottom"></a>
		&nbsp;&nbsp;<a href="logout.php"><img src="images/logoutuser.gif" border="0" title="Logout" style="vertical-align:bottom"></a>
		<div style="font-size:14px; position:absolute; top:150px; left:850px; width:200px; text-align:left">P1 :- Pending Indent</div>
		<div style="font-size:14px; position:absolute; top:200px; left:850px; width:200px; text-align:left">P2 :- Pending Purchase Order</div>
		<div style="font-size:14px; position:absolute; top:250px; left:850px; width:200px; text-align:left">P3 :- Pending Delivery</div>
		<div style="font-size:14px; position:absolute; top:300px; left:850px; width:200px; text-align:left">P4 :- Pending Purchase Bills</div>
		<div style="font-size:14px; position:absolute; top:350px; left:850px; width:200px; text-align:left">P5 :- Pending Payment</div>
	</td>
</tr>
</table>
</body>
</html>