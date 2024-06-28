<?php 
include("menu.php");
/*--------------------------------*/
$c1 = 0;
if(isset($_REQUEST["c1"])){$c1 = $_REQUEST["c1"];}
$c2 = 0;
if(isset($_REQUEST["c2"])){$c2 = $_REQUEST["c2"];}
/*-----------------------------*/
$msg = "";
$iid = "";
$itgroup_id = 0;
$item_name = "";
$tech_name = "";
$tech_details = "";
$unit_id = 0;
$alt_unit = "N";
$unit_name = "";
$alt_unit_num = "";
$alt_unit_id = 0;
$water_require = "";
$recomend_dose = "";
$max_dose = "";
$min_dose = "";
$usability_id1 = 0;
$usability_id2 = 0;
$usability_id3 = 0;
$rp_from1 = 0;
$rp_from2 = 0;
$rp_from3 = 0;
$rp_to1 = 0;
$rp_to2 = 0;
$rp_to3 = 0;
$app_method = 0;
$reorder_level = "";
$lead_time = "";
/*-----------------------------*/
if(isset($_REQUEST['iid'])){
	$iid = $_REQUEST['iid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql = mysql_query("SELECT item.*,unit_name FROM item INNER JOIN unit ON item.unit_id=unit.unit_id WHERE item_id=".$iid);
		$row = mysql_fetch_assoc($sql);
		$itgroup_id = $row["itgroup_id"];
		$item_name = $row["item_name"];
		$tech_name = $row["tech_name"];
		$tech_details = $row["tech_details"];
		$unit_id = $row["unit_id"];
		$alt_unit = $row["alt_unit"];
		$unit_name = $row["unit_name"];
		$alt_unit_num = $row["alt_unit_num"];
		$alt_unit_id = $row["alt_unit_id"];
		$water_require = $row["water_require"];
		$recomend_dose = $row["recomend_dose"];
		$max_dose = $row["max_dose"];
		$min_dose = $row["min_dose"];
		$usability_id1 = $row["usability_id1"];
		$usability_id2 = $row["usability_id2"];
		$usability_id3 = $row["usability_id3"];
		$rp_from1 = $row["rp_from1"];
		$rp_from2 = $row["rp_from2"];
		$rp_from3 = $row["rp_from3"];
		$rp_to1 = $row["rp_to1"];
		$rp_to2 = $row["rp_to2"];
		$rp_to3 = $row["rp_to3"];
		$app_method = $row["app_method"];
		$reorder_level = $row["reorder_level"];
		$lead_time = $row["lead_time"];
	}
}
/*-----------------------------*/
if(isset($_POST['submit'])){
	$water_requirement = ($_POST['waterRequirement']=="" ? 0 : $_POST['waterRequirement']);
	$recommended_dose = ($_POST['recommendedDose']=="" ? 0 : $_POST['recommendedDose']);
	$maximum_dose = ($_POST['maximumDose']=="" ? 0 : $_POST['maximumDose']);
	$minimum_dose = ($_POST['minimumDose']=="" ? 0 : $_POST['minimumDose']);
	$reorder_level = ($_POST['reorderLevel']=="" ? 0 : $_POST['reorderLevel']);
	$lead_time = ($_POST['leadTime']=="" ? 0 : $_POST['leadTime']);
	/*-----------------------------*/
	$sql = mysql_query("SELECT * FROM item WHERE (item_name='".$_POST['itemName']."' OR item_name like '".$_POST['itemName']."')") or die(mysql_error());
	$row_item = mysql_fetch_assoc($sql);
	$count = mysql_num_rows($sql);
	/*-----------------------------*/
	if($_POST['submit']=="update"){
		$updation_allow = "yes";
		$change_alt_unit = "yes";
		$change_prime_unit = "yes";
		if(($row['alt_unit']=="A" && $_POST['altUnitApplicable']=="N") || ($row['alt_unit_id']!=$_POST['alternateUnit'])){
			$sql_stores = mysql_query("SELECT * FROM stock_register WHERE item_id=".$iid." AND unit_id=".$row['alt_unit_id']) or die(mysql_error());
			if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_alt_unit = "no";}
			if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tbl_indent_item WHERE item_id=".$iid." AND unit_id=".$row['alt_unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_alt_unit = "no";}
			}
			if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tblpo_item WHERE item_id=".$iid." AND unit_id=".$row['alt_unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_alt_unit = "no";}
			}
			/*if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tblipt_item WHERE item_id=".$iid." AND unit_id=".$row['alt_unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_alt_unit = "no";}
			}*/
		}
		if($row['unit_id']!=$_POST['unit']){
			$sql_stores = mysql_query("SELECT * FROM stock_register WHERE item_id=".$iid." AND unit_id=".$row['unit_id']) or die(mysql_error());
			if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_prime_unit = "no";}
			if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tbl_indent_item WHERE item_id=".$iid." AND unit_id=".$row['unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_prime_unit = "no";}
			}
			if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tblpo_item WHERE item_id=".$iid." AND unit_id=".$row['unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_prime_unit = "no";}
			}
			/*if($updation_allow == "yes"){
				$sql_stores = mysql_query("SELECT * FROM tblipt_item WHERE item_id=".$iid." AND unit_id=".$row['unit_id']) or die(mysql_error());
				if(mysql_num_rows($sql_stores)>0){$updation_allow = "no"; $change_prime_unit = "no";}
			}*/
		}
		if($updation_allow == "yes"){
			if($count>0){
				if($row_item['item_id']!=$iid)
					$msg = "Duplication Error! can&prime;t update into item master record.";
				elseif($row_item['item_id']==$iid){
					$res = mysql_query("UPDATE item SET item_name='".$_POST['itemName']."',itgroup_id=".$_POST['itemGroup'].",tech_name='".$_POST['techName']."',unit_id=".$_POST['unit'].",alt_unit='".$_POST['altUnitApplicable']."',alt_unit_id=".($_POST['altUnitApplicable']=='A'?$_POST['alternateUnit']:0).",alt_unit_num=".($_POST['altUnitApplicable']=='A' && $_POST['alternateUnit']!=0?$_POST['oneUnitEqual']:1).",water_require=".$water_requirement.",recomend_dose=".$recommended_dose.",max_dose=".$maximum_dose.",min_dose=".$minimum_dose.",usability_id1=".$_POST['usefulIn1'].",usability_id2=".$_POST['usefulIn2'].",usability_id3=".$_POST['usefulIn3'].",app_method=".$_POST['applicationMethod'].",rp_from1=".$_POST['rpFrom1'].",rp_to1=".$_POST['rpTo1'].",rp_from2=".$_POST['rpFrom2'].",rp_to2=".$_POST['rpTo2'].",rp_from3=".$_POST['rpFrom3'].",rp_to3=".$_POST['rpTo3'].",reorder_level=".$reorder_level.",lead_time=".$lead_time.",tech_details='".$_POST['techDetail']."' WHERE item_id=".$iid) or die(mysql_error());
					echo '<script language="javascript">window.location="item.php?action=new";</script>';
				}
			} elseif($count==0){
				$res = mysql_query("UPDATE item SET item_name='".$_POST['itemName']."',itgroup_id=".$_POST['itemGroup'].",tech_name='".$_POST['techName']."',unit_id=".$_POST['unit'].",alt_unit='".$_POST['altUnitApplicable']."',alt_unit_id=".($_POST['altUnitApplicable']=='A'?$_POST['alternateUnit']:0).",alt_unit_num=".($_POST['altUnitApplicable']=='A' && $_POST['alternateUnit']!=0?$_POST['oneUnitEqual']:1).",water_require=".$water_requirement.",recomend_dose=".$recommended_dose.",max_dose=".$maximum_dose.",min_dose=".$minimum_dose.",usability_id1=".$_POST['usefulIn1'].",usability_id2=".$_POST['usefulIn2'].",usability_id3=".$_POST['usefulIn3'].",app_method=".$_POST['applicationMethod'].",rp_from1=".$_POST['rpFrom1'].",rp_to1=".$_POST['rpTo1'].",rp_from2=".$_POST['rpFrom2'].",rp_to2=".$_POST['rpTo2'].",rp_from3=".$_POST['rpFrom3'].",rp_to3=".$_POST['rpTo3'].",reorder_level=".$reorder_level.",lead_time=".$lead_time.",tech_details='".$_POST['techDetail']."' WHERE item_id=".$iid) or die(mysql_error());
				echo '<script language="javascript">window.location="item.php?action=new";</script>';
			}
		} elseif($updation_allow == "no"){
			if($change_prime_unit == "no")
				$msg = "Sorry dear! can&prime;t change primary unit into item master record.";
			elseif($change_alt_unit == "no")
				$msg = "Sorry dear! can&prime;t change alternate unit into item master record.";
		}
	} elseif($_POST['submit']=="delete"){
		$delete_confirm = "yes";
		$sql = mysql_query("SELECT * FROM stock_register WHERE item_id=".$iid) or die(mysql_error());
		if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tblbill_item WHERE item_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tblipt_item WHERE item_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tblpo_item WHERE item_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query("SELECT * FROM tbl_indent_item WHERE item_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		/*-----------------------------*/
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM bill3 WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM challan2 WHERE crop_id=".$iid." OR pkg_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM crop_input2 WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM field_input1 WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM os_packing WHERE pkg_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM pkg_return WHERE pkg_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM production WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM rate_input WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		if($delete_confirm == "yes"){
			$sql = mysql_query(DATABASE4,"SELECT * FROM seed WHERE crop_id=".$iid) or die(mysql_error());
			if(mysql_num_rows($sql)>0){$delete_confirm = "no";}
		}
		/*-----------------------------*/
		if($delete_confirm == "no"){
			$msg = "To many records found in Stores data.<br>Sorry! it can't delete from item master record.";
		} elseif($delete_confirm == "yes"){
			$res = mysql_query("DELETE FROM item WHERE item_id=".$iid) or die(mysql_error());
			echo '<script language="javascript">window.location="item.php?action=new";</script>';
		}
	} elseif($_POST['submit']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into item master record.";
		else {
			$sql = mysql_query("SELECT Max(item_id) as maxid FROM item WHERE itgroup_id=".$_POST['itemGroup']);
			$row = mysql_fetch_assoc($sql);
			$iid = ($row["maxid"]==null ? $_POST['itemGroup']*1000+1 : $row["maxid"]+1);
			$sql = "INSERT INTO item (item_id,item_name,itgroup_id,tech_name,unit_id,alt_unit,alt_unit_id,alt_unit_num,water_require,recomend_dose,max_dose,min_dose, usability_id1,usability_id2,usability_id3,app_method,rp_from1,rp_to1,rp_from2,rp_to2,rp_from3,rp_to3,reorder_level,lead_time,tech_details) VALUES(".$iid.",'".$_POST['itemName']."',".$_POST['itemGroup'].",'".$_POST['techName']."',".$_POST['unit'].",'".$_POST['altUnitApplicable']."',".($_POST['altUnitApplicable']=='A'?$_POST['alternateUnit']:0).",".($_POST['altUnitApplicable']=='A' && $_POST['alternateUnit']!=0?$_POST['oneUnitEqual']:1).",".$water_requirement.",".$recommended_dose.",".$maximum_dose.",".$minimum_dose.",".$_POST['usefulIn1'].",".$_POST['usefulIn2'].",".$_POST['usefulIn3'].",".$_POST['applicationMethod'].",".$_POST['rpFrom1'].",".$_POST['rpTo1'].",".$_POST['rpFrom2'].",".$_POST['rpTo2'].",".$_POST['rpFrom3'].",".$_POST['rpTo3'].",".$reorder_level.",".$lead_time.",'".$_POST['techDetail']."')";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">window.location="item.php?action=new";</script>';
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
function validate_itemdata()
{
	var err="";
	if(document.getElementById("itemGroup").value==0)
		err = "* please select the item's group name!\n";
	if(document.getElementById("itemName").value=="")
		err += "* please input item name!\n";
	if(document.getElementById("unit").value==0)
		err += "* please select the unit of item!\n";
	if(document.getElementById("altUnitApplicable").value=="S")
		err += "* please select alternate unit, applicable or not, for the item!\n";
	if(document.getElementById("altUnitApplicable").value=="A" && document.getElementById("alternateUnit").value==0)
		err += "* please select alternate unit for the item!\n";
	if(document.getElementById("altUnitApplicable").value=="A" && document.getElementById("alternateUnit").value!=0){
		if(document.getElementById("oneUnitEqual").value=="" || document.getElementById("oneUnitEqual").value==0)
			err += "* please input computational value of alternate unit for the item!\n";
		else if(document.getElementById("oneUnitEqual").value!="" && ! IsNumeric(document.getElementById("oneUnitEqual").value))
			err += "* please input computational value (numeric only) of alternate unit!\n";
	}
	if(document.getElementById("waterRequirement").value!="" && ! IsNumeric(document.getElementById("waterRequirement").value))
		err += "* please input valid water requirement!\n";
	if(document.getElementById("recommendedDose").value!="" && ! IsNumeric(document.getElementById("recommendedDose").value))
		err += "* please input valid recommended dose!\n";
	if(document.getElementById("maximumDose").value!="" && ! IsNumeric(document.getElementById("maximumDose").value))
		err += "* please input valid maximum dose!\n";
	if(document.getElementById("minimumDose").value!="" && ! IsNumeric(document.getElementById("minimumDose").value))
		err += "* please input valid minimum dose!\n";
	if(document.getElementById("reorderLevel").value!="" && ! IsNumeric(document.getElementById("reorderLevel").value))
		err += "* please input valid reorder level!\n";
	if(document.getElementById("leadTime").value!="" && ! IsNumeric(document.getElementById("leadTime").value))
		err += "* please input valid lead time!\n";
	if(document.getElementById("rpFrom1").value==0 && document.getElementById("rpTo1").value!=0)
		err += "* please select requirement period From for sequence no.1!\n";
	if(document.getElementById("rpFrom1").value!=0 && document.getElementById("rpTo1").value==0)
		err += "* please select requirement period To for sequence no.1!\n";
	if(document.getElementById("rpFrom2").value==0 && document.getElementById("rpTo2").value!=0)
		err += "* please select requirement period From for sequence no.2!\n";
	if(document.getElementById("rpFrom2").value!=0 && document.getElementById("rpTo2").value==0)
		err += "* please select requirement period To for sequence no.2!\n";
	if(document.getElementById("rpFrom3").value==0 && document.getElementById("rpTo3").value!=0)
		err += "* please select requirement period From for sequence no.3!\n";
	if(document.getElementById("rpFrom3").value!=0 && document.getElementById("rpTo3").value==0)
		err += "* please select requirement period To for sequence no.3!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function get_alt_unit(me)
{
	if(me=="A"){
		document.getElementById("alternateUnit").disabled=false;
		document.getElementById("oneUnitEqual").disabled=false;
	} else if(me=="N"){
		document.getElementById("alternateUnit").selectedIndex="0";
		document.getElementById("alternateUnit").disabled=true;
		document.getElementById("oneUnitEqual").value="";
		document.getElementById("oneUnitEqual").disabled=true;
		document.getElementById("oneUE2").innerHTML='1 unit =';
	}
}

function show_alt_unit(me)
{
	document.getElementById("oneUE2").innerHTML='1 '+document.getElementById("unit").options[document.getElementById("unit").selectedIndex].text+' =';
	if(me.value!=0){
		document.getElementById("altUnit").innerHTML=me.options[me.selectedIndex].text;
	} else if(me.value==0){
		document.getElementById("altUnit").innerHTML='alternate unit';
	}
}

function searching_data()
{
	var err="";
	if(document.getElementById("searchText").value==""){err = "Please input the text, that is to be searched!\n";}
	if(document.getElementById("searchFrom").value==0){err += "Please select the field, where text to be searched!\n";}
	if(err==""){
		window.location="item.php?action=search&txt="+document.getElementById("searchText").value+"&on="+document.getElementById("searchFrom").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
	} else {
		alert("Error:\n"+err);
	}
}

function item_onclick(col1)
{
	if(col1==0 || col1==1) col1=2; else col1=1;
	var strg = '&c1='+col1+'&c2=0';
	paging_item(strg);
}

function group_onclick(col2)
{
	if(col2==0 || col2==1) col2=2; else col2=1;
	var strg = '&c1=0&c2='+col2;
	paging_item(strg);
}

function paging_item(value1)
{
	if(document.getElementById("xson").value=="new")
		window.location="item.php?action="+document.getElementById("xson").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+value1;
	else if(document.getElementById("xson").value=="edit" || document.getElementById("xson").value=="delete")
		window.location="item.php?action="+document.getElementById("xson").value+"&iid="+document.getElementById("itmid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+value1;
	else if(document.getElementById("xson").value=="search")
		window.location="item.php?action=search&txt="+document.getElementById("searchText").value+"&on="+document.getElementById("searchFrom").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value+value1;
}

function firstpage_item()
{
	document.getElementById("page").value = 1;
	paging_item("");
}

function previouspage_item()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_item("");
}

function nextpage_item()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage < parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_item("");
}

function lastpage_item()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_item("");
}

function show_itlist()
{
	document.getElementById("spanITList").style.display = '';
	get_matching_items();
}

function hide_itlist()
{
	document.getElementById("spanITList").style.display = 'none';
}

</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="620px" width="900px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="item"  method="post" onsubmit="return validate_itemdata()">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Item Master</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th">Item Group Name:<span style="color:#FF0000">*</span></td>
			<td><select name="itemGroup" id="itemGroup" style="width:260px"><option value="0">-- Select --</option><?php 
			$sql_itgroup=mysql_query("SELECT * FROM itemgroup ORDER BY itgroup_name");
			while($row_itgroup=mysql_fetch_array($sql_itgroup)){
				if($row_itgroup["itgroup_id"]==$itgroup_id)
					echo '<option selected value="'.$row_itgroup['itgroup_id'].'">'.$row_itgroup['itgroup_name'].'</option>';
				else
					echo '<option value="'.$row_itgroup['itgroup_id'].'">'.$row_itgroup['itgroup_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('itemgroup.php?action=new','itemgroup','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0"/></a></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Item Name:<span style="color:#FF0000">*</span></td>
			<td><input name="itemName" id="itemName" maxlength="50" size="45" value="<?php echo $item_name;?>" onfocus="show_itlist()" onblur="hide_itlist()" onkeyup="get_matching_items()"><span id="spanITList" style="display:none;"></span></td>
			
			<td class="th" nowrap>Technical Name:</td>
			<td><input name="techName" id="techName" maxlength="50" size="45" value="<?php echo $tech_name;?>" ></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Technical Details:</td>
			<td><textarea name="techDetail" id="techDetail" cols="35" rows="5" ><?php echo $tech_details;?></textarea></td>
			
			<td class="th">Unit of Measurement:<span style="color:#FF0000">*</span><br/><br/>Is Alternate Unit?<span style="color:#FF0000">*</span></td>
			<td><select name="unit" id="unit" style="width:150px" onchange="get_unit(this.value)" ><option value="0">-- Select --</option><?php 
			$sqlUnit=mysql_query("SELECT * FROM unit ORDER BY unit_name");
			while($rowUnit=mysql_fetch_array($sqlUnit)){
				if($rowUnit["unit_id"]==$unit_id)
					echo '<option selected value="'.$rowUnit['unit_id'].'">'.$rowUnit['unit_name'].'</option>';
				else
					echo '<option value="'.$rowUnit['unit_id'].'">'.$rowUnit['unit_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('uom.php?action=new','unit','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no,directories=no, status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0" /></a><br/><br/>
			<select name="altUnitApplicable" id="altUnitApplicable" style="width:150px" onchange="get_alt_unit(this.value)" ><?php 
			if($alt_unit=="A"){
				echo '<option selected value="A">Applicable</option><option value="N">Not Applicable</option>';
			} elseif($alt_unit=="N"){
				echo '<option value="A">Applicable</option><option selected value="N">Not Applicable</option>';
			} ?>
			</select>
			</td>
		</tr>

		<?php 
		$alt_unit_text = "1 unit =";
		$alternate_unit_control = "";
		$one_unit_equal_control = "";
		if($alt_unit=="N"){
			$alternate_unit_control = "disabled";
			$one_unit_equal_control = "disabled";
		} else {
			$alt_unit_text = "1 ".$unit_name." =";
		}?>
		<tr class="Controls">
			<td class="th" nowrap>Alternate Unit:</td>
			<td><select name="alternateUnit" id="alternateUnit" style="width:150px" onchange="show_alt_unit(this)" <?php echo $alternate_unit_control;?> >
			<option value="0">-- Select --</option><?php 
			$alt_unit_name = "alternate unit";
			$sqlUnit2=mysql_query("SELECT * FROM unit ORDER BY unit_name");
			while($rowUnit2=mysql_fetch_array($sqlUnit2)){
				if($rowUnit2["unit_id"]==$alt_unit_id){
					$alt_unit_name = $rowUnit2['unit_name'];
					echo '<option selected value="'.$rowUnit2['unit_id'].'">'.$rowUnit2['unit_name'].'</option>';
				} else {
					echo '<option value="'.$rowUnit2['unit_id'].'">'.$rowUnit2['unit_name'].'</option>';
				}
			}?>
			</select></td>
			
			<td class="th" id="oneUE2" nowrap><?php echo $alt_unit_text;?></td>
			<td><input name="oneUnitEqual" id="oneUnitEqual" maxlength="8" size="20" value="<?php echo $alt_unit_num;?>"  <?php echo $one_unit_equal_control;?> >&nbsp;&nbsp;<span id="altUnit"><?php echo $alt_unit_name;?></span></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Water Requirement:</td>
			<td><input name="waterRequirement" id="waterRequirement" maxlength="8" size="20" value="<?php echo $water_require;?>" >&nbsp;&nbsp;Litre / Acre</td>
			
			<td class="th" nowrap>Recommended Dose:</td>
			<td><input name="recommendedDose" id="recommendedDose" maxlength="8" size="20" value="<?php echo $recomend_dose;?>" >&nbsp;&nbsp;<span id="rdUnit"><?php echo $unit_name." / Acre";?></span></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Maximum Dose:</td>
			<td><input name="maximumDose" id="maximumDose" maxlength="8" size="20" value="<?php echo $max_dose;?>" >&nbsp;&nbsp;<span id="maxdUnit"><?php echo $unit_name." / Acre";?></span></td>
			
			<td class="th" nowrap>Minimum Dose:</td>
			<td><input name="minimumDose" id="minimumDose" maxlength="8" size="20" value="<?php echo $min_dose;?>" >&nbsp;&nbsp;<span id="mindUnit"><?php echo $unit_name." / Acre";?></span></td>
		</tr>

		<tr class="Controls">
			<td class="th">Useful In:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/1.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="usefulIn1" id="usefulIn1" style="width:260px" ><option value="0">-- Select --</option><?php 
			$sql_use1=mysql_query("SELECT * FROM usability ORDER BY usability_name");
			while($row_use1=mysql_fetch_array($sql_use1)){
				if($row_use1["usability_id"]==$usability_id1)
					echo '<option selected value="'.$row_use1['usability_id'].'">'.$row_use1['usability_name'].'</option>';
				else
					echo '<option value="'.$row_use1['usability_id'].'">'.$row_use1['usability_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('usability.php?action=new','usefulin','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0" /></a></td>
			
			<?php $rp = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); ?>
			<td class="th">Requirement Period:&nbsp;<img src="images/1.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="rpFrom1" id="rpFrom1" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_from1==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;
			<select name="rpTo1" id="rpTo1" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_to1==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select></td>
		</tr>

		<tr class="Controls">
			<td class="th">Useful In:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/2.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="usefulIn2" id="usefulIn2" style="width:260px" ><option value="0">-- Select --</option><?php 
			$sql_use2=mysql_query("SELECT * FROM usability ORDER BY usability_name");
			while($row_use2=mysql_fetch_array($sql_use2)){
				if($row_use2["usability_id"]==$usability_id2)
					echo '<option selected value="'.$row_use2['usability_id'].'">'.$row_use2['usability_name'].'</option>';
				else
					echo '<option value="'.$row_use2['usability_id'].'">'.$row_use2['usability_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('usability.php?action=new','usefulin','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0" /></a></td>
			
			<td class="th">Requirement Period:&nbsp;<img src="images/2.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="rpFrom2" id="rpFrom2" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_from2==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;
			<select name="rpTo2" id="rpTo2" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_to2==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select></td>
		</tr>

		<tr class="Controls">
			<td class="th">Useful In:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/3.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="usefulIn3" id="usefulIn3" style="width:260px" ><option value="0">-- Select --</option><?php 
			$sql_use3=mysql_query("SELECT * FROM usability ORDER BY usability_name");
			while($row_use3=mysql_fetch_array($sql_use3)){
				if($row_use3["usability_id"]==$usability_id3)
					echo '<option selected value="'.$row_use3['usability_id'].'">'.$row_use3['usability_name'].'</option>';
				else
					echo '<option value="'.$row_use3['usability_id'].'">'.$row_use3['usability_name'].'</option>';
			}?>
			</select>&nbsp;&nbsp;<a onclick="window.open('usability.php?action=new','usefulin','width=900,height=650,resizable=no,scrollbars=yes,toolbar=no,location=no, directories=no,status=yes,menubar=no,copyhistory=no')"><img src="images/plus.gif" style="display:inline;cursor:hand;" border="0" /></a></td>
			
			<td class="th">Requirement Period:&nbsp;<img src="images/3.gif" style="display:inline; cursor:inherit; vertical-align:middle" border="0"/></td>
			<td><select name="rpFrom3" id="rpFrom3" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_from3==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;
			<select name="rpTo3" id="rpTo3" style="width:100px" ><option value="0">-- month --</option><?php 
			for ($i=1; $i<=12; $i++){
				if($rp_to3==$i)
					echo '<option selected value="'.$i.'">'.$rp[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$rp[$i-1].'</option>';
			}?>
			</select></td>
		</tr>

		<?php $am = array("Direct", "Drip", "Spray"); ?>
		<tr class="Controls">
			<td class="th">Application Method:</td>
			<td><select name="applicationMethod" id="applicationMethod" style="width:150px" ><option value="0">-- Select --</option><?php 
			for ($i=1; $i<=3; $i++){
				if($app_method==$i)
					echo '<option selected value="'.$i.'">'.$am[$i-1].'</option>';
				else
					echo '<option value="'.$i.'">'.$am[$i-1].'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>Re-order Level:</td>
			<td><input name="reorderLevel" id="reorderLevel" maxlength="5" size="20" value="<?php echo $reorder_level;?>" ></td>
		</tr>

		<tr class="Controls">
			<td colspan="2">&nbsp;</td>
			<td class="th" nowrap>Lead Time:</td>
			<td><input name="leadTime" id="leadTime" maxlength="3" size="20" value="<?php echo $lead_time;?>" >&nbsp;&nbsp;days</td>
		</tr>

		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>

 		<tr class="Bottom">
			<td colspan="4" style="text-align:right">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new" ><input type="hidden" name="submit" value="new"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update" ><input type="hidden" name="submit" value="update"/>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete" ><input type="hidden" name="submit" value="delete"/>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='item.php?action=new'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0"/></a>
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
			<td class="th"><strong>List of Item Data</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Row">
			<td colspan="6"><input name="searchText" id="searchText" value="<?php echo ($_REQUEST['action']=="search"?$_REQUEST['txt']:'');?>" size="20" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; height:12px; vertical-align:middle;"/>&nbsp;on&nbsp;<select name="searchFrom" id="searchFrom" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; width:100px; height:17px; vertical-align:middle;" ><?php 
			if($_REQUEST['action']=="search" && $_REQUEST['on']==1){
				echo '<option value="0">&nbsp;</option><option selected value="1">Group Name</option><option value="2">Item Name</option>';
			} elseif($_REQUEST['action']=="search" && $_REQUEST['on']==2){
				echo '<option value="0">&nbsp;</option><option value="1">Group Name</option><option selected value="2">Item Name</option>';
			} else {
				echo '<option selected value="0">&nbsp;</option><option value="1">Group Name</option><option value="2">Item Name</option>';
			}
			?>
	</select><input type="button" name="search" id="search" value="search" style=" font-size:9px; width:50px; height:20px; vertical-align: text-top" onclick="searching_data()" />
			</td>
		</tr>
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<?php if($c1==0){?>
				<th width="30%">Item Name&nbsp;&nbsp;<img src="&nbsp;" width="12" height="7" onclick="item_onclick(0)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="itemColumn" value="0" /></th>
			<?php } elseif($c1==1){?>
				<th width="30%">Item Name&nbsp;&nbsp;<img src="images/asc.gif" width="12" height="7" onclick="item_onclick(1)" style="display:inline;" border="0" /><input type="hidden" id="itemColumn" value="1" /></th>
			<?php } elseif($c1==2){?>
				<th width="30%">Item Name&nbsp;&nbsp;<img src="images/desc.gif" width="12" height="7" onclick="item_onclick(2)" style="display:inline;" border="0" /><input type="hidden" id="itemColumn" value="2" /></th>
			<?php }?>
			
			<?php if($c2==0){?>
				<th width="30%">Group Name&nbsp;&nbsp;<img src="&nbsp;" width="12" height="7" onclick="group_onclick(0)" style="display:inline;cursor:hand;" border="0" /><input type="hidden" id="groupColumn" value="0" /></th>
			<?php } elseif($c2==1){?>
				<th width="30%">Group Name&nbsp;&nbsp;<img src="images/asc.gif" width="12" height="7" onclick="group_onclick(1)" style="display:inline;" border="0" /><input type="hidden" id="groupColumn" value="1" /></th>
			<?php } elseif($c2==2){?>
				<th width="30%">Group Name&nbsp;&nbsp;<img src="images/desc.gif" width="12" height="7" onclick="group_onclick(2)" style="display:inline;" border="0" /><input type="hidden" id="groupColumn" value="2" /></th>
			<?php }?>
			<th width="15%">Unit</th>
			<th width="15%">Alt.Unit</th>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		if($_REQUEST['action']=="search"){
			$i = 0;
			if($_REQUEST['on']==1){
				$sql = "SELECT item.*,itgroup_name,unit_name FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id INNER JOIN unit ON item.unit_id=unit.unit_id WHERE itgroup_name LIKE '%".$_REQUEST['txt']."%' ";
			} elseif($_REQUEST['on']==2){
				$sql = "SELECT item.*,itgroup_name,unit_name FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id INNER JOIN unit ON item.unit_id=unit.unit_id WHERE item_name LIKE '%".$_REQUEST['txt']."%' ";
			}
			if($c1==0 && $c2==0){
				$sql .= "ORDER BY item_name asc,itgroup_name";
			} elseif($c1==1){
				$sql .= "ORDER BY item_name asc,itgroup_name ";
			} elseif($c1==2){
				$sql .= "ORDER BY item_name asc,itgroup_name ASC ";
			} elseif($c2==1){
				$sql .= "ORDER BY item_name asc,itgroup_name ";
			} elseif($c2==2){
				$sql .= "ORDER BY item_name asc,itgroup_name DESC ";
			}
		} else {
			$i = $start;
			$sql = "SELECT item.*,itgroup_name,unit_name FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id INNER JOIN unit ON item.unit_id=unit.unit_id ";
			if($c1==0 && $c2==0){
				$sql .= "ORDER BY item_name asc,itgroup_name ";
			} elseif($c1==1){
				$sql .= "ORDER BY item_name,itgroup_name ";
			} elseif($c1==2){
				$sql .= "ORDER BY item_name DESC,itgroup_name ASC ";
			} elseif($c2==1){
				$sql .= "ORDER BY item_name asc,itgroup_name";
			} elseif($c2==2){
				$sql .= "ORDER BY item_name asc,itgroup_name DESC ";
			}
			$sql .= "LIMIT ".$start.",".$end;
		}
		$sql_item = mysql_query($sql) or die(mysql_error());
		if(mysql_num_rows($sql_item)==0 && $_REQUEST['action']=="search"){
			echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold; color:#FF0000; height:25px;">';
			echo '<td style="text-align:center;" colspan="6">** No Data Found for the Selected Search **</td>';
			echo '</tr>';
		}
		
		while($row_item=mysql_fetch_array($sql_item))
		{
			$i++;
			echo '<tr class="Controls">';
			$delete_ref = "item.php?action=delete&iid=".$row_item['item_id'];
			$edit_ref = "item.php?action=edit&iid=".$row_item['item_id'];
			
			$altUnitName = "";
			$altUnit = "";
			if($row_item['alt_unit']=="A"){
				$sqlAlt = mysql_query("SELECT * FROM unit WHERE unit_id=".$row_item['alt_unit_id']);
				$rowAlt = mysql_fetch_assoc($sqlAlt);
				$altUnitName = $rowAlt['unit_name'];
				$altUnit = "(1 ".$row_item['unit_name']." = ".$row_item['alt_unit_num']." ".$altUnitName.")";
			}
			
			echo '<td>'.$i.'.</td><td>'.$row_item['item_name'].'</td><td>'.$row_item['itgroup_name'].'</td><td style="text-align:center;">'.$row_item['unit_name'].'</td><td  style="text-align:center;">'.$altUnitName.'<br/><span style="font-size:9px;color:#FF0000;">'.$altUnit.'</span>'.'</td>';
			echo '<td style="text-align:center;"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Bottom">
			<td colspan="6" style="text-align:center">
			<?php 
			if($_REQUEST['action']=="search"){
				if($_REQUEST['on']==1){
					$sql = "SELECT * FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id WHERE itgroup_name LIKE '%".$_REQUEST['txt']."%' order by item_name asc ";
				} elseif($_REQUEST['on']==2){
					$sql = "SELECT * FROM item INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id WHERE item_name LIKE '%".$_REQUEST['txt']."%' order by item_name asc ";
				}
			} else {
				$sql = "SELECT * FROM item order by item_name asc";
			}
			$sql_total = mysql_query($sql) or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			$strg = "&c1=".$c1."&c2=".$c2;?>
			Total <span style="color:red"><?php echo $tot_row;?></span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" name="show" id="show" value="Show:" onclick="paging_item('<?php echo $strg;?>')" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="<?php echo $end;?>" maxlength="2" size="2" /> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="xson" id="xson" value="<?php echo $_REQUEST["action"];?>" /><input type="hidden" name="itmid" id="itmid" value="<?php echo $iid;?>" /><?php 
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_item(\''.$strg.'\')" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_item()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_item()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_item()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_item()" />';
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
