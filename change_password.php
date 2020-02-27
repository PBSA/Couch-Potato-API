<?php
        include "db.php";
       
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        $postdata = file_get_contents("php://input");
        $user = json_decode($postdata,true);
        $userid = $user['userid'];
        $salt = $user['salt'];
        $password = $user['password'];
        $username = $user['username'];
        
        $q = mysqli_query($con, "UPDATE `users` SET  `salt` = '$salt',`password` = '$password' 
                                            WHERE `id` = '$userid'"); 
        if($q){
            $message->status = "200";
            $message->title = "Password successfully changed";
            $message->message = $id . $username;   
        }
        else{
            $message->status = "400";
            $message->subcode = "447";
            $message->title = "Failed to change password for [ " . $username . "]";
            $message->message = "";
        } 
        echo json_encode($message);         
?>