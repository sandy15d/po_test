<?php
    include 'db_connect.php';

    if(isset($_POST['action'])&&!empty($_POST['action'])){
        $action=$_POST['action'];
        if($action=='get_category'){
            $output = array('data' => array());
 
            $sql = "SELECT * FROM item_category";
            $query = $connect->query($sql);
             
            $x = 1;
            while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn-sm" data-toggle="modal" data-target="#category_modal" onclick="editCategory('.$row['category_id'].')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
                $output['data'][] = array(
                    $x,
                    $row['category'],  
                    $actionButton
                   
                );
             
                $x++;
            }
             
            // database connection close
            $connect->close();
             
            echo json_encode($output);
        }
        //-------------------Add/update city
        elseif ($action=='addcategory') {
            $category_id = $_POST['category_id'];
            $category=$_POST['category_name'];
          

            if(!empty($category_id)){
                $sql ="UPDATE item_category SET category ='$category' WHERE category_id =$category_id";
                $query=$connect->query($sql);
                if($query === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Updated"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while Updating the Item Group information";
                }

                $connect->close();
                echo json_encode($validator);
            }else{
                $check = "SELECT * FROM item_category WHERE category ='".$category."'";
                $result=$connect->query($check);
                if(mysqli_num_rows($result)>0){
                    $validator['success'] = false;
                    $validator['messages'] = "Item Category Already Exist"; 
                    $connect->close();
                    echo json_encode($validator);
                }else{
                    $sql = "INSERT INTO item_category(category) VALUES('$category')";
                    $query =$connect->query($sql);
                    if($query === TRUE) {           
                        $validator['success'] = true;
                        $validator['messages'] = "Successfully Added"; 
                    } else {        
                        $validator['success'] = false;
                        $validator['messages'] = "Error while adding the Item Category information";
                    }
                      // close the database connection
                $connect->close();
                echo json_encode($validator);
                }

        
              
            }
        }

                //------------------get single City-----------------
                
                elseif ($action=='get_single_group') {
                    
                    $group_id= $_POST['group_id'];
                  
                    $sql = "SELECT * FROM itemgroup WHERE itgroup_id=$group_id";
                    $query=$connect->query($sql);
                    $output = array('data' => array());
                    while ($row = $query->fetch_assoc()) {
                      
                        $output = array(
                            'itgroup_id'=> $row['itgroup_id'],
                            'itgroup_name'=>  $row['itgroup_name'],
                            
                              
                          );
                    }
                    $connect->close();
                    echo json_encode($output);
                }
    }
?>