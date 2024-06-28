<?php
session_start();
require_once('config/config.php');
include("function.php");
if (check_user() == false) {
    header("Location: login.php");
}
/*-------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Purchase Order</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/fancydropdown.css" type="text/css">
    <script type="text/javascript" src="js/fancydropdown.js"></script>
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <style>
        ul {

            padding-left: 0px;
        }

        ul li {
            list-style: none;
            float: left;


        }

        ul li a {

            text-decoration: none;
            color: green;
            display: block;
            font-size: 16px;
            padding: 5px;
            border-bottom: 1px solid silver;
        }

        ul li ul {
            display: none;
            position: absolute;
        }

        ul ul li {
            float: none;
        }

        ul ul a {
            text-align: eft;
        }

        ul li:hover ul {
            display: block;
        }

        ul li a:hover {
            background-color: red;
            color: black;
        }

        #menu {

            margin: -1px auto;

        }

        #mainbody1 {
            margin: 0 auto;
            width: 1000px;

            height: 320px;
        }

        a {
            text-align: justify;
            text-decoration: none;
            padding-left: 5;
            font-size: 20px;
        }
    </style>
</head>

<body background="images/hbox21.jpg">

    <!-- <body style="background-color:#607D8B"> -->
    <table style="margin-top:70px; margin-bottom:50px;" border="0" align="center">
        <tr>
            <td style="vertical-align:bottom;">
                <div style="position:absolute;top:5px;left:15px;font-size:40px;font-weight:bold;font-family:Georgia;">
                    <!--<img src="images/logo.png" border="0" />-->
                    <i>PO with Stores</i>
                </div>
                <div style="position:absolute;top:45px;left:600px;font-family:Times New Roman;">
                    <b>
                        User&nbsp;:&nbsp;<font color="#FFF"><?php echo $_SESSION["stores_uname"]; ?></font>&nbsp;&nbsp;
                        Location&nbsp;:&nbsp;<font color="#FFF"><?php echo $_SESSION["stores_lname"]; ?></font>
                        &nbsp;&nbsp;
                        Period&nbsp;:&nbsp;<font color="#FFF"><?php echo $_SESSION["stores_syr"]; ?></b> to
                    <b><?php echo $_SESSION["stores_eyr"]; ?></font>&nbsp;&nbsp;&nbsp;
                        <a href="home.php" style="font-size:16px;font-family:Times New Roman;"><img src="images/home.gif" border="0" title="Home" style="vertical-align:bottom;">&nbsp;Home</a>&nbsp;&nbsp;
                        <a href="logout.php" style="font-size:16px;font-family:Times New Roman;"><img src="images/logoutuser.gif" border="0" title="Logout" style="vertical-align:bottom;">&nbsp;Logout</a>
                    </b>
                </div>
                <?php
                $query = "select * from users where uid=" . $_SESSION['stores_uid'];
                $data = mysql_query($query);
                $rec = mysql_fetch_array($data); ?>

                <div id="menu">

                    <ul class="tabs">
                        <?php if ($_SESSION["stores_utype"] == "S") { ?>
                            <li class="hasmore"><a href="#">Masters</a>
                                <ul class="dropdown">

                                    <!--<li><a href="openadd2comfile.php?action=new">Company Master</a></li>-->
                                    <li><a href="company_master.php">Company Master</a></li>
                                    <!-- <li><a href="location.php">Location Master</a></li>-->
                                    <li><a href="location_new.php">Location Master</a></li>

                                    <!--<li><a href="cit2y.php">City Master</a></li>-->
                                    <li><a href="city_master.php">City Master</a></li>
                                    <!--<li><a href="party.php?action=new">Party Master</a></li>-->
                                    <li><a href="party_master.php">Party Master</a></li>
                                    <!--<li><a href="desig.php?action=new">Designation</a></li>-->
                                    <li><a href="designation_master.php">Designation</a></li>
                                    <!-- <li><a href="staff.php">Staff Master</a></li>-->

                                    <li><a href="staff_master.php">Staff Master</a></li>
                                    <!-- <li><a href="itemg.php?action=new">Item Group</a></li>-->
                                    <li><a href="itemgroup_master.php">Item Group</a></li>
                                    <!-- <li><a href="item.php?action=new">Item Master</a></li>-->
                                    <li><a href="item_master.php">Item Master</a></li>
                                    <li><a href="plot.php">Plot Master</a></li>
                                    <!--<li><a href="opstock.php">Opening Stock</a></li>-->
                                    <li><a href="stock_master.php">Opening Stock</a></li>

                                    <li><br /></li>
                                    <li><a href="user.php?action=new">New User</a></li>
                                    <li><a href="user.php?action=change">Change Profile</a></li>
                                    <li><a href="lstuser.php">User List</a></li>
                                    <li><a href="useraccess.php">User Access</a></li>


                                </ul>
                            </li>
                        <?php } ?>
                        <li>&nbsp;&nbsp;</li>

                        <?php if ($_SESSION["stores_utype"] == "U" or $_SESSION["stores_utype"] == "A") { ?>
                            <li class="hasmore">
                                <a href="#">Master</a>

                                <ul class="dropdown">
                                    <li><a href="item_master.php">Item Master</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if ($rec['oi1'] == 1 or $rec['oi2'] == 1 or $rec['oi3'] == 1 or $rec['oi4'] == 1 or $rec['ia1'] == 1 or $rec['ia2'] == 1 or $rec['ia3'] == 1 or $rec['ia4'] == 1 or $rec['dc1'] == 1 or $rec['dc2'] == 1 or $rec['dc3'] == 1 or $rec['dc4'] == 1 or $rec['po1'] == 1 or $rec['po2'] == 1 or $rec['po3'] == 1 or $rec['po4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Order</a>
                                <ul class="dropdown">

                                    <?php if ($rec['oi1'] == 1 or $rec['oi2'] == 1 or $rec['oi3'] == 1 or $rec['oi4'] == 1) { ?>
                                        <li><a href="order_indent.php">Order Indent</a></li>
                                    <?php }
                                    if ($rec['ia1'] == 1 or $rec['ia2'] == 1 or $rec['ia3'] == 1 or $rec['ia4'] == 1) { ?>
                                        <li><a href="indent_approval_master.php">Indent Approval</a></li>
                                    <?php }
                                    if ($rec['po1'] == 1 or $rec['po2'] == 1 or $rec['po3'] == 1 or $rec['po4'] == 1) { ?>
                                        <li><a href="purchase_order.php">Purchase Order</a></li>
                                    <?php }
                                    if ($rec['dc1'] == 1 or $rec['dc2'] == 1 or $rec['dc3'] == 1 or $rec['dc4'] == 1) { ?>
                                        <li><a href="delivery_confirm.php">Delivery Confirmation</a></li> <?php } ?>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($rec['mr1'] == 1 or $rec['mr2'] == 1 or $rec['mr3'] == 1 or $rec['mr4'] == 1 or $rec['rr1'] == 1 or $rec['rr2'] == 1 or $rec['rr3'] == 1 or $rec['rr4'] == 1 or $rec['cp1'] == 1 or $rec['cp2'] == 1 or $rec['cp3'] == 1 or $rec['cp4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Supply</a>
                                <ul class="dropdown">
                                    <?php if ($rec['mr1'] == 1 or $rec['mr2'] == 1 or $rec['mr3'] == 1 or $rec['mr4'] == 1) { ?>
                                        <li><a href="material_receipt.php">Material Receipt</a></li>
                                    <?php }
                                    if ($rec['rr1'] == 1 or $rec['rr2'] == 1 or $rec['rr3'] == 1 or $rec['rr4'] == 1) { ?>
                                        <li><a href="return_receipt.php">Receipt Return</a></li>
                                    <?php }
                                    if ($rec['cp1'] == 1 or $rec['cp2'] == 1 or $rec['cp3'] == 1 or $rec['cp4'] == 1) { ?>
                                        <li><a href="cash_purchase.php">Cash Purchase</a></li><?php } ?>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($rec['pb1'] == 1 or $rec['pb2'] == 1 or $rec['pb3'] == 1 or $rec['pb4'] == 1 or $rec['br1'] == 1 or $rec['br2'] == 1 or $rec['br3'] == 1 or $rec['br4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Billing</a>
                                <ul class="dropdown">
                                    <?php if ($rec['pb1'] == 1 or $rec['pb2'] == 1 or $rec['pb3'] == 1 or $rec['pb4'] == 1) { ?>
                                        <li><a href="purchase_bill.php">Purchase Bill</a></li>
                                    <?php }
                                    if ($rec['br1'] == 1 or $rec['br2'] == 1 or $rec['br3'] == 1 or $rec['br4'] == 1) { ?>
                                        <li><a href="pbselection.php?action=new">Pur.Bill Return</a></li><?php } ?>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($rec['pay1'] == 1 or $rec['pay2'] == 1 or $rec['pay3'] == 1 or $rec['pay4'] == 1) { ?>
                            <li><a href="payment.php?action=new">Payment</a></li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($rec['mi1'] == 1 or $rec['mi2'] == 1 or $rec['mi3'] == 1 or $rec['mi4'] == 1 or $rec['ir1'] == 1 or $rec['ir2'] == 1 or $rec['ir3'] == 1 or $rec['ir4'] == 1 or $rec['ilt1'] == 1 or $rec['ilt2'] == 1 or $rec['ilt3'] == 1 or $rec['ilt4'] == 1 or $rec['ipt1'] == 1 or $rec['ipt2'] == 1 or $rec['ipt3'] == 1 or $rec['ipt4'] == 1 or $rec['xlt1'] == 1 or $rec['xlt2'] == 1 or $rec['xlt3'] == 1 or $rec['xlt4'] == 1 or $rec['ps1'] == 1 or $rec['ps2'] == 1 or $rec['ps3'] == 1 or $rec['ps4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Consumption</a>
                                <ul class="dropdown">
                                    <?php if ($rec['mi1'] == 1 or $rec['mi2'] == 1 or $rec['mi3'] == 1 or $rec['mi4'] == 1) { ?>
                                        <li><a href="material_issue.php">Material Issue</a></li>
                                    <?php }
                                    if ($rec['ir1'] == 1 or $rec['ir2'] == 1 or $rec['ir3'] == 1 or $rec['ir4'] == 1) { ?>
                                        <li><a href="issue_return.php">Issue Return</a></li>
                                    <?php }
                                    if ($rec['ilt1'] == 1 or $rec['ilt2'] == 1 or $rec['ilt3'] == 1 or $rec['ilt4'] == 1) { ?>
                                        <li><a href="ilt_dispatch.php">ILT Despatch</a></li>
                                        <li><a href="ilt_receive.php">ILT Receive</a></li>
                                    <?php }
                                    if ($rec['ipt1'] == 1 or $rec['ipt2'] == 1 or $rec['ipt3'] == 1 or $rec['ipt4'] == 1) { ?>
                                        <li><a href="ipt_master.php">Inter Plot Transfer</a></li>
                                    <?php }
                                    if ($rec['xlt1'] == 1 or $rec['xlt2'] == 1 or $rec['xlt3'] == 1 or $rec['xlt4'] == 1) { ?>
                                        <li><a href="xlt_dispatch.php">XLT Despatch</a></li>
                                        <li><a href="xlt_receive.php">XLT Receive</a></li>
                                    <?php }
                                    if ($rec['ps1'] == 1 or $rec['ps2'] == 1 or $rec['ps3'] == 1 or $rec['ps4'] == 1) { ?>
                                        <li><a href="physical_stock.php">Physical Stock</a></li><?php } ?>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <li class="hasmore"><a href="#">Report (<font color="#F2F200">Pending</font>)</a>
                            <ul class="dropdown">
                                <?php if ($rec['oi1'] == 1 or $rec['oi2'] == 1 or $rec['oi3'] == 1 or $rec['oi4'] == 1) { ?>
                                    <li><a href="penindlist.php">Pending Indents</a></li>
                                <?php }
                                if ($rec['po1'] == 1 or $rec['po2'] == 1 or $rec['po3'] == 1 or $rec['po4'] == 1) { ?>
                                    <li><a href="penpolist.php">Pending Purchase Orders</a></li>
                                <?php }
                                if ($rec['dc1'] == 1 or $rec['dc2'] == 1 or $rec['dc3'] == 1 or $rec['dc4'] == 1) { ?>
                                    <li><a href="penmrlist.php">Pending Delivery</a></li>
                                <?php }
                                if ($rec['pb1'] == 1 or $rec['pb2'] == 1 or $rec['pb3'] == 1 or $rec['pb4'] == 1) { ?>
                                    <li><a href="penpblist.php">Pending Bills</a></li>
                                <?php }
                                if ($rec['pay1'] == 1 or $rec['pay2'] == 1 or $rec['pay3'] == 1 or $rec['pay4'] == 1) { ?>
                                    <li><a href="penpaylist.php">Pending Payments</a></li><?php } ?>
                            </ul>
                        </li>
                        <li>&nbsp;&nbsp;</li>

                        <?php if ($rec['mi1'] == 1 or $rec['mi2'] == 1 or $rec['mi3'] == 1 or $rec['mi4'] == 1 or $rec['ir1'] == 1 or $rec['ir2'] == 1 or $rec['ir3'] == 1 or $rec['ir4'] == 1 or $rec['ilt1'] == 1 or $rec['ilt2'] == 1 or $rec['ilt3'] == 1 or $rec['ilt4'] == 1 or $rec['ipt1'] == 1 or $rec['ipt2'] == 1 or $rec['ipt3'] == 1 or $rec['ipt4'] == 1 or $rec['xlt1'] == 1 or $rec['xlt2'] == 1 or $rec['xlt3'] == 1 or $rec['xlt4'] == 1 or $rec['ps1'] == 1 or $rec['ps2'] == 1 or $rec['ps3'] == 1 or $rec['ps4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Report (<font color="#F2F200">Stocks</font>)</a>
                                <ul class="dropdown">
                                    <li><a href="itemstock.php">Location v/s Item Stock</a></li>
                                    <li><a href="itemstock1.php">Item v/s Location Stock</a></li>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>


                        <?php if ($rec['oi1'] == 1 or $rec['oi2'] == 1 or $rec['oi3'] == 1 or $rec['oi4'] == 1 or $rec['ia1'] == 1 or $rec['ia2'] == 1 or $rec['ia3'] == 1 or $rec['ia4'] == 1 or $rec['dc1'] == 1 or $rec['dc2'] == 1 or $rec['dc3'] == 1 or $rec['dc4'] == 1 or $rec['po1'] == 1 or $rec['po2'] == 1 or $rec['po3'] == 1 or $rec['po4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Report (<font color="#F2F200">Orders</font>)</a>
                                <ul class="dropdown">
                                    <?php if ($rec['oi1'] == 1 or $rec['oi2'] == 1 or $rec['oi3'] == 1 or $rec['oi4'] == 1 or $rec['ia1'] == 1 or $rec['ia2'] == 1 or $rec['ia3'] == 1 or $rec['ia4'] == 1) { ?>
                                        <li><a href="indlist.php">Indent List</a></li>
                                    <?php }
                                    if ($rec['po1'] == 1 or $rec['po2'] == 1 or $rec['po3'] == 1 or $rec['po4'] == 1) { ?>
                                        <li><a href="ind2po.php">Indent v/s Purchase Order Report</a></li>
                                        <li><a href="ind2cp.php">Indent v/s Cash Purchase Report</a></li>
                                        <li><a href="indmap.php">Indent Mapping Report</a></li>
                                        <li><a href="polist.php">Purchase Order List</a></li>
                                        <li><a href="polistparty.php">Party wise Order List</a></li>
                                        <li><a href="polistitem.php">Item wise Order List</a></li>
                                        <?php if ($_SESSION["stores_utype"] == "U" or $_SESSION["stores_utype"] == "A") { ?>
                                            <li><a href="indent_report.php">Indent Report</a></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>


                        <?php if ($rec['mi1'] == 1 or $rec['mi2'] == 1 or $rec['mi3'] == 1 or $rec['mi4'] == 1 or $rec['ir1'] == 1 or $rec['ir2'] == 1 or $rec['ir3'] == 1 or $rec['ir4'] == 1 or $rec['ilt1'] == 1 or $rec['ilt2'] == 1 or $rec['ilt3'] == 1 or $rec['ilt4'] == 1 or $rec['ipt1'] == 1 or $rec['ipt2'] == 1 or $rec['ipt3'] == 1 or $rec['ipt4'] == 1 or $rec['xlt1'] == 1 or $rec['xlt2'] == 1 or $rec['xlt3'] == 1 or $rec['xlt4'] == 1 or $rec['ps1'] == 1 or $rec['ps2'] == 1 or $rec['ps3'] == 1 or $rec['ps4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Report (<font color="#F2F200">Materials</font>)</a>
                                <ul class="dropdown">
                                    <li><a href="mrlist.php">Material Receipt List</a></li>
                                    <li><a href="mrlistparty.php">Partywise Material Receipt</a></li>
                                    <li><a href="mrlistitem.php">Itemwise Material Receipt</a></li>
                                    <li><a href="rrlist.php">Receipt Return List</a></li>
                                    <li><a href="rrlistparty.php">Partywise Receipt Return</a></li>
                                    <li><a href="rrlistitem.php">Itemwise Receipt Return</a></li>
                                    <li><a href="cplist.php">Cash Purchase List</a></li>
                                    <li><a href="iltlist.php">ILT Despatch v/s Receipt List</a></li>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($rec['pb1'] == 1 or $rec['pb2'] == 1 or $rec['pb3'] == 1 or $rec['pb4'] == 1 or $rec['br1'] == 1 or $rec['br2'] == 1 or $rec['br3'] == 1 or $rec['br4'] == 1) { ?>
                            <li class="hasmore"><a href="#">Report (<font color="#F2F200">Billing</font>)</a>
                                <ul class="dropdown">
                                    <li><a href="pblist.php">Purchase Bill List</a></li>
                                    <li><a href="pblistparty.php">Partywise Purchase Bill</a></li>
                                    <li><a href="pblistitem.php">Itemwise Purchase Bill</a></li>
                                    <li><a href="prlist.php">Bill Return List</a></li>
                                    <li><a href="prlistparty.php">Partywise Bill Return</a></li>
                                    <li><a href="paylist.php">Payment List</a></li>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>

                        <?php if ($_SESSION["stores_utype"] == "A" || $_SESSION["stores_utype"] == "S") { ?>
                            <li class="hasmore"><a href="#">Report (<font color="#F2F200">Others</font>)</a>
                                <ul class="dropdown">
                                    <li><a href="daybook.php">DayBook</a></li>
                                    <li><a href="logbook.php">LogBook</a></li>
                                    <li><a href="itemlist.php">Item Master List</a></li>
                                    <li><a href="partylist.php">Party Master List</a></li>
                                </ul>
                            </li>
                            <li>&nbsp;&nbsp;</li>
                        <?php } ?>
                    </ul>

                </div>
            </td>
        </tr>
    </table>

</body>

</html>