<?php
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: ../index.php");
        die();
    }
    header("Location: index.php");
?>


<?php
    include '../dbinfo.php';
    $stuff = '';
    $currentitem = test_input($_GET["currentitem"]);
    $_SESSION['currentitem'] = $currentitem;


    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT P.username, CONCAT(U.first, ' ', U.last) name, U.email, item, purchasereason, vendor, committee, category, cost, status, comments FROM Purchases P
                INNER JOIN Users U ON U.username = P.username
                WHERE P.purchaseID = '$currentitem'";

        //$stmt->execute();

        foreach ($conn->query($sql) as $row) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['item'] =  $row['item'];
            $_SESSION['reason'] =  $row['purchasereason'];
            $_SESSION['vendor'] = $row['vendor'];
            $_SESSION['committee'] = $row['committee'];
            $_SESSION['category'] = $row['category'];
            $_SESSION['cost'] = $row['cost'];
            $_SESSION['status'] = $row['status'];
            $_SESSION['comments'] = $row['comments'];
            $_SESSION['email'] = $row['email'];
        }

    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;

    $_SESSION['aerialactive'] = '';
    $_SESSION['computersocietyactive'] = '';
    $_SESSION['embsactive'] = '';
    $_SESSION['learningactive'] = '';
    $_SESSION['racingactive'] = '';
    $_SESSION['rovactive'] = '';
    $_SESSION['rocketactive'] = '';


    if ($_SESSION['committee'] == 'Aerial Robotics') {
        $_SESSION['aerialactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'Computer Society') {
        $_SESSION['computersocietyactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'EMBS') {
        $_SESSION['embsactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'Learning') {
        $_SESSION['learningactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'Racing') {
        $_SESSION['racingactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'ROV') {
        $_SESSION['rovactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'Rocket') {
        $_SESSION['rocketactive'] = 'selected';
    }
    elseif ($_SESSION['committee'] == 'General IEEE') {
        $_SESSION['generalieee'] = 'selected';
    }


    // Figure out how much money committee has
    $committee = $_SESSION['committee'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT 
                (SELECT SUM(amount) AS income FROM Income
                 WHERE type in ('BOSO', 'Cash', 'SOGA') AND committee = '$committee')
                -
                (SELECT SUM(Purchases.cost) AS 'Spent' FROM Purchases
                 WHERE Purchases.committee = '$committee' AND Purchases.status in ('Purchased','Processing Reimbursement','Reimbursed','Approved',NULL)) AS Balance";

        //$stmt->execute();

        foreach ($conn->query($sql) as $row) {
            $_SESSION['balance'] = $row['Balance'];
        }

    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;
?>
