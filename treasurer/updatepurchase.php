<?php
    $title = 'Boiler Books';
    $mypurchasesactive = "active";
    include '../menu.php';
    include '../dbinfo.php';
    $decode = 1;

    $purchaseid = test_input($_GET["purchaseid"]);
    $url = "https://" . $_SERVER[HTTP_HOST] . "/api/purchaseid/?purchaseid=" . $purchaseid . "&user=" . $_SESSION['user'] . "&apikey=" . $_SESSION['apikey'] . "&role1=treasurer&role2=president";
    //echo $url;
    $jsonObj = file_get_contents($url);
    $values = json_decode($jsonObj, TRUE);
    $editable = "contenteditable='true'";
?>


<div class = "container">
    <div class="well">
        <h3 class="text-center">Edit Purchase Mode</h3>
        <h4 class="text-center">President and treasurer only</h4>
        <div class="col-sm-6">
            <h3 class="text-right">Purchase #<?php echo $purchaseid?>:</h3>
        </div>
        <div class="col-sm-6">
            <h3 class="text-left" style="color:blue" contenteditable="true"><?php echo $values['item']?></h3>
        </div>
        <p class="text-center"><em>Last updated <?php echo $values['mdate']?> EST</em></p>
        <p class="text-center"><a class="btn btn-info" href = <?php echo "../purchase.php?purchaseid=" . $purchaseid?> roll = "button">Exit Edit Purchase Mode</a></p>
        <p class="text-left"><em>Note: The president and treasurer should only use this mode when there was a mistake or unexpected change of a purchase. In general, purchases should be updated through the Request, Approve, and Complete steps (and reimbursed through the treasurer tab). When using this form, you only have the option to change items in blue. Titles that are in red have special care that needs to be taken when editing (highlight the mouse of the relevant section for more details). Always ensure proper data entry, or this form may not properly update the database.</em>

    </div>

    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2" class="text-center">
            <p><strong>Purchased by:</strong></p>
        </div>
        <div class="col-sm-3" class="text-center">
            <p><?php echo $values['purchasedby']?></p>
        </div>
        <div class="col-sm-2" class="text-center">
            <p title="Ensure same date format as indicated"><strong style="color:red">Purchase date:</strong></p>
        </div>
        <div class="col-sm-3">
            <p title="Ensure same date format as indicated" style="color:blue" contenteditable="true"><?php echo $values['date']?></p>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2">
            <p><strong>Purchase reason:</strong></p>
        </div>
        <div class="col-sm-3">
            <p style="color:blue" contenteditable="true"><?php echo $values['purchasereason']?></p>
        </div>

        <div class="col-sm-2">
            <p title="Must be Requested, Approved, Denied, Purchased, Processing Reimbursement, or Reimbursed"><strong style="color:red">Status:</strong></p>
        </div>
        <div class="col-sm-3">
            <p title="Must be Requested, Approved, Denied, Purchased, Processing Reimbursement, or Reimbursed" style="color:blue" contenteditable="true"><?php echo $values['status']?></p>
        </div>
    </div>

    <br>


    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2">
            <p title = "Enter only numbers (no dollar sign). Requires 2 decimal points"><strong style="color:red">Cost (USD):</strong></p>
        </div>
        <div class="col-sm-3">
            <p title = "Enter only numbers (no dollar sign). Requires 2 decimal points" style="color:blue" contenteditable="true"><?php echo $values['cost']?></p>
        </div>
        <div class="col-sm-2">
            <p><strong>Vendor:</strong></p>
        </div>
        <div class="col-sm-3">
            <p style="color:blue" contenteditable="true"><?php echo $values['vendor']?></p>
        </div>
    </div>

    <br>


    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2">
            <p title="Ensure the category is one of the committee's budget categories shown under their page"><strong style="color:red">Category:</strong></p>
        </div>
        <div class="col-sm-3">
            <p itle="Ensure the category is one of the committee's budget categories shown under their page" style="color:blue" contenteditable="true"><?php echo $values['category']?></p>
        </div>
        <div class="col-sm-2">
            <p><strong>Approved by:</strong></p>
        </div>
        <div class="col-sm-3">
            <p><?php echo $values['approvedby']?></p>
        </div>
    </div>

    <br>


    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2">
            <p><strong>Committee:</strong></p>
        </div>
        <div class="col-sm-3">
            <p><?php echo $values['committee']?></p>
        </div>
        <div class="col-sm-2">
            <p title="Must be BOSO, Cash, or SOGA"><strong style="color:red">Funding Source:</strong></p>
        </div>
        <div class="col-sm-3">
            <p title="Must be BOSO, Cash, or SOGA" style="color:blue" contenteditable="true"><?php echo $values['fundsource']?></p>
        </div>
    </div>

    <br>


    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-2">
            <p title="Must be 2018-2019, 2017-2018, 2016-2017, or 2015-2016"><strong style="color:red">Fiscal Year:</strong></p>
        </div>
        <div class="col-sm-3">
            <p title="Must be 2018-2019, 2017-2018, 2016-2017, or 2015-2016" style="color:blue" contenteditable="true"><?php echo $values['fiscalyear']?></p>
        </div>
        <div class="col-sm-2">
            <p><strong>Comments:</strong></p>
        </div>
        <div class="col-sm-3">
            <p style="color:blue" contenteditable="true"><?php echo $values['comments']?> </p>
        </div>
    </div>

    <br>

    <div class="row">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        <iframe src= "<?php echo 'https://' . $_SERVER[HTTP_HOST] . $values['receipt']?>" style="position: relative; width: 100%;" height="700">
            <a href="<?php echo 'https://' . $_SERVER[HTTP_HOST] . $values['receipt']?>">Download receipt</a>
        </iframe>
    </div>
    <div class="col-sm-2">
    </div>
    </div>
    <br>

</div>



<?php
    include 'smallfooter.php';
?>
