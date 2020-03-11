<?php
<<<<<<< HEAD
    header('Access-Control-Allow-Origin: *');
    define('DIR_VENDOR', __DIR__.'/vendor/');
    if (file_exists(DIR_VENDOR . 'autoload.php')) {
       require_once(DIR_VENDOR . 'autoload.php');
    }
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $db_host = getenv('DB_HOST');
    $db_user = getenv('DB_USER');
    $db_pass = getenv('DB_PASS');
    $db_name = getenv('DB_NAME');

    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die("could not connect DB");
?>
=======
    header('Access-Control-Allow-Origin: *');  
    //$con = mysqli_connect("127.0.0.1", "root", "scoobydoo", "couch_potato") or die("could not connect DB"); 
    $con = mysqli_connect("35.182.190.80", "paul", "scoobydoo", "couch_potato") or die("could not connect DB"); 
?>
>>>>>>> c763ad889e3c0a3a649af505277dba58c66d67b8
