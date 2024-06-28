<?php
    include 'db_connect.php';

    if(isset($_POST['action'])&&!empty($_POST['action'])){
        $action=$_POST['action'];
        if($action=='getcity'){
            $output = array('data' => array());
 
            $sql = "SELECT * FROM `city` c JOIN state s ON s.state_id = c.state_id ";
            $query = $connect->query($sql);
             
            $x = 1;
            while ($row = $query->fetch_assoc()) {
            $actionButton = '
                
            
                <button type ="button" class="btn " data-toggle="modal" data-target="#citymodal" onclick="editCity('.$row['city_id'].')""><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
                
                
                <button type ="button"  class="btn " data-toggle="modal" data-target="#removeCityModal" onclick="removeCity('.$row['city_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
                ';
                
             
                $output['data'][] = array(
                    $x,
                    $row['city_name'],
                    $row['state_name'],
                   
                    $actionButton
                   
                );
             
                $x++;
            }
             
            // database connection close
            $connect->close();
             
            echo json_encode($output);
        }
        //-------------------Add/update city
        elseif ($action=='addcity') {
            $city_id = $_POST['city_id'];
            $city_name=$_POST['city_name'];
            $state=$_POST['state'];

            if(!empty($city_id)){
                $sql ="UPDATE city SET city_name ='$city_name',state_id='$state' WHERE city_id =$city_id";
                $query=$connect->query($sql);
                if($query === TRUE) {           
                    $validator['success'] = true;
                    $validator['messages'] = "Successfully Updated"; 
                } else {        
                    $validator['success'] = false;
                    $validator['messages'] = "Error while Updating the City information";
                }

                $connect->close();
                echo json_encode($validator);
            }else{
                $check = "SELECT * FROM city WHERE city_name ='".$city_name."'";
                $result=$connect->query($check);
                if(mysqli_num_rows($result)>0){
                    $validator['success'] = false;
                    $validator['messages'] = "City Already Exist"; 
                    $connect->close();
                    echo json_encode($validator);
                }else{
                    $sql = "INSERT INTO city(city_name,state_id) VALUES('$city_name','$state')";
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
        elseif ($action=='delete_city') {
            # code...
            $city_id= $_POST['city_id'];
            $output = array('success' => false, 'messages' => array());
  
            $sql = "DELETE FROM city WHERE city_id = {$city_id}";
            $query = $connect->query($sql);
            if($query === TRUE) {
                $output['success'] = true;
                $output['messages'] = 'Successfully removed';
            } else {
                $output['success'] = false;
                $output['messages'] = 'Error while removing the city information';
            }
             
            // close database connection
            $connect->close();
             
            echo json_encode($output);
                }
                //------------------get single City-----------------
                
                elseif ($action=='get_single_city') {
                    
                    $city_id= $_POST['city_id'];
                  
                    $sql = "SELECT * FROM city c JOIN state s ON s.state_id =c.state_id WHERE c.city_id=$city_id";
                    $query=$connect->query($sql);
                    $output = array('data' => array());
                    while ($row = $query->fetch_assoc()) {
                      
                        $output = array(
                            'state_id'=> $row['state_id'],
                            'state_name'=>  $row['state_name'],
                            'city_name'=>  $row['city_name'],
                            'city_id'=>  $row['city_id']
                              
                          );
                    }
                    $connect->close();
                    echo json_encode($output);
                }
    }
?>