<?php
    // load config-dataproxy.yaml
    
    $data=array();
    // read in file as is, don't bother converting from YAML
    $myfile = fopen("work/config-dataproxy.yaml", "r") or die("Unable to open file!");
    echo(fread($myfile,filesize("work/config-dataproxy.yaml")));
    fclose($myfile);
?>