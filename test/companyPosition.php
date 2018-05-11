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
<title>Calculate Block Trade Price</title>
</head>
<body>
<?php 
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

If(!isset($_SESSION['loggedin'])){
	header('Location: login.php');
	exit();
}
require 'menu.php';?>

		      <?php 
		      		//Get Current Price From Aspen on Excel
		      		error_reporting(E_ALL ^ E_NOTICE);
		      		require_once 'excel/excel_reader2.php';
		      		$data = new Spreadsheet_Excel_Reader("excel/example.xls");
		      		
					$spot = [];
					$futures = [];
					$rowcount = $data->rowcount($sheet_index=0);
					for($j = 3;$j <= $rowcount; $j++){
					if($data->val($j,1) == 1){
						$spot['TRUE'][$data->val(2,2)] = $data->val($j,2);
						$spot['TRUE'][$data->val(2,3)] = $data->val($j,3);
						$spot['TRUE'][$data->val(2,4)] = $data->val($j,4);
					}
					else{
						$spot[$data->val($j,1)][$data->val(2,2)] = $data->val($j,2);
						$spot[$data->val($j,1)][$data->val(2,3)] = $data->val($j,3);
						$spot[$data->val($j,1)][$data->val(2,4)] = $data->val($j,4);
					}
					}
					
					for($i = 1;$i <= 4;$i++){
						for($j = 3;$j <= $rowcount; $j++){
							$futures[$data->val($j,(5+(2*$i)-1))] = $data->val($j,(5+(2*$i)));
						}
					}
		      		
		      		$servername = "172.16.24.235";
					$username = "mycustom";
					$password = "mypass";
					$dbname = "blocktradetest";
										  	
					$number = 1;
					$profit = 0;
					// Create connection
					$con = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$con) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$AllPos = "";
					$sql = "SELECT COT.COTUnderlying,S.SMultiplier,SUM(COTCash) AS COST,SUM(COTVolume) AS Volume,S.SName FROM companytransaction AS COT INNER JOIN serie AS S ON COT.SerieID = S.SerieID GROUP BY COT.COTUnderlying";
					$result = mysqli_query($con, $sql);
										  	
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							if ($row['Volume'] != 0){
								$AllPos .= "<tr>";
								$AllPos .= "<td>".$row['COTUnderlying']."</td>";
								if ($row['Volume'] > 0)
									$AllPos .= "<td><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['SMultiplier']."'>Long</button></td>";
								else if ($row['Volume'] < 0)
									$AllPos .=  "<td><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['SMultiplier']."'>Short</button></td>";
								else if ($row['Volume'] == 0)
									$AllPos .=  "<td><button type='button' class='btn btn-default' disabled='disabled' id='position".$number."' value='".$row['SMultiplier']."'>No Position</button></td>";
								$AllPos .= "<td><input type='text' class='form-control' id='cost".$number."' value='".$row['COST']."' style='text-align:right;' disabled></td>";
								$AllPos .= "<td><input type='text' class='form-control' id='volume".$number."' value='".$row['Volume']."' style='text-align:right;' disabled></td>";
								$currentPrice = 0;
								if(strlen($row['SName']) == 2){
									if ($row['Volume'] > 0){
										$AllPos .= "<td><input type='text' class='form-control' id='price".$number."' style='text-align:right;' value='".$spot[$row['COTUnderlying']]['BID']."'></td>";
										$currentPrice = $spot[$row['COTUnderlying']]['BID'];
									}
									elseif ($row['Volume'] < 0){
										$AllPos .= "<td><input type='text' class='form-control' id='price".$number."' style='text-align:right;' value='".$spot[$row['COTUnderlying']]['ASK']."'></td>";
										$currentPrice = $spot[$row['COTUnderlying']]['ASK'];
									}
									else{
										$AllPos .= "<td><input type='text' class='form-control' id='price".$number."' style='text-align:right;' value='".$spot[$row['COTUnderlying']]['LAST']."'></td>";
										$currentPrice = $spot[$row['COTUnderlying']]['LAST'];
									}
								}
								else{
									$AllPos .= "<td><input type='text' class='form-control' id='price".$number."' style='text-align:right;' value='".$futures[$row['COTUnderlying']]."'></td>";
									$currentPrice = $futures[$row['COTUnderlying']];
								}
								$AllPos .= "<td><input type='text' class='form-control' id='value".$number."' style='text-align:right;' value='".number_format($currentPrice*$row['Volume']*$row['SMultiplier'],2)."'></td>";
								$AllPos .= "<td><input type='text' class='form-control' id='profit".$number."' value='".number_format($row['COST']+($currentPrice*$row['Volume']*$row['SMultiplier']),2)."' style='text-align:right;'></td>";
			    				$AllPos .= "</tr>";
							}
		    				$number++;
		    				$profit += ($row['COST']+($currentPrice*$row['Volume']*$row['SMultiplier']));
						}
					}
					//Dividend
					$sql = "SELECT DCompanyPosition,SUM(((DVolume-DCurrentVolume)*DDividend*(1-DPercentOut))+(DDividend*DCurrentVolume)) AS Cash,SUM((DVolume-DCurrentVolume)*DDividend*DPercentOut) AS CashOut FROM `dividend` GROUP BY DCompanyPosition";
					$result = mysqli_query($con, $sql);
					
					$totalBuy = 0;
					$totalSell = 0;
					$totalCash = 0;
					$totalBuyOut = 0;
					$totalSellOut = 0;
					$totalCashOut = 0;
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							if ($row['DCompanyPosition'] == "BUY"){
								$totalBuy += $row['Cash'];
								$totalBuyOut += $row['CashOut'];	
							}
							else if ($row['DCompanyPosition'] == "SELL"){
								$totalSell += $row['Cash'];
								$totalSellOut += $row['CashOut'];	
							}
						}
					}
					$totalCash = $totalBuy - $totalSell;
					$totalCashOut = $totalBuyOut - $totalSellOut;
					//number_format($totalCash,2,'.',',')
					$dividend1 = "<input type='text' class='form-control' id='dividend' value='".number_format($totalCash, 2, '.', '')."' style='text-align:right;'>";
					$dividendOut = "<input type='text' class='form-control' id='dividendout' value='".number_format($totalCashOut, 2, '.', '')."' style='text-align:right;'>";
					mysqli_close($con);
					
					//Actual Profit
					$actualProfit = 0;
					$con = mysqli_connect($servername, $username, $password, $dbname);
					if (!$con) {
						die('Could not connect: ' . mysqli_error($con));
					}
					$dividendpart = "";
					$totaldividend = 0;
					$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
					if($result = mysqli_query($con,$sql)){
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_array($result)) {
								$totaldividend += ($row['DDividend']*$row['DVolume']);
							}
						}
					}
					
					$sql = "SELECT COT.CompanyTransactionID,COT.COTDate,COT.COTPosition,COT.COTUnderlying,COT.COTVolume,COT.COTCost,COT.COTValue,COT.COTCash,S.SName,C.Account FROM companytransaction AS COT INNER JOIN customer AS C ON C.CustomerID = COT.CustomerID INNER JOIN serie AS S ON COT.SerieID = S.SerieID";
					if($result = mysqli_query($con,$sql)){
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_array($result)) {
								$totalCash1 += $row['COTCash'];
							}
							$actualProfit += $totalCash1+$totaldividend;
						}
					}
					
					$totalValue = 0;
					$currentValue = 0;
					if (!$con) {
						die('Could not connect: ' . mysqli_error($con));
					}
					if($result = mysqli_query($con,"SELECT CTOUnderlying,CTOPosition,SUM(CTOVolumeCurrent) AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 GROUP BY CTOUnderlying,CTOPosition")){
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_array($result)) {
							$currentValue = 0;
							if($row['CTOPosition'] == "Short"){
								$currentValue = $spot[$row['CTOUnderlying']]['ASK']*$row['Volume']*$row['SMultiplier'];
								$currentValue = $currentValue*-1;
							}
							else{
								$currentValue = $spot[$row['CTOUnderlying']]['BID']*$row['Volume']*$row['SMultiplier'];
							}
								$totalValue += $currentValue;
							}
							$actualProfit += $totalValue;
						}
					}
					
					$totalValue = 0;
					$totalCurrent = 0;
					$today = new DateTime();
					$dividend = array();
					if (!$con) {
						die('Could not connect: ' . mysqli_error($con));
					}
					$sql = "SELECT XDDate,DDividend,DPercentOut,DStock FROM `dividend`";
					$result = mysqli_query($con, $sql);
					
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$dividend[$row['DStock']]['Dividend'] = $row['DDividend'];
							$dividend[$row['DStock']]['XD'] = $row['XDDate'];
							$dividend[$row['DStock']]['PercentOut'] = $row['DPercentOut'];
						}
					}
					if($result = mysqli_query($con,"SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTODiscount,CTO.CTOPercentInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SMultiplier,C.Account FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 ORDER BY CTO.CTOTranDate")){
						if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_array($result)) {
							$tranDate = new DateTime($row['CTOTranDate']);
							$Newformat = strtotime($row['CTOTranDate']);
							$myFormatForView = date("d/m/Y", $Newformat);
							
							$holdingperiod = 0;
							if($tranDate->diff($today)->format("%a") < $row['CTOMinimumDay'])
								$holdingperiod = $row['CTOMinimumDay'];
							else
								$holdingperiod = $tranDate->diff($today)->format("%a");
							
							$dividendpayback = 0;
							if (array_key_exists($row['CTOUnderlying'], $dividend)) {
								if($dividend[$row['CTOUnderlying']]['XD']>$row['CTOTranDate']){
									$dividendpayback = $dividend[$row['CTOUnderlying']]['Dividend']*$dividend[$row['CTOUnderlying']]['PercentOut'];
								}
							}
							$totalint = 0;
							if(($row['CTOSpot']*$row['CTOPercentInterest']/100*$holdingperiod/365) < $row['CTOMinimumInt'])
								$totalint = $row['CTOMinimumInt'];
							else
								$totalint = ($row['CTOSpot']*$row['CTOPercentInterest']/100*$holdingperiod/365);
							$fairprice = 0;
							$current = 0;
							if($row['CTOPosition'] == "Long"){
								$fairprice = ($spot[$row['CTOUnderlying']]['BID'] + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
								$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
							}
							else{
								$fairprice = ($spot[$row['CTOUnderlying']]['ASK'] - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback - $row['CTODiscount']);
								$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
							}
							$totalValue += $row['CTOValue'];
							$totalCurrent += $current;
						}
						$actualProfit += $totalCurrent;
						}
					}
					mysqli_close($con);
					echo "<div class='container'><div class='row'><div class='col-md-12'><div class='col-md-3'>";
					echo "<button class='btn btn-info btn-lg btn-block' value='".$number."' onClick='calculate(this.value)'>Calculate</button></div>";
					echo "<div class='col-md-2'><h3>Accounting</h3></div>";
					echo "<div class='col-md-2'><h3><input type='text' class='form-control' id='totalProfit' value='".number_format($profit+$totalCash+$totalCashOut,2)."' style='text-align:right;'></h3>";
					echo "</div><div class='col-md-2'><h3>Fair</h3></div><div class='col-md-2'><h3><input type='text' class='form-control' id='totalProfit' value='".number_format($actualProfit,2)."' style='text-align:right;'></h3></div></div></div></div>";
				?>
