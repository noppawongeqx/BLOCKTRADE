<?php
if(isset($_POST['MKTID']) AND isset($_POST['MKTName'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con,"utf8");
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "UPDATE customer SET MKTID = '".$_POST['MKTID']."',MKTName = '".$_POST['MKTName']."',TEAM = '".$_POST['TEAM']."' WHERE CustomerID = '".$_POST['CustomerID']."'";
		
	if (mysqli_query($con, $sql)) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($con);
	}
	mysqli_close($con);
}
?>