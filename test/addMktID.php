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
<title>Add Marketing Name and Marketing ID</title>
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
		<div class="col-md-10">
			<table class="table">
		      <caption><h3>Account and IC Information</h3></caption>
		      <thead>
		        <tr>
		          <th>Account</th>
		          <th>Marketing ID</th>
		          <th>Marketing Name</th>
		          <th>Team</th>
		          <th>Action</th>
		        </tr>
		      </thead>
		      <tbody>
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
					$sql = "SELECT CustomerID,Account,MKTID,MKTName,Team from customer";
					$result = mysqli_query($con, $sql);
										  	
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							echo "<tr>";
							echo "<td>".$row['Account']."</td><td><input type='text' class='form-control' id='MKTID".$row['CustomerID']."' value='".$row['MKTID']."'></td><td><input type='text' class='form-control' id='MName".$row['CustomerID']."' value='".$row['MKTName']."'></td><td><input type='text' class='form-control' id='TEAM".$row['CustomerID']."' value='".$row['Team']."'></td>";
							echo 	"<td style='text-align:right;'>
		  								<button class='btn btn-info' value='".$row['CustomerID']. "' onClick='finddata(this.value)'>Update</button>
		  							</td>";
		    				echo "</tr>";
						}
					} 
					mysqli_close($con);
				?>
		      </tbody>
		    </table>
		</div>
	</div>
</div>
</body>
<script>
function finddata(CustomerID){
	var MKTID = document.getElementById("MKTID"+CustomerID).value;
	var MKTName = document.getElementById("MName"+CustomerID).value;
	var TEAM = document.getElementById("TEAM"+CustomerID).value;
		$.ajax({
	        type: 'post',
	        url: 'addMktIDSave.php',
	        data: {CustomerID: CustomerID,MKTID: MKTID,MKTName: MKTName,TEAM: TEAM},
	        success: function( data ) {
	        	alert( data );
	        	window.location.href = "addMktID.php";
	        }
	    });
}
</script>
</html>