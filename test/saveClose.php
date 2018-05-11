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
	require_once 'menu.php';
?>
<div class="contrainer">
	<div class="row">
		<div class="col-md-12">

			<?php

				$position = $_GET['p'];
				$underlying = $_GET['ui'];
				$serie = $_GET['s'];
				$volumeStart = $_GET['vol'];
				$volumeCurrent = $_GET['volCur'];
				$cost = $_GET['cost'];
				$transactionDate = $_GET['idate'];
				$upfront = $_GET['up'];
				$hodingDay = $_GET['holday'];
				$interest = $_GET['interest'];
				$spot = $_GET['spot'];
				$value = $_GET['value'];
				$account = $_GET['acc'];
				$CTOID = $_GET['id'];
				$multiplier = $_GET['multi'];
				$discount = $_GET['discount'];
				$minDate = $_GET['mindate'];
				$minInt = $_GET['minint'];
				$isJPM = $_GET['isJPM'];

					
				if ($isJPM == "Y")
					echo "<h3 align = middle style='color:red;'>This is JPM deal, Don't forget to fill JPM Future Price</h3>";
				echo "<table class='table'>";
				// <caption>SaveClose</caption>
				echo "<thead>
				        <tr>
				        	<th>OpenDate</th>
				        	<th>Account</th>
				        	<th>Position</th>
							<th>Instrument</th>
							<th>Volume(Start)</th>
							<th>Volume(Now)</th>
							<th>Spot</th>
							<th>Upfront</th>
							<th>Cost</th>
							<th>Rate%</th>
							<th>MinDays</th>
							<th>MinBss</th>
							<th>Holding Period(days)</th>
				        </tr>
				     </thead>";

				echo "<tbody><tr>";
				echo "<td>".$transactionDate."</td>";
				echo "<td>".$account."</td>";			      
				if ($position == "Long")
					echo "<td><button type='button' class='btn btn-success' disabled='disabled'>Long</button></td>";
				elseif ($position == "Short")
					echo "<td><button type='button' class='btn btn-danger' disabled='disabled'>Short</button></td>";
				echo "<td>".$underlying.$serie."</td>";
				echo "<td>".$volumeStart."</td>";
				echo "<td>".$volumeCurrent."</td>";
				echo "<td>".$spot."</td>";
				echo "<td>".$upfront."</td>";
				echo "<td>".$cost."</td>";
				echo "<td>".$interest."</td>";
				echo "<td>".$minDate."</td>";
				echo "<td>".$minInt."</td>";
				echo "<th>".$hodingDay."</th>";
				echo "</tr></tbody></table>";
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<!-- <h3><?php //echo $underlying; ?> Price</h3> -->
				<h3>Spot Close</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="spot" placeholder="e.g. 20" autofocus></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<h3>Upfront Interest</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="upfrontInterest" style='text-align:right;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<!-- <h3>จำนวนวันขั้นต่ำ</h3> -->
				<h3>Minimum Days</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="mindata" value="<?php echo $minDate;?>" style='text-align:left;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Net Interest</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="totalInterest" style='text-align:right;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<!-- <h3>ดอกเบี้ยขั้นต่ำ</h3> -->
				<h3>Minimum Basis</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" align="left" id="minInterest" value="<?php echo $minInt;?>" style='text-align:left;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Extra Interest</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="netInterest" style='text-align:right;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<h3>Dividend</h3>
			</div>
			<div class = "col-md-6">
				<h3><input  type="text" 
							class="form-control" 
							id="dividend" 
							style='text-align:left;'
							disabled
							value=	'<?php 
										$transactionDate = str_replace('/', '-', $transactionDate);
										$closeDate = date('Y-m-d', strtotime($transactionDate . "+".$hodingDay." days"));
										$transactionDate =  date('Y-m-d', strtotime($transactionDate));
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
									  	$sql = "SELECT DDividend,DPercentOut FROM `dividend` WHERE XDDate > '$transactionDate' AND XDDate <= '$closeDate' AND DStock = '$underlying'";
									  	$result = mysqli_query($con, $sql);
									  	$GetAllDividend = 0;
									  	if (mysqli_num_rows($result) > 0) {
									  		while($row = mysqli_fetch_assoc($result)) {
									  			$GetAllDividend += $row['DDividend']*$row['DPercentOut'];
									  		}
									  		echo $GetAllDividend;
									  	}else{
									  		echo "0";
									  	}
									?>' 		
				></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
		<!--  <div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Dividend</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="dividend1" style='text-align:right;' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div> -->

		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Discount</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="discount" style='text-align:right;' value='<?php echo $discount;?>' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>

	</div>
	<!-- <div class='row'>
		<div class="col-md-5 col-md-offset-1">
			<br>
		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Discount</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="discount" style='text-align:right;' value='<?php //echo $discount;?>' disabled></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div> -->
	
	<div class='row'>
	<!-- <div class='row' style='display: none;'>  -->
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<h3>UnpaidMoney</h3>
			</div>

			<div class = "col-md-6">
				<h3>
					<input type="text" class="form-control" id="unpaid2" style='text-align:left;' disabled value='
						<?php 
							$sql = "SELECT SUM(UNCurrentValue) AS money FROM unpaid AS UN 
									INNER JOIN customer AS C ON UN.CustomerID = C.CustomerID 
									WHERE C.Account = '$account'";
							$result = mysqli_query($con, $sql);						
							if (mysqli_num_rows($result) > 0) {
								while($row = mysqli_fetch_assoc($result)) {
									echo $row['money'];
								}
							}
						?>'>
				</h3>
			</div>

			<div class = "col-md-2">
			</div>
		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Unpaid</h3>
			</div>
			<div class = "col-md-6">
				<h3>
					<input type="text" class="form-control" id="unpaid" style='text-align:right;' value='
					<?php 
					$sql = "SELECT SUM(UNCurrentValue) AS money FROM unpaid AS UN 
							INNER JOIN customer AS C ON UN.CustomerID = C.CustomerID 
							WHERE C.Account = '$account'";
					$result = mysqli_query($con, $sql);						
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							echo number_format($row['money'] / ($multiplier*$volumeCurrent),5);
						}
					}
					mysqli_close($con);
					?>'>
				</h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>

	<div class='row'>
		<div class="col-md-5 col-md-offset-1">

			<?php
				$isJPM = $_GET['isJPM'];
				if ($isJPM == "Y"){
				echo "  <div class = 'col-md-4'>
							<h3>Jpm Future Close</h3>
						</div>
						<div class = 'col-md-6'>
							<h3><input type='text' class='form-control' id='jpmfutureclose'></h3>
						</div>
						<div class = 'col-md-2'>
						</div>";
				}else{
				echo "  <div class = 'col-md-4' style='display: none;'>
							<h3>Jpm Future Close</h3>
						</div>
						<div class = 'col-md-6' style='display: none;'>
							<h3><input type='text' class='form-control' id='jpmfutureclose' value='N'></h3>
						</div>
						<div class = 'col-md-2' style='display: none;'>
						</div>";
				}
			?>

		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Closing Price</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" id="closeprice" style='text-align:right;box-shadow:0 0 50px red;'></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-12">
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<h3><button type="button" class="btn btn-lg btn-primary btn-block" id="calculate">Calculate</button></h3>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<br>
			<p class="text-center">--------------------------------------------------------------------------------------</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5 col-md-offset-1">
			<div class = "col-md-4">
				<h3>Closing Date</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" name = 'startdate' id='startdate' placeholder="e.g. 2015-1-31"></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
		<div class='col-md-5 col-md-offset-1'>
			<div class = "col-md-4">
				<h3>Closing Volume</h3>
			</div>
			<div class = "col-md-6">
				<h3><input type="text" class="form-control" name = 'closeVol' id='closeVol' placeholder="Max Volume = <?php echo $volumeCurrent;?>"></h3>
			</div>
			<div class = "col-md-2">
			</div>
		</div>
	</div>

	<div class='row' style='display: none;'> 
	<!-- <div class="row"> -->
		<div class="col-md-10 col-md-offset-1">
			<form class="form-inline">
				<div class = "col-md-2">
					<h3>UI (CA)</h3>
				</div>
				<div class = "col-md-2">
					<h3><input type="text" class="form-control" id="underlying" style="text-transform: uppercase" placeholder="e.g. PTT" disabled=""></h3>
				</div>
				<div class="col-sm-2">
			      	<h3><select class="form-control" id="series" disabled>
			      		<option>Select</option>
						  	<?php 
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
							  	$sql = "SELECT SName,SLastTradingDay,Underlying FROM `serie` WHERE SLastTradingDay >= CURDATE() ORDER BY SLastTradingDay ASC , SName ASC";
							  	$result = mysqli_query($con, $sql);
							  	
							  	if (mysqli_num_rows($result) > 0) {
							  		while($row = mysqli_fetch_assoc($result)) {
										echo "<option value=". $row['SLastTradingDay'].">".$row['SName']."  ".$row['Underlying']."</option>";
							  		}
							  	} 
							  	mysqli_close($con);
							?>
					</select></h3>
				</div>
			</form>
			<div class = "col-md-1">
				<h3>Flag</h3>
			</div>
			<div class="col-sm-2">
				<h3><select class="form-control" id="flag" disabled>
					<option>Normal</option>
					<option>Flag</option>
				</select></h3>
			</div>
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

	$(function() {
		$( "#startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
	});

	$(document).ready(function(){
		var defaultdate = new Date();
		var dd = defaultdate.getDate();
		var mm = defaultdate.getMonth()+1; //January is 0!
		var yyyy = defaultdate.getFullYear();
		if(dd<10) dd='0'+dd;
		if(mm<10) mm='0'+mm;
		defaultdate = yyyy+'-'+mm+"-"+dd;
		document.getElementById("startdate").value = defaultdate;
		document.getElementById("underlying").value = "<?php echo $underlying;?>";
		var ddd = parseFloat(document.getElementById("dividend").value) ;
		document.getElementById("dividend").value = ddd.toFixed(5);
		var uuu = parseFloat(document.getElementById("unpaid").value) ;
		document.getElementById("unpaid").value = uuu.toFixed(5);
		var uuu2 = document.getElementById("unpaid2").value.trim() ;
		if (uuu2 != "") {
			document.getElementById("unpaid2").value = parseFloat(uuu2).toFixed(5);
		}else{
			document.getElementById("unpaid2").value = (0).toFixed(5);
		}
	});
 
	function CalculateMaster(){
		// var YOpen = "<?php echo $transactionDate;?>";
		// var yydd = YOpen.substring(8, 10);
		// var yymm = YOpen.substring(5, 7);
		// var yyyy = YOpen.substring(0, 4);
		// var YOpen = new Date(yyyy+"-"+yymm+"-"+yydd);
		// var Yclose = new Date(document.getElementById("startdate").value);
		// var Ydiff = new Date(Yclose-YOpen);
		// var Ydays = Ydiff/1000/60/60/24;

	    if (document.getElementById("spot").value == ""){
			alert("Input <?php echo $underlying;?> price before");
			document.getElementById("spot").focus();
			return;
		}

    	var spot = parseFloat(document.getElementById("spot").value);
    	var discount = parseFloat(document.getElementById("discount").value);
    	var unpaid = parseFloat(document.getElementById("unpaid").value);
    	var dividend = parseFloat(document.getElementById("dividend").value);
		var upfront = parseFloat(<?php echo $upfront;?>);
		var multiplier = <?php echo $multiplier;?>;
		var volume = document.getElementById("closeVol").value;
    	document.getElementById("upfrontInterest").value = upfront;
		document.getElementById("spot").value = spot;

		if(unpaid != 0) {
			if (volume > 0){
				unpaid = parseFloat(document.getElementById("unpaid2").value)/(multiplier*volume);
				document.getElementById("unpaid").value = unpaid.toFixed(5);	
			}
		}
		// document.getElementById("dividend1").value = dividend;
     	// var hodingday = Math.max(parseFloat(document.getElementById("mindata").value),<?php //echo $hodingDay;?>);
    	// var TotalInterest = <?php //echo $spot;?> * <?php //echo $interest;?> / 100 * hodingday / 365;
    	// var hodingInterest = Math.max(TotalInterest,parseFloat(document.getElementById("minInterest").value));
    	// document.getElementById("totalInterest").value = hodingInterest.toFixed(5);
	    // document.getElementById("netInterest").value = (upfront-hodingInterest.toFixed(5)).toFixed(5);
    	
		if(parseFloat(document.getElementById("mindata").value) > parseFloat(<?php echo $hodingDay;?>) )
    		var hodingday = parseFloat(document.getElementById("mindata").value);
    	else
     	 	var hodingday = parseFloat(<?php echo $hodingDay;?>);
		
		var TotalInterest = parseFloat(<?php echo $spot;?>) * parseFloat(<?php echo $interest;?>) / 100 * hodingday / 365;

    	if (TotalInterest > parseFloat(document.getElementById("minInterest").value))
    		var hodingInterest = TotalInterest;
    	else
    		var hodingInterest = parseFloat(document.getElementById("minInterest").value);
    	document.getElementById("totalInterest").value = hodingInterest.toFixed(5);
	    document.getElementById("netInterest").value = (upfront-hodingInterest.toFixed(5)).toFixed(5);

    	var position = <?php echo ($position=="Long") ? 1 : 0;?>;
    	if(position == 1) var Cprice = spot + upfront - hodingInterest + dividend - discount + unpaid;
    	if(position == 0) var Cprice = spot - upfront + hodingInterest + dividend + discount - unpaid;
    	document.getElementById("closeprice").value = Cprice.toFixed(5);
    	document.getElementById("closeVol").focus();
    	
    	var Stalert = ""
    	if(dividend !== 0)	Stalert = Stalert + "Dividend = " + dividend.toFixed(5) + "\n\r";
    	if(discount !== 0)	Stalert = Stalert + "Discount = " + discount.toFixed(5) + "\n\r" ;		
    	if(unpaid !== 0){ 	Stalert = Stalert + "Unpaid = " + unpaid.toFixed(5) + "\n\r";
    						Stalert = Stalert + "Unpaid Amount = " + document.getElementById("unpaid2").value + "\n\r" ; }
    	if(Stalert !== "")	alert(Stalert);
    	
	};

	function SaveMaster(){

		var volume = document.getElementById("closeVol").value;
		var spot = document.getElementById("spot").value;
		var totalInterest = document.getElementById("totalInterest").value;
		var netInterest = document.getElementById("netInterest").value;
		var jpmfutureclose = document.getElementById("jpmfutureclose").value;
		var futurePrice = document.getElementById("closeprice").value;
		var dividend = document.getElementById("dividend").value;
		var unpaid = document.getElementById("unpaid").value;	
		var multiplier = <?php echo $multiplier;?>;
		var value = futurePrice*volume*multiplier;

		var account = "<?php echo $account;?>";
		var isJPM = "<?php echo $_GET['isJPM'];?>";
		var tranDate = document.getElementById("startdate").value;
		var position = "<?php echo ($position=="Long") ? "Short" : "Long";?>";
		if(document.getElementById("underlying").value != "")
			var underlying = (document.getElementById("underlying").value).toUpperCase();
		else
			var underlying = "<?php echo $underlying;?>";	
		var sel = document.getElementById("series");
		var serie = sel.options[sel.selectedIndex].text;
		if(serie == "Select")
			serie = "<?php echo $serie;?>";
		var futureName = "<?php echo $underlying.$serie;?>";
		sel = document.getElementById("flag");
		var flag = sel.options[sel.selectedIndex].text;
		if(flag == "Normal")
			flag = "N";
		else if(flag == "Flag")
			flag = "F";

		if (isJPM == "Y"){
			if (jpmfutureclose > 0){
				if ( (volume > 0) && (volume <= <?php echo $volumeCurrent;?>) ) {
					if ( (position != "") && (tranDate != "") && (underlying != "") && (volume != "") && (spot != "") && (value != "") && (totalInterest != "") && (netInterest != "") && (futurePrice != "") && (flag != "") ){
						$.ajax({
					        type: 'post',
					        url: 'saveClosePosition.php',
					        data: {position: position, tranDate: tranDate, underlying: underlying, futureName: futureName, volume: volume, spot: spot, value: value, totalInterest: totalInterest, netInterest: netInterest, futurePrice: futurePrice, jpmfutureclose: jpmfutureclose, flag: flag, serie: serie, multiplier: multiplier,dividend: dividend, account: account, unpaid: unpaid, CTOID: <?php echo $CTOID;?>},
					        success: function( data ) {
					        	alert( data );
					        	// window.close();
					            window.location.href = "closePosition.php";
				        	}
						 });
					}
					else{
						alert("Click Calculate before");
						return;
					}
				}else{
					alert("Volume on this deal is only  <?php echo $volumeCurrent;?>");
					document.getElementById("closeVol").value = "";
					document.getElementById("closeVol").focus();
					return;
				}

			}else {
				alert("!! JPM Future Close Price !!");
				document.getElementById("jpmfutureclose").focus();
				return;
			}

		}else{

				if ( (volume > 0) && (volume <= <?php echo $volumeCurrent;?>) ) {
					if ( (position != "") && (tranDate != "") && (underlying != "") && (volume != "") && (spot != "") && (value != "") && (totalInterest != "") && (netInterest != "") && (futurePrice != "") && (flag != "") ){
						$.ajax({
					        type: 'post',
					        url: 'saveClosePosition.php',
					        data: {position: position, tranDate: tranDate, underlying: underlying, futureName: futureName, volume: volume, spot: spot, value: value, totalInterest: totalInterest, netInterest: netInterest, futurePrice: futurePrice, jpmfutureclose: jpmfutureclose, flag: flag, serie: serie, multiplier: multiplier,dividend: dividend, account: account, unpaid: unpaid, CTOID: <?php echo $CTOID;?>},
					        success: function( data ) {
					        	alert( data );
					        	// window.close();
					            window.location.href = "closePosition.php";
				        	}
						 });
					}
					else{
						alert("Click Calculate before");
						return;
					}
				}else{
					alert("Volume on this deal is only  <?php echo $volumeCurrent;?>");
					document.getElementById("closeVol").value = "";
					document.getElementById("closeVol").focus();
					return;
				}		
		}
	}


	$('#calculate').click(function(){
		CalculateMaster();
	});
	$('#save').click(function(){
		SaveMaster();
	});

	$(document).keypress(function(e) {
		if(e.which == 13) {
		    CalculateMaster();
	    }
	});
	$(document).keydown(function(event) {
	  if (event.ctrlKey && event.keyCode === 13) {
	  	SaveMaster();
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
	        	multiplier = data;
	        }
	    });
	});
</script>

</html>