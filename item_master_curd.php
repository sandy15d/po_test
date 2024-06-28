<?php
  session_start();
    include 'db_connect.php';

    if(isset($_POST['action'])&&!empty($_POST['action'])){
        $action=$_POST['action'];
        if($action=='get_item_list'){    
        
        $output = array('data' => array());
        $search=$_POST['search'];
        $page_no = $_POST['page_no'];
        $total_records_per_page = 10;
        $offset = ($page_no-1) * $total_records_per_page;
        $previous_page = $page_no - 1;
        $next_page = $page_no + 1;
        $adjacents = "2";

        $condition ='';
        if(!empty($search)){
            $condition .= ' AND item.item_name like "%'.$search.'%" OR itemgroup.itgroup_name like "%'.$search.'%"';
        }
        $sql = "SELECT item.*,itgroup_name,U1.unit_name unit_name, ifnull(U2.unit_name,'-') alt_unit_name,GROUP_CONCAT(DISTINCT ic.category) as category FROM item
        INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id 
        INNER JOIN unit U1 ON item.unit_id=U1.unit_id
        LEFT JOIN unit U2 ON item.alt_unit_id =U2.unit_id
        LEFT JOIN item_category ic ON ic.item_id = item.item_id
        WHERE 1=1 ".$condition." GROUP BY item.item_id LIMIT $offset,$total_records_per_page ";
      
      //echo $sql;
      
        $query = $connect->query($sql);
        
        $x = 1;
        while ($row = $query->fetch_assoc()) {
            $edit='';
            $delete='';
            $add_category ='';
          
             if( $_SESSION["stores_utype"]=="S"){
                 
            $edit='<button type ="button"  class="btn btn-sm edit" data-toggle="modal" data-target="#itemmodal" onclick="editItem('.$row['item_id'].')"><span class="glyphicon glyphicon-pencil" style="color:blue"></span></button>';   
            
            $delete='<button type ="button"  class="btn btn-sm deleteitem"  onclick="deleteItem('.$row['item_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>'; 
            
            $add_category='<button type ="button"  class="btn btn-sm add_category"  onclick="show_category('.$row['item_id'].')"><span class="glyphicon glyphicon-plus" style="color:blue"></span></button>';    
             }
            $output['data'][] = array(
                $x,
                'item_id'=>$row['item_id'],
                'item_name'=> $row['item_name'],
                'itgroup_name'=> $row['itgroup_name'],
                'unit_name'=> $row['unit_name'],
                'alt_unit_name'=> $row['alt_unit_name'],
                'add_category'=> $add_category,
                'category_name'=>$row['category'],
                'edit'=> $edit,
                'delete'=>$delete
            );
            $x++;
        }
        
        $sql1 = "SELECT COUNT(*) total FROM item
        INNER JOIN itemgroup ON item.itgroup_id=itemgroup.itgroup_id 
       WHERE 1=1 ".$condition."";
        $total_records = $connect->query($sql1);
        $total_records = mysqli_fetch_array($total_records);
        $total_records = $total_records['total'];  
    
        //$total_no_of_pages = ceil($total_records / $total_records_per_page);
        //$second_last = $total_no_of_pages - 1; // total pages minus 1

        $total_no_of_pages = ceil($total_records / $total_records_per_page);
	    $pagination = array();
	    $flag = true;
	    for($i=1; $i <= $total_no_of_pages; $i++){
	        if($i >= ($page_no -1) && $i <= ($page_no + 5)){
	            $pagination['link'][] = $i;
	            $flag = false;
	        }
	        
	        if($i > ($total_no_of_pages-6) && $flag == true){
	            $pagination['link'][] = $i;
	        }
        }
        $final = array($output,'page_link'=>$pagination,'total_record'=>$total_records);
        //print_r($final);die;
        echo json_encode($final);
        $connect->close();
        }
        elseif($action=='additem'){
            $item_id =$_POST['item_id'];
            $item_name=$_POST['item_name'];
            $group_name =$_POST['group_name'];
            $tech_name=$_POST['tech_name'];
            $tech_desc=$_POST['tech_desc'];
            $unit_name=$_POST['unit_name'];
            $alt_unit_apply=$_POST['alt_unit_apply'];
            $alt_unit =$_POST['alt_unit'];
            $alt_num =$_POST['alt_num'];
            $water_require =$_POST['water_require'];
            $recommended_dose =$_POST['recommended_dose'];
            $max_dose=$_POST['max_dose'];
            $min_dose=$_POST['min_dose'];
            $usefull_1=$_POST['usefull_1'];
            $usefull_2=$_POST['usefull_2'];
            $usefull_3 =$_POST['usefull_3'];
            $app_method =$_POST['app_method'];
            $rp_form1 =$_POST['rp_form1'];
            $rp_form2 =$_POST['rp_form2'];
            $rp_form3 =$_POST['rp_form3'];
            $rp_to1 =$_POST['rp_to1'];
            $rp_to2 =$_POST['rp_to2'];
            $rp_to3 =$_POST['rp_to3'];
            $reorder=$_POST['re_order'];
            $lead_time=$_POST['lead_time'];
            if(!empty($item_id)){
                $check_item ="SELECT * FROM item WHERE item_name ='$item_name' AND item_id <> $item_id";
                
                $check =$connect->query($check_item);
                if(mysqli_num_rows($check)>0){
                    $validator['success']=false;
                    $validator['messages']="Duplicate Item, Please enter another item..";
                    $connect->close();
                    echo json_encode($validator);
                    die;
                }
                $sql ="UPDATE item SET item_name='$item_name',itgroup_id='$group_name',tech_name='$tech_name',unit_id='$unit_name',alt_unit='$alt_unit_apply',alt_unit_id='$alt_unit',alt_unit_num='$alt_num',water_require='$water_require',recomend_dose='$recommended_dose',max_dose='$max_dose',min_dose='$min_dose', usability_id1='$usefull_1',usability_id2='$usefull_2',usability_id3='$usefull_3',app_method='$app_method',rp_from1='$rp_form1',rp_to1='$rp_to1',rp_from2='$rp_form2',rp_to2='$rp_to2',rp_from3='$rp_form3',rp_to3='$rp_to3',reorder_level='$reorder',lead_time='$lead_time',tech_details='$tech_desc'  WHERE item_id =$item_id";
                $query=$connect->query($sql);
                if($query === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Updated"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while Updating the Item information";
                }

                $connect->close();
                echo json_encode($validator);
            }else{
                $check = "SELECT * FROM item WHERE item_name ='".$item_name."'";
                $result=$connect->query($check);
                if(mysqli_num_rows($result)>0){
                    $validator['success'] = false;
                    $validator['messages'] = "Item Already Exist"; 
                    $connect->close();
                    echo json_encode($validator);
                }else{
/*                     $sql1 = "SELECT Max(item_id) as maxid FROM item WHERE itgroup_id=$group_name";
                    $query1=$connect->query($sql1);
                    $query1 =mysqli_fetch_array($query1);
                    $maxid=$query1['maxid'];
                    $iid = ($maxid==null ? $group_name*1000+1 : $maxid+1); */
                    $sql = "INSERT INTO item(item_name,itgroup_id,tech_name,unit_id,alt_unit,alt_unit_id,alt_unit_num,water_require,recomend_dose,max_dose,min_dose, usability_id1,usability_id2,usability_id3,app_method,rp_from1,rp_to1,rp_from2,rp_to2,rp_from3,rp_to3,reorder_level,lead_time,tech_details) 
                    VALUES('$item_name','$group_name','$tech_name','$unit_name','$alt_unit_apply','$alt_unit','$alt_num','$water_require','$recommended_dose','$max_dose','$min_dose','$usefull_1','$usefull_2','$usefull_3','$app_method','$rp_form1','$rp_to1','$rp_form2','$rp_to2','$rp_form3','$rp_to3','$reorder','$lead_time','$tech_desc')";
                    $query =$connect->query($sql);
                    if($query === TRUE) {           
                        $validator['success'] = true;
                        $validator['messages'] = "Successfully Added"; 
                    } else {        
                        $validator['success'] = false;
                        $validator['messages'] = "Error while adding the Item information";
                    }
                      // close the database connection
                $connect->close();
                echo json_encode($validator);
                }
            }
        }elseif ($action=='get_single_item') {
                    
            $item_id= $_POST['item_id'];
          
            $sql = "SELECT * FROM item it
            JOIN itemgroup itg ON itg.itgroup_id =it.itgroup_id
            WHERE it.item_id=$item_id";
          //  print_r($sql);die;
            $query=$connect->query($sql);
            $result = $query->fetch_assoc();
 
            $connect->close();
             
            echo json_encode($result);
        }elseif ($action=='save_category') {
            $item_id =$_POST['item_id'];
            $category =$_POST['category'];
            $sql ="INSERT INTO item_category(item_id,category) VALUES($item_id,'$category')";
            $query =$connect->query($sql);
            if($query === TRUE) {           
                $validator['success'] = true;
                $validator['messages'] = "Successfully Added"; 
            } else {        
                $validator['success'] = false;
                $validator['messages'] = "Error while adding the Item information";
            }
            $connect->close();
            echo json_encode($validator);
        }elseif ($action=='get_category_list') {
            $item_id =$_POST['item_id'];
            $sql ="SELECT * FROM item_category WHERE item_id =$item_id";
            $query =$connect->query($sql);
            while ($row = $query->fetch_assoc()) {
                $output['data'][] = array(
                   'category_id'=>$row['category_id'],
                    'category'=> $row['category']
                );
            }
            echo json_encode($output);
        }elseif ($action=='delete_category') {
            $cat_id =$_POST['cat_id'];
            $check ="SELECT * FROM tbl_indent_item WHERE item_category= $cat_id";
          
            $query =$connect->query($check);
            if(mysqli_num_rows($query)>0){
                $validator['success']=false;
                $validator['messages']="This Category is used in Indent.. Can not be delete...";
                $connect->close();
                echo json_encode($validator);
                die;
            }else{
                $sql="DELETE FROM item_category WHERE category_id =$cat_id";
                $query =$connect->query($sql);
                if($query == TRUE){
                    $output['success']=true;
                }else {
                    $output['success']=false;
                }
    
                $connect->close();
                echo json_encode($output);
            }

            
        }elseif ($action=='delete_item') {
            $Id =$_POST['Id'];
            $check ="SELECT * FROM tbl_indent_item WHERE item_id= $Id";
          
            $query =$connect->query($check);
            if(mysqli_num_rows($query)>0){
                $validator['success']=false;
                $validator['messages']="This Item is used in Indent.. Can not be delete...";
                $connect->close();
                echo json_encode($validator);
                die;
            }else{
                $sql="DELETE FROM item WHERE item_id =$Id";
                $query =$connect->query($sql);
                if($query == TRUE){
                    $output['success']=true;
                }else {
                    $output['success']=false;
                }
    
                $connect->close();
                echo json_encode($output);
            }

            
        }
    }
?>