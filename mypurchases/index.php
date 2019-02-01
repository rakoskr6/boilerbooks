<?php
$title = 'Boiler Books';
$mypurchasesactive = "active";
include '../menu.php';

$purchases = db_purchases($_SESSION['user']);
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
            <?php foreach ($purchases as $row): ?>
            <tr>
                <td><a href=/purchase.php?purchaseid=<?= $row['purchaseid']; ?>><?= $row['purchaseid'] ?></a></td>
                <td><?= $row['date']; ?></td>
                <td><a href='<?= $row['receipt']; ?>'><?= $row['item']; ?></a></td>
                <td><?= $row['purchasereason']; ?></td>
                <td><?= $row['vendor']; ?></td>
                <td><?= $row['committee']; ?></td>
                <td><?= $row['approvedby']; ?></td>
                <td><?= $row['category']; ?></td>
                <td><?= $row['status']; ?></td>
                <td><?= $row['cost']; ?></td>
                <td><?= $row['comments']; ?></td>
            </tr>
            <?php endforeach; ?>
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

<?php include '../smallfooter.php';?>
