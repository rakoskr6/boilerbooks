<?php
// Request  HTML form
$title = 'Boiler Books';
$requestactive = "active";
include '../menu.php';
?>

<?php
if (isset($_GET['committee'])) {
    $committee = test_input($_GET['committee']);
}

$categorylist = '';

if ($committee != "") {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //$sql = "SELECT purchaseID, item FROM Purchases WHERE Purchases.status = 'Requested'";
        $sql = "SELECT category FROM `Budget`
        WHERE committee='$committee' AND year='2018-2019'";
        //$stmt->execute();

        foreach ($conn->query($sql) as $row) {
            $categorylist .= '<option value="';
            $categorylist .= $row['category'];
            $categorylist .= '">';
            $categorylist .= $row['category'];
            $categorylist .= '</option>\n';
        }
        //echo $categorylist;

    } catch (PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
    }
}

$conn = null;
?>

<form class="form-horizontal" action="/api/request/" method="post">
    <fieldset>
        <legend></legend>

        <div class="form-group">
            <label class="col-md-4 control-label" for="Committee">Committee</label>
            <div class="col-md-4">
                <select id="committee" name="committee" class="form-control" required="" onchange="categories()">
                    <?php include '../committees.php';?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="item">Item Being Purchased</label>
            <div class="col-md-4">
                <input id="item" name="item" type="text" placeholder="Resistors, screws, etc." class="form-control input-md" required="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="reason">Reason Being Purchased</label>
            <div class="col-md-4">
                <input id="reason" name="reason" type="text" placeholder="For testing, building claw, etc." class="form-control input-md" required="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="vendor">Vendor</label>
            <div class="col-md-4">
                <input id="vendor" name="vendor" type="text" placeholder="McMaster, Digikey, etc." class="form-control input-md" required="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="cost">Cost</label>
            <div class="col-md-4">
                <input id="cost" name="cost" type="number" step = "0.01" placeholder="$156.56" class="form-control input-md" required="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="comments">Comments</label>
            <div class="col-md-4">
                <textarea class="form-control" id="comments" name="comments"></textarea>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-4 control-label" for="category">Category</label>
            <div class="col-md-4">

                <select id="category" name="category" class="form-control input-md" required="">
                    <option value="">Select Item</option>
                    <?php echo $categorylist; ?>
                </select>

            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button id="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </fieldset>
</form>

<script>
function categories() {

    var com = document.getElementById('committee').value;
    var title = "index.php?committee=";
    var full = title.concat(com);

    /* Need a way to save value of committee accross page refresh */
    localStorage.setItem("committee", com);
    console.log(com);

    window.location = full;
}

$( function() {
    /* On page load retrieve the value of committee */
    committee = localStorage.getItem("committee");
    console.log(committee);
    if(committee != null) {
        document.getElementById("committee").value = committee;
        localStorage.removeItem("committee");
    }
})

</script>

<?php include '../smallfooter.php';?>
