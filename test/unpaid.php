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
		require 'menu.php';
	?>
<div class = "container">
	<div class = "row">
		<div class = "col-md-12">
				<div class = "col-md-2">
					  <h3>account</h3>
				</div>
				<div class = "col-md-3">
					  <h3><input type="text" class="form-control" id="account" placeholder="e.g. 6149205" autofocus></h3>
				</div>
				<div class = "col-md-1"></div>
				<div class = "col-md-2">
					  <h3>open date</h3>
				</div>
				<div class = "col-md-3">
					  <h3><input type="text" class="form-control" name = 'startdate' id='startdate'></h3>
				</div>
		</div>
		<div class = "col-md-12">
				<div class = "col-md-2">
					  <h3>amount (baht)</h3>
				</div>
				<div class = "col-md-3">
					  <h3><input type="text" class="form-control" id="amount" placeholder="e.g. 1,000.00" ></h3>
				</div>
				<div class = "col-md-1"></div>
				<div class = "col-md-2">
					  <h3>stock</h3>
				</div>
				<div class = "col-md-3">
					  <h3><input type="text" class="form-control" name='stock'  id='stock' style="text-transform: uppercase" placeholder="e.g. PTT"></h3>
				</div>
		</div>
		<div class = "col-md-12">			
				<div class = "col-md-2">
					  <h3>Reason (opt.)</h3>
				</div>
				<div class = "col-md-9">
					  <h3><input type="text" class="form-control" name='reason' id='reason' placeholder="คิดดอกเบี้ยผิด..."></h3>
				</div>
		</div>
		<div class="col-md-6 col-md-offset-3">
				<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">Save</button></h3>
		</div>
	</div>
	<div class = "row">
		<div class="col-md-12">
			<table class="table">
		    	<caption><h3>All Unpaid Data</h3></caption>
			    <thead>
			        <tr>
						<th>account</th>
						<th>stock</th>
						<th>open date</th>
						<th>open amount</th>
						<th>paid date</th>
						<th>current amount</th>
						<th>Reason</th>
						<th>Delete</th>
			        </tr>
			    </thead>
			    <tbody>
			      	<?php $servername = "172.16.24.235";
						$username = "mycustom";
						$password = "mypass";
						$dbname = "blocktradetest";
						$con = mysqli_connect($servername, $username, $password, $dbname);
						mysqli_set_charset($con,"utf8");
						if (!$con) {
							die("Connection failed: " . mysqli_connect_error());
						}
						$sql = "SELECT UN.UNID,UN.UNstock,C.Account,UN.UNTranDate,UN.UNPaidDate,UN.UNStartValue,UN.UNCurrentValue,UN.UNEvent from unpaid 	 as UN INNER JOIN customer AS C ON UN.CustomerID = C.CustomerID order by UN.UNCurrentValue asc,C.Account asc ";
						$result = mysqli_query($con, $sql);
										  	
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_assoc($result)) {
								$Newformat = strtotime($row['UNTranDate']);
								$myFormatForView = date("d/m/Y", $Newformat);
								$Newformat2 = strtotime($row['UNPaidDate']);
								$myFormatForView2 = date("d/m/Y", $Newformat2);
								echo "<tr>";
								echo "<td>".$row['Account']."</td>
									  <td>".$row['UNstock']."</td>
								      <td>".$myFormatForView."</td>
								      <td>".number_format($row['UNStartValue'],2)."</td>
								      <td>".$myFormatForView2."</td>
								      <td>".number_format($row['UNCurrentValue'],2)."</td>
								      <td>".$row['UNEvent']."</td>";
								echo "<td><button class='btn btn-info' value='".$row['UNID']. "' onClick='finddata(this.value)'>Delete</button></td>";
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
		
		if(dd<10)  dd='0'+dd;
		if(mm<10)  mm='0'+mm;

		defaultdate = yyyy+'-'+mm+"-"+dd;
		document.getElementById("startdate").value = defaultdate;
	});
	function finddata(value){
		if (confirm('Do you want to submit?')) {
			$.ajax({
		        type: 'post',
		        url: 'unpaidsave.php',
		        data: {deletedata: value},
		        success: function( data ) {
		        	alert( data );
		        	window.location.href = "unpaid.php";
		        }
		    });
	    }else {
	        return false;
	    }
	}
	function sendUnpaid(){
		var account = document.getElementById("account").value.trim();
		var trandate = document.getElementById("startdate").value.trim();
		var amount = document.getElementById("amount").value.trim();
		var reason = document.getElementById("reason").value.trim();
		var stock = document.getElementById("stock").value.toUpperCase().trim();

		if (document.getElementById("account").value == ""){	
			alert("Please fill account");
			document.getElementById("account").focus();
			return;
		} else if (document.getElementById("startdate").value == ""){	
			alert("Please fill open date");
			document.getElementById("startdate").focus();
			return;
		} else if (document.getElementById("amount").value == ""){	
			alert("Please fill amount");
			document.getElementById("amount").focus();
			return;
		} else if (document.getElementById("stock").value == ""){	
			alert("Please fill stock");
			document.getElementById("stock").focus();
			return;	
		}
					
		if ((account != "") && (trandate != "") && (amount != "") && (stock != "")  ){
			if (confirm('Do you want to submit?' + '\r\naccount : ' + account +'\r\nstock : '+ stock +'\r\nopen date : '+ trandate +'\r\namount : '+ amount +'\r\nreason : '+ reason)) {	
				    $.ajax({
				        type: 'post',
				        url: 'unpaidsave.php',
				        data: {account: account, stock:stock, trandate: trandate, amount: amount, reason: reason},
				        success: function( data ) {
				        	alert( data );
				            window.location.href = "unpaid.php";
				        }
				    });
			}else {
	        	return false;
	    	}
		}
		else {
			alert("There are still blank box.!!");
		}
	}

	$('#senddata').click(function(){
		sendUnpaid();
	});

	$(document).keypress(function(e) {
	    if(e.which == 13) {
  			sendUnpaid();
	    }
	});

	$(document).keydown(function(event) {
		if (event.ctrlKey && event.keyCode === 13) {
	  		sendUnpaid();
	  	}
	})

</script>
</html>