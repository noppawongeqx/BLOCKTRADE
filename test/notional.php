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
  <script src="http://cdn.oesmith.co.uk/morris-0.4.1.min.js"></script>
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
			<caption><h3>Today Notional</h3></caption>
			<thead>
		        <tr>
		          <th style='text-align:center;'>Underlying </th>
		          <th style='text-align:right;'>Notional </th>
		          <th style='text-align:right;'>MTM Loss (%)</th>
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

// Sum last year profit. 		      	
		      $sql = "SELECT SUM(LYPAccount) AS Account,SUM(LYPFair) AS Fair,LYPStock FROM `lastyearprofit` GROUP BY LYPStock";
		      $result = mysqli_query($con, $sql);
		      	
		      if (mysqli_num_rows($result) > 0) {
		      	while($row = mysqli_fetch_assoc($result)) {
		      		$lastyearprofit[$row['LYPStock']]['Account'] = $row['Account'];
		      		$lastyearprofit[$row['LYPStock']]['Fair'] = $row['Fair'];
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
		      
		      
		      $notional = [];
		      $totalNotional = 0;
		      $LongNotional = 0;
		      $ShortNotional = 0;
		      $sql = "SELECT CTOUnderlying,CTOPosition,CTOSpot,CTOVolumeCurrent AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0";
		      $result = mysqli_query($con, $sql);
		      
		      if (mysqli_num_rows($result) > 0) {
		      	while($row = mysqli_fetch_assoc($result)) {
		      		if(array_key_exists($row['CTOUnderlying'],$notional)){
		      			$notional[$row['CTOUnderlying']] += $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
		      		}
		      		else {
		      			$notional[$row['CTOUnderlying']] = $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
		      		}
		      		if($row['CTOPosition'] == "Long"){
		      			$LongNotional += $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
		      		}
		      		elseif($row['CTOPosition'] == "Short"){
		      			$ShortNotional += $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
		      		}
		      	}
		      }
		      foreach($notional as $ul => $value) {
		      	$MTM = ($accounting[$ul]+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'])/$value*100;
		      
		      	echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($value) ."</td>";
		      	if ($MTM < -2){
		      		echo "<td style='text-align:right;'><font color='red'><strong>".number_format($MTM,2)."</strong></font></td></tr>";
		      	}
		      	else {
		      		echo "<td style='text-align:right;'>".number_format($MTM,2)."</td></tr>";
		      	}
		      	$totalNotional += $value;
		      }
		      echo "<tr><th style='text-align:center;'>Total</th><td style='text-align:right;'>" . number_format($totalNotional) ."</td></tr>";
		      ?>
		      </tbody>
		    </table>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-4 col-md-offset-4">
			<table class="table">
			<caption><h3>Long / Short Notional</h3></caption>
			<thead>
		        <tr>
		          <th style='text-align:center;'>Position </th>
		          <th style='text-align:right;'>Notional </th>
		        </tr>
		      </thead>
		      <tbody>
		      <?php 
		      	echo "<tr><th style='text-align:center;'>Long</th><td style='text-align:right;'>".number_format($LongNotional)."</td></tr>";
		      	echo "<tr><th style='text-align:center;'>Short</th><td style='text-align:right;'>".number_format($ShortNotional)."</td></tr>";
		      	echo "<tr><th style='text-align:center;'>Total</th><td style='text-align:right;'>".number_format($LongNotional+$ShortNotional)."</td></tr>";
		      ?>
		      </tbody>
		    </table>
		</div>
	</div>
	<div class='row'>
		<div class="col-md-4 col-md-offset-4">
			<table class="table">
			<caption><h3>Average, Max, Min</h3></caption>
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
	   	  $sql = "SELECT DATE(NDate) AS wan,notional FROM `notional` ORDER BY NDate ASC";
	   	  // $sql = "SELECT DATE(NDate) AS wan,notional FROM `notional` WHERE NDate >= ( CURDATE() - INTERVAL $nodate DAY ) ORDER BY NDate ASC";
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