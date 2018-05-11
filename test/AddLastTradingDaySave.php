<?php
if(isset($_POST['SName']) AND isset($_POST['LTD']) AND isset($_POST['multiplier'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	$sql = "INSERT INTO serie (SName,Underlying,SLastTradingDay,SMultiplier) VALUES ('".$_POST['SName']."','".$_POST['underlying']."','".$_POST['LTD']."','".$_POST['multiplier']."')";
	if (mysqli_query($con, $sql)) {
		echo "Save New Serie successfully\r\n'".$_POST['SName']." ".$_POST['underlying']."','".$_POST['LTD']."','".$_POST['multiplier']."'";
	} else {
		echo "Error saving new serie: \n".mysqli_error($con)."\n".$sql ;
	}
	mysqli_close($con);
}
if(isset($_POST['deletedata'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "DELETE FROM serie WHERE SerieID ='".$_POST['deletedata']."'";
	if (mysqli_query($con, $sql)) {
		echo "Delete dividend successfully";
	} else {
		echo "Error deleting serie: \n".mysqli_error($con)."\n".$sql ;
	}
	mysqli_close($con);
}
?>