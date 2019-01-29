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

// define variables and set to empty values
$item = $reason = $vendor = $committee = $cost = $comments = $category = "";

$committee = test_input($_POST["committee"]);
$source = test_input($_POST["source"]);
$amount = test_input($_POST["amount"]);
$item = test_input($_POST["item"]);
$category = test_input($_POST["category"]);
$status = test_input($_POST["status"]);
$comments = test_input($_POST["comments"]);

$usr = $_SESSION['user'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO Income (updated, committee, source, amount, item, type, status, comments, addedby)
            VALUES (NOW(), '$committee', '$source', '$amount', '$item', '$category', '$status', '$comments', '$usr')";

    //$sql = "INSERT INTO Purchases (item)
    //VALUES ('$item')";

    // use exec() because no results are returned
    $conn->exec($sql);
    echo "Inserted";

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
header('Location: index.php');
?>
