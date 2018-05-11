<?php
if(isset($_POST['trandate']) AND isset($_POST['amount']) AND isset($_POST['source'])){
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
	
	$sql = "INSERT INTO expense (EXTranDate,EXAmount,EXSource) VALUE ('".$_POST['trandate']."','".$_POST['amount']."','".$_POST['source']."')";
	
	if (mysqli_query($con, $sql)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($con);
	}
	//$_SESSION['branch_id']=mysqli_insert_id($con);
	mysqli_close($con);
}
if(isset($_POST['deletedata'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$sql = "DELETE FROM expense WHERE EXID ='".$_POST['deletedata']."'";
	
	if (mysqli_query($con, $sql)) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . mysqli_error($con);
	}
	
	mysqli_close($con);
}
if(isset($_POST['account'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$sql = "SELECT SUM(UNCurrentValue) AS money FROM unpaid AS UN INNER JOIN customer AS C ON UN.CustomerID = C.CustomerID WHERE C.Account = '".$_POST['account']."'";
	$result = mysqli_query($con, $sql);
		
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			echo $row['money'];
		}
	}
	mysqli_close($con);
}
?>