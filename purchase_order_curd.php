<?php
require_once 'db_connect.php';
session_start();
$sql_user = "SELECT  po1,po2,po3,po4 FROM users WHERE uid=" . $_SESSION["stores_uid"];
$row_user = $connect->query($sql_user);
$row_user = mysqli_fetch_assoc($row_user);

if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'get_po') {
        $search = $_POST['search'];
        if ($search == 'unsent') {
            //Unsent PO List
            $sql = "SELECT tblpo.*, party_name, company.CCode, company_name,ti.ind_prefix FROM tblpo
            INNER JOIN party ON tblpo.party_id = party.party_id
            INNER JOIN company ON tblpo.company_id = company.company_id
            JOIN tbl_indent ti on ti.indent_id = tblpo.indent_id
            WHERE po_status='U' AND (po_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "'
                AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "') ORDER BY po_id DESC";
            $query = $connect->query($sql);
            $output = array(
                'data' => array()
            );
            $x = 1;
            while ($row = $query->fetch_assoc()) {
                if ($row_user['po2'] == 1) {
                    $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#pomodal" onclick="editPO(' . $row['po_id'] . ')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removePOModal" onclick="removePO(' . $row['po_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
                } else {
                    $actionButton = '-';
                }
                if ($row_user['po2'] == 4) {
                    $processbtn = '<a href="newpurchaseorder.php?po_id=' . $row['po_id'] . '" target="_blank"><span class="glyphicon glyphicon-print"
                    style="color:green"></span></a>';
                } else {
                    $processbtn = '-';
                }
                if ($row['po_no'] > 999) {
                    $po_no = $row['po_no'];
                } else {
                    if ($row['po_no'] > 99 && $row['po_no'] < 1000) {
                        $po_no = "0" . $row['po_no'];
                    } else {
                        if ($row['po_no'] > 9 && $row['po_no'] < 100) {
                            $po_no = "00" . $row['po_no'];
                        } else {
                            $po_no = "000" . $row['po_no'];
                        }
                    }
                }
                $po_no = $row['CCode'] . ' /' . $row['ind_prefix'] . ' /' . $po_no;
                $output['data'][] = array(
                    $x,
                    $po_no,
                    $row['po_date'],
                    $row['party_name'],
                    $row['company_name'],
                    $actionButton,
                    $processbtn
                );
                $x++;
            }
            $connect->close();
            echo json_encode($output);
        } else {
            //sent Po List
            $sql = "SELECT tblpo.*, party_name, company.CCode, company_name,ti.ind_prefix FROM tblpo
            INNER JOIN party ON tblpo.party_id = party.party_id
            INNER JOIN company ON tblpo.company_id = company.company_id
            JOIN tbl_indent ti on ti.indent_id = tblpo.indent_id
            WHERE po_status='S' AND (po_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "'
                AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "') ORDER BY po_id DESC";
            $query = $connect->query($sql);
            $output = array(
                'data' => array()
            );
            $x = 1;
            while ($row = $query->fetch_assoc()) {
                if ($row_user['po2'] == 1) {
                    $actionButton = '<button type="button" class="btn btn " onclick="recallPO(' . $row['po_id'] . ')"><span
                class="glyphicon glyphicon-repeat" style="color:green"></span></button>
                 <button type="button" class="btn btn delete" data-toggle="modal" data-target="#removePOModal"
              onclick="removePO(' . $row['po_id'] . ')"><span class="glyphicon glyphicon-trash"
                style="color:red"></span></button>';
                } else {
                    $actionButton = '-';
                }
                if ($row_user['po4'] == 1) {
                    $processbtn = '<a href="newpurchaseorder.php?po_id=' . $row['po_id'] . '" target="_blank"><span class="glyphicon glyphicon-print"
                style="color:green"></span></a>';
                } else {
                    $processbtn = '-';
                }
                if ($row['po_no'] > 999) {
                    $po_no = $row['po_no'];
                } else {
                    if ($row['po_no'] > 99 && $row['po_no'] < 1000) {
                        $po_no = "0" . $row['po_no'];
                    } else {
                        if ($row['po_no'] > 9 && $row['po_no'] < 100) {
                            $po_no = "00" . $row['po_no'];
                        } else {
                            $po_no = "000" . $row['po_no'];
                        }
                    }
                }
                $po_no = $row['CCode'] . ' /' . $row['ind_prefix'] . ' /' . $po_no;
                $output['data'][] = array(
                    $x,
                    $po_no,
                    $row['po_date'],
                    $row['party_name'],
                    $row['company_name'],
                    $actionButton,
                    $processbtn
                );
                $x++;
            }
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'get_company_detail') {
        $ship = $_POST['ship'];
        $company = $_POST['company_name'];
        if ($ship == 1) {
            $sql = "SELECT * FROM `company` cmp
                JOIN city c ON c.city_id =cmp.c_cityid
                JOIN state s ON s.state_id =c.state_id WHERE company_id=$company";
            $query = $connect->query($sql);
            $output = $query->fetch_assoc();
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'get_party_detail') {
        $party = $_POST['party_name'];
        $sql = "SELECT * FROM `party` p
                JOIN city c ON c.city_id =p.city_id
                JOIN state s on s.state_id =c.state_id WHERE party_id=$party";
        $query = $connect->query($sql);
        $output = $query->fetch_assoc();
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'get_company_detail1') {
        $company = $_POST['company_name'];
        $sql = "SELECT * FROM `company` cmp
                JOIN city c ON c.city_id =cmp.c_cityid
                JOIN state s ON s.state_id =c.state_id WHERE company_id=$company";
        $query = $connect->query($sql);
        $output = $query->fetch_assoc();
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'save_po') {
        $po_id = $_POST['po_id'];
        $po_no = $_POST['po_no'];
        $indent_id = $_POST['indent_id'];
        $po_date = $_POST['po_date'];
        $party_name = $_POST['party_name'];
        $ship_to = $_POST['ship_to'];
        $shipping_name1 = $_POST['shipping_name1'];
        $shipping_name = $_POST['shipping_name'];
        $ref = $_POST['ref'];
        $terms = $_POST['terms'];
        $company_name = $_POST['company_name'];
        $ship_method = $_POST['ship_method'];
        $work_order = $_POST['work_order'];
        $delivery_at = $_POST['delivery_at'];
        $ship_date = $_POST['ship_date'];
        if ($ship_to == 1) {
            $shipname = $shipping_name1;
        } else {
            $shipname = $shipping_name;
        }

        if (!empty($po_id)) {

            //update
            $sql = "UPDATE tblpo SET
                po_date='$po_date',indent_id=$indent_id,party_id='$party_name',company_id='$company_name',shipto='$ship_to',
                shipping_id='$shipname',ship_method='$ship_method',delivery_date='$ship_date',
                delivery_at='$delivery_at',vendor_ref='$ref',terms_condition='$terms',work_order='$work_order' WHERE
                po_id=$po_id";

            $query = $connect->query($sql);
            //===================================Logbook Insert========================//
            $sql = "SELECT MAX(rec_id) AS maxid FROM logbook";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
            if ($po_id > 99 && $po_id < 1000) {
                $voucherid = "0" . $po_id;
            } else {
                if ($po_id > 9 && $po_id < 100) {
                    $voucherid = "00" . $po_id;
                } else {
                    $voucherid = "000" . $po_id;
                }
            }

            $sql = "SELECT party_name FROM party WHERE party_id=$party_name";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $p_name = $query['party_name'];
            $particulars = "From " . $p_name;
            $sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','" . $particulars . "','" . $_SESSION["stores_lname"] . "','Change','" . $_SESSION["stores_uname"] . "')";
            $query = $connect->query($sql);

            if ($query === true) {
                $validator['success'] = true;
                $validator['messages'] = "Successfully Updated Purchase Order";
                $validator['po_id'] = $po_id;
            } else {
                $validator['success'] = false;
                $validator['messages'] = "Error while Updating the Purchase Order";
            }
            $connect->close();
            echo json_encode($validator);
        } else {
            //insert
            $sql = "SELECT Max(po_id) as maxid FROM tblpo";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $po_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
            $sql = "SELECT Max(po_no) as maxno FROM tblpo WHERE po_date BETWEEN
                '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "' AND
                '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "' AND company_id=$company_name";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);

            if ($query["maxno"] == null || $query["maxno"] == '' || $query['maxno'] == 0) {
                $po_no = 1;
            } else {
                $po_no = $query['maxno'] + 1;
            }
            //  $po_no = $query["maxno"] == null ? 1 : $query["maxno"] + 1;
            $sql = "INSERT INTO
                tblpo(po_id,po_date,po_no,indent_id,party_id,company_id,shipto,shipping_id,delivery_date,delivery_at,vendor_ref,terms_condition,work_order,ship_method)
                VALUES
                ('$po_id','$po_date','$po_no',$indent_id,'$party_name','$company_name','$ship_to','$shipname','$ship_date','$delivery_at','$ref','$terms','$work_order','$ship_method')";
            $query = $connect->query($sql);

            $last_id = "SELECT po_id FROM tblpo ORDER BY po_id DESC limit 0 ,1";

            $query1 = $connect->query($last_id);

            $id = mysqli_fetch_assoc($query1);
            $id = $id['po_id'];
            if ($query === true) {
                //===================================Logbook Insert========================//
                $sql = "SELECT MAX(rec_id) AS maxid FROM logbook";
                $query = $connect->query($sql);
                $query = mysqli_fetch_assoc($query);
                $rec_id = $query["maxid"] == null ? 1 : $query["maxid"] + 1;
                if ($id > 99 && $id < 1000) {
                    $voucherid = "0" . $id;
                } else {
                    if ($id > 9 && $id < 100) {
                        $voucherid = "00" . $id;
                    } else {
                        $voucherid = "000" . $id;
                    }
                }

                $sql = "SELECT party_name FROM party WHERE party_id=$party_name";
                $query = $connect->query($sql);
                $query = mysqli_fetch_assoc($query);
                $p_name = $query['party_name'];
                $particulars = "From " . $p_name;
                $sql = "INSERT INTO logbook (rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user) VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','" . $particulars . "','" . $_SESSION["stores_lname"] . "','New','" . $_SESSION["stores_uname"] . "')";
                $query = $connect->query($sql);
                $validator['success'] = true;
                $validator['messages'] = "Successfully Created a new Purchase Order";
                $validator['po_id'] = $id;
            } else {
                $validator['success'] = false;
                $validator['messages'] = "Error while createing the Purchase Order";
            }

            $connect->close();
            echo json_encode($validator);
        }
    } elseif ($action == 'get_view') {

        $po_id = $_POST['po_id'];
        $sql = "SELECT tp.*, p.party_id,p.party_name, p.address1 as party_address1,
                p.address2 as party_address2,p.address3 as party_address3, p.contact_person as party_contact,
                p.tin as party_tin, c1.city_name as party_city,s1.state_name as party_state, cmp.company_name,
                cmp.c_address1 as cmp_address1, cmp.c_address2 as cmp_address2, cmp.c_address3 as cmp_address3,
                c2.city_name as cmp_city, s2.state_name as cmp_state, l.location_name as delivery_city, cmp1.company_id as
                ship_id,
                cmp1.company_name as ship_name,cmp.CCode,ti.ind_prefix,indent_no FROM tblpo tp
                JOIN party p ON p.party_id = tp.party_id
                JOIN company cmp ON cmp.company_id =tp.company_id
                JOIN city c1 ON c1.city_id =p.city_id
                JOIN state s1 ON s1.state_id =c1.state_id
                JOIN city c2 ON c2.city_id =cmp.c_cityid
                JOIN state s2 ON s2.state_id =c2.state_id
                left JOIN location l ON l.location_id = tp.delivery_at
                JOIN company cmp1 ON cmp1.company_id = tp.shipping_id
                JOIN tbl_indent ti ON ti.indent_id =tp.indent_id
                WHERE po_id=$po_id";
        $query = $connect->query($sql);
        $result = $query->fetch_assoc();
        $connect->close();

        echo json_encode($result);
    } elseif ($action == 'delete_po') {


        $po_id = $_POST['po_id'];

        $sql = "SELECT * FROM tblpo WHERE po_id =$po_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $po_date = $row['po_date'];

        $party = $row['party_id'];
        $sql = "SELECT party_name FROM party WHERE party_id=$party";
        $query = $connect->query($sql);
        $query = mysqli_fetch_assoc($query);
        $p_name = $query['party_name'];
        $particulars = "From " . $p_name;
        $sql = "SELECT Max(rec_id) as maxid FROM logbook";
        $sql = $connect->query($sql);
        $row = mysqli_fetch_assoc($sql);
        $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
        if ($po_id > 99 && $po_id < 1000) {
            $voucherid = "0" . $po_id;
        } else {
            if ($po_id > 9 && $po_id < 100) {
                $voucherid = "00" . $po_id;
            } else {
                $voucherid = "000" . $po_id;
            }
        }

        $sql = "INSERT INTO logbook(rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user)
            VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','$particulars','" . $_SESSION["stores_lname"] . "','Delete','" . $_SESSION["stores_uname"] . "')";
        $query = $connect->query($sql);


        $sql3 = "SELECT indent_id FROM tblpo WHERE po_id=$po_id";
        $query3 = $connect->query($sql3);
        $indent_id = mysqli_fetch_assoc($query3);
        $indent_id = $indent_id['indent_id'];

        $sql4 = "UPDATE tbl_indent_item SET item_ordered='N' WHERE indent_id=$indent_id";
        $query4 = $connect->query($sql4);

        $sql1 = "DELETE FROM tblpo_item WHERE po_id =$po_id";
        $query1 = $connect->query($sql1);

        $sql2 = "DELETE FROM tblpo_dtm WHERE po_id=$po_id";
        $query2 = $connect->query($sql2);

        $sql = "DELETE FROM tblpo WHERE po_id =$po_id";
        $query = $connect->query($sql);
        if ($query == true) {



            $output['success'] = true;
            $output['messages'] = 'PO deleted successfully';
        } else {
            $output['success'] = false;
            $output['messages'] = 'Error while deleting the Purchase order...';
        }

        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'po_recall') {
        $po_id = $_POST['po_id'];
        $sql = "UPDATE tblpo SET po_status ='U' WHERE po_id =$po_id";
        $query = $connect->query($sql);
        if ($query == true) {

            $sql3 = "SELECT indent_id FROM tblpo WHERE po_id=$po_id";
            $query3 = $connect->query($sql3);
            $indent_id = mysqli_fetch_assoc($query3);
            $indent_id = $indent_id['indent_id'];

            $sql = "SELECT * FROM tblpo WHERE po_id =$po_id";
            $query = $connect->query($sql);
            $row = mysqli_fetch_assoc($query);
            $po_date = $row['po_date'];

            $party = $row['party_id'];
            $sql = "SELECT party_name FROM party WHERE party_id=$party";
            $query = $connect->query($sql);
            $query = mysqli_fetch_assoc($query);
            $p_name = $query['party_name'];
            $particulars = "From " . $p_name;
            $sql = "SELECT Max(rec_id) as maxid FROM logbook";
            $sql = $connect->query($sql);
            $row = mysqli_fetch_assoc($sql);
            $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
            if ($po_id > 99 && $po_id < 1000) {
                $voucherid = "0" . $po_id;
            } else {
                if ($po_id > 9 && $po_id < 100) {
                    $voucherid = "00" . $po_id;
                } else {
                    $voucherid = "000" . $po_id;
                }
            }

            $sql = "INSERT INTO logbook(rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,location,action,user)
                VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','$particulars','" . $_SESSION["stores_lname"] . "','Recall','" . $_SESSION["stores_uname"] . "')";
            $query = $connect->query($sql);

            $sql4 = "UPDATE tbl_indent_item SET item_ordered='N' WHERE indent_id=$indent_id";
            $query4 = $connect->query($sql4);

            //  $sql1 = "DELETE FROM tblpo_item WHERE po_id =$po_id";
            //    $query1 = $connect->query($sql1);

            //  $sql2 = "DELETE FROM tblpo_dtm WHERE po_id=$po_id";
            //  $query2 = $connect->query($sql2);

            //    $sql5 ="DELETE FROM tblpo_spec WHERE po_id=$po_id";
            //    $query5=$connect->query($sql5);
            $output['success'] = true;
            $output['messages'] = 'Purchase Order Recalled Successfully';
            $connect->close();
            echo json_encode($output);
        } else {
            $output['success'] = false;
            $output['messages'] = 'Failed to Recall Purchase Order. Please try again';
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'getIndent') {
        $sql = "SELECT tbl_indent.*, location_name, staff_name FROM tbl_indent
        INNER JOIN location ON tbl_indent.order_from = location.location_id
        INNER JOIN staff ON tbl_indent.order_by = staff.staff_id
        WHERE (indent_date BETWEEN '" . date("Y-m-d", strtotime($_SESSION['stores_syr'])) . "'
        AND '" . date("Y-m-d", strtotime($_SESSION['stores_eyr'])) . "')
        AND appr_status='S' AND indent_id
        IN (SELECT DISTINCT indent_id FROM tbl_indent_item WHERE item_ordered='N' AND aprvd_status=1)
        ORDER BY location_name, indent_date, indent_id";
        $query = $connect->query($sql);
        $output = array(
            'data' => array()
        );
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn view" data-toggle="modal" data-target="#IndentModal" onclick="processPO(' . $row['indent_id'] . ')"><span class="glyphicon glyphicon-ok" style="color:green"></span></button>';
            if ($row['indent_no'] > 99 && $row['indent_no'] < 1000) {
                $in_no = "0" . $row['indent_no'];
            } else {
                if ($row['indent_no'] > 9 && $row['indent_no'] < 100) {
                    $in_no = "00" . $row['indent_no'];
                } else {
                    $in_no = "000" . $row['indent_no'];
                }
            }
            $indent_no = $row['ind_prefix'] . '/' . $in_no;
            $output['data'][] = array(
                $x,
                $indent_no,
                $row['indent_date'],
                $row['location_name'],
                $row['supply_date'],
                $row['staff_name'],
                $actionButton
            );
            $x++;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'Get_Indent_Details') {
        $indent_id = $_POST['indent_id'];
        $sql = "SELECT tbl_indent.*,location_name,staff.staff_name,s.staff_name as approver
        FROM tbl_indent INNER JOIN location ON tbl_indent.order_from = location.location_id
        INNER JOIN staff ON tbl_indent.order_by = staff.staff_id
        INNER JOIN staff s ON tbl_indent.appr_by=s.staff_id
        WHERE indent_id =$indent_id";
        $query = $connect->query($sql);
        $result['indent_detail'] = mysqli_fetch_assoc($query);

        $sql1 = "SELECT itm.*,i.item_name,ic.category,u.unit_name,(select rate from tblpo_item where item_id=itm.item_id 
                and rec_id=(select max(rec_id) from tblpo_item where rec_id=(select max(rec_id) from tblpo_item where item_id=itm.item_id)
                 and item_id=itm.item_id)) pre_rate,tpi.rate as cur_rate

                FROM tbl_indent_item itm
                JOIN item i ON i.item_id =itm.item_id
                LEFT JOIN unit u ON u.unit_id =itm.unit_id
                LEFT JOIN tblpo_item tpi ON tpi.indent_id = itm.indent_id AND tpi.item_id = itm.item_id AND tpi.item_category = itm.item_category
                JOIN item_category ic ON ic.category_id = itm.item_category
                WHERE itm.indent_id=$indent_id";
        $query1 = $connect->query($sql1);

        $x = 1;
        while ($row = $query1->fetch_assoc()) {
            $result['item_detail'][] = array(
                $x,
                'rec_id' => $row['rec_id'],
                'indent_id' => $row['indent_id'],
                'item_id' => $row['item_id'],
                'item_name' => $row['item_name'] . ' ~~' . $row['category'],
                'remark' => $row['remark'],
                'AnyOther' => $row['AnyOther'],
                'unit_name' => $row['unit_name'],
                'unit_id' => $row['unit_id'],
                'appr_qnty' => $row['aprvd_qnty'],
                'cur_rate' => $row['cur_rate'],
                'pre_rate' => $row['pre_rate'],
                'category_id' => $row['item_category'],
                'aprvd_status' => $row['aprvd_status']

            );
            $x++;
        }
        $connect->close();
        echo json_encode(array(
            $result
        ));
    } elseif ($action == 'save_po_item') {
        $indent_id = $_POST['indent_id'];
        $po_id = $_POST['po_id'];

        $sql = "SELECT * FROM tblpo WHERE po_id =$po_id";
        $query = $connect->query($sql);
        $row = mysqli_fetch_assoc($query);
        $po_date = $row['po_date'];

        $party = $row['party_id'];
        $sql = "SELECT party_name FROM party WHERE party_id=$party";
        $query = $connect->query($sql);
        $query = mysqli_fetch_assoc($query);
        $p_name = $query['party_name'];
        $particulars = "From " . $p_name;
        $checkedValue = $_POST['checkedValue'];

        $sql = "SELECT * FROM tblpo_item WHERE po_id =$po_id AND indent_id =$indent_id";
        $query = $connect->query($sql);
        $num_rows = mysqli_num_rows($query);

        if ($num_rows > 0) {
            //Update existing po item
            $sql = "DELETE FROM tblpo_item WHERE po_id =$po_id AND indent_id =$indent_id";
            $query = $connect->query($sql);
            foreach ($checkedValue as $lst_arr) {

                $sql = "SELECT Max(rec_id) as maxid FROM logbook";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
                if ($po_id > 99 && $po_id < 1000) {
                    $voucherid = "0" . $po_id;
                } else {
                    if ($po_id > 9 && $po_id < 100) {
                        $voucherid = "00" . $po_id;
                    } else {
                        $voucherid = "000" . $po_id;
                    }
                }
                $item_id = $lst_arr['item_id'];
                $sql = "SELECT * FROM item
                   JOIN unit u ON u.unit_id = item.unit_id
                   WHERE item_id =$item_id";
                $query = $connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $item_name = $row['item_name'];
                $unit_name = $row['unit_name'];

                $category_id = $lst_arr['category_id'];
                $sql4 = "SELECT category FROM item_category WHERE category_id =$category_id";
                $query = $connect->query($sql4);
                $row = mysqli_fetch_assoc($query);
                $category_name = $row['category'];

                $sql = "INSERT INTO logbook(rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,'category_name',unit,item_qnty,item_rate,location,action,user)
                VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['cur_qty'] . "','" . $lst_arr['cur_rate'] . "','" . $_SESSION["stores_lname"] . "','Change PO Item','" . $_SESSION["stores_uname"] . "')";
                $query = $connect->query($sql);


                $sql = "SELECT Max(rec_id) as maxid FROM tblpo_item";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
                $sql = "SELECT Max(seq_no) as maxno FROM tblpo_item WHERE po_id=$po_id";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $seq_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);
                $sql = "INSERT INTO tblpo_item(rec_id,po_id,indent_id,seq_no,item_id,item_category,unit_id,qnty,rate,rate_required,item_description)VALUES('$rec_id','$po_id','$indent_id','$seq_no','" . $lst_arr['item_id'] . "','$category_id','" . $lst_arr['unit_id'] . "','" . $lst_arr['cur_qty'] . "','" . $lst_arr['cur_rate'] . "','1','" . $lst_arr['remark'] . "')";
                $query = $connect->query($sql);
            }
            if ($query == true) {
                $output['success'] = true;
            } else {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        } else {
            //Insert new po item
            foreach ($checkedValue as $lst_arr) {

                $sql = "SELECT Max(rec_id) as maxid FROM logbook";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
                if ($po_id > 99 && $po_id < 1000) {
                    $voucherid = "0" . $po_id;
                } else {
                    if ($po_id > 9 && $po_id < 100) {
                        $voucherid = "00" . $po_id;
                    } else {
                        $voucherid = "000" . $po_id;
                    }
                }
                $item_id = $lst_arr['item_id'];
                $sql = "SELECT * FROM item
                   JOIN unit u ON u.unit_id = item.unit_id
                   WHERE item_id =$item_id";
                $query = $connect->query($sql);
                $row = mysqli_fetch_assoc($query);
                $item_name = $row['item_name'];
                $unit_name = $row['unit_name'];
                $category_id = $lst_arr['category_id'];
                $sql4 = "SELECT category FROM item_category WHERE category_id =$category_id";
                $query = $connect->query($sql4);
                $row = mysqli_fetch_assoc($query);
                $category_name = $row['category'];

                $sql = "INSERT INTO logbook(rec_id,voucher_id,voucher_date,voucher_type,entry_date,particulars,item_name,category_name,unit,item_qnty,item_rate,location,action,user)
                VALUES($rec_id,'$voucherid','$po_date','Pur.Order','" . date("Y-m-d") . "','$particulars','$item_name','$category_name','$unit_name','" . $lst_arr['cur_qty'] . "','" . $lst_arr['cur_rate'] . "','" . $_SESSION["stores_lname"] . "','New PO Item','" . $_SESSION["stores_uname"] . "')";
                $query = $connect->query($sql);

                $sql = "SELECT Max(rec_id) as maxid FROM tblpo_item";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
                $sql = "SELECT Max(seq_no) as maxno FROM tblpo_item WHERE po_id=$po_id";
                $sql = $connect->query($sql);
                $row = mysqli_fetch_assoc($sql);
                $seq_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);
                $sql = "INSERT INTO tblpo_item(rec_id,po_id,indent_id,seq_no,item_id,item_category,unit_id,qnty,rate,rate_required,item_description)VALUES('$rec_id','$po_id','$indent_id','$seq_no','" . $lst_arr['item_id'] . "','$category_id','" . $lst_arr['unit_id'] . "','" . $lst_arr['cur_qty'] . "','" . $lst_arr['cur_rate'] . "','1','" . $lst_arr['remark'] . "')";
                $query = $connect->query($sql);
            }
            if ($query == true) {
                $output['success'] = true;
            } else {
                $output['success'] = false;
            }
            $connect->close();
            echo json_encode($output);
        }
    } elseif ($action == 'Get_Amt') {
        $po_id = $_POST['po_id'];
        $sql = "SELECT total_amount as amt FROM `tblpo_dtm` WHERE po_id=$po_id ORDER by rec_id DESC LIMIT 1";
        $query = $connect->query($sql);
        //  $row =mysqli_fetch_assoc($query);
        if (($query->num_rows) > 0) {
            $amount = mysqli_fetch_assoc($query);
        } else {
            $sql = "SELECT ROUND(SUM((qnty * rate)),3) amt FROM `tblpo_item` WHERE po_id = $po_id";
            $query = $connect->query($sql);
            $amount = mysqli_fetch_assoc($query);
        }
        $connect->close();
        echo json_encode($amount);
    } elseif ($action == 'get_dtm_list') {
        $po_id = $_POST['po_id'];
        $sql = "SELECT * FROM tblpo_dtm WHERE po_id=$po_id";
        $query = $connect->query($sql);
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button"  class="btn" data-toggle="modal" data-target="#removeItemModal"  onclick="removeItem(' . $row['rec_id'] . ')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>';
            if ($row['ssat'] == 'Y') {
                $ssat = 'Yes';
            } else {
                $ssat = 'No';
            }
            if ($row['feed'] == 'A') {
                $feed = 'Auto';
            } else {
                $feed = 'Manual';
            }
            if ($row['calc'] == 'P') {
                $calc = 'Plus';
            } else {
                $calc = 'Minus';
            }
            $output['data'][] = array(
                $x,
                $ssat,
                $feed,
                $calc,
                $row['dtm_id'],
                $row['dtm_percent'] . '%',
                $row['on_amount'],
                $row['dtm_amount'],
                $row['total_amount'],

                $actionButton
            );
            $x++;
        }

        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'save_dtm') {
        $po_id = $_POST['po_id'];
        $ssat = $_POST['ssat'];
        $feeding = $_POST['feeding'];
        $calc = $_POST['calc'];
        $desc = $_POST['desc'];
        $percent = $_POST['percent'];
        $on_amt = $_POST['on_amt'];
        $amt = $_POST['amt'];
        $total_amt = $_POST['total_amt'];
        $sql = "SELECT Max(rec_id) as maxid FROM tblpo_dtm";
        $sql = $connect->query($sql);
        $row = mysqli_fetch_assoc($sql);
        $rec_id = ($row["maxid"] == null ? 1 : $row["maxid"] + 1);
        $sql = "SELECT Max(seq_no) as maxno FROM tblpo_dtm WHERE po_id=$po_id";
        $sql = $connect->query($sql);
        $row = mysqli_fetch_assoc($sql);
        $seq_no = ($row["maxno"] == null ? 1 : $row["maxno"] + 1);
        $sql = "INSERT INTO tblpo_dtm(rec_id,po_id,seq_no,ssat,feed,calc,dtm_id,dtm_percent,on_amount,dtm_amount,total_amount)
        VALUES('$rec_id','$po_id','$seq_no','$ssat','$feeding','$calc','$desc','$percent','$on_amt','$amt','$total_amt')";
        $query = $connect->query($sql);
        if ($query == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'removeDtm') {
        $Id = $_POST['Id'];
        $sql = "DELETE FROM tblpo_dtm WHERE rec_id = $Id";
        $query = $connect->query($sql);
        if ($query == true) {
            $output['success'] = true;
        } else {
            $outpur['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'final_update') {
        $po_id = $_POST['po_id'];
        $indent_id = $_POST['indent_id'];
        $spec = $_POST['spec'];
        // $connect->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        if (!empty($spec)) {
            $sql1 = "INSERT INTO tblpo_spec(po_id,specification)VALUES('$po_id','$spec')";
            $query1 = $connect->query($sql1);
        }
        $sql = "UPDATE tbl_indent_item SET item_ordered='Y' WHERE indent_id =$indent_id";
        $query = $connect->query($sql);

        $sql2 = "UPDATE tblpo SET po_status='S' WHERE po_id =$po_id";
        $query2 = $connect->query($sql2);

        if ($query == true && $query2 == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        // $connect->commit();
        $connect->close();
        echo json_encode($output);
    } elseif ($action == 'delete_dtm') {
        $rec_id = $_POST['rec_id'];
        $sql = "DELETE FROM tblpo_dtm WHERE rec_id=$rec_id";
        $query = $connect->query($sql);
        if ($query == true) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }
        $connect->close();
        echo json_encode($output);
    }
}
