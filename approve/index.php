<?php
// approval form
$title = 'Boiler Books';
$approveactive = "active";
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
        WHERE p.status = 'Requested'
        AND a.username = '$usr'
        AND (a.category = p.category OR a.category = '*')
        AND p.cost <= (SELECT MAX(ap.ammount) FROM approval ap
        WHERE ap.username = '$usr'
        AND ap.committee = p.committee)";

    //$stmt->execute();

    foreach ($conn->query($sql) as $row) {
        $items .= '<option value="';
        $items .= $row['purchaseID'];
        $items .= '">';
        $items .= $row['item'];
        $items .= '</option>\n';
    }

    //echo $items;

} catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>

<!-- Page Content -->

<div class="container">
    <select id="currentitem" name="currentitem" class="form-control" onchange="selectitem()">
        <option value="">Select Item</option>
        <?php echo $items; ?>
    </select>
</div>

<div class="container">
    <h4>
    <?php
if ($_SESSION['balance'] < $_SESSION['cost']) {
    echo "<font color='red'>Warning! You only have $" . $_SESSION['balance'] . " left in your account. </font>";
    echo "<font color='red'>Please talk to the IEEE treasurer before approving this purchase!</font>";
} else if (($_SESSION['balance'] < 200) && ($_SESSION['balance'] != 0)) { // also != 0 to prevent showing before variable is set. Slight issue if actually 0 balance but presumably the cost would be greater than 0, thus still showing a warning
    echo "<font color='orange'>Warning! You only have $" . $_SESSION['balance'] . " left in your account!</font>";
}
?>
    </h4>
</div>

<!-- Page Content -->

<form class="form-horizontal" action="/api/approve/" method="post">
    <fieldset>

        <!-- Form Name -->
        <legend></legend>

        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-2">
                <h4 class='text-left'>Purchase Request by:</h4>
            </div>
            <div class="col-sm-6">
                <h4><em><?php echo $_SESSION['name']; ?></em></h4>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="Committee">Committee</label>
            <div class="col-md-4">
                <div class="col-sm-6">
                    <h4><em><?php echo $_SESSION['committee']; ?></em></h4>
                </div>
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="item">Item Being Purchased</label>
            <div class="col-md-4">
                <input id="item" name="item" type="text" placeholder="Select item above to view" class="form-control input-md" value="<?php echo $_SESSION['item']; ?>" required="">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="reason">Reason Being Purchased</label>
            <div class="col-md-4">
                <input id="reason" name="reason" type="text" placeholder="Select item above to view" class="form-control input-md" required="" value="<?php echo $_SESSION['reason']; ?>">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="vendor">Vendor</label>
            <div class="col-md-4">
                <input id="vendor" name="vendor" type="text" placeholder="Select item above to view" class="form-control input-md" required="" value="<?php echo $_SESSION['vendor']; ?>">

            </div>
        </div>


        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Cost</label>
            <div class="col-md-4">
                <input id="cost" name="cost" type="number" step = "0.01" placeholder="Select item above to view" class="form-control input-md" required="" value="<?php echo $_SESSION['cost']; ?>">

            </div>
        </div>

        <!-- Textarea -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="comments">Comments</label>
            <div class="col-md-4">
                <textarea class="form-control" id="comments" name="comments"><?php echo $_SESSION['comments']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="category">Category</label>
            <div class="col-md-4">
                <textarea class="form-control" id="category" name="category"><?php echo $_SESSION['category']; ?></textarea>
            </div>
        </div>


        <!-- Select Basic -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="category">Funding Source</label>
            <div class="col-md-4">
                <select id="fundsource" name="fundsource" class="form-control">
                    <option value="BOSO">BOSO</option>
                    <option value="Cash">Cash</option>
                    <option value="SOGA">SOGA</option>
                </select>
            </div>
        </div>

        <!-- Select Basic -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="category">Approve/Deny</label>
            <div class="col-md-4">
                <select id="status" name="status" class="form-control" required="">
                    <option></option>
                    <option value="Denied">Deny</option>
                    <option value="Approved">Approve</option>
                </select>
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


<?php include '../smallfooter.php';?>
