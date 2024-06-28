 <script src="js/jquery-1.11.2.min.js"></script>
 <script>
function funStaffEdit(id) {
    $("input[id$=Staff_" + id + "]").css({
        "color": "red",
        "background": "url('images/white.jpg')"
    }).attr("readonly", false);
    $("#staffEdit_" + id).hide();
    $("#staffCancel_" + id).hide();
    $("#staffCheck_" + id).show();
}

function funStaffDel(id) {
    if (confirm("Are you confirm to Delete")) {
        $.post("staff_delete.php", {
            id: id
        }, function(data) {
            location.reload();
        })
    }
}
 </script>
 <table border="1">
     <th style="background:#FFB7FF;" align="center">&nbsp;Sn&nbsp;</th>
     <th style="background:#FFB7FF;" align="center">StaffName</th>
     <?php /*?><th style="background:#FFB7FF;" align="center">Post_Name</th>
     <th style="background:#FFB7FF;" align="center">&nbsp;Action&nbsp;</th><?php */ ?>
     <?php
        include "config/config.php";
        if (isset($_POST['sub'])) {

            mysql_query("update staff set staff_name='" . $_POST['nameStaff'] . "',post_id='" . $_POST['postStaff'] . "',location_id='" . $_POST['loc_id'] . "' where staff_id=" . $_POST['staffId']);
        }
        if (isset($_POST['subAdd'])) {

            if (mysql_num_rows(mysql_query("select * from staff where staff_name='" . $_POST['nameStaff'] . "'"))) {
                echo "<script>alert('name already exists')</script>";
            } else
                mysql_query("insert into staff values('','" . $_POST['nameStaff'] . "','" . $_POST['postStaff'] . "','" . $_POST['locId'] . "')");
        }
        $data = mysql_query("select * from staff where location_id=" . $_REQUEST['id']);
        $i = 0;
        while ($rec =  mysql_fetch_array($data)) {

            echo "<form method='post'><tr bgcolor='#FFF'><td align='center'>" . ++$i . "<input type='hidden' name='idStaff' value='" . $rec[0] . "'></td><td><input type='text' name='nameStaff' id='nameStaff_" . $rec[0] . "' value='" . $rec['staff_name'] . "'  style='border:none;' readonly></td>";

            /*echo "<td><select type='text' style='background:url(images/hbox2.jpg);border:none;background-repeat:no-repeat'  name='postStaff' id='postStaff_".$rec[0]."'><option value='1'>Field Incharge</option><option value=2>Store Keeper</option></select><input type='hidden' name='loc_id' value='".$_REQUEST['id']."'><input type='hidden' name='staffId' value=".$rec[0]."></td>
		
		<td><center><img src='images/edit.gif' title='Edit' style='cursor:pointer' onclick=funStaffEdit('".$rec[0]."') id='staffEdit_".$rec[0]."'><img id='staffCancel_".$rec[0]."' style='cursor:pointer' src='images/cancel.png' title='delete' onclick=funStaffDel('".$rec[0]."')><input type='submit'   name='sub' title='save' style='background:url(images/check.gif);cursor:pointer;background-repeat:no-repeat;width:15px;display:none' id='staffCheck_".$rec[0]."' value=''></td>";*/

            echo "</tr></form>";
            echo "<script>
        
$('#postStaff_" . $rec[0] . "').val('" . $rec['post_id'] . "');
</script>";
        }
        /*echo"<form method='post'><tr><td></td><td><input type='text' required name='nameStaff'></td><td><select  name='postStaff'><option value=1>Field Incharge</option><option value=2>Store Keeper</option></select><input type='hidden' name='locId' value=".$_REQUEST['id']."></td><td colspan=2><input type='submit'name='subAdd' style='background:url(images/add.gif);width:80px' value=' ' ></td></tr></form>";*/
        ?>
 </table>