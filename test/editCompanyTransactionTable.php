<!DOCTYPE html>
<html lang="th">
<head>
	<meta http-equiv="Content-Language" content="th"> 
	<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <link href="css/styles.css" rel="stylesheet">
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
  	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  	<!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
        <script>
		  $(function() {
		    $( "#startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
		  });
		  </script>
<title>Calculate Block Trade Price</title>
</head>
<body>
<?php 
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if(!isset($_SESSION['loggedin'])){
	header('Location: login.php');
	exit();
}
require_once 'menu.php';?>
<div class="contrainer">
	<div class="row">
		<div class="col-md-12">
<?php

$account = $_GET['acc'];
$COTID = $_GET['id'];
$position = $_GET['p'];
$transactionDate = $_GET['Tdate'];
$underlying = $_GET['ui'];
$serie = $_GET['s'];
$volume = $_GET['vol'];
$cost = $_GET['cost'];
$cash = $_GET['cash'];
$value = $_GET['value'];

//First Row
echo "<table class='table'>";
echo "<thead>
		        <tr class='info'>
		          <th>Transaction Date</th>
		          <th>Account</th>
		          <th>Positon</th>
    			  <th>Underlying</th>
				  <th>Cost</th>
		        </tr>
		      </thead>
		      <tbody>";
echo "<tr class='warning'>";
echo "<td><input type='text'' class='form-control' id='startdate' value='$transactionDate'></td>";
echo "<td><input type='text'' class='form-control' id='account' value='$account'></td>";
echo "<td><select class='form-control' id='position'><option";
if($position == "Long")
	echo " selected='selected'>".$position."</option><option>Short</option>";
elseif($position == "Short")
	echo "selected='selected'>".$position."</option><option>Long</option>";
echo "</select>";
echo "<td><input type='text'' class='form-control' style='text-transform: uppercase' id='underlying' value='$underlying'></td>";
echo "<td><input type='text'' class='form-control' id='cost' value='$cost'></td>";
echo "</tr>";
echo "</tbody></table>";

//Second Row
echo "<table class='table'>";
echo "<thead>
		        <tr class='info'>
		          <th>Volume</th>
    			  <th>Value</th>
		          <th>Cash</th>
    			  <th>Serie</th>
		        </tr>
		      </thead>
		      <tbody>";
echo "<tr class='warning'>";
echo "<td><input type='text'' class='form-control' id='vol' value='$volume'></td>";
echo "<td><input type='text'' class='form-control' id='value1' value='$value'></td>";
echo "<td><input type='text'' class='form-control' id='cash' value='$cash'></td>";
echo "<td><select class='form-control' id='series'>";
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
$sql = "SELECT SName,SLastTradingDay,Underlying FROM `serie` WHERE SLastTradingDay >= CURDATE() ORDER BY SLastTradingDay";
$result = mysqli_query($con, $sql);
	
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
		if($serie == $row['SName'])
			echo "<option selected='selected'>".$row['SName']."  ".$row['Underlying']."</option>";
		else
			echo "<option>".$row['SName']."  ".$row['Underlying']."</option>";
	}
}
mysqli_close($con);
echo "</tr>";
echo "</tbody></table>";

?>

		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<h3><button type="button" class="btn btn-lg btn-primary btn-block" id="save">Save</button></h3>
		</div>
	</div>
	
</div>
</body>
<script type="text/javascript">
$('#save').click(function(){
	var tranDate = document.getElementById("startdate").value;
	var account = document.getElementById("account").value;
	var sel = document.getElementById("position");
	var position = sel.options[sel.selectedIndex].text;
	var underlying = document.getElementById("underlying").value;
	var cost = document.getElementById("cost").value;
	var vol = document.getElementById("vol").value;
	var value1 = document.getElementById("value1").value;
	var cash = document.getElementById("cash").value;
	var sel = document.getElementById("series");
	var serie = sel.options[sel.selectedIndex].text;
	if ((position != "") && (tranDate != "") && (underlying != "") && (account != "") && (value1 != "")){
		$.ajax({
	        type: 'post',
	        url: 'editCustomerCloseSave.php',
	        data: {COTID: <?php echo $COTID;?>, position: position, tranDate: tranDate, account: account, underlying: underlying, cost: cost, vol: vol, value1: value1, cash: cash, serie: serie},
	        success: function( data ) {
	        	alert( data );
	        	//redirect to member.php
	            window.location.href = "index.php";
	        }
	    });
	}
	else{
		alert("กรอกข้อมูลให้ครบ หรือ กด Calculate ก่อน");
	}
});
</script>
<script>
$(document).ready(function(){
	var defaultdate = new Date();
	var dd = defaultdate.getDate();
	var mm = defaultdate.getMonth()+1; //January is 0!
	var yyyy = defaultdate.getFullYear();

	if(dd<10) {
	    dd='0'+dd
	} 

	if(mm<10) {
	    mm='0'+mm
	} 

	defaultdate = yyyy+'-'+mm+"-"+dd;
	document.getElementById("startdate").value = defaultdate;
});
	$(function() {
		$( "#startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
</script>
</html>