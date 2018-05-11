<!DOCTYPE html>
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
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
		  <script>
		  $(function() {
		    $( "#enddate" ).datepicker({ dateFormat: "yy-mm-dd" });
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
require 'menu.php';?>
<div class = "container">
	<div class = "row">
		<div class = "col-md-12">
				<div class = "col-md-3">
					  <h3>Account</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="account" placeholder="e.g. Customer Account Number"></h3>
				</div>
				<div class = "col-md-1">
				</div>
				<div class = "col-md-3">
					  <h3>Underlying</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name='underlying' style="text-transform: uppercase" id='underlying' placeholder="e.g. PTT"></h3>
				</div>
				
		</div>
		<div class = "col-md-12">
				<div class = "col-md-3">
					  <h3>Marketing Name</h3>
				</div>
				<div class = "col-md-4">
					  <h3><select class="form-control" id="name">
					  	<option>All</option>
							  <?php $servername = "172.16.24.235";
								  	$username = "mycustom";
								  	$password = "mypass";
								  	$dbname = "blocktradetest";
								  	
								  	// Create connection
								  	$con = mysqli_connect($servername, $username, $password, $dbname);
									mysqli_set_charset($con,"utf8");
								  	// Check connection
								  	if (!$con) {
								  		die("Connection failed: " . mysqli_connect_error());
								  	}
								  	$sql = "SELECT distinct MKTName FROM customer";
								  	$result = mysqli_query($con, $sql);
								  	
								  	if (mysqli_num_rows($result) > 0) {
								  		while($row = mysqli_fetch_assoc($result)) {
											echo "<option>".$row['MKTName']."</option>";
								  		}
								  	} 
								  	mysqli_close($con);
							?>
							</select></h3>
				</div>
				<div class = "col-md-4">
				</div>
				
		</div>
		<div class = "col-md-12">
				<div class = "col-md-3">
					  <h3>From (Date)</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name = 'startdate' id='startdate'></h3>
				</div>
				<div class = "col-md-1">
				</div>
				<div class = "col-md-3">
					  <h3>To (Date)</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name = 'enddate' id='enddate'></h3>
				</div>
				
		</div>
		<div class="col-md-6 col-md-offset-3">
					<h3><button type="button" id="search" class="btn btn-primary btn-lg btn-block" onclick="searchResult()">Search</button></h3>
		</div>
	</div>
	<div id="resulthere"></div>
</div>
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
	document.getElementById("enddate").value = defaultdate;
});
function searchResult(){
	var account = document.getElementById("account").value;
	var startdate = document.getElementById("startdate").value;
	var enddate = document.getElementById("enddate").value;
	var underlying = document.getElementById("underlying").value;
	var sel = document.getElementById("name");
	var name = sel.options[sel.selectedIndex].text;
	if(startdate != "" && enddate != ""){
		$.ajax({
	        type: 'post',
	        url: 'rewardSearch.php',
	        data: {account: account, startdate: startdate, enddate: enddate, underlying: underlying, name: name},
	        success: function( data ) {
	        	document.getElementById("resulthere").innerHTML = data;
	        }
	    });
	}
	else {
		alert("กรูณาใส่วันที่");
	}
}
</script>
</body>
</html>