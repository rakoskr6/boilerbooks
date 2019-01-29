<?php
$title = 'Boiler Books';
include '../header.php';
?>

<body>
    <div class="container">
        <h1 class = "text-center"><a href="/loggedin.php">Boiler Books</a></h1>
        <h4 class = "text-center">The ultimate expense and income tracking system for student organizations</h4>
        <ul class="nav nav-tabs">
            <li class="disabled disabledTab"><a href="#">Request Purchase</a></li>
            <li class="disabled disabledTab"><a href="#">Complete Purchase</a></li>
            <li class="disabled disabledTab"><a href="#">View My Purchases</a></li>
        </ul>
    </div>

    <!--
    <div class="container">
        <h4><?php //echo $_SESSION['error'] ?></h4>
    </div>
    -->

    <form class="form-horizontal" action="newuserprocessing.php" method="post" enctype="multipart/form-data">
        <fieldset>

            <!-- Form Name -->
            <legend></legend>

            <!-- Select Basic -->

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="item">First Name</label>
                <div class="col-md-4">
                    <input id="first" name="first" type="text" placeholder="Mitch" class="form-control input-md" required="">
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="reason">Last Name</label>
                <div class="col-md-4">
                    <input id="last" name="last" type="text" placeholder="Daniels" class="form-control input-md" required="">
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="vendor">Email</label>
                <div class="col-md-4">
                    <input id="email" name="email" type="text" placeholder="president@purdue.edu " class="form-control input-md" required="">
                </div>
            </div>


            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">Address</label>
                <div class="col-md-4">
                    <input id="address" name="address" type="text" placeholder="610 Purdue Mall" class="form-control input-md" required="">
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">City</label>
                <div class="col-md-4">
                    <input id="city" name="city" type="text" placeholder="West Lafayette" class="form-control input-md" required="">
                </div>
            </div>


            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">State</label>
                <div class="col-md-4">
                    <input id="state" name="state" type="text" placeholder="IN" class="form-control input-md" required="">
                </div>
            </div>


            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">ZIP</label>
                <div class="col-md-4">
                    <input id="zip" name="zip" type="text" placeholder="47907" class="form-control input-md" required="">
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-4 control-label" for="cost"><a href="https://engineering.purdue.edu/PPI/docs/CheckRequestReimbursementCertification.pdf">Reimbursement Cert (click here to download it, then upload as a PDF)</a></label>
                <div class="col-md-4">
                    <input id="fileToUpload" name="fileToUpload" type="file" class="btn btn-default" required="" accept="application/pdf">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">Username</label>
                <div class="col-md-4">
                    <input id="username" name="username" type="text" placeholder="mdaniels" class="form-control input-md" required="">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">Password</label>
                <div class="col-md-4">
                    <input id="password1" name="password1" type="password" placeholder="*****" class="form-control input-md" required="">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">Reenter Password</label>
                <div class="col-md-4">
                    <input id="password2" name="password2" type="password" placeholder="*****" class="form-control input-md" required="">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="cost">IEEE Code</label>
                <div class="col-md-4">
                    <input id="ieeecode" name="ieeecode" type="password" placeholder="Enter the code given by IEEE to enable account creation" class="form-control input-md" required="">
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
