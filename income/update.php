<?php
include '../dbinfo.php';

$user = $_SESSION['user'];
$incomeID = sanatize($_GET['incomeID']);
$status = sanatize($_GET['status']);

if (db_update_income($user, $incomeID, $status)) {
    echo "Success!";

    //TODO: This is a weird email, who why only pres/treas get income email?
    send_email(
        $_SESSION['email'],
        "Your purchased item is now $stat",

        "$item for $committee is now $stat.
        Feel free to visit money.pieee.org or contact the IEEE treasurer for more information."
    );
} else {
    echo "There was an issue..";
}
?>
