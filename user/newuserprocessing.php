<?php
include '../header.php';
include '../dbinfo.php';

// define variables and set to empty values
$first = $last = $email = $address = $city = $state = $zip = $usr = $cert = $password1 = $password2 = $ieeecode = "";

$first = test_input($_POST["first"]);
$last = test_input($_POST["last"]);
$email = test_input($_POST["email"]);
$address = test_input($_POST["address"]);
$city = test_input($_POST["city"]);
$state = test_input($_POST["state"]);
$zip = test_input($_POST["zip"]);
$usr = test_input($_POST["username"]);
$ieeecode = test_input($_POST["ieeecode"]);
$password1 = ($_POST["password1"]);
$password2 = ($_POST["password2"]);

$uploaderr = '';


$target_dir = "certs/";
$target_file = $target_dir . $first . "_" . $last . "_" . $usr . ".pdf";
$uploadOk = 1;

echo "The upload code you entered is: ";
echo $ieeecode . "<br>";
if ($ieeecode != $uploadcode) {
  $uploaderr = $uploaderr . "Improper upload code <br>";
}
else if ($password1 != $password2) {
  $uploaderr = $uploaderr . "Passwords must match <br>";
}
else {
  $FileType = pathinfo($target_dir . basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image

  // Check if file already exists
  if (file_exists($target_file)) {
    $uploaderr = $uploaderr . "Username already in use, please select a different name.<br>";
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 1500000) {
    $uploaderr = $uploaderr . "Please keep the upload under 1.5MB";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if($FileType != "pdf" && $FileType != "PDF") {
    $uploaderr = $uploaderr . "Only PDFs are allowed for the reimbursement cert.<br>";
    $uploadOk = 0;
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 1) {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
          $cert = $target_file;
      } else {
          $uploaderr = $uploaderr . "Sorry, there was an unknown error uploading your file.<br>";
          $uploadOk = 0;
      }
  }

  if ($uploadOk == 1) {
      $password1 = password_hash($password1,PASSWORD_DEFAULT);
      $password2 = password_hash($password1,PASSWORD_DEFAULT);
      try {
          $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
          // set the PDO error mode to exception
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $sql = "INSERT INTO Users (first,last,email,address,city,state,zip,cert,username,password)
              VALUES ('$first', '$last', '$email', '$address', '$city', '$state', '$zip', '$cert', '$usr', '$password1')";


          // use exec() because no results are returned
          $conn->exec($sql);
          $sqlerr = '';
      } catch(PDOException $e) {
          $sqlerr = $sql . "<br>" . $e->getMessage();
          $uploaderr = $uploaderr . "There was an unknown error updating the database; refresh and try again.<br>";
      }

      $conn = null;
  }
}

if ($uploaderr == '' and $sqlerr == '') {
  header("Location: ../index.php");
}
else {
  echo "There was an error. Please view the messages below, go back, and try again <br>";
  //echo $uploaderr . "<br><br>";
  //echo $sqlerr . "<br><br>";
  echo $uploaderr;
  //header("Location: newuser.php");
}

include '../smallfooter.php';
?>
