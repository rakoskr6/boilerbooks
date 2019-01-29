<?php
include '../header.php';
include '../assets/fakemenu.php';
include '../dbinfo.php';
?>

<div class="container">
    <h3>Forgot password?</h3>
    <?php
$found = test_input($_GET[found]);
$email = test_input($_GET[email]);
$usrn = test_input($_GET[usrn]);

if ($found === '1') {
    echo '<p> An email containing a link to reset your password has been sent to ' . $email . '</p>';
} else if ($found === '0') {
    echo '<p>No account was found for ' . $usrn . " with the email " . $email . '</p>';
}
?>

    <form class="form-horizontal" action="/api/user/password.php" method="post">
      <fieldset>
        <div class="form-group">
          <label class="col-md-4 control-label" for="usrn">Username</label>
          <div class="col-md-4">
            <input id="usrn" name="usrn" type="text" placeholder="president@purdue.edu" class="form-control input-md" required="">
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="email">Email associated with account</label>
          <div class="col-md-4">
            <input id="email" name="email" type="text" placeholder="mdaniels" class="form-control input-md" required="">
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="submit"></label>
          <div class="col-md-4">
            <button id="submit" name="submit" class="btn btn-primary">Request password reset</button>
          </div>
        </div>

      </fieldset>
    </form>
</div>

<?php include '../smallfooter.php';?>

