<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------------------*/
$msg = "";
$unitid = "";
$unit_name = "";
if(isset($_REQUEST['uid'])){
	$unitid = $_REQUEST['uid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$unitid);
		$row = mysql_fetch_assoc($sql);
		$unit_name = $row["unit_name"];
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT unit_id FROM unit WHERE unit_name='".$_POST['unitName']."'") or die(mysql_error());
	$row_unit = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_unit['unit_id']!=$unitid)
				$msg = "Duplication Error! can&prime;t update into unit master record.";
			elseif($row_unit['unit_id']==$unitid){
				$res = mysql_query("UPDATE unit SET unit_name='".$_POST['unitName']."' WHERE unit_id=".$unitid) or die(mysql_error());
				echo '<script language="javascript">window.location="uom.php?action=new";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE unit SET unit_name='".$_POST['unitName']."' WHERE unit_id=".$unitid) or die(mysql_error());
			echo '<script language="javascript">window.location="uom.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$sqlUnit = mysql_query("SELECT * FROM item WHERE unit_id=".$unitid) or die(mysql_error());
		$rowUnit = mysql_fetch_assoc($sqlUnit);
		$count = mysql_num_rows($sqlUnit);
		if($count>0)
			$msg = "To many records found in Item master.<br>Sorry! it can't delete from unit master record.";
		else {
			$res = mysql_query("DELETE FROM unit WHERE unit_id=".$unitid) or die(mysql_error());
			echo '<script language="javascript">window.location="uom.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into unit master record.";
		else {
			$sql = mysql_query("SELECT Max(unit_id) as maxid FROM unit");
			$row = mysql_fetch_assoc($sql);
			$unitid = $row["maxid"] + 1;
			$sql = "INSERT INTO unit (unit_id,unit_name) VALUES(".$unitid.",'".$_POST['unitName']."')";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">window.location="uom.php?action=new";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Unit of Measurement</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_unit()
{
	var err="";
	if(document.getElementById("unitName").value=="")
		err = "* please input unit name!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function show_unit_list()
{
	document.getElementById("spanUnitList").style.display = '';
	get_matching_units();
}

function hide_unit_list()
{
	document.getElementById("spanUnitList").style.display = 'none';
}
</script>
</head>


<body background="images/hbox21.jpg">
<center>
<table align="center" cellspacing="0" cellpadding="0" height="200px" width="400px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="unit"  method="post" onsubmit="return validate_unit()">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Unit Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Unit Name:</td>
			<td><input name="unitName" id="unitName" maxlength="10" size="40" value="<?php echo $unit_name;?>" onfocus="show_unit_list()" onblur="hide_unit_list()" onkeyup="get_matching_units()"><span id="spanUnitList" style="display:none;"></span></td>
		</tr>

		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="2" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

 		<tr class="Bottom">
			<td colspan="2">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='uom.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
&nbsp;&nbsp;<a onclick="window.close();"><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
<tr>
	<td valign="top" colspan="2">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>List of Unit</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="20%">Unit Name</th>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_unit = mysql_query("SELECT * FROM unit ORDER BY unit_name") or die(mysql_error());
		$tot_row=mysql_num_rows($sql_unit);
		while($row_unit=mysql_fetch_array($sql_unit))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "uom.php?action=delete&uid=".$row_unit['unit_id'];
			$edit_ref = "uom.php?action=edit&uid=".$row_unit['unit_id'];
			
			echo '<td>'.$i.'.</td><td>'.$row_unit['unit_name'].'</td>';
			echo '<td style="text-align:center;"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Bottom">
			<td colspan="3" style="text-align:left">Total <span style="color:red"><?php echo $tot_row;?></span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</center>
</body>
</html>