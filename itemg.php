<?php
include"menu.php";

if(isset($_POST['sub']))
{ 
 mysql_query("update itemgroup set itgroup_name='".$_POST['itgroup_name_'.$_POST['itgroup_id']]."' where itgroup_id=".$_POST['itgroup_id']);
 
}

if(isset($_POST['subAdd']))
{ 
mysql_query("insert into itemgroup(itgroup_name,can_be_delete) values('".$_POST['itgroup_name']."','N')");
}

?>
<!DOCTYPE html>
<html>

<head>
    <script src="js/jquery-1.11.2.min.js"></script>
    <script>
    function funEdit(id) {
        window.location = "itemg.php?value=edit&v=" + id;

        //$("input[id$=NameLoc_"+id+"]").css({"color":"red","background":"url('images/white.jpg')"}).attr({"readonly":false});
        //$("#edit_"+id).hide();$("#cancel_"+id).hide();$("#check_"+id).show();
    }

    function funDel(id) {
        if (confirm("Are you confirm to Delete")) {
            $.post("staff_delete'.php", {
                id: id
            }, function(data) {
                location = "itemg.php";
            })
        }
    }

    function funStaff(id) {
        $("#staff").attr("src", "addStaff.php?id=" + id);
    }
    </script>
</head>
<center>
    <table style="border:0px solid green;border-collapse: collapse;width:100%;">
        <tr>
            <td width="100%" style="text-align:left;">
                <table border="1" style="border: 1px solid;border-collapse: collapse; width:40%;">
                    <tr style="height:25px;">
                        <th style="background:#FFB7FF;width:5%;" align="center">&nbsp;Sn&nbsp;</th>
                        <th style="background:#FFB7FF;width:75%;" align="center">Group Name</th>
                        <th style="background:#FFB7FF;width:20%;" align="center">Action</th>
                    </tr>
                    <form method='post'>
                        <tr style="height:24px;">
                            <td></td>
                            <td><input type='text' required name='itgroup_name' style="width:100%;height:23px;"></td>
                            <td colspan=2 align="center"><input type='submit' name='subAdd'
                                    style='background:url(images/add.gif);width:80px' value=''></td>
                        </tr>
                    </form>


                    <?php  $dataStf=mysql_query("select * from itemgroup order by itgroup_name asc");
 $i=1; while($recStf= mysql_fetch_array($dataStf))
 { 
  if($_REQUEST['value']=='edit' AND $recStf[0]==$_REQUEST['v']){ ?>
                    <form method="post">

                        <tr style="height:24px;background-color:#FFFFFF;">
                            <td align="center"><?php echo $i; ?><input type="hidden" name="itgroup_id"
                                    value="<?php echo $recStf[0];?>"></td>
                            <td><input type="text" name="itgroup_name_<?php echo $recStf[0];?>"
                                    id="itgroup_name_<?php echo $recStf[0];?>" style="border:none;width:100%;"
                                    value="<?php echo $recStf['itgroup_name']; ?>"></td>
                            <td>
                                <center><input type='submit' name='sub' title='save'
                                        style='background:url(images/check.gif);cursor:pointer;background-repeat:no-repeat;width:15px;'
                                        id='check_<?php echo $recStf[0];?>' value=''></center>
                            </td>
                        </tr>
                    </form>
                    <?php }else{ ?>
                    <form method="post">
                        <tr style="height:24px;background-color:#FFFFFF;">
                            <td align="center"><?php echo $i; ?></td>
                            <td><?php echo $recStf['itgroup_name']; ?></td>
                            <td>
                                <center><img src='images/edit.gif' title='Edit' style='cursor:pointer'
                                        onclick=funEdit('<?php echo $recStf[0];?>')
                                        id='edit_<?php echo $recStf[0];?>'><?php /*?><img
                                        id='cancel_<?php echo $recLoc[0];?>' style='cursor:pointer'
                                        src='images/cancel.png' title='delete'
                                        onclick=funDel('<?php echo $recStf[0];?>')><?php */?></center>
                            </td>
                        </tr>
                    </form>

                    <?php } ?>

                    <?php $i++; } ?>

                </table>
            </td>

        </tr>
    </table>