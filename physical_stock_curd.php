<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if ($action == 'save_ps')
    {
        $ps_id = $_POST['ps_id'];
        $ps_date = $_POST['ps_date'];
        $location = $_POST['location'];
        $status = $_POST['status'];
        if ($status == 'I')
        {
            $entry_mode = 'P+';
        }
        else
        {
            $entry_mode = 'P-';
        }

        $sql = "SELECT Max(ps_id) as maxid FROM tblpstock";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $maxid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);

        $sql = "SELECT Max(ps_no) as maxno FROM tblpstock WHERE location_id=" . $location . " AND (ps_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";

        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ps_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

        $sql = "SELECT * FROM location WHERE location_id =$location";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ps_prefix = $row['location_prefix'];

        if ($ps_id != '')
        {

            $sql = "SELECT * FROM tblpstock WHERE ps_id =$ps_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $ps_no = $row['ps_no'];

            if ($location != $row['location_id'])
            {
                $sql = "SELECT Max(ps_no) as maxno FROM tblpstock WHERE location_id=$location AND (ps_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
                $query = $connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $ino = $row['maxno'] + 1;
            }
            else
            {
                $ino = $ps_no;
            }
            $sql = "UPDATE tblpstock SET ps_no='$ino',ps_date='$ps_date',location_id='$location',ps_prefix='$ps_prefix', ps_type='$status' WHERE ps_id=$ps_id";

            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET entry_date='$ps_date',location_id=$location WHERE entry_mode='$entry_mode' AND entry_id=$ps_id";
            $query = $connect->query($sql);
            $sql = "SELECT ps_no, ps_prefix FROM tblpstock WHERE ps_id =$ps_id";

            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['ps_no'] > 99 && $row['ps_no'] < 1000)
            {
                $ps_no = "0" . $row['ps_no'];
            }
            else
            {
                if ($row['ps_no'] > 9 && $row['ps_no'] < 100)
                {
                    $ps_no = "00" . $row['ps_no'];
                }
                else
                {
                    $ps_no = "000" . $row['ps_no'];
                }
            }
            $ps_no = $row['ps_prefix'] . '/' . $ps_no;
            if ($query == true)
            {
                $output['success'] = true;
                $output['ps_id'] = $ps_id;
                $output['ps_no'] = $ps_no;
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
            $sql = "INSERT INTO tblpstock(ps_id,ps_date,ps_no,ps_prefix,ps_type,location_id) 
           VALUES('$maxid','$ps_date','$ps_no','$ps_prefix','$status','$location')";

            $query = $connect->query($sql);

            $last_id = "SELECT ps_id FROM tblpstock ORDER BY ps_id DESC limit 0 ,1";
            $query = $connect->query($last_id);
            $row = mysqli_fetch_assoc($query);

            $id = $row['ps_id'];

            $sql = "SELECT ps_no, ps_prefix FROM tblpstock WHERE ps_id =$id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            if ($row['ps_no'] > 99 && $row['ps_no'] < 1000)
            {
                $ps_no = "0" . $row['ps_no'];
            }
            else
            {
                if ($row['ps_no'] > 9 && $row['ps_no'] < 100)
                {
                    $ps_no = "00" . $row['ps_no'];
                }
                else
                {
                    $ps_no = "000" . $row['ps_no'];
                }
            }
            $ps_no = $row['ps_prefix'] . '/' . $ps_no;
            if ($query == true)
            {
                $output['success'] = true;
                $output['ps_id'] = $id;
                $output['ps_no'] = $ps_no;
            }
            else
            {
                $output['success'] = false;
            }

            $connect->close();
            echo json_encode($output);
        }
    }
    elseif ($action == 'save_item')
    {
        $rec_id = $_POST['rec_id'];
        $ps_id = $_POST['ps_id'];
        $item = $_POST['item'];
        $item_category = $_POST['item_category'];
        $unit = $_POST['unit'];
        $qty = $_POST['qty'];
      
        $status =$_POST['status'];
        if($status=='I'){
            $ps_qty =$qty;
            $entry_mode ='P+';
        }else{
            $ps_qty = '-'.$qty;
            $entry_mode ='P-';
        }

        $sql = "SELECT * FROM tblpstock WHERE ps_id =$ps_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ps_date = $row['ps_date'];
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
        $category_name = $res['category'];

        if ($rec_id != '')
        {
            //Update
            $sql = "UPDATE tblpstock_item SET item_id ='$item', item_category ='$item_category',unit_id='$unit',ps_qnty='$qty' WHERE rec_id =$rec_id";
            $query = $connect->query($sql);

            $sql = "UPDATE stock_register SET item_qnty ='$ps_qty' WHERE entry_mode='$entry_mode' AND entry_id =$ps_id AND item_id=$item AND item_category =$item_category";
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
            $sql = "SELECT Max(rec_id) as maxid FROM tblpstock_item";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $rid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $sql = "SELECT Max(seq_no) as maxno FROM tblpstock_item WHERE ps_id=" . $ps_id;
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sno = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

            $sql = "INSERT INTO tblpstock_item(rec_id,ps_id,seq_no,item_id,item_category,unit_id,ps_qnty)
            VALUES('$rid','$ps_id','$sno','$item','$item_category','$unit','$ps_qty')";

            $query = $connect->query($sql);

            $sql = "SELECT Max(stock_id) AS maxid FROM stock_register";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            $str = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt)
                   VALUES('$sid','$entry_mode','$ps_id','$ps_date','$sno','$location','$item','$item_category','$unit','$ps_qty','0.00','0.00')";
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
    elseif ($action=='get_item_list') 
    {
        $ps_id =$_POST['ps_id'];
 
        $sql ="SELECT tbl.*,i.item_name,ic.category,u.unit_name FROM `tblpstock_item` tbl
        JOIN item i ON i.item_id = tbl.item_id
        JOIN item_category ic ON ic.category_id = tbl.item_category
        JOIN unit u ON u.unit_id = tbl.unit_id
        WHERE tbl.ps_id =$ps_id";
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
            
             $row['item_name'].' ~~'.$row['category'],
             $row['ps_qnty'].' '. $row['unit_name'],
            
             $actionButton
         );
         $x++;
        }
        $connect->close();
        echo json_encode($output);
     }elseif ($action=='get_pstock') 
    {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
      
        if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
            $sql ="SELECT tblpstock.*, location_name FROM tblpstock 
            INNER JOIN location ON tblpstock.location_id = location.location_id 
            WHERE ps_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY location_name, ps_date, ps_no";
        }elseif($_SESSION['stores_utype']=="U"){
            $sql ="SELECT tblpstock.*, location_name FROM tblpstock 
            INNER JOIN location ON tblpstock.location_id = location.location_id 
            WHERE tblpstock.location_id=".$_SESSION['stores_locid']." AND ps_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
            ORDER BY location_name, ps_date, ps_no";
        }
     
        $query = $connect->query($sql);
       
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['ps_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeMaterialModal" onclick="removeMaterial(' . $row['ps_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>'; 
            if ($row['ps_no'] > 999)
            {
                $ps_no = $row['ps_no'];
            }
            else
            {
                if ($row['ps_no'] > 99 && $row['ps_no'] < 1000)
                {
                    $ps_no = "0" . $row['ps_no'];
                }
                else
                {
                    if ($row['ps_no'] > 9 && $row['ps_no'] < 100)
                    {
                        $ps_no = "00" . $row['ps_no'];
                    }
                    else
                    {
                        $ps_no = "000" . $row['ps_no'];
                    }
                }
            }



            $ps_no = $row['ps_prefix'] . '/' . $ps_no;
            $output['data'][] = array(
                $x,
                $ps_no,
                $row['ps_date'],
               $row['location_name'],
             
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }elseif ($action == 'delete_record')
    {
        $ps_id = $_POST['ps_id'];

        $sql = "SELECT * FROM tblpstock WHERE ps_id =$ps_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ps_date = $row['ps_date'];
        $entry_mode = $row['ps_type'];

        if($entry_mode=='I'){
            $mode ='P+';
        }else{
            $mode ='P-';
        }

        $sql = "DELETE FROM tblpstock_item WHERE ps_id=$ps_id";
        $query = $connect->query($sql);

        $sql1 = "DELETE FROM tblpstock WHERE ps_id =$ps_id";
        $query1 = $connect->query($sql1);

        $str = "DELETE FROM stock_register WHERE entry_mode='$mode' AND entry_id=$ps_id AND entry_date='$ps_date'";
        $query = $connect->query($str);

        if ($query1 == true)
        {
            $output['success'] = true;
            $output['messages'] = "Delete Record Successfully...!!";
        }
        else
        {
            $output['success'] = false;
            $output['messages'] = "Failed To Delete Record...!!";
        }
        $connect->close();
        echo json_encode($output);
    }    elseif ($action =='edit_physical_stock')
    {
        $ps_id = $_POST['ps_id'];
        $sql = "SELECT tblpstock.*,l.location_name FROM `tblpstock` JOIN location l ON l.location_id = tblpstock.location_id WHERE ps_id=$ps_id";
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }elseif($action=='get_edit_data')
    {
        $rec_id = $_POST['rec_id'];
        $sql ="SELECT ti.*,i.item_name,ic.category,u.unit_name FROM `tblpstock_item` ti 
        JOIN item i ON i.item_id = ti.item_id JOIN item_category ic ON ic.category_id = ti.item_category 
        JOIN unit u ON u.unit_id = ti.unit_id WHERE rec_id=$rec_id";
        $query= $connect->query($sql);
        $connect->close();
        $output = mysqli_fetch_assoc($query);
        echo json_encode($output);
    }


 
} ?>