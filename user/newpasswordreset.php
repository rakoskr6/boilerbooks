<?php
    $title = 'Boiler Books';
    include '../header.php';
    include '../dbinfo.php';
    $usrn = test_input($_GET['usrn']);
    $rstlink = test_input($_GET['rstlink']);
?>

<!-- Page Content -->
<div class="container">
    <form class="form-horizontal" action="changepasswordprocessing2.php" method="post">
        <h3 class="form-horizontal-heading">Please Select a new password</h3>

        <?php
            if (test_input($_GET["match"]) === '0') {
                echo "<font color='blue'><em>Passwords must match</em></font>";
            }
        ?>

        <fieldset>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">New Password</label>
                <div class="col-md-4">
                    <input id="password1" name="password1" type="password" placeholder="*****" class="form-control input-md" required="">

                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">Reenter New Password</label>
                <div class="col-md-4">
                    <input id="password2" name="password2" type="password" placeholder="*****" class="form-control input-md" required="">

                </div>
            </div>

            <input id="usrnid" name="usrnid" type="hidden" class="form-control input-md" value='<?php echo $usrn?>'>
            <input id="rstlinkid" name="rstlinkid" type="hidden" class="form-control input-md" value='<?php echo $rstlink?>'>

            <!-- Button -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="submit"></label>
                <div class="col-md-4">
                    <button id="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<?php include '../smallfooter.php';?>
