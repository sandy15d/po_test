<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action']))
{
    $action = $_POST['action'];
    if ($action == 'get_po')
    {
        $bill_date = $_POST['bill_date'];
        $sql = "SELECT tp.po_id,tp.po_no, ti.ind_prefix,c.CCode FROM tblpo tp
       JOIN tbl_indent ti ON ti.indent_id =tp.indent_id
       JOIN company c ON c.company_id =tp.company_id
       WHERE tp.po_status='S' AND tp.po_date<='$bill_date' ORDER BY po_id";
        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        while ($row = $query->fetch_assoc())
        {
            $output['data'][] = array(

                'po_id' => $row['po_id'],
                'ind_prefix' => $row['ind_prefix'],
                'po_no' => $row['po_no'],
                'CCode' => $row['CCode']
            );
        }
        $connect->close();
        echo json_encode(array(
            'data' => $output,
            'status' => 200
        ));
    }
    elseif ($action == 'get_po_detail')
    {
        $po_id = $_POST['po_id'];
        $query = "SELECT tp.po_date,tp.party_id,p.party_name,p.address1,p.address2,p.address3,tp.company_id,c.company_name,ct.city_name,s.state_name FROM tblpo tp 
        JOIN party p ON p.party_id = tp.party_id
        JOIN company c ON c.company_id = tp.company_id
        JOIN city ct ON ct.city_id = p.city_id
        JOIN state s ON s.state_id = ct.state_id
        WHERE tp.po_id=$po_id";
        $result = $connect->query($query);
        $output = $result->fetch_assoc();
        $connect->close();
        echo json_encode(array(
            'status' => 200,
            'data' => $output
        ));
    }
    elseif ($action == 'get_mr_no')
    {
        $po_id = $_POST['po_id'];
        $query = "SELECT * FROM `tblreceipt1` t1 
        JOIN tbldelivery1 td1 ON td1.dc_id = t1.dc_id
        WHERE t1.po_id =$po_id";

        $result = $connect->query($query);
        $output = array(
            'data' => array()
        );
        while ($row = $result->fetch_assoc())
        {
            $output['data'][] = array(
                'receipt_id' => $row['receipt_id'],
                'receipt_prefix' => $row['receipt_prefix'],
                'receipt_no' => $row['receipt_no']

            );
        }
        $connect->close();
        echo json_encode(array(
            'data' => $output,
            'status' => 200
        ));
    }
    elseif ($action == 'get_mr_date')
    {
        $mr_id = $_POST['mr_id'];
        $sql = "SELECT DATE_FORMAT(receipt_date,'%d/%m/%Y') AS receipt_date FROM tblreceipt1 WHERE receipt_id=$mr_id";

        $query = $connect->query($sql);

        $result = mysqli_fetch_assoc($query);
        $result = $result['receipt_date'];

        $connect->close();
        echo json_encode($result);
    }
    elseif ($action == 'save_bill')
    {
        $bill_id = $_POST['bill_id'];
        $bill_no = $_POST['bill_no'];
        $bill_date = $_POST['bill_date'];
        $bill_amt = $_POST['bill_amt'];
        $party_id = $_POST['party_id'];
        $company_id = $_POST['company_id'];

        if (!empty($bill_id))
        {
            //update
            
        }
        else
        {
            //insert
            $sql = "SELECT MAX(bill_id) AS maxid FROM tblbill";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $max_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
            $sql = "INSERT INTO `tblbill` (`bill_id`, `bill_no`, `bill_date`, `party_id`, `company_id`, `bill_amt`) VALUES 
            ('$max_id', '$bill_no', '$bill_date', '$party_id', '$company_id', '$bill_amt')";
            $query = $connect->query($sql);
            if ($query === true)
            {
                $output['success'] = true;
                $output['bill_id'] = $max_id;
            }
            else
            {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }

    }
    elseif ($action == 'get_item_list')
    {
        $mr_id = $_POST['mr_id'];
        $output = array(
            'data' => array()
        );
        $sql = "SELECT tbl2.rec_id, tbl2.item_id,tbl2.item_category,tbl2.unit_id,tbl2.receipt_qnty,u.unit_name,i.item_name,ic.category FROM tblreceipt2 tbl2
        JOIN item i ON i.item_id = tbl2.item_id
        JOIN item_category ic ON ic.category_id = tbl2.item_category
        JOIN tblreceipt1 tbl1 ON tbl1.receipt_id = tbl2.receipt_id
        JOIN unit u ON u.unit_id = tbl2.unit_id
        WHERE tbl2.receipt_id=$mr_id";
        $query = $connect->query($sql);
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $output['data'][] = array(
                'sno' => $x,
                'rec_id'=>$row['rec_id'],
                'item_name' => $row['item_name'].' ~~'.$row['category'],
                'item_id' => $row['item_id'],
                'receipt_qnty' => $row['receipt_qnty'],
                'unit_id' => $row['unit_id'],
                'unit_name'=>$row['unit_name'],
                 'category_id'=>$row['item_category']
            );
            $x++;
        }
        echo json_encode($output);
        $connect->close();
    }
    elseif ($action == 'save_item')
    {
        $po_id = $_POST['po_id'];
        $mr_id = $_POST['mr_id'];
        $bill_id = $_POST['bill_id'];
        $list_array = $_POST['list_array'];
        foreach ($list_array as $lst_arr)
        {
            $sql = "SELECT MAX(rec_id) AS maxid FROM tblbill_item";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;

            $sql = "SELECT Max(seq_no) as maxno FROM tblbill_item WHERE bill_id=$bill_id";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $seq_no = $query["maxno"] == null ? 1 : $query["maxno"] + 1;

            $sql = "INSERT INTO tblbill_item(rec_id,bill_id,po_id,receipt_id,seq_no,item_id,item_category,unit_id,bill_qnty,rate,amt)VALUES
            ($rec_id,$bill_id,$po_id,$mr_id,$seq_no,'" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "','" . $lst_arr['billing_qty'] . "','" . $lst_arr['rate'] . "',(" . $lst_arr['billing_qty'] . "*" . $lst_arr['rate'] . "))";

            $query = $connect->query($sql);
        }

        if ($query == true)
        {
            $output['success'] = true;
            $output['message'] = "Insert Successfully";
        }
        else
        {
            $output['success'] = false;
            $output['message'] = "Failed to Insert";
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'get_bill')
    {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $sql = "SELECT tbl1.*,p.party_name,c.company_name,DATE_FORMAT(bill_date,'%d/%m/%Y') AS bill_date FROM `tblbill` tbl1
            JOIN party p ON p.party_id = tbl1.party_id
            JOIN company c ON c.company_id = tbl1.company_id WHERE bill_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ORDER BY bill_id DESC";
        $query = $connect->query($sql);

        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc())
        {
            $actionButton = '<button type ="button" class="btn btn edit_record"  id="' . $row['bill_id'] . '"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                        <button type ="button"  class="btn btn delete_record"  id="' . $row['bill_id'] . '"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            if($row['bill_return']==1){
                $bill_return ='Yes';
            }else{
                $bill_return ='No';
            }

            if($row['bill_paid']=='Y'){
                $bill_paid ='Yes';
            }else{
                $bill_paid ='No';
            }
            $output['data'][] = array(
                $x,
                $row['bill_no'],
                $row['bill_date'],
                $row['bill_amt'],
                $row['company_name'],
                $row['party_name'],
                $bill_return,
                $bill_paid,
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'delete_record')
    {
        $bill_id = $_POST['bill_id'];
        $sql = "DELETE FROM tblbill WHERE bill_id=$bill_id";
        $query = $connect->query($sql);

        $sql = "DELETE FROM tblbill_item WHERE bill_id=$bill_id";
        $query = $connect->query($sql);

        if ($query === true)
        {
            $output['success'] = true;
            $output['messages'] = 'Purchase Bill Record Delete Successfully...!!!';
        }
        else
        {
            $output['success'] = false;
            $output['messages'] = 'Something Went Wrong, Please Try Again...!!!';
        }
        $connect->close();
        echo json_encode($output);
    }
    elseif ($action == 'get_data_edit')
    {
        $bill_id = $_POST['bill_id'];
        $sql = "SELECT t1.bill_id,ti.rec_id,t1.bill_no,t1.bill_amt,
        ti.item_id,i.item_name,ti.bill_qnty,ti.rate, ti.unit_id,u.unit_name,p.party_name,c.company_name,
        tr.receipt_no,tr.receipt_prefix,ti.receipt_id,ti.po_id,ti.item_category,ic.category FROM tblbill t1 
        JOIN party p ON p.party_id = t1.party_id 
        JOIN company c ON c.company_id = t1.company_id 
        JOIN tblbill_item ti ON ti.bill_id = t1.bill_id 
        JOIN item i ON i.item_id = ti.item_id 
        JOIN unit u ON u.unit_id = ti.unit_id 
        JOIN tblreceipt1 tr ON tr.receipt_id = ti.receipt_id
        JOIN item_category ic ON ic.category_id = ti.item_category
        WHERE t1.bill_id=$bill_id";
        $query = $connect->query($sql);
        $output = array(
            'data' => array()
        );

        while ($row = $query->fetch_assoc())
        {
            if ($row['receipt_no'] > 999)
            {
                $receipt_no = $row['receipt_no'];
            }
            else
            {
                if ($row['receipt_no'] > 99 && $row['receipt_no'] < 1000)
                {
                    $receipt_no = "0" . $row['receipt_no'];
                }
                else
                {
                    if ($row['receipt_no'] > 9 && $row['receipt_no'] < 100)
                    {
                        $receipt_no = "00" . $row['receipt_no'];
                    }
                    else
                    {
                        $receipt_no = "000" . $row['receipt_no'];
                    }
                }
            }
            $receipt_no = $row['receipt_prefix'] . '/' . $receipt_no;
            $output['data'][] = array(
                'bill_id' => $row['bill_id'],
                'po_id' => $row['po_id'],
                'rec_id' => $row['rec_id'],
                'bill_no' => $row['bill_no'],
                'bill_amt' => $row['bill_amt'],
                'company_name' => $row['company_name'],
                'party_name' => $row['party_name'],
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'].' ~~'.$row['category'],
                'bill_qnty' => $row['bill_qnty'],
                'rate' => $row['rate'],
                'unit_name' => $row['unit_name'],
                'unit_id' => $row['unit_id'],
                'receipt_id' => $row['receipt_id'],
                'category_id'=>$row['item_category'],
                'mr_no' => $receipt_no
            );
        }

        echo json_encode(array(
            'bill_detail' => $output,
            'status' => 200
        ));
    }elseif ($action == 'update_item')
    {
        $po_id = $_POST['po_id'];
        $bill_id = $_POST['bill_id'];
        $bill_amt =$_POST['bill_amt'];
        $bill_no =$_POST['bill_no'];
        $list_array = $_POST['list_array'];

        $sql ="UPDATE tblbill SET bill_no ='$bill_no', bill_amt ='$bill_amt' WHERE bill_id =$bill_id";
        $query=$connect->query($sql);

        $sql ="DELETE FROM tblbill_item WHERE bill_id =$bill_id";
        $query=$connect->query($sql);
        foreach ($list_array as $lst_arr)
        {
            $sql = "SELECT MAX(rec_id) AS maxid FROM tblbill_item";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;

            $sql = "SELECT Max(seq_no) as maxno FROM tblbill_item WHERE bill_id=$bill_id";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $seq_no = $query["maxno"] == null ? 1 : $query["maxno"] + 1;

            $sql = "INSERT INTO tblbill_item(rec_id,bill_id,po_id,receipt_id,seq_no,item_id,item_category,unit_id,bill_qnty,rate,amt)VALUES
            ($rec_id,$bill_id,$po_id,'" . $lst_arr['receipt_id'] . "',$seq_no,'" . $lst_arr['item_id'] . "','".$lst_arr['category_id']."','" . $lst_arr['unit_id'] . "','" . $lst_arr['billing_qty'] . "','" . $lst_arr['rate'] . "',(" . $lst_arr['billing_qty'] . "*" . $lst_arr['rate'] . "))";

            $query = $connect->query($sql);
        } 

        if ($query == true)
        {
            $output['success'] = true;
            $output['message'] = " Update Successfully";
        }
        else
        {
            $output['success'] = false;
            $output['message'] = "Failed to update";
        }
        $connect->close();
        echo json_encode($output);
    }
}

?>