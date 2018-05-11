<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link href="css/styles.css" rel="stylesheet">
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<!-- <script src="http://cdn.oesmith.co.uk/morris-0.4.1.min.js"></script> -->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
	<meta charset=utf-8 />
<title>Notional</title>
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
$newyear = new DateTime(($y-1).'-12-31');
$nodate = $newyear->diff($today)->format("%a");
?>
<div class='contrainer'>
	<div class='row'>
		<div class="col-md-8 col-md-offset-2">
  			<h3 class='text-center'>Notional (Baht) last <?php echo $nodate;?> days</h3>
  		</div>
  	</div>
	<div class='row'>
		<div class="col-md-8 col-md-offset-2">
  			<div id="line-example"></div>
  		</div>
  	</div>
  	<div class='row'>
		<div class="col-md-4 col-md-offset-4">

			<table class="table">
			  <caption><h3>BlockTrade Notional (Baht)</h3></caption>
			  <thead>
		        <tr>
		          <th style='text-align:center;'>Underlying </th>
		          <th style='text-align:right;'>Internal</th>
		          <th style='text-align:right;color:brown;'>External</th>
		          <th style='text-align:right;color: #1A397D;'>Total</th>
		          <th style='text-align:right;'>MTM Loss (%)</th>
		          <th style='text-align:right;color: #1A397D;'>MTM Loss (%)</th>
		        </tr>
		      </thead>
		      <tbody>


		      <?php 
		      $servername = "172.16.24.235";
		      $username = "mycustom";
		      $password = "mypass";
		      $dbname = "blocktradetest";
		      $con = mysqli_connect($servername, $username, $password, $dbname);

//Get Current Price From Aspen on Excel
		      error_reporting(E_ALL ^ E_NOTICE);
		      require_once 'excel/excel_reader2.php';
		      $data = new Spreadsheet_Excel_Reader("excel/example.xls");
		      $spot = [];
		      $futures = [];
		      $rowcount = $data->rowcount($sheet_index=0);
		      //Spot price
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
		      //Future Settlement Price
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

// Sum last year profit. 		      	
		      $sql = "SELECT SUM(LYPAccount) AS Account,SUM(LYPFair) AS Fair,LYPStock FROM `lastyearprofit` GROUP BY LYPStock";
		      $result = mysqli_query($con, $sql);
		      	
		      if (mysqli_num_rows($result) > 0) {
		      	while($row = mysqli_fetch_assoc($result)) {
		      		$lastyearprofit[$row['LYPStock']]['Account'] = $row['Account'];
		      		// $lastyearprofit[$row['LYPStock']]['Fair'] = $row['Fair'];
		      	}
		      }

// Sum cash from company transaction, that will correct every case.
// and mark2market outstanding of stock and future 

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

