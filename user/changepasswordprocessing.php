<?php
    $title = 'Boiler Books';
    include '../menu.php';
?>

<?php
include '../dbinfo.php';

// define variables and set to empty values
$oldpassword = $password1 = $password2 = "";

$oldpassword = ($_POST["oldpassword"]);
$password1 = ($_POST["password1"]);
$password2 = ($_POST["password2"]);
$usr = $_SESSION['user'];

if ($password1 != $password2) {
    echo "Passwords must match";
} else {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT password FROM Users WHERE Users.username = '$usr'");
        $stmt->execute();

        $results = $stmt->fetchAll();
        foreach ($results as $pswd) {
            $dbpsw = $pswd['password'];
        }


        if (password_verify($oldpassword,$dbpsw)) {
            $password1 = password_hash($password1,PASSWORD_DEFAULT);
            $password2 = password_hash($password1,PASSWORD_DEFAULT);

            $sql = "UPDATE Users SET modifydate = NOW(), password='$password1' WHERE username='$usr'";

            // use exec() because no results are returned
            $conn->exec($sql);
            $conn = null;

            header("Location: ../loggedin.php");
        } else {
            header("Location: changepassword.php");
        }

    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

$conn = null;
?>
