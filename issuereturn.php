<?php 
include("menu.php");
/*-----------------------------------*/
$sql_user = mysql_query("SELECT ir1,ir2,ir3,ir4 FROM users WHERE uid=".$_SESSION['stores_uid']) or die(mysql_error());
$row_user = mysql_fetch_assoc($sql_user);
/*-----------------------------------*/
$msg = "";
$mid = $_REQUEST['mid'];
$sql1 = mysql_query("SELECT tblissue1.*,location_name,staff_name FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE issue_id=".$mid);
$row1 = mysql_fetch_assoc($sql1);
$location_id = $row1['location_id'];
$issue_number = ($row1['issue_no']>999 ? $row1['issue_no'] : ($row1['issue_no']>99 && $row1['issue_no']<1000 ? "0".$row1['issue_no'] : ($row1['issue_no']>9 && $row1['issue_no']<100 ? "00".$row1['issue_no'] : "000".$row1['issue_no'])));
if($row1['issue_prefix']!=null){$issue_number = $row1['issue_prefix']."/".$issue_number;}
$issue_date = date("d-m-Y",strtotime($row1["issue_date"]));
$location_name = $row1['location_name'];
$staff_name = $row1['staff_name'];
$issue_to = $row1['issue_to'];
/*-----------------------------------*/
$rid = 0;
$item_id = 0;
$item_name = "";
$issue_qnty = "";
$issue_unit = "";
$return_qnty = "";
$return_unit = "";
$plot_name = "";
$clqnty_prime = 0;
$clqnty_alt = 0;
$prime_unit_id = 0;
$alt_unit_id = 0;
$alt_unit_num = 0;
$alt_unit = "";
$prime_unit_name = "";
$alt_unit_name = "";
if(isset($_REQUEST['rid'])){
	$rid = $_REQUEST['rid'];
	if($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete"){
		$sql2=mysql_query("SELECT tblissue2.*, item_name, unit_name, plot_name FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE rec_id=".$rid) or die(mysql_error());
		$row2 = mysql_fetch_assoc($sql2);
		$item_id = $row2["item_id"];
		$item_name = $row2["item_name"];
		$issue_qnty = $row2["issue_qnty"];
		$issue_unit = $row2['unit_name'];
		$plot_name = $row2["plot_name"];
		$return_qnty = $row2["return_qnty"];
		/*----------------------------------------*/
		$sql3=mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row2['return_unit']) or die(mysql_error());
		$row3 = mysql_fetch_assoc($sql3);
		$return_unit = $row2['unit_name'];
		/*----------------------------------------*/
		$sql4=mysql_query("SELECT item.unit_id AS prime_unit_id, unit_name AS prime_unit_name, alt_unit, alt_unit_id, alt_unit_num FROM item INNER JOIN unit ON item.unit_id = unit.unit_id  WHERE item_id=".$row2['item_id']);
		$row4=mysql_fetch_assoc($sql4);
		$prime_unit_id = $row4['prime_unit_id'];
		$prime_unit_name = $row4['prime_unit_name'];
		$alt_unit = $row4['alt_unit'];
		$alt_unit_id = $row4['alt_unit_id'];
		$alt_unit_num = $row4['alt_unit_num'];
		
		if($alt_unit=="A" && $alt_unit_id!="0"){
			$sql5=mysql_query("SELECT unit_name AS alt_unit_name FROM unit WHERE unit_id=".$alt_unit_id);
			$row5=mysql_fetch_assoc($sql5);
			$alt_unit_name = $row5['alt_unit_name'];
		}
		/*----------------------------------------*/
		$sql_stk_rgstr = mysql_query("SELECT Sum(item_qnty) AS qty, unit_id FROM stock_register WHERE item_id=".$item_id." AND location_id=".$location_id." AND entry_date<='".date("Y-m-d",strtotime($issue_date))."' GROUP BY unit_id") or die(mysql_error());
		while($row_stk_rgstr=mysql_fetch_array($sql_stk_rgstr)){
			if($row_stk_rgstr['unit_id']==$prime_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'];
				$clqnty_alt += $row_stk_rgstr['qty'] * $alt_unit_num;
			} elseif($row_stk_rgstr['unit_id']==$alt_unit_id){
				$clqnty_prime += $row_stk_rgstr['qty'] / $alt_unit_num;
				$clqnty_alt += $row_stk_rgstr['qty'];
			}
		}
	}
}
/*-----------------------------------*/
if(isset($_POST['submit'])){
	$dateIssue=$row1["issue_date"];
	$itemname=$row2["item_name"];
	$particulars = $row1['location_name']."/".$row2["plot_name"];
	$voucherid = ($mid>999 ? $mid : ($mid>99 && $mid<1000 ? "0".$mid : ($mid>9 && $mid<100 ? "00".$mid : "000".$mid)));
	/*-----------------------------------*/
	if($row4['prime_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['returnQnty'];
	} elseif($row4['alt_unit_id']==$_POST['unit']){
		$unitid = $row4['prime_unit_id'];
		$itemQnty = $_POST['returnQnty'] / $row4['alt_unit_num'];
	}
	$sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$_POST['unit']);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-----------------------------------*/
	$sql = mysql_query("SELECT * FROM stock_register WHERE entry_mode='I-' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	/*-----------------------------------*/
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE tblissue2 SET return_qnty=".$_POST['returnQnty'].",return_unit=".$_POST['unit']." WHERE rec_id=".$rid) or die(mysql_error());
		if($count==0){
			$sql = mysql_query("SELECT Max(stock_id) as maxid FROM stock_register");
			$row = mysql_fetch_assoc($sql);
			$sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,unit_id,item_qnty,item_rate) VALUES(".$sid.",'I-',".$mid.",'".$dateIssue."',".$row2['seq_no'].",".$row1['location_id'].",".$row2['item_id'].",".$unitid.",".$itemQnty.",0)";
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','IssueRtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		} elseif($count>0){
			$res = mysql_query("UPDATE stock_register SET item_id=".$row2['item_id'].",unit_id=".$unitid.",item_qnty=".$itemQnty." WHERE entry_mode='I-' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','IssueRtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
		echo '<script language="javascript">window.location="issuereturn.php?action=new&mid='.$mid.'";</script>';
	} elseif($_POST['submit']=="delete"){
		$res = mysql_query("UPDATE tblissue2 SET return_qnty=0, return_unit=0 WHERE rec_id=".$rid) or die(mysql_error());
		if($count>0){
			$res = mysql_query("DELETE FROM stock_register WHERE entry_mode='I-' AND entry_id=".$mid." AND entry_date='".$dateIssue."' AND seq_no=".$row2['seq_no']." AND location_id=".$row1['location_id']) or die(mysql_error());
		}
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIssue."','IssueRtrn.','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['returnQnty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		echo '<script language="javascript">window.location="issuereturn.php?action=new&mid='.$mid.'";</script>';
	}
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
function validate_issue_return_item()
{
	var err="";
	if(document.getElementById("itemName").value=="")
		err = "* please select item, which is to be returned!\n";
	if(document.getElementById("returnQnty").value!="" && ! IsNumeric(document.getElementById("returnQnty").value))
		err += "* please input valid (numeric only) return quantity!\n";
	if(parseFloat(document.getElementById("returnQnty").value) == 0)
		err += "* returned quantity required!\n";
	if(parseFloat(document.getElementById("returnQnty").value) > parseFloat(document.getElementById("issueQnty").value))
		err += "* returned quantity excess than issue quantity!\n";
	if(err=="")
		return true;
	else {
		alert("Error: \n"+err);
		return false;
	}
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="280px" width="675px" border="0">
<tr>
	<td valign="top" colspan="3">
	<table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Issue Return - [ Main ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<td class="th" nowrap>Issue No.:</td>
			<td><input name="issueNo" id="issueNo" maxlength="15" size="20" readonly="true" value="<?php echo $issue_number; ?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th" nowrap>Issue Date:</td>
			<td><input name="issueDate" id="issueDate" maxlength="10" size="10" readonly="true" value="<?php echo $issue_date;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th">Location:</td>
			<td><input name="locationName" id="locationName" maxlength="50" size="45" readonly="true" value="<?php echo $location_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td class="th">Issued By:</td>
			<td><input name="issueBy" id="issueBy" maxlength="50" size="45" readonly="true" value="<?php echo $staff_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Issued To:</td>
			<td><input name="issueTo" id="issueTo" maxlength="50" size="45" readonly="true" value="<?php echo $issue_to;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" colspan="3">
	<form name="issuereturn"  method="post" onsubmit="return validate_issue_return_item()">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Grid" cellspacing="0" cellpadding="0" width="100%">
		<tr class="Controls">
			<td class="th" nowrap>Item Name:</td>
			<td><input name="itemName" id="itemName" maxlength="50" size="45" readonly="true" value="<?php echo $item_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
			
			<th class="th" nowrap>Stock On Date:</th>
			<td><input name="itemStock" id="itemStock" maxlength="10" size="10" readonly="true" value="<?php echo number_format($clqnty_prime,3,".","");?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit1"><?php echo $prime_unit_name; if($alt_unit=="A"){echo '<br><span style="font-size: 10px;">('.number_format($clqnty_alt,3,".","")." ".$alt_unit_name.')</span>';} else {echo "";}?></span></td>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Issue Qnty.:</td>
			<td><input name="issueQnty" id="issueQnty" maxlength="10" size="10" readonly="true" value="<?php echo $issue_qnty;?>" style="background-color:#E7F0F8; color:#0000FF">&nbsp;<span id="spanUnit2"><?php echo $issue_unit;?></span></td>
			
			<th class="th" nowrap>Plot No.:</th>
			<td><input name="plotName" id="plotName" maxlength="50" size="30" readonly="true" value="<?php echo $plot_name;?>" style="background-color:#E7F0F8; color:#0000FF"></td>
		</tr>
		
		<tr class="Controls">
			<th class="th" nowrap>Return Qnty.:</th>
			<td><input name="returnQnty" id="returnQnty" maxlength="10" size="10" value="<?php echo $return_qnty;?>" ></td>
			
			<td class="th" nowrap>Unit :</td>
			<td><?php if(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="edit" || $_REQUEST["action"]=="delete")){
				if(($alt_unit=="N") || ($alt_unit=="A" && $alt_unit_id==0)){
					echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option></select>';
				} elseif($alt_unit=="A" && $alt_unit_id!=0){
					if($issue_unit==$prime_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option selected value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					} elseif($issue_unit==$alt_unit_id){
						echo '<select name="unit" id="unit" style="width:115px"><option value="'.$prime_unit_id.'">'.$prime_unit_name.'</option><option selected value="'.$alt_unit_id.'">'.$alt_unit_name.'</option></select>';
					}
				}
			} else {
				echo '<select name="unit" id="unit" style="width:115px"><option value="0">&nbsp;</option></select>';
			}?></td>
		</tr>
		
		<?php if($msg!=""){echo '<tr class="Controls"><td colspan="4" align="center" style="color:#FF0000; font-weight:bold">'.$msg.'</td></tr>';} ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="4">
		<?php if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="new"){
			if($row_user['ir1']==1){?>
				<input type="image" name="submit" src="images/add.gif" width="72" height="22" alt="new"><input type="hidden" name="submit" value="new"/>
			<?php } elseif($row_user['ir1']==0){?>
				<input type="image" name="submit" src="images/add.gif" style="visibility:hidden" width="72" height="22" alt="new">
			<?php }?>
&nbsp;&nbsp;<a href="javascript:document.issuereturn.reset()"><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
			<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>&nbsp;&nbsp;<a href="javascript:window.location='issuereturn.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php } elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=="delete"){?>
			<input type="image" name="submit" src="images/delete.gif" width="72" height="22" alt="delete"><input type="hidden" name="submit" value="delete"/>&nbsp;&nbsp;<a href="javascript:window.location='issuereturn.php?action=new&mid=<?php echo $mid;?>'" ><img src="images/reset.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
		<?php }?>
&nbsp;&nbsp;<a href="javascript:window.location='miselection.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<td valign="top" colspan="3">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td class="HeaderLeft"><img src="images/spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Material Issue Return - [ Item List ]</strong></td>
			<td class="HeaderRight"><img src="images/spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="25%">Item Name</th>
			<th width="20%">Issue Qnty.</th>
			<th width="20%">Plot No.</th>
			<th width="20%">Return Qnty.</th>
			<th width="5%">Edit</th>
			<th width="5%">Del</th>
		</tr>
		
		<?php 
		$i = 0;
		$sql_issue = mysql_query("SELECT tblissue2.*, item_name, unit_name, plot_name FROM tblissue2 INNER JOIN item ON tblissue2.item_id = item.item_id INNER JOIN unit ON tblissue2.issue_unit = unit.unit_id INNER JOIN plot ON tblissue2.plot_id = plot.plot_id WHERE issue_id=".$mid." ORDER BY seq_no") or die(mysql_error());
		while($row_issue=mysql_fetch_array($sql_issue))
		{
			$sql_return=mysql_query("SELECT unit_name FROM unit WHERE unit_id=".$row_issue['return_unit']) or die(mysql_error());
			$row_return = mysql_fetch_assoc($sql_return);
			
			$i++;
			echo '<tr class="Row">';
			$edit_ref = "issuereturn.php?action=edit&mid=".$mid."&rid=".$row_issue['rec_id'];
			$delete_ref = "issuereturn.php?action=delete&mid=".$mid."&rid=".$row_issue['rec_id'];
			
			echo '<td align="center">'.$i.'.</td><td>'.$row_issue['item_name'].'</td><td align="center">'.$row_issue['issue_qnty']." ".$row_issue['unit_name'].'</td><td>'.$row_issue['plot_name'].'</td><td align="center">'.$row_issue['return_qnty']." ".$row_return['unit_name'].'</td>';
			if($row_user['ir2']==1)
				echo '<td align="center"><a href="'.$edit_ref.'"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ir2']==0)
				echo '<td align="center"><a href="'.$edit_ref.'" style="visibility:hidden"><img src="images/edit.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			if($row_user['ir3']==1)
				echo '<td align="center"><a href="'.$delete_ref.'"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			elseif($row_user['ir3']==0)
				echo '<td align="center"><a href="'.$delete_ref.'" style="visibility:hidden"><img src="images/cancel.gif" style="display:inline;cursor:hand;" border="0" /></a></td>';
			echo '</tr>';
		} ?>
		
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