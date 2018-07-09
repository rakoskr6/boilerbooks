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
	$user = $_SESSION['usernamec'];

	if (!empty($purchaseID)) { // only do stuff if there is actually a purchase to complete 
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

		$file_save_name = $committee . "_" . $user . "_" . $item . "_" . $purchaseID . "." . $FileType;
		$file_save_name = str_replace(' ', '_', $file_save_name);

		$target_file = $target_dir . $file_save_name;
		$target_file_save = $target_dir_save . $file_save_name;

		$isUploadError = false;
		$uploadErrMessage = "";
		$sqlErr = "";

		$okayFileTypes = ['pdf', 'jpeg', 'jpg'];
		$maxFileSize = 16 * 1024 * 1024 - 1; // MB * KB * B - 1 (size of medium blob)

		// Make sure it actually uploaded
		if ($_FILES["fileToUpload"]["size"] == 0) {
			$uploadErrMessage = $uploadErrMessage . "Your receipt file size is 0 bytes.<br>";
			$uploadErrMessage = $uploadErrMessage . "This may be because your file was greater than the max upload size.<br>";
			$uploadErrMessage = $uploadErrMessage . "Make sure your receipt is smaller than 2MB.<br>";
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

		// Only run if we succeed in passing the file checks prepare the file for database upload
		if ($isUploadError === false)  {
			// Open submitted file for reading
			$fp = fopen($_FILES["fileToUpload"]["tmp_name"],"r");
			if ($fp) { // if successful read from the file pointer
				$content = fread($fp, $_FILES['fileToUpload']['size']);
				fclose($fp);
				// Add slashes to the content so that it will escape special characters. TODO: look at  mysql_real_escape_string later
				$content = addslashes($content);

				// Save image type
				$receipt = strtolower($FileType);

				// Rename jpeg file endings to jpg for consistency
				if (!strcmp($receipt, "jpeg")) {
					$receipt = "jpg";
				}

			}
			else {
				$isUploadError = true;
			}
		}

		if ($isUploadError === false)  {
			// Update sql database with the file and purchase info
			try {
				$cost = test_input(str_replace('$', '', $cost));
				$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set the PDO error mode to exception

				$sql = "UPDATE Purchases SET modifydate = NOW(), purchasedate='$purchasedate', cost='$cost', status='Purchased', comments='$comments',
				receipt='$receipt', receipt_file='$content' WHERE Purchases.purchaseID = '$purchaseID'";

				// use exec() because no results are returned
				$conn->exec($sql);
			} 
			catch (PDOException $e) {
				$sqlErr =  "SQL Statement: $sql <br>";
				$sqlErr .= "SQL Error: " . $e->getMessage();

				$uploadErrMessage = $uploadErrMessage . "There was an error inserting the row into the database<br>";
				$isUploadError = true;
			}

			$conn = null; // close connection
		}

		if ($isUploadError === false)  {
			if ($sendemail == 1) {
				$to = "purdue.ieee.treasurer@gmail.com";
				$subject = "New purchase by $committee";
				$message = "<p>Please visit money.pieee.org at your earliest convenience to begin the reimbursement process for $item.</p>";
				$header = "From:ieeeboilerbooks@gmail.com \r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-type: text/html\r\n";

				$retval = mail($to, $subject, $message, $header);

				if ($retval == true ) {
					//echo "Message sent successfully...";
				} 
				else {
					//echo "Message could not be sent...";

				}
			}
		}
	
		// Clear variables to ensure we don't accidentally complete purchases multiple times, thus sending many emails
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
?>

<div style='text-align:center; line-height: 2.5em'>
	

<?php
	// Success or fail messages
	
	if (!empty($purchaseID) && $isUploadError == false) {
		echo "<h1> Your purchase was successful </h1>";
	} 
	else {
		echo "<h1> Oops... something went wrong</h1>";

		if (empty($purchaseID)) {
			echo '<h4>Bad form data - Please go back and reselect the purchase</h4>';
		}

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
