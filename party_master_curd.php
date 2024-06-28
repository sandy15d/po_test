<?php
    require_once 'db_connect.php';
    //----------------------------//Retrive Party List------------------//
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    if($action == 'getParty'){
    $output = array('data' => array());
$sql = "SELECT p.party_id,p.party_name,p.category,c.city_name,s.state_name,p.code,p.party_type,p.sub_group,p.status FROM `party`p 
LEFT JOIN city c ON c.city_id =p.city_id
LEFT JOIN state s ON s.state_id =c.state_id";
$query = $connect->query($sql); 
$x = 1;
$category;
while ($row = $query->fetch_assoc()) {
    if($row['category']==1){
       $category='Preferencial';
    }elseif ($row['category']==2) {
        $category='blank-1';
    }elseif ($row['category']==3) {
        $category='blank-2';
    }elseif ($row['category']==4) {
        $category='blank-3';
    }
$actionButton = '
    <button type ="button" class="btn btn view"  onclick="editParty('.$row['party_id'].')"><span class="glyphicon glyphicon-edit" style="color:green"></span></button>
    <button type ="button"  class="btn btn delete" data-toggle="modal" data-target="#removePartyModal" onclick="removeParty('.$row['party_id'].')"><span class="glyphicon glyphicon-trash" style="color:red"></span></button>    
    ';

    
 
    $output['data'][] = array(
        $x,
        $row['party_name'],
        $row['code'],
        $row['party_type'],
        $row['sub_group'],
        $row['city_name'],
        $row['state_name'],
        $category,
        ($row['status'] =='A'?'Active':'Deactive'),
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
   elseif ($action=='get_edit') {
    $party_id = $_POST['party_id'];
 
$sql = "SELECT * FROM `party`p 
left JOIN city c ON c.city_id =p.city_id
left JOIN state s ON s.state_id =c.state_id WHERE party_id = $party_id";
$query = $connect->query($sql);
$result = $query->fetch_assoc();
 
$connect->close();
 
echo json_encode($result);
}
elseif ($action=='addParty') {
   
    $party_id= $_POST['party_id'];
    $party_name = $_POST['party_name'];
    $vendor_code = $_POST['vendor_code'];
    $party_type = $_POST['party_type'];
    $sub_group = $_POST['sub_group'];
    $contact_person=$_POST['contact_person'];
    $address1=$_POST['address1'];
    $address2=$_POST['address2'];
    $address3=$_POST['address3'];
    $city =$_POST['city'];
    $mobile=$_POST['mobile'];
    $email=$_POST['email'];
    $pan=$_POST['pan'];
    $tin=$_POST['tin'];
    $gst=$_POST['gst'];
    $opening_balance=$_POST['opening_balance'];
    $credit_days=$_POST['credit_days'];
    $category=$_POST['category'];
    $msme = $_POST['msme'];
    $msme_number = $_POST['msme_number'];
    $status=$_POST['status'];
    if(!empty($party_id)){
        $sql = "UPDATE party SET party_name='$party_name',code='$vendor_code',party_type='$party_type',sub_group='$sub_group',contact_person='$contact_person',address1='$address1',address2='$address2',
        address3='$address3', city_id='$city',contact_person='$contact_person',email_id='$email',mobile_no='$mobile',
        pan='$pan',tin='$tin',gstno='$gst',op_balance='$opening_balance',credit_days='$credit_days',category='$category',status='$status',msme='$msme',msme_number='$msme_number'
        WHERE party_id=$party_id";
       
        $query=$connect->query($sql);
        if($query === TRUE) {           
            $validator['success'] = true;
            $validator['messages'] = "Successfully Updated"; 
        } else {        
            $validator['success'] = false;
            $validator['messages'] = "Error while Updating the Party information";
        }

        $connect->close();
        echo json_encode($validator);
    }
    else{
        $sql = "INSERT INTO party (party_name,code,party_type,sub_group,contact_person,address1,address2,address3,city_id,email_id,mobile_no,pan,tin,gstno,op_balance,credit_days,category,status,msme,msme_number)VALUES
        ('$party_name','$vendor_code','$party_type','$sub_group','$contact_person','$address1','$address2','$address3','$city','$email','$mobile','$pan','$tin','$gst','$opening_balance','$credit_days','$category','$status','$msme','$msme_number')";

        $query = $connect->query($sql);
        if($query === TRUE) {           
            $validator['success'] = true;
            $validator['messages'] = "Successfully Added"; 
        } else {        
            $validator['success'] = false;
            $validator['messages'] = "Error while adding the Party information";
        }

        // close the database connection
        $connect->close();
        echo json_encode($validator);

    }
    

}
    //------------------------------------Delete Party----------------

    elseif ($action=='delete_party') {
        $output = array('success' => false, 'messages' => array());
 
$party_id = $_POST['party_id'];
 
$sql = "DELETE FROM party WHERE party_id = {$party_id}";
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
 ?>