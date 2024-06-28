<?php
include 'db_connect.php';
if (isset($_POST['action']) && !empty($_POST['action'])) {
    if ($_POST['action'] == 'get_indent_list') {
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $location = $_POST['location'];
        $sql = "SELECT tbl_indent.indent_id, indent_no,ind_prefix, tbl_indent.indent_date, appr_status as ind_appr_status,
        tbl_indent.appr_date,tblpo.po_id, tblpo.po_no,tblpo.po_date,tblpo.po_status,tblreceipt1.receipt_no,
        tblreceipt1.receipt_date,tblreceipt1.receipt_prefix, party.party_name, company.company_name,
        l1.location_name as order_location,l2.location_name as rec_at,s1.staff_name as ord_by,
        s2.staff_name as rec_by,s3.staff_name as apprd_by, company.CCode,tbldelivery1.dc_no,dc_date FROM `tbl_indent`
        LEFT JOIN tblpo ON tblpo.indent_id = tbl_indent.indent_id
        LEFT JOIN party ON party.party_id = tblpo.party_id
        LEFT JOIN company ON company.company_id = tblpo.company_id
        LEFT JOIN tbldelivery1 ON tbldelivery1.po_id = tblpo.po_id
        LEFT JOIN tblreceipt1 ON tblreceipt1.po_id= tblpo.po_id
        LEFT JOIN location l1 ON l1.location_id = tbl_indent.order_from
        LEFT JOIN location l2 ON l2.location_id = tblreceipt1.recd_at
        LEFT JOIN staff s1 ON s1.staff_id = tbl_indent.order_by
        LEFT JOIN staff s2 ON s2.staff_id = tblreceipt1.recd_by
        LEFT JOIN staff s3 ON s3.staff_id = tbl_indent.appr_by WHERE tbl_indent.indent_date BETWEEN '$from_date' AND '$to_date' AND tbl_indent.ind_status='S' ";
        if (!empty($location)) {
            $sql .= " AND tbl_indent.order_from = '$location' ";
        }
        $sql .= " GROUP BY tbl_indent.indent_id ORDER BY tbl_indent.indent_id DESC";
        $query = $connect->query($sql);
        $output = array(
            'data' => array()
        );
        $x = 1;

        while ($row = $query->fetch_assoc()) {

            $output['data'][] = array(
                $x,
                $row['ind_prefix'] . '/' . str_pad($row['indent_no'], 4, '0', STR_PAD_LEFT),
                date('d-m-Y', strtotime($row['indent_date'])),
                $row['order_location'],
                $row['ord_by'],
                $row['ind_appr_status'] == 'S' ? 'Approved' : 'Pending',
                $row['ind_appr_status'] =='S'? date('d-m-Y', strtotime($row['appr_date'])) : '',
                $row['apprd_by'],
                $row['po_id']>0 ? $row['CCode'].'/'.$row['ind_prefix'].'/'.str_pad($row['po_no'], 4, '0', STR_PAD_LEFT) : '',
                $row['po_id'] > 0? date('d-m-Y', strtotime($row['po_date'])) : '',
                $row['po_status'] == 'S' ? 'Generated' : 'Pending',

                empty($row['dc_no']) ? ' ' : $row['ind_prefix'] . '/' . str_pad($row['dc_no'], 4, '0', STR_PAD_LEFT),
                empty($row['dc_date']) ? ' ' : date("d-m-y", strtotime($row['dc_date'])),

                empty($row['receipt_no']) ? ' ' : $row['receipt_prefix'] . '/' . str_pad($row['receipt_no'], 4, '0', STR_PAD_LEFT),
                empty($row['receipt_date']) ? ' ' : date("d-m-y", strtotime($row['receipt_date'])),

                $row['rec_by'],
                $row['rec_at'],
                $row['company_name'],
                $row['party_name'],
        );
            $x++;
        }
        $connect->close();
        echo json_encode($output);

    }
}
