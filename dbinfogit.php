<?php
session_start();

$servername = "";
$username = "";
$password = "";
$dbname = "";
$uploadcode = "";

$SEND_EMAIL = false;

include "db_lib.php";

function test_input($data) {
    return sanatize($data);
}

function test_input2($data) {
    return sanatize($data, $strip_slashes=false);
}
?>
