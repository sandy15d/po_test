<?php 
session_start();
require_once('config/config.php');
include("function.php");
if(check_user()==false){header("Location: login.php");}
/*--------------------*/
if(isset($_REQUEST["xn"]) && $_REQUEST["xn"]=="D"){
	$oid = $_REQUEST['oid'];
	$sql = mysql_query("SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=".$oid);
	$row = mysql_fetch_assoc($sql);
	$dateIndent = $row['indent_date'];
	$particulars = "From ".$row['location_name'];
	$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
	if(isset($_REQUEST['rid'])){
		$sql2 = mysql_query("SELECT tbl_indent_item.*, item_name, unit_name FROM tbl_indent_item INNER JOIN item ON tbl_indent_item.item_id = item.item_id INNER JOIN unit ON tbl_indent_item.unit_id = unit.unit_id WHERE rec_id=".$_REQUEST['rid']);
		$row2 = mysql_fetch_assoc($sql2);
		$res = mysql_query("DELETE FROM tbl_indent_item WHERE rec_id=".$_REQUEST['rid']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$row2['item_name']."','".$row2['unit_name']."',".$row2['qnty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
		$sql = mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$oid) or die(mysql_error());
		if(mysql_num_rows($sql)==0){
			$res = mysql_query("DELETE FROM tbl_indent WHERE indent_id=".$oid) or die(mysql_error());
		}
	} else {
		$res = mysql_query("DELETE FROM tbl_indent WHERE indent_id=".$oid) or die(mysql_error());
		$res = mysql_query("DELETE FROM tbl_indent_item WHERE indent_id=".$oid) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
	}
	$x = 'indlist.php?lid='.$_REQUEST['lid'].'&rf='.$_REQUEST['rf'].'&sm='.$_REQUEST['sm'].'&em='.$_REQUEST['em'];
	if(isset($_REQUEST['pg'])){$x .= '&pg='.$_REQUEST['pg'].'&tr='.$_REQUEST['tr'];}
	header('Location:'.$x);
} elseif(isset($_REQUEST["xn"]) && $_REQUEST["xn"]=="R"){
	$oid = $_REQUEST['oid'];
	$sql = mysql_query("SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=".$oid);
	$row = mysql_fetch_assoc($sql);
	$dateIndent = $row['indent_date'];
	$particulars = "From ".$row['location_name'];
	$res = mysql_query("UPDATE tbl_indent SET ind_status='U' WHERE indent_id=".$oid) or die(mysql_error());
	//insert into logbook
	$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
	$row = mysql_fetch_assoc($sql);
	$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
	$voucherid = ($oid>999 ? $oid : ($oid>99 && $oid<1000 ? "0".$oid : ($oid>9 && $oid<100 ? "00".$oid : "000".$oid)));
	$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$_SESSION['stores_lname']."','Recall','".$_SESSION['stores_uname']."')";
	$res = mysql_query($sql) or die(mysql_error());
	//end of inserting record into logbook
	$x = 'indlist.php?lid='.$_REQUEST['lid'].'&rf='.$_REQUEST['rf'].'&sm='.$_REQUEST['sm'].'&em='.$_REQUEST['em'];
	if(isset($_REQUEST['pg'])){$x .= '&pg='.$_REQUEST['pg'].'&tr='.$_REQUEST['tr'];}
	header('Location:'.$x);
}
?>