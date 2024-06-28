<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------------------*/
$msg = "";
$pid = "";
$party_name = "";
$contact_person = "";
$address1 = "";
$address2 = "";
$address3 = "";
$state_name = "";
$city_id = 0;
$email_id = "";
$mobile_no = "";
$pan = "";
$op_balance = "";
$tin = "";
$category = 0;
$credit_days = "";
/*--------------------------------*/
if(isset($_REQUEST['pid'])){
	$pid = $_REQUEST['pid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT party.*,state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id WHERE party_id=".$pid);
		$row = mysql_fetch_assoc($sql);
		$party_name = $row["party_name"];
		$contact_person = $row["contact_person"];
		$address1 = $row["address1"];
		$address2 = $row["address2"];
		$address3 = $row["address3"];
		$state_name = $row["state_name"];
		$city_id = $row["city_id"];
		$email_id = $row["email_id"];
		$mobile_no = $row["mobile_no"];
		$pan = $row["pan"];
		$op_balance = $row["op_balance"];
		$tin = $row["tin"];
		$category = $row["category"];
		$credit_days = $row["credit_days"];
	}
}
/*------------------------------*/
if(isset($_POST['submit'])){
	$opBal = ($_POST['opBalance']==""? 0 : $_POST['opBalance']);
	$crDays = ($_POST['creditDays']==""? 0 : $_POST['creditDays']);
	/*------------------------------*/
	$sql = mysql_query("SELECT party_id FROM party WHERE party_name='".$_POST['partyName']."'") ;
	$row_party = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($row_party['party_id']!=$pid)
				$msg = "Duplication Error! can&prime;t update into party master record.";
			elseif($row_party['party_id']==$pid){
				$res = mysql_query("UPDATE party SET party_name='".$_POST['partyName']."',address1='".$_POST['address1']."',address2='".$_POST['address2']."',address3='".$_POST['address3']."',city_id=".$_POST['city'].",contact_person='".$_POST['contactPerson']."',email_id='".$_POST['emailID']."',mobile_no='".$_POST['mobileNo']."',pan='".$_POST['pan']."',tin='".$_POST['tin']."',op_balance=".$opBal.",credit_days=".$crDays.",category=".$_POST['category']." WHERE party_id=".$pid) ;
				echo '<script language="javascript">window.location="openaddpartyfile.php?action=new";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE party SET party_name='".$_POST['partyName']."',address1='".$_POST['address1']."',address2='".$_POST['address2']."',address3='".$_POST['address3']."',city_id=".$_POST['city'].",contact_person='".$_POST['contactPerson']."',email_id='".$_POST['emailID']."',mobile_no='".$_POST['mobileNo']."',pan='".$_POST['pan']."',tin='".$_POST['tin']."',op_balance=".$opBal.",credit_days=".$crDays.",category=".$_POST['category']." WHERE party_id=".$pid) ;
			echo '<script language="javascript">window.location="openaddpartyfile.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$delete_confirm = "yes";
		$sql = mysql_query("SELECT * FROM tblbill WHERE party_id=".$pid) ;
		if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tblpayment1 WHERE party_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tblpo WHERE party_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM bill1 WHERE broaker_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM challan1 WHERE broaker_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM os_packing WHERE broaker_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM pkg_return WHERE broaker_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM rate_input WHERE broaker_id=".$pid) ;
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		/*-----------------------------*/
		if($delete_confirm == "no"){
			$msg = "To many records found in database.<br>Sorry! it can't delete from party master record.";
		} elseif($delete_confirm == "yes"){
			$res = mysql_query("DELETE FROM party WHERE party_id=".$pid) ;
			echo '<script language="javascript">window.location="openaddpartyfile.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into party master record.";
		else {
			$sql = mysql_query("SELECT Max(party_id) as maxid FROM party");
			$row = mysql_fetch_assoc($sql);
			$pid = $row["maxid"] + 1;
			$sql = "INSERT INTO party VALUES(".$pid.",'".$_POST['partyName']."','".$_POST['address1']."','".$_POST['address2']."','".$_POST['address3']."',".$_POST['city'].",'".$_POST['contactPerson']."','".$_POST['emailID']."','".$_POST['mobileNo']."','".$_POST['pan']."','".$_POST['tin']."',".$opBal.",".$crDays.",".$_POST['category'].")";
			$res = mysql_query($sql) ;
			echo '<script language="javascript">window.location="openaddpartyfile.php?action=new";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_partydata()
{
	var err="";
	if(document.getElementById("partyName").value=="")
		err = "* please input party name!\n";
	if(document.getElementById("city").value==0)
		err += "* please select city of the party!\n";
	if(document.getElementById("mobileNo").value!="")
		err += validatePhone(document.getElementById("mobileNo"),"Mobile Number");
	if(document.getElementById("emailID").value!="")
		err += validateEmail(document.getElementById("emailID"));
	if(document.getElementById("category").value==0)
		err += "* please select category of the party!\n";
	if(document.getElementById("opBalance").value!="" && ! IsNumeric(document.getElementById("opBalance").value))
		err += "* please input valid opening Balance of the party!\n";
	if(document.getElementById("creditDays").value!="" && ! IsNumeric(document.getElementById("creditDays").value))
		err += "* please input valid credit days of the party!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function paging_party()
{
	if(document.getElementById("xson").value=="new")
		window.location="openaddpartyfile.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
	else
		window.location="openaddpartyfile.php?action="+document.getElementById("xson").value+"&pid="+document.getElementById("ptyid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_party()
{
	document.getElementById("page").value = 1;
	paging_party();
}

function previouspage_party()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_party();
}

function nextpage_party()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_party();
}

function lastpage_party()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_party();
}

function show_party_list()
{
	document.getElementById("spanPartyList").style.display = '';
	get_matching_party();
}

function hide_party_list()
{
	document.getElementById("spanPartyList").style.display = 'none';
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="390px" width="780px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="party"  method="post" onsubmit="return validate_partydata()">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Party Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Party Name:<span style="color:#FF0000">*</span></td>
			<td><input name="partyName" id="partyName" maxlength="50" size="40" value="<?php echo $party_name;?>" onfocus="show_party_list()" onblur="hide_party_list()" onkeyup="get_matching_party()"><span id="spanPartyList" style="display:none;"></span></td>
			
			<td class="th" nowrap>Contact Person:</td>
			<td><input name="contactPerson" id="contactPerson" maxlength="50" size="40" value="<?php echo $contact_person;?>"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Address1:</td>
			<td><input name="address1" id="address1" maxlength="50" size="40" value="<?php echo $address1;?>"></td>
			
			<td class="th">City Name:<span style="color:#FF0000">*</span></td>
			<td><select name="city" id="city" style="width:250px" onchange="get_state(this.value)"><option value="0">-- Select --</option>
			<?php 
			$sql_city=mysql_query("SELECT * FROM city ORDER BY city_name");
			while($row_city=mysql_fetch_array($sql_city)){
				if($row_city["city_id"]==$city_id)
					echo '<option selected value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';
				else
					echo '<option value="'.$row_city['city_id'].'">'.$row_city['city_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('city.php?action=new','city','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0" /></a></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Address2:</td>
			<td><input name="address2" id="address2" maxlength="50" size="40" value="<?php echo $address2;?>"></td>
			
			<td class="th" nowrap>State:</td>
			<td><span id="state"><input name="stateName" id="stateName" maxlength="50" size="40" readonly="true" value="<?php echo $state_name;?>" style="background-color:#E7F0F8; color:#0000FF"></span></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Address3:</td>
			<td><input name="address3" id="address3" maxlength="50" size="40" value="<?php echo $address3;?>"></td>
			
			<td class="th" nowrap>e-mail:</td>
			<td><input name="emailID" id="emailID" maxlength="50" size="40" value="<?php echo $email_id;?>"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Mobile No.:</td>
			<td><input name="mobileNo" id="mobileNo" maxlength="15" size="20" value="<?php echo $mobile_no;?>"></td>
			
			<td class="th" nowrap>P.A.N.:</td>
			<td><input name="pan" id="pan" maxlength="15" size="20" value="<?php echo $pan;?>"></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Opening Balance:</td>
			<td><input name="opBalance" id="opBalance" maxlength="10" size="20" value="<?php echo $op_balance;?>"></td>
			
			<td class="th" nowrap>T.I.N.:</td>
			<td><input name="tin" id="tin" maxlength="15" size="20" value="<?php echo $tin;?>"></td>
		</tr>

		<tr class="Controls">
			<td class="th">Category:<span style="color:#FF0000">*</span></td>
			<td><select name="category" id="category" style="width:150px"><option value="0">-- Select --</option>
			<?php 
			if($category==1){echo '<option selected value="1">Preferencial</option>';} else {echo '<option value="1">Preferencial</option>';}
			if($category==2){echo '<option selected value="2">blank-1</option>';} else {echo '<option value="2">blank-1</option>';}
			if($category==3){echo '<option selected value="3">blank-2</option>';} else {echo '<option value="3">blank-2</option>';}
			if($category==4){echo '<option selected value="4">blank-3</option>';} else {echo '<option value="4">blank-3</option>';}
			?>
			</select></td>
			
			<td class="th" nowrap>Credit Days:</td>
			<td><input name="creditDays" id="creditDays" maxlength="3" size="10" value="<?php echo $credit_days;?>">&nbsp;days</td>
		</tr>

		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

 		<tr class="Bottom">
			<td colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='openaddpartyfile.php?action=new'"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
&nbsp;&nbsp;<a href="javascript:window.location='menu.php'"><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>List of Party</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="35%">Party Name</th>
			<th width="20%">City</th>
			<th width="20%">State</th>
			<th width="15%">Category</th>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql_party = mysql_query("SELECT party.*,city_name,state_name FROM party INNER JOIN city ON party.city_id = city.city_id INNER JOIN state ON city.state_id = state.state_id ORDER BY party_name LIMIT ".$start.",".$end) ;
		
		while($row_party=mysql_fetch_array($sql_party))
		{
			$i++;
			echo '<tr class="Row">';
			$delete_ref = "openaddpartyfile.php?action=delete&pid=".$row_party['party_id'];
			$edit_ref = "openaddpartyfile.php?action=edit&pid=".$row_party['party_id'];
			if($row_party['category']==1){
				$categoryName = "Preferencial";
			} elseif($row_party['category']==2){
				$categoryName = "blank-1";
			} elseif($row_party['category']==3){
				$categoryName = "blank-2";
			} elseif($row_party['category']==4){
				$categoryName = "blank-3";
			}
			
			echo '<td>'.$i.'.</td><td>'.$row_party['party_name'].'</td><td>'.$row_party['city_name'].'</td><td>'.$row_party['state_name'].'</td><td>'.$categoryName.'</td>';
			echo '<td style="text-align:center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="6" style="text-align:center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM party") ;
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_party()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="'.$_REQUEST["action"].'" /><input type="hidden" name="ptyid" id="ptyid" value="'.$pid.'" />';
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_party()" style="vertical-align:middle">';
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
			if($total_page>1 && $pg > 1)
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_party()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_party()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg < $total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_party()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_party()" />';
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