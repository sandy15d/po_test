<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$dateIndent=substr($_POST['idt'],6,4)."-".substr($_POST['idt'],3,2)."-".substr($_POST['idt'],0,2);
	$dateSupply=substr($_POST['sdt'],6,4)."-".substr($_POST['sdt'],3,2)."-".substr($_POST['sdt'],0,2);
	/*-------------------------------*/
	$sql = mysql_query("SELECT * FROM location WHERE location_id=".$_POST['loc']);
	$row_loc = mysql_fetch_assoc($sql);
	$particulars = "From ".$row_loc['location_name'];
	/*-------------------------------*/
	if($_POST['id']=="edit"){
		$sql = "UPDATE tbl_indent SET indent_date='".$dateIndent."',order_from=".$_POST['loc'].",";
		if($row_loc['location_prefix']==null)
			$sql .= "ind_prefix=null,";
		else
			$sql .= "ind_prefix='".$row_loc['location_prefix']."',";
		$sql .= "supply_date='".$dateSupply."',order_by=".$_POST['oby']." WHERE indent_id=".$_POST['oid'];
		$res = mysql_query($sql) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$voucherid = ($_POST['oid']>999 ? $_POST['oid'] : ($_POST['oid']>99 && $_POST['oid']<1000 ? "0".$_POST['oid'] : ($_POST['oid']>9 && $_POST['oid']<100 ? "00".$_POST['oid'] : "000".$_POST['oid'])));
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		$ino=0;
		echo $_POST['id']."~~".$_POST['oid']."~~".$ino;
	} elseif($_POST['id']=="new"){
		$sql = mysql_query("SELECT Max(indent_id) as maxid FROM tbl_indent");
		$row = mysql_fetch_assoc($sql);
		$oid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = mysql_query("SELECT Max(indent_no) as maxno FROM tbl_indent WHERE order_from=".$_POST['loc']." AND (indent_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')");
		$row = mysql_fetch_assoc($sql);
		$ino = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
		$sql = "INSERT INTO tbl_indent(indent_id,indent_date,indent_no,order_from,ind_prefix,supply_date,order_by,uid) VALUES(".$oid.",'".$dateIndent."',".$ino.",".$_POST['loc'].",";
		if($row_loc['location_prefix']==null){$sql .= "null,";} else {$sql .= "'".$row_loc['location_prefix']."',";}
		$sql .= "'".$dateSupply."',".$_POST['oby'].",".$_SESSION['stores_uid'].")";
		$res = mysql_query($sql) or die(mysql_error());
		echo $_POST['id']."~~".$oid."~~".$ino;
	}
}
?>
