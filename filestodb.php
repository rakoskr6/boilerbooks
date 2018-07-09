<?php
// Move this file to the receipts folder and run it once to tranfer files from that folder into the database. Ensure you check which files may have failed (if any) and manually fix them.

$title = 'Files to DB';
include '../menu.php';
?>

<div class="container">

<?php
	include '../dbinfo.php';

	$jpgs = ['jpeg','JPEG', 'jpg', 'JPG'];
	$pdfs = ['pdf', 'PDF'];
	
	// Get files in directory
	$dir = ".";
	$file_names = scandir($dir);
	
	$x = 0;
	foreach ($file_names as $file_name) {
		// Reset variables each loop
		$fail = false;
		$isUploadError = false;
		$x = $x + 1;
		
		/*** Extract purchase ID number and file type ***/
		preg_match("#_(\d+)\.(pdf|PDF|jpg|JPG|jpeg|JPEG)#",$file_name, $matches);
		
		$purchaseID = $matches[1];
		if (!empty($purchaseID)) { // if a purchaseID was found 
			if (in_array($matches[2], ['jpeg','JPEG', 'jpg', 'JPG'])) {
				$file_type = "jpg";
			}
			elseif (in_array($matches[2], ['pdf', 'PDF'])) {
				$file_type = "pdf";
			}
			else {
				$fail = true;
			}
		}
		else {
			$fail = true;
		}

		/*** Read file and upload to database ***/
		if (!$fail) {
			$fp = fopen($file_name,"r");
			if ($fp) { // if successful read from the file pointer
				$content = fread($fp, 16*1024*1024-1);
				fclose($fp);
				// Add slashes to the content so that it will escape special characters. TODO: look at  mysql_real_escape_string later
				$content = addslashes($content);
			}
			else {
				echo "Error reading " . $file_name . "<br>";
				$isUploadError = true;
			}

			if ($isUploadError === false)  {
				// Update sql database with the file and purchase info
				try {
					$cost = test_input(str_replace('$', '', $cost));
					$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set the PDO error mode to exception

					$sql = "UPDATE Purchases SET receipt='$file_type', receipt_file='$content' WHERE Purchases.purchaseID = '$purchaseID'";
					// use exec() because no results are returned
					$conn->exec($sql);
				} 
				catch (PDOException $e) {
					$sqlErr =  "SQL Statement: $sql <br>";
					$sqlErr .= "SQL Error: " . $e->getMessage();

					echo "Error uploading " . $file_name . "to database <br>";
					$isUploadError = true;
				}

				$conn = null; // close connection
			}
			echo "Done with " . $file_name . "<br>";
		}
		else {
			// list files that weren't imported into the database
			if ($file_name != "." and $file_name != "..") {
				echo $file_name . " not imported due to missing information<br>";
			}
		}
	}

	die();
	



?>
</div>