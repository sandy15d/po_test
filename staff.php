<?php
include"menu.php";

if(isset($_POST['sub']))
{ 
 mysql_query("update staff set staff_name='".$_POST['NameStaff_'.$_POST['IdStaff']]."',post_id='".$_POST['NamePost_'.$_POST['IdStaff']]."',location_id='".$_POST['NameLoc_'.$_POST['IdStaff']]."' where staff_id=".$_POST['IdStaff']);
}

if(isset($_POST['subAdd']))
{ 
mysql_query("insert into staff(staff_name,post_id,location_id) values('".$_POST['NameStaff']."','".$_POST['Name2Post']."','".$_POST['Name2Loc']."')");
}

?>
<!DOCTYPE html>
<html>
    <head>
        <script src="js/jquery-1.11.2.min.js"></script>
        <script>
        function funEdit(id){
		 window.location="staff.php?value=edit&v="+id;
		
            //$("input[id$=NameLoc_"+id+"]").css({"color":"red","background":"url('images/white.jpg')"}).attr({"readonly":false});
            //$("#edit_"+id).hide();$("#cancel_"+id).hide();$("#check_"+id).show();
        }
        function funDel(id){
            if(confirm("Are you confirm to Delete")){
           $.post("staff_delete'.php",{id:id},function(data){
             location="staff.php";
           })
       }
        }
        function funStaff(id){
           $("#staff").attr("src","addStaff.php?id="+id);
        }
        </script>
    </head>
<center>
<table style="border:0px solid green;border-collapse: collapse;width:100%;">     
<tr>
 <td width="100%" style="text-align:left;">
  <table border="1" style="border: 1px solid;border-collapse: collapse; width:50%;">
   <tr style="height:25px;">
	<th style="background:#FFB7FF;" align="center">&nbsp;Sn&nbsp;</th>
	<th style="background:#FFB7FF;" align="center">Location</th>
    <th style="background:#FFB7FF;" align="center">Staff Name</th>
    <th style="background:#FFB7FF;" align="center">Designation</th>
    <th style="background:#FFB7FF;" align="center">Action</th>
   </tr>
<?php  $dataStf=mysql_query("select staff.*,location_name from staff INNER JOIN location ON staff.location_id=location.location_id  order by location_name asc,staff_name asc ");
 $i=1; while($recStf= mysql_fetch_array($dataStf))
 {
  $PosN=mysql_query("select post_name from designation where post_id='".$recStf['post_id']."'"); 
  $rPosN=mysql_fetch_assoc($PosN); 
  if($_REQUEST['value']=='edit' AND $recStf[0]==$_REQUEST['v']){ ?>                         
  <form method="post">
   <tr style="height:24px;background-color:#FFFFFF;">
   <td align="center"><?php echo $i; ?><input type="hidden" name="IdStaff" value="<?php echo $recStf[0];?>" ></td>
   <td><select name="NameLoc_<?php echo $recStf[0];?>" id="NameLoc_<?php echo $recStf[0];?>" style="border:none;background-repeat:no-repeat;width:100%;"><option value="<?php echo $recStf['location_id'];?>"><?php echo $recStf['location_name']; ?></option><?php $sLocN=mysql_query("select * from location order by location_name asc"); while($rrLocN=mysql_fetch_assoc($sLocN)){ echo "<option value='".$rrLocN['location_id']."'>".$rrLocN['location_name']."</option>"; } ?></select></td>
   <td><input type="text" name="NameStaff_<?php echo $recStf[0];?>" id="NameStaff_<?php echo $recStf[0];?>" style="border:none;width:100%;" value="<?php echo $recStf['staff_name']; ?>"></td>						
   <td><select name="NamePost_<?php echo $recStf[0];?>" id="NamePost_<?php echo $recStf[0];?>" style="border:none;width:100%;"><option value="<?php echo $recStf['post_id'];?>"><?php echo $rPosN['post_name'];?></option>
	<?php $sPosN=mysql_query("select * from designation order by post_name asc"); while($rrPosN=mysql_fetch_assoc($sPosN)){
		  echo "<option value='".$rrPosN['post_id']."'>".$rrPosN['post_name']."</option>"; } ?></select></td>
							
   <td><center><input type='submit' name='sub' title='save' style='background:url(images/check.gif);cursor:pointer;background-repeat:no-repeat;width:15px;' id='check_<?php echo $recStf[0];?>' value=''></center></td>
							
   <?php /*?><td><center><img src='images/plus.gif' style='cursor:pointer' onclick=funStaff('".$recLoc[0]."')></td><?php */?>
  </tr>
  </form>
  <?php }else{ ?>
  <form method="post">
   <tr style="height:24px;background-color:#FFFFFF;">
   <td align="center"><?php echo $i; ?></td>
   <td><?php echo $recStf['location_name']; ?></td>
   <td><?php echo $recStf['staff_name']; ?></td>						
   <td><?php echo $rPosN['post_name'];?></td>				
   <td><center><img src='images/edit.gif' title='Edit' style='cursor:pointer' onclick=funEdit('<?php echo $recStf[0];?>') id='edit_<?php echo $recStf[0];?>'><?php /*?><img id='cancel_<?php echo $recLoc[0];?>' style='cursor:pointer' src='images/cancel.png' title='delete' onclick=funDel('<?php echo $recStf[0];?>')><?php */ ?></center></td>
							
   <?php /*?><td><center><img src='images/plus.gif' style='cursor:pointer' onclick=funStaff('".$recLoc[0]."')></td><?php */?>
  </tr>
  </form>
  
  <?php } ?>
  
  <?php $i++; } ?>
  
                   
  <form method='post'>
  <tr style="height:24px;">
   <td></td>
   <td><select name='Name2Loc' required style="width:100%;height:23px;"><option value=0>select</option><?php $sLocN=mysql_query("select * from location order by location_name asc"); while($rrLocN=mysql_fetch_assoc($sLocN)){ echo "<option value='".$rrLocN['location_id']."'>".$rrLocN['location_name']."</option>"; } ?></select></td>
   <td><input type='text' required name='NameStaff' style="width:100%;height:23px;"></td>
   <td><select name='Name2Post' required style="width:100%;height:23px;"><option value=0>select</option><?php $sPosN=mysql_query("select * from designation order by post_name asc"); while($rrPosN=mysql_fetch_assoc($sPosN)){ echo "<option value='".$rrPosN['post_id']."'>".$rrPosN['post_name']."</option>"; } ?></select></td>				
   <td colspan=2 align="center"><input type='submit' name='subAdd' style='background:url(images/add.gif);width:80px' value=''></td>
  </tr>
  </form>
 </table>
  </td>
   
   <td><?php /*<center><iframe width="100%" id="staff" />*/ ?></td>
  </tr>
 </table>
