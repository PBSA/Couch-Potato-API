<?php
    // load config-dataproxy.json
    $string = file_get_contents("work/config-dataproxy.json");
    if ($string == false) {
        // deal with error...
    
    }
    echo $string;
    //echo json_encode($config);

?>