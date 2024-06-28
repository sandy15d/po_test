<?php 
include("menu.php");
//-------------------------------//
$msg = "";
$uaid = "";
$user_id = "";
$appr_auth = 0;
$email_id = "";
$initial = "";
$oi1 = $oi2 = $oi3 = $oi4 = 0; $ia1 = $ia2 = $ia3 = $ia4 = 0; $dc1 = $dc2 = $dc3 = $dc4 = 0; $po1 = $po2 = $po3 = $po4 = 0;
$mr1 = $mr2 = $mr3 = $mr4 = 0; $rr1 = $rr2 = $rr3 = $rr4 = 0; $cp1 = $cp2 = $cp3 = $cp4 = 0;
$pb1 = $pb2 = $pb3 = $pb4 = 0; $br1 = $br2 = $br3 = $br4 = 0;
$pay1 = $pay2 = $pay3 = $pay4 = 0;
$mi1 = $mi2 = $mi3 = $mi4 = 0; $ir1 = $ir2 = $ir3 = $ir4 = 0; $ilt1 = $ilt2 = $ilt3 = $ilt4 = 0;
$ipt1 = $ipt2 = $ipt3 = $ipt4 = 0; $xlt1 = $xlt2 = $xlt3 = $xlt4 = 0; $ps1 = $ps2 = $ps3 = $ps4 = 0;

