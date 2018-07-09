<?php
session_start();
include "../dbinfo.php";

// Deactivate API keys
$usr = $_SESSION['user'];
try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "UPDATE Users SET apikey = NULL, modifydate=NOW() WHERE username = '$usr'";
	echo $sql . "<br>";
	$conn->exec($sql);
	$_SESSION['apikey'] = $randNum;
}
catch(PDOException $e) {
	echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


setcookie(session_name(), '', 100);
session_unset();
$_SESSION = array();
session_destroy();

header("Location: ../index.php");


?>
