<?php
        include "db.php";
       
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        $postdata = file_get_contents("php://input");
        $user = json_decode($postdata,true);
        
        $q = mysqli_query($con, "UPDATE `user` SET  `salt` = '$user->salt',`password` = '$user->password' 
                                            WHERE `id` = '$user->id'"); 
        if($q){
            $message->status = "200";
            $message->title = "Password changed";
            $message->message = "";   
        }
        else{
            $message->status = "400";
            $message->subcode = "447";
            $message->title = "Failed to change password for [" . $user->username . "]";
            $message->message = "";
        } 
        return $message;          
?>