if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){
	$uaid = $_REQUEST['uaid'];
	$sql = mysql_query("SELECT * FROM users WHERE uid=".$uaid);
	$row = mysql_fetch_assoc($sql);
	$user_id = $row['user_id'];
	$appr_auth = $row['appr_auth'];
	$email_id = $row['email_id'];
	$initial = $row['initial'];
	$oi1 = $row['oi1'];
	$oi2 = $row['oi2'];
	$oi3 = $row['oi3'];
	$oi4 = $row['oi4'];
	$po1 = $row['po1'];
	$po2 = $row['po2'];
	$po3 = $row['po3'];
	$po4 = $row['po4'];
	
	$ia1 = $row['ia1'];
	$ia2 = $row['ia2'];
	$ia3 = $row['ia3'];
	$ia4 = $row['ia4'];
	$dc1 = $row['dc1'];
	$dc2 = $row['dc2'];
	$dc3 = $row['dc3'];
	$dc4 = $row['dc4'];
	
	$mr1 = $row['mr1'];
	$mr2 = $row['mr2'];
	$mr3 = $row['mr3'];
	$mr4 = $row['mr4'];
	$rr1 = $row['rr1'];
	$rr2 = $row['rr2'];
	$rr3 = $row['rr3'];
	$rr4 = $row['rr4'];
	$pb1 = $row['pb1'];
	$pb2 = $row['pb2'];
	$pb3 = $row['pb3'];
	$pb4 = $row['pb4'];
	$br1 = $row['br1'];
	$br2 = $row['br2'];
	$br3 = $row['br3'];
	$br4 = $row['br4'];
	$cp1 = $row['cp1'];
	$cp2 = $row['cp2'];
	$cp3 = $row['cp3'];
	$cp4 = $row['cp4'];
	$pay1 = $row['pay1'];
	$pay2 = $row['pay2'];
	$pay3 = $row['pay3'];
	$pay4 = $row['pay4'];
	$mi1 = $row['mi1'];
	$mi2 = $row['mi2'];
	$mi3 = $row['mi3'];
	$mi4 = $row['mi4'];
	$ir1 = $row['ir1'];
	$ir2 = $row['ir2'];
	$ir3 = $row['ir3'];
	$ir4 = $row['ir4'];
	$ilt1 = $row['ilt1'];
	$ilt2 = $row['ilt2'];
	$ilt3 = $row['ilt3'];
	$ilt4 = $row['ilt4'];
	$ipt1 = $row['ipt1'];
	$ipt2 = $row['ipt2'];
	$ipt3 = $row['ipt3'];
	$ipt4 = $row['ipt4'];
	$xlt1 = $row['xlt1'];
	$xlt2 = $row['xlt2'];
	$xlt3 = $row['xlt3'];
	$xlt4 = $row['xlt4'];
	$ps1 = $row['ps1'];
	$ps2 = $row['ps2'];
	$ps3 = $row['ps3'];
	$ps4 = $row['ps4'];
}
//--------------------------------//
if(isset($_POST['submit'])){
	if($_POST['submit']=="update"){
		$res = mysql_query("UPDATE users SET appr_auth=".(isset($_POST['approvalAuthority'])?1:0).",initial='".$_POST['authorityInitial']."',email_id='".$_POST['emailID']."',oi1=".(isset($_POST['indentcheck_add'])?1:0).",oi2=".(isset($_POST['indentcheck_edit'])?1:0).",oi3=".(isset($_POST['indentcheck_delete'])?1:0).",oi4=".(isset($_POST['indentcheck_display'])?1:0).",ia1=".(isset($_POST['ia_add'])?1:0).",ia2=".(isset($_POST['ia_edit'])?1:0).",ia3=".(isset($_POST['ia_delete'])?1:0).",ia4=".(isset($_POST['ia_display'])?1:0).",dc1=".(isset($_POST['dc_add'])?1:0).",dc2=".(isset($_POST['dc_edit'])?1:0).",dc3=".(isset($_POST['dc_delete'])?1:0).",dc4=".(isset($_POST['dc_display'])?1:0).",po1=".(isset($_POST['pocheck_add'])?1:0).",po2=".(isset($_POST['pocheck_edit'])?1:0).",po3=".(isset($_POST['pocheck_delete'])?1:0).",po4=".(isset($_POST['pocheck_display'])?1:0).",mr1=".(isset($_POST['mrcheck_add'])?1:0).",mr2=".(isset($_POST['mrcheck_edit'])?1:0).",mr3=".(isset($_POST['mrcheck_delete'])?1:0).",mr4=".(isset($_POST['mrcheck_display'])?1:0).",rr1=".(isset($_POST['rrcheck_add'])?1:0).",rr2=".(isset($_POST['rrcheck_edit'])?1:0).",rr3=".(isset($_POST['rrcheck_delete'])?1:0).",rr4=".(isset($_POST['rrcheck_display'])?1:0).",pb1=".(isset($_POST['pbcheck_add'])?1:0).",pb2=".(isset($_POST['pbcheck_edit'])?1:0).",pb3=".(isset($_POST['pbcheck_delete'])?1:0).",pb4=".(isset($_POST['pbcheck_display'])?1:0).",br1=".(isset($_POST['brcheck_add'])?1:0).",br2=".(isset($_POST['brcheck_edit'])?1:0).",br3=".(isset($_POST['brcheck_delete'])?1:0).",br4=".(isset($_POST['brcheck_display'])?1:0).",cp1=".(isset($_POST['cashpurcheck_add'])?1:0).",cp2=".(isset($_POST['cashpurcheck_edit'])?1:0).",cp3=".(isset($_POST['cashpurcheck_delete'])?1:0).",cp4=".(isset($_POST['cashpurcheck_display'])?1:0).",pay1=".(isset($_POST['paycheck_add'])?1:0).",pay2=".(isset($_POST['paycheck_edit'])?1:0).",pay3=".(isset($_POST['paycheck_delete'])?1:0).",pay4=".(isset($_POST['paycheck_display'])?1:0).",mi1=".(isset($_POST['micheck_add'])?1:0).",mi2=".(isset($_POST['micheck_edit'])?1:0).",mi3=".(isset($_POST['micheck_delete'])?1:0).",mi4=".(isset($_POST['micheck_display'])?1:0).",ir1=".(isset($_POST['ircheck_add'])?1:0).",ir2=".(isset($_POST['ircheck_edit'])?1:0).",ir3=".(isset($_POST['ircheck_delete'])?1:0).",ir4=".(isset($_POST['ircheck_display'])?1:0).",ilt1=".(isset($_POST['iltcheck_add'])?1:0).",ilt2=".(isset($_POST['iltcheck_edit'])?1:0).",ilt3=".(isset($_POST['iltcheck_delete'])?1:0).",ilt4=".(isset($_POST['iltcheck_display'])?1:0).",ipt1=".(isset($_POST['iptcheck_add'])?1:0).",ipt2=".(isset($_POST['iptcheck_edit'])?1:0).",ipt3=".(isset($_POST['iptcheck_delete'])?1:0).",ipt4=".(isset($_POST['iptcheck_display'])?1:0).",xlt1=".(isset($_POST['xltcheck_add'])?1:0).",xlt2=".(isset($_POST['xltcheck_edit'])?1:0).",xlt3=".(isset($_POST['xltcheck_delete'])?1:0).",xlt4=".(isset($_POST['xltcheck_display'])?1:0).",ps1=".(isset($_POST['pscheck_add'])?1:0).",ps2=".(isset($_POST['pscheck_edit'])?1:0).",ps3=".(isset($_POST['pscheck_delete'])?1:0).",ps4=".(isset($_POST['pscheck_display'])?1:0).",aid=".$_SESSION['stores_uid']." WHERE uid=".$uaid) or die(mysql_error());
		echo '<script language="javascript">window.location="useraccess.php";</script>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function set_focus_on_init_mail(me)
{
	if(me.checked){
		document.getElementById('userinit').innerHTML = '<input name="authorityInitial" maxlength="5" size="5" value="" />';
		document.getElementById('usermail').innerHTML = '<input name="emailID" maxlength="50" size="45" value="" />';
	} else if(!me.checked){
		document.getElementById('userinit').innerHTML = '<input name="authorityInitial" maxlength="5" size="5" value="" readonly="true" style="background-color:#E7F0F8; color:#0000FF">';
		document.getElementById('usermail').innerHTML = '<input name="emailID" maxlength="50" size="45" value="" readonly="true" style="background-color:#E7F0F8; color:#0000FF"/>';
	}
}

function paging_user()
{
	window.location="useraccess.php?pg="+document.getElementById("page").value+"&tr="+document.getElementById("displayTotalRows").value;
}

function firstpage_user()
{
	document.getElementById("page").value = 1;
	paging_user();
}

function previouspage_user()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage>1){
		cpage = cpage - 1;
		document.getElementById("page").value = cpage;
	}
	paging_user();
}

