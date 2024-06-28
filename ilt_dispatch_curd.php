<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if($action =='save_ilt'){
        $ilt_id = $_POST['ilt_id'];
        $ilt_date = $_POST['ilt_date'];
        $dispatch = $_POST['dispatch'];
        $destination = $_POST['destination'];
        $dispatch_by = $_POST['dispatch_by'];
        $dispatch_mode = $_POST['dispatch_mode'];
        $vehicle_no = $_POST['vehicle_no'];
     
        $sql = "SELECT Max(ilt_id) as maxid FROM tblilt1";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $maxid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);

        $sql = "SELECT Max(ilt_no) as maxno FROM tblilt1 WHERE despatch_from=".$dispatch." AND (ilt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')";
      
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ilt_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

        $sql ="SELECT * FROM location WHERE location_id =$dispatch";
        $query =$connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $ilt_prefix = $row['location_prefix'];
     
        if($ilt_id!=''){


            $sql ="SELECT * FROM tblilt1 WHERE ilt_id =$ilt_id";
            $query =$connect->query($sql);
            $row =mysqli_fetch_assoc($query);
            $ilt_no = $row['ilt_no'];

            if($dispatch!=$row['despatch_from']){
                $sql ="SELECT Max(ilt_no) as maxno FROM tblilt1 WHERE despatch_from=$dispatch AND (ilt_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."')";
                $query =$connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $ino = $row['maxno']+1;
            }else{
                $ino = $ilt_no;
            }
              $sql ="UPDATE tblilt1 SET ilt_no='$ino',ilt_date='$ilt_date',despatch_from='$dispatch',ilt_prefix='$ilt_prefix',receive_at='$destination',despatch_mode='$dispatch_mode', vehicle_num='$vehicle_no' WHERE ilt_id=$ilt_id";
     
              $query =$connect->query($sql);

              $sql ="UPDATE stock_register SET entry_date='$ilt_date',location_id=$dispatch WHERE entry_mode='T-' AND entry_id=$ilt_id";
              $query =$connect->query($sql);
              $sql ="SELECT ilt_no, ilt_prefix FROM tblilt1 WHERE ilt_id =$ilt_id";
             
              $query =$connect->query($sql);
              $row =mysqli_fetch_assoc($query);
              if ($row['ilt_no'] > 99 && $row['ilt_no'] < 1000)
              {
                  $ilt_no = "0" . $row['ilt_no'];
              }
              else
              {
                  if ($row['ilt_no'] > 9 && $row['ilt_no'] < 100)
                  {
                      $ilt_no = "00" . $row['ilt_no'];
                  }
                  else
                  {
                      $ilt_no = "000" . $row['ilt_no'];
                  }
              }
              $ilt_no = $row['ilt_prefix'] . '/' . $ilt_no;
              if($query==true){
                  $output['success']=true;
                  $output['ilt_id']=$ilt_id;
                  $output['ilt_no']=$ilt_no;
              }else{
                  $output['success']=false;
              }
$connect->close();
echo json_encode($output);
        }else{
            //Insert

           $sql ="INSERT INTO tblilt1(ilt_id,ilt_date,ilt_no,ilt_prefix,despatch_from,receive_at,despatch_by,despatch_mode,vehicle_num) 
           VALUES('$maxid','$ilt_date','$ilt_no','$ilt_prefix','$dispatch','$destination','$dispatch_by','$dispatch_mode','$vehicle_no')";
          
           $query= $connect->query($sql);

           $last_id ="SELECT ilt_id FROM tblilt1 ORDER BY ilt_id DESC limit 0 ,1";
           $query = $connect->query($last_id);
           $row = mysqli_fetch_assoc($query);
           
           $id = $row['ilt_id'];

           $sql ="SELECT ilt_no, ilt_prefix FROM tblilt1 WHERE ilt_id =$id";
           $query =$connect->query($sql);
           $row =mysqli_fetch_assoc($query);
           if ($row['ilt_no'] > 99 && $row['ilt_no'] < 1000)
           {
               $ilt_no = "0" . $row['ilt_no'];
           }
           else
           {
               if ($row['ilt_no'] > 9 && $row['ilt_no'] < 100)
               {
                   $ilt_no = "00" . $row['ilt_no'];
               }
               else
               {
                   $ilt_no = "000" . $row['ilt_no'];
               }
           }
           $ilt_no = $row['ilt_prefix'] . '/' . $ilt_no;
           if($query==true){
               $output['success']=true;
               $output['ilt_id']=$id;
               $output['ilt_no']=$ilt_no;
           }else{
               $output['success']=false;
           }

           $connect->close();
           echo json_encode($output);
        }
    }elseif ($action=='save_item') {
        $rec_id = $_POST['rec_id'];
        $ilt_id = $_POST['ilt_id'];
        $item=$_POST['item'];
        $item_category =$_POST['item_category'];
        $unit =$_POST['unit'];
        $qty =$_POST['qty'];
      

        $sql ="SELECT * FROM tblilt1 WHERE ilt_id =$ilt_id";
        $query = $connect->query($sql);
        $row =mysqli_fetch_assoc($query);
        $ilt_date =$row['ilt_date'];
        $dispatch = $row['despatch_from'];

        $sql ="SELECT location_name FROM location WHERE location_id =$dispatch";
        $query =$connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $location_name = $res['location_name'];

        $sql ="SELECT item_name FROM item WHERE item_id =$item";
        $query =$connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $item_name = $res['item_name'];


        $sql ="SELECT category FROM item_category WHERE category_id =$item_category";
        $query =$connect->query($sql);
        $res = mysqli_fetch_assoc($query);
        $category_name  = $res['category'];

        if($rec_id!=''){
           //Update
           $sql ="UPDATE tblilt2 SET item_id ='$item', item_category ='$item_category', unit_id='$unit',despatch_qnty='$qty' WHERE rec_id =$rec_id";
           $query =$connect->query($sql);

           $sql ="UPDATE stock_register SET item_qnty ='-$qty' WHERE entry_mode='T-' AND entry_id =$ilt_id AND item_id=$item AND item_category =$item_category";
           $query =$connect->query($sql); 
               
           if($query==true){
            $output['success']=true;

        }else {
            $output['success']=false;
        }
           $connect->close();
           echo json_encode($output);
        }else{
            //Insert
            $sql = "SELECT Max(rec_id) as maxid FROM tblilt2";
            $query =$connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $rid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
            $sql = "SELECT Max(seq_no) as maxno FROM tblilt2 WHERE ilt_id=".$ilt_id;
            $query =$connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
    
            $sql ="INSERT INTO tblilt2(rec_id,ilt_id,seq_no,item_id,item_category,unit_id,despatch_qnty)
            VALUES('$rid','$ilt_id','$sno','$item','$item_category','$unit','$qty')";
           
            $query=$connect->query($sql);
    
              $sql ="SELECT Max(stock_id) AS maxid FROM stock_register";
             $query =$connect->query($sql);
             $row = mysqli_fetch_assoc($query);
             $sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
            $str ="INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt)
                   VALUES('$sid','T-','$ilt_id','$ilt_date','$sno','$dispatch','$item','$item_category','$unit','-$qty','0.00','0.00')";
                   $query =$connect->query($str); 
            if($query==true){
                $output['success']=true;
    
            }else {
                $output['success']=false;
            }
            $connect->close();
            echo json_encode($output);
        }
    }elseif ($action=='get_ilt') {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status= $_POST['status'];
        if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
            $sql ="SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation, staff_name,
            DATE_FORMAT(ilt_date,'%d-%m-%Y') as ilt_date,despatch_status FROM tblilt1 
            INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id 
            INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id 
            INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id 
            WHERE despatch_status='$status' AND ilt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
            ORDER BY sourceLocation,ilt_date,ilt_id";
        }elseif($_SESSION['stores_utype']=="U"){
            $sql ="SELECT tblilt1.*, source.location_name AS sourceLocation, destination.location_name AS destinationLocation,staff_name,
            DATE_FORMAT(ilt_date,'%d-%m-%Y') as ilt_date,despatch_status FROM tblilt1 
            INNER JOIN location AS source ON tblilt1.despatch_from = source.location_id 
            INNER JOIN location AS destination ON tblilt1.receive_at = destination.location_id
            INNER JOIN staff ON tblilt1.despatch_by = staff.staff_id 
            WHERE despatch_from='".$_SESSION['stores_locid']."' 
            AND despatch_status='$status' AND ilt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
            ORDER BY sourceLocation,ilt_date,ilt_id";
        }
      
        $query = $connect->query($sql);
       
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $editButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['ilt_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
             $deleteButton=       '<button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeMaterialModal" onclick="removeMaterial(' . $row['ilt_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span>';
            $sentButton =' </button> <button type ="button"  class="btn btn sent" id="'.$row['ilt_id'].'"><span class="glyphicon glyphicon-send" style="color:black"></span></button>';
            if ($row['ilt_no'] > 999)
            {
                $ilt_no = $row['ilt_no'];
            }
            else
            {
                if ($row['ilt_no'] > 99 && $row['ilt_no'] < 1000)
                {
                    $ilt_no = "0" . $row['ilt_no'];
                }
                else
                {
                    if ($row['ilt_no'] > 9 && $row['ilt_no'] < 100)
                    {
                        $ilt_no = "00" . $row['ilt_no'];
                    }
                    else
                    {
                        $ilt_no = "000" . $row['ilt_no'];
                    }
                }
            }



            $ilt_no = $row['ilt_prefix'] . '/' . $ilt_no;


            if($row['despatch_status']=='U'){
                $send =$sentButton;
                $edit =$editButton;
            }else{
                $send ='';
                $edit ='';
            }

            
            $output['data'][] = array(
                $x,
                $ilt_no,
                $row['ilt_date'],
                $row['sourceLocation'],
                $row['destinationLocation'],
                $row['staff_name'],
                $edit .' '.$deleteButton .' '.$send

            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }elseif ($action=='get_item_list') {
       $ilt_id =$_POST['ilt_id'];

       $sql ="SELECT tblilt2.*,i.item_name,ic.category,u.unit_name FROM `tblilt2` JOIN item i ON i.item_id = tblilt2.item_id 
       JOIN item_category ic ON ic.category_id = tblilt2.item_category JOIN unit u ON u.unit_id = tblilt2.unit_id WHERE ilt_id =$ilt_id";
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
            $row['despatch_qnty'].' '. $row['unit_name'],
           
            $actionButton
        );
        $x++;
       }
       $connect->close();
       echo json_encode($output);
    }elseif ($action=='edit_ilt_dispatch') {
        $ilt_id=$_POST['ilt_id'];
        $sql ="SELECT tblilt1.*,l1.location_name as dispatch_location,l2.location_name as received_location,s.staff_name FROM `tblilt1` JOIN location l1 ON l1.location_id = tblilt1.despatch_from JOIN location l2 ON l2.location_id = tblilt1.receive_at JOIN staff s on s.staff_id = tblilt1.despatch_by where tblilt1.ilt_id=$ilt_id";
      //  print_r($sql);die;
        $query=$connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }elseif ($action=='delete_item') {
        $rec_id =$_POST['rec_id'];

        $sql ="SELECT ilt_id, seq_no FROM tblilt2 WHERE rec_id =$rec_id";
        $query =$connect->query($sql);
        $row =mysqli_fetch_assoc($query);

        $ilt_id = $row['ilt_id'];
        $seq_no =$row['seq_no'];

        $sql ="DELETE FROM tblilt2 WHERE rec_id=$rec_id";
        $query=$connect->query($sql);

        $str ="DELETE FROM stock_register WHERE entry_mode='T-' AND entry_id=$ilt_id AND seq_no='$seq_no'";
        $query =$connect->query($str); 

        if($query==true){
            $output['success']=true;
        }else{
            $output['success']=false;

        }
        $connect->close();
        echo json_encode($output);
    }elseif ($action=='delete_record') {
       $ilt_id = $_POST['ilt_id'];

       $sql ="SELECT * FROM tblilt1 WHERE ilt_id =$ilt_id";
       $query =$connect->query($sql);
       $row = mysqli_fetch_assoc($query);
       $ilt_date =$row['ilt_date'];

       $sql ="DELETE FROM tblilt2 WHERE ilt_id=$ilt_id";
       $query= $connect->query($sql);

       $sql1 ="DELETE FROM tblilt1 WHERE ilt_id =$ilt_id";
       $query1 =$connect->query($sql1);

       $str ="DELETE FROM stock_register WHERE entry_mode='T-' AND entry_id=$ilt_id AND entry_date='$ilt_date'";
       $query =$connect->query($str);
       
       if($query1==true){
           $output['success']=true;
           $output['messages']="Delete Record Successfully...!!";
       }else{
        $output['success']=false;
        $output['messages']="Failed To Delete Record...!!";
       }
       $connect->close();
       echo json_encode($output);
    }elseif($action=='get_edit_data'){
        $rec_id = $_POST['rec_id'];
        $sql ="SELECT tblilt2.*, i.item_name,ic.category,u.unit_name FROM `tblilt2` JOIN item i ON i.item_id = tblilt2.item_id JOIN item_category ic ON ic.category_id = tblilt2.item_category JOIN unit u ON u.unit_id = tblilt2.unit_id WHERE rec_id=$rec_id";
        $query= $connect->query($sql);
        $connect->close();
        $output = mysqli_fetch_assoc($query);
        echo json_encode($output);
    }elseif($action=='sent_ilt'){
        $ilt_id =$_POST['ilt_id'];
        $sql ="UPDATE tblilt1 SET despatch_status ='S' WHERE ilt_id =$ilt_id";
        $query =$connect->query($sql);
        if($query==true){
            $output['success']=true;
        }else{
            $output['success']=false;
        }
        $connect->close();
        echo json_encode($output);
    }
}
?>