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

If(!isset($_SESSION['loggedin'])){
	header('Location: login.php');
	exit();
}
require 'menu.php';

$y = date("Y");
$today = new DateTime();
$newyear = new DateTime($y.'-01-01');
$nodate = $newyear->diff($today)->format("%a");
?>
<div class='contrainer'>
	<div class='row'>
		<div class="col-md-4 col-md-offset-4">
			<table class="table">
		      <caption><h3>Profit / Loss by Underlying</h3></caption>
		      <thead>
		        <tr>
		          <th style='text-align:center;'>Underlying</th>
		          <th style='text-align:right;'>Accounting</th>
		          <th style='text-align:right;'>Fair</th>
		        </tr>
		      </thead>
		      <tbody>
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
					
					$accounting = [];
					$cashindividend = [];
					$fair = [];
					$underlying = "";
					$sumAcc = 0;
					$sumFair = 0;
					
					$servername = "172.16.24.235";
					$username = "mycustom";
					$password = "mypass";
					$dbname = "blocktradetest";
					$con = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$con) {
						die("Connection failed: " . mysqli_connect_error());
					}
					$lastyearprofit = [];
					$sql = "SELECT SUM(LYPAccount) AS Account,SUM(LYPFair) AS Fair,LYPStock FROM `lastyearprofit` GROUP BY LYPStock";
					$result = mysqli_query($con, $sql);
						
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$lastyearprofit[$row['LYPStock']]['Account'] = $row['Account'];
							$lastyearprofit[$row['LYPStock']]['Fair'] = $row['Fair'];
						}
					}
					$sql = "SELECT COT.COTUnderlying,S.SMultiplier,SUM(COTCash) AS Cash,SUM(COTVolume) AS Volume,S.SName FROM companytransaction AS COT INNER JOIN serie AS S ON COT.SerieID = S.SerieID GROUP BY COT.COTUnderlying";
					$result = mysqli_query($con, $sql);
						
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							if($row['SName'] != "UI"){
								$underlying = substr($row['COTUnderlying'],0,(strlen($row['COTUnderlying'])-strlen($row['SName'])));
								$accounting[$underlying] += $row['Cash'];
								$accounting[$underlying] += $futures[$row['COTUnderlying']]*$row['Volume']*$row['SMultiplier'];
							}
							else{
								$accounting[$row['COTUnderlying']] += $row['Cash'];
								if($row['Volume'] > 0){
									$accounting[$row['COTUnderlying']] += $spot[$row['COTUnderlying']]['BID']*$row['Volume'];
								}
								elseif($row['Volume'] < 0){
									$accounting[$row['COTUnderlying']] += $spot[$row['COTUnderlying']]['ASK']*$row['Volume'];
								}
							}
						}
					}
					$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
					if($result = mysqli_query($con,$sql)){
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_array($result)) {
								$cashindividend[$row['DStock']] = ($row['DDividend']*$row['DVolume']);
							}
						}
					}
					// Fair
					$dividend = array();
					$today = new DateTime();
					$sql = "SELECT XDDate,DDividend,DPercentOut,DStock FROM `dividend`";
					$result = mysqli_query($con, $sql);
						
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$dividend[$row['DStock']]['Dividend'] = $row['DDividend'];
							$dividend[$row['DStock']]['XD'] = $row['XDDate'];
							$dividend[$row['DStock']]['PercentOut'] = $row['DPercentOut'];
						}
					}
					if($result = mysqli_query($con,"SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOTotalInterest,CTO.CTODiscount,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SMultiplier,C.Account FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 ORDER BY CTO.CTOTranDate")){
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
								$fair[$row['CTOUnderlying']] += $current;
							}
						}
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
								$fair[$row['CTOUnderlying']] += $currentValue;
							}
						}
					}
					$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
					if($result = mysqli_query($con,$sql)){
						if (mysqli_num_rows($result) > 0) {
							while($row = mysqli_fetch_array($result)) {
								$fair[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
							}
						}
					}
						
					$sql = "SELECT COT.COTUnderlying,S.SMultiplier,SUM(COTCash) AS Cash,SUM(COTVolume) AS Volume,S.SName FROM companytransaction AS COT INNER JOIN serie AS S ON COT.SerieID = S.SerieID GROUP BY COT.COTUnderlying";
					$result = mysqli_query($con, $sql);
						
					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							if($row['SName'] != "UI"){
								$underlying = substr($row['COTUnderlying'],0,(strlen($row['COTUnderlying'])-strlen($row['SName'])));
								$fair[$underlying] += $row['Cash'];
							}
							else{
								$fair[$row['COTUnderlying']] += $row['Cash'];
							}
						}
					}
					mysqli_close($con);
					foreach($accounting as $ul => $profit) {
						echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account']) ."</td><td style='text-align:right;'>".number_format($fair[$ul]-$lastyearprofit[$ul]['Fair'])."</td></tr>";
						$sumAcc += $profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'];
						$sumFair += $fair[$ul]-$lastyearprofit[$ul]['Fair'];
					}
					echo "<tr><th style='text-align:center;'>Total</th><th style='text-align:right;'>".number_format($sumAcc)."</th><th style='text-align:right;'>".number_format($sumFair)."</th></tr>";
				?>
		      </tbody>
		    </table>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-6 col-md-offset-3">
			<h3><button type="button" id="senddata" class="btn btn-primary btn-lg btn-block">บันทึกข้อมูลนี้ลงฐานข้อมูล</button></h3>
		</div>
	</div>
</div>
</body>
<script>
$('#senddata').click(function(){
	$.ajax({
        type: 'post',
        url: 'lastyearprofitsave.php',
        data: {save: '1'},
        success: function( data ) {
        	alert( data );
        }
    });    
});
</script>
</html>