<?php
    $title = 'Boiler Books';
    $donationactive = "active";
    include '../menu.php';
?>

<form class="form-horizontal" action="donationprocessing.php" method="post">
<fieldset>

    <!-- Form Name -->
    <legend></legend>

    <!-- Text input-->
    <!-- Select Basic -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="committee">Committee</label>
        <div class="col-md-4">
            <select id="committee" name="committee" class="form-control">
            <?php include '../committees.php'; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="source">Source</label>
        <div class="col-md-4">
            <input id="source" name="source" type="text" placeholder="Provost, PESC, Northrop, etc." class="form-control input-md" required="">
        </div>
    </div>

    <!-- Text input-->
    <div class="form-group">
        <label class="col-md-4 control-label" for="amount">Amount</label>
        <div class="col-md-4">
            <input id="amount" name="amount" type="number" step="0.01" placeholder="$1500.00" class="form-control input-md" required="">
        </div>
    </div>

    <!-- Text input-->
    <div class="form-group">
        <label class="col-md-4 control-label" for="item">Item</label>
        <div class="col-md-4">
            <input id="item" name="item" type="text" placeholder="If donation list item" class="form-control input-md">
        </div>
    </div>

    <!-- Select Basic -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="category">Type</label>
        <div class="col-md-4">
            <select id="category" name="category" class="form-control">
            <option value="BOSO">BOSO</option>
            <option value="Cash">Cash</option>
            <option value="Discount">Discount</option>
            <option value="SOGA">SOGA</option>
            </select>
        </div>
    </div>

    <!-- Select Basic -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="status">Status</label>
        <div class="col-md-4">
            <select id="status" name="status" class="form-control">
            <option value="Received">Received</option>
            <option value="Expected">Expected</option>
            </select>
        </div>
    </div>

    <!-- Textarea -->
    <div class="form-group">
        <label class="col-md-4 control-label" for="comments">Comments</label>
        <div class="col-md-4">
            <textarea class="form-control" id="comments" name="comments"></textarea>
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

<?php include '../smallfooter.php'; ?>
