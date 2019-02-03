<?php
$title = 'Boiler Books';
$committeeactive = "active";
include '../menu.php';

$committee = sanatize($_GET['committee']);
$fiscalyear = sanatize($_GET['fiscalyear']);


$income = db_committee_income($committee, ["fiscalyear" => $fiscalyear, "user" => $_SESSION['user']]);
$expenses = db_committee_expenses($committee, ["fiscalyear" => $fiscalyear, "user" => $_SESSION['user']]);
$expenses_summary = db_committee_expenses_summary($committee, $fiscalyear, $_SESSION['user']);

$total_expenses = db_committee_total_expenses($committee);
$total_expenses_year = db_committee_total_expenses_year($committee, $fiscalyear);
$total_budget = db_committee_total_budget($committee);
$total_budget_year = db_committee_total_budget_year($committee, $fiscalyear);
$total_income = db_committee_total_income($committee);
$total_income_year = db_committee_total_income_year($committee, $fiscalyear);

$balance = db_committee_balance($committee);
?>

<br>

<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <select id="committee" name="committee" class="form-control" onchange="updateQuery()">
              <?php include '../committees.php';?>
            </select>
        </div>
        <div class="col-sm-6">
            <select id="fiscalyear" name="fiscalyear" class="form-control" onchange="updateQuery()">
                <option value="2018-2019">2018 - 2019</option>
                <option value="2017-2018">2017 - 2018</option>
                <option value="2016-2017">2016 - 2017</option>
                <option value="2015-2016">2015 - 2016</option>
            </select>
        </div>
    </div>
</div>

<br>

<div class="container">
    <div class = "row">
        <div class="col-sm-3">
            <h4 class="text-left" title="Balance = Income - Total (for all years)">
                Balance: <?= $balance; ?>
            </h4>
        </div>
        <div class="col-sm-3">
            <h4 class="text-center" title='Income = Sum of  BOSO, SOGA, & Cash income (for current fiscal year)'>Income: $<?= $total_income_year ?></h4>
        </div>
        <div class="col-sm-3">
            <h4 class="text-center" title='Spent = Sum of reimbursed, processing, purchased, & approved purchases (for current fiscal year)'>Spent: $<?= $total_expenses_year ?></h4>
        </div>
        <div class="col-sm-3">
            <h4 class="text-right" title='Budget = Sum of budget items (for current fiscal year)'>Budget: $<?= $total_budget_year ?></h4>
        </div>
    </div>
</div>

<div class="container">
    <h3 class="text-center">
        <?=  $fiscalyear . ' ' . $committee; ?> Expenses Summary
    </h3>
    <table id="expensestablesummary" class="display">
        <thead>
            <tr>
                <th>Category</th>
                <th>Spent</th>
                <th>Budget</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses_summary as $row): ?>
            <tr>
                <td><?= $row['category']; ?></td>
                <td><?= $row['spent']; ?></td>
                <td><?= $row['budget']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<br> <br> <br>

<div class="container">
    <h3 class="text-center">
        <?= $fiscalyear . ' ' . $committee; ?> Expenses
    </h3>
    <table id="expensestable" class="display">
        <thead>
            <tr>
                <th>Purchase ID</th>
                <th>Purchase Date</th>
                <th>Item</th>
                <th>Reason</th>
                <th>Vendor</th>
                <th>Purchased By</th>
                <th>Reviewed By</th>
                <th>Category</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $row): ?>
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
    </table>
</div>

<br> <br> <br>

<div class="container">
    <h3 class="text-center">
        <?= $fiscalyear . ' ' . $committee ?> Income
    </h3>
    <table id="incometable" class="display">
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Item (if donated)</th>
                <th>Status</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($income as $row): ?>
            <tr>
                <td><?= $row['date']; ?></td>
                <td> <?= $row['source']; ?> </td>
                <td> <?= $row['type']; ?> </td>
                <td> <?= $row['amount']; ?> </td>
                <td> <?= $row['item']; ?> </td>
                <td> <?= $row['status']; ?> </td>
                <td> <?= $row['comments']; ?> </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script>
        function parseQuery(queryString) {
            var query = {};
            var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i].split('=');
                query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
            }
            return query;
        }

        $(document).ready(function() {
            window.queryString = parseQuery(window.location.search);

            if (queryString.committee) {
                document.getElementById('committee').value = queryString.committee;
            }
            if (queryString.fiscalyear) {
                document.getElementById('fiscalyear').value = queryString.fiscalyear;
            }

            $('#expensestablesummary').DataTable({
                "order": [[ 0, "asc" ]]
            });
            $('#expensestable').DataTable({
                "order": [[ 1, "desc" ]]
            });
            $('#incometable').DataTable({
               "order": [[0, "desc"]]
            });
        });
    </script>
</div>

<script>
    function updateQuery() {
        var committee = document.getElementById('committee').value;
        var fiscalyear = document.getElementById('fiscalyear').value;
        window.location.search = "?committee="+committee+"&fiscalyear="+fiscalyear;
    }
</script>

<?php include '../smallfooter.php';?>
