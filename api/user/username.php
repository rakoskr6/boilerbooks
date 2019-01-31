<?php
include '../../dbinfo.php';
include '../../header.php';

$email = $items = "";
$email = test_input($_POST["email"]); //change back to POST
//echo $email . '<br>';
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT U.username AS usrn FROM Users U WHERE email = '$email'";

    foreach ($conn->query($sql) as $row) {
        $items .= $row['usrn'] . ', ';
    }

    $items = rtrim($items, ', ');
} catch (PDOException $e) {
    $returnStat = "Error";
}

$conn = null;
//echo '<br>The users are:<br>' . $items . '<br>';

if ($items != '') {
    /*** Send email ***/
    send_email(
        $email,
        "Boiler Books Username",

        "Hello! A remainder of your username was requested. The following username(s) are associated with this email:<br>
        $items<br><br>Please visit <a href='https://$_SERVER[HTTP_HOST]'>https://$_SERVER[HTTP_HOST]</a> to login with your username.
        You can also request a password reset on the Boiler Books homepage.<br><br>Thanks,<br>Boiler Books Team"
    );

    $header = 'Location: /user/forgotusername.php?found=1&email=' . $email;
    header($header);
} else {
    $header = 'Location: /user/forgotusername.php?found=0&email=' . $email;
    header($header);
}
