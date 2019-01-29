<?php
session_start();
$title = 'Boiler Books';
include 'header.php';
include 'dbinfo.php';
$returnto = test_input2($_GET['returnto']);
$verifylogin = "login.php?returnto=" . $returnto;
include 'assets/fakemenu.php'
?>

<div class="container">
    <div class="col-sm-3"> </div>
    <div class="col-sm-6">
        <form class="form-signin" action=<?php echo $verifylogin ?> method="post">
            <h3 class="form-signin-heading">Please Sign In</h3>
            <font color="blue"><em><?php echo test_input($_GET["fail"]); ?></em></font>

            <label for="inputEmail" class="sr-only">Username</label>
            <input type="text" id="usr" name="usr" class="form-control" placeholder="Your Boiler Books user account" required autofocus>

            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="psw" name = "psw" class="form-control" placeholder="Password" required>

            <br>

            <div class="form-group">
                <button id="submit" name="submit" class="btn btn-primary">Sign in</button>
            </div>


            <a href = "user/newuser.php" class="btn btn-lg btn-block">New User</a></button>
            <br>
            <div>
                <div class="col-sm-6">
                    <a href = "user/forgotusername.php" class="btn btn-lg btn-block">Forgot username?</a></button>
                </div>
                <div class="col-sm-6">
                    <a href = "user/forgotpassword.php" class="btn btn-lg btn-block">Forgot password?</a></button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-sm-3"> </div>
</div>

<?php include 'smallfooter.php';?>
