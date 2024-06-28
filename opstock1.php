<?php 
include("menu.php");
/*--------------------------------*/
$lid = $_REQUEST['lid'];
$sql = mysql_query("SELECT location_name FROM location WHERE location_id=".$lid) or die(mysql_error());
$row = mysql_fetch_assoc($sql);
$locName = $row['location_name'];
//--------------------------------//
$data_found = "no";
if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){
	$sql = mysql_query("SELECT stock_register.*,unit_name FROM stock_register INNER JOIN unit ON stock_register.unit_id = unit.unit_id WHERE stock_id=".$_REQUEST['rid']);
	$row = mysql_fetch_assoc($sql);
	$data_found = "yes";
} elseif($_REQUEST['xn']=="N"){
	$sql = mysql_query("SELECT * FROM stock_register WHERE entry_mode='O+' AND location_id=".$lid);
	$row = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql)>0){$data_found = "yes";}
}
//--------------------------------//
if(isset($_POST['submit'])){
	$opDate = date("Y-m-d",strtotime($_POST['asonDate']));
	$opngQnty = ($_POST['opQnty']==""?0:$_POST['opQnty']);
	$opngRate = ($_POST['opRate']==""?0:$_POST['opRate']);
	$opngAmount = $opngQnty * $opngRate;
	$x = "opstock1.php?xn=N&lid=".$lid;
	if(isset($_REQUEST['pg'])){$x .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];}
	/*------------------------------*/
	$sqlSTK = mysql_query("SELECT * FROM stock_register WHERE entry_mode='O+' AND location_id=".$lid." AND item_id=".$_POST['itemName']) or die(mysql_error());
	$rowSTK = mysql_fetch_assoc($sqlSTK);
	$count = mysql_num_rows($sqlSTK);
	/*------------------------------*/
	if($_POST['submit']=="update"){
		if($count>0){
			if($rowSTK['stock_id']!=$_REQUEST['rid'])
				echo '<script language="javascript">alert("Duplication Error! can\'t update into stock register.");</script>';
			elseif($rowSTK['stock_id']==$_REQUEST['rid']){
				$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['itemName'].",item_qnty=".$opngQnty.",item_rate=".$opngRate.",item_amt=".$opngAmount.",unit_id=".$_POST['unitID']." WHERE stock_id=".$_REQUEST['rid']) or die(mysql_error());
				echo '<script language="javascript">window.location="'.$x.'";</script>';
			}
		} elseif($count==0){
			$res = mysql_query("UPDATE stock_register SET item_id=".$_POST['itemName'].",item_qnty=".$opngQnty.",item_rate=".$opngRate.",item_amt=".$opngAmount.",unit_id=".$_POST['unitID']." WHERE stock_id=".$_REQUEST['rid']) or die(mysql_error());
			echo '<script language="javascript">window.location="'.$x.'";</script>';
		}
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("DELETE FROM stock_register WHERE stock_id=".$_REQUEST['rid']) or die(mysql_error());
		echo '<script language="javascript">window.location="'.$x.'";</script>';
	} elseif($_POST['submit']=="new"){
		if($count>0)
			echo '<script language="javascript">alert("Duplication Error! can\'t insert into stock register.");</script>';
		else {
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$rid = $row["maxid"] + 1;
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_qnty,unit_id,item_rate,item_amt) VALUES(".$rid.",'O+',".$rid.",'".$opDate."',1,".$lid.",".$_POST['itemName'].",".$opngQnty.",".$_POST['unitID'].",".$opngRate.",".$opngAmount.")";
			$res = mysql_query($sql) or die(mysql_error());
			echo '<script language="javascript">window.location="'.$x.'";</script>';
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Opening Stock</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/calendar.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/calendar_eu.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script language="javascript" type="text/javascript">
function validate_opstock1()
{
	var err="";
	if(document.getElementById("asonDate").value!=""){
		if(!checkdate(document.opstock.asonDate)){return false;}
	} else {
		err = "* please input/select as on date for opening stock!\n";
	}
	if(document.getElementById("itemId").value==0)
		err += "* please select an item from the list!\n";
	if(document.getElementById("opQnty").value!="" && ! IsNumeric(document.getElementById("opQnty").value))
		err += "* please input valid numeric data for item quantity!\n";
	if(document.getElementById("opRate").value!="" && ! IsNumeric(document.getElementById("opRate").value))
		err += "* please input valid numeric data for item rate!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}

function show_amount()
{
	document.getElementById("opAmount").value = parseFloat(document.getElementById("opQnty").value) * parseFloat(document.getElementById("opRate").value);
}

function paging_item()
{
	window.location="opstock1.php?xn="+document.getElementById("action").value+"&lid="+document.getElementById("locid").value+"&pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_item()
{
	document.getElementById("page").value = 1;
	paging_item();
}

function previouspage_item()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_item();
}

function nextpage_item()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_item();
}

function lastpage_item()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_item();
}
</script>
</head>


