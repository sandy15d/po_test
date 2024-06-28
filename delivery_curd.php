<?php
require_once 'db_connect.php';
session_start();
$sql_user = "SELECT  dc1,dc2,dc3,dc4 FROM users WHERE uid=".$_SESSION["stores_uid"];
$row_user =$connect->query($sql_user);
$row_user = mysqli_fetch_assoc($row_user);
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if ($action == 'get_dc')
    {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $sql = "SELECT tbl1.*,tp.po_date,tp.po_no,p.party_name,c.company_name,c.CCode,ti.ind_prefix FROM tbldelivery1 tbl1
        LEFT JOIN tblpo tp ON tp.po_id =tbl1.po_id
        LEFT JOIN tbl_indent ti ON ti.indent_id =tp.indent_id
        LEFT JOIN party p ON p.party_id =tp.party_id
        LEFT JOIN company c ON c.company_id =tp.company_id WHERE dc_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY dc_date, dc_id";
        $query = $connect->query($sql);
        //echo json_encode($sql);die;
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            if($row_user['dc2']==1){
                $edit = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#dcmodal_edit" onclick="editDC(' . $row['dc_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
            }else{
                $edit='-';
            }
           if($row_user['dc3']==1){
            $delete='<button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeDCModal" onclick="removeDC(' . $row['dc_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
           }else{
               $delete ='-';
           }
           
            if ($row['dc_no'] > 999)
            {
                $dc_no = $row['dc_no'];
            }
            else
            {
                if ($row['dc_no'] > 99 && $row['dc_no'] < 1000)
                {
                    $dc_no = "0" . $row['dc_no'];
                }
                else
                {
                    if ($row['dc_no'] > 9 && $row['dc_no'] < 100)
                    {
                        $dc_no = "00" . $row['dc_no'];
                    }
                    else
                    {
                        $dc_no = "000" . $row['dc_no'];
                    }
                }
            }

            $dc_no = $row['ind_prefix'].'/'.$dc_no;
            if ($row['po_no'] > 999)
            {
                $po_no = $row['po_no'];
            }
            else
            {
                if ($row['po_no'] > 99 && $row['po_no'] < 1000)
                {
                    $po_no = "0" . $row['po_no'];
                }
                else
                {
                    if ($row['po_no'] > 9 && $row['po_no'] < 100)
                    {
                        $po_no = "00" . $row['po_no'];
                    }
                    else
                    {
                        $po_no = "000" . $row['po_no'];
                    }
                }
            }

            $po_no = $row['CCode'] . '/' . $row['ind_prefix'].'/'.$po_no;
            $output['data'][] = array(
                $x,
                $dc_no,
                $row['dc_date'],
                $po_no,
                $row['po_date'],
                $row['party_name'],
                $row['company_name'],
                $edit,
                $delete
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'get_po_detail')
    {
        $po_id = $_POST['po_id'];
        $sql = "SELECT po_id, po_date, p.party_name,p.address1,p.address2,p.address3,c1.city_name as city,s.state_name,cmp.company_name,tp.delivery_date,l.location_name FROM tblpo tp 
            JOIN party p ON p.party_id =tp.party_id
            JOIN city c1 on c1.city_id =p.city_id
            JOIN state s on s.state_id =c1.state_id
            JOIN company cmp on cmp.company_id =tp.company_id
            JOIN location l ON l.location_id =tp.delivery_at
            WHERE po_id= $po_id";
        $query = $connect->query($sql);
        $output = $query->fetch_assoc();
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'create_dc')
    {
        $dc_date = $_POST['dc_date'];
        $po_id = $_POST['po_id'];

        $sql ="SELECT * FROM tbldelivery1 WHERE po_id =$po_id AND dc_date ='$dc_date'";
        $query =$connect->query($sql);
        $num_rows =mysqli_num_rows($query);
        if($num_rows>0){
            $output['success']=false;
            $output['messages']='Dplicate Entry..!!Try Again';
            $connect->close();
            echo json_encode($output);
        }else{
            $sql = "SELECT Max(dc_id) as maxid FROM tbldelivery1";
            $dc_id = $connect->query($sql);
            $dc_id = mysqli_fetch_assoc($dc_id);
            $dc_id = $dc_id["maxid"] == null ? 1 : $dc_id["maxid"] + 1;
    
            $sql = "SELECT Max(dc_no) as maxno FROM tbldelivery1 WHERE (dc_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
            $dc_no = $connect->query($sql);
            $dc_no = mysqli_fetch_assoc($dc_no);
            $dc_no = $dc_no["maxno"] == null ? 1 : $dc_no["maxno"] + 1;
    
            $sql = "INSERT INTO tbldelivery1(dc_id, dc_date, dc_no, po_id) VALUES('$dc_id','$dc_date','$dc_no','$po_id')";
            $query = $connect->query($sql);
            $last_id = "SELECT dc_id FROM tbldelivery1 ORDER BY dc_id DESC limit 0 ,1";
            $query = $connect->query($last_id);
            $id = mysqli_fetch_assoc($query);
            $id = $id['dc_id'];
    
            $sql = "SELECT dc_no FROM tbldelivery1 WHERE dc_id =$id";
            $query = $connect->query($sql);
    
            $dc_no = mysqli_fetch_assoc($query);
    
            if ($dc_no['dc_no'] > 999)
            {
                $dc_no = $dc_no['dc_no'];
            }
            else
            {
                if ($dc_no['dc_no'] > 99 && $dc_no['dc_no'] < 1000)
                {
                    $dc_no = "0" . $dc_no['dc_no'];
                }
                else
                {
                    if ($dc_no['dc_no'] > 9 && $dc_no['dc_no'] < 100)
                    {
                        $dc_no = "00" . $dc_no['dc_no'];
                    }
                    else
                    {
                        $dc_no = "000" . $dc_no['dc_no'];
                    }
                }
            }
            $output['success']=true;
        $output['messages']='success';
        $output['dc_no']=$dc_no;
        $output['dc_id']=$id;
                $connect->close();
                echo json_encode($output);
                }



    }
    elseif ($action == 'Get_Item')
    {
        $po_id = $_POST['po_id'];
        $dc_id = $_POST['dc_id'];
        $output = array(
            'data' => array()
        );
        $sql = "SELECT tbld1.dc_id,itm.item_id, itm.item_name,u.unit_id,u.unit_name,tpi.qnty,ic.category,ic.category_id FROM `tbldelivery1` tbld1
        JOIN tblpo_item tpi ON tpi.po_id = tbld1.po_id
        LEFT JOIN item itm on itm.item_id =tpi.item_id
        LEFT JOIN unit u ON u.unit_id = tpi.unit_id
        LEFT JOIN item_category ic ON ic.category_id = tpi.item_category
        WHERE tbld1.po_id=$po_id AND dc_id =$dc_id";
        $query = $connect->query($sql);
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $output['data'][] = array(
                'sno' => $x,
                'dc_id' => $row['dc_id'],
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'].' ~~'.$row['category'],
                'unit_id' => $row['unit_id'],
                'unit_name' => $row['unit_name'],
                'qty' => $row['qnty'],
                'category_id'=>$row['category_id']
            );
            $x++;
        }
        
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'save_item')
    {
        $dc_id = $_POST['dc_id'];
        $list_array = $_POST['list_array'];
        $sql ="SELECT * FROM `tbldelivery1` tbl1
        JOIN tblpo tp ON tp.po_id = tbl1.po_id
        JOIN party p on p.party_id = tp.party_id
        WHERE dc_id=$dc_id";
        $query =$connect->query($sql);
        $row =mysqli_fetch_assoc($query);
        $dc_date =$row['dc_date'];
        $party_name =$row['party_name'];
        $particulars ="From".$party_name;
        foreach ($list_array as $lst_arr)
        {
            $sql = "SELECT MAX(rec_id) as max_rec_id FROM tbldelivery2";
            $query = $connect->query($sql);
            $rec_id = mysqli_fetch_assoc($query);
            $rec_id = $rec_id["max_rec_id"] == null ? 1 : $rec_id["max_rec_id"] + 1;

            $sql = "SELECT MAX(seq_no) as maxno FROM tbldelivery2 WHERE dc_id=$dc_id";
            $query = $connect->query($sql);
            $seq_no = mysqli_fetch_assoc($query);
            $seq_no = $seq_no['maxno'] == null ? 1 : $seq_no['maxno'] + 1;

            $sql = "INSERT INTO tbldelivery2(rec_id,dc_id,seq_no,item_id,item_category,unit_id,delivery_qnty,item_received) VALUES('" . $rec_id . "','" . $dc_id . "','" . $seq_no . "','" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "','" . $lst_arr['dlr_qty'] . "','N')";
            $query = $connect->query($sql);

            //===================================Logbook Insert========================//
            $sql ="SELECT MAX(rec_id) AS maxid FROM logbook";
            $query =$connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
            if ( $dc_id > 99 &&  $dc_id < 1000)
            {
                $voucherid = "0" .  $dc_id;
            }
            else
            {
                if ( $dc_id > 9 &&  $dc_id < 100)
                {
                    $voucherid = "00" .  $dc_id;
                }
                else
                {
                    $voucherid = "000" .  $dc_id;
                }
            }
            $sql ="SELECT * FROM item
            JOIN unit u ON u.unit_id = item.unit_id
            WHERE item_id ='" . $lst_arr['item_id'] . "'";
            $query2= $connect->query($sql);
            $row =mysqli_fetch_assoc($query2);
            $item_name =$row['item_name'];
            $unit_name =$row['unit_name'];
            $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
            $query =$connect->query($sql4);
            $row=mysqli_fetch_assoc($query);
            $category_name =$row['category'];
            $sql ="INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user)
            VALUES($rec_id,'$voucherid','$dc_date','Dlry.Conf.','".date("Y-m-d")."','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['dlr_qty'] . "','".$_SESSION['stores_lname']."','New','".$_SESSION['stores_uname']."')";
            $query1=$connect->query($sql);
        }
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
    elseif ($action == 'get_view')
    {
        $dc_id = $_POST['dc_id'];
        $output = array(
            'data' => array()
        );
        $sql = "SELECT tbld2.*,tblpo_item.qnty,itm.item_name,u.unit_name,ic.category FROM `tbldelivery2` tbld2
                LEFT JOIN tbldelivery1 tbld1 ON tbld1.dc_id = tbld2.dc_id
                LEFT JOIN tblpo ON tblpo.po_id = tbld1.po_id
                LEFT JOIN tblpo_item ON tblpo_item.po_id = tbld1.po_id AND (tblpo_item.item_id = tbld2.item_id AND tblpo_item.item_category = tbld2.item_category)
                LEFT  JOIN item itm ON itm.item_id = tbld2.item_id
                LEFT  JOIN unit u ON u.unit_id =tbld2.unit_id
                LEFT   JOIN item_category ic ON ic.category_id = tbld2.item_category
                WHERE tbld2.dc_id=$dc_id";
        $query1 = $connect->query($sql);
        $x = 1;
        while ($row = $query1->fetch_assoc())
        {
            $output['data'][] = array(
                'sno' => $x,
                'rec_id' => $row['rec_id'],
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'].' ~~'.$row['category'],
                'unit_id' => $row['unit_id'],
                'unit_name' => $row['unit_name'],
                'qty' => $row['delivery_qnty'],
                'ord_qty' => $row['qnty'],
                'category_id'=>$row['item_category']
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'update_item')
    {
        $list_array = $_POST['list_array'];
        foreach ($list_array as $lst_arr)
        {
            $sql = "UPDATE tbldelivery2 SET delivery_qnty='" . $lst_arr['dlr_qty'] . "' WHERE rec_id ='" . $lst_arr['rec_id'] ."'";
            $query = $connect->query($sql);

            $sql1 ="SELECT * FROM tbldelivery2  WHERE rec_id ='" . $lst_arr['rec_id'] ."'";
            $query1 = $connect->query($sql1);
            $row=mysqli_fetch_assoc($query1);
            $dc_id = $row['dc_id'];
            $sql2 ="SELECT * FROM `tbldelivery1` tbl1
            JOIN tblpo tp ON tp.po_id = tbl1.po_id
            JOIN party p on p.party_id = tp.party_id
            WHERE dc_id=$dc_id";
            $query2 =$connect->query($sql2);
            $row =mysqli_fetch_assoc($query2);
            $dc_date =$row['dc_date'];
            $party_name =$row['party_name'];
            $particulars ="From".$party_name;

            $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
                              
            $query =$connect->query($sql4);
            $row=mysqli_fetch_assoc($query);
         
           $category_name =$row['category'];

           //===================================Logbook Insert========================//
           $sql3 ="SELECT MAX(rec_id) AS maxid FROM logbook";
           $query3 =$connect->query($sql3);
           $query3 = mysqli_fetch_assoc($query3);
           $maxid = $query3["maxid"] == null ? 1 : $query3["maxid"] + 1;
           if ( $dc_id > 99 &&  $dc_id < 1000)
           {
               $voucherid = "0" .  $dc_id;
           }
           else
           {
               if ( $dc_id > 9 &&  $dc_id < 100)
               {
                   $voucherid = "00" .  $dc_id;
               }
               else
               {
                   $voucherid = "000" .  $dc_id;
               }
           }
           $sql4 ="SELECT * FROM item
           JOIN unit u ON u.unit_id = item.unit_id
           WHERE item_id ='" . $lst_arr['item_id'] . "'";
           $query4= $connect->query($sql4);
           $row =mysqli_fetch_assoc($query4);
           $item_name =$row['item_name'];
           $unit_name =$row['unit_name'];
           $sql5 ="INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user)
           VALUES($maxid,'$voucherid','$dc_date','Dlry.Conf.','".date("Y-m-d")."','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['dlr_qty'] . "','".$_SESSION['stores_lname']."','Change','".$_SESSION['stores_uname']."')";
           $query5=$connect->query($sql5);
            

        }
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
    elseif ($action == 'delete_record')
    {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT po_id FROM tbldelivery1 WHERE dc_id=$rec_id";
        $query = $connect->query($sql);
        $po_id = mysqli_fetch_assoc($query);
        $po_id = $po_id["po_id"];
        $sql1 ="SELECT * FROM `tbldelivery1` tbl1
        JOIN tblpo tp ON tp.po_id = tbl1.po_id
        JOIN party p on p.party_id = tp.party_id
        WHERE dc_id=$rec_id";
        $query1 =$connect->query($sql1);
        $row =mysqli_fetch_assoc($query1);
        $dc_date =$row['dc_date'];
        $party_name =$row['party_name'];
        $particulars ="From".$party_name;

        
        $sql5 = "DELETE FROM tbldelivery2 WHERE dc_id=$rec_id";
        $query5 = $connect->query($sql5); 

        $sql6 = "DELETE FROM tbldelivery1 WHERE dc_id=$rec_id";
        $query6 = $connect->query($sql6);
        if ($query6 == true)
        {
            //=======================================Logbook======================
     
        $sql2 ="SELECT MAX(rec_id) AS maxid FROM logbook";
        $query2 =$connect->query($sql2);
        $query2 = mysqli_fetch_assoc($query2);
      $max_id = $query2["maxid"] == null ? 1 : $query2["maxid"] + 1;
        if ( $rec_id > 99 &&$rec_id < 1000)
        {
            $voucherid = "0" .$rec_id;
        }
        else
        {
            if ( $rec_id > 9 &&$rec_id < 100)
            {
                $voucherid = "00" .$rec_id;
            }
            else
            {
                $voucherid = "000" .$rec_id;
            }
        }
       
        $sql3 ="INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user)
        VALUES($max_id,'$voucherid','$dc_date','Dlry.Conf.','".date("Y-m-d")."','$particulars','".$_SESSION['stores_lname']."','Delete','".$_SESSION['stores_uname']."')";
        $query3=$connect->query($sql3); 
            
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
    }
}
?>