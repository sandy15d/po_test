<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
  
    $action = $_POST['action'];
    if ($action == 'create')
    {
        $txn_id =$_POST['txn_id'];
        $memo_no =$_POST['memo_no'];
       
        $memo_date =$_POST['memo_date'];
        $particulars =$_POST['particulars'];
       
        $purchase_amount =$_POST['purchase_amount'];
        $company =$_POST['company'];
        $location =$_POST['location'];
       
        if (!empty($txn_id))
        {

            //update
            $sql = "UPDATE tblcashmemo SET memo_no='$memo_no',memo_date='$memo_date',company_id=$company,location_id=$location,particulars='$particulars',memo_amt='$purchase_amount' WHERE txn_id =$txn_id";
            $query = $connect->query($sql);
            if ($query === true)
            {
                $validator['success'] = true;
                $validator['messages'] = "Successfully Updated Purchase Order";
                $validator['txn_id']=$txn_id;
            }
            else
            {
                $validator['success'] = false;
                $validator['messages'] = "Error while Updating the Purchase Order";
            }
            $connect->close();
            echo json_encode($validator);
        }
        else
        {
            //insert
            $check ="SELECT * FROM tblcashmemo WHERE memo_no =$memo_no";
        
            $chkquery =$connect->query($check);
            $num_rows = mysqli_num_rows($chkquery);
            if($num_rows>0){
                $validator['success'] = false;
                $validator['messages'] = "Duplication of Memo Number";
                $connect->close();
                echo json_encode($validator);
            }else{
                $sql = "SELECT Max(txn_id) as maxtxn FROM tblcashmemo";
                $query = $connect->query($sql);
                $query = mysqli_fetch_assoc($query);
                $txn_no = $query["maxtxn"] == null ? 1 : $query["maxtxn"] + 1;
                $sql = "INSERT INTO tblcashmemo(txn_id,memo_no,memo_date,company_id,location_id,particulars,memo_amt)VALUES('$txn_no','$memo_no','$memo_date',$company,'$location','$particulars','$purchase_amount')";
                $query = $connect->query($sql);
               
                $last_id = "SELECT txn_id FROM tblcashmemo ORDER BY txn_id DESC limit 0 ,1";
               //print_r($last_id);die;
                $query1 =$connect->query($last_id);
               
                $id = mysqli_fetch_assoc($query1);
                $id = $id['txn_id'];
                if ($query === true)
                {
                    $validator['success'] = true;
                    $validator['txn_id']=$id;
                }
                else
                {
                    $validator['success'] = false;
                    $validator['messages'] = "Error while createing the Purchase Order";
                }
    
                $connect->close();
                echo json_encode($validator);
            }


        }
    }elseif ($action=='get_indent') {
        $memo_date =$_POST['memo_date'];
        $location =$_POST['location'];
        $sql ="SELECT *,DATE_FORMAT(indent_date,'%d/%m/%Y') AS indent_date FROM tbl_indent WHERE order_from=$location AND indent_date<='$memo_date' ORDER BY indent_date, indent_no";
        $query=$connect->query($sql);
        while ($row = $query->fetch_assoc()) {
                $output['data'][] = array(
           
                    'indent_id'=>$row['indent_id'],
                    'ind_prefix'=>$row['ind_prefix'],
                    'indent_no'=> $row['indent_no'],
                    'indent_date'=>$row['indent_date']
                );
            }
            $connect->close();
             
            echo json_encode(array('data'=>$output,'status'=>200));
    }/* elseif ($action=='get_indent_detail') {
        $indent_id =$_POST['indent_id'];
       $sql ="SELECT indent_date FROM tbl_indent WHERE indent_id=$indent_id";
         $query =$connect->query($sql);
        
         $result = mysqli_fetch_assoc($query);
         $result = $result['indent_date'];
        
         $connect->close();
         echo json_encode($result);
    } */elseif ($action=='get_indent_item') {
       $indent_id =$_POST['indent_id'];
        $sql ="SELECT ti.indent_id, i.item_id,i.item_name,ic.category_id,ic.category,u.unit_id,ti_item.aprvd_qnty as qty FROM `tbl_indent` ti 
        JOIN tbl_indent_item ti_item ON ti_item.indent_id =ti.indent_id
        JOIN item i ON i.item_id =ti_item.item_id
        JOIN unit u ON u.unit_id = i.unit_id
        JOIN item_category ic ON ic.category_id = ti_item.item_category
        WHERE ti.indent_id=$indent_id";
        $query=$connect->query($sql);
        while ($row = $query->fetch_assoc()) {
                $output['data'][] = array(
           
                   'item_id'=>$row['item_id'],
                   'item_name'=>$row['item_name'].' ~~'.$row['category'],
                   'category_id'=>$row['category_id'],
                   'unit_id'=>$row['unit_id'],
                   'qty'=>$row['qty']
                );
               
            
            }
            $connect->close();
             
            echo json_encode($output);
    }/* elseif ($action=='get_stock') {
        $item_id =$_POST['item_id'];
        $location=$_POST['location'];
        $entry_date =$_POST['entry_date'];
        $sql="SELECT ifnull(Sum(s.item_qnty),0) AS qty,u.unit_id,u.unit_name,u.unit_id FROM stock_register s
        JOIN item itm ON itm.item_id =s.item_id
        JOIN unit u ON u.unit_id = itm.unit_id
        WHERE s.item_id=$item_id AND s.location_id=$location AND s.entry_date<='$entry_date'";
        $query =$connect->query($sql);
      
        $result = $query->fetch_assoc();
       echo json_encode($result);
    } */elseif($action=='save_item'){
        $txn_id =$_POST['txn_id'];
        $indent_id =$_POST['indent_id'];
        $location =$_POST['location'];
        $memo_date=$_POST['memo_date'];
        $list_array =$_POST['list_array'];
        foreach($list_array as $lst_arr){
            $sql = "SELECT Max(rec_id) as rec_id FROM tblcash_item";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["rec_id"] == null ? 1 : $query["rec_id"] + 1;
            $sql = "SELECT Max(seq_no) as maxno FROM tblcash_item WHERE txn_id=$txn_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $sno = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
            $check ="SELECT * FROM tblcash_item WHERE txn_id=$txn_id AND indent_id =$indent_id AND item_id='".$lst_arr['item_id']."' AND item_category ='".$lst_arr['category_id']."'";
            $chkquery=$connect->query($check);
            $num_rows = mysqli_num_rows($chkquery);
            if($num_rows>0){
    
                $sql ="UPDATE tblcash_item SET memo_qnty='".$lst_arr['item_qty']."',rate='".$lst_arr['item_rate']."' WHERE indent_id=$indent_id AND '".$lst_arr['item_id']."' AND item_category ='".$lst_arr['category_id']."'";
                $query=$connect->query($sql);

               
                $item_rate = $lst_arr['item_qty'] *$lst_arr['item_rate'];
                  
                $sql ="UPDATE stock_register SET item_qnty='".$lst_arr['item_qty']."', item_rate ='".$lst_arr['item_rate']."', item_amt ='$item_rate' WHERE entry_id =$txn_id AND item_id ='".$lst_arr['item_id']."' AND item_category ='".$lst_arr['category_id']."' ";
                $query =$connect->query($sql);

                $validator['success'] = true;
                $validator['messages']='Successfully Save Changes..!!';
            }
            else
            {
               
               $sql ="INSERT INTO tblcash_item(rec_id,txn_id,indent_id,seq_no,item_id,item_category,unit_id,memo_qnty,rate)
               VALUES($rec_id,$txn_id,$indent_id,$sno,'".$lst_arr['item_id']."','".$lst_arr['category_id']."','".$lst_arr['unit_id']."','".$lst_arr['item_qty']."','".$lst_arr['item_rate']."')";
               $query=$connect->query($sql);
               $sql ="SELECT Max(stock_id) as maxid FROM stock_register";
               $query = $connect->query($sql);
               $query = mysqli_fetch_assoc($query);
               $maxid = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
               $sql = "SELECT Max(seq_no) as maxno FROM stock_register WHERE entry_id=$txn_id AND entry_mode='C+'";
               $query = $connect->query($sql);
               $row = mysqli_fetch_assoc($query);
               $seq_no = ($row["maxno"]==null ? 1 : $row["maxno"] + 1);
               $item_rate = $lst_arr['item_qty'] *$lst_arr['item_rate'];
               $sql ="INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,item_qnty,unit_id,item_rate,item_amt) VALUES
               ($maxid,'C+',$txn_id,'$memo_date',$seq_no,$location,'".$lst_arr['item_id']."','".$lst_arr['category_id']."','".$lst_arr['item_qty']."','".$lst_arr['unit_id']."','".$lst_arr['item_rate']."',$item_rate)";
               $query =$connect->query($sql);
               if($query===true){
                $validator['success'] = true;
                $validator['messages'] = "Succssfully....";
               }else{
                $validator['success'] = false;
                $validator['messages'] = "Error while Inserting Item...!!";
               }
               
            }
        }


        $connect->close();
        echo json_encode($validator);
    }elseif($action=='item_list') {
        $txn_id =$_POST['txn_id'];
        $sql ="SELECT  t2.rec_id,i.item_name,t2.memo_qnty,u.unit_name,t2.rate FROM tblcashmemo t1
        JOIN tblcash_item t2 ON t2.txn_id =t1.txn_id 
        JOIN item i ON i.item_id = t2.item_id
        JOIN unit u ON u.unit_id = t2.unit_id
        WHERE t1.txn_id=$txn_id";
         $query = $connect->query($sql);
         $output = array('data' => array());
         $x = 1;
         while ($row = $query->fetch_assoc())
         {
             $actionButton = '<button type ="button" class="btn btn delete" id="'.$row['rec_id'].'"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';

             $output['data'][] = array(
                 $x,
              
                 $row['item_name'],
                 $row['memo_qnty'].' '.$row['unit_name'],
                 $row['rate'],
                 $actionButton
             );
             $x++;
         }
         $connect->close();
         echo json_encode($output);
    }elseif ($action=='delete_item') {
       $rec_id =$_POST['rec_id'];
       $txn_id=$_POST['txn_id'];
       $memo_date=$_POST['memo_date'];
       $location =$_POST['location'];
       
       $sql ="SELECT item_id FROM tblcash_item WHERE rec_id =$rec_id";
       $query =$connect->query($sql);
       $query = mysqli_fetch_assoc($query);
       $item_id = $query["item_id"];
       
       $sql ="DELETE FROM tblcash_item WHERE rec_id =$rec_id";
       $query =$connect->query($sql);

       $sql ="DELETE FROM stock_register WHERE entry_mode='C+' AND entry_id=$txn_id AND entry_date='$memo_date' AND location_id=$location AND item_id =$item_id";
       $query =$connect->query($sql);
       if($query===true){
           $output['success']=true;
       }else{
           $output['success']=false;
       }
       $connect->close();
       echo json_encode($output);
    }elseif ($action == 'get_cash_memo'){
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $sql = "SELECT t1.*,DATE_FORMAT(memo_date,'%d/%m/%Y') AS memo_date,c.company_name,l.location_name FROM tblcashmemo t1
        JOIN company c ON c.company_id = t1.company_id
        JOIN location l ON l.location_id =t1.location_id WHERE memo_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY memo_date, txn_id";
        $query = $connect->query($sql);
        //echo json_encode($sql);die;
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn edit" id="'.$row['txn_id'].'"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                    <button type ="button"  class="btn btn delete_record"  id="'.$row['txn_id'].'"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';

            $output['data'][] = array(
                $x,
                $row['memo_no'],
                $row['memo_date'],
                $row['memo_amt'],
                $row['company_name'],
                $row['location_name'],
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }elseif ($action=='delete_record') {
        $txn_id =$_POST['txn_id'];
        //delete from stock register
        $sql1="DELETE FROM stock_register WHERE entry_id =$txn_id AND entry_mode='C+'";
        $query1=$connect->query($sql1);
        //delete from cash item 
        $sql2 ="DELETE FROM tblcash_item WHERE txn_id=$txn_id";
        $query2=$connect->query($sql2);
        //delete cash memo 
        $sql3 ="DELETE FROM tblcashmemo WHERE txn_id =$txn_id";
        $query3=$connect->query($sql3);

        if($query3===true){
            $output['success']=true;
            $output['messages']='Delete Successfully...!!!';
        }else{
            $output['success']=false;
            $output['messages']='Something went wrong...Please try again.';
        }

        $connect->close();
        echo json_encode($output);
    }elseif ($action == 'edit_cash_purchase'){
        $txn_id = $_POST['txn_id'];
        $sql = "SELECT tbcm.*,c.company_name,l.location_name FROM `tblcashmemo` tbcm
        INNER JOIN company c ON c.company_id = tbcm.company_id
        INNER JOIN location l  ON l.location_id=tbcm.location_id WHERE txn_id =$txn_id";
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }
}

?>