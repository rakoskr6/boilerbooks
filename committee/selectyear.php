<?php
session_start();
$title = 'Boiler Books';
?>

<?php
include '../dbinfo.php';

$_SESSION['fiscalyear'] = test_input($_GET["fiscalyear"]);
$committee = $_SESSION["committee"];

header("Location: selectcommittee.php?committee=" . $committee);
?>
