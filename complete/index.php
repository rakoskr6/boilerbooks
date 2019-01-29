<?php
$title = 'Boiler Books';
$completeactive = "active";
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
    //$sql = "SELECT purchaseID, item FROM Purchases WHERE Purchases.status = 'Requested'";
    $sql = "SELECT DISTINCT p.purchaseID, p.item FROM Purchases p
    INNER JOIN approval a on p.committee = a.committee
    WHERE p.status = 'Approved'
    AND p.username = '$usr'";
    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $items .= '<option value="';
        $items .= $row['purchaseID'];
        $items .= '">';
        $items .= $row['item'];
        $items .= '</option>\n';
    }

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>


<div class="container">
    <select id="currentitem" name="currentitem" class="form-control" onchange="selectitem()">
        <option value="">Select Item</option>
        <?php echo $items; ?>
    </select>
</div>

<!-- Page Content -->
<form class="form-horizontal" action="completeprocessing.php" method="post" enctype="multipart/form-data">
    <fieldset>

        <!-- Form Name -->
        <legend></legend>

        <!-- Text input-->
        <div class="container">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h4 class='text-left'>Committee:</h4>
                </div>
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['committeec']; ?></em></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h4 class='text-left'>Item Being Purchased:</h4>
                </div>
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['itemc']; ?></em></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h4 class='text-left'>Reason Being Purchased:</h4>
                </div>
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['reasonc']; ?></em></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h4 class='text-left'>Vendor:</h4>
                </div>
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['vendorc']; ?></em></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-3">
                    <h4 class='text-left'>Category:</h4>
                </div>
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['categoryc']; ?></em></h4>
                </div>
            </div>


        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Final Cost (Shipping and all)</label>
            <div class="col-md-4">
                <input id="cost" name="cost" type="number" step = "0.01" placeholder="<?php echo $_SESSION['costc']; ?>" class="form-control input-md" required="" value="<?php echo $_SESSION['costc']; ?>">

            </div>
        </div>


        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="purchasedate">Purchase Date</label>
            <div class="col-md-4">
                <input id="purchasedate" name="purchasedate" type="text" class="form-control input-md" required="">
            </div>
        </div>

        <script>
            $('input[name="purchasedate"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true
            });
        </script>

        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Receipt</label>
            <div class="col-md-4">
                <input id="fileToUpload" name="fileToUpload" type="file" class="btn btn-default" required="">
            </div>
        </div>


        <!-- Textarea -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="comments">Comments</label>
            <div class="col-md-4">
                <textarea class="form-control" id="comments" name="comments"><?php echo $_SESSION['commentsc']; ?></textarea>
            </div>
        </div>


        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button id="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </fieldset>
</form>

<script>
    function selectitem() {
        var com = document.getElementById('currentitem').value;
        var title = "finditem.php?currentitem=";
        var full = title.concat(com);
        window.location = full;
    }
</script>

<?php include '../smallfooter.php'; ?>
