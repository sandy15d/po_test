<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'save_issue') {
        $issue_id = $_POST['issue_id'];
        $issue_date = $_POST['issue_date'];
        $location = $_POST['location'];
        $issue_by = $_POST['issue_by'];
        $issue_to = $_POST['issue_to'];

        $sql = "SELECT Max(issue_id) as maxid FROM tblissue1";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $maxid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);

        $sql = "SELECT Max(issue_no) as maxno FROM tblissue1 WHERE location_id=" . $location . " AND (issue_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $issue_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

        $sql = "SELECT * FROM location WHERE location_id =$location";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $loc_prefix = $row['location_prefix'];

        if ($issue_id != '') {


            $sql = "SELECT * FROM tblissue1 WHERE issue_id =$issue_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $issue_no = $row['issue_no'];

            if ($location != $row['location_id']) {
                $sql = "SELECT Max(issue_no) as maxno FROM tblissue1 WHERE location_id=$location AND (issue_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
                $query = $connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $ino = $row['maxno'] + 1;
            } else {
                $ino = $issue_no;
            }
            $sql = "UPDATE tblissue1 SET issue_no='$ino',issue_date='$issue_date',location_id='$location',issue_prefix='$loc_prefix',issue_by='$issue_by',issue_to='$issue_to' WHERE issue_id=$issue_id";

            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET entry_date='$issue_date',location_id=$location WHERE entry_mode='I+' AND entry_id=$issue_id";
            $query = $connect->query($sql);


            $sql = "SELECT issue_no, issue_prefix FROM tblissue1 WHERE issue_id =$issue_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['issue_no'] > 99 && $row['issue_no'] < 1000) {
                $issue_no = "0" . $row['issue_no'];
            } else {
                if ($row['issue_no'] > 9 && $row['issue_no'] < 100) {
                    $issue_no = "00" . $row['issue_no'];
                } else {
                    $issue_no = "000" . $row['issue_no'];
                }
            }
            $issue_no = $row['issue_prefix'] . '/' . $issue_no;
            if ($query == true) {
                $output['success'] = true;
                $output['issue_id'] = $issue_id;
                $output['issue_no'] = $issue_no;
            } else {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        } else {
            //Insert

            $sql = "INSERT INTO tblissue1(issue_id,issue_no,issue_date,issue_prefix,location_id,issue_by,issue_to) 
           VALUES('$maxid','$issue_no','$issue_date','$loc_prefix','$location','$issue_by','$issue_to')";
            $query = $connect->query($sql);

            $last_id = "SELECT issue_id FROM tblissue1 ORDER BY issue_id DESC limit 0 ,1";
            $query = $connect->query($last_id);
            $row = mysqli_fetch_assoc($query);

            $id = $row['issue_id'];

            $sql = "SELECT issue_no, issue_prefix FROM tblissue1 WHERE issue_id =$id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['issue_no'] > 99 && $row['issue_no'] < 1000) {
                $issue_no = "0" . $row['issue_no'];
            } else {
                if ($row['issue_no'] > 9 && $row['issue_no'] < 100) {
                    $issue_no = "00" . $row['issue_no'];
                } else {
                    $issue_no = "000" . $row['issue_no'];
                }
            }
            $issue_no = $row['issue_prefix'] . '/' . $issue_no;
            if ($query == true) {
                $output['success'] = true;
                $output['issue_id'] = $id;
                $output['issue_no'] = $issue_no;
            } else {
                $output['success'] = false;
            }

            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'save_item') {
        $rec_id = $_POST['rec_id'];
        $issue_id = $_POST['issue_id'];
        $item = $_POST['item'];
        $item_category = $_POST['item_category'];
        $unit = $_POST['unit'];
        $qty = $_POST['qty'];
        $plot = $_POST['plot'];

        $sql = "SELECT * FROM tblissue1 WHERE issue_id =$issue_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $issue_date = $row['issue_date'];
        $location = $row['location_id'];

        $sql = "SELECT location_name FROM location WHERE location_id =$location";
        $query = $connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $location_name = $res['location_name'];

        $sql = "SELECT item_name FROM item WHERE item_id =$item";
        $query = $connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $item_name = $res['item_name'];


        $sql = "SELECT category FROM item_category WHERE category_id =$item_category";
        $query = $connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $category_name  = $res['category'];

        if ($rec_id != '') {
            //Update
            $sql = "UPDATE tblissue2 SET item_id ='$item', item_category ='$item_category', issue_unit='$unit', plot_id ='$plot',issue_qnty='$qty' WHERE rec_id =$rec_id";
            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET item_qnty ='-$qty' WHERE entry_mode='I+' AND entry_id =$issue_id AND item_id=$item AND item_category =$item_category";
            $query = $connect->query($sql);

            if ($query == true) {
                $output['success'] = true;
            } else {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        } else {
            //Insert
            $sql = "SELECT Max(rec_id) as maxid FROM tblissue2";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $rid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $sql = "SELECT Max(seq_no) as maxno FROM tblissue2 WHERE issue_id=" . $issue_id;
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sno = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

            $sql = "INSERT INTO tblissue2(rec_id,issue_id,seq_no,item_id,item_category,issue_unit,issue_qnty,plot_id)
            VALUES('$rid','$issue_id','$sno','$item','$item_category','$unit','$qty','$plot')";
            $query = $connect->query($sql);

            $sql = "SELECT Max(stock_id) AS maxid FROM stock_register";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $str = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt)
                   VALUES('$sid','I+','$issue_id','$issue_date','$sno','$location','$item','$item_category','$unit','-$qty','0.00','0.00')";
            $query = $connect->query($str);
            if ($query == true) {
                $output['success'] = true;
            } else {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'get_mr') {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {
            $sql = "SELECT tblissue1.*, location_name, staff_name, DATE_FORMAT(issue_date,'%d-%m-%Y') as issue_date FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id  WHERE issue_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY issue_date, issue_id";
        } elseif ($_SESSION['stores_utype'] == "U") {
            $sql = "SELECT tblissue1.*, location_name, staff_name,DATE_FORMAT(issue_date,'%d-%m-%Y') as issue_date FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE tblissue1.location_id=" . $_SESSION['stores_locid'] . " AND issue_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY issue_date, issue_id";
        }

        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['issue_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeMaterialModal" onclick="removeMaterial(' . $row['issue_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            if ($row['issue_no'] > 999) {
                $issue_no = $row['issue_no'];
            } else {
                if ($row['issue_no'] > 99 && $row['issue_no'] < 1000) {
                    $issue_no = "0" . $row['issue_no'];
                } else {
                    if ($row['issue_no'] > 9 && $row['issue_no'] < 100) {
                        $issue_no = "00" . $row['issue_no'];
                    } else {
                        $issue_no = "000" . $row['issue_no'];
                    }
                }
            }



            $issue_no = $row['issue_prefix'] . '/' . $issue_no;

            $output['data'][] = array(
                $x,
                $issue_no,
                $row['issue_date'],
                $row['location_name'],
                $row['staff_name'],
                $row['issue_to'],

                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'get_item_list') {
        $issue_id = $_POST['issue_id'];

        $sql = "SELECT tblissue2.*,i.item_name,ic.category,u.unit_name,p.plot_name FROM `tblissue2` 
       JOIN item i ON i.item_id = tblissue2.item_id
       JOIN item_category ic ON ic.category_id = tblissue2.item_category
       JOIN unit u ON u.unit_id = tblissue2.issue_unit
       JOIN plot p ON p.plot_id = tblissue2.plot_id
       WHERE issue_id =$issue_id";
        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn edit" id=' . $row['rec_id'] . '><span class="glyphicon glyphicon-edit" style="color:blue"></span></button> <button type ="button" class="btn btn delete" id=' . $row['rec_id'] . '><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            $output['data'][] = array(
                $x,

                $row['item_name'] . ' ~~' . $row['category'],
                $row['issue_qnty'],
                $row['unit_name'],
                $row['plot_name'],
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'edit_material_issue') {
        $issue_id = $_POST['issue_id'];
        $sql = "SELECT * FROM `tblissue1` 
        JOIN location l ON l.location_id = tblissue1.location_id
        JOIN staff s on s.staff_id = tblissue1.issue_by
        WHERE tblissue1.issue_id=$issue_id";
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'delete_item') {
        $rec_id = $_POST['rec_id'];

        $sql = "SELECT issue_id, seq_no FROM tblissue2 WHERE rec_id =$rec_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);

        $issue_id = $row['issue_id'];
        $seq_no = $row['seq_no'];

        $sql = "DELETE FROM tblissue2 WHERE rec_id=$rec_id";
        $query = $connect->query($sql);

        $str = "DELETE FROM stock_register WHERE entry_mode='I+' AND entry_id=$issue_id AND seq_no='$seq_no'";
        $query = $connect->query($str);

        if ($query == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'delete_record') {
        $issue_id = $_POST['issue_id'];

        $sql = "SELECT * FROM tblissue1 WHERE issue_id =$issue_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $issue_date = $row['issue_date'];

        $sql = "DELETE FROM tblissue2 WHERE issue_id=$issue_id";
        $query = $connect->query($sql);

        $sql1 = "DELETE FROM tblissue1 WHERE issue_id =$issue_id";
        $query1 = $connect->query($sql1);

        $str = "DELETE FROM stock_register WHERE entry_mode='I+' AND entry_id=$issue_id AND entry_date='$issue_date'";
        $query = $connect->query($str);

        if ($query1 == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'get_edit_data') {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT tblissue2.*,i.item_name,ic.category,u.unit_name,p.plot_name FROM `tblissue2` 
        JOIN item i ON i.item_id = tblissue2.item_id
        JOIN item_category ic ON ic.category_id = tblissue2.item_category
        JOIN unit u ON u.unit_id = tblissue2.issue_unit
        JOIN plot p ON p.plot_id = tblissue2.plot_id
        WHERE rec_id=$rec_id";
        $query = $connect->query($sql);
        $connect->close();
        $output = mysqli_fetch_assoc($query);
        echo json_encode($output);
    }
}
