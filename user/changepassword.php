<?php
    $title = 'Boiler Books';
    include '../menu.php';
?>

<body>


<!-- Page Content -->

<form class="form-horizontal" action="changepasswordprocessing.php" method="post">
    <fieldset>

        <!-- Form Name -->
        <legend></legend>

        <!-- Select Basic -->

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Old Password</label>
            <div class="col-md-4">
                <input id="oldpassword" name="oldpassword" type="password" placeholder="*****" class="form-control input-md" required="">

            </div>
        </div>

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

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button id="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </fieldset>
</form>

<?php include '../smallfooter.php';?>
