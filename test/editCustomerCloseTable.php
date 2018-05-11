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
$CTCID = $_GET['id'];
$position = $_GET['p'];
$transactionDate = $_GET['Tdate'];
$underlying = $_GET['ui'];
$serie = $_GET['s'];
$futureName = $_GET['futureName'];
$volume = $_GET['vol'];
$futurePrice = $_GET['cost'];
$totalInterest = $_GET['totalI'];
$netInterest = $_GET['net'];
$flag = $_GET['flag'];
$spot = $_GET['spot'];
$value = $_GET['value'];

//First Row
echo "<table class='table'>";
echo "<thead>
		        <tr class='info'>
		          <th>Transaction Date</th>
		          <th>Account</th>
		          <th>Positon</th>
    			  <th>Underlying</th>
				  <th>Underlying Price</th>
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
echo "<td><input type='text'' class='form-control' id='spot' value='$spot'></td>";
echo "</tr>";
echo "</tbody></table>";

//Second Row
echo "<table class='table'>";
echo "<thead>
		        <tr class='info'>
				  <th>Futures Name</th>
		          <th>Futures Price</th>
		          <th>Volume</th>
    			  <th>Value</th>
		          <th>Total Interest</th>
		        </tr>
		      </thead>
		      <tbody>";
echo "<tr class='warning'>";
echo "<td><input type='text'' class='form-control' style='text-transform: uppercase' id='futurename' value='$futureName'></td>";
echo "<td><input type='text'' class='form-control' id='futureprice' value='$futurePrice'></td>";
echo "<td><input type='text'' class='form-control' id='vol' value='$volume'></td>";
echo "<td><input type='text'' class='form-control' id='value1' value='$value'></td>";
echo "<td><input type='text'' class='form-control' id='total' value='$totalInterest'></td>";
echo "</tr>";
echo "</tbody></table>";

//Third Row
echo "<table class='table'>";
echo "<thead>
		        <tr class='info'>
				  <th>Net Interest</th>
		          <th>Flag</th>
    			  <th>Serie</th>
		        </tr>
		      </thead>
		      <tbody>";
echo "<tr class='warning'>";
echo "<td><input type='text'' class='form-control' id='net' value='$netInterest'></td>";
echo "<td><select class='form-control' id='flag'><option";
if($flag == "Normal")
	echo " selected='selected'>".$flag."</option><option>Flag</option>";
elseif($flag == "Flag")
	echo "selected='selected'>".$flag."</option><option>Normal</option>";
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
							
echo"</select></td>";
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
	var spot = document.getElementById("spot").value;
	var futureName = document.getElementById("futurename").value;
	var futurePrice = document.getElementById("futureprice").value;
	var vol = document.getElementById("vol").value;
	var value1 = document.getElementById("value1").value;
	var total = document.getElementById("total").value;
	var net = document.getElementById("net").value;
	sel = document.getElementById("flag");
	var flag = sel.options[sel.selectedIndex].text;
	var sel = document.getElementById("series");
	var serie = sel.options[sel.selectedIndex].text;
	if ((position != "") && (tranDate != "") && (underlying != "") && (account != "") && (spot != "") && (futureName != "") && (futurePrice != "") && (value1 != "") && (net != "") && (total != "")){
		$.ajax({
	        type: 'post',
	        url: 'editCustomerCloseSave.php',
	        data: {CTCID: <?php echo $CTCID;?>, position: position, tranDate: tranDate, account: account, underlying: underlying, spot: spot, futureName: futureName, futurePrice: futurePrice, vol: vol, value1: value1, net: net, total: total, serie: serie, flag :flag},
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