<?php
session_start();
setcookie(session_name(), '', 100);
session_unset();
$_SESSION = array();
session_destroy();

header("Location: ../index.php");
?>
