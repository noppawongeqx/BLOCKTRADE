<?php
	$position = $_POST['position'];
	$tranDate = $_POST['tranDate'];
	$account = $_POST['account'];
	$underlying = $_POST['underlying'];
	$spot = $_POST['spot'];
	$futureName = $_POST['futureName'];
	$futurePrice = $_POST['futurePrice'];
	$startVol = $_POST['startVol'];
	$currentVol = $_POST['currentVol'];
	$value = $_POST['value1'];
	$percentUp = $_POST['percentUp'];
	$upfront = $_POST['upfront'];
	$percentTotal = $_POST['percentTotal'];
	$total = $_POST['total'];
	$serie = $_POST['serie'];
	$CTOID = $_POST['CTOID'];
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
	//Check Account
	$sql = "SELECT COUNT(*) AS TOTAL FROM `customer` WHERE Account = '$account'";
	$result = mysqli_query($con,$sql);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_array($result)) {
			$check_select = $row['TOTAL'];
		}
	}
	//Insert AccountID to Database if not exist
	if($check_select == 0){
		$sql = "INSERT INTO customer (Account) VALUES ('$account')";
		if (mysqli_query($con, $sql)) {
			echo "Account record stored successfully<br>";
		} else {
			echo "Error: " . $sql . "<br>" . mysqli_error($con);
		}
	}
	
	$sql = "UPDATE customertransactionopen SET CTOTranDate = '$tranDate',CTOPosition = '$position',CTOUnderlying = '$underlying',CTOFutureName = '$futureName',CTOVolumeStart = '$startVol',CTOVolumeCurrent = '$currentVol',CTOSpot = '$spot',CTOValue = '$value',CTOUpfrontInterest = '$upfront',CTOTotalInterest = '$total',CTOPercentInterest = '$percentTotal',CTOPercentUpfront = '$percentUp',CTOFuturePrice = '$futurePrice',SerieID = (SELECT SerieID FROM serie WHERE SName = '$serie'),CustomerID = (SELECT CustomerID FROM customer WHERE Account = '$account') WHERE CTOID = '".$CTOID."'";
	
	if (mysqli_query($con, $sql)) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($con);
	}

	mysqli_close($con);
?>