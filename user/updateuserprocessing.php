<?php
include '../menu.php';
include '../dbinfo.php';

// define variables and set to empty values
$first = $last = $email = $address = $city = $state = $zip = $usr = $cert = $password1 = $password2 = "";

$first = test_input($_POST["first"]);
$last = test_input($_POST["last"]);
$email = test_input($_POST["email"]);
$address = test_input($_POST["address"]);
$city = test_input($_POST["city"]);
$state = test_input($_POST["state"]);
$zip = test_input($_POST["zip"]);
//$usr = test_input($_POST["username"]);
$usr = $_SESSION['user'];
$curusr = $_SESSION['user'];
$uploaderr = '';

if ($_FILES["fileToUpload"]["name"] != '') {
    $target_dir = "certs/";
    $target_file = $target_dir . $first . "_" . $last . "_" . $usr . ".pdf";
    $uploadOk = 1;

    $FileType = pathinfo($target_dir . basename($_FILES["fileToUpload"]["name"]), PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image

    // Check if file already exists
    //if (file_exists($target_file)) {
    //  $uploaderr = $uploaderr . "Sorry, file already exists.";
    //  $uploadOk = 0;
    //}

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploaderr = $uploaderr . "Sorry, your reimbursement cert is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($FileType != "pdf") {
        $uploaderr = $uploaderr . "Sorry, only PDFs are allowed for the reimbursement cert.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $uploaderr = $uploaderr . "Sorry, your file was not uploaded.";

        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            $cert = $target_file;
        } else {
            $uploaderr = $uploaderr . "Sorry, there was an error uploading your file.";
        }
    }
} else {
    //keep same cert
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT cert FROM Users U
        WHERE U.username = '$curusr'";

        foreach ($conn->query($sql) as $row) {
            $cert = $row['cert'];

        }
    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE Users SET modifydate = NOW(), first='$first', last='$last', email = '$email', address = '$address',
    city='$city', state = '$state', zip='$zip', cert='$cert', username='$usr' WHERE username='$curusr'";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // execute the query
    $stmt->execute();
    $_SESSION['user'] = $usr;
} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

if ($uploaderr == '' and $sqlerr == '') {
    echo "<div class='container'>";
    echo "<h3>Your updated information:</h3>";
    echo "<br> <p>";
    echo $first . "<br>";
    echo $last . "<br>";
    echo $email . "<br>";
    echo $address . "<br>";
    echo $city . "<br>";
    echo $state . "<br>";
    echo $zip . "<br>";
    echo $cert . "<br>";
    echo "usr: " . $usr . "<br>";
    echo "curusr: " . $curusr . "</p>";
    echo "</div>";
    //header("Location: ../loggedin.php");
} else {
    echo "There was an error. View the messages below and try again";
    echo $uploaderr;
    echo $sqlerr;
    //header("Location: newuser.php");
}
