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
<title>Add Dividend</title>
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
					  <h3>Stock</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="stock" style="text-transform: uppercase" placeholder="e.g. PTT" autofocus></h3>
				</div>
				<div class = "col-md-2">
					  <h3>Dividend/share</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" id="dividend"></h3>
				</div>
				<div class = "col-md-2">
					  <h3>XD Date</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name = 'startdate' id='startdate' placeholder="e.g. 2015-1-31" ></h3>
				</div>
		</div>
		<div class = "col-md-12">
				<div class = "col-md-2">
					  <h3>NO. of stock</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name='volume' id='volume' placeholder="e.g. 10000"></h3>
				</div>
				<div class = "col-md-2">
					  <h3>% Cash Out</h3>
				</div>
				<div class = "col-md-2">
					  <h3><input type="text" class="form-control" name='perout' id='perout' placeholder="e.g. 0.9"></h3>
				</div>
				<div class = "col-md-2">
					  <h3>Position</h3>
				</div>
				<div class = "col-md-2">
					  <h3><h3><select class="form-control" id="position" disabled><option>BUY</option><option>SELL</option></select></h3></h3>
				</div>
		</div>
		<div class="col-md-6 col-md-offset-3">
				<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">Save</button></h3>
		</div>
	</div>
	<div class = "row">
		<div class="col-md-10">
			<table class="table" id="DividendTable">
				<caption>All time added dividend data</caption>
					<thead>
						<tr>
							<th>XD Date</th>
							<th onclick="sortTable(1)">Stock</th>
							<th>Dividend</th>
							<th>% Cash out</th>
							<th>Start Volume</th>
							<th>Current Volume</th>
							<th>Company Position</th>
							<th>Delete</th>
							<!-- <th>#Open</th> -->
						</tr>
					</thead>
				<tbody>
			    <?php 
	      		$servername = "172.16.24.235";
				$username = "mycustom";
				$password = "mypass";
				$dbname = "blocktradetest";
				$con = mysqli_connect($servername, $username, $password, $dbname);
				if(!$con){
					die("Connection failed: " . mysqli_connect_error());
				}
				$sql = "SELECT DID,XDDate,DStock,DDividend,DPercentOut,DVolume,DCurrentVolume,DCompanyPosition 
						FROM `dividend` ORDER BY XDdate DESC , DStock ASC";
				$result = mysqli_query($con, $sql);		  	
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {
						$Newformat = strtotime($row['XDDate']);
						$myFormatForView = date("d/m/Y", $Newformat);
						echo "<tr>";
						echo "<td>".$myFormatForView."</td><td>".$row['DStock']."</td><td>". $row['DDividend']."</td><td>".
							  $row['DPercentOut']."</td><td>".number_format($row['DVolume'])."</td><td>".
							  number_format($row['DCurrentVolume'])."</td><td>".$row['DCompanyPosition']."</td>";
						if($row['DVolume']==$row['DCurrentVolume']){
						echo "<td><button class='btn btn-info' value='".$row['DID']."' onClick='finddata(this.value)'>Delete</button></td>";
						}else{
						echo "<td><button disabled class='btn btn-info' value='".$row['DID']."' onClick='finddata(this.value)'>Delete</button></td>";
						}

						// $CountSql = "SELECT COUNT(*) FROM customertransactionopen WHERE CTOUnderlying = '".$row['DStock']."' AND DATE(CTOTranDate) <= DATE('".$row['XDDate']."') AND CTOVolumeCurrent > '0' "; 
						// $CountResult = mysqli_query($con, $CountSql);		
						// if (mysqli_num_rows($CountResult) > 0) {
						// 	while($row = mysqli_fetch_assoc($CountResult)) {
						// 		$CountValue = $row['COUNT(*)']; 
						// 		if ($row['COUNT(*)'] > 0){ 
						// 			echo "<td>".$row['COUNT(*)']."</td>";
						// 		}else{
						// 			echo "<td></td>";
						// 		}
						// 	}
						// }else{
						// 	echo "<td></td>";
						// }

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

	function finddata(value){
		if (confirm('Do you want to submit?')) {
			$.ajax({
		        type: 'post',
		        url: 'addDividendSave.php',
		        data: {deletedata: value},
		        success: function( data ) {
		        	alert( data );
		        	window.location.href = "addDividend.php";
		        }
		    });
		} else {
	        return false;
	    }
	}
	function sendDividend(){
			if (document.getElementById("startdate").value == ""){	
				alert("Please fill XD Date");
				document.getElementById("startdate").focus();
				return;
			} else if (document.getElementById("stock").value == ""){	
				alert("Please fill Stock Name");
				document.getElementById("stock").focus();
				return;
			} else if (document.getElementById("dividend").value == ""){	
				alert("Please fill Dividend/share");
				document.getElementById("dividend").focus();
				return;	
			} else if (document.getElementById("volume").value == ""){	
				alert("Please fill NO. of stock");
				document.getElementById("volume").focus();
				return;	
			} else if (document.getElementById("perout").value == ""){	
				alert("Please fill % Cash Out");
				document.getElementById("perout").focus();
				return;		
			} 
		var Stock = (document.getElementById("stock").value).toUpperCase();
		var XDDate = document.getElementById("startdate").value;
		var dividend = document.getElementById("dividend").value;
		var volume = document.getElementById("volume").value;
		var perout = document.getElementById("perout").value;
		var sel = document.getElementById("position");
		var position = sel.options[sel.selectedIndex].text;
		if ((Stock != "") && (XDDate != "") && (dividend != "") && (volume != "") && (perout != "")){
			if (confirm('Do you want to submit?' + '\r\nXD Date : ' + XDDate +'\r\nStock Name : '+ Stock +'\r\nDividend/share : '+ dividend +'\r\nNO. of stock : '+ volume  +'\r\n% Cash Out : '+ perout)) {	
				    $.ajax({
				        type: 'post',
				        url: 'addDividendSave.php',
				        data: {Stock: Stock, XDDate: XDDate,perout: perout, dividend: dividend, volume: volume, position: position},
				        success: function( data ) {
				        	alert( data );
				            window.location.href = "addDividend.php";
				        }
				    });
		    }else {
	        	return false;
	    	} 
		}
		else {
			alert("Please fill all blank spaces !!");
		}
	}
	$('#senddata').click(function(){
		sendDividend();
	});

	$(document).keypress(function(e) {
	    if(e.which == 13) {
  			sendDividend();
	    }
	});
	
	function sortTable(n) {
		alert("Sort table by Stock Name; refresh to sort by XDdate")
		var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
		table = document.getElementById("DividendTable");
		switching = true;
		dir = "asc"; 
		while (switching) {
		    switching = false;
		    rows = table.getElementsByTagName("TR");
		    for (i = 1; i < (rows.length - 1); i++) {
		      	shouldSwitch = false;
	      		x = rows[i].getElementsByTagName("TD")[n];
	      		y = rows[i + 1].getElementsByTagName("TD")[n];
	      		if (dir == "asc") {
			        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {		      
			          	shouldSwitch= true;
			          	break;
			        }
		      	}else if (dir == "desc") {
			        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {		     
			          	shouldSwitch= true;
			          	break;
			        }
			      }
		    }
		    if (shouldSwitch) {
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				switchcount ++; 
		    } else if (switchcount == 0 && dir == "asc") {
		       	dir = "desc";
		       	switching = true;
		    }
	    }
	}
</script>
</html>