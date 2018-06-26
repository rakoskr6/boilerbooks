<?php
	$title = 'Boiler Books';
	$completeactive = "active";
	include '../menu.php';
?>

<div class="container">

	<?php
		include '../dbinfo.php';
		// define variables and set to empty values
		$cost = $comments = $receipt = $purchasedate = "";
		$committee = $_SESSION['committeec'];
		$item = $_SESSION['itemc'];
		$purchaseID = $_SESSION['purchaseIDc'];

		if ($purchaseID == '') {
			echo 'no purchase';
			header("Location: index.php");
		} else {
			$sqler = $uploaderr = '';
			$comments = test_input($_POST["comments"]);
			$cost = test_input($_POST["cost"]);
			$purchasedate = test_input($_POST["purchasedate"]);
			$purchasedateorig = $purchasedate;

			$purchasedate = str_replace('-','/',$purchasedate);
			$purchasedate = date('Y-m-d H:i:s', strtotime($purchasedate));  
			$purchasedatetemp = $purchasedate;
			$receipt = $servername . "/";
			$target_dir = "../receipts/";
			$target_dir_save = "/receipts/";
			$FileType = pathinfo($target_dir . basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
			$target_file = $target_dir . $committee . "_" . $item . "_" . $purchaseID . "." . $FileType;
			$target_file_save = $target_dir_save . $committee . "_" . $item . "_" . $purchaseID . "." . $FileType;


			$isUploadError = false;
			$uploadErrMessage = "";

			$okayFileTypes = ['pdf', 'jpeg', 'jpg'];
			$maxFileSize = 2 * 1024 * 1024; // MB * KB * B

			// Make sure it actually uploaded
			if ($_FILES["fileToUpload"]["size"] == 0) {
				$uploadErrMessage = $uploadErrMessage . "Your receipt file size is 0 bytes<br>";
				$isUploadError = true;
			} 

			// Check file size
			if ($_FILES["fileToUpload"]["size"] > $maxFileSize) {
				$uploadErrMessage = $uploadErrMessage . "Your receipt must be smaller than 2MB<br>";
				$isUploadError = true;
			}

			// Allow certain file formats
			if (!in_array(strtolower($FileType), $okayFileTypes)) {
				$uploadErrMessage = $uploadErrMessage . "Only PDFs and JPEGs are allowed<br>";
				$isUploadError = true;
			} 

			// Check if file already exists
			if (file_exists($target_file)) {
				$uploadErrMessage = $uploadErrMessage . " Your file already exists on the server<br>";
				$isUploadError = true;
			} 

			// Only run if we succeed in passing the file checks
			if ($isUploadError === false)  {

				// Attempts to move file to the server
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>";
					$receipt = str_replace(' ', '%20', $target_file_save);
				} else {

					$uploadErrMessage = $uploadErrMessage . "Could not transfer file to destination on server<br>";
					$isUploadError = true;
					break;
				}

				// Update sql database with the file and purchase info
				try {
					$cost = test_input(str_replace('$', '', $cost));
					$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

					// set the PDO error mode to exception
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					$sql = "UPDATE Purchases SET modifydate = NOW(), purchasedate='$purchasedate', cost='$cost', status='Purchased', comments='$comments',
						receipt='$receipt' WHERE Purchases.purchaseID = '$purchaseID'";

					// use exec() because no results are returned
					$conn->exec($sql);
				} catch (PDOException $e) {
					$sqlErr =  "SQL Statement: $sql <br>";
					$sqlErr .= "SQL Error: " . $e->getMessage();

					$uploadErrMessage = $uploadErrMessage . "There was an error inserting the row into the database<br>";
					$isUploadError = true;
					break;
				}

				$conn = null;
				$to = "purdue.ieee.treasurer@gmail.com";
				$subject = "New purchase by $committee";
				$message = "<p>Please visit money.pieee.org at your earliest convenience to begin the reimbursement process for $item.</p>";
				$header = "From:ieeeboilerbooks@gmail.com \r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-type: text/html\r\n";

				if ($sendemail == 1) {
					$retval = mail($to, $subject, $message, $header);
					if ($retval == true ) {
					//echo "Message sent successfully...";
					} else {
					//echo "Message could not be sent...";
					}
				}

				$_SESSION['usernamec'] = '';
				$_SESSION['itemc'] =  '';
				$_SESSION['reasonc'] =  '';
				$_SESSION['vendorc'] = '';
				$_SESSION['committeec'] = '';
				$_SESSION['categoryc'] = '';
				$_SESSION['costc'] = '';
				$_SESSION['statusc']= '';
				$_SESSION['commentsc']= '';
				$_SESSION['statusc']= '';
				$_SESSION['purchaseIDc']= '';
			}
		}
	?>

	<div style='text-align:center; line-height: 2.5em'>
		<?php
			if ($isUploadError == false) {
				//header('Location: index.php');
				echo "<h1> Your purchase was successful </h1>";
			} else {
				echo "<h1> Oops... something went wrong</h1>";
				
				echo "<h2> $uploadErrMessage </h2>";

				echo "<h2> Debug Info </h2>";
				echo "<h4>";
				echo "Purchase Date Original: $purchasedateorig <br><br>";
				echo "Changed Purchase Date: $purchasedate <br><br>";
				echo "File Type: $FileType <br><br>";
				echo "File Size: " . $_FILES["fileToUpload"]["size"] . "<br><br>";
				echo "Receipt: $receipt <br><br>";
				echo "Target File: $target_file <br><br>";
				echo "Target File Save: $target_file_save <br><br>";
				echo "Temp File Name: " . $_FILES["fileToUpload"]["tmp_name"] . "<br><br>";

				if (!empty($sqlErr)) {
					echo "SQL Error: <br><br>";
					echo "$sqlErr <br>";
				}
				echo "</h4>";
			}
		?>
	</div>
</div>