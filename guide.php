<?php
    $title = 'Boiler Books';
    include 'menu.php';
?>


<div class = "container">
    <h2>Boiler Books Guide</h2>
    <p>Boiler Books is an expense and income tracking system. It was developed on behalf of IEEE to track all purchases for the organization.</p>

    <p>The system allows any IEEE member to create an account and request to purchase something on behalf of the indicated committee. The purchase may then be approved or denied by the committee chair (or designated member). If the purchase is approved, the member will then proceed to buy the item. After it is bought the receipt shall be uploaded to allow for timely reimbursement by the IEEE treasurer.</p>

    <p>In addition to being a convenient system to request and approve purchases, members also have the ability to view all of their past purchases. Those with the appropriate permissions are also able to view purchases and income for their entire committee.</p>

    <p>Boiler Books is currently in Beta. There may be some features that are not currently available. Additionally, if you need access to something, would like to help develop Boiler Books, or have any questions or suggestions, please send us an <a href="mailto:ieee-officers@purdueieee.org">email</a>.</p>


</div>

<div class="container">
    <h3>Standard Purchase Process</h3>
    <img src="images/process.jpg" class="img" alt="Standard Process" style="width:70%">
</div>


<div class="container">
    <table class="table">
        <tbody>
            <tr>
                <td><h4><a href='/request'>Request Purchase</a></h4></td>
                <td>This is where anyone may ask to buy something. You will be asked for basic information that will be
                    used to keep track of items purchased and to decide if it will be approved or denied.</td>
            </tr>
            <tr>
                <td><h4><a href='/approve'>Approve Purchase</a></h4></td>
                <td>This is accessable to the committee chairs or their designated members to decide if the purchase shoudl be approved or denied. They also have the ability to update the original information.</td>
            </tr>
            <tr>
                <td><h4><a href='/complete'>Complete Purchase</a></h4></td>
                <td>This is where the purchaser will go to finish the purchase if it was approved. At this point they should buy the item and
                    procceed to upload the receipt.</td>
            </tr>

        </tbody>
    </table>
</div>


<div class="container">
    <h3>Additional Tools</h3>
    <img src="images/tracking.png" class="img" alt="Tracking expenses" style="width:50%">

</div>


<div class="container">
    <table class="table">
        <tbody>
            <tr>
                <td><h4><a href='/mypurchases'>View My Purchases</a></h4></td>
                <td>This is where you may view all purchases you have made. It will show both approved
                    and denied requests.</td>
            </tr>
            <tr>
                <td><h4><a href='/committee'>View Committee Expenses</a></h4></td>
                <td>This is accessable to the committee chair or their designated members to view expenses and income of the committee.</td>
            </tr>
            <tr>
                <td><h4><a href='/donation'>Receive Donation</a></h4></td>
                <td>This is accessable to the committee chair or their designated members to update committee income.</td>
            </tr>
            <tr>
                <td><h4><a href='/treasurer'>Treasurer</a></h4></td>
                <td>This is accessable to the IEEE treasurer and president. This is where they will indicate if BOSO has processed the check and if it is ready for you to pick up.</td>
            </tr>

        </tbody>
    </table>
</div>


<?php include 'smallfooter.php'; ?>
