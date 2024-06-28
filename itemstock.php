<?php
include("menu.php");
/*--------------------*/
$lid = $_SESSION['stores_locid'];
if (isset($_REQUEST['lid'])) {
    $lid = $_REQUEST['lid'];
}
if (isset($_REQUEST['gid'])) {
    $gid = $_REQUEST['gid'];
}
if (isset($_REQUEST['iid'])) {
    $iid = $_REQUEST['iid'];
}
if (isset($_REQUEST['flt'])) {
    $flt = $_REQUEST['flt'];
}

if (isset($_REQUEST['location_group_id']) && $_REQUEST['location_group_id'] != 0) {
    $query = mysql_query("SELECT GROUP_CONCAT(location_map.location_id) AS location_id FROM location_group LEFT join location_map on location_map.location_group_id = location_group.id WHERE location_group.id = '" . $_REQUEST['location_group_id'] . "'") or die(mysql_error());
    $result = mysql_fetch_assoc($query);
    $lid = $result['location_id'];
    $lgid = $_REQUEST['location_group_id'];
}
/*--------------------*/
if (isset($_POST['show'])) {
    $sm = strtotime($_POST['dateFrom']);
    $em = strtotime($_POST['dateTo']);

    if (isset($_REQUEST['location_group_id']) && $_REQUEST['location_group_id'] != 0) {
        $query = mysql_query("SELECT GROUP_CONCAT(location_map.location_id) AS location_id FROM location_group LEFT join location_map on location_map.location_group_id = location_group.id WHERE location_group.id = '" . $_REQUEST['location_group_id'] . "'") or die(mysql_error());
        $result = mysql_fetch_assoc($query);
        $lid = $result['location_id'];
        $lgid = $_REQUEST['location_group_id'];
    } else {
        $lid = $_POST['location'];
    }


    $gid = $_POST['itemGroup'];
    $iid = $_POST['itemName'];
    $flt = $_POST['filterData'];
} elseif (isset($_REQUEST['sm'])) {
    $sm = $_REQUEST['sm'];
    $em = $_REQUEST['em'];
} else {
    $sm = strtotime(date("Y-m-d"));
    $em = strtotime(date("Y-m-d"));
}
/*--------------------*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Purchase Order</title>
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <script type="text/javascript" src="js/calendar_eu.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script language="javascript" type="text/javascript">
        function validate_dateselection() {
            if (checkdate(document.itemstock.dateFrom)) {
                if (checkdate(document.itemstock.dateTo)) {
                    var no_of_days1 = getDaysbetween2Dates(document.itemstock.dateFrom, document.itemstock.dateTo);
                    if (no_of_days1 < 0) {
                        alert("* Sorry! date range wrongly selected. Please correct and submit again.\n");
                        return false;
                    } else {
                        var no_of_days2 = getDaysbetween2Dates(document.itemstock.startYear, document.itemstock.dateFrom);
                        if (no_of_days2 < 0) {
                            alert("* Report From date wrongly selected. Please correct and submit again.\n");
                            return false;
                        } else {
                            var no_of_days3 = getDaysbetween2Dates(document.itemstock.dateTo, document.itemstock.endYear);
                            if (no_of_days3 < 0) {
                                alert("* Report To date wrongly selected. Please correct and submit again.\n");
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        function searching_data() {
            var err = "";
            if (document.getElementById("searchText").value == "") {
                err = "Please input the text, that is to be searched!\n";
            }
            if (document.getElementById("searchFrom").value == 0) {
                err += "Please select the field, where text to be searched!\n";
            }
            if (err == "") {
                window.location = "itemstock.php?xon=search&txt=" + document.getElementById("searchText").value + "&on=" +
                    document.getElementById("searchFrom").value + "&lid=" + document.getElementById("location").value +
                    "&gid=" + document.getElementById("itemGroup").value + "&iid=" + document.getElementById("itemName")
                    .value + "&flt=" + document.getElementById("filterData").value + "&pg=" + document.getElementById(
                        "page").value + "&tr=" + document.getElementById("displayTotalRows").value + "&sm=" + document
                    .getElementById("date1").value + "&em=" + document.getElementById("date2").value;
            } else {
                alert("Error:\n" + err);
            }
        }
    </script>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <center>
                <form name="itemstock" id="itemstock" method="post" action="itemstock.php" onsubmit="return validate_dateselection()">
                    <table align="center" border="0" cellpadding="2" cellspacing="1" width="1150px">
                        <tbody>
                            <tr>
                                <td width="30%"></td>
                                <td width="15%">&nbsp;</td>
                                <td width="25%">&nbsp;</td>
                                <td width="30%" align="right"><input name="searchText" id="searchText" value="" size="20" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; height:10px; vertical-align:middle;" />&nbsp;on&nbsp;<select name="searchFrom" id="searchFrom" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; width:100px; height:17px; vertical-align:middle;">
                                        <option selected value="0">&nbsp;</option>
                                        <option value="1">Group Name</option>
                                        <option value="2">Item Name</option>
                                    </select><input type="button" name="search" id="search" value="search" style=" font-size:9px; widows:20px; height:20px; vertical-align: text-top" onclick="searching_data()" /></td>
                            </tr>
                            <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: xx-large; font-weight: normal ; color: #CC9933">
                                <td colspan="4">Item Stock List</td>
                            </tr>
                            <tr align="center">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:20px;">
                                <td>&nbsp;</td><?php
                                                if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {
                                                    echo '<td align="right">Select Location :</td>';
                                                    echo '<td align="left"><select name="location" id="location" style="width:200px" >';
                                                    $sql_location = mysql_query("SELECT * FROM location ORDER BY location_name") or die(mysql_error());
                                                    while ($row_location = mysql_fetch_array($sql_location)) {
                                                        if ($row_location["location_id"] == $lid)
                                                            echo '<option selected value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                                        else
                                                            echo '<option value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                                    }
                                                    echo '</select></td>';
                                                } elseif ($_SESSION['stores_utype'] == "U") {
                                                    echo '<td align="right">Select Location :</td>';
                                                    echo '<td align="left"><select name="location" id="location" style="width:200px" >';

                                                    $sql_location = mysql_query("SELECT lm.location_id, lm.location_name FROM location lm JOIN users um ON FIND_IN_SET(lm.location_id, um.location_id) WHERE um.uid =" . $_SESSION['stores_uid']);

                                                    while ($row_location = mysql_fetch_array($sql_location)) {
                                                        if ($row_location["location_id"] == $lid)
                                                            echo '<option selected value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                                        else
                                                            echo '<option value="' . $row_location["location_id"] . '">' . $row_location["location_name"] . '</option>';
                                                    }

                                                    echo '</select></td>';
                                                } ?>
                                <td align="right">Filter:&nbsp;<select name="filterData" id="filterData" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:9px; width:175px; height:17px; vertical-align:middle;">
                                        <?php
                                        if (isset($_REQUEST['flt'])) {
                                            if ($flt == 0) {
                                                echo '<option selected value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
                                            } elseif ($flt == 1) {
                                                echo '<option value="0">&nbsp;</option><option selected value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
                                            } elseif ($flt == 2) {
                                                echo '<option value="0">&nbsp;</option><option value="1">Only +ve stock</option><option selected value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
                                            } elseif ($flt == 3) {
                                                echo '<option value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option selected value="3">Opening but no transactions</option>';
                                            }
                                        } else {
                                            echo '<option selected value="0">&nbsp;</option><option value="1">Only +ve stock</option><option value="2">Only -ve stock</option><option value="3">Opening but no transactions</option>';
                                        } ?>
                                    </select></td>
                            </tr>

                            <?php
                            if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S" || $_SESSION['appr_auth'] == '1') { ?>
                                <tr>
                                    <td></td>
                                    <td align="right">Location Group:</td>
                                    <td>
                                        <select name="location_group_id" id="location_group_id" style="width: 200px;">
                                            <option value="0">Select</option>
                                            <?php
                                            if ($_SESSION['stores_utype'] == "A" || $_SESSION['stores_utype'] == "S") {
                                                $sql = mysql_query("SELECT id,location_group_name FROM location_group ORDER BY location_group_name") or die(mysql_error());
                                            } else {
                                                $sql = mysql_query("SELECT location_group.id,location_group_name FROM location_group  LEFT join users on users.location_group_id = location_group.id where users.location_group_id = '" . $_SESSION['location_group_id'] . "' AND users.user_id = '" . $_SESSION['stores_uname'] . "'") or die(mysql_error());
                                            }
                                            while ($row_loc_group = mysql_fetch_array($sql)) {
                                                if ($row_loc_group["id"] == $lgid) {
                                                    echo '<option selected value="' . $row_loc_group['id'] . '">' . $row_loc_group['location_group_name'] . '</option>';
                                                } else {
                                                    echo '<option value="' . $row_loc_group['id'] . '">' . $row_loc_group['location_group_name'] . '</option>';
                                                }
                                            }
                                            ?>

                                        </select>
                                    </td>
                                </tr>
                            <?php }
                            ?>
                            <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:20px;">
                                <td>&nbsp;</td><?php
                                                echo '<td align="right">Select Group:</td>';
                                                echo '<td align="left"><select name="itemGroup" id="itemGroup" style="width:200px" onchange="get_items_on_itemgroup(this.value)" >';
                                                echo '<option selected value="0">All Groups</option>';
                                                $sql_group = mysql_query("SELECT * FROM itemgroup ORDER BY itgroup_name") or die(mysql_error());
                                                while ($row_group = mysql_fetch_array($sql_group)) {
                                                    if ($row_group["itgroup_id"] == $gid)
                                                        echo '<option selected value="' . $row_group["itgroup_id"] . '">' . $row_group["itgroup_name"] . '</option>';
                                                    else
                                                        echo '<option value="' . $row_group["itgroup_id"] . '">' . $row_group["itgroup_name"] . '</option>';
                                                }
                                                echo '</select></td>'; ?>
                                <td>&nbsp;</td>
                            </tr>
                            <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:20px;">
                                <td>&nbsp;</td><?php
                                                echo '<td align="right">Select Item:</td>';
                                                echo '<td align="left" id="tdItemName"><select name="itemName" id="itemName" style="width:200px" >';
                                                echo '<option selected value="0">All Items</option>';
                                                if (isset($_REQUEST['gid']) && $_REQUEST['gid'] > 0)
                                                    $sql_items = mysql_query("SELECT * FROM item WHERE itgroup_id=" . $gid . " ORDER BY item_name") or die(mysql_error());
                                                else
                                                    $sql_items = mysql_query("SELECT * FROM item ORDER BY item_name") or die(mysql_error());
                                                while ($row_items = mysql_fetch_array($sql_items)) {
                                                    if ($row_items["item_id"] == $iid)
                                                        echo '<option selected value="' . $row_items["item_id"] . '">' . $row_items["item_name"] . '</option>';
                                                    else
                                                        echo '<option value="' . $row_items["item_id"] . '">' . $row_items["item_name"] . '</option>';
                                                }

                                                echo '</select></td>'; ?>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal ; color: #000000; height:25px;">
                                <td>&nbsp;<input type="hidden" name="startYear" id="startYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_syr'])); ?>" /><input type="hidden" name="endYear" id="endYear" value="<?php echo date("d-m-Y", strtotime($_SESSION['stores_eyr'])); ?>" /></td>
                                <td align="center" colspan="2"><span style="vertical-align:top;">From:</span>&nbsp;&nbsp;<input name="dateFrom" id="dateFrom" maxlength="10" size="10" value="<?php echo date("d-m-Y", $sm); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                                        new tcal({
                                            "formname": "itemstock",
                                            "controlname": "dateFrom"
                                        });
                                    </script>&nbsp;&nbsp;<span style="vertical-align:top;">To:</span>&nbsp;&nbsp;<input name="dateTo" id="dateTo" maxlength="10" size="10" value="<?php echo date("d-m-Y", $em); ?>" style="vertical-align:top;">&nbsp;<script language="JavaScript">
                                        new tcal({
                                            "formname": "itemstock",
                                            "controlname": "dateTo"
                                        });
                                    </script>
                                </td>
                                <td><input type="image" name="show" src="images/show.gif" width="72" height="22" alt="show"><input type="hidden" name="show" value="show" /></td>
                            </tr>


                        </tbody>
                    </table>
                </form>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table align="center" id="mytable" class="table table-bordered">
                    <thead>
                        <tr bgcolor="#E6E1B0" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #006600; height:25px;">
                            <td width="4%">
                                Sl.No.
                            </td>
                            <td width="20%">
                                Location
                                Name
                            </td>
                            <td width="20%">
                                Group
                                Name
                            </td>
                            <td width="20%">
                                Item
                                Name
                            </td>
                            <td width="20%">
                                Category
                                Name/Packing Size/Type
                            </td>
                            <td width="14%">
                                Op.Stock
                            </td>
                            <td width="14%">
                                Incoming
                            </td>
                            <td width="14%">
                                Outgoing
                            </td>
                            <td width="14%">
                                Cl.Stock
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (isset($_POST['show']) || isset($_REQUEST['pg'])) {

                            $ctr = 0;
                            if ($iid > 0) {                                                    //if(item == single item)

                                $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name,location_name, item.item_id, item_name,item_category.category,item_category.category_id, unit_name, 
                        Sum(item_qnty) AS qty FROM  stock_register 
                        LEFT JOIN location ON location.location_id = stock_register.location_id 
                        LEFT JOIN item ON stock_register.item_id = item.item_id 
                        LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                        LEFT JOIN unit ON item.unit_id = unit.unit_id 
                        LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                        WHERE stock_register.location_id in ($lid) AND 
                        item.item_id=" . $iid . " AND 
                        entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category  ") or die(mysql_error());

                        echo "SELECT itemgroup.itgroup_id, itgroup_name,location_name, item.item_id, item_name,item_category.category,item_category.category_id, unit_name, 
                        Sum(item_qnty) AS qty FROM  stock_register 
                        LEFT JOIN location ON location.location_id = stock_register.location_id 
                        LEFT JOIN item ON stock_register.item_id = item.item_id 
                        LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                        LEFT JOIN unit ON item.unit_id = unit.unit_id 
                        LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                        WHERE stock_register.location_id in ($lid) AND 
                        item.item_id=" . $iid . " AND 
                        entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category  ";
                            } elseif ($iid == 0 && $gid > 0) {                                //if(item == all items AND group == single group)
                                if ($flt == 0) {                                            //if(filter == no filetr)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name,item_category.category,item_category.category_id, unit_name,
                            Sum(item_qnty) AS qty FROM  stock_register 
                            LEFT JOIN location ON location.location_id = stock_register.location_id 
                            LEFT JOIN item ON stock_register.item_id = item.item_id 
                            LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                            LEFT JOIN unit ON item.unit_id = unit.unit_id 
                            LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                            WHERE 
                            stock_register.location_id in ($lid) AND
                            itemgroup.itgroup_id=" . $gid . " AND 
                            entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category  ") or die(mysql_error());
                                } elseif ($flt == 1) {                                        //if(filter == only +ve stock)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name,item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM  
                            stock_register 
                            LEFT JOIN location ON location.location_id = stock_register.location_id 
                            LEFT JOIN item ON stock_register.item_id = item.item_id 
                            LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                            LEFT JOIN unit ON item.unit_id = unit.unit_id
                            LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                            WHERE 
                            stock_register.location_id in ($lid) AND itemgroup.itgroup_id=" . $gid . "
                             AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category HAVING qty>=0  ") or die(mysql_error());
                                } elseif ($flt == 2) {                                        //if(filter == only -ve stock)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name,item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM 
                             stock_register 
                             LEFT JOIN location ON location.location_id = stock_register.location_id 
                             LEFT JOIN item ON stock_register.item_id = item.item_id 
                             LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                             LEFT JOIN unit ON item.unit_id = unit.unit_id 
                             LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                             WHERE stock_register.location_id in ($lid) AND itemgroup.itgroup_id=" . $gid . " AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category HAVING qty<0  ") or die(mysql_error());
                                } elseif ($flt == 3) {                                        //if(filter == only opening and no transactions during the period)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name, item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM
                              stock_register LEFT JOIN location ON location.location_id = stock_register.location_id
                               LEFT JOIN item ON stock_register.item_id = item.item_id 
                               LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id 
                               LEFT JOIN unit ON item.unit_id = unit.unit_id 
                               LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                               WHERE stock_register.location_id in ($lid) AND itemgroup.itgroup_id=" . $gid . " AND (entry_date<'" . date("Y-m-d", $sm) . "' OR entry_mode ='O+' ) GROUP BY stock_register.item_category HAVING qty>0 AND item.item_id NOT IN (SELECT item.item_id FROM  stock_register LEFT JOIN item ON stock_register.item_id = item.item_id LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id WHERE location_id in ($lid) AND itemgroup.itgroup_id=" . $gid . " AND entry_date <='" . date("Y-m-d", $em) . "' AND entry_mode !='O+' GROUP BY item_name)  ") or die(mysql_error());
                                }
                            } elseif ($iid == 0 && $gid == 0) {                                //if(item == all items AND group == all groups)
                                if ($flt == 0) {                                            //if(filter == no filetr)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name, item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM 
                             stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id
                              LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id 
                              LEFT JOIN item_category ON item_category.category_id = stock_register.item_category
                              WHERE stock_register.location_id in ($lid) AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category  ") or die(mysql_error());
                                } elseif ($flt == 1) {                                        //if(filter == only +ve stock)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name, item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM  
                            stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id
                             LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id LEFT JOIN item_category ON item_category.category_id = stock_register.item_category WHERE stock_register.location_id in ($lid) AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category HAVING qty>=0  ") or die(mysql_error());
                                } elseif ($flt == 2) {                                        //if(filter == only -ve stock)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name, item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM  stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id LEFT JOIN item_category ON item_category.category_id = stock_register.item_category WHERE stock_register.location_id in ($lid) AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY stock_register.item_category HAVING qty<0  ") or die(mysql_error());
                                } elseif ($flt == 3) {                                        //if(filter == only opening and no transactions during the period)
                                    $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name, location_name, item.item_id, item_name, item_category.category,item_category.category_id, unit_name, Sum(item_qnty) AS qty FROM  stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id LEFT JOIN item_category ON item_category.category_id = stock_register.item_category WHERE stock_register.location_id in ($lid) AND (entry_date<'" . date("Y-m-d", $sm) . "' OR entry_mode ='O+') GROUP BY itgroup_name, item_name HAVING qty>0 AND item.item_id NOT IN (SELECT Distinct item_id FROM  stock_register WHERE location_id in ($lid) AND entry_date <='" . date("Y-m-d", $em) . "' AND entry_mode !='O+')  ") or die(mysql_error());
                                }
                            }

                            // if (isset($_REQUEST['xon']) && $_REQUEST['xon'] == "search") {
                            //     if ($_REQUEST['on'] == 1) {
                            //         $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name,location_name, IFNULL(item.item_id,0) AS item_id, item_name, unit_name, Sum(item_qnty) AS qty,item_category.category,item_category.category_id FROM  stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id  LEFT JOIN item_category ON item_category.category_id = stock_register.item_category WHERE stock_register.location_id in ($lid) AND itgroup_name LIKE '%" . $_REQUEST['txt'] . "%' AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY itgroup_name, item_name ,item_category.category ") or die(mysql_error());
                            //     } elseif ($_REQUEST['on'] == 2) {

                            //         $sql = mysql_query("SELECT itemgroup.itgroup_id, itgroup_name,location_name, IFNULL(item.item_id,0) AS item_id, item_name, unit_name, Sum(item_qnty) AS qty,item_category.category,item_category.category_id FROM  stock_register LEFT JOIN location ON location.location_id = stock_register.location_id LEFT JOIN item ON stock_register.item_id = item.item_id LEFT JOIN itemgroup ON item.itgroup_id = itemgroup.itgroup_id LEFT JOIN unit ON item.unit_id = unit.unit_id LEFT JOIN item_category ON item_category.category_id = stock_register.item_category WHERE stock_register.location_id in ($lid) AND item_name LIKE '%" . $_REQUEST['txt'] . "%' AND entry_date <='" . date("Y-m-d", $em) . "' GROUP BY itgroup_name, item_name ,item_category.category ") or die(mysql_error());
                            //     }
                            //     if (mysql_num_rows($sql) == 0) {
                            //         echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: bold; color:#000000; height:25px;">';
                            //         echo '<td align="center" colspan="11" width="100%">** No Data Found for the Selected Search **</td>';
                            //         echo '</tr>';
                            //     }
                            // }
                            while ($row = mysql_fetch_array($sql)) {
                                $opqnty = 0;
                                $clqnty = 0;
                                $incoming = 0;
                                $outgoing = 0;
                                $sql_opstk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE stock_register.location_id in ($lid) AND item_id=" . $row['item_id'] . " AND item_category=" . $row['category_id'] . " AND (entry_date<'" . date("Y-m-d", $sm) . "' OR entry_mode='O+')") or die(mysql_error());
                                $row_opstk = mysql_fetch_assoc($sql_opstk);
                                $opqnty = $row_opstk['itemqnty'];

                                $sql_instk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE stock_register.location_id in ($lid) AND item_id=" . $row['item_id'] . " AND item_category=" . $row['category_id'] . " AND (entry_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "') AND (entry_mode='R+' OR entry_mode='I-' OR entry_mode='T+' OR entry_mode='X+' OR entry_mode='P+' OR entry_mode='C+')") or die(mysql_error());
                                $row_instk = mysql_fetch_assoc($sql_instk);
                                $incoming = $row_instk['itemqnty'];

                                $sql_outstk = mysql_query("SELECT IFNULL(Sum(item_qnty),0) AS itemqnty FROM stock_register WHERE stock_register.location_id in ($lid) AND item_id=" . $row['item_id'] . " AND item_category=" . $row['category_id'] . " AND (entry_date BETWEEN '" . date("Y-m-d", $sm) . "' AND '" . date("Y-m-d", $em) . "') AND (entry_mode='R-' OR entry_mode='I+' OR entry_mode='T-' OR entry_mode='X-' OR entry_mode='P-')") or die(mysql_error());
                                $row_outstk = mysql_fetch_assoc($sql_outstk);
                                $outgoing = 0 - $row_outstk['itemqnty'];

                                if ($cnt == 1) {
                                    echo '<tr bgcolor="#EEEEEE" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
                                    $cnt = 0;
                                } else {
                                    echo '<tr bgcolor="#FFFFCC" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; font-weight: normal; color:#000000; height:25px;">';
                                    $cnt = 1;
                                }
                                $ctr++;
                                $itemid = $row['item_id'];
                                $category_id = $row['category_id'];
                                $x = "window.open('mthstock.php?lid=$lid&iid=$itemid&category=$category_id&sm=$sm&em=$em', 'monthlystock', 'width=1075, height=650, resizable=no, scrollbars=yes, toolbar=no, location=no, directories=no, status=yes, menubar=no, copyhistory=no')";
                                echo '<td style="border-left:none; border-bottom:none" width="4%">' . $ctr . '</td>';
                                echo '<td style="border-left:none; border-bottom:none" width="20%">' . $row['location_name'] . '</td>';
                                echo '<td style="border-left:none; border-bottom:none" width="20%">' . $row['itgroup_name'] . '</td>';
                                echo '<td style="border-left:none; border-bottom:none" width="20%"><a onclick="' . $x . '" style="display:inline; cursor:hand; text-decoration:underline; font-size:12px; cursor:hand; color:#0000FF;">' . $row['item_name'] . '</a></td>';
                                echo '<td style="border-left:none; border-bottom:none" width="20%"><a onclick="' . $x . '" style="display:inline; cursor:hand; text-decoration:underline; font-size:12px; cursor:hand; color:#0000FF;">' . $row['category'] . '</a></td>';
                                echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">' . ($opqnty == 0 ? "&nbsp;" : number_format($opqnty, 3, ".", "")) . '&nbsp;' . ($opqnty == 0 ? "&nbsp;" : $row['unit_name']) . '</td>';

                                echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">' . ($incoming == 0 ? "&nbsp;" : number_format($incoming, 3, ".", "")) . '&nbsp;' . ($incoming == 0 ? "&nbsp;" : $row['unit_name']) . '</td>';

                                echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">' . ($outgoing == 0 ? "&nbsp;" : number_format($outgoing, 3, ".", "")) . '&nbsp;' . ($outgoing == 0 ? "&nbsp;" : $row['unit_name']) . '</td>';

                                echo '<td style="border-left:none; border-bottom:none; border-right:none" width="10%" align="right">' . ($row['qty'] == 0 ? "&nbsp;" : number_format($row['qty'], 3, ".", "")) . '&nbsp;' . ($row['qty'] == 0 ? "&nbsp;" : $row['unit_name']) . '</td>';

                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mytable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-4'i><'col-sm-4 text-center'l><'col-sm-4'p>>",
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        title: 'Item Stock List',
                    },

                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Item Stock List',
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        title: 'Item Stock List',
                    }
                ]
            });
        });
    </script>
</body>

</html>