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

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

echo "Valid User: ".$validuser."<br>";
if ($validuser >= 1) {
    // define variables and set to empty values
    $item = $reason = $vendor = $committee = $cost = $comments = $category = "";

    $incomeid = test_input($_GET["incomeid"]);
    $status = test_input($_GET["status"]);

    $usr = $_SESSION['user'];
    echo $processing;
    echo "<br>";


    echo $stat;

    try {
        $cost = test_input(str_replace('$','',$cost));
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "UPDATE Income SET status='$status' WHERE Income.incomeid = '$incomeid'";

        //$sql = "INSERT INTO Purchases (item)
        //VALUES ('$item')";

        // use exec() because no results are returned
        $conn->exec($sql);
        echo "Updated";
    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;


    $to = $email;
    $subject = "Your purchased item is now $stat";

    $message = "<p>$item for $committee is now $stat.
    Feel free to visit money.pieee.org or contact the IEEE treasurer for more information.</p>";

    $header = "From:ieeeboilerbooks@gmail.com \r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";

    if ($sendmail == 1) {
        $retval = mail ($to,$subject,$message,$header);

        if ($retval == true) {
            //echo "Message sent successfully...";
        } else {
            //echo "Message could not be sent...";
        }
    }
}

header('Location: /income/index.php');

?>
