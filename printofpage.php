<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*-------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Purchase Order</title>
<Link rel='alternate' media='print' href=null>
<script type="text/javascript" language="javascript">
function setPrintPage()
{
	if(document.getElementById('select1').checked){
		if(document.getElementById('vtype').value=="ilt"){
			var prnThis = "iltprint.php?v="+document.getElementById('vid').value;
		} else if(document.getElementById('vtype').value=="xlt"){
			var prnThis = "xltprint.php?v="+document.getElementById('vid').value;
		}
		var prnDoc = document.getElementsByTagName('Link');
		//prnDoc[0].setAttribute('href', prnThis);
		//window.parent.print();
		window.location.href=prnThis;
	} else if(document.getElementById('select2').checked){
		//do nothing and close the window
	}
	//window.close();
}
</script>
</head>

<body>
<form name="printpage" method="post">
<fieldset><legend><b>Voucher Printing Confirmation</b></legend>
<table border="0" width="100%">
<tr><td>&nbsp;<input type="hidden" name="vid" id="vid" value="<?php echo $_REQUEST['vid'];?>" /><input type="hidden" name="vtype" id="vtype" value="<?php echo $_REQUEST['typ'];?>" /></td>
</tr>
<tr>
	<td>Is your printer ready to print ?</td>
</tr>
<tr>
	<td><input type="radio" name="selectR" id="select1" value="1" checked/>&nbsp;YES</td>
	<td><input type="radio" name="selectR" id="select2" value="2"/>&nbsp;NO</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input type="button" name="submit" id="submit" value="Submit" onclick="setPrintPage()" />&nbsp;&nbsp;<input type="button" name="cancel" id="cancel" value="Cancel" onclick="window.close()" /></td>
</tr>
</table>
</fieldset>
</form>
</body>
</html>