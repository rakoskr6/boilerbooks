<?php
include '../header.php';
include '../assets/fakemenu.php';
include '../dbinfo.php';
?>

<div class="container">
    <h3>Forgot username?</h3>

    <?php
$found = test_input($_GET[found]);
$email = test_input($_GET[email]);
if ($found === '1') {
    echo '<p> An email containing your username has been sent to ' . $email . '</p>';
} else if ($found === '0') {
    echo '<p>No account was found for ' . $email . '</p>';
    echo "<p>Would you like to <a href='/user/newuser.php'>create a new account?</a></p>";
}
?>

    <form class="form-horizontal" action="/api/user/username.php" method="post">
      <fieldset>
        <div class="form-group">
          <label class="col-md-4 control-label" for="email">Email associated with account</label>
          <div class="col-md-4">
            <input id="email" name="email" type="text" placeholder="president@purdue.edu" class="form-control input-md" required="">
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="submit"></label>
          <div class="col-md-4">
            <button id="submit" name="submit" class="btn btn-primary">Find Username</button>
          </div>
        </div>

      </fieldset>
    </form>
</div>

<?php include '../smallfooter.php';?>