function nextpage_user()
{
	var cpage = parseInt(document.getElementById("page").value);
	if(cpage<parseInt(document.getElementById("totalPage").value)){
		cpage = cpage + 1;
		document.getElementById("page").value = cpage;
	}
	paging_user();
}

function lastpage_user()
{
	document.getElementById("page").value = document.getElementById("totalPage").value;
	paging_user();
}
</script>
</head>


<body>
<center>
<table align="center" cellspacing="0" cellpadding="0" height="380px" width="675px" border="0">
<tr>
	<td valign="top">
	<form name="useraccess"  method="post">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>User Access</strong></td>
			<td class="HeaderRight"><img src="spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Record" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Controls">
			<th width="30%">User Name:</th>
			<th colspan="5" width="50%" style="color:#0033FF; font-weight:bold"><?php echo $user_id; ?></th>
			<th colspan="3" width="15%">Approval Authority:</th>
			<th width="5%" align="center" style="color:#0033FF; font-weight:bold">
			<?php 
			if($appr_auth==1)
				echo '<input type="checkbox" name="approvalAuthority" checked="checked" onclick="set_focus_on_init_mail(this)" /></th>';
			else
				echo '<input type="checkbox" name="approvalAuthority" onclick="set_focus_on_init_mail(this)" /></th>';
			?>
		</tr>
		<tr class="Controls">
			<th>Initial:</th>
			<th colspan="2"><span id="userinit"><?php 
			if($appr_auth==1)
				echo '<input name="authorityInitial" maxlength="5" size="5" value="'.$initial.'" />';
			else 
				echo '<input name="authorityInitial" maxlength="5" size="5" value="'.$initial.'" readonly="true" style="background-color:#E7F0F8; color:#0000FF"/>';
			?> </span></th>
			<th colspan="2">Email-Id:</th>
			<th colspan="5"><span id="usermail"><?php 
			if($appr_auth==1)
				echo '<input name="emailID" maxlength="50" size="45" value="'.$email_id.'" />';
			else
				echo '<input name="emailID" maxlength="50" size="45" value="'.$email_id.'" readonly="true" style="background-color:#E7F0F8; color:#0000FF"/>';
			?> </span></th>
		</tr>
		
		<tr class="Caption">
			<th width="30%">Input Pages</th>
			<th width="5%">Add</th>
			<th width="5%">Change</th>
			<th width="5%">Delete</th>
			<th width="5%">View</th>
			<th width="30%">Input Pages</th>
			<th width="5%">Add</th>
			<th width="5%">Change</th>
			<th width="5%">Delete</th>
			<th width="5%">View</th>
		</tr>
		
		<tr class="Controls">
			<td class="th" width="30%" nowrap>Order Indent:</td>
			<?php 
			if($oi1==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_add" /></td>';
			if($oi2==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_edit" /></td>';
			if($oi3==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_delete" /></td>';
			if($oi4==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="indentcheck_display" /></td>';
			?>
			
			<td class="th" width="30%" nowrap>Payment:</td>
			<?php 
			if($pay1==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_add" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_add" /></td>';
			if($pay2==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_edit" /></td>';
			if($pay3==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_delete" /></td>';
			if($pay4==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_display" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="paycheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
			<td class="th" nowrap>Indent Approval:</td>
			<?php 
			if($ia1==1)
				echo '<td align="center"><input type="checkbox" name="ia_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ia_add" /></td>';
			if($ia2==1)
				echo '<td align="center"><input type="checkbox" name="ia_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ia_edit" /></td>';
			if($ia3==1)
				echo '<td align="center"><input type="checkbox" name="ia_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ia_delete" /></td>';
			if($ia4==1)
				echo '<td align="center"><input type="checkbox" name="ia_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ia_display" /></td>';
			?>
			
			<td class="th" nowrap>Material Issue:</td>
			<?php 
			if($mi1==1)
				echo '<td align="center"><input type="checkbox" name="micheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="micheck_add" /></td>';
			if($mi2==1)
				echo '<td align="center"><input type="checkbox" name="micheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="micheck_edit" /></td>';
			if($mi3==1)
				echo '<td align="center"><input type="checkbox" name="micheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="micheck_delete" /></td>';
			if($mi4==1)
				echo '<td align="center"><input type="checkbox" name="micheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="micheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
		    <td class="th" nowrap>Delivery Confirmation:</td>
			<?php 
			if($dc1==1)
				echo '<td align="center"><input type="checkbox" name="dc_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="dc_add" /></td>';
			if($dc2==1)
				echo '<td align="center"><input type="checkbox" name="dc_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="dc_edit" /></td>';
			if($dc3==1)
				echo '<td align="center"><input type="checkbox" name="dc_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="dc_delete" /></td>';
			if($dc4==1)
				echo '<td align="center"><input type="checkbox" name="dc_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="dc_display" /></td>';
			?>
			
			
			<td class="th" nowrap>Issue Return:</td>
			<?php 
			if($ir1==1)
				echo '<td align="center"><input type="checkbox" name="ircheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ircheck_add" /></td>';
			if($ir2==1)
				echo '<td align="center"><input type="checkbox" name="ircheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ircheck_edit" /></td>';
			if($ir3==1)
				echo '<td align="center"><input type="checkbox" name="ircheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ircheck_delete" /></td>';
			if($ir4==1)
				echo '<td align="center"><input type="checkbox" name="ircheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="ircheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
		    <td class="th" nowrap>Purchase Order:</td>
			<?php 
			if($po1==1)
				echo '<td align="center"><input type="checkbox" name="pocheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pocheck_add" /></td>';
			if($po2==1)
				echo '<td align="center"><input type="checkbox" name="pocheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pocheck_edit" /></td>';
			if($po3==1)
				echo '<td align="center"><input type="checkbox" name="pocheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pocheck_delete" /></td>';
			if($po4==1)
				echo '<td align="center"><input type="checkbox" name="pocheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pocheck_display" /></td>';
			?>
		    
			
			
			<td class="th" nowrap>Inter Location Transfer:</td>
			<?php 
			if($ilt1==1)
				echo '<td align="center"><input type="checkbox" name="iltcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iltcheck_add" /></td>';
			if($ilt2==1)
				echo '<td align="center"><input type="checkbox" name="iltcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iltcheck_edit" /></td>';
			if($ilt3==1)
				echo '<td align="center"><input type="checkbox" name="iltcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iltcheck_delete" /></td>';
			if($ilt4==1)
				echo '<td align="center"><input type="checkbox" name="iltcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iltcheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
		    <td class="th" nowrap>Material Receipt:</td>
			<?php 
			if($mr1==1)
				echo '<td align="center"><input type="checkbox" name="mrcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="mrcheck_add" /></td>';
			if($mr2==1)
				echo '<td align="center"><input type="checkbox" name="mrcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="mrcheck_edit" /></td>';
			if($mr3==1)
				echo '<td align="center"><input type="checkbox" name="mrcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="mrcheck_delete" /></td>';
			if($mr4==1)
				echo '<td align="center"><input type="checkbox" name="mrcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="mrcheck_display" /></td>';
			?>
		    
			
			
			<td class="th" nowrap>Inter Plot Transfer:</td>
			<?php 
			if($ipt1==1)
				echo '<td align="center"><input type="checkbox" name="iptcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iptcheck_add" /></td>';
			if($ipt2==1)
				echo '<td align="center"><input type="checkbox" name="iptcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iptcheck_edit" /></td>';
			if($ipt3==1)
				echo '<td align="center"><input type="checkbox" name="iptcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iptcheck_delete" /></td>';
			if($ipt4==1)
				echo '<td align="center"><input type="checkbox" name="iptcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="iptcheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
		    <td class="th" nowrap>Receipt Return:</td>
			<?php 
			if($rr1==1)
				echo '<td align="center"><input type="checkbox" name="rrcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="rrcheck_add" /></td>';
			if($rr2==1)
				echo '<td align="center"><input type="checkbox" name="rrcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="rrcheck_edit" /></td>';
			if($rr3==1)
				echo '<td align="center"><input type="checkbox" name="rrcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="rrcheck_delete" /></td>';
			if($rr4==1)
				echo '<td align="center"><input type="checkbox" name="rrcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="rrcheck_display" /></td>';
			?>
		    
			
			
			<td class="th" nowrap>External Location Transfer:</td>
			<?php 
			if($xlt1==1)
				echo '<td align="center"><input type="checkbox" name="xltcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="xltcheck_add" /></td>';
			if($xlt2==1)
				echo '<td align="center"><input type="checkbox" name="xltcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="xltcheck_edit" /></td>';
			if($xlt3==1)
				echo '<td align="center"><input type="checkbox" name="xltcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="xltcheck_delete" /></td>';
			if($xlt4==1)
				echo '<td align="center"><input type="checkbox" name="xltcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="xltcheck_display" /></td>';
			?>
		</tr>
		
		<tr class="Controls">
		   <td class="th" nowrap>Cash Purchase:</td>
			<?php 
			if($cp1==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_add" /></td>';
			if($cp2==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_edit" /></td>';
			if($cp3==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_delete" /></td>';
			if($cp4==1)
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center" width="5%"><input type="checkbox" name="cashpurcheck_display" /></td>';
			?>

			<td class="th" nowrap>Physical Stock:</td>
			<?php 
			if($ps1==1)
				echo '<td align="center"><input type="checkbox" name="pscheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pscheck_add" /></td>';
			if($ps2==1)
				echo '<td align="center"><input type="checkbox" name="pscheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pscheck_edit" /></td>';
			if($ps3==1)
				echo '<td align="center"><input type="checkbox" name="pscheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pscheck_delete" /></td>';
			if($ps4==1)
				echo '<td align="center"><input type="checkbox" name="pscheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pscheck_display" /></td>';
			?>
		</tr>
		<tr class="Controls">
		  <td class="th" nowrap>Purchase Bill:</td>
			<?php 
			if($pb1==1)
				echo '<td align="center"><input type="checkbox" name="pbcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pbcheck_add" /></td>';
			if($pb2==1)
				echo '<td align="center"><input type="checkbox" name="pbcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pbcheck_edit" /></td>';
			if($pb3==1)
				echo '<td align="center"><input type="checkbox" name="pbcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pbcheck_delete" /></td>';
			if($pb4==1)
				echo '<td align="center"><input type="checkbox" name="pbcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="pbcheck_display" /></td>';
			?>
		</tr>
		<tr class="Controls">
		 <td class="th" nowrap>Bill Return:</td>
			<?php 
			if($br1==1)
				echo '<td align="center"><input type="checkbox" name="brcheck_add" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="brcheck_add" /></td>';
			if($br2==1)
				echo '<td align="center"><input type="checkbox" name="brcheck_edit" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="brcheck_edit" /></td>';
			if($br3==1)
				echo '<td align="center"><input type="checkbox" name="brcheck_delete" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="brcheck_delete" /></td>';
			if($br4==1)
				echo '<td align="center"><input type="checkbox" name="brcheck_display" checked="checked" /></td>';
			else
				echo '<td align="center"><input type="checkbox" name="brcheck_display" /></td>';
			?>
		 
		
		</tr>
		
		<?php if($msg!=""){?>
		<tr class="Controls">
			<td colspan="10" align="center" style="color:#FF0000; font-weight:bold"><?php echo $msg; ?></td>
		</tr>
		<?php } ?>
		
 		<tr class="Bottom">
			<td align="left" colspan="10">
		<?php 
		if(isset($_REQUEST["action"]) && $_REQUEST["action"]=="edit"){?>
		<input type="image" name="submit" src="images/update.gif" width="82" height="22" alt="update"><input type="hidden" name="submit" value="update"/>
&nbsp;&nbsp;<a href="javascript:window.location='useraccess.php'" ><img src="images/reset.gif" width="72" height="22" style="display:inline; cursor:hand;" border="0" /></a>
		<?php }?>
		&nbsp;&nbsp;<a href="javascript:window.location='menu.php'" ><img src="images/back.gif" width="72" height="22" style="display:inline;cursor:hand;" border="0" /></a>
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
	<td valign="top">
	<table align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
		<table class="Header" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td class="HeaderLeft"><img src="spacer.gif" border="0" alt=""></td>
			<td class="th"><strong>Users - [ List ]</strong></td>
			<td class="HeaderRight"><img src="spacer.gif" border="0" alt=""></td>
		</tr>
		</table>
		
		<table class="Grid" width="100%" cellspacing="0" cellpadding="0">
		<tr class="Caption">
			<th width="5%">Sl.No.</th>
			<th width="30%">User Name</th>
			<th width="15%">Type</th>
			<th width="10%">Status</th>
			<th width="40%">Location</th>
		</tr>
		
		<?php 
		$start = 0;
		$pg = 1;
		if(isset($_REQUEST['tr']) && $_REQUEST['tr']!=""){$end=$_REQUEST['tr'];} else {$end=PAGING;}
		if(isset($_REQUEST['pg']) && $_REQUEST['pg']!=""){$pg = $_REQUEST['pg']; $start=($pg-1)*$end;}
		
		$i = $start;
		if($_SESSION['stores_utype']=="A")
			$sql = mysql_query("SELECT users.*,location_name FROM users INNER JOIN location ON users.location_id = location.location_id WHERE user_type='U' ORDER BY location_name, user_id limit ".$start.",".$end) or die(mysql_error());
		elseif($_SESSION['stores_utype']=="S")
			$sql = mysql_query("SELECT users.*,location_name FROM users INNER JOIN location ON users.location_id = location.location_id ORDER BY location_name, user_id limit ".$start.",".$end) or die(mysql_error());
		while($row=mysql_fetch_array($sql))
		{
			$i++;
			echo '<tr class="Row">';
			$sel_ref = "useraccess.php?action=edit&uaid=".$row['uid'];
			
			if($row['user_type']=="S")
				$userType = "Super User";
			elseif($row['user_type']=="A")
				$userType = "Admin";
			elseif($row['user_type']=="U")
				$userType = "User";
			
			if($row['user_status']=="A")
				$userStatus = "Active";
			elseif($row['user_status']=="D")
				$userStatus = "Deactive";
			
			echo '<td align="center">'.$i.'.</td><td align="center"><a href="'.$sel_ref.'" style="font-size:12px;">'.strtoupper($row['user_id']).'</a></td><td align="center">'.$userType.'</td><td>'.$userStatus.'</td><td>'.$row['location_name'].'</td>';
			echo '</tr>';
		} ?>
		
		<tr class="Footer">
			<td colspan="5" align="center">
			<?php 
			echo '<input type="button" name="show" id="show" value="Show:" onclick="paging_user()" />&nbsp;&nbsp;<input name="displayTotalRows" id="displayTotalRows" value="'.$end.'" maxlength="2" size="2"/> rows&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			if($_SESSION['stores_utype']=="A")
				$sql = mysql_query("SELECT * FROM users WHERE user_type='U'") or die(mysql_error());
			elseif($_SESSION['stores_utype']=="S")
				$sql = mysql_query("SELECT * FROM users") or die(mysql_error());
			$tot_row=mysql_num_rows($sql);
			$total_page=0;
			if($tot_row>$end)
			{
				echo "Page number: ";
				$total_page=ceil($tot_row/$end);
				echo '<select name="page" id="page" onchange="paging_user()" style="vertical-align:middle">';
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
				echo '<input type="button" name="fistPage" id="firstPage" value=" << " onclick="firstpage_user()" />&nbsp;&nbsp;<input type="button" name="prevPage" id="prevPage" value=" < " onclick="previouspage_user()" />&nbsp;&nbsp;';
			if($total_page>1 && $pg<$total_page)
				echo '<input type="button" name="nextPage" id="nextPage" value=" > " onclick="nextpage_user()" />&nbsp;&nbsp;<input type="button" name="lastPage" id="lastPage" value=" >> " onclick="lastpage_user()" />';
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