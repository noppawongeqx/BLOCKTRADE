<?php
	$position = $_POST['position'];
	$tranDate = $_POST['tranDate'];
	$account = $_POST['account'];
	$underlying = $_POST['underlying'];
	$cost = $_POST['cost'];
	$volume = $_POST['vol'];
	$value = $_POST['value1'];
	$cash = $_POST['cash'];
	$serie = $_POST['serie'];
	$COTID = $_POST['COTID'];
	$serie = substr($serie,0,4);
	
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	$sql = "UPDATE companytransaction SET COTDate = '$tranDate',COTPosition = '$position',COTUnderlying = '$underlying',COTVolume = '$volume',COTCost = '$cost',COTValue = '$value',COTCash = '$cash',SerieID = (SELECT SerieID FROM serie WHERE SName = '$serie'),CustomerID = (SELECT CustomerID FROM customer WHERE Account = '$account') WHERE CompanyTransactionID = '".$COTID."'";
	
	if (mysqli_query($con, $sql)) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($con);
	}

	mysqli_close($con);
?>