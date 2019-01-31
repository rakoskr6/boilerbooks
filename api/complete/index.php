<?php
/*** This api updates information in the database that is needed to complete a purchase,
/*** uploads the receipt to the server, and sends an email to the treasurer. **/

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
//header('Location: /complete/');
?>

    <?php
include '../../dbinfo.php';
$returnStat = "200";

/*** Add completion info to database ***/
// At some point consider accepting all info using JSON formating
// define variables and set to empty values
$cost = $comments = $receipt = $purchasedate = $committee = $item = $purchaseID = $usr = "";
$committee = $_SESSION['committeec'];
$item = $_SESSION['itemc'];
$purchaseID = $_SESSION['purchaseIDc'];
$usr = $_SESSION['user']; // eventually make this a passed parameter (maybe)

if ($purchaseID == '') {
    header("Location: index.php");
} else {
    $sqler = $uploaderr = '';
    $comments = test_input($_POST["comments"]);
    $cost = test_input($_POST["cost"]);
    $purchasedate = test_input($_POST["purchasedate"]);
    $purchasedatetemp = $purchasedate;

    $receipt = "money.krakos.net/";

    $target_dir = "../receipts/";
    $target_dir_save = "/receipts/";
    $uploadOk = 1;
    $FileType = pathinfo($target_dir . basename($_FILES["fileToUpload"]["name"]), PATHINFO_EXTENSION);
    $target_file = $target_dir . $committee . "_" . $item . "_" . $purchaseID . "." . $FileType;
    $target_file_save = $target_dir_save . $committee . "_" . $item . "_" . $purchaseID . "." . $FileType;
    echo $target_file_save;

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploaderr = $uploaderr . "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploaderr = $uploaderr . "Sorry, your reimbursement cert is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($FileType != "pdf" and $FileType != "PDF" and $FileType != "jpg" and $FileType != "jpeg" and $FileType != "JPG" and $FileType != "JPEG") {
        $uploaderr = $uploaderr . "Sorry, only PDFs and JPEGs are allowed";
        echo $FileType;
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $uploaderr = $uploaderr . "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            $receipt = str_replace(' ', '%20', $target_file_save);
        } else {
            $uploaderr = $uploaderr . "Sorry, there was an error uploading your file.";
        }
    }

    if ($uploadOk != 0) {
        try {
            $cost = test_input(str_replace('$', '', $cost));
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE Purchases SET modifydate = NOW(), purchasedate='$purchasedate', cost='$cost', status='Purchased', comments='$comments',
                receipt='$receipt' WHERE Purchases.purchaseID = '$purchaseID'";

            // use exec() because no results are returned
            $conn->exec($sql);
            echo "New record created successfully";
        } catch (PDOException $e) {
            $sqler = $sql . "<br>" . $e->getMessage();
        }

        $conn = null;

        send_email(
            "purdue.ieee.treasurer@gmail.com",
            "New purchase by $committee",
            "Please visit money.pieee.org at your earliest convenience to begin the reimbursement process for $item."
        );


        $_SESSION['usernamec'] = '';
        $_SESSION['itemc'] = '';
        $_SESSION['reasonc'] = '';
        $_SESSION['vendorc'] = '';
        $_SESSION['committeec'] = '';
        $_SESSION['categoryc'] = '';
        $_SESSION['costc'] = '';
        $_SESSION['statusc'] = '';
        $_SESSION['commentsc'] = '';
        $_SESSION['statusc'] = '';
        $_SESSION['purchaseIDc'] = '';

        if ($uploaderr == '' && $sqler == '') {
            //header('Location: index.php');
            echo "<br>";
            echo $purchasedatetemp;
            echo "<br>";
            echo $purchasedate;
            echo "<br>";
        } else {
            echo $uploaderr . "'<br>'";
            echo "There were errors <br> '";
            echo $sqler . "'<br>";
        }
    }
}

$cost = $comments = $receipt = $purchasedate = $committee = $item = $purchaseID = $usr = "";
?>
