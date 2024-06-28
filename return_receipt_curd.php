<?php
    require_once 'db_connect.php';
    session_start();
   
    if (isset($_POST['action']) && !empty($_POST['action'])) {
        $action = $_POST['action'];
        if($action=='get_detail'){
            $receipt_id =$_POST['receipt_id'];
            $sql ="SELECT recd_at FROM tblreceipt1 WHERE receipt_id=$receipt_id";
            $query=$connect->query($sql);
             $recd_at =mysqli_fetch_array($query);
             $recd_at= $recd_at[0];
        
            $sql = "SELECT * FROM staff WHERE location_id=$recd_at";
            $query = $connect->query($sql);

            $output = [];
            while ($row = mysqli_fetch_row($query)) {
                $temp = [];
                $temp['staff_id'] = $row[0];
                $temp['staff_name']=$row[1];
                $output[] = $temp;
              }
              $data['staff']=$output;

              $sql="SELECT tblr1.*, tblpo.po_no,tblpo.po_date,l.location_name as transit_point_location,
              tblpo.delivery_date,l2.location_name as delivery_location,l3.location_name as received_location,
              s.staff_name,p.party_name,p.address1,p.address2,p.address3,c.city_name,
              st.state_name FROM `tblreceipt1` tblr1 
              JOIN tbldelivery1 tbld1 ON tbld1.dc_id = tblr1.dc_id 
              JOIN tblpo ON tblpo.po_id = tbld1.po_id 
              JOIN location l on l.location_id = tblr1.transit_point 
              JOIN location l2 ON l2.location_id = tblpo.delivery_at 
              JOIN location l3 ON l3.location_id = tblr1.recd_at 
              JOIN staff s ON s.staff_id = tblr1.recd_by 
              JOIN party p on p.party_id = tblpo.party_id 
              JOIN city c ON c.city_id = p.city_id 
              JOIN state st on st.state_id=c.state_id WHERE receipt_id =$receipt_id";
              $query=$connect->query($sql);
              $output1=[];
                $row =mysqli_fetch_assoc($query);
                $temp['receipt_id'] = $row['receipt_id'];
                $temp['receipt_date'] = $row['receipt_date'];
                $temp['receipt_no'] = $row['receipt_no'];
                $temp['po_no'] = $row['po_no'];
                $temp['po_date'] = $row['po_date'];
                $temp['receipt_prefix'] = $row['receipt_prefix'];
                $temp['challan_no'] = $row['challan_no'];
                $temp['challan_date'] = $row['challan_date'];
                $temp['transit_point'] = $row['transit_point_location'];
                $temp['recd_at'] = $row['received_location'];
                $temp['delivery_location'] = $row['delivery_location'];
                $temp['delivery_date'] = $row['delivery_date'];
                $temp['staff_name'] = $row['staff_name'];
                $temp['party_name'] = $row['party_name'];
                $temp['address1'] = $row['address1'];
                $temp['address2'] = $row['address2'];
                $temp['address3'] = $row['address3'];
                $temp['freight_paid'] = $row['freight_paid'];
                $temp['freight_amt'] = $row['freight_amt'];
                $temp['city_name'] = $row['city_name'];
                $temp['state_name'] = $row['state_name'];
                $output1[] = $temp;
            

              $data['detail']=$output1;
             // $data['detail']=mysqli_fetch_array($query);
            if(count($output) > 0){
              
              echo json_encode(array('staff'=>$data['staff'],'detail'=>$data['detail'],'status'=>200));
            }else{
                echo json_encode(array('msg'=>'Record not found.','status'=>500));
            }
        }elseif ($action=='save_receipt') {
            $return_id =$_POST['return_id'];
            $return_no =$_POST['return_no'];
            $receipt_id =$_POST['receipt_no'];
            $return_by =$_POST['return_by'];
            $return_date =$_POST['return_date'];

            if($return_id!=''){
                //================Update================//
                $sql ="UPDATE tblreceipt_return1 SET receipt_id=$receipt_id, return_date ='$return_date',return_by=$return_by WHERE return_id =$return_id";
                $query =$connect->query($sql);

                $output['success']=true;
                $output['message']='successfully save changes';
                $output['return_id']=$return_id;
                $output['return_no']=$return_no;
                $connect->close();
                echo json_encode($output);

            }else{
                //==================Insert==============//
                        //-------------Check for duplicate entry-----------------//
                $check ="SELECT * FROM tblreceipt_return1 WHERE return_date='$return_date' AND receipt_id =$receipt_id AND return_by =$return_by";
                $result =$connect->query($check);
                $count =mysqli_num_rows($result);
               if($count>0){
                   //----------------------if Duplicate Entry---------------------------------------
                   $output['success']=false;
                   $output['messages']='Duplicate Entry..!! Please try again with other option.';
               }else{
                   //---------------If No Duplication--------------------//
                    $sql ="SELECT Max(return_id) as maxid FROM tblreceipt_return1";
                    $row =$connect->query($sql);
                    $row =mysqli_fetch_assoc($row);
                    $rec_id =($row["maxid"] == null ? 1 : $row["maxid"] + 1);

                    $sql ="SELECT Max(return_no) as maxno FROM tblreceipt_return1 WHERE return_date BETWEEN '".date("Y-m-d",strtotime($_SESSION['stores_syr']))."' AND '".date("Y-m-d",strtotime($_SESSION['stores_eyr']))."'";
                    $row =$connect->query($sql);
                    $row =mysqli_fetch_assoc($row);
                    $rno =($row["maxno"] == null ? 1 : $row["maxno"] + 1);

                    $str ="INSERT INTO tblreceipt_return1(return_id,return_date,return_no,receipt_id,return_by) VALUES($rec_id,'$return_date',$rno,$receipt_id,$return_by)";
                    $query =$connect->query($str);

                    $sql = "SELECT return_id FROM tblreceipt_return1 ORDER BY return_id DESC limit 0 ,1";
                    $query = $connect->query($sql);
                    $row = mysqli_fetch_array($query);
                    $last_id = $row['return_id'];

                    $sql ="SELECT * from tblreceipt_return1 WHERE return_id =$last_id";
                    $query =$connect->query($sql);
                    $row =mysqli_fetch_assoc($query);
                    $rno =$row['return_no'];
                    if ($rno > 99 && $rno < 1000)
                    {
                        $return_no = "0" . $rno;
                    }
                    else
                    {
                        if ($rno > 9 && $rno < 100)
                        {
                            $return_no = "00" . $rno;
                        }
                        else
                        {
                            $return_no = "000" . $rno;
                        }
                    }

                    $output['success']=true;
                    $output['return_id']=$last_id;
                    $output['return_no']=$return_no;
                    
               }
               $connect->close();
               echo json_encode($output);
            }
            
        }elseif ($action == 'Get_Item'){
            $receipt_no = $_POST['receipt_no'];
            $output = array(
                'data' => array()
            );
            $sql = "SELECT tbl2.item_id,ic.category_id,ic.category,tbl2.unit_id,tbl2.receipt_qnty,i.item_name,u.unit_name FROM tblreceipt1 tbl1
            JOIN tblreceipt2 tbl2 ON tbl2.receipt_id = tbl1.receipt_id
            JOIN item i ON i.item_id = tbl2.item_id
            JOIN unit u ON u.unit_id = tbl2.unit_id
            JOIN item_category ic ON ic.category_id = tbl2.item_category
            WHERE tbl1.receipt_id =$receipt_no";
            $query = $connect->query($sql);
            $x = 1;
            while ($row = $query->fetch_assoc())
            {
                $output['data'][] = array(
                    'sno' => $x,
                    'item_id' => $row['item_id'],
                    'item_name' => $row['item_name'].' ~~'.$row['category'],
                    'unit_id' => $row['unit_id'],
                    'unit_name' => $row['unit_name'],
                    'qty' => $row['receipt_qnty'],
                    'category_id'=>$row['category_id']
                );
                $x++;
            }
            $connect->close();
            echo json_encode($output);
        }elseif ($action == 'save_item'){
            $return_id = $_POST['return_id'];
            $return_date = $_POST['return_date'];
            $list_array = $_POST['list_array'];
    

            $location ="SELECT * FROM tblreceipt1 tr JOIN tblreceipt_return1 t1 ON t1.receipt_id =tr.receipt_id WHERE t1.return_id =$return_id";
            $res =$connect->query($location);
            $row =mysqli_fetch_assoc($res);
            $location_id =$row['recd_at'];
            foreach ($list_array as $lst_arr)
            {
                $sql = "SELECT MAX(rec_id) as maxid FROM tblreceipt_return2";
                $query = $connect->query($sql);
                $query = mysqli_fetch_assoc($query);
                $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
    
                $sql = "SELECT MAX(seq_no) as maxno FROM tblreceipt_return2 WHERE return_id=$return_id";
                $query = $connect->query($sql);
                $seq_no = mysqli_fetch_assoc($query);
                $seq_no = $seq_no['maxno'] == null ? 1 : $seq_no['maxno'] + 1;
    
                $sql1 = "SELECT count('rec_id') as total,seq_no FROM tblreceipt_return2 WHERE return_id = $return_id AND item_id ='" . $lst_arr['item_id'] . "' AND item_category ='".$lst_arr['category_id']."'";
    
                $query1 = $connect->query($sql1);
                $row = mysqli_fetch_array($query1);
                $count = $row['total'];
                $s_qno =$row['seq_no'];
    
                if ($count > 0)
                {
                    //Update Table Receipt
                    $sql2 = "UPDATE tblreceipt_return2 SET return_qnty='" . $lst_arr['return_qty'] . "' WHERE return_id=$return_id AND item_id='" . $lst_arr['item_id'] . "' AND item_category ='".$lst_arr['category_id']."'";
                    $query = $connect->query($sql2);
    
                    if ($query == true)
                    {
                            //=========================stock register entry===================//
                            $query =$connect->query($sql);
                            $row = mysqli_fetch_assoc($query);
                            $sql = "UPDATE stock_register SET item_id='" . $lst_arr['item_id'] . "',item_category='".$lst_arr['category_id']."',unit_id='" . $lst_arr['unit_id'] . "',item_qnty='".(-1*$lst_arr['return_qty'])."'
                            WHERE entry_mode='R-' AND entry_id=$return_id AND entry_date='$return_date' AND seq_no=$s_qno AND location_id=$location_id";
                            $query =$connect->query($sql);
                         //===========================logbook entry===================//
                         $sql = "SELECT Max(rec_id) as maxid FROM logbook";
                         $query =$connect->query($sql);
                         $row = mysqli_fetch_assoc($query);
                         $recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
                         $voucherid = ($return_id>999 ? $return_id : ($return_id>99 && $return_id<1000 ? "0".$return_id : ($return_id>9 && $return_id<100 ? "00".$return_id : "000".$return_id)));
                         $str ="SELECT * FROM `tblreceipt_return2` tr 
                         JOIN tblreceipt_return1 tr1 ON tr1.return_id = tr.return_id
                         JOIN tblreceipt1 t1 ON t1.receipt_id = tr1.receipt_id
                         JOIN tblpo tp ON tp.po_id = t1.po_id
                         JOIN party p ON p.party_id = tp.party_id
                         JOIN location l ON l.location_id = t1.recd_at
                         JOIN item i ON i.item_id = tr.item_id
                         JOIN unit u ON u.unit_id = tr.unit_id
                         WHERE tr.return_id =$return_id";
                         $result = $connect->query($str);
                         $result =mysqli_fetch_assoc($result);
                         $particulars = "From ".$result['party_name'];

                         $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
                              
                         $query =$connect->query($sql4);
                         $row=mysqli_fetch_assoc($query);
                      
                        $category_name =$row['category'];
                         
                         $sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,item_rate,item_amount,location, action,user) 
                         VALUES(".$recordid.",'".$voucherid."','".$return_date."','Rcpt.Rtrn.','".date("Y-m-d")."','$particulars','" . $result['item_name'] . "','".$category_name."','" . $result['unit_name'] . "','" . $lst_arr['return_qty'] . "',0,0,'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
                        $output['success'] = true;
                    }
                    else
                    {
                        $output['success'] = false;
    
                    }
    
                }
                else
                {
                    //Insert New entry in Table Receipt
                    $sql = "INSERT INTO tblreceipt_return2(rec_id,return_id,seq_no,item_id,item_category,unit_id,return_qnty)VALUES('" . $rec_id . "','" . $return_id . "','" . $seq_no . "','" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "','" . $lst_arr['return_qty'] . "')";
                    $query = $connect->query($sql);
                    if ($query == true)
                    {
                       //=========================stock register entry===================//
                        $sql = "SELECT Max(stock_id) as maxid FROM stock_register";
                        $query =$connect->query($sql);
				        $row = mysqli_fetch_assoc($query);
				        $sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
                        $sql = "INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,unit_id,item_qnty,item_rate) 
                        VALUES(".$sid.",'R-',".$return_id.",'".$return_date."',".$seq_no.",$location_id,'" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "',".(-1*$lst_arr['return_qty']).",0)";
                        $query =$connect->query($sql);
                        //===========================logbook entry===================//
                        $sql = "SELECT Max(rec_id) as maxid FROM logbook";
                        $query =$connect->query($sql);
				        $row = mysqli_fetch_assoc($query);
                        $recordid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
                        $voucherid = ($return_id>999 ? $return_id : ($return_id>99 && $return_id<1000 ? "0".$return_id : ($return_id>9 && $return_id<100 ? "00".$return_id : "000".$return_id)));
                        $str ="SELECT tr.*,i.item_name,u.unit_name,l.location_name FROM `tblreceipt_return2` tr 
                        JOIN tblreceipt_return1 tr1 ON tr1.return_id = tr.return_id
                        JOIN tblreceipt1 t1 ON t1.receipt_id = tr1.receipt_id
                        JOIN location l ON l.location_id = t1.recd_at
                        JOIN item i ON i.item_id = tr.item_id
                        JOIN unit u ON u.unit_id = tr.unit_id
                        WHERE tr.return_id =$return_id";
                        $result = $connect->query($str);
                        $result =mysqli_fetch_assoc($result);
                        $particulars = "From ".$result['location_name'];

                        $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
                              
                        $query =$connect->query($sql4);
                        $row=mysqli_fetch_assoc($query);
                     
                       $category_name =$row['category'];
                        
                        $sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,item_rate,item_amount,location, action,user) 
                        VALUES(".$recordid.",'".$voucherid."','".$return_date."','Rcpt.Rtrn.','".date("Y-m-d")."','$particulars','" . $result['item_name'] . "','".$category_name."','" . $result['unit_name'] . "','" . $lst_arr['return_qty'] . "',0,0,'".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
                        $query=$connect->query($sql);
                        $output['success'] = true;
    
                    }
                    else
                    {
                        $output['success'] = false;
    
                    }
    
                }
    
            }
            $connect->close();
            echo json_encode($output);
        }elseif ($action =='get_receipt') {

                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $sql = "SELECT tr1.*, t1.receipt_no,l.location_name,l.location_prefix,p.party_name FROM `tblreceipt_return1` tr1 
                JOIN tblreceipt_return2 tr2 ON tr2.return_id = tr1.return_id
                JOIN tblreceipt1 t1 ON t1.receipt_id = tr1.receipt_id
                JOIN location l ON l.location_id = t1.recd_at
                JOIN tblpo tp ON tp.po_id = t1.po_id
                JOIN party p ON p.party_id = tp.party_id
                JOIN item i ON i.item_id = tr2.item_id
                JOIN unit u ON u.unit_id = tr2.unit_id
                WHERE tr1.return_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' GROUP BY tr1.return_id";
                $query = $connect->query($sql);
                //echo json_encode($sql);die;
                $output = array(
                    'data' => array()
                );
                $x = 1;
                while ($row = $query->fetch_assoc())
                {
                    $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['return_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                            <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeMaterialModal" onclick="removeMaterial(' . $row['return_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
                    
                    
                            if ($row['return_no'] > 999)
                            {
                                $return_no = $row['return_no'];
                            }
                            else
                            {
                                if ($row['return_no'] > 99 && $row['return_no'] < 1000)
                                {
                                    $return_no = "0" . $row['return_no'];
                                }
                                else
                                {
                                    if ($row['return_no'] > 9 && $row['return_no'] < 100)
                                    {
                                        $return_no = "00" . $row['return_no'];
                                    }
                                    else
                                    {
                                        $return_no = "000" . $row['return_no'];
                                    }
                                }
                            }

                            if ($row['receipt_no'] > 999)
                    {
                        $rcpt_no = $row['receipt_no'];
                    }
                    else
                    {
                        if ($row['receipt_no'] > 99 && $row['receipt_no'] < 1000)
                        {
                            $rcpt_no = "0" . $row['receipt_no'];
                        }
                        else
                        {
                            if ($row['receipt_no'] > 9 && $row['receipt_no'] < 100)
                            {
                                $rcpt_no = "00" . $row['receipt_no'];
                            }
                            else
                            {
                                $rcpt_no = "000" . $row['receipt_no'];
                            }
                        }
                    }
        

        
                    $rcpt_no = $row['location_prefix'] . '/' . $rcpt_no;
        
                    $output['data'][] = array(
                        $x,
                        $return_no,
                        $row['return_date'],
                        $rcpt_no,
                   
                        $row['location_name'],
                        $row['party_name'],
                        $actionButton
                    );
                    $x++;
                }
                $connect->close();
                echo json_encode($output);
            
        }elseif ($action == 'delete_record')
        {
            $return_id = $_POST['return_id'];
             $sql = "SELECT * FROM `tblreceipt_return2` tr 
             JOIN tblreceipt_return1 tr1 ON tr1.return_id = tr.return_id
             JOIN tblreceipt1 t1 ON t1.receipt_id = tr1.receipt_id
             JOIN tblpo tp ON tp.po_id = t1.po_id
             JOIN party p ON p.party_id = tp.party_id
             JOIN location l ON l.location_id = t1.recd_at
             JOIN item i ON i.item_id = tr.item_id
             JOIN unit u ON u.unit_id = tr.unit_id
             WHERE tr.return_id =$return_id";

            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
           
            $return_date = $row['return_date'];
            $party_name = $row['party_name'];
            $location_id = $row['recd_at'];
            $particulars = "From" . $party_name; 
    
         
            $sql = "DELETE FROM tblreceipt_return2 WHERE return_id=$return_id";
            $query = $connect->query($sql);
    
            $sql = "DELETE FROM tblreceipt_return1 WHERE return_id=$return_id";
            $query = $connect->query($sql);
            if ($query == true)
            {
                     //====================Delete from Stock Register==========================//
                     $str ="DELETE FROM stock_register WHERE entry_mode='R-' AND entry_id=$return_id AND entry_date='$return_date'  AND location_id=$location_id";
                     $result =$connect->query($str);
                     
                //=======================================Logbook======================
                $sql2 = "SELECT MAX(rec_id) AS maxid FROM logbook";
                $query2 = $connect->query($sql2);
                $query2 = mysqli_fetch_assoc($query2);
                $max_id = $query2["maxid"] == null ? 1 : $query2["maxid"] + 1;
    
                if ($return_id > 99 && $return_id < 1000)
                {
                    $voucherid = "0" . $return_id;
                }
                else
                {
                    if ($return_id > 9 && $return_id < 100)
                    {
                        $voucherid = "00" . $return_id;
                    }
                    else
                    {
                        $voucherid = "000" . $return_id;
                    }
                }
    
                $sql3 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user)
                VALUES($max_id,'$voucherid','$return_date','Rcpt.Rtrn.','" . date("Y-m-d") . "','$particulars','" . $_SESSION['stores_lname'] . "','Delete','" . $_SESSION['stores_uname'] . "')";
                $query3 = $connect->query($sql3);
                $output['success'] = true;
                $output['messages'] = 'Successfully Delete Record..!!';
            }
            else
            {
                $output['success'] = false;
                $output['messages'] = 'Failed To Delete Record.. Please Try Again..!!';
            }
    
            $connect->close();
            echo json_encode($output);
        }elseif ($action == 'edit_return_receipt')
        {
            $return_id = $_POST['return_id'];
            $sql = "SELECT tr1.*,tbl1.*,l.location_name as transit_point_name,l2.location_name as received_at,s.staff_name as received_by,
            tbld1.*,tblpo.party_id,tblpo.delivery_date,tblpo.delivery_at,tblpo.po_no,tblpo.po_date,l3.location_name as delivery_location,
            p.*,c.*,st.state_name,s1.staff_name as return_by_name FROM tblreceipt1  tbl1
            INNER JOIN location l ON l.location_id =tbl1.transit_point
            INNER JOIN location l2 ON l2.location_id = tbl1.recd_at
            INNER JOIN staff s ON s.staff_id = tbl1.recd_by
            INNER JOIN tbldelivery1 tbld1 on tbld1.dc_id = tbl1.dc_id
            INNER JOIN tblpo on tblpo.po_id =tbld1.po_id
            INNER JOIN location l3 ON l3.location_id =tblpo.delivery_at
            JOIN party p ON p.party_id = tblpo.party_id
            INNER JOIN city c ON c.city_id = p.city_id
            INNER JOIN state st ON st.state_id =c.state_id
            INNER JOIN tblreceipt_return1 tr1 ON tr1.receipt_id = tbl1.receipt_id
            INNER JOIN staff s1 ON s1.staff_id = tr1.return_by
            WHERE tr1.return_id=$return_id";
            $query = $connect->query($sql);
            $output = mysqli_fetch_assoc($query);
            $connect->close();
            echo json_encode($output);
        }
    }
?>