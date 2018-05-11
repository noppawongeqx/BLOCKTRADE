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
				<div class = "col-md-2">
					  <h3>วันที่เกิดค่าใช้จ่าย</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name = 'startdate' id='startdate'></h3>
				</div>
				<div class = "col-md-1">
				</div>
				<div class = "col-md-3">
					  <h3>สาเหตุ</h3>
				</div>
				<div class = "col-md-2">
					  <h3><select class="form-control" id="source">
					  	<option>SBL</option>
					  	<option>Short at bid</option>
					  	<option>คีร์ผิด</option>
					  	<option>อื่น ๆ</option>
					  </select></h3>
				</div>
		</div>
		<div class = "col-md-12">
				<div class = "col-md-2">
					  <h3>จำนวน</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="amount"></h3>
				</div>
				<div class = "col-md-1"><h3>บาท</h3>
				</div>
				<div class = "col-md-3">
				</div>
				<div class = "col-md-2">
				</div>
		</div>
		<div class="col-md-6 col-md-offset-3">
				<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">Save</button></h3>
		</div>
	</div>
	<div class = "row">
		<div class="col-md-10">
			<table class="table">
		      <caption><h3>All Expense</h3></caption>
		      <thead>
		        <tr>
		          <th>วันที่เกิดค่าใช้จ่าย</th>
		          <th>สาเหตุ</th>
		          <th style='text-align:right;'>จำนวน</th>
		          <th style='text-align:right;'>ลบ</th>
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
					$sql = "SELECT EXID,EXTranDate,EXAmount,EXSource from expense";
					$result = mysqli_query($con, $sql);
										  	
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$Newformat = strtotime($row['EXTranDate']);
							$myFormatForView = date("d/m/Y", $Newformat);
							echo "<tr>";
							echo "<td>".$myFormatForView."</td><td>". $row['EXSource']."</td><td style='text-align:right;'>".number_format($row['EXAmount'],2)."</td>";
							echo 	"<td style='text-align:right;'>
		  								<button class='btn btn-info' value='".$row['EXID']. "' onClick='finddata(this.value)'>Delete</button>
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
	var trandate = document.getElementById("startdate").value;
	var amount = document.getElementById("amount").value;
	var sel = document.getElementById("source");
	var source = sel.options[sel.selectedIndex].text;
	if ((source != "") && (trandate != "")){
	    $.ajax({
	        type: 'post',
	        url: 'expensesave.php',
	        data: {trandate: trandate, amount: amount, source: source},
	        success: function( data ) {
	        	alert( data );
	        	//redirect to member.php
	            window.location.href = "expense.php";
	            document.getElementById("startdate").value = "";
	        }
	    });
	}
	else {
		alert("��سҡ�͡���������ú");
	}
    
});

</script>
<script>
function finddata(value){
	$.ajax({
        type: 'post',
        url: 'expensesave.php',
        data: {deletedata: value},
        success: function( data ) {
        	alert( data );
        	window.location.href = "expense.php";
        }
    });
}
</script>
</html>