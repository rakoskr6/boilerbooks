<!DOCTYPE html>
<!-- The header that is included on every page. Contains top bar, libaries, css, etc. -->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>
        <?php
            // Only displays title if set
            if ($title == '') {
                echo 'Boiler Books';
            }
            else {
                echo $title;
            }
        ?>
    </title>

    <!-- Bootstrap Components -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- Jquery used for various things such as datatables and date entry -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

    <!-- Custom CSS -->
    <link href="/assets/landing-page.css" rel="stylesheet">
    <link href="/assets/custom.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="/text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="/text/css">

    <!--favicon link-->
    <link rel="SHORTCUT ICON" href="/favicon.ico">
    <link rel="apple-touch-icon" href="https://www.ieee.org/ucm/groups/webassets/@ieee/@web/@org/documents/images/ieee_apple_touch_icon.png">

    <!-- Stuff for datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

    <!-- Used for date entry -->
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
</head>

<body> <!-- closing body in footer -->
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar"> <!-- for collapsable menu on small screens -->
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/loggedin.php">Boiler Books</a>
            </div>

            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="/user/updateuser.php"><?php echo $_SESSION['user'] ?></a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="http://purdueieee.org"><img src="/images/ieeeLogo.svg" alt="" height="16" width="16"> Purdue IEEE</a></li>
                    <li><a href="/user/updateuser.php"><?php if ($_SESSION['user'] != '') {echo "<span class='glyphicon glyphicon-cog'></span></span> Settings";}?></a></li>
                    <li><a href="/user/signout.php"> <?php if ($_SESSION['user'] != '') {echo "<span class='glyphicon glyphicon-log-in'></span> Sign out";}?></a></li>
                </ul>

            </div>
        </div>
    </nav>
