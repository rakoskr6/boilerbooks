<?php
    $title = 'Boiler Books';
    include 'menu.php';
?>

<div class = "container">
    <h2>Welcome to Boiler Books!</h2>
    <p>This is IEEE's expense and income tracking system. If you are going to make a purchase on behalf of Purdue IEEE, you will be required to use this system to claim your reimbursement. If you have the appropriate level of permissions you can also approve purchases, process donations, and view your committee's expenses. If you have any questions please send us an <a href="mailto:ieee-officers@purdueieee.org">email</a> and also view the guide below.</p>
</div>

<div class="container">
    <div class = "row">
        <div class="col-md-1">
            <a class="btn btn-info" href = "user/updateuser.php" roll = "button">Update Info/Password</a>
        </div>

        <div class="col-md-4">
        </div>

        <div class="col-md-1">
            <a class="btn btn-info" href = "guide.php" roll = "button">Guide</a>
        </div>

        <div class="col-md-5">
        </div>

        <div class="col-md-1">
            <a href = "user/signout.php" class="btn btn-info" roll="button">Sign Out</a>
        </div>

    </div>
</div>

<?php include 'smallfooter.php'; ?>
