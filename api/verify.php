<?php
    /* This file is included before beginning other API calls in order to ensure the user is properly logged in.
    * Variables are passed using GET. There is no response, as the user is redirected to the login page if they
    * do not have the appropriate permissions
    * Required:
    *	apikey: The api key that is generated upon login for each user
    *	user: The currently logged in user. This will be verified against the API key
    * Optional:
    *	role1: If you would like to verify the user has a certain role you may pass this variable (eg.
    * treasurer)
    *	role2: If you would like to verify the user has another certain role you may pass this variable (eg.
    * 	president). Options will be successfully returned if either role1 OR role2 are fulfilled
    */


    include '../../dbinfo.php';

    // define variables and set to empty values
    $apikey = $user = $role1 = $role2 = "";

    $apikey = test_input($_GET["apikey"]);
    $user = test_input($_GET["user"]);
    $role1 = test_input($_GET["role1"]);
    $role2 = test_input($_GET["role2"]);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT apikey, apikeygentime
            FROM Users U
            WHERE U.username = '$user'
            AND U.username in (
                SELECT A.username
                    FROM approval A
                    WHERE A.username = U.username
                    AND (A.role = '$role1' OR A.role = '$role2' OR ('$role1'='' AND '$role2'=''))
            )
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll();
        foreach ($results as $pswd) {
            $dbpsw = $pswd['apikey'];
            $dbtime = $pswd['apikeygentime'];
        }
        $now = date('Y-m-d H:i:s');

        $datetime1 = new DateTime($dbtime);
        $datetime2 = new DateTime($now);
        $interval = $datetime1->diff($datetime2);
        //echo $interval->format('%i minutes');
        $timediff = $interval->format('%i');

        if (!(password_verify($apikey,$dbpsw) && ($timediff<=120))) {
            $headerinfo = "Location: /index.php?returnto=" . $_SERVER['REQUEST_URI'];
            header($headerinfo);
            die();
        }

    } catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
        $headerinfo = "Location: /index.php?returnto=" . $_SERVER['REQUEST_URI'];
        header($headerinfo);
        die();
    }

    $conn = null;
?>