<div class = "container">
	<div class = "row">
		<div class="col-md-12">
			<p class="text-center">___________________________________________________________________________________________</p>
		</div>
		<div class="col-md-12">
			<table class="table">
		      <caption><h3>Company Position</h3></caption>
		      <thead>
		        <tr>
		          <th>Underlying / Future</th>
		          <th>Position</th>
		          <th>Cost</th>
		          <th>Volume</th>
		          <th>Current Price</th>
		          <th>Current Value</th>
		          <th>Profit / Loss</th>
		        </tr>
		      </thead>
		      <tbody>
		      <?php echo $AllPos;?>
		      </tbody>
		    </table>
		</div>
		<div class="col-md-12">
			<div class = "col-md-4">
				<h3>Total Cash In from dividend</h3>
			</div>
			<div class = "col-md-4">
				<h3><?php echo $dividend1;?></h3>
			</div>
		</div>
		<div class="col-md-12">
			<div class = "col-md-4">
				<h3>Total Cash Out from dividend</h3>
			</div>
			<div class = "col-md-4">
				<h3><?php echo $dividendOut;?></h3>
			</div>
		</div>
	</div>
</div>
</body>
<script>
function calculate(value){
	var value1;
	var profit;
	var total1 = 0;
	var dividend;
	var dividendOut;
	dividend = Number(document.getElementById("dividend").value);
	dividendOut = Number(document.getElementById("dividendout").value);
	total1 = Number(total1) + Number(dividend) + Number(dividendOut);
	document.getElementById("dividend").value = dividend;
	document.getElementById("dividendout").value = dividendOut;
	for(i = 1; i <= value; i++){
		value1 = Number(document.getElementById("volume"+i).value) * Number(document.getElementById("price"+i).value)*Number(document.getElementById("position"+i).value);
		profit = Number(value1) + Number(document.getElementById("cost"+i).value);
		document.getElementById("value"+i).value = value1;
		document.getElementById("profit"+i).value = profit;
		total1 = Number(total1) + Number(profit);
		document.getElementById("totalProfit").value = total1;
	}
}
function formatCurrency(num)
{
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
    {
        num = "0";
    }

    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();

    if (cents < 10)
    {
        cents = "0" + cents;
    }
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
    {
        num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
    }

    return (((sign) ? '' : '-') + num + '.' + cents);
}
</script>
</html>