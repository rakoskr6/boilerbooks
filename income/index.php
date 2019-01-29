<?php
$title = 'Boiler Books';
$incomeeactive = "active";
include '../menu.php';
include '../dbinfo.php';

$committee = test_input($_GET["committee"]);
if ($committee == '') {
    $committee = "%";
    $committeeDisplay = "all committees";
} else {
    $committeeDisplay = $committee;
}

$fiscalyear = test_input($_GET["fiscalyear"]);
if ($fiscalyear == '') {
    $fiscalyear = '2018-2019';
}

$items = '';
$usr = $_SESSION['user'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT DATE_FORMAT(i.updated,'%Y-%m-%d') as date,
        i.source,
        i.type,
        i.committee,
        i.amount,
        i.item,
        i.incomeid,
        i.status

        FROM Income i
        ORDER BY i.updated DESC";

    foreach ($conn->query($sql) as $row) {
        $items .= '<tr> <td>';
        $items .= $row['source'];
        $items .= '</td> <td>';
        $items .= $row['date'];
        $items .= '</td> <td>';
        $items .= $row['type'];
        $items .= '</td> <td>';
        $items .= $row['committee'];
        $items .= '</td> <td>';
        $items .= $row['amount'];
        $items .= '</td> <td>';
        $items .= $row['item'];
        $items .= '</td> <td>';
        $items .= $row['status'];
        $items .= '</td> <td>';
        $items .= "<a href='update.php?incomeid=";
        $items .= $row['incomeid'];
        $items .= "&status=";

        if (strcmp($row['status'], 'Expected') == 0) {
            $items .= "Received'>";
            $items .= 'Mark Received';
        } else {
            $items .= "Expected'>";
            $items .= 'Mark Expected';
        }
        $items .= '</a></td>';
        $items .= '</tr>';

    }

} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>


<div class="container">
    <div class="text-center">
        <h3>Income</h3>
    </div>
</div>


<br> <br>

<div class="container">
    <table id="incometable" class="display">
        <thead>
            <tr>
                <th>Source</th>
                <th>Date Entered</th>
                <th>Type</th>
                <th>Committee</th>
                <th>Amount</th>
                <th>Item</th>
                <th>Status</th>
                <th>Change Status</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $items ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function() {
            $('#incometable').DataTable({
                "order": [[ 1, "desc" ]]
            });
            stateSave: true
        });
    </script>
</div>


<?php include '../smallfooter.php';?>
