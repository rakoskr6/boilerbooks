<?
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function bb_log($message) {
    file_put_contents($_SERVER["DOCUMENT_ROOT"].'/log.log', $message, FILE_APPEND);
}

function send_email($email_address, $subject, $message) {
    if (!$SEND_EMAIL) return;

    bb_log("Sending email to " + $email_address);

    $header = "From:ieeeboilerbooks@gmail.com\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-type: text/html\r\n";

    $success = mail($email_address, $subject, $message, $header);

    if ($success == true) {
        bb_log("Message sent successfully");
    } else {
        bb_log("Message could not be sent");
    }
}

function sanatize($data, $strip_slashes = true) {
    if ($strip_slashes) {
        $data = str_replace('/','-',$data);
    }
    # The original functions still call strip stripslashes. Figure out why
    $data = stripslashes($data);

    $data = str_replace('&','-',$data);
    $data = str_replace('"','',$data);
    $data = trim($data);
    $data = htmlspecialchars($data, $flags=ENT_QUOTES| ENT_HTML401);

    return $data;
}

function db_purchases($user) {
    try {
        $sql = "SELECT DATE_FORMAT(p.purchasedate,'%Y-%m-%d') as date, p.purchaseid, p.item, p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.status,
                p.cost, p.comments, p.username purchasedby
                , (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
                FROM Purchases p
                WHERE p.username = '$user'
                ORDER BY p.purchasedate";

        $query = $GLOBALS['conn']->query($sql);
        $purchases = array();
        foreach ($query as $row) {
            array_push($purchases, $row);
        }

        return $purchases;
    } catch (PDOException $e) {
        bb_log($e->getMessage());
        return array();
    }
}
?>
