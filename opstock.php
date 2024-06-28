<?php 
include("menu.php");
/*--------------------------------*/
if(isset($_POST['submit'])){
	if($_POST['submit']=="Submit"){
		echo '<script language="javascript">window.location="opstock1.php?xn=N&lid='.$_POST['location'].'";</script>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Opening Stock</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function validate_opstock()
{
	if(document.getElementById("location").value==0){
		alert("Error: \n* please select a location of the stock item, it is mandatory field!\n");
		return false;
	} else {
		return true;
	}
}
</script>
</head>


<body>
<table align="center" cellspacing="0" cellpadding="0" height="260px" width="600px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="opstock"  method="post" onsubmit="return validate_opstock()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Opening Stock</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th">Stock Location:<span style="color:#FF0000">*</span></td>
			<td><select name="location" id="location" style="width:250px"><option value="0">-- Select --</option>
			<?php 
			$sql_location=mysql_query("SELECT * FROM location ORDER BY location_name");
			while($row_location=mysql_fetch_array($sql_location)){
				echo '<option value="'.$row_location['location_id'].'">'.$row_location['location_name'].'</option>';
			}
			$x = "window.open('location.php?action=new','location','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no,status=yes, menubar=no,copyhistory=no')";?>
			</select>&nbsp;&nbsp;<a onclick="<?php echo $x;?>"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
			
			<td class="th" nowrap>As On Date:</td>
			<td><input name="asonDate" id="asonDate" size="10" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Item Name:</td>
			<td><input name="itemName" id="itemName" size="40" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Unit:</td>
			<td><input name="unitName" id="unitName" size="15" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Op.Quantity:</td>
			<td><input name="opQnty" id="opQnty" size="15" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Rate:</td>
			<td><input name="opRate" id="opRate" size="15" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Op.Amount:</td>
			<td><input name="opAmount" id="opAmount" size="15" readonly="true" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

 		<tr class="Bottom">
			<td colspan="4">
			<input type="image" name="submit" src="images/submit.gif" width="72" height="22" alt="submit"><input type="hidden" name="submit" value="Submit"/>&nbsp;&nbsp;<a href="javascript:document.opstock.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0"/></a>
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