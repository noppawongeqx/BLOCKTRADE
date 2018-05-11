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

if(!isset($_SESSION['loggedin'])){
	header('Location: login.php');
	exit();
}
require 'menu.php';?>
<div class = "container">
	<div class= "row">
	<?php 
		
		$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0";
				
		$servername = "172.16.24.235";
		$username = "mycustom";
		$password = "mypass";
		$dbname = "blocktradetest";
		
		$queryresult = 0;
			// Create connection
		$con = mysqli_connect($servername, $username, $password, $dbname);
		if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
		}
		
		$result = mysqli_query($con,$sql);
		echo "<div class='col-md-12'>";
		echo "<table class='table'>";
		echo "<caption><h1>Position ทั้งหมดที่เปิดอยู่</h1></caption>";
		echo "<thead>
					<tr>
					<th>Initial Date</th>
					  <th>Account</th>
			          <th>Position</th>
			          <th>Underlying</th>
			          <th>Volume(Start)</th>
					  <th>Volume(Now)</th>
					  <th>Spot</th>
			          <th>ดอกมัดจำ</th>
					  <th>ราคาเริ่มต้น</th>
					  <th>Value</th>
					  <th>JPM</th>
					  <th>FutureJPM</th>
			        </tr>
			      </thead>
			      <tbody>";
		if(mysqli_num_rows($result)>0){
			$queryresult = mysqli_num_rows($result);
			while($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			if ($row['CTOPosition'] == "Long")
					echo "<td><button type='button' class='btn btn-success' disabled='disabled' value='".$row['CTOPosition']."'>Long</button></td>";
					elseif ($row['CTOPosition'] == "Short")
					echo "<td><button type='button' class='btn btn-danger' disabled='disabled' value='".$row['CTOPosition']."'>Short</button></td>";
					echo "<td>".$row['CTOFutureName']."</td>";
					echo "<td>".$row['CTOVolumeStart']."</td>";
					echo "<td>".$row['CTOVolumeCurrent']."</td>";
					echo "<td>".$row['CTOSpot']."</td>";
					echo "<td>".$row['CTOUpfrontInterest']."</td>";
					echo "<td>".$row['CTOFuturePrice']."</td>";
					echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";									
					echo "<td>".$row['isJPM']."</td>";
					echo "<td>".$row['jpmfutureprice']."</td>";

					echo "</tr>";
				}
			}
		echo "</tbody></table></div>";
			mysqli_close($con);
		if($queryresult == 0) {
			echo "No result";
		}
		else
			echo "<a class='btn btn-primary' href='excel.php?current=".urlencode($sql)."' role='button'>Excel (Current Internal Report)</a>
				  <a class='btn btn-danger' href='excel.php?current2=".urlencode($sql)."' role='button'>Excel (Current BlockTrade Report)</a>";
	
	?>
	</div>
</div>
</body>
</html>