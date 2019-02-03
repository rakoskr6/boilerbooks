<?php
// approval form
$title = 'Boiler Books';
$approveactive = "active";
include '../menu.php';


$purchases = db_approval_purchases($_SESSION['user']);

if (isset($_GET['purchase'])) {
    $purchaseID = sanatize($_GET['purchase']);
    //TODO: No user verification on this purchase
    $purchase = db_purchase($user, $purchaseID);

    $balance = db_committee_balance($purchase['committee']);
}
?>

<!-- Page Content -->

<div class="container">
    <select id="currentitem" name="currentitem" class="form-control" onchange="selectitem()">
        <option value="">Select Item</option>
        <?php foreach ($purchases as $option): ?>
        <option
            <?php if ($option['purchaseID'] == $purchaseID) {  ?> selected="selected" <?php } ?>
            value="<?= $option['purchaseID']; ?>">
            <?= $option['item'] ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="container">
    <h4>
    <?php
        if (isset($_GET['purchase']) && $balance < $purchase['cost']) {
            echo "<font color='red'>Warning! You only have $" . $balance . " left in your account. </font>";
            echo "<font color='red'>Please talk to the IEEE treasurer before approving this purchase!</font>";
        } else if (isset($_GET['purchase']) && ($balance < 200)) {
            echo "<font color='orange'>Warning! You only have $" . $balance . " left in your account!</font>";
        }
    ?>
    </h4>
</div>

<!-- Page Content -->

<?php if (isset($_GET['purchase'])) { ?>
<form class="form-horizontal" action="/approve/approve.php" method="post">
    <fieldset>
        <!-- Form Name -->
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-2">
                <h4 class='text-left'>Purchase Request by:</h4>
            </div>
            <div class="col-sm-6">
                <h4><em><?php echo $purchase['purchasedby']; ?></em></h4>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="Committee">Committee</label>
            <div class="col-md-4">
                <div class="col-sm-6">
                    <h4><em><?php echo $purchase['committee']; ?></em></h4>
                </div>
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="item">Item Being Purchased</label>
            <div class="col-md-4">
                <input id="item" name="item" type="text" class="form-control input-md" value="<?php echo $purchase['item']; ?>" required="">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="reason">Reason Being Purchased</label>
            <div class="col-md-4">
                <input id="reason" name="reason" type="text" class="form-control input-md" required="" value="<?php echo $purchase['reason']; ?>">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="vendor">Vendor</label>
            <div class="col-md-4">
                <input id="vendor" name="vendor" type="text" class="form-control input-md" required="" value="<?php echo $purchase['vendor']; ?>">

            </div>
        </div>


        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Cost</label>
            <div class="col-md-4">
                <input id="cost" name="cost" type="number" step = "0.01" class="form-control input-md" required="" value="<?php echo $purchase['cost']; ?>">
            </div>
        </div>

        <!-- Textarea -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="comments">Comments</label>
            <div class="col-md-4">
                <textarea class="form-control" id="comments" name="comments"><?php echo $purchase['comments']; ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="category">Category</label>
            <div class="col-md-4">
                <textarea class="form-control" id="category" name="category"><?php echo $purchase['category']; ?></textarea>
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

        <!-- Hidden Purchase ID -->
        <input style="display: none;" name="purchaseID" value="<?php echo $purchaseID; ?>">

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button id="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </fieldset>
</form>

<?php } ?>

<script>
    function selectitem() {
        var purchase = document.getElementById('currentitem').value;
        var query = "index.php?purchase=" + purchase;
        window.location = query;
    }
</script>

<?php include '../smallfooter.php';?>
