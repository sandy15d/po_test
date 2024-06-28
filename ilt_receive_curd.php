<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'get_ilt') {
        $status = $_POST['status'];
         $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {
            $sql = "SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, 
            despatchStaff.staff_name AS sourceStaff,DATE_FORMAT(ilt_date,'%d-%m-%Y') as ilt_date,despatch_status FROM tblilt1 
            LEFT JOIN location AS source ON tblilt1.despatch_from = source.location_id 
            LEFT JOIN location AS destination ON tblilt1.receive_at = destination.location_id 
            LEFT JOIN staff AS despatchStaff ON tblilt1.despatch_by = despatchStaff.staff_id 
            WHERE despatch_status='S' AND receive_status='$status' AND ilt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
           
            ORDER BY destinationLocation,ilt_date,ilt_id";
        } elseif ($_SESSION['stores_utype'] == "U") {

            $sql = "SELECT 
            t.*, 
            src.location_name AS sourceLocation, 
            dst.location_name AS destinationLocation,
            ds.staff_name AS sourceStaff,
            DATE_FORMAT(t.ilt_date,'%d-%m-%Y') as formattedDate
        FROM tblilt1 t
        left JOIN location src ON t.despatch_from = src.location_id 
        left JOIN location dst ON t.receive_at = dst.location_id 
        left JOIN staff ds ON t.despatch_by = ds.staff_id 
        WHERE 
            t.receive_at IN (" . $_SESSION['stores_locid'] . ")
            AND t.despatch_status = 'S' 
            AND t.receive_status = '$status'  AND ilt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
           
        ORDER BY 
            destinationLocation, 
            formattedDate, 
            ilt_id";
        }


        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn edit" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['ilt_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button> ';

            if ($row['ilt_no'] > 999) {
                $ilt_no = $row['ilt_no'];
            } else {
                if ($row['ilt_no'] > 99 && $row['ilt_no'] < 1000) {
                    $ilt_no = "0" . $row['ilt_no'];
                } else {
                    if ($row['ilt_no'] > 9 && $row['ilt_no'] < 100) {
                        $ilt_no = "00" . $row['ilt_no'];
                    } else {
                        $ilt_no = "000" . $row['ilt_no'];
                    }
                }
            }
            $ilt_no = $row['ilt_prefix'] . '/' . $ilt_no;
            $output['data'][] = array(
                $x,
                $ilt_no,
                $row['ilt_date'],
                $row['sourceLocation'],
                $row['destinationLocation'],
                $row['sourceStaff'],
                $actionButton

            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'edit_ilt_receive') {
        $ilt_id = $_POST['ilt_id'];
        $sql = "SELECT tblilt2.*,i.item_name,ic.category,u.unit_name,ilt_date,receive_by FROM `tblilt2` 
        LEFT JOIN item i ON i.item_id = tblilt2.item_id
        LEFT JOIN item_category ic ON ic.category_id = tblilt2.item_category
        LEFT JOIN unit u ON u.unit_id = tblilt2.unit_id
        JOIN tblilt1 ON tblilt2.ilt_id = tblilt1.ilt_id
        WHERE tblilt2.ilt_id=$ilt_id";
        $query = $connect->query($sql);
        $output = array('data' => array());
        while ($row = $query->fetch_assoc()) {
            $output['data'][] = array(
                'item_name' => $row['item_name'] . ' ~~' . $row['category'],
                'qty' => $row['despatch_qnty'],
                'rec_id' => $row['rec_id'],
                'ilt_id' => $row['ilt_id'],
                'unit_name' => $row['unit_name'],
                'unit_id' => $row['unit_id'],
                'item_id' => $row['item_id'],
                'item_category' => $row['item_category'],
                'seq_no' => $row['seq_no'],
                'ilt_date' => $row['ilt_date'],
                'receive_by' => $row['receive_by'],
            );
        }
        echo json_encode($output);
        $connect->close();
    }
    elseif ($action == 'get_staff') {
        $ilt_id = $_POST['ilt_id'];
        $sql = "SELECT receive_at FROM tblilt1 WHERE ilt_id =$ilt_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $recive_at = $row['receive_at'];

        $sql = "SELECT * FROM staff WHERE location_id =$recive_at";
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
    }
    elseif ($action == 'receive_item') {

        $ilt_id = $_POST['ilt_id'];
        $receive_by = $_POST['receive_by'];
        $receive_date = $_POST['receive_date'];
        $list_array = $_POST['list_array'];
        $sql_location = "SELECT receive_at FROM tblilt1 WHERE ilt_id =$ilt_id";
        $query_location = $connect->query($sql_location);
        $row_location = mysqli_fetch_assoc($query_location);
        $receive_at = $row_location['receive_at'];
       
        foreach ($list_array as $lst_arr) {
            $sql = "UPDATE tblilt2 SET receive_qnty='" . $lst_arr['receive_qty'] . "' WHERE ilt_id =$ilt_id AND item_id ='" . $lst_arr['item_id'] . "' AND item_category ='" . $lst_arr['item_category'] . "'";
            $query = $connect->query($sql);

            // check stock register entry

            $sql_stock_check = "SELECT * FROM stock_register WHERE entry_mode='T+' AND entry_id=$ilt_id AND item_id ='" . $lst_arr['item_id'] . "' AND item_category ='" . $lst_arr['item_category'] . "' AND unit_id ='" . $lst_arr['unit'] . "'";
            $query_stock_check = $connect->query($sql_stock_check);
            $row_stock_check = mysqli_fetch_assoc($query_stock_check);
           //if stock register entry not exist then insert new entry
            if($row_stock_check == null){
                $sql_stock = "SELECT Max(stock_id) AS maxid FROM stock_register";
                $query_stock = $connect->query($sql_stock);
                $row = mysqli_fetch_assoc($query_stock);


                $sid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
                $str = "INSERT INTO stock_register 
                 (stock_id, entry_mode, entry_id, entry_date, seq_no, location_id, item_id, item_category, unit_id, item_qnty, item_rate, item_amt)
                    VALUES ('$sid', 'T+', '$ilt_id', '$receive_date', '{$lst_arr['seq_no']}', '$receive_at', '{$lst_arr['item_id']}', '{$lst_arr['item_category']}', '{$lst_arr['unit']}', '{$lst_arr['receive_qty']}', '0.00', '0.00')";
                $query_str = $connect->query($str);
            }else{
                // update stock register

                $update_stock = "UPDATE stock_register SET entry_date ='$receive_date', item_qnty = '{$lst_arr['receive_qty']}' WHERE entry_mode='T+' AND entry_id=$ilt_id AND item_id ='" . $lst_arr['item_id'] . "' AND item_category ='" . $lst_arr['item_category'] . "' AND unit_id ='" . $lst_arr['unit'] . "'";

                $query_update = $connect->query($update_stock);
            }

        }

        $str = "UPDATE tblilt1 SET receive_by=$receive_by,receive_status='R',receive_date='$receive_date' WHERE ilt_id =$ilt_id";

        $query = $connect->query($str);


        if ($query == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    }
        elseif ($action == 'delete_record') {
        $ilt_id = $_POST['ilt_id'];

        $sql = "SELECT * FROM tblilt1 WHERE ilt_id =$ilt_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ilt_date = $row['ilt_date'];

    

        $sql1 = "UPDATE tblilt1 SET receive_status='U' WHERE ilt_id =$ilt_id";
        $query1 = $connect->query($sql1);

        $str = "DELETE FROM stock_register WHERE entry_mode='T+' AND entry_id=$ilt_id AND entry_date='$ilt_date'";
        $query = $connect->query($str);

        if ($query1 == true) {
            $output['success'] = true;
            $output['messages'] = "Delete Record Successfully...!!";
        } else {
            $output['success'] = false;
            $output['messages'] = "Failed To Delete Record...!!";
        }
        $connect->close();
        echo json_encode($output);
    }
}
?>