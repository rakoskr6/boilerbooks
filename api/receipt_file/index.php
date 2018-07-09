<?php
	/* This API provides the receipt file (either returned as a jpg or PDF. As input it takes the purchaseID
	/* Consider adding additional security to prevent certain people from seeing all receipts */

	include '../verify.php';

	$usr = $_SESSION['user'];

	$purchaseid = test_input($_GET["purchaseid"]);

	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "SELECT DATE_FORMAT(p.purchasedate,'%m/%d/%Y') as date, p.modifydate as mdate, p.item, p.purchasereason, p.vendor, p.committee, p.category, p.receipt, p.receipt_file, p.status,
		p.cost, p.comments, p.fundsource, p.fiscalyear, p.username
		, (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.username) purchasedby
		, (SELECT CONCAT(U.first, ' ', U.last) FROM Users U WHERE U.username = p.approvedby) approvedby
		 FROM Purchases p 
		 WHERE p.purchaseID = $purchaseid";
		//$stmt->execute();

		foreach ($conn->query($sql) as $row) {
			$receipt_file = $row['receipt_file'];
			$receipt = $row['receipt'];
		}

		if (!strcmp($receipt, "jpg")) {
			header("Content-type: image/jpeg");
		}
		elseif (!strcmp($receipt, "pdf")) {
			header("Content-type: application/pdf");
		}
		
		echo $receipt_file;
		
	}
	catch(PDOException $e) {
		echo "SQL Error:<br>";
		echo $sql . "<br>" . $e->getMessage();
		}

	$conn = null;
?>