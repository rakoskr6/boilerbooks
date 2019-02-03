<?php
$title = 'Boiler Books';
$incomeeactive = "active";
include '../menu.php';

//TODO: THIS NEEDS USER VERIFICATION!!!!

$items = db_income($committee, $fiscalyear, $_SESSION['user']);
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
            <?php foreach ($items as $row): ?>
            <tr>
                <td><?= $row['source']; ?></td>
                <td><?= $row['date']; ?></td>
                <td><?= $row['type']; ?></td>
                <td><?= $row['committee']; ?></td>
                <td><?= $row['amount']; ?></td>
                <td><?= $row['item']; ?></td>
                <td><?= $row['status']; ?></td>
                <?php if (strcmp($row['status'], 'Expected')) { ?>
                    <td><a href='update.php?incomeID=<?= $row['incomeid']; ?>&status=Expected'>Mark Expected</a></td>
                <?php } else { ?>
                    <td><a href='update.php?incomeID=<?= $row['incomeid']; ?>&status=Received'>Mark Received</a></td>
                <?php } ?>
            <?php endforeach; ?>
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
