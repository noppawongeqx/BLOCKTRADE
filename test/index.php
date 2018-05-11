<!DOCTYPE html>
<html lang="th">
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

	$min = array();
	$max = array();
	$tick = array();
	
	$servername = "172.16.24.235";
  	$username = "mycustom";
  	$password = "mypass";
  	$dbname = "blocktradetest";
  	$con = mysqli_connect($servername, $username, $password, $dbname);
  	if (!$con) {
  		die("Connection failed: " . mysqli_connect_error());
  	}
  	
  	$sql = "SELECT Min,Max,Tick FROM `tick`";
  	$result = mysqli_query($con, $sql);
  	if (mysqli_num_rows($result) > 0) {
  		while($row = mysqli_fetch_assoc($result)) {
			$min[] = $row['Min'];
			$max[] = $row['Max'];
			$tick[] = $row['Tick'];
  		}
  	} 
  	mysqli_close($con);
?>
<div class = "container">
	<div class = "row">
		<div class = "col-md-6">
				<div class = "col-md-4">
					<label>
						<h1>
							<input type="radio" name="inlineRadioOptions" id="long" value="option1" checked="checked">
							<span class="label label-success">Long</span>
						</h1> 
					</label>
				</div>
				<div class = "col-md-4">
					<label>
						<h1>
							<input type="radio" name="inlineRadioOptions" id="short" value="option2"> 
							<span class="label label-danger">Short</span>
						</h1>
					</label>
				</div>
		</div>
		<div class = "col-md-6">
			<div class="col-md-6">		
				<h1></h1>
			</div>			
			<div class="col-md-6">		
				<h1><img src="img/logo.png" alt="logo" align="middle"></h1>
			</div>
		</div>
	</div>
	<div class = "row">
		<div class = "col-md-6">
				<div class = "col-md-3">
					<h3>UI</h3>
				</div>
				<div class = "col-md-4">
					<h3><input type="text" class="form-control" id="underlying" style="text-transform: uppercase" autofocus></h3>
				</div>
				<div class="col-md-4">
			      	<h3><select class="form-control" id="series">
					  	<?php $servername = "172.16.24.235";
						  	$username = "mycustom";
						  	$password = "mypass";
						  	$dbname = "blocktradetest";
						  	$con = mysqli_connect($servername, $username, $password, $dbname);
						  	if (!$con) {
						  		die("Connection failed: " . mysqli_connect_error());
						  	}
						  	$sql = "SELECT SName,SLastTradingDay,Underlying FROM `serie` WHERE SLastTradingDay >= CURDATE() 
						  			ORDER BY SLastTradingDay ASC , SName ASC";
						  	$result = mysqli_query($con, $sql);
						  	if (mysqli_num_rows($result) > 0) {
						  		$nr = 'Y';
						  		while($row = mysqli_fetch_assoc($result)) {
						  			if($nr == 'Y'){
										echo "<option selected value=". $row['SLastTradingDay'].">".$row['SName']."  ".$row['Underlying']."</option>";	
									}else if($nr == 'N'){
										echo "<option value=". $row['SLastTradingDay'].">".$row['SName']."  ".$row['Underlying']."</option>";
									}
						  		$nr = 'N';
						  		}
						  	} 
						  	mysqli_close($con);
						?>
					</select></h3>
				</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<h3>Remaining Days</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="dateleft" style='text-align:right;' disabled></h3>
			</div>
		</div>	
	</div>
	<div class = "row">
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>Spot</h3>
			</div>
			<div class = "col-md-5">
				<h3><input type="text" class="form-control" id="spot"></h3>
			</div>
			<div class = "col-md-3">
				<h3>Baht</h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<h3>Spot</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="spotprice" style='text-align:right;' disabled></h3>
			</div>


		</div>
	</div>
	<div class = "row">
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>Upfront</h3>
			</div>
			<div class = "col-md-5">
				<label class="radio-inline">
					<div class="col-md-6">
						<h3><input type="radio" name="inlineRadio" id="percent" value="op1" checked="checked">Percent</h3>
					</div>
					<div class="col-md-6">
						<h3><input type="text" class="form-control" id="percentvalue"></h3>
					</div></label>
			</div>
			<div class = "col-md-3">
				<h3>%</h3>
			</div>
		</div>
		<div class="col-md-6">
				<div class = "col-md-6">
				<h3>Upfront</h3>
			</div>
			<div class = "col-md-6">
		   		<h3><input type="text" class="form-control" id="interest1" style='text-align:right;' disabled></h3>
			</div>
		</div>
	</div>
	<div class = "row">
		<div class = "col-md-6">
			<div class = "col-md-5 col-md-offset-3">
				<label class="radio-inline">
					<div class="col-md-6">
						<h3><input type="radio" name="inlineRadio" id="tick" value="op2">Tick</h3>
					</div>
					<div class="col-md-6">
						<h3><input type="text" class="form-control" id="tickvalue"></h3>
					</div>
				</label>
			</div>
			<div class = "col-md-3">
				<h3>Baht</h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<h3>Total Interest</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="totalinterest" style='text-align:right;' disabled></h3>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>Interest </h3>
			</div>
			<div class = "col-md-5">
				<h3><input type="text" class="form-control" id="interest3"></h3>
			</div>
			<div class = "col-md-3">
				<h3>% </h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<h3>Discount </h3>
			</div>
			<div class = "col-md-6">
			  	<h3><input type="text" class="form-control" id="discountdividend" style='text-align:right;' disabled></h3>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>min days </h3>
			</div>
			<div class = "col-md-5">
				<h3><input type="text" class="form-control" id="mindate"></h3>
			</div>
			<div class = "col-md-3">
				<h3>days</h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<h3>Future Price</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="openprice" style='text-align:right;box-shadow:0 0 50px red;'></h3>
			</div>		
		</div>
	</div>
	<div class='row'>
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>min basis </h3>
			</div>
			<div class = "col-md-5">
				<h3><input type="text" class="form-control" id="minbaht"></h3>
			</div>
			<div class = "col-md-3">
				<h3>Baht</h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				<label><h3><input type="checkbox" id ="isJPM">  <span class="label label-primary">JPM Deal</span></h3> </label>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="jpmfutureprice" style='text-align:right;'></h3>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class = "col-md-6">
			<div class = "col-md-3">
				<h3>Discount</h3>
			</div>
			<div class = "col-md-5">
				<h3><input type="text" class="form-control" name = 'discount' id='discount'></h3>
			</div>
			<div class = "col-md-3">
				<h3>Baht</h3>
			</div>
		</div>
		<div class="col-md-6">
			<div class = "col-md-6">
				  <h3>Start Date</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" name = 'startdate' id='startdate' style='text-align:right;'></h3>
			</div>
		</div>
	</div>
	<div class = "row">
		<div class="col-md-6 col-md-offset-3">
			<h3><button type="button" id="calculate" class="btn btn-info btn-lg btn-block">Calculate</button></h3>
		</div>
	</div>
	<div class="row">
		<div class = "col-md-12">
			<div class = "col-md-2">
				<h3>Account</h3>
			</div>
			<div class = "col-md-2">
				<h3><input type="text" class="form-control" id="account"></h3>
			</div>
			<div class = "col-md-2">
				<h3 style='text-align:right;'>Volume (Cons)</h3>
			</div>
			<div class = "col-md-2">
				<h3><input type="text" class="form-control" id="contract"></h3>
			</div>
			<div class = "col-md-2">
				<h3 style='text-align:right;'>multiplier</h3>
			</div>
			<div class = "col-md-2">
				<h3 style='text-align:left;'><input type="text" class="form-control" id="multiplier" disabled></h3>
			</div>
		</div>
		<div class="col-md-6 col-md-offset-3">
			<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">Save</button></h3>
		</div>
	</div>
