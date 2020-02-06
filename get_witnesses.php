<?php

function getWitnesses(){
    $witnesses=array();

    $file = fopen("work/config-dataproxy.yaml", "r") or die("Unable to open file!");
    while(! feof($file)){
    // only interested in lines with 'url:'
    $line =  fgets($file);
    if(strpos($line,'url:')){
            $witnesses[]= str_replace(' - url: ','',$line);
        }
    }
    fclose($file);
    return $witnesses;

}

        
?>