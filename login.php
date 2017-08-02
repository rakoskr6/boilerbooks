<?php
session_start();
include 'dbinfo.php';

// define variables and set to empty values
$psw = $usr = "";

$psw = ($_POST["psw"]);
$usr = test_input($_POST["usr"]);




try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $conn->prepare("SELECT password FROM Users WHERE Users.username = '$usr'");
	$stmt->execute();

	$results = $stmt->fetchAll();
	foreach ($results as $pswd) {
		$dbpsw = $pswd['password'];
	}





	if (password_verify($psw,$dbpsw))
	{

		$_SESSION['user'] = $usr;
		
		/***** Figures out what options to display to user ******/
		$usr = $_SESSION['user'];

		try {
			$conn2 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql2 = "SELECT COUNT(*) AS count FROM Users U3
				    INNER JOIN approval A ON U3.username = A.username
				    WHERE (A.role = 'treasurer' OR A.role = 'president')
				    AND U3.username = '$usr'";

			foreach ($conn2->query($sql2) as $row2) {
				$item2 = $row2['count'];
			}
				
			$_SESSION['viewTreasurer'] = $item2;

			}
		catch(PDOException $e)
			{
			echo $sql2 . "<br>" . $e->getMessage();
			}



		try {
			$conn2 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql2 = "SELECT COUNT(*) AS count FROM approval A WHERE A.username = '$usr'";

			foreach ($conn2->query($sql2) as $row2) {
				$item2 = $row2['count'];
			}
				
			$_SESSION['viewCommitteeExpenses'] = $item2;
			$_SESSION['viewReceiveDonation'] = $item2;

			}
		catch(PDOException $e)
			{
			echo $sql2 . "<br>" . $e->getMessage();
			}


		try {
			$conn2 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql2 = "SELECT COUNT(*) AS count FROM approval A WHERE A.username = '$usr' AND A.ammount > 0";

			foreach ($conn2->query($sql2) as $row2) {
				$item2 = $row2['count'];
			}
				
			$_SESSION['viewApprovePurchase'] = $item2;

			}
		catch(PDOException $e)
			{
			echo $sql2 . "<br>" . $e->getMessage();
			}
		/***** Figures out what options to display to user ******/




		$returnto = test_input2($_GET['returnto']);
		if ($returnto != '') {
			$headerinfo = "Location: " . $returnto;
		}
		else {
			$headerinfo = "Location: loggedin.php";
		}
		header($headerinfo);
	}
	else
	{
		header("Location: index.php?fail=Incorrect Username or Password");
	}

	}
catch(PDOException $e)
	{
	echo $sql . "<br>" . $e->getMessage();
	}

$conn = null;


?>