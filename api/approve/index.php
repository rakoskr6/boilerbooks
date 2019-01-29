<?php
/*** This api updates information in the database that is needed for a purchase approval (or denial),
/*** sends an email to the purchaser approver, and clears old session variables ***/

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
header('Location: /approve/');
//echo 'Email is <br>';
//echo $SESSION['email'];
?>

    <?php
include '../../dbinfo.php';
$returnStat = "200";

/*** Add approval info to database ***/
// At some point consider accepting all info using JSON formating
// define variables and set to empty values
$item = $reason = $vendor = $committee = $cost = $comments = $category = $stat = $fundsource = $usr = $purchaseid = "";

$item = test_input($_POST["item"]);
$reason = test_input($_POST["reason"]);
$vendor = test_input($_POST["vendor"]);
$cost = test_input($_POST["cost"]);
$comments = test_input($_POST["comments"]);
$category = test_input($_POST["category"]);
$stat = test_input($_POST["status"]);
$fundsource = test_input($_POST["fundsource"]);
$purchaseid = $_SESSION['currentitem'];
$usr = $_SESSION['user']; // eventually make this a passed parameter (maybe)

try {
    $cost = test_input(str_replace('$', '', $cost));
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE Purchases SET modifydate = NOW(), approvedby='$usr', item='$item', purchasereason='$reason', vendor='$vendor',
        category='$category', cost='$cost', status='$stat', fundsource='$fundsource',
        comments='$comments' WHERE Purchases.purchaseID = '$purchaseid'";
    $conn->exec($sql);
    echo "New record created successfully";
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

$to = $_SESSION['email'];
$subject = "Your request has been $stat";

$message = "<p>Please visit money.pieee.org at your earliest convenience to finish the purchase for $item.</p>" .
    "<p>You always view the most up-to-date stauts of the purchase <a href=https://money.purdueieee.org/purchase.php?purchaseid=" . $purchaseid . "> here</a>.</p>";

$header = "From:ieeeboilerbooks@gmail.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";

if ($sendemail == 1) {
    $retval = mail($to, $subject, $message, $header);

    if ($retval == true) {
        //echo "<br>Message sent successfully...";
    } else {
        //echo "<br>Message could not be sent...";
    }
}

// Reset all session values that are no longer needed on the approval page (prevents reediting purchase)
$_SESSION['currentitem'] = '';
$_SESSION['username'] = '';
$_SESSION['item'] = '';
$_SESSION['reason'] = '';
$_SESSION['vendor'] = '';
$_SESSION['committee'] = '';
$_SESSION['category'] = '';
$_SESSION['cost'] = '';
$_SESSION['status'] = '';
$_SESSION['comments'] = '';
$_SESSION['email'] = '';
$_SESSION['name'] = '';
$item = $reason = $vendor = $committee = $cost = $comments = $category = $stat = $fundsource = $usr = $purchaseid = "";

?>
