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
  	<link rel="stylesheet" href="/resources/demos/style.css">
        <script>
		  $(function() {
		    $( "#startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
		  });
		  </script>
<title>Add Profit</title>
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
		<div class="col-md-12">
			<table class="table">
		      <caption>Current Data</caption>
		      <thead>
		        <tr>
		          <th style='text-align:center;'>Date</th>
		          <th style='text-align:center;'>Acc Total</th>
		          <th style='text-align:center;'>Fair Total</th>
		          <th style='text-align:center;'>Acc DTD</th>
		          <th style='text-align:center;'>Acc MTD</th>
		          <th style='text-align:center;'>Acc YTD</th>
		          <th style='text-align:center;'>Fair DTD</th>
		          <th style='text-align:center;'>Fair MTD</th>
		          <th style='text-align:center;'>Fair YTD</th>
		          <th>Action</th>
		        </tr>
		      </thead>
		      <tbody>
		      <tr>
		      <td><input type="text" class="form-control" name = 'startdate' id='startdate'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='Atotal'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='Ftotal'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='ADTD'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='AMTD'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='AYTD'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='FDTD'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='FMTD'></td>
		      <td><input type='text' style='text-align:right;' class='form-control' id='FYTD'></td>
		      <td><button type="button" id="senddata" class="btn btn-primary">Insert</button></td>
		      </tr>
		      <?php $servername = "172.16.24.235";
					$username = "mycustom";
					$password = "mypass";
					$dbname = "blocktradetest";
										  	
					// Create connection
					$con = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$con) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$sql = "SELECT RID,RDate,TotalAccount,DTDAccount,MTDAccount,YTDAccount,TotalFair,DTDFair,MTDFair,YTDFair FROM `revenue` ORDER BY RDate DESC";
					$result = mysqli_query($con, $sql);
										  	
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$Newformat = strtotime($row['RDate']);
							$myFormatForView = date("d/m/Y", $Newformat);
							echo "<tr>";
							echo "<td>".$myFormatForView."</td><td><input type='text' style='text-align:right;' class='form-control' id='Acctotal".$row['RID']."' value='".$row['TotalAccount']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='FairTotal".$row['RID']."' value='".$row['TotalFair']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='AccDTD".$row['RID']."' value='".$row['DTDAccount']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='AccMTD".$row['RID']."' value='".$row['MTDAccount']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='AccYTD".$row['RID']."' value='".$row['YTDAccount']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='FairDTD".$row['RID']."' value='".$row['DTDFair']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='FairMTD".$row['RID']."' value='".$row['MTDFair']."'></td>";
							echo "<td><input type='text' style='text-align:right;' class='form-control' id='FairYTD".$row['RID']."' value='".$row['YTDFair']."'></td>";
							echo 	"<td>
		  								<button class='btn btn-info' value='".$row['RID']. "' onClick='finddata(this.value)'>Update</button>
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
$('#senddata').click(function(){
	var RDATE = document.getElementById("startdate").value;
	var Acctotal = document.getElementById("Atotal").value;
	var Fairtotal = document.getElementById("Ftotal").value;
	var AccDTD = document.getElementById("ADTD").value;
	var AccMTD = document.getElementById("AMTD").value;
	var AccYTD = document.getElementById("AYTD").value;
	var FairDTD = document.getElementById("FDTD").value;
	var FairMTD = document.getElementById("FMTD").value;
	var FairYTD = document.getElementById("FYTD").value;
	if ((Acctotal != "") && (Fairtotal != "") && (AccYTD != "") && (FairYTD != "")){
	    $.ajax({
	        type: 'post',
	        url: 'addDailyRevenueSave.php',
	        data: {insertdata: 1,RDATE: RDATE, Acctotal: Acctotal, Fairtotal: Fairtotal,AccDTD: AccDTD, AccMTD: AccMTD, AccYTD: AccYTD, FairDTD: FairDTD, FairMTD: FairMTD, FairYTD: FairYTD},
	        success: function( data ) {
	        	alert( data );
	        	//redirect to member.php
	            window.location.href = "addDailyRevenue.php";
	        }
	    });
	}
	else {
		alert("กรุณากรอกข้อมูลให้ครบ");
	}
    
});

</script>
<script>
function finddata(RID){
	var Acctotal = document.getElementById("Acctotal"+RID).value;
	var Fairtotal = document.getElementById("FairTotal"+RID).value;
	var AccDTD = document.getElementById("AccDTD"+RID).value;
	var AccMTD = document.getElementById("AccMTD"+RID).value;
	var AccYTD = document.getElementById("AccYTD"+RID).value;
	var FairDTD = document.getElementById("FairDTD"+RID).value;
	var FairMTD = document.getElementById("FairMTD"+RID).value;
	var FairYTD = document.getElementById("FairYTD"+RID).value;
	$.ajax({
        type: 'post',
        url: 'addDailyRevenueSave.php',
        data: {updatedata: 1, Acctotal: Acctotal, Fairtotal: Fairtotal,AccDTD: AccDTD, AccMTD: AccMTD, AccYTD: AccYTD, FairDTD: FairDTD, FairMTD: FairMTD, FairYTD: FairYTD, RID: RID},
        success: function( data ) {
        	alert( data );
        	window.location.href = "addDailyRevenue.php";
        }
    });
}
</script>
</html>