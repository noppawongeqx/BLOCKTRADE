<!DOCTYPE html>
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link href="../css/styles.css" rel="stylesheet">
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

if(isset($_POST['account']) AND isset($_POST['closedate'])){
	//Result of All transaction of given account
	$account = $_POST['account'];
	$closedate = date_create($_POST['closedate']);
	
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE C.Account = '$account' AND CTO.CTOVolumeCurrent > 0";
		
	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption>Current Position of ".$account."</caption>
		      <thead>
		        <tr>
		          <th>Position</th>
		          <th>Futures</th>
				  <th>Cost</th>
		          <th>Volume(Start)</th>
				  <th>Volume(Now)</th>
				  <th>Value</th>
		          <th>Initial Date</th>
		          <th>ดอกมัดจำ</th>
				  <th>ดอกทั้งหมด</th>
				  <th>JPM</th>					  
				  <th>FutureJPM</th>
				  <th>Action</th>
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				if ($row['CTOPosition'] == "Long")
					echo "<td><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
				elseif ($row['CTOPosition'] == "Short")
				echo "<td><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";
				echo "<td>".$row['CTOFutureName']."</td>";
				echo "<td>".$row['CTOFuturePrice']."</td>";
				echo "<td>".$row['CTOVolumeStart']."</td>";
				echo "<td>".$row['CTOVolumeCurrent']."</td>";
				echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td>".$myFormatForView."</td>";
				echo "<td>".$row['CTOUpfrontInterest']."</td>";
				echo "<td>".$row['CTOTotalInterest']."</td>";
				$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
				$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');
				echo "<td>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
				echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$account."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
				echo "</tr>";
				$number++;
			}
		}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	// else
	// 	// echo "<br><br><a class='btn btn-success' href='excel.php?open=".urlencode($sql)."' role='button'>Download to Excel</a>";
	// 	echo "<a class='btn btn-primary' href='excel.php?current=".urlencode($sql)."' role='button'>Excel (Internal Report)</a>
	// 			  <a class='btn btn-danger' href='excel.php?current2=".urlencode($sql)."' role='button'>Excel (BlockTrade Report)</a>";	
}elseif(isset($_POST['account2']) AND isset($_POST['closedate2'])){
	
	$account = $_POST['account2'];
	$closedate = date_create($_POST['closedate2']);
	
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE C.Account = '$account' AND CTO.CTOVolumeCurrent > 0 ORDER BY CTOFutureName ASC,CTOTranDate ASC ";
	
	// like concat('%','$account','%') 

	$sql2 = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE  C.Account = '$account' AND CTO.CTOVolumeCurrent > 0 ORDER BY CTOFutureName ASC,CTOTranDate ASC ";	

	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption><h3 class='text-center' style='text-align:center;color:black;'>Current Position of ".$account."</h3></caption>
		      <thead>
		        <tr>
		          <th class='text-center'>OpenDate</th>
		          <th class='text-center'>Account</th>
		          <th class='text-center'>Position</th>
		          <th class='text-center'>Instrument</th>
				  <th class='text-center'>Volume(Start)</th>
				  <th class='text-center'>Volume(Now)</th>
				  <th class='text-center'>Spot</th>
		          <th class='text-center'>Upfront</th>
		          <th class='text-center'>Cost</th>
		 		  <th class='text-center'>Rate%</th>
				  <th class='text-center'>MinDays</th>
				  <th class='text-center'>MinBss</th>
		          <th class='text-center'>JPM</th>					  
				  <th class='text-center'>FutureJPM</th>
				  <th class='text-center'>Action</th>
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			if ($row['isJPM'] == "Y"){
				echo "<tr style='color:#002b80;font-weight:bold;'>";
			}else{
				echo "<tr>";				
			}
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td class='text-center'>".$myFormatForView."</td>";
			echo "<td class='text-center'>".$account."</td>";
			if ($row['CTOPosition'] == "Long")
			echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
			elseif ($row['CTOPosition'] == "Short")
			echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";			
			echo "<td class='text-center'>".$row['CTOFutureName']."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeStart'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeCurrent'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOSpot'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOFuturePrice'],5)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOPercentInterest'],2)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOMinimumDay'],0)."</td>";
			echo "<td class='text-center'>".$row['CTOMinimumInt']."</td>";
			echo "<th class='text-center'>".$row['isJPM']."</th>";
			echo "<th class='text-right'>".($row['jpmfutureprice'])."</th>";
			$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
			$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');		
			echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$account."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
			echo "</tr>";
			$number++;
		}
	}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	else
		echo "<a class='btn btn-primary' href='excel.php?current3=".urlencode($sql2)."&account3=".$account."' role='button'>Excel (Current Internal Report)</a>
		<a class='btn btn-danger' href='excel.php?current4=".urlencode($sql2)."&account4=".$account."' role='button'>Excel (Current BlockTrade Report)</a>";	
}elseif(isset($_POST['accountisjpm']) AND isset($_POST['closedate2'])){
	
	$account = $_POST['accountisjpm'];
	$closedate = date_create($_POST['closedate2']);
	
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.isJPM is not null AND CTO.CTOVolumeCurrent > 0 ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate ASC ";

	$sql2 = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 and CTO.isJPM is not null ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate ASC ";	

	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption><h3 class='text-center' style='text-align:center;color:black;'>All Current Position at JPM</h3></caption>
		      <thead>
		        <tr>
		          <th class='text-center'>OpenDate</th>
		          <th class='text-center'>Account</th>
		          <th class='text-center'>Position</th>
		          <th class='text-center'>Instrument</th>
				  <th class='text-center'>Volume(Start)</th>
				  <th class='text-center'>Volume(Now)</th>
				  <th class='text-center'>Spot</th>
		          <th class='text-center'>Upfront</th>
		          <th class='text-center'>Cost</th>
		 		  <th class='text-center'>Rate%</th>
				  <th class='text-center'>MinDays</th>
				  <th class='text-center'>MinBss</th>
		          <th class='text-center'>JPM</th>					  
				  <th class='text-center'>FutureJPM</th>
				  <th class='text-center'>Action</th>
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";				
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td class='text-center'>".$myFormatForView."</td>";
			echo "<td class='text-center'>".$row['Account']."</td>";
			if ($row['CTOPosition'] == "Long")
			echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
			elseif ($row['CTOPosition'] == "Short")
			echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";			
			echo "<td class='text-center'>".$row['CTOFutureName']."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeStart'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeCurrent'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOSpot'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOFuturePrice'],5)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOPercentInterest'],2)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOMinimumDay'],0)."</td>";
			echo "<td class='text-center'>".$row['CTOMinimumInt']."</td>";
			echo "<th class='text-center'>".$row['isJPM']."</th>";
			echo "<th class='text-right'>".($row['jpmfutureprice'])."</th>";
			$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
			$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');		
			echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$row['Account']."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
			echo "</tr>";
			$number++;
		}
	}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	else{
		echo "<a class='btn btn-primary' href='excel.php?currentJPM=".urlencode($sql2)."' role='button'>Excel (Current Internal Report)</a>
		<a class='btn btn-danger' href='excel.php?currentJPM2=".urlencode($sql2)."' role='button'>Excel (Current BlockTrade Report)</a>";	
	}
}elseif(isset($_POST['accountisstock']) AND isset($_POST['closedate2'])){
	
	$account = $_POST['accountisstock'];
	$isstock = "%".$_POST['accountisstock']."%";
	$closedate = date_create($_POST['closedate2']);
	
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOVolumeCurrent > 0 and  CTO.CTOFutureName like concat('%','$isstock','%') ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate ASC ";

	$sql2 = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOVolumeCurrent > 0 and  CTO.CTOFutureName like concat('%','$isstock','%') ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate ASC ";	

	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption><h3 class='text-center' style='text-align:center;color:black;'>Current Position of ".$account." </h3></caption>
		      <thead>
		        <tr>
		          <th class='text-center'>OpenDate</th>
		          <th class='text-center'>Account</th>
		          <th class='text-center'>Position</th>
		          <th class='text-center'>Instrument</th>
				  <th class='text-center'>Volume(Start)</th>
				  <th class='text-center'>Volume(Now)</th>
				  <th class='text-center'>Spot</th>
		          <th class='text-center'>Upfront</th>
		          <th class='text-center'>Cost</th>
		 		  <th class='text-center'>Rate%</th>
				  <th class='text-center'>MinDays</th>
				  <th class='text-center'>MinBss</th>
		          <th class='text-center'>JPM</th>					  
				  <th class='text-center'>FutureJPM</th>
				  <th class='text-center'>Action</th>
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";				
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td class='text-center'>".$myFormatForView."</td>";
			echo "<td class='text-center'>".$row['Account']."</td>";
			if ($row['CTOPosition'] == "Long")
			echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
			elseif ($row['CTOPosition'] == "Short")
			echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";			
			echo "<td class='text-center'>".$row['CTOFutureName']."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeStart'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeCurrent'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOSpot'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOFuturePrice'],5)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOPercentInterest'],2)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOMinimumDay'],0)."</td>";
			echo "<td class='text-center'>".$row['CTOMinimumInt']."</td>";
			echo "<th class='text-center'>".$row['isJPM']."</th>";
			echo "<th class='text-right'>".($row['jpmfutureprice'])."</th>";
			$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
			$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');		
			echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$row['Account']."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
			echo "</tr>";
			$number++;
		}
	}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	else{
		echo "<a class='btn btn-primary' href='excel.php?currentstock5=".urlencode($sql2)."&account5=".$account."' role='button'>Excel (Current Internal Report)</a>
		<a class='btn btn-danger' href='excel.php?currentstock6=".urlencode($sql2)."&account6=".$account."' role='button'>Excel (Current BlockTrade Report)</a>";	
	}
}elseif(isset($_POST['closedate'])){
	$closedate = date_create($_POST['closedate']);
	
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOVolumeCurrent > 0 ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate DESC ";
		
	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption>Current Position</caption>
		      <thead>
		        <tr>
 				  <th>Account</th>
		          <th>Position</th>
		          <th>Futures</th>
				  <th>Cost</th>
		          <th>Volume(Start)</th>
				  <th>Volume(Now)</th>
				  <th>Value</th>
		          <th>Initial Date</th>
		          <th>ดอกมัดจำ</th>
				  <th>ดอกทั้งหมด</th>
				  <th>JPM</th>					  
				  <th>FutureJPM</th>
				  <th>Action</th>		
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				echo "<td>".$row['Account']."</td>";
				if ($row['CTOPosition'] == "Long")
					echo "<td><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
				elseif ($row['CTOPosition'] == "Short")
				echo "<td><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";
				echo "<td>".$row['CTOFutureName']."</td>";
				echo "<td>".$row['CTOFuturePrice']."</td>";
				echo "<td>".$row['CTOVolumeStart']."</td>";
				echo "<td>".$row['CTOVolumeCurrent']."</td>";
				echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td>".$myFormatForView."</td>";
				echo "<td>".$row['CTOUpfrontInterest']."</td>";
				echo "<td>".$row['CTOTotalInterest']."</td>";
				$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
				$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');
				echo "<td>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
				echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$row['Account']."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
				echo "</tr>";
				$number++;
			}
		}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	// else
	// 	// echo "<br><br><a class='btn btn-success' href='excel.php?open=".urlencode($sql)."' role='button'>Download to Excel</a>";
	// 	echo "<a class='btn btn-primary' href='excel.php?current=".urlencode($sql)."' role='button'>Excel (Internal Report)</a>
	// 			  <a class='btn btn-danger' href='excel.php?current2=".urlencode($sql)."' role='button'>Excel (BlockTrade Report)</a>";	
}elseif(isset($_POST['closedate2'])){
	$closedate = date_create($_POST['closedate2']);
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
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SLastTradingDay,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOVolumeCurrent > 0 ORDER BY CTOFutureName ASC,Account ASC,CTOTranDate ASC ";
	
	$sql2 = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOTotalInterest,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOVolumeCurrent > 0  ORDER BY CTO.CTOFutureName ASC,Account ASC,CTO.CTOTranDate ASC ";

	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<caption><h3 class='text-center' style='text-align:center;color:black;'>Current Position of All Account</h3></caption>
		      <thead>
		        <tr>
		          <th class='text-center'>OpenDate</th>
		          <th class='text-center'>Account</th>
		          <th class='text-center'>Position</th>
		          <th class='text-center'>Instrument</th>
				  <th class='text-center'>Volume(Start)</th>
				  <th class='text-center'>Volume(Now)</th>
				  <th class='text-center'>Spot</th>
		          <th class='text-center'>Upfront</th>
		          <th class='text-center'>Cost</th>
		 		  <th class='text-center'>Rate%</th>
				  <th class='text-center'>MinDays</th>
				  <th class='text-center'>MinBss</th>
		          <th class='text-center'>JPM</th>					  
				  <th class='text-center'>FutureJPM</th>
				  <th class='text-center'>Action</th>
		        </tr>
		      </thead>
		      <tbody>";
	$number = 1;
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			if ($row['isJPM'] == "Y"){
				echo "<tr style='color:#002b80;font-weight:bold;'>";
			}else{
				echo "<tr>";				
			}
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td class='text-center'>".$myFormatForView."</td>";
			echo "<td class='text-center'>".$row['Account']."</td>";
			if ($row['CTOPosition'] == "Long")
			echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Long</button></td>";
			elseif ($row['CTOPosition'] == "Short")
			echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' id='position".$number."' value='".$row['CTOPosition']."'>Short</button></td>";			
			echo "<td class='text-center'>".$row['CTOFutureName']."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeStart'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOVolumeCurrent'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTOSpot'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOFuturePrice'],5)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOPercentInterest'],2)."</td>";
			echo "<td class='text-center'>".number_format($row['CTOMinimumDay'],0)."</td>";
			echo "<td class='text-center'>".$row['CTOMinimumInt']."</td>";
			echo "<th class='text-center'>".$row['isJPM']."</th>";
			echo "<th class='text-right'>".($row['jpmfutureprice'])."</th>";
			$hodingday = (date_diff($closedate,date_create($row['CTOTranDate']))->format("%a"));
			$hodinginterest = number_format($row['CTOSpot'] * $row['CTOPercentInterest'] / 100 * $hodingday / 365,5,'.',',');
			echo "<td><a class='btn btn-primary' href='saveClose.php?acc=".$row['Account']."&id=".$row['CTOID']."&p=".$row['CTOPosition']."&ui=".$row['CTOUnderlying']."&mindate=".$row['CTOMinimumDay']."&minint=".$row['CTOMinimumInt']."&s=".$row['SName']."&vol=".$row['CTOVolumeStart']."&volCur=".$row['CTOVolumeCurrent']."&cost=".$row['CTOFuturePrice']."&idate=".$myFormatForView."&up=".$row['CTOUpfrontInterest']."&discount=".$row['CTODiscount']."&holday=".$hodingday."&spot=".$row['CTOSpot']."&interest=".$row['CTOPercentInterest']."&multi=".$row['SMultiplier']."&isJPM=".$row['isJPM']."&value=".number_format($row['CTOValue'],2,'.',',')."' role='button' target='_blank'>Action</a></td>";
			echo "</tr>";
			$number++;
		}
	}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	else
		echo "<a class='btn btn-primary' href='excel.php?current=".urlencode($sql2)."' role='button'>Excel (Current Internal Report)</a>
		<a class='btn btn-danger' href='excel.php?current2=".urlencode($sql2)."' role='button'>Excel (Current BlockTrade Report)</a>";	
}elseif(isset($_POST['openPos']) AND $_POST['openPos'] == 1){
	//Result of Edit Open Position
	$account = $_POST['account'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$underlying = $_POST['underlying'];
	
	$sql = "SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTODiscount,CTO.CTOTotalInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,C.Account,CTO.isJPM,CTO.jpmfutureprice  FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTO.CTOTranDate BETWEEN '$startdate' AND '$enddate'";
	
	echo "<div class='col-md-12'>";
	echo "<h3><p class='text-center'>All Open Transaction Between ".date("d/m/Y", strtotime($startdate))." To  ".date("d/m/Y", strtotime($enddate));
	if($account != ""){
		$sql .= " AND C.Account like '%$account%'";
		echo " of Account $account";
	}
	if($underlying != ""){
		$sql .= " AND CTO.CTOUnderlying like '%$underlying%'";
		echo " AND  Underlying : $underlying";
	}
	// if($account != ""){
	// 	$sql = $sql." AND C.Account = '$account'";
	// 	echo " of Account $account";
	// }
	// if($underlying != ""){
	// 	$sql = $sql." AND CTO.CTOUnderlying = '$underlying'";
	// 	echo " AND  Underlying : $underlying";
	// }
	$sql .= " ORDER BY CTOFutureName ";
	echo "</p></h3>";
	echo "</div>";
	
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
	echo "<thead>
		        <tr>
		          <th class='text-center'>OpenDate</th>
				  <th class='text-center'>Account</th>
		          <th class='text-center'>Position</th>
		          <th class='text-center'>Instrument</th>
		          <th class='text-center'>Vol(Start)</th>
				  <th class='text-center'>Vol(Now)</th>
				  <th class='text-center'>Spot</th>
		          <th class='text-center'>Upfront</th>
		          <th class='text-center'>Discount</th>
				  <th class='text-center'>Cost</th>
				  <th class='text-center'>Rate%</th>
				  <th class='text-center'>MinDays</th>
				  <th class='text-center'>MinBss</th>
  				  <th class='text-center'>JPM</th>					  
				  <th class='text-center'>FutureJPM</th>				  
				  <th class='text-center'>Delete</th>
		        </tr>
		      </thead>
		      <tbody>";
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
				if ($row['isJPM'] == "Y"){
					echo "<tr style='color:#002b80;font-weight:bold;'>";
				}else{
					echo "<tr>";				
				}
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td class='text-center'>".$myFormatForView."</td>";
				echo "<td class='text-center'>".$row['Account']."</td>";
				if ($row['CTOPosition'] == "Long")
					echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' value='".$row['CTOPosition']."'>Long</button></td>";
				elseif ($row['CTOPosition'] == "Short")
				echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' value='".$row['CTOPosition']."'>Short</button></td>";
				echo "<td class='text-center'>".$row['CTOFutureName']."</td>";
				echo "<td class='text-right'>".number_format($row['CTOVolumeStart'])."</td>";
				echo "<td class='text-right'>".number_format($row['CTOVolumeCurrent'])."</td>";
				echo "<td class='text-right'>".number_format($row['CTOSpot'],5)."</td>";
				echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
				echo "<td class='text-right'>".number_format($row['CTODiscount'],5)."</td>";
				echo "<td class='text-right'>".number_format($row['CTOFuturePrice'],5)."</td>";
				echo "<td class='text-right'>".number_format($row['CTOPercentInterest'],2)."</td>";
				echo "<td class='text-right'>".number_format($row['CTOMinimumDay'],0)."</td>";
				echo "<td class='text-right'>".number_format($row['CTOMinimumInt'],5)."</td>";
				// echo "<td class='text-right'>".number_format($row['CTOValue'],2,'.',',')."</td>";
				echo "<th class='text-center'>".$row['isJPM']."</th>";
				echo "<th class='text-right'>".($row['jpmfutureprice'])."</th>";


				if ($row['CTOVolumeCurrent'] == $row['CTOVolumeStart']){
					echo "<td><button type='button' value='".$row['CTOID']."' onclick='del(this.value)' class='btn btn-primary'>delete</button></td>";
				}else{
					echo "<td><button type='button' value='".$row['CTOID']."' onclick='del(this.value)' class='btn btn-primary' disabled='disabled'>delete</button></td>";
				}
				
				echo "</tr>";
			}
		}
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}	
	else
		echo "<a class='btn btn-success' href='excel.php?open=".urlencode($sql)."' role='button'>Download to Excel</a>";	
}elseif(isset($_POST['closePos']) AND $_POST['closePos'] == 2){
	//Result of Edit Close Position
	$account = $_POST['account'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$underlying = $_POST['underlying'];
	
	$sql = "SELECT CTC.CTCID,CTC.CTCTranDate,CTC.CTCPosition,CTC.CTCUnderlying,CTC.CTCFutureName,CTC.CTCVolume,CTC.CTCSpot,CTC.CTCTotalInterest,CTC.CTCNetInterest,CTC.CTCFuturePrice,CTC.CTCValue,CTC.CTCDividend,CTC.CTCUnpaid,CTC.CTCForceCloseFlag,S.SName,C.Account,CTC.jpmfutureclose,CTO.CTOUpfrontInterest FROM customertransactionclose AS CTC INNER JOIN customertransactionopen AS CTO ON CTC.CTOID = CTO.CTOID INNER JOIN customer AS C ON C.CustomerID = CTC.CustomerID INNER JOIN serie AS S ON CTC.SerieID = S.SerieID WHERE CTC.CTCTranDate BETWEEN '$startdate' AND '$enddate'";

	echo "<div class='col-md-12'>";
	echo "<h3><p class='text-center'>All Close Transaction Between ".date("d/m/Y", strtotime($startdate))." To  ".date("d/m/Y", strtotime($enddate));
	if($account != ""){
		$sql .= " AND C.Account like '%$account%'";
		echo " of Account $account";
	}
	if($underlying != ""){
		$sql = $sql." AND CTC.CTCUnderlying like '%$underlying%'";
		echo " AND  Underlying : $underlying";
	}
	// if($account != ""){
	// 	$sql = $sql." AND C.Account = '$account'";
	// 	echo " of Account $account";
	// }
	// if($underlying != ""){
	// 	$sql = $sql." AND CTC.CTCUnderlying = '$underlying'";
	// 	echo " AND  Underlying : $underlying";
	// }
	$sql .= " ORDER BY CTCFutureName";
	echo "</p></h3>";
	echo "</div>";

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
		echo "<thead>
					<tr>
					  <th class='text-center'>CloseDate</th>
					  <th class='text-center'>Account</th>
			          <th class='text-center'>Position</th>
			          <th class='text-center'>Instrument</th>
			          <th class='text-center'>Volume</th>
			          <th class='text-center'>Spot</th>
			          <th class='text-center'>Upfront</th>
			          <th class='text-center'>Interest</th>
			          <th class='text-center'>Dividend</th>
			          <th class='text-center'>Unpaid</th>
			          <th class='text-center'>Price</th>
					  <th class='text-center'>FutureJPM</th>				  
					  <th class='text-center'>Delete</th>
			        </tr>
			      </thead>
			      <tbody>";
		if(mysqli_num_rows($result)>0){
			$queryresult = mysqli_num_rows($result);
			while($row = mysqli_fetch_array($result)) {

			if ($row['jpmfutureclose'] == ''){
					echo "<tr>";
				}else{
					echo "<tr style='color:#002b80;font-weight:bold;'>";				
				}

			$Newformat = strtotime($row['CTCTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td class='text-center'>".$myFormatForView."</td>";
			echo "<td class='text-center'>".$row['Account']."</td>";
			if ($row['CTCPosition'] == "Long")
				echo "<td class='text-center'><button type='button' class='btn btn-success' disabled='disabled' value='".$row['CTCPosition']."'>Long</button></td>";
			elseif ($row['CTCPosition'] == "Short")
				echo "<td class='text-center'><button type='button' class='btn btn-danger' disabled='disabled' value='".$row['CTCPosition']."'>Short</button></td>";
			echo "<td class='text-center'>".$row['CTCFutureName']."</td>";
			echo "<td class='text-right'>".number_format($row['CTCVolume'])."</td>";
			echo "<td class='text-right'>".number_format($row['CTCSpot'],5)."</td>";
			// echo "<td class='text-right'>".number_format($row['CTCTotalInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTOUpfrontInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTCTotalInterest'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTCDividend'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTCUnpaid'],5)."</td>";
			echo "<td class='text-right'>".number_format($row['CTCFuturePrice'],5)."</td>";
			
			// echo "<td class='text-center'>".$row['CTCUnderlying']."</td>";
			
			// echo "<td class='text-right'>".number_format($row['CTCValue'],2,'.',',')."</td>";
			echo "<th class='text-right'>".$row['jpmfutureclose']."</th>";
			echo "<td><button type='button' value='".$row['CTCID']."' onclick='del(this.value)' class='btn btn-primary'>delete</button></td>";
			echo "</tr>";
			}
		}
		echo "</tbody></table></div>";
		mysqli_close($con);
			if($queryresult == 0) {
				echo "No result";
			}
			else
				echo "<a class='btn btn-success' href='excel.php?close=".urlencode($sql)."' role='button'>Download to Excel</a>";		  
}elseif(isset($_POST['company']) AND $_POST['company'] == 1){
	//Get Result of Company Transaction by start-end date; account and stock is optional
	$account = $_POST['account'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$underlying = $_POST['underlying'];
	
	$sql = "SELECT COT.CompanyTransactionID,COT.COTDate,COT.COTPosition,COT.COTUnderlying,COT.COTVolume,COT.COTCost,COT.COTValue,COT.COTCash,S.SName,C.Account FROM companytransaction AS COT INNER JOIN customer AS C ON C.CustomerID = COT.CustomerID INNER JOIN serie AS S ON COT.SerieID = S.SerieID WHERE COT.COTDate BETWEEN '$startdate' AND '$enddate'";
	
	echo "<div class='col-md-12'>";
	echo "<h3><p class='text-center'>Company Transaction Transaction Between ".date("d/m/Y", strtotime($startdate))." To  ".date("d/m/Y", strtotime($enddate));
	if($account != ""){
		$sql .= " AND C.Account like '%$account%'";
		echo " of Account $account";
	}
	if($underlying != ""){
		$sql .= " AND COT.COTUnderlying like '%$underlying%'";
		echo " AND  Underlying : $underlying";
	}	
	// if($account != ""){
	// 	$sql = $sql." AND C.Account = '$account'";
	// 	echo " of Account $account";
	// }

	// if($underlying != ""){
	// 	$sql = $sql." AND COT.COTUnderlying = '$underlying'";
	// 	echo " AND  Underlying : $underlying";
	// }
	$sql .= " ORDER BY COTUnderlying ";
	echo "</p></h3>";
	echo "</div>";
	
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	
	$queryresult = 0;
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	
	$result = mysqli_query($con,$sql);
	echo "	<div class='col-md-12'>";
	echo "	<table class='table'>";
	echo "	<thead>
				<tr>
					<th>Initial Date</th>
					<th>Position</th>
					<th>Underlying</th>
					<th>Volume</th>
					<th>ราคา</th>
					<th>Value</th>
					<th>กระแสเงินสด</th>
				</tr>
		  	</thead>
		  	<tbody>";
				if(mysqli_num_rows($result)>0){
					$queryresult = mysqli_num_rows($result);
					while($row = mysqli_fetch_array($result)) {
						echo "<tr>";
						$Newformat = strtotime($row['COTDate']);
						$myFormatForView = date("d/m/Y", $Newformat);
						echo "<td>".$myFormatForView."</td>";
						if ($row['COTPosition'] == "Long")
							echo "<td><button type='button' class='btn btn-success' disabled='disabled' value='".$row['COTPosition']."'>Long</button></td>";
						elseif ($row['COTPosition'] == "Short")
							echo "<td><button type='button' class='btn btn-danger' disabled='disabled' value='".$row['COTPosition']."'>Short</button></td>";
						echo "<td>".$row['COTUnderlying']."</td>";
						echo "<td>".$row['COTVolume']."</td>";
						echo "<td>".$row['COTCost']."</td>";
						echo "<td>".$row['COTValue']."</td>";
						echo "<td>".$row['COTCash']."</td>";
						echo "</tr>";
					}
				}
	echo "	</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}
	else
		echo "<br><br><a class='btn btn-success' href='excel.php?company=".urlencode($sql)."' role='button'>Download to Excel</a>";
}

?>
</body>
</html>