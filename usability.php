<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------------------*/
$msg = "";
$useid = "";
$usability_name = "";
if(isset($_REQUEST['uid'])){
	$useid = $_REQUEST['uid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM usability WHERE usability_id=".$useid);
		$row = mysql_fetch_assoc($sql);
		$usability_name = $row["usability_name"];
	}
}
/*--------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT usability_id FROM usability WHERE usability_name='".$_POST['usabilityName']."'") or die(mysql_error());
	$row_usability = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*--------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_usability['usability_id']!=$useid)
				$msg = "Duplication Error! can&prime;t update into usability master record.";
			elseif($row_usability['usability_id']==$useid)
				$res = mysql_query("UPDATE usability SET usability_name='".$_POST['usabilityName']."' WHERE usability_id=".$useid) or die(mysql_error());
				echo '<script language="javascript">window.location="usability.php?action=new";</script>';
		} elseif($count==0){
			$res = mysql_query("UPDATE usability SET usability_name='".$_POST['usabilityName']."' WHERE usability_id=".$useid) or die(mysql_error());
			echo '<script language="javascript">window.location="usability.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$sqlUsb = mysql_query("SELECT * FROM item WHERE unit_id=".$useid) or die(mysql_error());
		$rowUsb = mysql_fetch_assoc($sqlUsb);
		$count = mysql_num_rows($sqlUsb);
		if($count>0)
			$msg = "To many records found in Item master.<br>Sorry! it can't delete from usability master record.";
		else {
			$res = mysql_query("DELETE FROM usability WHERE usability_id=".$useid) or die(mysql_error());
			echo '<script language="javascript">window.location="usability.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into usability master record.";
		else {
			$sql = mysql_query("SELECT Max(usability_id) as maxid FROM usability");
			$row = mysql_fetch_assoc($sql);
			$useid = $row["maxid"] + 1;
			$sql = "INSERT INTO usability (usability_id,usability_name) VALUES(".$useid.",'".$_POST['usabilityName']."')";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">window.location="usability.php?action=new";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Usability Master</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_usability()
{
	var err="";
	if(document.getElementById("usabilityName").value=="")
		err = "* please input usability name!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function paging_ujblit()
{
	if(document.getElementById("xson").value=="new")
		window.location="usability.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
	else
		window.location="usability.php?action="+document.getElementById("xson").value+"&uid="+document.getElementById("ujbid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_ujblit()
{
	document.getElementById("page").value = 1;
	paging_ujblit();
}

function previouspage_ujblit()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_ujblit();
}

function nextpage_ujblit()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_ujblit();
}

function lastpage_ujblit()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_ujblit();
}

function show_use_list()
{
	document.getElementById("spanUseList").style.display = '';
	get_matching_usability();
}

function hide_use_list()
{
	document.getElementById("spanUseList").style.display = 'none';
}
</script>
</head>


<body background="images/hbox21.jpg">
<table align="center" cellspacing="0" cellpadding="0" height="200px" width="400px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="usability"  method="post" onsubmit="return validate_usability()">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Usability Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Usability Name:</td>
			<td><input name="usabilityName" id="usabilityName" maxlength="50" size="40" value="<?php echo $usability_name;?>" onfocus="show_use_list()" onblur="hide_use_list()" onkeyup="get_matching_usability()"><span id="spanUseList" style="display:none;"></span></td>
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
&nbsp;&nbsp;<a href="javascript:window.location='usability.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>List of Usability</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="30%">Usability Name</th>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql_usability = mysql_query("SELECT * FROM usability ORDER BY usability_name LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_usability=mysql_fetch_array($sql_usability))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "usability.php?action=delete&uid=".$row_usability['usability_id'];
			$edit_ref = "usability.php?action=edit&uid=".$row_usability['usability_id'];
			
			echo '<td>'.$i.'.</td><td>'.$row_usability['usability_name'].'</td>';
			echo '<td style="text-align:center;"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Bottom">
			<td colspan="3" style="text-align:center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM usability") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_ujblit()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="ujbid" id="ujbid" value="'.$useid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_ujblit()" style="vertical-align:middle">';
				for($i=1;$i<=$total_page;$i++)
				{
					if(isset($_REQUEST["pg"]) && $_REQUEST["pg"]==$i)
						echo '<option selected value="'.$i.'">'.$i.'</option>';
					else
						echo '<option value="'.$i.'">'.$i.'</option>';
				}
				echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}else {
				echo '<input type="hidden" name="page" id="page" value="1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			echo '<input type="hidden" name="totalPage" id="totalPage" value="'.$total_page.'" />';
			if($total_page>1 && $pg>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_ujblit()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_ujblit()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_ujblit()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_ujblit()" />';
			?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>