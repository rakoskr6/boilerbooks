<?php
include '../dbinfo.php';

$user = $_SESSION['user'];

$committee = sanatize($_POST["committee"]);
$source = sanatize($_POST["source"]);
$amount = sanatize($_POST["amount"]);
$item = sanatize($_POST["item"]);
$category = sanatize($_POST["category"]);
$status = sanatize($_POST["status"]);
$comments = sanatize($_POST["comments"]);

if (db_receive_donation($user, $committee, $source, $amount, $item, $category, $status, $comments)) {
    echo "Success!";

} else {
    echo "There was an issue..";
}
?>
