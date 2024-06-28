<?php
    require_once 'db_connect.php';
    //----------------------------//Retrive Party List------------------//
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if($action == 'getStock'){
        $location = $_POST['location'];
            $output = array('data' => array());
            $sql = "SELECT sr.*,i.item_name,ic.category,u.unit_name FROM stock_register sr 
            LEFT JOIN item i ON i.item_id = sr.item_id
            LEFT JOIN item_category ic ON ic.category_id = sr.item_category
            LEFT JOIN unit u ON u.unit_id = sr.unit_id
            WHERE sr.location_id=$location AND sr.entry_mode ='O+' ORDER by sr.stock_id";
            $query = $connect->query($sql); 
            $x = 1;
            while ($row = $query->fetch_assoc()) {
                    $actionButton = '<button type ="button" class="btn "  onclick="editStock('.$row['stock_id'].')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                <button type ="button"  class="btn " data-toggle="modal" data-target="#removeStockModal" onclick="removeStock('.$row['stock_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
                ';
                $output['data'][] = array(
                    $x,
                    $row['item_name'].' ~~ '.$row['category'],
                  
                    $row['item_qnty'],
                    $row['unit_name'],
                    $row['item_rate'],
                    $row['item_amt'],
                    $actionButton
                
                );
            
                $x++;
            }
        // database connection close
        $connect->close();
        echo json_encode($output);
    }
    elseif($action == 'get_unit'){
           $output = array();
           $itm_id = $_POST['item_id'];
           
           $sql=   "SELECT u.unit_name, u.unit_id FROM unit u join item i on i.unit_id =u.unit_id WHERE i.item_id =$itm_id";
           $query = $connect->query($sql);
           while ($row = $query->fetch_assoc()) {
              
                   $output = array(
                     'unit_id'=>  $row['unit_id'],
                     'unit_name'=>$row['unit_name']
                       
                   );
   
               }
          
           echo json_encode($output);
           
       }
       elseif ($action =='addstock') {
           $stock_id =$_POST['stock_id'];
           $location= $_POST['location'];
           $item_name =$_POST['item_name'];
           $date=$_POST['date'];
           $unit=$_POST['unit'];
           $op_quantity =$_POST['op_quantity'];
           $rate =$_POST['rate'];
           $op_amount =$_POST['op_amount'];
           $item_category =$_POST['item_category'];
           
           if(!empty($stock_id)){
                //-----------------Update Stock--------------------------
                $sql="UPDATE stock_register SET item_id='$item_name',item_qnty='$op_quantity',item_rate='$rate',item_amt='$op_amount',unit_id='$unit',item_category ='$item_category' WHERE stock_id=$stock_id";
               // print_r($sql);die;
                $query=$connect->query($sql);
                if($query === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Updated"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while Updating the Stock information";
                }
                $connect->close();
                echo json_encode($validator);

           }else{
                //-------------------Insert New Stock--------------------
                $get_stock_id ="SELECT Max(stock_id) as maxid FROM stock_register";
                $query=$connect->query($get_stock_id);
                $query =mysqli_fetch_array($query);
                $s_id=$query['maxid']+1;
                $sql="INSERT INTO stock_register(stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,item_qnty,unit_id,item_rate,item_amt)
                VALUES('$s_id','O+','$s_id','$date',1,'$location','$item_name','$item_category','$op_quantity','$unit','$rate','$op_amount')";
              //  print_r($sql);die;
                $query1=$connect->query($sql);
                if($query1 === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Added"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while adding the Stock information";
                }
                $connect->close();
                echo json_encode($validator);
           }
       }elseif ($action=='delete_srock') {
           $stock_id =$_POST['stock_id'];
           $output = array('success' => false, 'messages' => array());
           $sql="DELETE FROM stock_register WHERE stock_id={$stock_id}";
           $query =$connect->query($sql);
           if($query === TRUE) {
                $output['success'] = true;
                $output['messages'] = 'Successfully removed';
            } else {
                $output['success'] = false;
                $output['messages'] = 'Error while removing the city information';
            }
            $connect->close();
            echo json_encode($output);
       }elseif($action=='get_single_stock'){
        $stock_id= $_POST['stock_id'];      
        $sql = "SELECT s.*,l.location_name,u.unit_name,i.item_name FROM stock_register s
        JOIN location l ON l.location_id =s.location_id
        JOIN unit u ON u.unit_id =s.unit_id
        JOIN item i ON i.item_id = s.item_id WHERE stock_id =$stock_id";
        $query=$connect->query($sql);
        $output = array('data' => array());
        while ($row = $query->fetch_assoc()) {
          
            $output = array(
                'stock_id'=> $row['stock_id'],
                'location'=>  $row['location_id'],
                'location_name'=>$row['location_name'],
                'item_id'=>$row['item_id'],
                'item_name'=>$row['item_name'],
                'op_quantity'=>$row['item_qnty'],
                'op_amount'=>$row['item_amt'],
                'rate'=>$row['item_rate'],
                'date'=>$row['entry_date'],
                'unit_id'=>$row['unit_id'],
                'unit_name'=>$row['unit_name']
              );
        }
        $connect->close();
        echo json_encode($output);
       }
   
}
 ?>