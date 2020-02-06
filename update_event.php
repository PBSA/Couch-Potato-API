<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    if($data['action'] == "insert"){
        $user = $data['user']; 
        $league = $data['league'];
        $date = $data['date']; 
        
        $q = mysqli_query($con, "INSERT INTO `events` ( `user`, `league`, `date` ) 
                                    VALUES ('$user', '$league', '$date')"); 

        // get the last id
        $x = $con->query("SELECT MAX(id) as `id` FROM events");
        $row=mysqli_fetch_object($x);

        if($q){
           echo json_encode($row);
        }
        else{
            $message['status'] = "error"; 
            echo json_encode($message); 
        }
    }
    echo mysqli_error($con); 
?>