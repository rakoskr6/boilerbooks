<?php
session_start();
header("Location: index.php");
$title = 'Boiler Books';
$completeactive = "active";
?>


<?php
include '../dbinfo.php';
$stuff = '';
$currentitem = $_GET["currentitem"];
$_SESSION['currentitemc'] = $currentitem;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT username, purchaseID, item, purchasereason, vendor, committee, category, cost, status, comments FROM Purchases WHERE Purchases.purchaseID = '$currentitem'";
    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $_SESSION['usernamec'] = $row['username'];
        $_SESSION['itemc'] = $row['item'];
        $_SESSION['reasonc'] = $row['purchasereason'];
        $_SESSION['vendorc'] = $row['vendor'];
        $_SESSION['committeec'] = $row['committee'];
        $_SESSION['categoryc'] = $row['category'];
        $_SESSION['costc'] = $row['cost'];
        $_SESSION['statusc'] = $row['status'];
        $_SESSION['commentsc'] = $row['comments'];
        $_SESSION['statusc'] = $row['status'];
        $_SESSION['purchaseIDc'] = $row['purchaseID'];
    }

} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>
