<?php
/*** This api inserts information into the database that is needed for a purchase request, provides info
/*** to the requestor through a session variable (name, and item), redirects to seperate page,
/*** and sends an email to the potential approver ***/

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
header('Location: /request/submitted.php ');
?>

<?php
include '../../dbinfo.php';
$returnStat = "200";

/*** Add purchase request to database ***/
// At some point consider accepting all info using JSON formating
$item = $reason = $vendor = $committee = $cost = $comments = $category = "";
$item = test_input($_POST["item"]);
$reason = test_input($_POST["reason"]);
$vendor = test_input($_POST["vendor"]);
$committee = test_input($_POST["committee"]);
$cost = test_input($_POST["cost"]);
$comments = test_input($_POST["comments"]);
$category = test_input($_POST["category"]);
$usr = $_SESSION['user']; // eventually make this a passed parameeter (maybe)

try {
    $cost = test_input(str_replace('$', '', $cost));
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO Purchases (username,item,purchasereason,vendor,committee,category,cost,status,comments)
        VALUES ('$usr', '$item', '$reason', '$vendor', '$committee', '$category', '$cost', 'Requested', '$comments');
        ";

    // use exec() because no results are returned
    $conn->exec($sql);
    $sql = "SELECT @@IDENTITY AS PID;";
    foreach ($conn->query($sql) as $row) {
        $currentitemid = $row['PID'];

    }
} catch (PDOException $e) {
    $returnStat = "Failed to add request to database";
}

$conn = null;

/*** Get info of person to approve purchase ***/
$names = '';
$emails = '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT CONCAT(U.first, ' ', U.last) name, U.email, a.committee FROM approval a
            INNER JOIN Users U
            ON U.username = a.username

            WHERE a.committee = (
                SELECT P.committee FROM Purchases P WHERE P.purchaseID = '$currentitemid')
                AND a.ammount >= (
                    SELECT P.cost FROM Purchases P WHERE P.purchaseID = '$currentitemid')
                    AND (a.category = (
                        SELECT P.category FROM Purchases P WHERE P.purchaseID = '$currentitemid')
                        OR a.category = '*')
            ";

    foreach ($conn->query($sql) as $row) {
        $names .= $row['name'];
        $names .= ', ';

        $emails .= $row['email'];
        $emails .= ', ';

        $committee = $row['committee'];
    }

    $names = rtrim($names, ', ');
    $emails = rtrim($emails, ', ');
} catch (PDOException $e) {
    $returnStat = "Failed to get information on potential approvers";
}

$conn = null;

/*** Send emails to people who need to approve purchase ***/
$to = $emails;
$subject = "New Purchase from $committee";

$user = $_SESSION['user'];
$message = "<p>A request to buy $item has been made by $user.
    Please visit <a href='https://money.purdueieee.org/approve/'>money.pieee.org</a> at your earliest convenience to approve or deny the request.</p>
    <p>You always view the most up-to-date stauts of the purchase <a href=https://money.purdueieee.org/purchase.php?purchaseid=" . $currentitemid . "> here</a>.</p>";

$header = "From:ieeeboilerbooks@gmail.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";

if ($sendemail == 1) {
    $retval = mail($to, $subject, $message, $header);

    if ($retval == true) {
        //echo "Message sent successfully...";
    } else {
        //echo "Message could not be sent...";
    }
}

$_SESSION['item'] = $item;
$_SESSION['names'] = $names;

// Clear unused variables
$item = $reason = $vendor = $committee = $cost = $comments = $category = "";

// eventually consider the API to pass back info using JSON
//$jsonArray = compact("item", "names","returnStat");
//echo json_encode($jsonArray);
?>
