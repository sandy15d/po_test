<?php
require_once 'db_connect.php';
session_start();
/*-------------------------------*/
$sql_user = "SELECT oi1,oi2,oi3,oi4 FROM users WHERE uid=" . $_SESSION["stores_uid"];
$row_user = $connect->query($sql_user);
$row_user = mysqli_fetch_assoc($row_user);
if (isset($_SESSION["stores_utype"])) {
    $uid = $_SESSION["stores_uid"];
    $uname = $_SESSION["stores_uname"];
    $utype = $_SESSION["stores_utype"];
    $locid = $_SESSION["stores_locid"];
    $lname = $_SESSION["stores_lname"];
    $syear = $_SESSION["stores_syr"];
    $eyear = $_SESSION["stores_eyr"];
}

/*-------------------------------*/

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'get_staff') {
        $location = $_POST['location'];
        $sql = "SELECT * FROM staff WHERE location_id=$location";
        $query = $connect->query($sql);

        $output = [];
        while ($row = mysqli_fetch_row($query)) {
            $temp = [];
            $temp['staff_id'] = $row[0];
            $temp['staff_name'] = $row[1];
            $output[] = $temp;
        }
        if (count($output) > 0) {
            echo json_encode(array('data' => $output, 'status' => 200));
        } else {
            echo json_encode(array('msg' => 'Record not found.', 'status' => 500));
        }
    } elseif ($action == 'add_order_indent') {

        $location = $_POST['location'];
        $indent_date = $_POST['indent_date'];
        $supply_date = $_POST['supply_date'];
        $order_by = $_POST['order_by'];
        $uid = $_SESSION['stores_uid'];

        // Using prepared statements to prevent SQL injection.
        $stmt = $connect->prepare("SELECT location_prefix FROM location WHERE location_id = ?");
        $stmt->bind_param("i", $location);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $loc_pre = ($row['location_prefix'] !== null) ? $row['location_prefix'] : 'null';

        // Get the maximum indent_id
        $sql = "SELECT IFNULL(MAX(indent_id), 0) AS maxid FROM tbl_indent";
        $result = $connect->query($sql);
        $maxid = $result->fetch_assoc()['maxid'];
        $oid = $maxid + 1;

        // Get the maximum indent_no
        $sql = "SELECT IFNULL(MAX(indent_no), 0) AS maxno FROM tbl_indent WHERE order_from = ? AND (indent_date BETWEEN ? AND ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sss", $location, $_SESSION['stores_syr'], $_SESSION['stores_eyr']);
        $stmt->execute();
        $result = $stmt->get_result();
        $ino = $result->fetch_assoc()['maxno'] + 1;

        $file_name = '';

        if (isset($_FILES["quotation_attachment"]) && !empty($_FILES["quotation_attachment"]["name"])) {
            $file_extension = pathinfo($_FILES["quotation_attachment"]["name"], PATHINFO_EXTENSION);
            $file_name = 'quotation_' . time() . '.' . $file_extension;
            $temp_name = $_FILES["quotation_attachment"]["tmp_name"];
            $destination = "uploads/" . $file_name;
            move_uploaded_file($temp_name, $destination);
        }

        // Use prepared statements to insert data safely.
        $stmt = $connect->prepare("INSERT INTO tbl_indent (indent_id, indent_date, indent_no, order_from, ind_prefix, supply_date, order_by, uid, attachment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississsss", $oid, $indent_date, $ino, $location, $loc_pre, $supply_date, $order_by, $uid, $file_name);
        $result = $stmt->execute();

        if ($result) {
            // Retrieve data for response.
            $sql = "SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name, ordfrom.location_id FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $oid);
            $stmt->execute();
            $output = $stmt->get_result()->fetch_assoc();

            $output['success'] = true;
        } else {
            $output['success'] = false;
            $output['messages'] = "Error while creating the new order indent";
        }

        $connect->close();

        echo json_encode($output);
    } elseif ($action == 'get_stock') {
        $item_id = $_POST['item'];
        $location = $_POST['location'];
        $entry_date = $_POST['entry_date'];
        $category = $_POST['category'];
        $sql = "SELECT ifnull(Sum(s.item_qnty),0) AS qty,u.unit_id,u.unit_name FROM stock_register s
            JOIN item itm ON itm.item_id =s.item_id
            JOIN unit u ON u.unit_id = itm.unit_id
            WHERE s.item_id=$item_id AND item_category= $category AND s.location_id=$location AND s.entry_date<='$entry_date'";
        //print_r($sql);die;
        $query = $connect->query($sql);
        $result = $query->fetch_assoc();
        echo json_encode(array('success' => true, 'data' => $result));
    } elseif ($action == 'additem') {
        $rec_id = $_POST['rec_id'];
        $indent_order_id = $_POST['indent_order_id'];
        $item_id = $_POST['item'];
        $item_category = $_POST['item_category'];
        $qty = $_POST['qty'];
        $unit_id = $_POST['unit'];
        $desc = $_POST['desc'];
        $remark = $_POST['remark'];
        $indent_date = $_POST['indent_date'];
        $location = $_POST['location'];
        if ($desc == "" && empty($desc)) {
            $desc = "";
        } else {
            $desc = addslashes($desc);
        }
        if ($remark == "" & empty($remark)) {
            $remark = "";
        } else {
            $remark = addslashes($remark);
        }

        if (!empty($rec_id)) {
            //Update Item Record
            $sql = "UPDATE tbl_indent_item SET item_id='$item_id',qnty='$qty',item_category='$item_category',remark='$remark',AnyOther='$desc' WHERE rec_id=$rec_id";
            $query = $connect->query($sql);
            if ($query == true) {
                //===============Logbook Insert================================
                $logbok_rec_id = "SELECT MAX(rec_id) as maxid FROM logbook";
                $logbook_query = $connect->query($logbok_rec_id);
                $logbook_rec_id = mysqli_fetch_array($logbook_query);
                $logbook_rec_id = ($logbook_rec_id["maxid"] == null ? 1 : $logbook_rec_id["maxid"] + 1);
                //------------------------
                $order_from = "SELECT location_name FROM location WHERE location_id =$location";
                $query = $connect->query($order_from);
                $location_name = mysqli_fetch_assoc($query);
                $location_name = $location_name['location_name'];
                $particulars = "From " . $location_name;
                //------------------------------------
                $item_name = "SELECT item_name FROM item WHERE item_id =$item_id";
                $query = $connect->query($item_name);
                $item = mysqli_fetch_assoc($query);
                $item = $item['item_name'];
                //--------------------------
                $sql2 = "SELECT unit_name FROM unit WHERE unit_id =$unit_id";

                $query = $connect->query($sql2);
                $row = mysqli_fetch_assoc($query);

                $unit_name = $row['unit_name'];
                //--------------------------
                $sql4 = "SELECT category FROM item_category WHERE category_id =$item_category";

                $query = $connect->query($sql4);
                $row = mysqli_fetch_assoc($query);

                $category_name = $row['category'];
                //-----------------------------------------
                $voucherid = ($indent_order_id > 999 ? $indent_order_id : ($indent_order_id > 99 && $indent_order_id < 1000 ? "0" . $indent_order_id : ($indent_order_id > 9 && $indent_order_id < 100 ? "00" . $indent_order_id : "000" . $indent_order_id)));
                $sql1 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user)VALUES(" . $logbook_rec_id . ",'" . $voucherid . "','" . $indent_date . "','INDENT','" . date("Y-m-d") . "','" . $particulars . "','" . $item . "','" . $category_name . "','" . $unit_name . "'," . $qty . ",'" . $lname . "','Change','" . $uname . "')";
                $query = $connect->query($sql1);
                //=============================================================  
                $output['success'] = true;
                $output['messages'] = "Successfully Updated";
            } else {
                $output['success'] = false;
                $output['messages'] = "Error while updating the item information";
            }
            $connect->close();
            echo json_encode($output);
        } else {
            //Add New Item 
            $sql = "SELECT Max(rec_id) as maxid FROM tbl_indent_item";
            $query = $connect->query($sql);
            $query = mysqli_fetch_array($query);
            $maxid = $query['maxid'];
            $rec_id = ($maxid == null ? 1 : $maxid + 1);
            $sql = "SELECT Max(seq_no) as maxno FROM tbl_indent_item WHERE indent_id=$indent_order_id";
            $query = $connect->query($sql);
            $query = mysqli_fetch_array($query);
            $maxno = $query['maxno'];
            $seq_id = ($maxno == null ? 1 : $maxno + 1);
            $sql = "INSERT INTO tbl_indent_item(rec_id,indent_id,seq_no,item_id,item_category,qnty,unit_id,item_ordered,remark,AnyOther)
                VALUES('$rec_id','$indent_order_id','$seq_id','$item_id','$item_category','$qty','$unit_id','N','$remark','$desc')";
            // print_r($sql);die;
            $query = $connect->query($sql);
            if ($query == TRUE) {
                //===============Logbook Insert================================
                $logbok_rec_id = "SELECT MAX(rec_id) as maxid FROM logbook";
                $logbook_query = $connect->query($logbok_rec_id);
                $logbook_rec_id = mysqli_fetch_array($logbook_query);
                $logbook_rec_id = ($logbook_rec_id["maxid"] == null ? 1 : $logbook_rec_id["maxid"] + 1);
                //------------------------
                $order_from = "SELECT location_name FROM location WHERE location_id =$location";
                $query = $connect->query($order_from);
                $location_name = mysqli_fetch_assoc($query);
                $location_name = $location_name['location_name'];
                $particulars = "From " . $location_name;
                //------------------------------------
                $item_name = "SELECT item_name FROM item WHERE item_id =$item_id";
                $query = $connect->query($item_name);
                $item = mysqli_fetch_assoc($query);
                $item = $item['item_name'];
                //-----------------------------
                $sql = "SELECT unit_name from unit where unit_id =$unit_id";
                $query = $connect->query($sql);
                $unit_name = mysqli_fetch_assoc($query);
                $unit_name = $unit_name['unit_name'];
                //------------------------------
                $sql4 = "SELECT category FROM item_category WHERE category_id =$item_category";

                $query = $connect->query($sql4);
                $row = mysqli_fetch_assoc($query);

                $category_name = $row['category'];

                //-----------------------------------------
                $voucherid = ($indent_order_id > 999 ? $indent_order_id : ($indent_order_id > 99 && $indent_order_id < 1000 ? "0" . $indent_order_id : ($indent_order_id > 9 && $indent_order_id < 100 ? "00" . $indent_order_id : "000" . $indent_order_id)));
                $sql1 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user)VALUES(" . $logbook_rec_id . ",'" . $voucherid . "','" . $indent_date . "','INDENT','" . date("Y-m-d") . "','" . $particulars . "','" . $item . "','" . $category_name . "','" . $unit_name . "'," . $qty . ",'" . $lname . "','New','" . $uname . "')";
                $query = $connect->query($sql1);
                //=============================================================      
                $output['success'] = true;
                $output['messages'] = "Successfully Added New Item";
            } else {
                $output['success'] = false;
                $output['messages'] = "Error while creating the new order indent";
            }
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'get_item_list') {
        $indent_id = $_POST['indent_id'];
        $sql = "SELECT tbl1.rec_id,tbl1.indent_id,i.item_name,ic.category,u.unit_name,tbl1.qnty,tbl1.remark,tbl1.AnyOther FROM `tbl_indent_item` tbl1
            JOIN item i on i.item_id = tbl1.item_id
            JOIN unit u ON u.unit_id = tbl1.unit_id
            JOIN item_category ic ON ic.category_id = tbl1.item_category
            WHERE indent_id  =$indent_id ORDER BY tbl1.seq_no";
        $query = $connect->query($sql);
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            if ($row_user['oi2'] == 1) {
                $edit_btn = '<button type ="button" class="btn"  onclick="editItem(' . $row['rec_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
            } else {
                $edit_btn = '-';
            }
            if ($row_user['oi3'] == 1) {
                $delete_btn = '<button type ="button"  class="btn" data-toggle="modal" data-target="#removeItemModal"  onclick="removeItem(' . $row['rec_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            } else {
                $delete_btn = '-';
            }
            $output['data'][] = array(
                $x,
                $row['item_name'] . ' ~~ ' . $row['category'],
                $row['remark'] . '  ' . $row['AnyOther'],
                $row['qnty'],
                $row['unit_name'],
                $edit_btn,
                $delete_btn
            );
            $x++;
        }

        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'delete_item') {

        $rec_id = $_POST['rec_id'];

        $sql = "SELECT * FROM `tbl_indent_item` ti 
            JOIN tbl_indent t ON t.indent_id = ti.indent_id 
            WHERE ti.rec_id =$rec_id";

        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $item_id = $row['item_id'];
        $location = $row['order_from'];
        $unit_id = $row['unit_id'];
        $qty = $row['qnty'];
        $indent_order_id = $row['indent_id'];
        $indent_date = $row['indent_date'];
        $item_category = $row['item_category'];

        $str = "DELETE FROM tbl_indent_item WHERE rec_id = $rec_id";
        $query1 = $connect->query($str);
        if ($query1 === TRUE) {
            //===============Logbook Insert================================
            $logbok_rec_id = "SELECT MAX(rec_id) as maxid FROM logbook";
            $logbook_query = $connect->query($logbok_rec_id);
            $logbook_rec_id = mysqli_fetch_array($logbook_query);
            $logbook_rec_id = ($logbook_rec_id["maxid"] == null ? 1 : $logbook_rec_id["maxid"] + 1);
            //------------------------
            $order_from = "SELECT location_name FROM location WHERE location_id =$location";
            $query = $connect->query($order_from);
            $location_name = mysqli_fetch_assoc($query);
            $location_name = $location_name['location_name'];
            $particulars = "From " . $location_name;
            //------------------------------------
            $item_name = "SELECT item_name FROM item WHERE item_id =$item_id";
            $query = $connect->query($item_name);
            $item = mysqli_fetch_assoc($query);
            $item = $item['item_name'];
            //-----------------------------
            $sql = "SELECT unit_name from unit where unit_id =$unit_id";
            $query = $connect->query($sql);
            $unit_name = mysqli_fetch_assoc($query);
            $unit_name = $unit_name['unit_name'];
            //-----------------------------------------
            $sql4 = "SELECT category FROM item_category WHERE category_id =$item_category";

            $query = $connect->query($sql4);
            $row = mysqli_fetch_assoc($query);

            $category_name = $row['category'];
            //------------------------------------------------
            $voucherid = ($indent_order_id > 999 ? $indent_order_id : ($indent_order_id > 99 && $indent_order_id < 1000 ? "0" . $indent_order_id : ($indent_order_id > 9 && $indent_order_id < 100 ? "00" . $indent_order_id : "000" . $indent_order_id)));
            $sql1 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,'category_name',unit,item_qnty,location,action,user)VALUES(" . $logbook_rec_id . ",'" . $voucherid . "','" . $indent_date . "','INDENT','" . date("Y-m-d") . "','" . $particulars . "','" . $item . "','" . $category_name . "','" . $unit_name . "'," . $qty . ",'" . $lname . "','Delete','" . $uname . "')";
            $query = $connect->query($sql1);
            //=============================================================  
            $output['success'] = true;
            $output['messages'] = 'Successfully removed';
        } else {
            $output['success'] = false;
            $output['messages'] = 'Error while removing the company information';
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'get_item') {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT * FROM tbl_indent_item itm
            JOIN item i ON i.item_id =itm.item_id
            JOIN item_category ic ON ic.category_id = itm.item_category
            WHERE itm.rec_id =$rec_id";
        $query = $connect->query($sql);
        $result = $query->fetch_assoc();
        $connect->close();
        echo json_encode($result);
    } elseif ($action == 'update_indent') {
        $indent_order_id = $_POST['indent_order_id'];
        $sql = "UPDATE tbl_indent SET ind_status='S' WHERE indent_id=$indent_order_id";
        $updatequery = $connect->query($sql);

        //=================Send Mail to Reporting Manager==================

        $sql1 = "SELECT * FROM `tbl_indent` WHERE `indent_id`=$indent_order_id";
        $query1 = $connect->query($sql1);
        $ino = mysqli_fetch_assoc($query1);
        $ino = $ino['indent_no'];

        $voucherid = ($ino > 999 ? $ino : ($ino > 99 && $ino < 1000 ? "0" . $ino : ($ino > 9 && $ino < 100 ? "00" . $ino : "000" . $ino)));

        $sql1 = "SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=$indent_order_id";
        $query1 = $connect->query($sql1);
        $row = mysqli_fetch_assoc($query1);

        if ($row['ind_prefix'] != null) {
            $voucherid = $row['ind_prefix'] . "/" . $voucherid;
        }
        $dateIndent = date("d-m-Y", strtotime($row['indent_date']));
        $UserId = $row['uid'];
        $i = 0;

        // Initialize the styled email text
        $styledText = '';

        $indentInfo = '
        <div style="background-color: #007bff; color: #ffffff; padding: 10px; margin-bottom: 10px;">
        <p style="margin: 0; font-weight: bold;">Indent No.: ' . $voucherid . '&emsp;&emsp; Date: ' . $dateIndent . '</p>
        <p style="margin: 0;">From - ' . $row['location_name'] . '</p>
        </div>';

        // Append the styled indent information to the styled text
        $styledText .= $indentInfo;

        // Start the HTML email with a table
        $styledText .= '<table style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border: 1px solid #dfe2e5;">';

        // Table header with colored background
        $styledText .= '<thead style="background-color: #007bff; color: #ffffff;">';
        $styledText .= '<tr>';
        $styledText .= '<th style="padding: 10px; border: 1px solid #dfe2e5;">#</th>';
        $styledText .= '<th style="padding: 10px; border: 1px solid #dfe2e5;">Item Name</th>';
        $styledText .= '<th style="padding: 10px; border: 1px solid #dfe2e5;">Packing Size/Type</th>';
        $styledText .= '<th style="padding: 10px; border: 1px solid #dfe2e5;">Quantity</th>';
        $styledText .= '<th style="padding: 10px; border: 1px solid #dfe2e5;">Unit Name</th>';
        $styledText .= '</tr>';
        $styledText .= '</thead>';

        $sql2 = "SELECT ti.*,i.item_name,u.unit_name,ic.category FROM tbl_indent_item ti
        JOIN item i ON i.item_id = ti.item_id
        JOIN unit u ON u.unit_id = i.unit_id
        JOIN item_category ic ON ic.category_id= ti.item_category WHERE ti.indent_id=$indent_order_id ORDER BY seq_no";
        $query2 = $connect->query($sql2);

        while ($row = mysqli_fetch_array($query2)) {
            $i++;
            // Create a formatted row for each item
            $itemRow = '<tr>';
            $itemRow .= "<td style='padding: 10px; border: 1px solid #dfe2e5;'>{$i}</td>";
            $itemRow .= "<td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['item_name']}</td>";
            $itemRow .= "<td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['category']}</td>";
            $itemRow .= "<td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['qnty']}</td>";
            $itemRow .= "<td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['unit_name']}</td>";
            $itemRow .= '</tr>';

            // Append the formatted row to the styled text
            $styledText .= $itemRow;
        }

        // Close the table
        $styledText .= '</table>';

        $additionalContent = '
        <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dfe2e5;">
            <p style="font-weight: bold;">Kindly go through the website for approval of these items.</p>
            <p style="font-style: italic; color: #007bff;">Thank you.</p>
            <p style="color: #6c757d;">(This is a system-generated message)</p>
        </div>
    ';

        // Append the additional content to the styled text
        $styledText .= $additionalContent;


        $subject = "Indent Approval";
        $body = $styledText;

        $queryU = $connect->query("select repuser_id, repuser2_id, repuser3_id, repuser4_id, repuser5_id from users where uid ='" . $UserId . "'");
        $resU = mysqli_fetch_assoc($queryU);

        if ($resU['repuser_id'] > 0) {
            $queryE = $connect->query("select email_id from users where uid ='" . $resU['repuser_id'] . "'");
            $resE = mysqli_fetch_assoc($queryE);
            if ($resE['email_id'] != '') {
                $email_to = $resE['email_id'];
                require 'vendor/mail.php';
                // $sendmail = mail($resE['email_id'], $subject, $msg, $headers);
            }
        }
        if ($resU['repuser2_id'] > 0) {
            $queryE = $connect->query("select email_id from users where uid ='" . $resU['repuser2_id'] . "'");
            $resE2 = mysqli_fetch_assoc($queryE);
            if ($resE2['email_id'] != '') {
                $email_to = $resE2['email_id'];
                require 'vendor/mail.php';
                // $sendmail = mail($resE2['email_id'], $subject, $msg, $headers);
            }
        }
        if ($resU['repuser3_id'] > 0) {
            $queryE = $connect->query("select email_id from users where uid ='" . $resU['repuser3_id'] . "'");
            $resE3 = mysqli_fetch_assoc($queryE);
            if ($resE3['email_id'] != '') {
                $email_to = $resE3['email_id'];
                require 'vendor/mail.php';
                // $sendmail = mail($resE3['email_id'], $subject, $msg, $headers);
            }
        }
        if ($resU['repuser4_id'] > 0) {
            $queryE = $connect->query("select email_id from users where uid ='" . $resU['repuser4_id'] . "'");
            $resE4 = mysqli_fetch_assoc($queryE);
            if ($resE4['email_id'] != '') {
                $email_to = $resE4['email_id'];
                require 'vendor/mail.php';
                //$sendmail = mail($resE4['email_id'], $subject, $msg, $headers);
            }
        }
        if ($resU['repuser5_id'] > 0) {
            $queryE = $connect->query("select email_id from users where uid ='" . $resU['repuser5_id'] . "'");
            $resE5 = mysqli_fetch_assoc($queryE);
            if ($resE5['email_id'] != '') {
                $email_to = $resE5['email_id'];
                require 'vendor/mail.php';
                //$sendmail = mail($resE5['email_id'], $subject, $msg, $headers);
            }
        }


        if ($updatequery) {

            $output['success'] = true;
        } else {
            $output['success'] = false;
        }

        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'get_category') {
        $item_id = $_POST['item'];
        $sql = "SELECT * FROM item_category WHERE item_id=$item_id";

        $query = $connect->query($sql);

        $output = [];
        while ($row = mysqli_fetch_row($query)) {
            $temp = [];
            $temp['category_id'] = $row[0];
            $temp['category'] = $row[2];
            $output[] = $temp;
        }
        if (count($output) > 0) {
            echo json_encode(array('data' => $output, 'status' => 200));
        } else {
            echo json_encode(array('msg' => 'Record not found.', 'status' => 500));
        }
    } elseif ($action == 'get_order_indent') {
        $oid  = $_POST['oid'];
        $sql = "SELECT tbl_indent.*, ordfrom.location_name AS orderfrom, staff_name,ordfrom.location_id FROM tbl_indent INNER JOIN location AS ordfrom ON tbl_indent.order_from = ordfrom.location_id INNER JOIN staff ON tbl_indent.order_by = staff.staff_id WHERE indent_id=$oid";
        $query = $connect->query($sql);
        $output = $query->fetch_assoc();
        echo json_encode($output);
    }
}
