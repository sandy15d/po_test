<?php
require_once 'db_connect.php';
session_start();
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'indent_list') {
        $uid = $_POST['uid'];
        $sql = "SELECT * FROM users WHERE uid=$uid AND appr_auth=1 AND user_status='A'";
        $query = $connect->query($sql);
        if (mysqli_num_rows($query) > 0) {
            //Logged In User has Approval Authority
            $sql = "SELECT uid FROM users WHERE (repuser_id=$uid OR repuser2_id=$uid OR repuser3_id=$uid OR repuser4_id=$uid OR repuser5_id=$uid OR $uid=1 OR $uid=2 OR $uid=3 OR $uid=4)";
            //$sql ="SELECT uid FROM users";
            $query = $connect->query($sql);
            if (mysqli_num_rows($query) > 0) {
                while ($row = $query->fetch_assoc()) {
                    $u_array[] = $row['uid'];
                    $userids = implode(',', $u_array);
                }
                $sql = "SELECT tbl_indent.*,location_name,staff_name FROM tbl_indent 
                LEFT JOIN location ON tbl_indent.order_from = location.location_id 
                LEFT JOIN staff ON tbl_indent.order_by = staff.staff_id 
                WHERE ind_status='S' AND appr_status='U' 
                AND (indent_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' 
                AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "') 
                AND (tbl_indent.uid=" . $_SESSION['stores_uid'] . " OR tbl_indent.uid in (" . $userids . ")) 
                ORDER BY location_name,indent_date,indent_id";



                $query = $connect->query($sql);
                $output = array(
                    'data' => array()
                );
                $x = 1;
                while ($row = $query->fetch_assoc()) {
                    if ($row['indent_no'] > 999) {
                        $ino = $row['indent_no'];
                    } else {
                        if ($row['indent_no'] > 99 && $row['indent_no'] < 1000) {
                            $ino = "0" . $row['indent_no'];
                        } else {
                            if ($row['indent_no'] > 9 && $row['indent_no'] < 100) {
                                $ino = "00" . $row['indent_no'];
                            } else {
                                $ino = "000" . $row['indent_no'];
                            }
                        }
                    }
                    $indent_no = $row['ind_prefix'] . '/' . $ino;
                    $actionButton = '<button type ="button" class="btn btn-sm" data-toggle="modal" data-target="#viewIndentModal" onclick="getRecord(' . $row['indent_id'] . ')"><span class="glyphicon glyphicon-pencil" style="color:green"></span></button>';
                    $quotation = '<a href="uploads/' . $row['attachment'] . '" target="_blank" style="font-size:14px;">' . $row['attachment'] . '</a>';
                    $output['data'][] = array(
                        $x,
                        $indent_no,
                        $row['indent_date'],
                        $row['supply_date'],
                        $row['location_name'],
                        $row['staff_name'],
                        $quotation,
                        $actionButton

                    );

                    $x++;
                }

                $connect->close();
                echo json_encode($output);
            }
        }
    } elseif ($action == 'get_item_list') {
        $indent_id = $_POST['indent_id'];
        $output = array('data' => array());
        $sql = "SELECT tbl_indent_item.*,item_name,unit_name,ic.category FROM tbl_indent_item 
            LEFT JOIN item ON tbl_indent_item.item_id = item.item_id 
            LEFT JOIN unit ON tbl_indent_item.unit_id = unit.unit_id 
            LEFT JOIN item_category ic ON ic.category_id = tbl_indent_item.item_category
            WHERE indent_id=$indent_id ORDER BY seq_no";
        $query = $connect->query($sql);
        $x = 1;
        while ($row = $query->fetch_assoc()) {


            $output['data'][] = array(
                'sno' =>  $x,
                'item_name' => $row['item_name'] . ' ~~' . $row['category'],
                'desc' => $row['remark'] . '--' . $row['AnyOther'],
                'qty' => $row['qnty'],
                'rec_id' => $row['rec_id'],
                'indent_id' => $row['indent_id'],
                'unit_name' => $row['unit_name']

            );
            $x++;
        }

        echo json_encode($output);
        $connect->close();
    } elseif ($action == 'approve_indent') {
        $indent_id = $_POST['indent_id'];
        $list_array = $_POST['list_array'];
        $date = date('Y-m-d');
        // print_r($list_array);die;
        foreach ($list_array as $lst_arr) {
            $sql1 =  "UPDATE tbl_indent_item SET aprvd_qnty='" . $lst_arr['appr_qty'] . "',aprvd_status='1',aprvd_by='" . $_SESSION['stores_uid'] . "', aprvd_date='$date' WHERE rec_id ='" . $lst_arr['rec_id'] . "'";
            $query1 = $connect->query($sql1);
        }
        $sql = "UPDATE tbl_indent SET appr_status='S', appr_by ='" . $_SESSION['stores_uid'] . "',appr_date ='$date' WHERE indent_id =$indent_id";
        $query = $connect->query($sql);
        if ($query == true) {

            //=================Send Mail to Reporting Manager==================

            $sql1 = "SELECT * FROM `tbl_indent` WHERE `indent_id`=$indent_id";
            $query1 = $connect->query($sql1);
            $ino = mysqli_fetch_assoc($query1);
            $ino = $ino['indent_no'];

            $voucherid = ($ino > 999 ? $ino : ($ino > 99 && $ino < 1000 ? "0" . $ino : ($ino > 9 && $ino < 100 ? "00" . $ino : "000" . $ino)));

            $sql2 = "SELECT tbl_indent.*,location_name FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id WHERE indent_id=$indent_id";
            $query2 = $connect->query($sql2);
            $row = mysqli_fetch_assoc($query2);

            if ($row['ind_prefix'] != null) {
                $voucherid = $row['ind_prefix'] . "/" . $voucherid;
            }
            $dateIndent = date("d-m-Y", strtotime($row['indent_date']));
            $UserId = $row['uid'];
            $i = 0;
            $stext = '';
            $indentInfo = '
            <div style="background-color: #007bff; color: #ffffff; padding: 10px; margin-bottom: 10px;">
            <p style="margin: 0; font-weight: bold;">Indent No.: ' . $voucherid . '&emsp;&emsp; Date: ' . $dateIndent . '</p>
            <p style="margin: 0;">From - ' . $row['location_name'] . '</p>
            </div>';
           
            $stext .= $indentInfo;
            $sql3 = "SELECT ti.*,i.item_name,u.unit_name,ic.category FROM tbl_indent_item ti
                    JOIN item i ON i.item_id = ti.item_id
                    JOIN unit u ON u.unit_id = i.unit_id
                    JOIN item_category ic ON ic.category_id= ti.item_category WHERE ti.indent_id=$indent_id ORDER BY seq_no";
            $query3 = $connect->query($sql3);
            $tableBody = '';
            while ($row = mysqli_fetch_array($query3)) {
                $i++;
                $tableBody .= "<tr>
                            <td style='padding: 10px; border: 1px solid #dfe2e5;'>$i</td>
                            <td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['item_name']}</td>
                            <td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['category']}</td>
                            <td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['qnty']}</td>
                            <td style='padding: 10px; border: 1px solid #dfe2e5;'>{$row['unit_name']}</td>
                        </tr>";
            }
            $tableHeader = '<thead style="background-color: #007bff;color:#ffffff"><tr><th style="padding: 10px; border: 1px solid #dfe2e5;">SNo</th><th style="padding: 10px; border: 1px solid #dfe2e5;">Item Name</th><th style="padding: 10px; border: 1px solid #dfe2e5;">Packing Size/Type</th><th style="padding: 10px; border: 1px solid #dfe2e5;">Quantity</th><th style="padding: 10px; border: 1px solid #dfe2e5;">UOM</th></tr></thead>';
            $table = "<table style='width: 100%; border-collapse: collapse; background-color: #f8f9fa; border: 1px solid #dfe2e5;'>$tableHeader<tbody>$tableBody</tbody></table>";

            $message = "
            $table
            <p style='margin-top: 20px;font-weight:bold'>Kindly go through the website for PO Generation.</p><br><br>
            <p style='margin-bottom: 0px;'>Thanks & Regards</p>
            <p style='margin-top:0px;'>Purchase Operations Team</p>
            <small>*Please do not reply to this email. This is an automated message, and responses cannot be received by our system.</small>
             ";
            $stext .= $message;

           
           $email_to = 'sandeepdewangan2012@gmail.com';
           $subject = "Purchase Order Generation";
           $body = $stext;
           require 'vendor/mail.php';

            $output['success'] = true;
            $output['message'] = "Indert Order Approved Successfully";
        } else {
            $output['success'] = false;
            $output['message'] = "Indert Order failed to approve";
        }
        $connect->close();
        echo json_encode($output);
    }
}
