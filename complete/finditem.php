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
        $_SESSION['itemc'] =  $row['item'];
        $_SESSION['reasonc'] =  $row['purchasereason'];
        $_SESSION['vendorc'] = $row['vendor'];
        $_SESSION['committeec'] = $row['committee'];
        $_SESSION['categoryc'] = $row['category'];
        $_SESSION['costc'] = $row['cost'];
        $_SESSION['statusc']= $row['status'];
        $_SESSION['commentsc']= $row['comments'];
        $_SESSION['statusc']= $row['status'];
        $_SESSION['purchaseIDc']= $row['purchaseID'];
    }

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

$_SESSION['aerialactive'] = '';
$_SESSION['computersocietyactive'] = '';
$_SESSION['embsactive'] = '';
$_SESSION['learningactive'] = '';
$_SESSION['racingactive'] = '';
$_SESSION['rovactive'] = '';
$_SESSION['rocketactive'] = '';


if ($_SESSION['committee'] == 'Aerial Robotics') {
    $_SESSION['aerialactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'Computer Society') {
    $_SESSION['computersocietyactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'EMBS') {
    $_SESSION['embsactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'Learning') {
    $_SESSION['learningactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'Racing') {
    $_SESSION['racingactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'ROV') {
    $_SESSION['rovactive'] = 'selected';
}
elseif ($_SESSION['committee'] == 'Rocket') {
    $_SESSION['rocketactive'] = 'selected';
}
?>
