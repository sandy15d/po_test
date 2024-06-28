<?php  include"menu.php";
session_start();
require_once('config/config.php');
//include("function.php");
//if(check_user()==false){header("Location: login.php");}
/*------------------------------*/
$c1 = 0;
if(isset($_REQUEST["c1"])){$c1 = $_REQUEST["c1"];}
$c2 = 0;
if(isset($_REQUEST["c2"])){$c2 = $_REQUEST["c2"];}
/*------------------------------*/
$msg = "";
$cid = "";
$city_name = "";
$state_id = 0;
if(isset($_REQUEST['cid'])){
	$cid = $_REQUEST['cid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT * FROM city WHERE city_id=".$cid);
		$row = mysql_fetch_assoc($sql);
		$city_name = $row["city_name"];
		$state_id = $row["state_id"];
	}
}
/*------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT city_id FROM city WHERE city_name='".$_POST['cityName']."'") or die(mysql_error());
	$row_city = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_city['city_id']!=$cid)
				$msg = "Duplication Error! can&prime;t update into city master record.";
			elseif($row_city['city_id']==$cid){
				$res = mysql_query("UPDATE city SET city_name='".$_POST['cityName']."',state_id=".$_POST['state']." WHERE city_id=".$cid) or die(mysql_error());
				echo '<script language="javascript">window.location="cit2y.php?action=new";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE city SET city_name='".$_POST['cityName']."',state_id=".$_POST['state']." WHERE city_id=".$cid) or die(mysql_error());
			echo '<script language="javascript">window.location="cit2y.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$sqlParty = mysql_query("SELECT * FROM party WHERE city_id=".$cid) or die(mysql_error());
		$rowParty = mysql_fetch_assoc($sqlParty);
		$count = mysql_num_rows($sqlParty);
		if($count>0)
			$msg = "To many records found in Party master.<br>Sorry! it can't delete from city master record.";
		else {
			$res = mysql_query("DELETE FROM city WHERE city_id=".$cid) or die(mysql_error());
			echo '<script language="javascript">window.location="cit2y.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into city master record.";
		else {
			$sql = mysql_query("SELECT Max(city_id) as maxid FROM city");
			$row = mysql_fetch_assoc($sql);
			$cid = $row["maxid"] + 1;
			$sql = "INSERT INTO city (city_id,city_name,state_id) VALUES(".$cid.",'".$_POST['cityName']."',".$_POST['state'].")";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">window.location="cit2y.php?action=new";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>City Master</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_city()
{
	var err="";
	if(document.getElementById("cityName").value=="")
		err = "* please input city name!\n";
	if(document.getElementById("state").value==0)
		err += "* please select the state of the city!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function city_onclick(col1)
{
	if(col1==0 || col1==1) col1=2; else col1=1;
	var strg = "&c1="+col1+"&c2=0";
	paging_city(strg);
}

function state_onclick(col2)
{
	if(col2==0 || col2==1) col2=2; else col2=1;
	var strg = "&c1=0&c2="+col2;
	paging_city(strg);
}

function paging_city(value1)
{
	if(document.getElementById("xson").value=="new")
		window.location="cit2y.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+value1;
	else
		window.location="cit2y.php?action="+document.getElementById("xson").value+"&cid="+document.getElementById("ctyid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+value1;
}

function firstpage_city()
{
	document.getElementById("page").value = 1;
	paging_city("");
}

function previouspage_city()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_city("");
}

function nextpage_city()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_city("");
}

function lastpage_city()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_city("");
}

function show_city_list()
{
	document.getElementById("spanCityList").style.display = '';
	get_matching_city();
}

function hide_city_list()
{
	document.getElementById("spanCityList").style.display = 'none';
}
</script>
</head>


<body background="images/hbox21.jpg">
<center>
<table align="center" cellspacing="0" cellpadding="0" height="225px" width="400px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="city"  method="post" onsubmit="return validate_city()">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>City Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>City Name:</td>
			<td><input name="cityName" id="cityName" maxlength="50" size="45" value="<?php echo $city_name;?>" onfocus="show_city_list()" onblur="hide_city_list()" onkeyup="get_matching_city()"><span id="spanCityList" style="display:none;"></span></td>
		</tr>

		<tr class="Controls">
			<td class="th">State:</td>
			<td><select name="state" id="state" style="width:280px"><option value="0">-- Select --</option>
			<?php 
			$sql_state=mysql_query("SELECT * FROM state ORDER BY state_name");
			while($row_state=mysql_fetch_array($sql_state)){
				if($row_state["state_id"]==$state_id)
					echo '<option selected value="'.$row_state['state_id'].'">'.$row_state['state_name'].'</option>';
				else
					echo '<option value="'.$row_state['state_id'].'">'.$row_state['state_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('state.php?action=new','state','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
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
&nbsp;&nbsp;<a href="javascript:window.location='cit2y.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>List of City</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<?php 
			if($c1==0)
				echo '<th width="45%">City Name&nbsp;&nbsp;<img src="&nbsp;" width="12" height="7" onclick="city_onclick(0)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="cityColumn" value="0" /></th>';
			elseif($c1==1)
				echo '<th width="45%">City Name&nbsp;&nbsp;<img src="images/asc.gif" width="12" height="7" onclick="city_onclick(1)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="cityColumn" value="1" /></th>';
			elseif($c1==2)
				echo '<th width="45%">City Name&nbsp;&nbsp;<img src="images/desc.gif" width="12" height="7" onclick="city_onclick(2)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="cityColumn" value="2" /></th>';
			
			if($c2==0)
				echo '<th width="45%">State&nbsp;&nbsp;<img src="&nbsp;" width="12" height="7" onclick="state_onclick(0)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="stateColumn" value="0" /></th>';
			elseif($c2==1)
				echo '<th width="45%">State&nbsp;&nbsp;<img src="images/asc.gif" width="12" height="7" onclick="state_onclick(1)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="stateColumn" value="1" /></th>';
			elseif($c2==2)
				echo '<th width="45%">State&nbsp;&nbsp;<img src="images/desc.gif" width="12" height="7" onclick="state_onclick(2)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="stateColumn" value="2" /></th>';
			?>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql = "SELECT city.*,state_name FROM city INNER JOIN state ON city.state_id=state.state_id ";
		if($c1==0 && $c2==0)
			$sql .= "ORDER BY state_name,city_name ";
		elseif($c1==1)
			$sql .= "ORDER BY city_name ";
		elseif($c1==2)
			$sql .= "ORDER BY city_name DESC ";
		elseif($c2==1)
			$sql .= "ORDER BY state_name ";
		elseif($c2==2)
			$sql .= "ORDER BY state_name DESC ";
		$sql .= "LIMIT ".$start.",".$end;
		$sql_city = mysql_query($sql) or die(mysql_error());
		
		while($row_city=mysql_fetch_array($sql_city))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "cit2y.php?action=delete&cid=".$row_city['city_id'];
			$edit_ref = "cit2y.php?action=edit&cid=".$row_city['city_id'];
			
			echo '<td>'.$i.'.</td><td>'.$row_city['city_name'].'</td><td>'.$row_city['state_name'].'</td>';
			echo '<td style="text-align:center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Bottom">
			<td colspan="4" style="text-align:center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM city") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			$strg = "";
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_city('.$strg.')" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="ctyid" id="ctyid" value="'.$cid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_city('.$strg.')" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_city()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_city()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_city()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_city()" />';
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
</center>
</body>
</html>