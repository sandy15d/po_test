<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if($action=='get_mr'){
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        if($_SESSION['stores_utype']=="A" || $_SESSION['stores_utype']=="S"){
            $sql ="SELECT tblissue1.*, location_name, staff_name, DATE_FORMAT(issue_date,'%d-%m-%Y') as issue_date FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id  WHERE issue_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY issue_date, issue_id";
        }elseif($_SESSION['stores_utype']=="U"){
            $sql ="SELECT tblissue1.*, location_name, staff_name,DATE_FORMAT(issue_date,'%d-%m-%Y') as issue_date FROM tblissue1 INNER JOIN location ON tblissue1.location_id = location.location_id INNER JOIN staff ON tblissue1.issue_by = staff.staff_id WHERE tblissue1.location_id=".$_SESSION['stores_locid']." AND issue_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY issue_date, issue_id";
        }
      
        $query = $connect->query($sql);
       
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['issue_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
            if ($row['issue_no'] > 999)
            {
                $issue_no = $row['issue_no'];
            }
            else
            {
                if ($row['issue_no'] > 99 && $row['issue_no'] < 1000)
                {
                    $issue_no = "0" . $row['issue_no'];
                }
                else
                {
                    if ($row['issue_no'] > 9 && $row['issue_no'] < 100)
                    {
                        $issue_no = "00" . $row['issue_no'];
                    }
                    else
                    {
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
    }elseif ($action=='get_item_list') {
        $issue_id =$_POST['issue_id'];
 
        $sql ="SELECT tblissue2.*,i.item_name,ic.category,u.unit_name,p.plot_name FROM `tblissue2` 
        JOIN item i ON i.item_id = tblissue2.item_id
        JOIN item_category ic ON ic.category_id = tblissue2.item_category
        JOIN unit u ON u.unit_id = tblissue2.issue_unit
        JOIN plot p ON p.plot_id = tblissue2.plot_id
        WHERE issue_id =$issue_id";
        $query = $connect->query($sql);
        
        $output = array(
            'data' => array()
        );

        while ($row = $query->fetch_assoc())
        {
       
         $output['data'][] = array(

            'item_name'=> $row['item_name'].' ~~'.$row['category'],
            'issue_qty'=> $row['issue_qnty'],
            'unit_name'=> $row['unit_name'],
            'unit'=>$row['issue_unit'],
            'plot_name'=> $row['plot_name'],
            'return_qnty'=> $row['return_qnty'],
            'item_id'=>$row['item_id'],
            'item_category'=>$row['item_category'],
            'rec_id'=>$row['rec_id'],
           'issue_id'=>$row['issue_id']
         );
      
        }
        $connect->close();
        echo json_encode($output);
     }elseif ($action=='return_item') {
       $issue_id = $_POST['issue_id'];
       $list_array = $_POST['list_array'];

       $sql ="SELECT * FROM tblissue1 WHERE issue_id=$issue_id";
       $query =$connect->query($sql);
       $row =mysqli_fetch_assoc($query);
       $issue_date=$row['issue_date'];
       $location =$row['location_id'];
       foreach($list_array as $lst_arr){
           $sql ="UPDATE tblissue2 SET return_qnty='".$lst_arr['return_qty']."', return_unit ='".$lst_arr['unit']."' WHERE issue_id =$issue_id AND item_id='".$lst_arr['item_id']."' AND item_category='".$lst_arr['item_category']."'";
           $query=$connect->query($sql);

           $sql ="SELECT seq_no FROM tblissue2 WHERE issue_id=$issue_id AND item_id ='".$lst_arr['item_id']."' AND item_category ='".$lst_arr['item_category']."'";
           $query =$connect->query($sql);
           $row =mysqli_fetch_assoc($query);
           $sno =$row['seq_no'];
           
           $sql ="SELECT Max(stock_id) AS maxid FROM stock_register";
             $query =$connect->query($sql);
             $row = mysqli_fetch_assoc($query);
             $sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);

            $check ="SELECT * FROM stock_register WHERE entry_mode ='I-' AND entry_id =$issue_id AND item_id ='".$lst_arr['item_id']."' AND item_category='".$lst_arr['item_category']."' AND seq_no =$sno";
            $check_query =$connect->query($check);
            $num_rows = mysqli_num_rows($check_query);
            if($num_rows>0){
                //update
                $str ="UPDATE stock_register SET item_qnty='".$lst_arr['return_qty']."' WHERE entry_mode ='I-' AND entry_id =$issue_id AND item_id ='".$lst_arr['item_id']."' AND item_category='".$lst_arr['item_category']."' AND seq_no=$sno";
                $query =$connect->query($str);
            }else{
                //insert
                $str ="INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate,item_amt)
                VALUES('$sid','I-','$issue_id','$issue_date','$sno','$location','".$lst_arr['item_id']."','".$lst_arr['item_category']."','".$lst_arr['unit']."','".$lst_arr['return_qty']."','0.00','0.00')";
                $query =$connect->query($str);
            }
       }
       $output['success']=true;
       $connect->close();
       echo json_encode($output);
     }
}
?>