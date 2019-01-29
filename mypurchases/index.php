<?php
    $title = 'Boiler Books';
    $mypurchasesactive = "active";
    include '../menu.php';
?>

<?php
include '../dbinfo.php';
$items = '';
$usr = $_SESSION['user'];


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT DATE_FORMAT(p.purchasedate,'%Y-%m-%d') as date, p.purchaseid, p.item, p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.status,
    p.cost, p.comments, p.username purchasedby
    , (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
    FROM Purchases p
            WHERE p.username = '$usr'
            ORDER BY p.purchasedate";

    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $items .= '<tr> <td><a href=/purchase.php?purchaseid=';
        $items .= $row['purchaseid'];
        $items .= '>';
        $items .= $row['purchaseid'];
        $items .= '</a> <td>';

        $items .= $row['date'];
        $items .= '</td> <td><a href=';
        $items .= $row['receipt'];
        $items .= '>';
        $items .= $row['item'];
        $items .= '</a></td> <td>';
        $items .= $row['purchasereason'];
        $items .= '</td> <td>';
        $items .= $row['vendor'];
        $items .= '</td> <td>';
        $items .= $row['committee'];
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

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>

<div class="container">
    <table id="mypurchasestable" class="display">
        <thead>
            <tr>
                <th>Purchase ID</th>
                <th>Purchase Date</th>
                <th>Item</th>
                <th>Reason</th>
                <th>Vendor</th>
                <th>Committee</th>
                <th>Reviewed By</th>
                <th>Category</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $items ?>
        </tbody>
    </table>
    <script>
    $(document).ready(function() {
        $('#mypurchasestable').DataTable({
            "order": [[0, "desc"]]
        });
    });
    </script>
</div>

<?php include '../smallfooter.php'; ?>
