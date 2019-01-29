<?php
    $title = 'Boiler Books';
    $mypurchasesactive = "active";
?>

<?php
include '../dbinfo.php';
$items = '';
$items2 = '';
$usr = $_SESSION['user'];
$fiscalyear = $_SESSION['fiscalyear'] ;


// Expenses
if ($committee == '') {
    $committee = test_input($_GET["committee"]);
}


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT p.purchaseid, DATE_FORMAT(p.purchasedate,'%m/%d/%Y') as date, p.item, p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.status,
    p.cost, p.comments
    , (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.username) purchasedby
    , (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
     FROM Purchases p
            INNER JOIN approval a ON a.committee = p.committee
            WHERE p.committee = '$committee'
            AND a.username = '$usr'
            AND p.fiscalyear = '$fiscalyear'
            ";
    echo "<br><br>" . $sql . "<br><br>";

    foreach ($conn->query($sql) as $row) {
        $items.= '<tr> <td>';
        $items .= $row['date'];

        $items .= '</td> <td><a href=/purchase.php?purchaseid=';
        $items .= $row['purchaseid'];
        $items .= '>';
        $items .= $row['purchaseid'];
        $items .= '</a>';

        $items .= '</td> <td><a href=';
        $items .= $row['receipt'];
        $items .= '>';
        $items .= $row['item'];
        $items .= '</a></td> <td>';

        $items .= $row['purchasereason'];
        $items .= '</td> <td>';
        $items .= $row['vendor'];
        $items .= '</td> <td>';
        $items .= $row['purchasedby'];
        $items .= '</td> <td>';
        $items .= $row['approvedby'];
        $items .= '</td> <td>';
        $items .= $row['category'];
        $items .= '</td> <td>';
        $items .= $row['status'];
        $items .= '</td> <td>$';
        $items .= $row['cost'];
        $items .= '</td> <td>';
        $items .= $row['comments'];


        $items .= '</td></tr>';
    }

    $_SESSION['commiteepurchases'] = $items;
    $_SESSION['committee'] = $committee;
    //echo $items;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Income
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT *, DATE_FORMAT(updated,'%m/%d/%Y') as date
     FROM Income I
            INNER JOIN approval a ON a.committee = I.committee
            WHERE I.committee = '$committee'
            AND a.username = '$usr'
            AND I.fiscalyear = '$fiscalyear'
            ORDER BY I.updated";

    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $items2.= '<tr> <td>';
        $items2 .= $row['date'];
        $items2 .= '</td> <td>';
        $items2 .= $row['source'];
        $items2 .= '</td> <td>';
        $items2 .= $row['type'];
        $items2 .= '</td> <td>';
        $items2 .= $row['amount'];
        $items2 .= '</td> <td>';
        $items2 .= $row['item'];
        $items2 .= '</td> <td>';
        $items2 .= $row['status'];
        $items2 .= '</td> <td>';
        $items2 .= $row['comments'];


        $items2 .= '</td></tr>';
    }

    $_SESSION['commiteeincome'] = $items2;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Expenses Summary
$items = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT B.category, SUM(CASE WHEN (P.status in ('Purchased','Processing Reimbursement','Reimbursed', 'Approved', NULL) AND (P.committee = '$committee') AND (P.fiscalyear = '$fiscalyear')) THEN P.cost ELSE 0 END) AS 'Spent'
        ,B.amount AS 'Budget' FROM Budget B
                LEFT JOIN Purchases P ON B.category = P.category
                INNER JOIN approval a ON a.committee = P.committee OR a.committee = B.committee
                WHERE B.committee = '$committee'
                AND B.year = '$fiscalyear'
                AND a.username = '$usr'
                GROUP BY B.category
                ";

    foreach ($conn->query($sql) as $row) {
        $items .= '<tr> <td>';
        $items .= $row['category'];
        $items .= '</td> <td>';
        $items .= $row['Spent'];
        $items .= '</td> <td>';
        $items .= $row['Budget'];
        $items .= '</td></tr>';
    }

    $_SESSION['commiteepurchasessummary'] = $items;
    $_SESSION['committee'] = $committee;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Total Budget
$items = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT SUM(Budget.amount) AS 'Budget' FROM Budget
    WHERE Budget.committee = '$committee'
    AND Budget.year = '$fiscalyear'";

    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $items .= $row['Budget'];
    }

    $_SESSION['totalbudget'] = $items;
    $_SESSION['committee'] = $committee;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Total expenses
$items = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT SUM(Purchases.cost) AS 'Spent' FROM Purchases
    WHERE Purchases.committee = '$committee' AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed', 'Approved', NULL)
    AND Purchases.fiscalyear = '$fiscalyear'";


    foreach ($conn->query($sql) as $row) {
        $items .= $row['Spent'];
    }

    $_SESSION['spent'] = $items;
    $_SESSION['committee'] = $committee;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

// Total income
$items = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT SUM(amount) AS income FROM Income
    WHERE type in ('BOSO', 'Cash', 'SOGA') AND committee = '$committee'
    AND fiscalyear = '$fiscalyear'";

    foreach ($conn->query($sql) as $row) {
        $items .= $row['income'];
    }

    $_SESSION['incometotal'] = $items;
    $_SESSION['committee'] = $committee;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Total expenses
$items = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT SUM(Purchases.cost) AS 'Spent' FROM Purchases
    WHERE Purchases.committee = '$committee' AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed','Approved',NULL)
    ";

    foreach ($conn->query($sql) as $row) {
        $items .= $row['Spent'];
    }

    $_SESSION['spentall'] = $items;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


// Total income
$items = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // anyone with approval status in a committee for any amount can view the entire committee
    $sql = "SELECT SUM(amount) AS income FROM Income
    WHERE type in ('BOSO', 'Cash', 'SOGA') AND committee = '$committee'
    ";

    foreach ($conn->query($sql) as $row) {
        $items .= $row['income'];
    }

    $_SESSION['incometotalall'] = $items;

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

$_SESSION['left'] = $_SESSION['incometotalall'] - $_SESSION['spentall'];

header("Location: index.php");

?>
