<?php
if(isset($_POST['min']) AND isset($_POST['max']) AND isset($_POST['tick'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	$sql = "INSERT INTO tick (Min,Max,Tick) VALUES ('".$_POST['min']."','".$_POST['max']."','".$_POST['tick']."')";
	
	if (mysqli_query($con, $sql)) {
		echo "Save tick successfully";
	} else {
		echo "Error: " . $sql . "\r\n" . mysqli_error($con);
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
	$sql = "DELETE FROM tick WHERE TickID='".$_POST['deletedata']."'";
	
	if (mysqli_query($con, $sql)) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . mysqli_error($con);
	}
	mysqli_close($con);
}
?>