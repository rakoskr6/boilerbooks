<?php
// a (scetchy) solution for master to change users to any user
session_start();
include 'dbinfo.php';
include 'menu.php';

// define variables and set to empty values
$usr = "";

$usr = test_input($_GET["usr"]);

if (($_SESSION['user']) == 'master') {
    $_SESSION['user'] = $usr;
}

header("Location: /loggedin.php");
