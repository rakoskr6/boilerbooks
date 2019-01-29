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

        $items = rtrim($items,', ');
    } catch(PDOException $e) {
        $returnStat = "Error";
    }

    $conn = null;
    //echo '<br>The users are:<br>' . $items . '<br>';

    if ($items != '') {
        /*** Send email ***/
         $to = $email;
         $subject = "Boiler Books Username";

         $user = $_SESSION['user'];
         $message = "<p>Hello! A remainder of your username was requested. The following username(s) are associated with this email:<br>" . $items . ". <br><br>Please visit <a href='https://" . $_SERVER[HTTP_HOST] . "'>https://" . $_SERVER[HTTP_HOST] . "</a> to login with your username. You can also request a password reset on the Boiler Books homepage.<br><br>Thanks,<br>Boiler Books Team";

         $header = "From:ieeeboilerbooks@gmail.com \r\n";
         $header .= "MIME-Version: 1.0\r\n";
         $header .= "Content-type: text/html\r\n";

        $retval = mail ($to,$subject,$message,$header);

        if ($retval == true) {
            //echo "Message sent successfully...";
        } else {
            //echo "Message could not be sent...";
        }
        $header = 'Location: /user/forgotusername.php?found=1&email=' . $email;
        header($header);
    } else {
        $header = 'Location: /user/forgotusername.php?found=0&email=' . $email;
        header($header);
    }
?>
