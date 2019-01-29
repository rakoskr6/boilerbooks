<?php
    $title = 'Boiler Books';
    include '../header.php';
?>

<?php
include '../dbinfo.php';

// define variables and set to empty values
$password1 = $password2 = "";

$password1 = ($_POST["password1"]); // consider if this is safe without using test_input
$password2 = ($_POST["password2"]);
$usr = test_input($_POST['usrnid']);
$rstlink = test_input($_POST['rstlinkid']);
echo $usr . "<br>";
echo $rstlink . "<br>";



if (!($password1 === $password2)) {
    $head = "Location: newpasswordreset.php?match=0&usrn=" . $usr . "&rstlink=" . $rstlink;
    header($head);
} else {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT passwordreset, resettime FROM Users WHERE Users.username = '$usr'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll();
        foreach ($results as $pswd) {
            $dbpsw = $pswd['passwordreset'];
            $dbtime = $pswd['resettime'];
        }
        $now = date('Y-m-d H:i:s');

        $datetime1 = new DateTime($dbtime);
        $datetime2 = new DateTime($now);
        $interval = $datetime1->diff($datetime2);
        echo $interval->format('%i minutes');
        $timediff = $interval->format('%i');


        echo "<br>Time from db: " . $dbtime . "<br>";
        echo "Time from PHP: " . $now . "<br>";
        echo $timediff;
        //$dbtime = $dbtime + 5;
        //echo "Time added to db: " . $dbtime . "<br>";

        if (password_verify($rstlink,$dbpsw) && ($timediff<=120)) {
            $password1 = password_hash($password1,PASSWORD_DEFAULT);

            $sql = "UPDATE Users SET modifydate = NOW(), password='$password1', passwordreset='' WHERE username='$usr'";

            // use exec() because no results are returned
            $conn->exec($sql);
            $conn = null;
            echo "<br>success";

            header("Location: ../loggedin.php");
        } else {
            header("Location: ../user/forgotpassword.php");
        }

    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

$conn = null;
?>
