<?php
include '../dbinfo.php';


$user = $_SESSION['user'];
$status = sanatize($_GET['status']);
$purchaseID = sanatize($_GET['purchaseID']);

if (strcmp($status, "processing") == 0) {
    $stat = 'Processing Reimbursement';

    $message = "$item for $committee is now $stat. Check money.pieee.org or contact the treasurer.";
}
else if (strcmp($status, "reimbursed") == 0) {
    $stat = 'Reimbursed';


    $message = "$item for $committee is now $stat. Please stop by EE 14 to pick up your check.";
} else {
    echo "Status must be set";
}


if (db_update_purchase_status($user, $purchaseID, $stat)) {
    echo "Success!";

    $email = db_purchaser_email($purchaseID);

    send_email(
        $email,
        "Your purchased item is now $stat",
        $message
    );
} else {
    echo "There was an issue..";
}
?>
