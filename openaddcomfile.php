<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*------------------------------*/
$cid = "";
if(isset($_REQUEST['cid'])){$cid = $_REQUEST['cid'];}
if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
	$sql = mysql_query("SELECT company.*,state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE company_id=".$cid);
	$row = mysql_fetch_assoc($sql);
}
/*------------------------------*/
if(isset($_POST['submit'])){
	$sql = mysql_query("SELECT company_id FROM company WHERE company_name='".$_POST['companyName']."'") or die(mysql_error());
	$row_company = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_company['company_id']!=$cid)
				$msg = "Duplication Error! can&prime;t update into company master record.";
			elseif($row_company['company_id']==$cid){
				$res = mysql_query("UPDATE company SET company_name='".$_POST['companyName']."',c_address1='".$_POST['address1']."',c_address2='".$_POST['address2']."',c_address3='".$_POST['address3']."',c_cityid=".$_POST['city'].",c_phone='".$_POST['phoneNo']."',c_fax='".$_POST['faxNo']."',c_email='".$_POST['emailID']."',c_tin='".$_POST['tin']."',c_cst='".$_POST['cst']."' WHERE company_id=".$cid) or die(mysql_error());
				header("location:openaddcomfile.php?action=new");
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE company SET company_name='".$_POST['companyName']."',c_address1='".$_POST['address1']."',c_address2='".$_POST['address2']."',c_address3='".$_POST['address3']."',c_cityid=".$_POST['city'].",c_phone='".$_POST['phoneNo']."',c_fax='".$_POST['faxNo']."',c_email='".$_POST['emailID']."',c_tin='".$_POST['tin']."',c_cst='".$_POST['cst']."' WHERE company_id=".$cid) or die(mysql_error());
			header("location:openaddcomfile.php?action=new");
		}
	} elseif($_POST['submit']=="delete"){
		$sqlPlot = mysql_query("SELECT * FROM plot WHERE company_id=".$cid) or die(mysql_error());
		$rowPlot = mysql_fetch_assoc($sqlPlot);
		$count = mysql_num_rows($sqlPlot);
		if($count>0)
			$msg = "To many records found in Plot master.<br>Sorry! it can't delete from company master record.";
		else {
			$res = mysql_query("DELETE FROM company WHERE company_id=".$cid) or die(mysql_error());
			header("location:openaddcomfile.php?action=new");
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into company master record.";
		else {
			$sql = mysql_query("SELECT Max(company_id) as maxid FROM company");
			$row = mysql_fetch_assoc($sql);
			$cid = $row["maxid"] + 1;
			$sql = "INSERT INTO company (company_id,company_name,c_address1,c_address2,c_address3,c_cityid,c_phone,c_fax,c_email,c_tin,c_cst) VALUES(".$cid.",'".$_POST['companyName']."','".$_POST['address1']."','".$_POST['address2']."','".$_POST['address3']."',".$_POST['city'].",'".$_POST['phoneNo']."','".$_POST['faxNo']."','".$_POST['emailID']."','".$_POST['tin']."','".$_POST['cst']."')";
			$res = mysql_query($sql) or die(mysql_error());
			header("location:openaddcomfile.php?action=new");
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Company Master</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_company()
{
	var err="";
	if(document.getElementById("companyName").value=="")
		err = "* please input company name!\n";
	if(document.getElementById("city").value==0)
		err += "* please select city of the company!\n";
	if(document.getElementById("phoneNo").value!="")
		err += validatePhone(document.getElementById("phoneNo"),"Telephone Number");
	if(document.getElementById("faxNo").value!="")
		err += validatePhone(document.getElementById("faxNo"),"Fax Number");
	if(document.getElementById("emailID").value!="")
		err += validateEmail(document.getElementById("emailID"));
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function paging_cmp()
{
	if(document.getElementById("xson").value=="new")
		window.location="openaddcomfile.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
	else
		window.location="openaddcomfile.php?action="+document.getElementById("xson").value+"&cid="+document.getElementById("cmpid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_cmp()
{
	document.getElementById("page").value = 1;
	paging_cmp();
}

function previouspage_cmp()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_cmp();
}

function nextpage_cmp()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_cmp();
}

function lastpage_cmp()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_cmp();
}
</script>
</head>


<body onload="document.getElementById('companyName').focus()">
<table align="center" cellspacing="0" cellpadding="0" width="830px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="company"  method="post" onsubmit="return validate_company()">
	<table align="center" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Company Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Company Name:<span style="color:#FF0000">*</span></td>
			<td><input name="companyName" id="companyName" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["company_name"];}?>" tabindex="1"></td>
			
			<td class="th" nowrap>Phone No.:</td>
			<td><input name="phoneNo" id="phoneNo" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_phone"];}?>" tabindex="6"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_address1"];}?>" tabindex="2"></td>
			
			<td class="th" nowrap>Fax No.:</td>
			<td><input name="faxNo" id="faxNo" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_fax"];}?>" tabindex="7"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_address2"];}?>" tabindex="3"></td>
			
			<td class="th" nowrap>E-mail:</td>
			<td><input name="emailID" id="emailID" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_email"];}?>" tabindex="8"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Address3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="40" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["c_address3"];}?>" tabindex="4"></td>
			
			<td class="th" nowrap>T.I.N.:</td>
			<td><input name="tin" id="tin" maxlength="15" size="20" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["tin"];}?>" tabindex="9"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>City Name:<span style="color:#FF0000">*</span></td>
			<td><select name="city" id="city" style="width:250px" onchange="get_state(this.value)" tabindex="5">
			<option value="0">-- Select --</option>
			<?php 
			$sql_city=mysql_query("SELECT * FROM city ORDER BY city_name");
			while($row_city=mysql_fetch_array($sql_city))
			{
				if($row_city["city_id"]==$row["c_cityid"])
					echo '<option selected value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';
				else
					echo '<option value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('city.php?action=new','citymaster','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
			
			<td class="th" nowrap>C.S.T.:</td>
			<td><input name="cst" id="cst" maxlength="15" size="20" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["cst"];}?>" tabindex="10"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>State:</td>
			<td><span id="state"><input name="stateName" id="stateName" maxlength="50" size="40" readonly="true" value="<?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){ echo $row["state_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"></span></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

		<?php if($msg!=""){
		echo '<tr class="Controls">
			<td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td>
		</tr>';
		} ?>

 		<tr class="Bottom">
			<td colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new" tabindex="11"><input type="hidden" name="submit" value="new"/>&nbsp;&nbsp;<a href="javascript:document.company.reset()" tabindex="12"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update" tabindex="11"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='openaddcomfile.php?action=new'" tabindex="12"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete" tabindex="11"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='openaddcomfile.php?action=new'" tabindex="12"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" tabindex="13"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<table align="center" width="750px" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>List of Company</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="30%">Company Name</th>
			<th width="20%">City Name</th>
			<th width="20%">State Name</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$start=0;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$start=($_REQUEST['pg']-1)*$end;}
		
		$i = $start;
		$sql_company = mysql_query("SELECT company.*,city_name,state_name FROM company INNER JOIN city ON company.c_cityid = city.city_id INNER JOIN state ON city.state_id = state.state_id ORDER BY company_name LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_company=mysql_fetch_array($sql_company))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "openaddcomfile.php?action=delete&cid=".$row_company['company_id'];
			$edit_ref = "openaddcomfile.php?action=edit&cid=".$row_company['company_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_company['company_name'].'</td><td>'.$row_company['city_name'].'</td><td>'.$row_company['state_name'].'</td>';
			echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="6" align="center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM company") or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_cmp()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" tabindex="14" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="cmpid" id="cmpid" value="'.$cid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_cmp()" style="vertical-align:middle">';
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
			if($total_page>1 && $_REQUEST["pg"]>1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_cmp()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_cmp()" />&nbsp;&nbsp;';
			if($total_page>1 && $_REQUEST["pg"]<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_cmp()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_cmp()" />';
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