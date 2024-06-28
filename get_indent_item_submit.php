<?php 
session_start();
require_once('config/config.php');
if(isset($_POST['id']) && $_POST['id']!=""){
	$msg = "";
	$sql = mysql_query("SELECT * FROM item WHERE item_id=".$_POST['itm']);
	$row = mysql_fetch_assoc($sql);
	$itemname = $row["item_name"];
	/*-------------------------------*/
	if($row['alt_unit']=="N"){$unitid = $row['unit_id'];} elseif($row['alt_unit']=="A"){$unitid = $_POST['unt'];}
        $sql = mysql_query("SELECT * FROM unit WHERE unit_id=".$unitid);
	$row = mysql_fetch_assoc($sql);
	$unitname = $row['unit_name'];
	/*-------------------------------*/
	$sql1 = mysql_query("SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=".$_POST['oid']);
	$row1 = mysql_fetch_assoc($sql1);
	$dateIndent = date("Y-m-d",strtotime($row1['indent_date']));
	$particulars = "From ".$row1['orderfrom'];
	$voucherid = ($_POST['oid']>999 ? $_POST['oid'] : ($_POST['oid']>99 && $_POST['oid']<1000 ? "0".$_POST['oid'] : ($_POST['oid']>9 && $_POST['oid']<100 ? "00".$_POST['oid'] : "000".$_POST['oid'])));
	/*-------------------------------*/
	$sql=mysql_query("SELECT * FROM tbl_indent_item WHERE indent_id=".$_POST['oid']." AND item_id=".$_POST['itm']." AND qnty=".$_POST['qty']) or die(mysql_error());
	$count = mysql_num_rows($sql);
	if($_POST['id']=="edit"){
		if($count>=0){
			$sql = "UPDATE tbl_indent_item SET item_id=".$_POST['itm'].",qnty=".$_POST['qty'].",unit_id=".$unitid.",";
			if($_POST['rmk']=="")
				$sql .= "remark=null,";
			else
				$sql .= "remark='".addslashes($_POST['rmk'])."',";

                        if($_POST['AnyOther']=="")
				$sql .= "AnyOther=''";
			else
				$sql .= "AnyOther='".addslashes($_POST['AnyOther'])."'";
                        
			$sql .= " WHERE rec_id=".$_POST['rid'];
			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['qty'].",'".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
	} elseif($_POST['id']=="delete"){
		$res = mysql_query("DELETE FROM tbl_indent_item WHERE rec_id=".$_POST['rid']) or die(mysql_error());
		//insert into logbook
		$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
		$row = mysql_fetch_assoc($sql);
		$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
		$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['qty'].",'".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
		$res = mysql_query($sql) or die(mysql_error());
		//end of inserting record into logbook
	} elseif($_POST['id']=="new"){
		if($count>0)
			$msg = "Duplication Error! can&prime;t insert into order record.";
		else {
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM tbl_indent_item");
			$row = mysql_fetch_assoc($sql);
			$rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = mysql_query("SELECT Max(seq_no) as maxno FROM tbl_indent_item WHERE indent_id=".$_POST['oid']);
			$row = mysql_fetch_assoc($sql);
			$sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
			$sql = "INSERT INTO tbl_indent_item (rec_id,indent_id,seq_no,item_id,qnty,unit_id,item_ordered,remark,AnyOther) VALUES(".$rid.",".$_POST['oid'].",".$sno.",".$_POST['itm'].",".$_POST['qty'].",".$unitid.",'N',";
			if($_POST['rmk']=="")
				$sql .= "null,";
			else
				$sql .= "'".addslashes($_POST['rmk'])."',";
			if($_POST['AnyOther']=="")
				$sql .= "'')";
			else
				$sql .= "'".addslashes($_POST['AnyOther'])."')";

			$res = mysql_query($sql) or die(mysql_error());
			//insert into logbook
			$sql = mysql_query("SELECT Max(rec_id) as maxid FROM logbook");
			$row = mysql_fetch_assoc($sql);
			$recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
			$sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,unit,item_qnty,location,action,user) VALUES(".$recordid.",'".$voucherid."','".$dateIndent."','INDENT','".date("Y-m-d")."','".$particulars."','".$itemname."','".$unitname."',".$_POST['qty'].",'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
			$res = mysql_query($sql) or die(mysql_error());
			//end of inserting record into logbook
		}
	}
	echo $msg;
}
?>