</div>
</body>

<script>

	var min = <?php echo json_encode($min) ?>;
	var max = <?php echo json_encode($max) ?>;
	var tick = <?php echo json_encode($tick) ?>;
	var jj = "N";

	$(document).ready(function(){
		var defaultdate = new Date();
		var dd = defaultdate.getDate();
		var mm = defaultdate.getMonth()+1; //January is 0!
		var yyyy = defaultdate.getFullYear();
		if(dd<10) dd='0'+dd; 
		if(mm<10) mm='0'+mm; 
		defaultdate = yyyy+'-'+mm+"-"+dd;
		document.getElementById("startdate").value = defaultdate;
		document.getElementById("mindate").value = 3;
		document.getElementById("minbaht").value = 0.003;
		document.getElementById("discount").value = 0.00;
		document.getElementById("interest3").value = 4.5;
		// document.getElementById("multiplier").value = 1000;
		var sel = document.getElementById("series");
		var multi = sel.options[sel.selectedIndex].text;
		$.ajax({
	        type: 'post',
	        url: 'transactionSave.php',
	        data: {multi: multi},
	        success: function( data ) {
	        	document.getElementById("multiplier").value = data;
	        }
	    });
	    $('.navbar').next('.container').css('background-color','rgb(130, 241, 130)');	
	});

	$('#long').click(function(){
		if(jj == "N"){
			$('.navbar').next('.container').css('background-color','rgb(130, 241, 130)');
		}else if (jj=="Y"){
			$('.navbar').next('.container').css('background-color','rgb(102, 205, 170)');
		}
		document.getElementById("interest3").value = 4.5;
		document.getElementById("underlying").focus();
		return;
	});

	$('#short').click(function(){	
		if(jj == "N"){
			$('.navbar').next('.container').css('background-color','rgb(220, 126, 123)');
		}else if (jj=="Y"){
			$('.navbar').next('.container').css('background-color','rgb(255, 182, 193)');
		}
		document.getElementById("interest3").value = 5.5;
		document.getElementById("underlying").focus();
		return;
	});

	$('#isJPM').click(function(){
		if(jj == "N"){
			if(document.getElementById("long").checked){
	        	$('.navbar').next('.container').css('background-color','rgb(102, 205, 170)');
	        }else{
	        	$('.navbar').next('.container').css('background-color','rgb(255, 182, 193)');
	        }
			jj = "Y";
			document.getElementById("jpmfutureprice").focus();
		}else if (jj=="Y"){
			if(document.getElementById("long").checked){
	        	$('.navbar').next('.container').css('background-color','rgb(130, 241, 130)');
	        }else{
	        	$('.navbar').next('.container').css('background-color','rgb(220, 126, 123)');
	        }
	        jj = "N";   
	        document.getElementById("account").focus();    
		}
		return;
	});

	function calculateBT(){

		if(document.getElementById("spot").value == ""){
			alert("Input " + document.getElementById("underlying").value + " price before");
			document.getElementById("spot").focus();
			return;
		}
		
		var min = <?php echo json_encode($min) ?>;
		var max = <?php echo json_encode($max) ?>;
		var tick = <?php echo json_encode($tick) ?>;
		var sel = document.getElementById("series");
		var serie = sel.options[sel.selectedIndex].text;
		var LTD = new Date($('#series').val());
		var discount = document.getElementById("discount").value;
		var transactionDay = new Date(document.getElementById("startdate").value);
		var diff = new Date(LTD-transactionDay);
		var days = diff/1000/60/60/24;
		var spot = parseFloat(document.getElementById("spot").value);
		var interest3 = document.getElementById("interest3").value;
		if (days <= document.getElementById("mindate").value)
			days = document.getElementById("mindate").value;
		if(document.getElementById("interest1").value == ""){
			if(interest3 != document.getElementById("percentvalue").value && document.getElementById("percentvalue").value !== ""){
				document.getElementById("interest3").value = document.getElementById("percentvalue").value;
				interest3 = document.getElementById("percentvalue").value;
				document.getElementById("interest3").focus();
			}
		}	

		document.getElementById("underlying").value = document.getElementById("underlying").value.toUpperCase().trim();
		document.getElementById("account").value = document.getElementById("account").value.trim();
		document.getElementById("spot").value = spot;
		document.getElementById("dateleft").value = days;
		document.getElementById("spotprice").value = spot
		document.getElementById("discountdividend").value = discount;
		document.getElementById("totalinterest").value = (spot * (interest3/100) * days / 365).toFixed(5);
		if(document.getElementById("percent").checked){
			document.getElementById("interest1").value = (spot * (document.getElementById("percentvalue").value/100) * days / 365).toFixed(5);
		}else if(document.getElementById("tick").checked){
			document.getElementById("interest1").value = parseFloat(document.getElementById("tickvalue").value);
		}

		// //Check upfront to minimum basis
		// if((document.getElementById("minbaht").value > 0) && (document.getElementById("minbaht").value > document.getElementById("interest1").value)){
		// 	document.getElementById("interest1").value = document.getElementById("minbaht").value;
		// }

		var interest1 = document.getElementById("interest1").value;
		if(document.getElementById("long").checked){
			document.getElementById("openprice").value = (parseFloat(spot) + parseFloat(interest1) - parseFloat(discount)).toFixed(5);
			alert("Customer Open Long "+document.getElementById("underlying").value+serie+" and Spot = "+spot) ;
		}else if(document.getElementById("short").checked){
			document.getElementById("openprice").value = (parseFloat(spot) - parseFloat(interest1) + parseFloat(discount)).toFixed(5);
			alert("Customer Open Short "+document.getElementById("underlying").value+serie+" and Spot = "+spot);
		}
	}



	function saveBlockTrade(){
		if(document.getElementById("long").checked){
			var position = "Long";
		}else if(document.getElementById("short").checked){
			var position = "Short";
		}
		var tranDate = document.getElementById("startdate").value;
		var underlying = (document.getElementById("underlying").value).toUpperCase().trim();
		var multiplier = document.getElementById("multiplier").value;
		var volume = document.getElementById("contract").value;
		var cost = document.getElementById("spot").value;
		var futurePrice = document.getElementById("openprice").value;	
		var value = futurePrice * volume * multiplier;
		var upfront = document.getElementById("interest1").value;
		var totalint = document.getElementById("totalinterest").value;
		var mindate = document.getElementById("mindate").value;
		var minbaht = document.getElementById("minbaht").value;
		var acccount = document.getElementById("account").value.trim();
		var percentInterest = document.getElementById("interest3").value;
		var percentUpfront = document.getElementById("percentvalue").value;
		var discount = document.getElementById("discountdividend").value;
		var sel = document.getElementById("series");
		var serie = sel.options[sel.selectedIndex].text;
	  	var checkJPM = document.getElementById("isJPM");
		if (checkJPM.checked == true){
			var isJPM = "Y";
			var jpmfutureprice = document.getElementById("jpmfutureprice").value;
		}else{
			var isJPM = "";
			var jpmfutureprice = "";
		}

		if ((position != "") && (tranDate != "") && (underlying != "") && (volume != "") && (cost != "") && (value != "") && (upfront != "") && (totalint != "") && (mindate != "") && (acccount != "")){
		    $.ajax({
		        type: 'post',
		        url: 'transactionSave.php',
		        data: {position: position, tranDate: tranDate, underlying: underlying, volume: volume, spot: cost, value: value, upfront: upfront, totalint: totalint, mindate: mindate, minbaht: minbaht, acccount: acccount, serie: serie, percentInterest: percentInterest, multiplier: multiplier, percentUpfront: percentUpfront, futurePrice: futurePrice, discount: discount, isJPM : isJPM, jpmfutureprice : jpmfutureprice},
		        success: function( data ) {
		        	document.getElementById("senddata").disabled = true;
		        	alert( data );
		            window.location.href = "index.php";
		        }
		    });
		}
		else{
			alert("Please fill all blank space / click Calculate before");
			document.getElementById("account").focus();
		}
	}


	$('#calculate').click(function(){
		calculateBT();	
	});

	$(document).keypress(function(e) {
	    if(e.which == 13) {
	    	calculateBT();	
	    }
	});

	$('#senddata').click(function(){
		saveBlockTrade();
	});

	$(document).keydown(function(event) {
	  if (event.ctrlKey && event.keyCode === 13) {
	  	saveBlockTrade();
	  }
	})

	$('#series').on('change', function(){
		var sel = document.getElementById("series");
		var multi = sel.options[sel.selectedIndex].text;
		$.ajax({
	        type: 'post',
	        url: 'transactionSave.php',
	        data: {multi: multi},
	        success: function( data ) {
	        	document.getElementById("multiplier").value = data;
	        	alert('Multiplier of ' + multi + ' : ' + addCommas(data));
	        	document.getElementById("spot").focus();
	        }
	    });
	});

	function addCommas(nStr){
		nStr = nStr + '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

</script>
</html>