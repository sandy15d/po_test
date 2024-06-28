<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if($action=='get_xlt')
    {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
       
        if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
            $sql ="SELECT tblxlt.*, location_name, staff_name FROM tblxlt 
            INNER JOIN location ON tblxlt.location_id = location.location_id 
            INNER JOIN staff ON tblxlt.tfr_by = staff.staff_id WHERE xlt_type='R' 
            AND xlt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY location_name,xlt_date,xlt_id";
        }elseif($_SESSION['stores_utype']=="U"){
            $sql ="SELECT tblxlt.*, location_name, staff_name FROM tblxlt 
            INNER JOIN location ON tblxlt.location_id = location.location_id 
            INNER JOIN staff ON tblxlt.tfr_by = staff.staff_id 
            WHERE location_id=".$_SESSION['stores_locid']." AND xlt_type='R' 
            AND xlt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY location_name,xlt_date,xlt_id";
        }
   
        $query = $connect->query($sql);
       
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn edit" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['xlt_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
           
            if ($row['xlt_no'] > 999)
            {
                $xlt_no = $row['xlt_no'];
            }
            else
            {
                if ($row['xlt_no'] > 99 && $row['xlt_no'] < 1000)
                {
                    $xlt_no = "0" . $row['xlt_no'];
                }
                else
                {
                    if ($row['xlt_no'] > 9 && $row['xlt_no'] < 100)
                    {
                        $xlt_no = "00" . $row['xlt_no'];
                    }
                    else
                    {
                        $xlt_no = "000" . $row['xlt_no'];
                    }
                }
            }
            $xlt_no = $row['xlt_prefix'] . '/' . $xlt_no;
            $output['data'][] = array(
                $x,
                $xlt_no,
                $row['xlt_date'],
                $row['tfr_location'],
                $row['location_name'],
                $row['staff_name'],
              $actionButton

            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }elseif ($action=='save_xlt')
    {
        $xlt_id=$_POST['xlt_id'];
        $xlt_date=$_POST['xlt_date'];
        $dispatch_from=$_POST['dispatch_from'];
        $receive_at=$_POST['receive_at'];
        $received_by=$_POST['received_by'];
        $received_mode=$_POST['received_mode'];
        $vehicle_no=$_POST['vehicle_no'];
        
        $sql = "SELECT Max(xlt_id) as maxid FROM tblxlt";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $maxid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);

        $sql = "SELECT Max(xlt_no) as maxno FROM tblxlt WHERE location_id=" . $receive_at . " AND xlt_type='R' AND (xlt_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";

        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $xlt_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

        $sql = "SELECT * FROM location WHERE location_id =$receive_at";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $xlt_prefix = $row['location_prefix'];

        if ($xlt_id != '')
        {

            $sql = "SELECT * FROM tblxlt WHERE xlt_id =$xlt_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $xlt_no = $row['xlt_no'];

            if ($receive_at != $row['location_id'])
            {
                $sql = "SELECT Max(xlt_no) as maxno FROM tblxlt WHERE location_id=$receive_at  AND xlt_type='R' AND (xlt_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
                $query = $connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $ino = $row['maxno'] + 1;
            }
            else
            {
                $ino = $xlt_no;
            }
            $sql = "UPDATE tblxlt SET xlt_no='$ino',xlt_date='$xlt_date',location_id='$receive_at',xlt_prefix='$xlt_prefix',tfr_location='$dispatch_from',despatch_mode='$received_mode', vehicle_num='$vehicle_no' WHERE xlt_id=$xlt_id";

            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET entry_date='$xlt_date',location_id=$receive_at WHERE entry_mode='X+' AND entry_id=$xlt_id";
            $query = $connect->query($sql);
            $sql = "SELECT xlt_no, xlt_prefix FROM tblxlt WHERE xlt_id =$xlt_id";

            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['xlt_no'] > 99 && $row['xlt_no'] < 1000)
            {
                $xlt_no = "0" . $row['xlt_no'];
            }
            else
            {
                if ($row['xlt_no'] > 9 && $row['xlt_no'] < 100)
                {
                    $xlt_no = "00" . $row['xlt_no'];
                }
                else
                {
                    $xlt_no = "000" . $row['xlt_no'];
                }
            }
            $xlt_no = $row['xlt_prefix'] . '/' . $xlt_no;
            if ($query == true)
            {
                $output['success'] = true;
                $output['xlt_id'] = $xlt_id;
                $output['xlt_no'] = $xlt_no;
            }
            else
            {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }
        else
        {
            //Insert
            $sql = "INSERT INTO tblxlt(xlt_id,xlt_date,xlt_no,xlt_prefix,xlt_type,location_id,tfr_location,tfr_by,despatch_mode,vehicle_num) 
           VALUES('$maxid','$xlt_date','$xlt_no','$xlt_prefix','R','$receive_at','$dispatch_from','$received_by','$received_mode','$vehicle_no')";

            $query = $connect->query($sql);

            $last_id = "SELECT xlt_id FROM tblxlt ORDER BY xlt_id DESC limit 0 ,1";
            $query = $connect->query($last_id);
            $row = mysqli_fetch_assoc($query);

            $id = $row['xlt_id'];

            $sql = "SELECT xlt_no, xlt_prefix FROM tblxlt WHERE xlt_id =$id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['xlt_no'] > 99 && $row['xlt_no'] < 1000)
            {
                $xlt_no = "0" . $row['xlt_no'];
            }
            else
            {
                if ($row['xlt_no'] > 9 && $row['xlt_no'] < 100)
                {
                    $xlt_no = "00" . $row['xlt_no'];
                }
                else
                {
                    $xlt_no = "000" . $row['xlt_no'];
                }
            }
            $xlt_no = $row['xlt_prefix'] . '/' . $xlt_no;
            if ($query == true)
            {
                $output['success'] = true;
                $output['xlt_id'] = $id;
                $output['xlt_no'] = $xlt_no;
            }
            else
            {
                $output['success'] = false;
            }

            $connect->close();
            echo json_encode($output);
        }
    }    elseif ($action == 'save_item')
    {
        $rec_id = $_POST['rec_id'];
        $xlt_id = $_POST['xlt_id'];
        $item = $_POST['item'];
        $item_category = $_POST['item_category'];
        $unit = $_POST['unit'];
        $qty = $_POST['qty'];

        $sql = "SELECT * FROM tblxlt WHERE xlt_id =$xlt_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $xlt_date = $row['xlt_date'];
        $receive_at = $row['location_id'];

        $sql = "SELECT location_name FROM location WHERE location_id =$receive_at";
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
        $category_name = $res['category'];

        if ($rec_id != '')
        {
            //Update
            $sql = "UPDATE tblxlt_item SET item_id ='$item', item_category ='$item_category', unit_id='$unit',xlt_qnty='$qty' WHERE rec_id =$rec_id";
            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET item_qnty ='$qty' WHERE entry_mode='X+' AND entry_id =$xlt_id AND item_id=$item AND item_category =$item_category";
            $query = $connect->query($sql);

            if ($query == true)
            {
                $output['success'] = true;

            }
            else
            {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }
        else
        {
            //Insert
            $sql = "SELECT Max(rec_id) as maxid FROM tblxlt_item";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $rid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $sql = "SELECT Max(seq_no) as maxno FROM tblxlt_item WHERE xlt_id=" . $xlt_id;
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sno = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

            $sql = "INSERT INTO tblxlt_item(rec_id,xlt_id,seq_no,item_id,item_category,unit_id,xlt_qnty)
            VALUES('$rid','$xlt_id','$sno','$item','$item_category','$unit','$qty')";

            $query = $connect->query($sql);

            $sql = "SELECT Max(stock_id) AS maxid FROM stock_register";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $str = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt)
                   VALUES('$sid','X+','$xlt_id','$xlt_date','$sno','$receive_at','$item','$item_category','$unit','$qty','0.00','0.00')";
            $query = $connect->query($str);
            if ($query == true)
            {
                $output['success'] = true;

            }
            else
            {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }
    }elseif ($action == 'get_item_list')
    {
        $xlt_id = $_POST['xlt_id'];

        $sql = "SELECT tblxlt_item.*,i.item_name,ic.category,u.unit_name FROM `tblxlt_item` JOIN item i ON i.item_id = tblxlt_item.item_id 
       JOIN item_category ic ON ic.category_id = tblxlt_item.item_category JOIN unit u ON u.unit_id = tblxlt_item.unit_id WHERE xlt_id =$xlt_id";
        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn edit" id=' . $row['rec_id'] . '><span class="glyphicon glyphicon-edit" style="color:blue"></span></button> <button type ="button" class="btn btn delete" id=' . $row['rec_id'] . '><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            $output['data'][] = array(
                $x,

                $row['item_name'] . ' ~~' . $row['category'],
                $row['xlt_qnty'] . ' ' . $row['unit_name'],

                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }    elseif ($action == 'edit_xlt_receive')
    {
        $xlt_id = $_POST['xlt_id'];
        $sql = "SELECT tblxlt.*,l.location_name as receive_at,s.staff_name FROM tblxlt
        INNER JOIN location l ON l.location_id = tblxlt.location_id
        INNER JOIN staff s ON s.staff_id = tblxlt.tfr_by
        WHERE xlt_id=$xlt_id";
      
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }elseif ($action == 'get_edit_data')
    {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT tblxlt_item.*, i.item_name,ic.category,u.unit_name FROM `tblxlt_item` JOIN item i ON i.item_id = tblxlt_item.item_id JOIN item_category ic ON ic.category_id = tblxlt_item.item_category JOIN unit u ON u.unit_id = tblxlt_item.unit_id WHERE rec_id=$rec_id";
        $query = $connect->query($sql);
        $connect->close();
        $output = mysqli_fetch_assoc($query);
        echo json_encode($output);
    }elseif ($action == 'delete_item')
    {
        $rec_id = $_POST['rec_id'];

        $sql = "SELECT xlt_id, seq_no FROM tblxlt_item WHERE rec_id =$rec_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);

        $xlt_id = $row['xlt_id'];
        $seq_no = $row['seq_no'];

        $sql = "DELETE FROM tblxlt_item WHERE rec_id=$rec_id";
        $query = $connect->query($sql);

        $str = "DELETE FROM stock_register WHERE entry_mode='X+' AND entry_id=$xlt_id AND seq_no='$seq_no'";
        $query = $connect->query($str);

        if ($query == true)
        {
            $output['success'] = true;
        }
        else
        {
            $output['success'] = false;

        }
        $connect->close();
        echo json_encode($output);
    }
}
?>