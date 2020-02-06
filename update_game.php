<?php header('Access-Control-Allow-Origin: *'); ?>
<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    if($data['action'] == "insert"){
        $event = $data['event']; 
        $user = $data['user']; 
        $hometeam = $data['hometeam']; 
        $awayteam = $data['awayteam']; 
        $starttime = $data['starttime']; 
    
        $q = mysqli_query($con, "INSERT INTO `games` ( `user`, `event`, `hometeam`,`awayteam`,`starttime` ) 
                                    VALUES ('$user', '$event', '$hometeam', '$awayteam', '$starttime')"); 
        // get the last id
        $x = $con->query("SELECT MAX(id) as `id` FROM `games`");
        $row=mysqli_fetch_object($x);

        if($q){
           echo json_encode($row);
        }
        else{
            $message['status'] = "error"; 
        }
        //echo json_encode($message); 
    }
    echo mysqli_error($con); 
?>
