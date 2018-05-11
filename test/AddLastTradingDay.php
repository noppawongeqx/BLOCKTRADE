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
					  <h3>Serie Name </h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="Sname" style="text-transform: uppercase" placeholder="H18, H18X, A100"  autofocus></h3>
				</div>
				<div class = "col-md-1">
				</div>
				<div class = "col-md-3">
					  <h3>Last Trading Day</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name = 'startdate' id='startdate' placeholder="e.g. 2015-1-31"></h3>
				</div>
		</div>
		<div class = "col-md-12">
				<div class = "col-md-2">
					  <h3>Multiplier</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="multiplier"></h3>
				</div>
				<div class = "col-md-1">
				</div>
				<div class = "col-md-3">
					  <h3>Underlying (CA Case)</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name='underlying' id='underlying' style="text-transform: uppercase" placeholder="e.g. TRUE , BBL"></h3>
				</div>
		</div>
		<div class="col-md-6 col-md-offset-2">
				<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">Save</button></h3>
		</div>
	</div>
	<div class = "row">
		<div class="col-md-10">
			<table class="table">
		      <caption>Active Trading Serie</caption>
		      <thead>
		        <tr>
		          <th>Serie Name</th>
		          <th>Underlying</th>
		          <th>Last Trading Day</th>
		          <th>Multiplier</th>
		          <th>Delete</th>
		        </tr>
		      </thead>
		      <tbody>
		      <?php $servername = "172.16.24.235";
					$username = "mycustom";
					$password = "mypass";
					$dbname = "blocktradetest";
					$con = mysqli_connect($servername, $username, $password, $dbname);
					if (!$con) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$sql = "SELECT SerieID,SName,SLastTradingDay,Underlying,SMultiplier FROM `serie` 
							WHERE SLastTradingDay >= CURDATE() ORDER BY SLastTradingDay ASC , SName ASC";
					$result = mysqli_query($con, $sql);	  	
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$Newformat = strtotime($row['SLastTradingDay']);
							$myFormatForView = date("d/m/Y", $Newformat);
							echo "<tr>";
							echo "<td>".$row['SName']."</td><td>".$row['Underlying']."</td><td>".$myFormatForView."</td><td>".$row['SMultiplier']."</td>";
							echo 	"<td>
		  								<button class='btn btn-info' value='".$row['SerieID']."' onClick='finddata(this.value)'>Delete</button>
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
		document.getElementById("multiplier").value = 1000;
	});
	
	function finddata(value){
		if (confirm('Do you want to submit?')) {	
			$.ajax({
		        type: 'post',
		        url: 'AddLastTradingDaySave.php',
		        data: {deletedata: value},
		        success: function( data ) {
		        	alert( data );
		        	window.location.href = "AddLastTradingDay.php";
		        }
		    });
		} else {
	        return false;
	    }    
	}

	function sending(){

		var SName = document.getElementById("Sname").value.toUpperCase();
		var LTD = document.getElementById("startdate").value;
		var multiplier = document.getElementById("multiplier").value;
		var underlying = document.getElementById("underlying").value.toUpperCase();
			
			if (document.getElementById("Sname").value == ""){	
				alert("Please fill Serie Name");
				document.getElementById("Sname").focus();
				return;
			} else if (document.getElementById("startdate").value == ""){	
				alert("Please fill Last Trading Day");
				document.getElementById("startdate").focus();
				return;
			} else if (document.getElementById("multiplier").value == ""){	
				alert("Please fill Multiplier");
				document.getElementById("multiplier").focus();
				return;
			}	

		if ((SName != "") && (LTD != "")  && (multiplier != "")){
			if (confirm('Do you want to submit?' + '\r\nSerie Name : ' + document.getElementById("Sname").value.toUpperCase() +'\r\nUnderlying : '+ document.getElementById("underlying").value.toUpperCase() + '\r\nLast Trading Day : ' + LTD + '\r\nMultiplier : '+ multiplier)) {	
			    $.ajax({
			        type: 'post',
			        url: 'AddLastTradingDaySave.php',
			        data: {SName: SName, LTD: LTD, multiplier: multiplier, underlying: underlying},
			        success: function( data ) {
			        	alert( data );
			            window.location.href = "AddLastTradingDay.php";
			        }
			    });
			}else {
	        	return false;
	    	}      
		}else{
			alert("Please fill all blank spaces!!");
		}		
	}


	$('#senddata').click(function(){
		sending();
	});

	$(document).keypress(function(e) {
	    if(e.which == 13) {
    		sending();
	    }
	});
	
</script>
</html>