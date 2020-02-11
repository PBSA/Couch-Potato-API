<?php
    // load config-dataproxy.json

    $config = array();

    $string = file_get_contents("work/config-dataproxy.json");
    if ($string == false) {
        // deal with error...
    
    }
    $config = json_decode($string);
    //echo json_encode($config);

?>