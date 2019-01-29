<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
?>

<?php //header('Location: /request/newpurchasesubmitted.php '); ?>

<?php
include '../dbinfo.php';
include '../helper/email.php';

$validuser = '';
$usr = $_SESSION['user'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT COUNT(U3.username) AS validuser FROM Users U3
    INNER JOIN approval A ON U3.username = A.username
    WHERE (A.role = 'treasurer' OR A.role = 'president')
    AND U3.username = '$usr'";
    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $validuser .= $row['validuser'];
    }
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

echo "Valid User: " . $validuser . "<br>";
if ($validuser >= 1) {
    // define variables and set to empty values
    $item = $reason = $vendor = $committee = $cost = $comments = $category = "";

    $processing = test_input($_GET["processing"]);
    $reimbursed = test_input($_GET["reimbursed"]);
    $usr = $_SESSION['user'];
    echo $processing;
    echo "<br>";
    echo $reimbursed;
    echo "<br>";

    if ($processing != '-1') {
        $stat = 'Processing Reimbursement';
        $purchaseid = $processing;
    } else if ($reimbursed != '-1') {
        $stat = 'Reimbursed';
        $purchaseid = $reimbursed;

    } else {
        $stat = '';
        echo 'None';
        header('Location: /treasurer/index.php');
        return 1;
    }
    echo $stat;

    try {
        $cost = test_input(str_replace('$', '', $cost));
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE Purchases SET modifydate = NOW(), status='$stat' WHERE Purchases.purchaseID = '$purchaseid'";

        // use exec() because no results are returned
        $conn->exec($sql);
        echo "Updated";
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT U.email, item, committee, status FROM Purchases P
        INNER JOIN Users U ON U.username = P.username
        WHERE P.purchaseID = '$purchaseid'";
        //$stmt->execute();

        error_log("hello");
        foreach ($conn->query($sql) as $row) {
            $email = $row['email'];
            $item = $row['item'];
            $committee = $row['committee'];
            $status = $row['status'];
            error_log($email);
        }

    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;

    $subject = "Your purchased item is now $stat";

    if ($reimbursed != '-1') {
        $message = "<p>$item for $committee is now $stat.</p>
        <p>Please stop by EE 14 to pick up your check.</p>";
    } else {
        $message = "<p>$item for $committee is now $stat.</p>
        <p>Check money.pieee.org or contact the treasurer.</p>";
    }

    send_email($email, $subject, $message);
}

header('Location: /treasurer/index.php');

?>
