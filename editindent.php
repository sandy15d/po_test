<?php 
include("menu.php");
/*-------------------------------*/
$sql_user = mysql_query("SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=".$_SESSION["stores_uid"]) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-------------------------------*/
$oid = $_REQUEST['oid'];
$sql = mysql_query("SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=".$oid);
$row = mysql_fetch_assoc($sql);
$indent_number = ($row['indent_no']>999 ? $row['indent_no'] : ($row['indent_no']>99 && $row['indent_no']<1000 ? "0".$row['indent_no'] : ($row['indent_no']>9 && $row['indent_no']<100 ? "00".$row['indent_no'] : "000".$row['indent_no'])));
if($row['ind_prefix']!=null){$indent_number = $row['ind_prefix']."/".$indent_number;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_indent()
{
	var err="";
	if(document.getElementById("indentDate").value!=""){
		if(!checkdate(document.indentorder.indentDate)){
			return false;
		} else {
			var no_of_days1 = getDaysbetween2Dates(document.indentorder.indentDate,document.indentorder.endYear);
			if(no_of_days1 < 0){
				err += "* Order Indent date wrongly selected. Please correct and submit again.\n";
			} else {
				var no_of_days2 = getDaysbetween2Dates(document.indentorder.startYear,document.indentorder.indentDate);
				if(no_of_days2 < 0){
					err += "* Order Indent date wrongly selected. Please correct and submit again.\n";
				} else {
					var no_of_days3 = getDaysbetween2Dates(document.indentorder.maxDate,document.indentorder.indentDate);
					if(no_of_days3 < 0){
						err += "* Order Indent date wrongly selected. Please correct and submit again.\n"+
						"Last indent date was "+document.getElementById("maxDate").value+", so lower date is not acceptable.\n";
					}
				}
			}
		}
	} else
		err += "* please input/select indent date!\n";
	if(document.getElementById("indentFrom").value==0)
		err += "* please select location, where order being sent from!\n";
	if(document.getElementById("orderBy").value==0)
		err += "* please select name of staff, who has given the order!\n";
	if(err==""){
		document.getElementById("submit").style.display = 'none';
		get_indent_submit(document.getElementById("xn").value,document.getElementById("indentDate").value,document.getElementById("supplyDate").value,document.getElementById("indentFrom").value,document.getElementById("orderBy").value,document.getElementById("indid").value);
		return true;
	} else {
		alert("Error: \n"+err);
		return false;
	}
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="300px" width="775px" border="0">
<tr>
	<td valign="top" colspan="3">
	<form name="indentorder"  method="post">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Order Indent - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Indent No.:</td>
			<td><input name="indentNo" id="indentNo" maxlength="15" size="20" readonly="true" value="<?php echo $indent_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Indent Date:</td>
			<td><input name="indentDate" id="indentDate" maxlength="10" size="10" readonly="true" value="<?php echo date("d-m-Y",strtotime($row["indent_date"])); ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Indent From:</td>
			<td><input name="location" id="location" maxlength="50" size="45" readonly="true" value="<?php echo $row["location_name"]; ?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<input type="hidden" name="indentFrom" id="indentFrom" value="<?php echo $row["order_from"]; ?>" /></td>
			
			<td class="th" nowrap>Estimated Supply Date:</td>
			<td><input name="supplyDate" id="supplyDate" maxlength="10" size="10" value="<?php echo date("d-m-Y",strtotime($row["supply_date"])); ?>"><script language="JavaScript">new tcal ({'formname': 'indentorder', 'controlname': 'supplyDate'});</script></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Order By:<span style="color:#FF0000">*</span></td>
			<td><div id="orderbydiv"><select name="orderBy" id="orderBy" onchange="get_max_date(document.getElementById('indentFrom').value, document.getElementById('startDate').value, document.getElementById('endDate').value, document.getElementById('callingPage').value)" style="width:300px">
			<option value="0">-- Select --</option>
			<?php if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S" || $_SESSION['stores_utype']=="U")
				$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$row['order_from']." ORDER BY staff_name");
			elseif($_SESSION['stores_utype']=="U")
				$sql_staff=mysql_query("SELECT * FROM staff WHERE location_id=".$_SESSION["stores_locid"]." ORDER BY staff_name");
			
			while($row_staff=mysql_fetch_array($sql_staff)){
				if($row_staff["staff_id"]==$row["order_by"])
					echo '<option selected value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
				else
					echo '<option value="'.$row_staff["staff_id"].'">'.$row_staff["staff_name"].'</option>';
			}?>
			</select></div></td>
			
			<td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y",strtotime($_SESSION["stores_syr"]));?>"/><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y",strtotime($_SESSION["stores_eyr"]));?>"/><input type="hidden" name="maxDate" id="maxDate" value="<?php echo date("d-m-Y",strtotime($_SESSION["stores_syr"]));?>"/><input type="hidden" name="startDate" id="startDate" value="<?php echo strtotime($_SESSION["stores_syr"]);?>"/><input type="hidden" name="endDate" id="endDate" value="<?php echo strtotime($_SESSION["stores_eyr"]);?>"/><input type="hidden" name="callingPage" id="callingPage" value="order_indent"/><input type="hidden" name="xn" id="xn" value="edit" /><input type="hidden" name="indid" id="indid" value="<?php echo $oid; ?>" /></td>
			<td>&nbsp;</td>
		</tr>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if($row_user['oi2']==1){?>
				<img id="submit" src="images/update.gif" width="82" height="22" style="cursor:hand;" onclick="return validate_indent()"/>
		<?php }?>
&nbsp;&nbsp;<img src="images/next.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='newindentitem.php?oid=<?php echo $oid;?>'" />&nbsp;&nbsp;<img src="images/reset.gif" width="72" height="22" style="cursor:hand;" onclick="reset()" />&nbsp;&nbsp;<img src="images/back.gif" width="72" height="22" style="cursor:hand;" onclick="window.location='indlist.php'"/>
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
</center>
</body>
</html>
