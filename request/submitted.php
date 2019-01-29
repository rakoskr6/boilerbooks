<?php
$title = 'Boiler Books';
include '../menu.php';
?>

<div class="container">
    <h2>Purchase successfully submitted!</h2>
    <p>Your purchase request for <?php echo $_SESSION['item']; ?> will soon be approved or denied.</p>
    <p>It can be reviewed by: <?php echo $_SESSION['names']; ?>.</p>
</div>

<?php
$_SESSION['item'] = '';
$_SESSION['names'] = '';
include '../smallfooter.php';
?>
