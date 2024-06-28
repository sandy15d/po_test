<?php
include"menu.php";
?>
<!DOCTYPE html>
<html>
    <head>
           <script src="js/jquery-1.11.2.min.js"></script>
        <script>
        function funEdit(id){
            $("input[id$=Loc_"+id+"]").css({"color":"red","background":"url('images/white.jpg')"}).attr({"readonly":false});
            $("#edit_"+id).hide();$("#cancel_"+id).hide();$("#check_"+id).show();
        }
        function funDel(id){
            if(confirm("Are you confirm to Delete")){
           $.post("location_delete.php",{id:id},function(data){
             location="location.php";
           })
       }
        }
        function funStaff(id){
           $("#staff").attr("src","addStaff.php?id="+id);
        }
        </script>
    </head>
	
<table style="border:0px solid green;border-collapse:collapse;">
       
            <td width="50%">
                <table border="1" style="border: 1px solid;border-collapse:collapse;">
       <tr style="height:25px;">
                     <th style="background:#FFB7FF;" align="center">&nbsp;Sn&nbsp;</th>
        <th style="background:#FFB7FF;" align="center">Location</th>
        <th style="background:#FFB7FF;" align="center">Prefix</th>
        <th style="background:#FFB7FF;" align="center">Suffix</th>
        <th style="background:#FFB7FF;" align="center">&nbsp;Action&nbsp;</th>
        <th style="background:#FFB7FF;" align="center">&nbsp;&nbsp;Staff&nbsp;&nbsp;</th>
                    <?php
                    if(isset($_POST['sub'])){
                        
                        mysql_query("update location set location_name='".$_POST['nameLoc']."',location_prefix='".$_POST['prefLoc']."',location_suffix='".$_POST['sufLoc']."' where location_id=".$_POST['idLoc']);
                    }
                    if(isset($_POST['subAdd'])){
                    
                       if(mysql_num_rows(mysql_query("select * from location where location_name='".$_POST['nameLoc']."'")))
                       {  echo"<script>alert('name already exists')</script>";
                       
                       }
                       else
                        mysql_query("insert into location values('','".$_POST['nameLoc']."','".$_POST['prefLoc']."','".$_POST['sufLoc']."')");
                    }
                        $dataLoc=mysql_query("select * from location ");
                    $i=0;
                    while($recLoc=  mysql_fetch_array($dataLoc))
                            echo"<form method='post'><tr style='height:24px;'><td>".++$i."<input type='hidden' name='idLoc' value='".$recLoc[0]."'></td><td><input type='text' name='nameLoc' id='nameLoc_".$recLoc[0]."' value='".$recLoc['location_name']."'  style='background:url(images/hbox2.jpg);border:none;background-repeat:no-repeat' readonly></td><td><input type='text' style='background:url(images/hbox2.jpg);border:none;background-repeat:no-repeat' readonly name='prefLoc' id='prefLoc_".$recLoc[0]."' value='".$recLoc['location_prefix']."'></td><td><input type='text' name='sufLoc' id='sufLoc_".$recLoc['location_id']."' style='background:url(images/hbox2.jpg);border:none;background-repeat:no-repeat;' readonly id='suf_".$recLoc[0]."' value='".$recLoc['location_suffix']."'></td><td><center><img src='images/edit.gif' title='Edit' style='cursor:pointer' onclick=funEdit('".$recLoc[0]."') id='edit_".$recLoc[0]."'><img id='cancel_".$recLoc[0]."' style='cursor:pointer' src='images/cancel.png' title='delete' onclick=funDel('".$recLoc[0]."')><input type='submit'   name='sub' title='save' style='background:url(images/check.gif);cursor:pointer;background-repeat:no-repeat;width:15px;display:none' id='check_".$recLoc[0]."' value=''></td>
							
							<td><center><img src='images/Plus.gif' style='cursor:pointer' onclick=funStaff('".$recLoc[0]."')></td>
							
							</tr></form>";
                   
				   
				    echo"<form method='post'><tr><td></td><td><input type='text' required name='nameLoc'></td><td><input type='text'required name='prefLoc'></td><td><input type='text' required name='sufLoc'></td><td colspan=2><input type='submit'name='subAdd' style='background:url(images/add.gif);width:80px' value=' ' ></td></tr></form>"; 
                    ?>
              
			<td></td>
            <td>
        <iframe width="100%" id="staff" style="border:0px;"/>
            </td>
        </tr>
    </table>
