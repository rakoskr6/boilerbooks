<?php
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db = $conn;


function bb_log($message) {
    file_put_contents($_SERVER["DOCUMENT_ROOT"].'/log.log', $message, FILE_APPEND);
}

function send_email($email_address, $subject, $message) {
    if (!$GLOBALS['SEND_EMAIL']) return;

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

function get_param($param, $default="") {
    if (isset($_GET[$param])) {
        return sanatize($_GET[$param]);
    }

    return $default;
}

function db_execute($sql) {
    try {
        $GLOBALS['db']->exec($sql);
        return true;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return false;
}

function db_fetchOne($sql) {
    try {
        return $GLOBALS['db']->query($sql)->fetch();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return NULL;
}

function db_fetchAll($sql) {
    try {
        return $GLOBALS['db']->query($sql)->fetchAll();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return NULL;
}

function db_purchases($user) {
    $sql = "SELECT DATE_FORMAT(p.purchasedate,'%Y-%m-%d') as date, p.purchaseid, p.item, p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.status,
            p.cost, p.comments, p.username purchasedby,
            (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
            FROM Purchases p
            WHERE p.username = '$user'
            ORDER BY p.purchasedate";

    return db_fetchAll($sql);
}

function db_committee_income($committee, $optional) {
    $sql = "SELECT *,
            DATE_FORMAT(updated, '%Y-%m-%d') as date
            FROM Income
            INNER JOIN approval ON approval.committee = Income.committee
            WHERE Income.committee='$committee'";

    if (isset($optional['fiscalyear'])) {
        $sql .= " AND Income.fiscalyear='$optional[fiscalyear]'";
    }

    if (isset($optional['user'])) {
        $sql .= " AND approval.username='$optional[user]'";
    }

    $sql .= " ORDER BY Income.updated";

    return db_fetchAll($sql);
}

function db_committee_expenses($committee, $optional) {
    $sql = "SELECT
            DATE_FORMAT(Purchases.purchasedate, '%Y-%m-%d') as date,
            Purchases.purchaseid, Purchases.purchasedate, Purchases.item,
            Purchases.purchasereason, Purchases.vendor, Purchases.committee, Purchases.category,
            Purchases.receipt, Purchases.status, Purchases.cost, Purchases.comments,
            (SELECT CONCAT(Users.first, ' ', Users.last) FROM Users WHERE Users.username = Purchases.username) as purchasedby,
            (SELECT CONCAT(Users.first, ' ', Users.last) FROM Users WHERE Users.username = Purchases.approvedby) as approvedby
            FROM Purchases
            INNER JOIN approval ON approval.committee = Purchases.committee
            WHERE Purchases.committee='$committee'";

    if (isset($optional['fiscalyear'])) {
        $sql .= " AND Purchases.fiscalyear='$optional[fiscalyear]'";
    }

    if (isset($optional['user'])) {
        $sql .= " AND approval.username='$optional[user]'";
    }

    return db_fetchAll($sql);
}

function db_committee_expenses_summary($committee, $fiscalyear, $user) {
    $sql = "SELECT Budget.category, SUM(CASE WHEN (Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed', 'Approved', NULL)
            AND (Purchases.committee = '$committee') AND (Purchases.fiscalyear = '$fiscalyear')) THEN Purchases.cost ELSE 0 END) AS 'spent',
            Budget.amount AS 'budget' FROM Budget
            LEFT JOIN Purchases ON Budget.category = Purchases.category
            INNER JOIN approval ON approval.committee = Purchases.committee OR approval.committee = Budget.committee
            WHERE Budget.committee = '$committee'
            AND Budget.year='$fiscalyear'
            AND approval.username='$user'
            GROUP BY Budget.category";

    return db_fetchAll($sql);
}

function db_committee_total_expenses($committee) {
    $sql = "SELECT SUM(Purchases.cost) FROM Purchases
            WHERE Purchases.committee='$committee'
            AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed','Approved',NULL)";

    return db_fetchOne($sql)[0];
}

function db_committee_total_expenses_year($committee, $year) {
    $sql = "SELECT SUM(Purchases.cost) FROM Purchases
            WHERE Purchases.committee='$committee'
            AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed','Approved',NULL)
            AND Purchases.fiscalyear='$year'";

    return db_fetchOne($sql)[0];
}

function db_committee_total_budget($committee) {
    $sql = "SELECT SUM(Budget.amount) FROM Budget
            WHERE Budget.committee='$committee'";

    return db_fetchOne($sql)[0];
}

function db_committee_total_budget_year($committee, $year) {
    $sql = "SELECT SUM(Budget.amount) FROM Budget
            WHERE Budget.committee='$committee'
            AND Budget.year='$year'";

    return db_fetchOne($sql)[0];
}

function db_committee_total_income($committee) {
    $sql = "SELECT SUM(amount) FROM Income
            WHERE type in ('BOSO', 'Cash', 'SOGA')
            AND committee='$committee'";

    return db_fetchOne($sql)[0];
}

function db_committee_total_income_year($committee, $year) {
    $sql = "SELECT SUM(amount) FROM Income
            WHERE type in ('BOSO', 'Cash', 'SOGA')
            AND committee='$committee'
            AND fiscalyear='$year'";

    return db_fetchOne($sql)[0];
}

function db_committee_balance($committee) {
    $sql = "SELECT
            (SELECT SUM(amount) AS income FROM Income
             WHERE type in ('BOSO', 'Cash', 'SOGA') AND committee = '$committee')
            -
            (SELECT SUM(Purchases.cost) AS 'Spent' FROM Purchases
            WHERE Purchases.committee = '$committee' AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed','Approved',NULL)) AS Balance";

    return db_fetchOne($sql)[0];
}

function db_purchase($user, $purchaseID) {
    $sql = "SELECT DATE_FORMAT(p.purchasedate,'%Y-%m-%d') as date, p.modifydate, p.item,
            p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.status,
            p.cost, p.comments, p.fundsource, p.fiscalyear, p.username,
            (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.username) purchasedby,
            (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
            FROM Purchases p
            WHERE p.purchaseID = $purchaseID";

    return db_fetchOne($sql);
}

function db_purchaser_email($purchaseID) {
    $sql = "SELECT Users.email FROM Users
            INNER JOIN Purchases on Users.username = Purchases.username
            WHERE Purchases.purchaseID = $purchaseID";

    return db_fetchOne($sql)[0];
}

function db_treasurer($committee, $fiscalyear, $user) {
    $sql = "SELECT DATE_FORMAT(p.purchasedate,'%Y-%m-%d') as date,
            p.item, p.purchaseID, p.purchasereason,
            p.vendor, p.committee, p.category,
            p.receipt, p.status, p.cost, p.comments,
            p.username, p.fundsource,
            (SELECT CONCAT(Users.first, ' ', Users.last) FROM Users WHERE Users.username = p.username) purchasedby,
            (SELECT CONCAT(Users.first, ' ', Users.last) FROM Users WHERE Users.username = p.approvedby) approvedby
            FROM Purchases p
            WHERE p.status in ('Purchased','Processing Reimbursement', 'Reimbursed')
            AND '$user' in (
            SELECT Users.username FROM Users
            INNER JOIN approval A ON Users.username = A.username
            WHERE (A.role = 'treasurer' OR A.role = 'president'))
            AND p.committee LIKE '$committee' AND p.fiscalyear LIKE '$fiscalyear'
            ORDER BY p.purchasedate DESC";

    return db_fetchAll($sql);
}

function db_user($user_lookup, $user) {
    $sql = "SELECT * FROM Users
            WHERE Users.username = '$user_lookup'
            AND '$user' in (
                SELECT Users.username FROM Users
                INNER JOIN approval A ON Users.username = A.username
                WHERE (A.role = 'treasurer' OR A.role = 'president')
            )";

    return db_fetchOne($sql);
}
function db_income() {
    $sql = "SELECT DATE_FORMAT(i.updated,'%Y-%m-%d') as date,
            i.source, i.type, i.committee, i.amount,
            i.item, i.incomeid, i.status

            FROM Income i
            ORDER BY i.updated DESC";

    return db_fetchAll($sql);
}

function db_approval_purchases($user) {
    $sql = "SELECT DISTINCT p.purchaseID, p.item FROM Purchases p
            INNER JOIN approval on p.committee = approval.committee
            WHERE p.status = 'Requested'
            AND approval.username = '$user'
            AND (approval.category = p.category OR approval.category = '*')
            AND p.cost <= (SELECT MAX(approval.ammount) FROM approval
            WHERE approval.username = '$user'
            AND approval.committee = p.committee)";


    return db_fetchAll($sql);
}

function db_approve_purchase($purchaseID, $user, $item, $reason, $vendor, $cost, $comments, $category, $status, $fundsource) {
    $sql = "UPDATE Purchases SET modifydate = NOW(), approvedby='$user', item='$item', purchasereason='$reason', vendor='$vendor',
            category='$category', cost='$cost', status='$status', fundsource='$fundsource',
            comments='$comments' WHERE Purchases.purchaseID = '$purchaseID'";

    echo $sql;

    return db_execute($sql);
}

function db_receive_donation($user, $committee, $source, $amount, $item, $category, $status, $comments) {
    $sql = "INSERT INTO Income (updated, committee, source, amount, item, type, status, comments, addedby)
        VALUES (NOW(), '$committee', '$source', '$amount', '$item', '$category', '$status', '$comments', '$usr')";


    return db_execute($sql);
}

function db_is_treasurer($user) {
    $sql = "SELECT COUNT(Users.username) FROM Users
            INNER JOIN approval ON Users.username = approval.username
            WHERE (approval.role = 'treasurer' OR approval.role = 'president')
            AND Users.username = '$user'";

    return db_fetchOne($sql)[0];
}

function db_update_income($user, $incomeID, $status) {
    //TODO: This counts if the user is a treasurer of _some_ committee, not in general
    //could be better with committee based approval

    if (db_is_treasurer($user)) {
        $sql = "UPDATE Income SET status='$status' WHERE Income.incomeid = '$incomeID'";
        return db_execute($sql);
    }

    return false;
}

function db_update_purchase_status($user, $purchaseID, $status) {
    if (db_is_treasurer($user)) {
        $sql = "UPDATE Purchases SET modifydate = NOW(), status='$status' WHERE Purchases.purchaseID = '$purchaseID'";
        return db_execute($sql);
    }

    return false;
}
?>