// Sum cash dividend for accounting	
		      $sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
		      if($result = mysqli_query($con,$sql)){
		      	if (mysqli_num_rows($result) > 0) {
		      		while($row = mysqli_fetch_array($result)) {
		      			// $cashindividend[$row['DStock']] = ($row['DDividend']*$row['DVolume']);
						$cashindividend[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
		      		}
		      	}
		      }
		      
// Notional Parts
//0 or none :total=old version  ;  2:internal ;  3:external 
			  $notional = [];
			  $notional2 = [];
			  $notional3 = [];
			  $totalNotional2 = 0;
			  $totalNotional3 = 0;
			  $LongNotional2 = 0;
			  $LongNotional3 = 0;
			  $ShortNotional2 = 0;
			  $ShortNotional3 = 0;
			  $accountingTotal = 0;

			  $sql = "SELECT CTOUnderlying,CTOPosition,CTOSpot,isJPM,CTOVolumeCurrent AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0";
		      $result = mysqli_query($con, $sql);
		      if (mysqli_num_rows($result) > 0) {
		      	while($row = mysqli_fetch_assoc($result)) {

		      		$tempNotional = $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];

		      		if(array_key_exists($row['CTOUnderlying'],$notional)){		      								
		      				if(is_null($row['isJPM']) || empty($row['isJPM'])){
								$notional[$row['CTOUnderlying']] += $tempNotional;
								$notional2[$row['CTOUnderlying']] += $tempNotional;								
								if($row['CTOPosition'] == "Long"){
									$LongNotional2 += $tempNotional;
								}else{
									$ShortNotional2 += $tempNotional;
								}
							}else{
								$notional[$row['CTOUnderlying']] += $tempNotional;
								$notional3[$row['CTOUnderlying']] += $tempNotional;
								if($row['CTOPosition'] == "Long"){
									$LongNotional3 += $tempNotional;
								}else{
									$ShortNotional3 += $tempNotional;
								}
							}
		      		}else{
							if(is_null($row['isJPM']) || empty($row['isJPM'])){
								$notional[$row['CTOUnderlying']] = $tempNotional;
								$notional2[$row['CTOUnderlying']] = $tempNotional;
								$notional3[$row['CTOUnderlying']] = 0;
								if($row['CTOPosition'] == "Long"){
									$LongNotional2 += $tempNotional;
								}
								elseif($row['CTOPosition'] == "Short"){
									$ShortNotional2 += $tempNotional;
								}
							}else{
								$notional[$row['CTOUnderlying']] = $tempNotional;
								$notional2[$row['CTOUnderlying']] = 0;
								$notional3[$row['CTOUnderlying']] = $tempNotional;
								if($row['CTOPosition'] == "Long"){
									$LongNotional3 += $tempNotional;
								}else{
									$ShortNotional3 += $tempNotional;
								}								
							}
		      		}
		      	}
		      }



		      foreach($notional as $ul => $value) {
		      	$accountingTotal += ($accounting[$ul]+$cashindividend[$ul]-$lastyearprofit[$ul]['Account']);


		      	if ($value <1000){
					$MTM = 0;
				}else{
					$MTM = number_format(($accounting[$ul]+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'])/$value*100, 2, '.', ''); 
				}

				if ($notional2[$ul] <1000){
					$MTM2 = 0;
				}else{
					$MTM2 = number_format(($accounting[$ul]+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'])/$notional2[$ul]*100, 2, '.', ''); //internal only
				}
			
		      	if ($notional2[$ul] <1000){
					echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'></td>";
		      	}else{
		    	  	echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($notional2[$ul]) ."</td>";
		      	}
		      	if ($notional3[$ul] <1000){
					echo    "<td style='text-align:right;'></td>";
		      	}else{
		    	  	echo    "<td style='text-align:right;'>" . number_format($notional3[$ul]) ."</td>";
		      	}

				echo    "<td style='text-align:right;color: #1A397D;'>" . number_format($value) ."</td>";

		      	if ($MTM2 < -2){
		      		echo "<td style='text-align:right;'><font color='red'><strong>".number_format($MTM2, 2, '.', '')."</strong></font></td>";
		      		echo "<td style='text-align:right;'><font color='red'><strong>".number_format($MTM, 2, '.', '')."</strong></font></td></tr>";
		      	}
		      	else {
		      		echo "<td style='text-align:right;'>".number_format($MTM2,2)."</td>";
		      		echo "<td style='text-align:right;color: #1A397D;'>".number_format($MTM,2)."</td></tr>";
		      	}
		      	$totalNotional2 += $notional2[$ul];	
		      	$totalNotional3 += $notional3[$ul];
		      }
		      echo "<tr><th style='text-align:center;'>Total</th>";
		      echo "<th style='text-align:right;'>" . number_format($totalNotional2) ."</th>";
		      echo "<th style='text-align:right;'>" . number_format($totalNotional3) ."</th>";
			  echo "<th style='text-align:right;color: #1A397D;'>" . number_format($totalNotional2+$totalNotional3) ."</th>";

			  if ($totalNotional2 <1000){
			  		echo "<th style='text-align:right;color: #1A397D;'></th>";
		      }else{
		    	  	echo "<th style='text-align:right;color: #1A397D;'>" . number_format($accountingTotal*100/($totalNotional2), 2, '.', '')."</th>";
		      }

		      if (($totalNotional2+$totalNotional3) <1000){
		      	echo "<th style='text-align:right;color: #1A397D;'></th></tr>";
		      }else{
		    	echo "<th style='text-align:right;color: #1A397D;'>" . number_format($accountingTotal*100/($totalNotional2+$totalNotional3), 2, '.', '') ."</th></tr>";
		      }

		      ?>

		      </tbody>
		    </table>
		    <!-- <h5>ps. M2M Loss (%) calculated by Profit/Loss from Accounting / Notional </h5> -->
		</div>
	</div>
	<div class='row'>
		<div class="col-md-4 col-md-offset-4">
			<table class="table">
			<caption><h3>Long / Short Notional</h3></caption>
			<thead>
		        <tr>
		          <th style='text-align:center;'>Position </th>
		          <th style='text-align:right;'>Internal  </th>
		          <th style='text-align:right;'>External  </th>
		          <th style='text-align:right;color: #1A397D;'>Total Notional </th>
		          <!-- <th style='text-align:right;color: #1A397D;'>MTM Loss (%)</th> -->
		        </tr>
		      </thead>
		      <tbody>
		      <?php 
		      	echo "<tr><th style='text-align:center;'>Long</th><td style='text-align:right;'>".number_format($LongNotional2)."</td><td style='text-align:right;'>".number_format($LongNotional3)."</td><td style='text-align:right;color: #1A397D;'>".number_format($LongNotional2+$LongNotional3)."</td></tr>";
		      	echo "<tr><th style='text-align:center;'>Short</th><td style='text-align:right;'>".number_format($ShortNotional2)."</td><td style='text-align:right;'>".number_format($ShortNotional3)."</td><td style='text-align:right;color: #1A397D;'>".number_format($ShortNotional2+$ShortNotional3)."</td></tr>";
		      	echo "<tr><th style='text-align:center;'>Total</th><th style='text-align:right;'>".number_format($LongNotional2 + $ShortNotional2)."</th><th style='text-align:right;'>".number_format($LongNotional3 + $ShortNotional3)."</th><th style='text-align:right;color: #1A397D;'>".number_format($LongNotional2 + $ShortNotional2+$LongNotional3 + $ShortNotional3)."</th></tr>";
		      ?>
		      </tbody>
		    </table>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-4 col-md-offset-4">
			<table class="table">
			<caption><h3>Average, Max, Min of Notional </h3></caption>
			<thead>
		      </thead>
		      <tbody>
		      <?php 
		      $y = date("Y");
		      $oldYear = $y-1;
		      $sql = "SELECT AVG(notional) AS notional,MAX(notional) AS maxNot,MIN(notional) AS minNot FROM `notional` WHERE NDate >='$oldYear-12-31'" ;;
		      $result = mysqli_query($con, $sql);
		      
		      if (mysqli_num_rows($result) > 0) {
		      	while($row = mysqli_fetch_assoc($result)) {
		      		echo "<tr><th style='text-align:center;'>Average Notional</th><td style='text-align:right;'>".number_format($row['notional'])."</td></tr>";
		      		echo "<tr><th style='text-align:center;'>Max Notional</th><td style='text-align:right;'>".number_format($row['maxNot'])."</td></tr>";
		      		echo "<tr><th style='text-align:center;'>Min Notional</th><td style='text-align:right;'>".number_format($row['minNot'])."</td></tr>";
		      	}
		      }
		      ?>
		      </tbody>
		    </table>
		</div>
	</div>

	<!-- 	<div class='row'>
		<div class='col-md-4 col-md-offset-4'>
			//Additional space for button of excel exporter
		</div>
	</div> -->

 </div>
  <script type="text/javascript">
  Morris.Line({
	  element: 'line-example',
	  data: [
	   	  <?php 
	   	 
	   	  // Check connection
	   	  if (!$con) {
	   	  	die("Connection failed: " . mysqli_connect_error());
	   	  }
	   	  // $sql = "SELECT DATE(NDate) AS wan,notional FROM `notional` ORDER BY NDate ASC";
	   	  $sql = "SELECT DATE(NDate) AS wan,notional FROM `notional` WHERE NDate >= ( CURDATE() - INTERVAL $nodate DAY ) ORDER BY NDate ASC";
	   	  $result = mysqli_query($con, $sql);
	   	  $num = 1;
	   	  if (mysqli_num_rows($result) > 0) {
	   	  	while($row = mysqli_fetch_assoc($result)) {
	   	  		if($num == 1){
	   	  			echo "{ y: '".$row['wan']."' , a: ".$row['notional']."}";
	   	  			$num++;
	   	  		}
	   	  		else 
	   	  			echo ",{ y: '".$row['wan']."' , a: ".$row['notional']."}";
	   	  	}
	   	  }
	   	  mysqli_close($con);
	   	  ?>
	  ],
	  xkey: 'y',
	  ykeys: ['a'],
	  labels: ['Notional']
	});
  </script>
</body>
</html>