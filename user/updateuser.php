<?php
    $title = 'Boiler Books';
    include '../menu.php';
?>

<?php
include '../dbinfo.php';
$usr = $_SESSION['user'];


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM Users U
        WHERE U.username = '$usr'";

    foreach ($conn->query($sql) as $row) {
        $first = $row['first'];
        $last = $row['last'];
        $email = $row['email'];
        $address = $row['address'];
        $city = $row['city'];
        $state = $row['state'];
        $zip = $row['zip'];
        $cert = $row['cert'];
        $usr = $row['username'];
    }

} catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
?>

<body>
<br>
<div class="container">
    <div class = "row">
        <div class="col-md-5">
        </div>
        <div class="col-md-2">
      <a class="btn btn-info" href = "/user/changepassword.php" roll = "button">Change Password</a>
            <p> </p>
        </div>
        <div class="col-md-5">
        </div>
    </div>
</div>

<div class="container">
    <p><?php echo $uploaderr ?></p>
</div>
   <!-- Page Content -->

    <form class="form-horizontal" action="updateuserprocessing.php" method="post" enctype="multipart/form-data">
<fieldset>


<!-- Select Basic -->

<!-- Text input-->
<div class="form-group">

  <label class="col-md-4 control-label" for="item">Current First Name</label>
  <div class="col-md-4">
  <input id="first" name="first" type="text" placeholder="Mitch" class="form-control input-md" required="" value='<?php echo $first ?>'>

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="reason">Current Last Name</label>
  <div class="col-md-4">
  <input id="last" name="last" type="text" placeholder="Daniels" class="form-control input-md" required="" value='<?php echo $last ?>'>

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="vendor">Current Email</label>
  <div class="col-md-4">
  <input id="email" name="email" type="text" placeholder="president@purdue.edu " class="form-control input-md" required=""  value='<?php echo $email ?>'>

  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="cost">Current Address</label>
  <div class="col-md-4">
  <input id="address" name="address" type="text" placeholder="610 Purdue Mall" class="form-control input-md" required=""  value='<?php echo $address ?>'>

  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="cost">Current City</label>
  <div class="col-md-4">
    <input id="city" name="city" type="text" placeholder="West Lafayette" class="form-control input-md" required="" value='<?php echo $city ?>'>
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="cost">Current State</label>
  <div class="col-md-4">
  <input id="state" name="state" type="text" placeholder="IN" class="form-control input-md" required=""  value ='<?php echo $state ?>'>

  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="cost">Current ZIP</label>
  <div class="col-md-4">
  <input id="zip" name="zip" type="text" placeholder="47907" class="form-control input-md" required=""  value ='<?php echo $zip ?>'>

  </div>
</div>



<div class="form-group">
  <label class="col-md-4 control-label" for="cost"><a href="../reimburse.pdf">Reimbursement Certificate</a></label>
  <div class="col-md-4">
  <input id="fileToUpload" name="fileToUpload" type="file" class="btn btn-default">

  </div>
</div>

<!--
<div class="form-group">
  <label class="col-md-4 control-label" for="cost">Current Username</label>
  <div class="col-md-4">
  <input id="username" name="username" type="text" placeholder="mdaniels" class="form-control input-md" required=""  value = '<?php echo $usr ?>'>

  </div>
</div>
-->

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">Update Info</button>
  </div>
</div>

</fieldset>
</form>


<?php include '../smallfooter.php';?>
