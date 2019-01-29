<?php
    function send_email($email_address, $subject, $message) {
        error_log("sending email to " +  $email_address);

        $header = "From:ieeeboilerbooks@gmail.com \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";

        $retval = mail ($email_address, $subject, $message, $header);

        if( $retval == true ) {
            //echo "Message sent successfully...";
        } else {
            //echo "Message could not be sent...";
        }
    }
?>
