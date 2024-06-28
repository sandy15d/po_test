<?php
    include 'db_connect.php';

    if(isset($_POST['action'])&&!empty($_POST['action'])){
        $action=$_POST['action'];
        if($action=='getitemgroup'){
            $output = array('data' => array());
 
            $sql = "SELECT * FROM itemgroup";
            $query = $connect->query($sql);
             
            $x = 1;
            while ($row = $query->fetch_assoc()) {
            $actionButton = '<button type ="button" class="btn btn-sm" data-toggle="modal" data-target="#groupmodal" onclick="editGroup('.$row['itgroup_id'].')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>';
                $output['data'][] = array(
                    $x,
                    $row['itgroup_name'],  
                    $actionButton
                   
                );
             
                $x++;
            }
             
            // database connection close
            $connect->close();
             
            echo json_encode($output);
        }
        //-------------------Add/update city
        elseif ($action=='addgroup') {
            $group_id = $_POST['group_id'];
            $group_name=$_POST['group_name'];
          

            if(!empty($group_id)){
                $sql ="UPDATE itemgroup SET itgroup_name ='$group_name' WHERE itgroup_id =$group_id";
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
                $check = "SELECT * FROM itemgroup WHERE itgroup_name ='".$group_name."'";
                $result=$connect->query($check);
                if(mysqli_num_rows($result)>0){
                    $validator['success'] = false;
                    $validator['messages'] = "Item Group Already Exist"; 
                    $connect->close();
                    echo json_encode($validator);
                }else{
                    $sql = "INSERT INTO itemgroup(itgroup_name,can_be_delete) VALUES('$group_name','N')";
                    $query =$connect->query($sql);
                    if($query === TRUE) {           
                        $validator['success'] = true;
                        $validator['messages'] = "Successfully Added"; 
                    } else {        
                        $validator['success'] = false;
                        $validator['messages'] = "Error while adding the Item Group information";
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