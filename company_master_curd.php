<?php
    require_once 'db_connect.php';
    //----------------------------//Retrive Comapy List------------------//
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if($action == 'getCompany'){
    $output = array('data' => array());
$sql = "SELECT * FROM `company` c JOIN city ct ON ct.city_id = c.c_cityid JOIN state s ON s.state_id =ct.state_id ORDER BY c.c_cityid";
$query = $connect->query($sql); 
$x = 1;
while ($row = $query->fetch_assoc()) {
$actionButton = '
    

    <button type ="button" class="btn btn edit" data-toggle="modal" data-target="#viewCompanyModal" onclick="viewCompany('.$row['company_id'].')"><span class="glyphicon glyphicon-eye-open" style="color:blue"></span></button>
    <button type ="button" class="btn btn view" data-toggle="modal" data-target="#addCompany" onclick="editCompany('.$row['company_id'].')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
    
    
    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removeComapanyModal" onclick="removeCompany('.$row['company_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
    ';

    
 
    $output['data'][] = array(
        $x,
        $row['company_name'],
        $row['CCode'],
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
 //---------------------------Get State Name ---------------------------
elseif($action=='getState'){
     $output = array();
        $city_id = $_POST['city'];
        $sql=   "SELECT * FROM city c JOIN state s ON s.state_id = c.state_id WHERE city_id=$city_id";
        $query = $connect->query($sql);
        while ($row = $query->fetch_assoc()) {
           
                $output = array(
                   
                  'state_name'=>  $row['state_name'],
                    
                );

            }
       
        echo json_encode($output);
        
    }

    //------------------------Add Company Detaisl---------------------------
    elseif ($action=='addCompany') {
        $company_id= $_POST['company_id'];
        $company_name = $_POST['company_name'];
        $company_code = $_POST['company_code'];
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $address3 = $_POST['address3'];
        $phone = $_POST['phone'];
        $city = $_POST['city'];
        $email = $_POST['email'];
        $fax = $_POST['fax'];
        $pan = $_POST['pan'];
        $gst = $_POST['gst'];
        $cin = $_POST['cin'];
        $tin = $_POST['tin'];
        $cst = $_POST['cst'];
        if(!empty($company_id)){
            $sql = "UPDATE company SET company_name='$company_name', CCode='$company_code',c_address1='$address1',c_address2='$address2',c_address3='$address3',c_cityid='$city',c_phone='$phone',c_fax='$fax',c_email='$email',c_tin='$tin',c_cst='$cst',c_gst='$gst',c_cin='$cin',c_pan='$pan' WHERE company_id=$company_id";
            $query=$connect->query($sql);
            if($query === TRUE) {           
                $validator['success'] = true;
                $validator['messages'] = "Successfully Updated"; 
            } else {        
                $validator['success'] = false;
                $validator['messages'] = "Error while Updating the Company information";
            }

            $connect->close();
            echo json_encode($validator);
        }
        else{
            $sql = "INSERT INTO company (company_name,CCode,c_address1,c_address2,c_address3,c_cityid,c_phone,c_fax,c_email,c_tin,c_cst,c_gst,c_cin,c_pan) VALUES ('$company_name', '$company_code', '$address1','$address2','$address3','$city','$phone','$fax','$email','$tin','$cst','$gst','$cin','$pan')";
            $query = $connect->query($sql);
            if($query === TRUE) {           
                $validator['success'] = true;
                $validator['messages'] = "Successfully Added"; 
            } else {        
                $validator['success'] = false;
                $validator['messages'] = "Error while adding the Company information";
            }
    
            // close the database connection
            $connect->close();
            echo json_encode($validator);

        }

    }

    //------------------------------------Delete Company----------------

    elseif ($action=='delete_company') {
        $output = array('success' => false, 'messages' => array());
 
$company_id = $_POST['company_id'];
$sql ="SELECT * FROM plot WHERE company_id =$company_id";
$chkquery =$connect->query($sql);
$num_rows = mysqli_num_rows($chkquery);
        if($num_rows>0){
            $output['success'] = false;
            $output['messages']='To many records found in Plot master.<br>Sorry! it cant delete from company master record.';
            $connect->close();
            echo json_encode($output);;
        }
        else
        {
            $sql = "DELETE FROM company WHERE company_id = {$company_id}";
            $query = $connect->query($sql);
            if($query === TRUE) {
                $output['success'] = true;
                $output['messages'] = 'Successfully removed';
            } else {
                $output['success'] = false;
                $output['messages'] = 'Error while removing the company information';
            }
             
            // close database connection
            $connect->close();
             
            echo json_encode($output);
        }
 

    }

//------------------View Single Company Data
elseif ($action=='get_view') {
$company_id = $_POST['company_id'];
$sql = "SELECT * FROM company c  
JOIN city ct ON ct.city_id = c.c_cityid 
JOIN state s ON s.state_id = ct.state_id WHERE c.company_id = $company_id";
$query = $connect->query($sql);
$result = $query->fetch_assoc();
$connect->close();
 
echo json_encode($result);
}

}
 

?>