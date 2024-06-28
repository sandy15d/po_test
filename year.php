<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['submit'])){
	$sd = date("d-m-Y",strtotime("01-04-".$_POST['syear']));
	$startDate = substr($sd,6,4)."-".substr($sd,3,2)."-".substr($sd,0,2);
	$speriod = substr(date("Y",strtotime($sd)),0,4)."-".substr(date("Y",strtotime($sd))+1,0,4);
	$sql = mysql_db_query(DATABASE2,"SELECT Max(year_id) as maxid FROM year");
	$row = mysql_fetch_assoc($sql);
	$yid = $row["maxid"] + 1;
	$sql = "INSERT INTO year VALUES(".$yid.",'".$startDate."','".$speriod."')";
	$res = mysql_db_query(DATABASE2,$sql) or die(mysql_error());
	header('Location:login.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_year()
{
	var err="";
	var dateobj = new Date();
	if(document.getElementById("syear").value=="")
		err = "* please input starting year!\n";
	else {
		if(parseInt(document.getElementById("syear").value)>dateobj.getFullYear() || parseInt(document.getElementById("syear").value)<dateobj.getFullYear()-2){
			err = "* please input correct starting year!\n";
		}
	}
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		if(dateobj.getMonth()+1 >= 1 && dateobj.getMonth()+1 <= 3)
			document.getElementById("syear").value = dateobj.getFullYear()-1;
		else
			document.getElementById("syear").value = dateobj.getFullYear();
		return false;
	}
}
</script>
</head>


<body onload="document.getElementById('syear').focus()">
<table align="center" cellspacing="0" cellpadding="0" height="200px" width="100%" border="0">
<tr>
	<td valign="top">
	<form name="year"  method="post" onsubmit="return validate_year()">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Financial Year</strong></td>
			<td class="HeaderRight"><img src="spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Year :<span style="color:#FF0000">*</span></td>
			<td><input name="syear" id="syear" maxlength="4" size="15" value="<?php echo ((date("m")>=1 && date("m")<=3) ? date("Y")-1 : date("Y")); ?>" ></td>
		</tr>
		<tr class="Controls">
			<td class="th" nowrap>starting date :<span style="color:#FF0000">*</span></td>
			<td><input name="sdate" id="sdate" maxlength="10" size="15" readonly="true" value="<?php echo date("d-m-Y",strtotime("01-04-".substr(((date("m")>=1 && date("m")<=3) ? date("Y")-1 : date("Y")),0,4)));?>"  style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
 		<tr class="Bottom">
			<td align="left" colspan="2">
				<input type="image" name="submit" src="images/add.gif" alt="new"><input type="hidden" name="submit" value="new" />&nbsp;&nbsp;<a href="javascript:document.year.reset()"><img src="images/ButtonCancel.gif" style="display:inline; cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
</table>
</body>
</html>