<?php
$title = 'Boiler Books';
include '../menu.php';

$user = db_user(get_param("user_lookup"), $_SESSION['user']);
?>


<div class='container'>
    <div class='container'>
        <label class="col-md-4">First Name</label>
        <div class="col-md-4">
            <?= $user['first']; ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">Last Name</label>
        <div class="col-md-4">
            <?= $user['last']; ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">Email</label>
        <div class="col-md-4">
            <?= "<a href='mailto:$user[email]'>$user[email]</a>"; ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">Address</label>
        <div class="col-md-4">
            <?= $user['address']; ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">City</label>
        <div class="col-md-4">
            <?= $user['city']; ?>
        </div>
    </div>


    <div class='container'>
        <label class="col-md-4">State</label>
        <div class="col-md-4">
            <?= $user['state']; ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">ZIP</label>
        <div class="col-md-4">
            <?= sprintf("%05d", $user['zip']); ?>
        </div>
    </div>

    <div class='container'>
        <label class="col-md-4">
            <a href='../user/<?= $user['cert']; ?>'>Reimbursement Certificate</a>
        </label>
    </div>
</div>

<?php include '../smallfooter.php';?>
