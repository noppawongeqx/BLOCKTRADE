<?php
if(isset($_POST['insertdata'])){
	$RDATE = $_POST['RDATE'];
	$Acctotal = $_POST['Acctotal'];
	$AccDTD = $_POST['AccDTD'];
	$AccMTD = $_POST['AccMTD'];
	$AccYTD = $_POST['AccYTD'];
	$Fairtotal = $_POST['Fairtotal'];
	$FairDTD = $_POST['FairDTD'];
	$FairMTD = $_POST['FairMTD'];
	$FairYTD = $_POST['FairYTD'];
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	$sql = "INSERT INTO revenue (RDate,TotalAccount,DTDAccount,MTDAccount,YTDAccount,TotalFair,DTDFair,MTDFair,YTDFair) VALUES ('$RDATE','$Acctotal','$AccDTD','$AccMTD','$AccYTD','$Fairtotal','$FairDTD','$FairMTD','$FairYTD')";
	
	if (mysqli_query($con, $sql)) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($con);
	}
	//$_SESSION['branch_id']=mysqli_insert_id($con);
	mysqli_close($con);
}
elseif(isset($_POST['updatedata'])){
	$Acctotal = $_POST['Acctotal'];
	$AccDTD = $_POST['AccDTD'];
	$AccMTD = $_POST['AccMTD'];
	$AccYTD = $_POST['AccYTD'];
	$Fairtotal = $_POST['Fairtotal'];
	$FairDTD = $_POST['FairDTD'];
	$FairMTD = $_POST['FairMTD'];
	$FairYTD = $_POST['FairYTD'];
	$RID = $_POST['RID'];
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
	
	$sql = "UPDATE revenue SET TotalAccount = '$Acctotal',DTDAccount = '$AccDTD',MTDAccount = '$AccMTD',YTDAccount = '$AccYTD',TotalFair = '$Fairtotal',DTDFair = '$FairDTD',MTDFair = '$FairMTD',YTDFair = '$FairYTD' WHERE RID = '$RID'";
	
	if (mysqli_query($con, $sql)) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($con);
	}
	mysqli_close($con);
}
?>