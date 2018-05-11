<?php
if(	isset($_POST['Stock']) AND isset($_POST['XDDate']) AND isset($_POST['dividend']) 
	AND isset($_POST['volume']) AND isset($_POST['position']) AND isset($_POST['perout'])){

	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$sql = "INSERT INTO dividend (XDDate,DStock,DDividend,DPercentOut,DVolume,DCurrentVolume,DCompanyPosition) VALUES ('".$_POST['XDDate']."','".$_POST['Stock']."','".$_POST['dividend']."','".$_POST['perout']."','".$_POST['volume']."','".$_POST['volume']."','".$_POST['position']."')";
	if (mysqli_query($con, $sql)) {
		echo "Save dividend successfully\r\n'".$_POST['XDDate']."','".$_POST['Stock']."','".$_POST['dividend']."','".$_POST['perout']."','".$_POST['volume']."','".$_POST['volume']."','".$_POST['position']."'";
	} else {
		echo "Error saving dividend: \n".mysqli_error($con)."\n".$sql ;
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

	$sql = "DELETE FROM dividend WHERE DID='".$_POST['deletedata']."'";
	if (mysqli_query($con, $sql)) {
		echo "Delete dividend successfully";
	} else {
		echo "Error deleting dividend: \n".mysqli_error($con)."\n".$sql ;
	}
	mysqli_close($con);
}
?>