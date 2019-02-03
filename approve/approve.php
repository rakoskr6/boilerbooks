<?php
include '../dbinfo.php';

$purchaseID = sanatize($_POST["purchaseID"]);

$user = $_SESSION['user'];

$item = sanatize($_POST["item"]);
$reason = sanatize($_POST["reason"]);
$vendor = sanatize($_POST["vendor"]);
$cost = sanatize($_POST["cost"]);
$cost = sanatize(str_replace('$', '', $cost));
$comments = sanatize($_POST["comments"]);
$category = sanatize($_POST["category"]);
$status = sanatize($_POST["status"]);
$fundsource = sanatize($_POST["fundsource"]);

if (db_approve_purchase($purchaseID, $user, $item, $reason, $vendor, $cost, $comments, $category, $status, $fundsource)) {
    echo "Success!";

    send_email(
        $_SESSION['email'],
        "Your request has been $status",
        "Please visit money.pieee.org at your earliest convenience to finish the purchase for $item
        You always view the most up-to-date stauts of the purchase <a href=https://money.purdueieee.org/purchase.php?purchaseid=\"$purchaseid\">here</a>."
    );
} else {
    echo "There was an issue..";
}
?>
