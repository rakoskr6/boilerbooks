<?php
	// Performs the user login and verification

	include 'dbinfo.php';

	// define variables and set to empty values
	$psw = $usr = "";

	$psw = ($_POST["psw"]);
	$usr = test_input($_POST["usr"]);

	// run query to get hashed password for selected user
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


		// Verifies password
		if (password_verify($psw,$dbpsw))
		{
			$_SESSION['user'] = $usr;
			
			/***** Figures out what options to display to user ******/

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
				$_SESSION['viewIncome'] = $item2;

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



			$randNum = base64_encode(random_bytes(64));

			// string replace because I'm sturgling with URL encoding 
	  		$randNum = str_replace('+','_',$randNum);
	  		$randNum = str_replace('/','-',$randNum);
	  		$randNum = str_replace('=','-',$randNum);

			$randNumEn = password_hash($randNum,PASSWORD_DEFAULT);

			try {
				$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql = "UPDATE Users SET apikeygentime = NOW(), apikey='$randNumEn', modifydate=NOW() WHERE username = '$usr'";
				$conn->exec($sql);
				$_SESSION['apikey'] = $randNum;
			}
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}

		$conn = null;




		$_SESSION['fiscalyear'] = '2017-2018'; // initilize for viewing committee expenses

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
