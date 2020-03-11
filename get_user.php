<?php
    include "db.php"; 
    $username = $_GET['username'];
      
    $data=array(); 
    $q = $con->query("SELECT * FROM users 
                    WHERE username = '$username'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>