<body>
<table align="center" cellspacing="0" cellpadding="0" height="260px" width="600px" border="0">
<tr>
	<td valign="top" colspan="2">
	<form name="opstock"  method="post" onsubmit="return validate_opstock1()">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
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
			<td class="th">Stock Location:</td>
			<td><input name="locationName" id="locationName" size="40" readonly="true" value="<?php echo $locName; ?>" style="background-color:#E7F0F8; color:#0000FF"><input type="hidden" name="location" id="location" value="<?php echo $lid; ?>" /></td>
			
			<td class="th" nowrap>As On Date:</td>
			<td><?php 
			if($data_found == "yes"){
				echo '<input name="asonDate" id="asonDate" maxlength="10" size="10" readonly="true" value="'.date("d-m-Y",strtotime($row['entry_date'])).'" style="background-color:#E7F0F8; color:#0000FF">';
			} elseif($data_found == "no"){
				echo '<input name="asonDate" id="asonDate" maxlength="10" size="10" value="'.date("d-m-Y").'">&nbsp;<script language="JavaScript">new tcal ({"formname": "opstock", "controlname": "asonDate"});</script>';
			}?>
			</td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Item Name:</td>
			<td><select name="itemName" id="itemName" style="width:250px;" onchange="get_unit_from_opstock(this.value)"><option value="0">-- Select --</option>
			<?php 
			$sql_item=mysql_query("SELECT * FROM item ORDER BY item_name");
			while($row_item=mysql_fetch_array($sql_item)){
				if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){
					if($row_item['item_id']==$row['item_id']){
						echo '<option selected value="'.$row_item['item_id'].'">'.$row_item['item_name'].'</option>';
					}
				}
				echo '<option value="'.$row_item['item_id'].'">'.$row_item['item_name'].'</option>';
			}?>
			</select></td>
			
			<td class="th" nowrap>Unit:</td>
			<td><input name="unitName" id="unitName" maxlength="15" size="15" readonly="true" value="<?php if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){echo $row["unit_name"];}?>" style="background-color:#E7F0F8; color:#0000FF"><input type="hidden" name="unitID" id="unitID" value="<?php if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){echo $row["unit_id"];}?>" /></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Op.Quantity:</td>
			<td><input name="opQnty" id="opQnty" maxlength="15" size="15" value="<?php if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){echo $row["item_qnty"];}?>"/></td>
			
			<td class="th" nowrap>Rate:</td>
			<td><input name="opRate" id="opRate" maxlength="15" size="15" value="<?php if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){echo $row["item_rate"];}?>"/></td>
		</tr>

		<tr class="Controls">
			<td class="th" nowrap>Op.Amount:</td>
			<td><input name="opAmount" id="opAmount" maxlength="15" size="15" readonly="true" value="<?php if($_REQUEST['xn']=="E" || $_REQUEST['xn']=="D"){echo $row["item_amt"];}?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;&nbsp;&nbsp;<input type="button" name="calculate" value="Calculate" onclick="show_amount()" /></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

 		<tr class="Bottom">
			<td colspan="4">
		<?php if($_REQUEST["xn"]=="N"){?>
			<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>&nbsp;&nbsp;<a href="javascript:document.opstock1.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php } elseif($_REQUEST["xn"]=="E"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='opstock1.php?xn=N&lid=<?php echo $lid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif($_REQUEST["xn"]=="D"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='opstock1.php?xn=N&lid=<?php echo $lid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='opstock.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
			<td class="th"><strong>List of Items</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>

		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="40%">Item Name</th>
			<th width="15%">Op.Qnty.</th>
			<th width="5%">Unit</th>
			<th width="15%">Rate</th>
			<th width="15%">Amount</th>
			<th width="5%">Action</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		$sql_stock = mysql_query("SELECT stock_register.*, item_name, unit_name FROM stock_register INNER JOIN item ON stock_register.item_id = item.item_id INNER JOIN unit ON stock_register.unit_id = unit.unit_id WHERE entry_mode='O+' AND location_id=".$lid." ORDER BY item_name LIMIT ".$start.",".$end) or die(mysql_error());
		while($row_stock=mysql_fetch_array($sql_stock)){
			$i++;
			echo '<tr class="Row">';
			$edit_ref = "opstock1.php?xn=E&lid=".$lid."&rid=".$row_stock['stock_id'];
			$delete_ref = "opstock1.php?xn=D&lid=".$lid."&rid=".$row_stock['stock_id'];
			if(isset($_REQUEST['pg'])){
				$edit_ref .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];
				$delete_ref .= "&pg=".$_REQUEST['pg']."&tr=".$_REQUEST['tr'];
			}
			
			echo '<td align="center" width="5%">'.$i.'.</td><td width="40%">'.$row_stock['item_name'].'</td><td align="right" width="15%">'.($row_stock['item_qnty']==0?"&nbsp;":$row_stock['item_qnty'].'</td><td width="5%">'.$row_stock['unit_name']).'</td><td align="right" width="15%">'.($row_stock['item_rate']==0?"&nbsp;":$row_stock['item_rate']).'</td><td align="right" width="15%">'.($row_stock['item_amt']==0?"&nbsp;":$row_stock['item_amt']).'</td><td align="center" width="5%"><a href="'.$edit_ref.'"><img src="images/edit.png" style="display:inline;cursor:hand;" border="0" /></a>&nbsp;&nbsp;<a href="'.$delete_ref.'"><img src="images/cancel.png" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="7" align="center">
			<?php 
			$sql_total = mysql_query("SELECT * FROM stock_register WHERE entry_mode='O+' AND location_id=".$lid) or die(mysql_error());
			$tot_row=mysql_num_rows($sql_total);
			$total_page=0;
			echo 'Total <span style="color:red">'.$tot_row.'</span> records&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_item()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="locid" id="locid" value="'.$lid.'" /><input type="hidden" name="action" id="action" value="'.$_REQUEST['xn'].'" />';
			if($tot_row>$end){
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_item()" style="vertical-align:middle">';
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
</body>
</html>