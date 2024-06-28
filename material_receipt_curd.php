<?php
require_once 'db_connect.php';
session_start();
$sql_user = "SELECT * FROM users WHERE uid=".$_SESSION["stores_uid"];
$row_user =$connect->query($sql_user);
$row_user = mysqli_fetch_assoc($row_user);
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if ($action == 'get_mr')
    {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $sql = "SELECT tblreceipt1.*, dc_no, dc_date, location_name, party_name,ti.ind_prefix 
        FROM tblreceipt1 
        INNER JOIN tbldelivery1 ON tblreceipt1.dc_id = tbldelivery1.dc_id 
        INNER JOIN location ON tblreceipt1.recd_at = location.location_id 
        INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id 
        JOIN tbl_indent ti ON ti.indent_id = tblpo.indent_id
        INNER JOIN party ON tblpo.party_id = party.party_id 
        WHERE receipt_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        $query = $connect->query($sql);
        //echo json_encode($sql);die;
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
               if($row_user['mr2']==1){
            $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#material_modal" onclick="editMaterial(' . $row['receipt_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeMaterialModal" onclick="removeMaterial(' . $row['receipt_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
               }else{
                   $actionButton ='-';
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

            $rcpt_no = $row['receipt_prefix'] . '/' . $rcpt_no;

            $output['data'][] = array(
                $x,
                $rcpt_no,
                $row['receipt_date'],
                $dc_no,
                $row['dc_date'],
                $row['location_name'],
                $row['party_name'],
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'get_detail')
    {
        $dc_id = $_POST['dc_id'];
        $sql = "SELECT tbldelivery1.*, po_no, po_date, delivery_date, party_name, address1, address2, address3, 
        city_name, state_name, location_name,ti.ind_prefix,c.CCode FROM tbldelivery1 
        INNER JOIN tblpo ON tbldelivery1.po_id = tblpo.po_id
        JOIN tbl_indent ti ON ti.indent_id =tblpo.indent_id
        JOIN company c ON c.company_id =tblpo.company_id
        INNER JOIN party ON tblpo.party_id = party.party_id 
        INNER JOIN location ON tblpo.delivery_at = location.location_id 
        INNER JOIN city ON party.city_id = city.city_id 
        INNER JOIN state ON city.state_id = state.state_id 
        WHERE dc_id=$dc_id";
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'save_material_receipt')
    {
        $receipt_id = $_POST['receipt_id'];
        $receipt_date = $_POST['receipt_date'];
        $dc_no = $_POST['dc_no'];
        $dc_date = $_POST['dc_date'];
        $chalan = $_POST['chalan'];
        $chalan_date = $_POST['chalan_date'];
        $invoice_no = $_POST['invoice_no'];
        $invoice_date = $_POST['invoice_date'];
        $delivery_at = $_POST['delivery_at'];
        $req_date = $_POST['req_date'];
        $transit_point = $_POST['transit_point'];
        $rec_at = $_POST['rec_at'];
        $rec_by = $_POST['rec_by'];
        $f_paid = $_POST['f_paid'];
        $f_amount = $_POST['f_amount'];
        $po_id = $_POST['po_id'];

        $sql = "SELECT Max(receipt_id) as maxid FROM tblreceipt1";
        $query = $connect->query($sql);
        $row = mysqli_fetch_array($query);
        $mid = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);

        $sql = "SELECT Max(receipt_no) as maxno FROM tblreceipt1 WHERE recd_at=$rec_at AND (receipt_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')";
        $query = $connect->query($sql);
        $row = mysqli_fetch_array($query);
        $rno = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);

        $sql = "SELECT location_prefix FROM location WHERE location_id=$rec_at";

        $query = $connect->query($sql);
        $row = mysqli_fetch_array($query);
        $loc_prefix = $row['location_prefix'];
        if (empty($chalan))
        {
            $chalan_date = NULL;
        }

        if ($receipt_id != '')
        {
            //Update Existing Material Receipt
            $sql = "UPDATE tblreceipt1 SET receipt_date='$receipt_date', receipt_no=$rno, dc_id=$dc_no, po_id=$po_id,challan_no=$chalan,
            receipt_prefix='$loc_prefix', challan_date='$chalan_date',invoice_no =$invoice_no,invoice_date='$invoice_date', transit_point=$transit_point, recd_at=$rec_at, recd_by=$rec_by, 
            freight_paid='$f_paid', freight_amt =$f_amount WHERE receipt_id=$receipt_id";
            $query = $connect->query($sql);
            $sql1 = "SELECT receipt_no,receipt_prefix FROM tblreceipt1 WHERE receipt_id =$receipt_id";
            $query = $connect->query($sql1);
            $row = mysqli_fetch_array($query);

            if ($row['receipt_no'] > 99 && $row['receipt_no'] < 1000)
            {
                $recpt_no = "0" . $row['receipt_no'];
            }
            else
            {
                if ($row['receipt_no'] > 9 && $row['receipt_no'] < 100)
                {
                    $recpt_no = "00" . $row['receipt_no'];
                }
                else
                {
                    $recpt_no = "000" . $row['receipt_no'];
                }
            }
            $receipt_no = $row['receipt_prefix'] . '/' . $recpt_no;
            $output['receipt_id'] = $receipt_id;
            $output['receipt_no'] = $receipt_no;
            $output['success'] = true;

            $connect->close();
            echo json_encode($output);
        }
        else
        {
            //Insert New Material Receipt
            $sql = "INSERT INTO tblreceipt1(receipt_id, receipt_date, receipt_no, dc_id, po_id,challan_no, receipt_prefix, challan_date, transit_point, recd_at, recd_by, freight_paid, freight_amt,invoice_no,invoice_date) 
            VALUES('$mid','$receipt_date','$rno','$dc_no',$po_id,'$chalan','$loc_prefix','$chalan_date','$transit_point','$rec_at','$rec_by','$f_paid','$f_amount','$invoice_no', '$invoice_date')";
            $query = $connect->query($sql);
            $sql = "SELECT receipt_id FROM tblreceipt1 ORDER BY receipt_id DESC limit 0 ,1";

            $query = $connect->query($sql);
            $row = mysqli_fetch_array($query);
            $receipt_id = $row['receipt_id'];

            $sql = "SELECT receipt_no,receipt_prefix FROM tblreceipt1 WHERE receipt_id =$receipt_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_array($query);

            if ($row['receipt_no'] > 99 && $row['receipt_no'] < 1000)
            {
                $recpt_no = "0" . $row['receipt_no'];
            }
            else
            {
                if ($row['receipt_no'] > 9 && $row['receipt_no'] < 100)
                {
                    $recpt_no = "00" . $row['receipt_no'];
                }
                else
                {
                    $recpt_no = "000" . $row['receipt_no'];
                }
            }
            $receipt_no = $row['receipt_prefix'] . '/' . $recpt_no;
            $output['receipt_id'] = $receipt_id;
            $output['receipt_no'] = $receipt_no;
            $output['success'] = true;
            $connect->close();
            echo json_encode($output);

        }

    }
    elseif ($action == 'Get_Item')
    {
        $dc_id = $_POST['dc_id'];
        $output = array(
            'data' => array()
        );
        $sql = "SELECT i.item_id,i.item_name,u.unit_id,u.unit_name,tbld.delivery_qnty,ic.category,ic.category_id FROM `tbldelivery2` tbld
        JOIN item i ON i.item_id =tbld.item_id
        JOIN unit u on u.unit_id = tbld.unit_id
        JOIN item_category ic ON ic.category_id = tbld.item_category
        WHERE tbld.dc_id =$dc_id";
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
                'qty' => $row['delivery_qnty'],
                'category_id'=>$row['category_id']
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'save_item')
    {
        $receipt_id = $_POST['receipt_id'];

        $dc_id = $_POST['dc_id'];

        $list_array = $_POST['list_array'];

        $sql = "SELECT * FROM `tblreceipt1` tbl1 JOIN tblpo on tblpo.po_id = tbl1.po_id
        JOIN party p ON p.party_id = tblpo.party_id WHERE receipt_id=$receipt_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $receipt_date = $row['receipt_date'];
        $rec_at =$row['recd_at'];
        $party_name = $row['party_name'];
        $particulars = "From" . $party_name;

        foreach ($list_array as $lst_arr)
        {
            $sql = "SELECT MAX(rec_id) as maxid FROM tblreceipt2";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;

            $sql = "SELECT MAX(seq_no) as maxno FROM tblreceipt2 WHERE receipt_id=$receipt_id";
            $query = $connect->query($sql);
            $seq_no = mysqli_fetch_assoc($query);
            $seq_no = $seq_no['maxno'] == null ? 1 : $seq_no['maxno'] + 1;

            $sql1 = "SELECT count('rec_id') as total,seq_no FROM tblreceipt2 WHERE receipt_id = $receipt_id AND item_id ='" . $lst_arr['item_id'] . "' AND item_category='".$lst_arr['category_id']."'";

            $query1 = $connect->query($sql1);
            $row = mysqli_fetch_array($query1);
            $count = $row['total'];
            $s_qno =$row['seq_no'];

            if ($count > 0)
            {
                //Update Table Receipt
                $sql2 = "UPDATE tblreceipt2 SET receipt_qnty='" . $lst_arr['recd_qty'] . "' WHERE receipt_id=$receipt_id AND item_id='" . $lst_arr['item_id'] . "' AND item_category ='".$lst_arr['category_id']."'";
                $query = $connect->query($sql2);

                if ($query == true)
                {
                   //========================Update Stock Register=======================
                   $str ="UPDATE stock_register SET entry_date='".$receipt_date."',item_id='".$lst_arr['item_id']."',item_category='".$lst_arr['category_id']."',item_qnty='".$lst_arr['recd_qty']."',unit_id='".$lst_arr['unit_id']."' WHERE entry_mode='R+' AND entry_id=$receipt_id AND seq_no =$s_qno AND location_id=$rec_at";
                
                   $result =$connect->query($str);
                   
                   //======================Insert into logbook=====================
                    $sql3 = "SELECT MAX(rec_id) AS maxid FROM logbook";
                    $query3 = $connect->query($sql3);
                    $query3 = mysqli_fetch_assoc($query3);
                    $maxid = $query3["maxid"] == null ? 1 : $query3["maxid"] + 1;
                    if ($dc_id > 99 && $dc_id < 1000)
                    {
                        $voucherid = "0" . $dc_id;
                    }
                    else
                    {
                        if ($dc_id > 9 && $dc_id < 100)
                        {
                            $voucherid = "00" . $dc_id;
                        }
                        else
                        {
                            $voucherid = "000" . $dc_id;
                        }
                    }

                    $sql4 = "SELECT * FROM item JOIN unit u ON u.unit_id = item.unit_id WHERE item_id ='" . $lst_arr['item_id'] . "'";
                    $query4 = $connect->query($sql4);
                    $row = mysqli_fetch_assoc($query4);
                    $item_name = $row['item_name'];
                    $unit_name = $row['unit_name'];

                    $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
                              
                    $query =$connect->query($sql4);
                    $row=mysqli_fetch_assoc($query);
                 
                   $category_name =$row['category'];

                    $sql5 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user)
                    VALUES($maxid,'$voucherid','$receipt_date','Mtrl.Rcpt.','" . date("Y-m-d") . "','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['recd_qty'] . "','" . $_SESSION['stores_lname'] . "','Update','" . $_SESSION['stores_uname'] . "')";
                    $query5 = $connect->query($sql5);
                    //=======================================
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
                $sql = "INSERT INTO tblreceipt2(rec_id,receipt_id,seq_no,item_id,item_category,unit_id,receipt_qnty)VALUES('" . $rec_id . "','" . $receipt_id . "','" . $seq_no . "','" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "','" . $lst_arr['recd_qty'] . "')";
                $query = $connect->query($sql);

                $sql1 = "UPDATE tbldelivery2 SET item_received='Y' WHERE dc_id='" . $dc_id . "' AND item_id='" . $lst_arr['item_id'] . "'";
                $query1 = $connect->query($sql1);
                if ($query1 == true)
                {
                    //====================Insert into Stock Register===========================
                        $str = "SELECT Max(stock_id) as maxid FROM stock_register";
                        $row =$connect->query($str);
                        $row =mysqli_fetch_assoc($row);
                        $sid = ($row["maxid"]==null ? 1 : $row["maxid"] + 1);
                        $str1 ="INSERT INTO stock_register (stock_id,entry_mode,entry_id,entry_date,seq_no,location_id,item_id,item_category,item_qnty,unit_id) 
                        VALUES($sid,'R+','$receipt_id','$receipt_date',$seq_no,$rec_at,'" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['recd_qty'] . "','" . $lst_arr['unit_id'] . "')";
                        $result =$connect->query($str1);
                    //====================Insert into Log Book======================
                    $sql3 = "SELECT MAX(rec_id) AS maxid FROM logbook";
                    $query3 = $connect->query($sql3);
                    $query3 = mysqli_fetch_assoc($query3);
                    $maxid = $query3["maxid"] == null ? 1 : $query3["maxid"] + 1;
                    if ($dc_id > 99 && $dc_id < 1000)
                    {
                        $voucherid = "0" . $dc_id;
                    }
                    else
                    {
                        if ($dc_id > 9 && $dc_id < 100)
                        {
                            $voucherid = "00" . $dc_id;
                        }
                        else
                        {
                            $voucherid = "000" . $dc_id;
                        }
                    }

                    $sql4 = "SELECT * FROM item JOIN unit u ON u.unit_id = item.unit_id WHERE item_id ='" . $lst_arr['item_id'] . "'";
                    $query4 = $connect->query($sql4);
                    $row = mysqli_fetch_assoc($query4);
                    $item_name = $row['item_name'];
                    $unit_name = $row['unit_name'];

                    $sql4 ="SELECT category FROM item_category WHERE category_id ='".$lst_arr['category_id']."'";
                              
                    $query =$connect->query($sql4);
                    $row=mysqli_fetch_assoc($query);
                 
                   $category_name =$row['category'];

                    $sql5 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,location,action,user) VALUES($maxid,'$voucherid','$receipt_date','Mtrl.Rcpt.','" . date("Y-m-d") . "','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['recd_qty'] . "','" . $_SESSION['stores_lname'] . "','New','" . $_SESSION['stores_uname'] . "')";
                    $query5 = $connect->query($sql5);
                    //=================================================================

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
    }
    elseif ($action == 'delete_record')
    {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT * FROM `tblreceipt1` tbl1
        JOIN tblpo on tblpo.po_id = tbl1.po_id
        JOIN party p ON p.party_id = tblpo.party_id
        WHERE receipt_id=$rec_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $dc_id = $row["dc_id"];
        $receipt_date = $row['receipt_date'];
        $party_name = $row['party_name'];
        $particulars = "From" . $party_name;

        $sql = "UPDATE tbldelivery2 SET item_received='N' WHERE dc_id=$dc_id";
        $query = $connect->query($sql);

        $sql = "DELETE FROM tblreceipt2 WHERE receipt_id=$rec_id";
        $query = $connect->query($sql);

        $sql = "DELETE FROM tblreceipt1 WHERE receipt_id=$rec_id";
        $query = $connect->query($sql);
        if ($query == true)
        {
            //=======================================Logbook======================
            $sql2 = "SELECT MAX(rec_id) AS maxid FROM logbook";
            $query2 = $connect->query($sql2);
            $query2 = mysqli_fetch_assoc($query2);
            $max_id = $query2["maxid"] == null ? 1 : $query2["maxid"] + 1;

            if ($rec_id > 99 && $rec_id < 1000)
            {
                $voucherid = "0" . $rec_id;
            }
            else
            {
                if ($rec_id > 9 && $rec_id < 100)
                {
                    $voucherid = "00" . $rec_id;
                }
                else
                {
                    $voucherid = "000" . $rec_id;
                }
            }

            $sql3 = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user)
            VALUES($max_id,'$voucherid','$receipt_date','Mtrl.Rcpt.','" . date("Y-m-d") . "','$particulars','" . $_SESSION['stores_lname'] . "','Delete','" . $_SESSION['stores_uname'] . "')";
            $query3 = $connect->query($sql3);

            //====================Delete from Stock Register==========================//
            $str ="DELETE FROM stock_register WHERE entry_mode='R+' AND entry_id =$rec_id";
            $result =$connect->query($str);

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
    elseif ($action == 'edit_material_receipt')
    {
        $rec_id = $_POST['rec_id'];
        $sql = "SELECT tbl1.*,l.location_name as transit_point_name,l2.location_name as received_at,s.staff_name as received_by,
            tbld1.*,tblpo.party_id,tblpo.delivery_date,tblpo.delivery_at,tblpo.po_no,tblpo.po_date,l3.location_name as delivery_location,
            p.*,c.*,st.state_name FROM tblreceipt1  tbl1
            INNER JOIN location l ON l.location_id =tbl1.transit_point
            INNER JOIN location l2 ON l2.location_id = tbl1.recd_at
            INNER JOIN staff s ON s.staff_id = tbl1.recd_by
            INNER JOIN tbldelivery1 tbld1 on tbld1.dc_id = tbl1.dc_id
            INNER JOIN tblpo on tblpo.po_id =tbld1.po_id
            INNER JOIN location l3 ON l3.location_id =tblpo.delivery_at
            JOIN party p ON p.party_id = tblpo.party_id
            INNER JOIN city c ON c.city_id = p.city_id
            INNER JOIN state st ON st.state_id =c.state_id
            WHERE tbl1.receipt_id=$rec_id";
        $query = $connect->query($sql);
        $output = mysqli_fetch_assoc($query);
        $connect->close();
        echo json_encode($output);
    }
}
?>