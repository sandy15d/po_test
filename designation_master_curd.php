<?php
    include 'db_connect.php';

    if(isset($_POST['action'])&&!empty($_POST['action'])){
        $action=$_POST['action'];
        if($action=='getpost'){
            $output = array('data' => array());
 
            $sql = "SELECT * FROM designation";
            $query = $connect->query($sql);
             
            $x = 1;
            while ($row = $query->fetch_assoc()) {
            $actionButton = '
                
            
                <button type ="button" class="btn " data-toggle="modal" data-target="#postmodal" onclick="editPost('.$row['post_id'].')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                
                
                <button type ="button"  class="btn " data-toggle="modal" data-target="#removePostModal" onclick="removePost('.$row['post_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
                ';
                
             
                $output['data'][] = array(
                    $x,
                    $row['post_name'],
                 
                   
                    $actionButton
                   
                );
             
                $x++;
            }
             
            // database connection close
            $connect->close();
             
            echo json_encode($output);
        }
        //-------------------Add/update city
        elseif ($action=='addpost') {
            $post_id = $_POST['post_id'];
            $post_name=$_POST['post_name'];
          

            if(!empty($post_id)){
                $sql ="UPDATE designation SET post_name ='$post_name' WHERE post_id =$post_id";
                $query=$connect->query($sql);
                if($query === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Updated"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while Updating the Designation information";
                }

                $connect->close();
                echo json_encode($validator);
            }else{
                $search = "SELECT * FROM designation WHERE post_name ='".$post_name."'";
                $result =$connect->query($search);
                if(mysqli_num_rows($result)>0){
                    $validator['success'] = false;
                    $validator['messages'] = "Post Already Exist"; 
                    $connect->close();
                    echo json_encode($validator);
                }else{
                    $sql = "INSERT INTO designation(post_name) VALUES('$post_name')";
                    $query =$connect->query($sql);
                    if($query === TRUE) {           
                        $validator['success'] = true;
                        $validator['messages'] = "Successfully Added"; 
                    } else {        
                        $validator['success'] = false;
                        $validator['messages'] = "Error while adding the City information";
                    }
                            
                // close the database connection
                $connect->close();
                echo json_encode($validator);
                }


            }
        }
        //------------------------Delete City
        elseif ($action=='delete_post') {
            # code...
            $post_id= $_POST['post_id'];
            $output = array('success' => false, 'messages' => array());
  
            $sql = "DELETE FROM designation WHERE post_id = {$post_id}";
            $query = $connect->query($sql);
            if($query === TRUE) {
                $output['success'] = true;
                $output['messages'] = 'Successfully removed';
            } else {
                $output['success'] = false;
                $output['messages'] = 'Error while removing the post information';
            }
             
            // close database connection
            $connect->close();
             
            echo json_encode($output);
                }
                //------------------get single City-----------------
                
                elseif ($action=='get_single_post') {
                    
                    $post_id= $_POST['post_id'];
                  
                    $sql = "SELECT * FROM designation WHERE post_id=$post_id";
                    $query=$connect->query($sql);
                    $output = array('data' => array());
                    while ($row = $query->fetch_assoc()) {
                      
                        $output = array(
                            'post_id'=> $row['post_id'],
                            'post_name'=>  $row['post_name']
                            
                              
                          );
                    }
                    $connect->close();
                    echo json_encode($output);
                }
    }
?>