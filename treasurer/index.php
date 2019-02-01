<?php
$title = 'Boiler Books';
$treasuereactive = "active";
include '../menu.php';

$committee = get_param("committee", $default="%");
$committeeDisplay = get_param("committee", $default="All Committees");
$fiscalyear = get_param("fiscalyear", $default="2018-2019");

$items = db_treasurer($committee, $fiscalyear, $_SESSION['user']);
?>


<div class="container">
    <div class="text-center">
        <h3>Currently viewing <?php echo $committeeDisplay ?> for fiscal year <?php echo $fiscalyear ?></h3>
    </div>
</div>

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
    <table id="expenses" class="display">
        <thead>
            <tr>
                <th>Purchase ID</th>
                <th>Purchase Date</th>
                <th>Item</th>
                <th>Fund Source</th>
                <th>Vendor</th>
                <th>Committee</th>
                <th>Purchased By</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Comments</th>
                <th>Processing</th>
                <th>Reimbursed</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($items as $row): ?>
            <tr>
                <td><a href=/purchase.php?purchaseid=<?= $row['purchaseID']; ?>><?= $row['purchaseID'] ?></a></td>
                <td><?= $row['date']; ?></td>
                <td><a href='<?= $row['receipt']; ?>'><?= $row['item']; ?></a></td>
                <td><?= $row['fundsource']; ?></td>
                <td><?= $row['vendor']; ?></td>
                <td><?= $row['committee']; ?></td>
                <td><?= $row['purchasedby']; ?></td>
                <td><?= $row['status']; ?></td>
                <td><?= $row['cost']; ?></td>
                <td><?= $row['comments']; ?></td>
                <td><a href='update.php?reimbursed=-1&processing=<?= $row['purchaseID']; ?>'>Mark Processing</a></td>
                <td><a href='update.php?processing=-1&reimbursed=<?= $row['purchaseID']; ?>'>Mark Reimbursed</a></td>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            $('#expenses').DataTable({
                "order": [[0, "desc"]]
            });
        });
    </script>

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
