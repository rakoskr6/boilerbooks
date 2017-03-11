<?php
    require("vendor/autoload.php");
    $swagger = \Swagger\scan('index.php');
    header('Content-Type: application/json');
    echo $swagger;
?>
