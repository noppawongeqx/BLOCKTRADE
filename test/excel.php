<?php

if(session_status() == PHP_SESSION_NONE){
	session_start();
}

if(!isset($_SESSION['loggedin'])){
	header('Location: login.php');
	exit();
}

if(isset($_GET['current'])){
	//internal report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	
	if($result = mysqli_query($con,"
		SELECT CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM( 
			SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume
			FROM customertransactionopen 
			INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID 
			WHERE CTOVolumeCurrent > 0 and isJPM is null)
		AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){

		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";

			echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			// echo "<td>".$row['CTOPosition']."</td>";
			echo "<td>".number_format($row['Vol'])."</td>";
			echo "</tr></table>";
		}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['current'];
	if($result = mysqli_query($con,$sql)){
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>
				<th>JPM</th>
				<th>JPMprice</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";

			echo "<td>".$row['CTOFutureName']."</td>";
			// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string

			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";

			if ($row['isJPM'] == "Y"){
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".number_format($row['jpmfutureprice'],2,'.','')."</td>";
			}else{
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
			}

			echo "</tr>";
			$totalValue += $row['CTOValue'];
		}
		// echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td><td></td><td></td></tr></table>";

		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	
		
	}
	
	mysqli_close($con);
}elseif(isset($_GET['current2'])){
	//BlockTrade Report, send to Risk Monitor and Account Control
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
		
	if($result = mysqli_query($con,"
		SELECT CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
			SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume
			FROM customertransactionopen 
			INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID 
			WHERE CTOVolumeCurrent > 0 and isJPM is null)
		AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>";
		echo "</tr></table>";	

		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";

			echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			// echo "<td>".$row['CTOPosition']."</td>";
			echo "<td>".number_format($row['Vol'])."</td>";
			echo "</tr></table>";
		}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['current2'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {

			if($row['isJPM'] == ''){
				echo "<table border='1'><tr>";
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td>".$myFormatForView."</td>";
				echo "<td>".$row['Account']."</td>";
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
						
				echo "<td>".$row['CTOFutureName']."</td>";
				// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
				// add noneprintable character to convert all name to string

				echo "<td>".$row['CTOFuturePrice']."</td>";
				echo "<td>".$row['CTOVolumeCurrent']."</td>";
				echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
				echo "</tr>";
				$totalValue += $row['CTOValue'];
			}
		}
		// echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	


	}
	
	mysqli_close($con);
}elseif(isset($_GET['currentJPM'])){
	//internal JPM report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	
	//if($result = mysqli_query($con,"SELECT CTOUnderlying,CTOPosition,SUM(CTOVolumeCurrent) AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 GROUP BY CTOUnderlying,CTOPosition")){

	if($result = mysqli_query($con,"
		SELECT CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
			SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume
			FROM customertransactionopen 
			INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID 
			WHERE CTOVolumeCurrent > 0 and isJPM = 'WeHaveNoStock')
		AS  CTO group by CTOUnderlying,CTOPosition ORDER BY CTOUnderlying ASC ;")){
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";

			echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			// echo "<td>".$row['CTOPosition']."</td>";
			echo "<td>".number_format($row['Vol'])."</td>";
			echo "</tr></table>";
		}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['currentJPM'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>
				<th>JPM</th>
				<th>JPMprice</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";

			echo "<td>".$row['CTOFutureName']."</td>";
			// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string

			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";

			if ($row['isJPM'] == "Y"){
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".number_format($row['jpmfutureprice'],2,'.','')."</td>";
			}else{
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
			}

			echo "</tr>";
			$totalValue += $row['CTOValue'];
		}
		// echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td><td></td><td></td></tr></table>";

		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	

	}
	
	mysqli_close($con);
}elseif(isset($_GET['currentJPM2'])){
	//internal JPM report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	
	//if($result = mysqli_query($con,"SELECT CTOUnderlying,CTOPosition,SUM(CTOVolumeCurrent) AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 GROUP BY CTOUnderlying,CTOPosition")){

	if($result = mysqli_query($con,"
		SELECT CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
			SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume
			FROM customertransactionopen 
			INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID 
			WHERE CTOVolumeCurrent > 0 and isJPM = 'WeHaveNoStock')
		AS  CTO group by CTOUnderlying,CTOPosition ORDER BY CTOUnderlying ASC ;")){
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";	
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			echo "<td>".number_format($row['Vol'])."</td>";
			echo "</tr></table>";
		}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['currentJPM2'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>";
		echo "</tr></table>";
	
	}
	
	mysqli_close($con);	
}elseif(isset($_GET['current3']) and isset($_GET['account3'])){
	//internal report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$account = $_GET['account3'];

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

	if($result = mysqli_query($con,"
	SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
		SELECT CTOUnderlying,CTOPosition,isJPM, customer.Account as account ,CTOVolumeCurrent * serie.SMultiplier AS Volume FROM customertransactionopen 
		INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID
		INNER JOIN customer ON customertransactionopen.CustomerID = customer.CustomerID
		WHERE CTOVolumeCurrent > 0 and isJPM is null and account = '$account' )
	AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){

			//header info for browser
			header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
			header("Content-type: application/x-msexcel; charset=UTF-8");
			header("Content-Disposition: attachment; filename=$filename.xls");
			//header("Pragma: no-cache");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			//table caption
			echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
			
			echo "<table border='1'><tr>";
			echo "<th>Stock</th>
					<th>Side</th>
					<th>Quantity</th>";
			echo "</tr></table>";
		
			while($row = mysqli_fetch_array($result)) {
				echo "<table border='1'><tr>";

				echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
				// echo "<td>".$row['CTOUnderlying']."</td>";
				// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
				// add noneprintable character to convert all name to string
				
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
				// echo "<td>".$row['CTOPosition']."</td>";
				echo "<td>".number_format($row['Vol'])."</td>";
				echo "</tr></table>";
			}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['current3'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>
				<th>JPM</th>
				<th>JPMprice</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";

			echo "<td>".$row['CTOFutureName']."</td>";
			// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string

			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";

			if ($row['isJPM'] == "Y"){
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".number_format($row['jpmfutureprice'], 2, '.', '')."</td>";
			}else{
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
			}

			echo "</tr>";
			$totalValue += $row['CTOValue'];
		}
		// echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td><td></td><td></td></tr></table>";
		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	

	}
	
	mysqli_close($con);
}elseif(isset($_GET['current4']) and isset($_GET['account4'])){
	//BlockTrade Report, send to Risk Monitor and Account Control
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$account = $_GET['account4'];
	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
		
	if($result = mysqli_query($con,"
	SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
		SELECT CTOUnderlying,CTOPosition,isJPM, customer.Account as account ,CTOVolumeCurrent * serie.SMultiplier AS Volume FROM customertransactionopen 
		INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID
		INNER JOIN customer ON customertransactionopen.CustomerID = customer.CustomerID
		WHERE CTOVolumeCurrent > 0 and isJPM is null and account = '$account' )
	AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){

		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>";
		echo "</tr></table>";

		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";

			echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."</td>";
			// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			// echo "<td>".$row['CTOPosition']."</td>";
			echo "<td>".number_format($row['Vol'])."</td>";
			echo "</tr></table>";
		}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['current4'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {

			if($row['isJPM'] == ''){
				echo "<table border='1'><tr>";
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td>".$myFormatForView."</td>";
				echo "<td>".$row['Account']."</td>";
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
						
				echo "<td>".$row['CTOFutureName']."</td>";
				// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
				// add noneprintable character to convert all name to string

				echo "<td>".$row['CTOFuturePrice']."</td>";
				echo "<td>".$row['CTOVolumeCurrent']."</td>";
				echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
				echo "</tr>";
				$totalValue += $row['CTOValue'];
			}
		}
		// echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	

	}
	
	mysqli_close($con);
}elseif(isset($_GET['currentstock5']) and isset($_GET['account5'])){
	//internal report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$account = $_GET['account5'];

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

	if($result = mysqli_query($con,"
	SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
		SELECT CTOUnderlying,CTOPosition,isJPM, customer.Account as account ,CTOVolumeCurrent * serie.SMultiplier AS Volume FROM customertransactionopen 
		INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID
		INNER JOIN customer ON customertransactionopen.CustomerID = customer.CustomerID
		WHERE CTOVolumeCurrent > 0 and isJPM is null and CTOUnderlying like concat('%','$account','%'))
	AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){

			//header info for browser
			header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
			header("Content-type: application/x-msexcel; charset=UTF-8");
			header("Content-Disposition: attachment; filename=$filename.xls");
			//header("Pragma: no-cache");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			//table caption
			echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
			
			echo "<table border='1'><tr>";
			echo "<th>Stock</th>
					<th>Side</th>
					<th>Quantity</th>";
			echo "</tr></table>";
		
			while($row = mysqli_fetch_array($result)) {
				echo "<table border='1'><tr>";
				echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
				echo "<td>".number_format($row['Vol'])."</td>";
				echo "</tr></table>";
			}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['currentstock5'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>
				<th>JPM</th>
				<th>JPMprice</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
			echo "<td>".$row['CTOFutureName']."</td>";
			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";

			if ($row['isJPM'] == "Y"){
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".number_format($row['jpmfutureprice'], 2, '.', '')."</td>";
			}else{
				echo "<td style='text-align:center;'>".$row['isJPM']."</td>";
				echo "<td>".$row['jpmfutureprice']."</td>";
			}

			echo "</tr>";
			$totalValue += $row['CTOValue'];
		}
		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}
	}
	
	mysqli_close($con);
}elseif(isset($_GET['currentstock6']) and isset($_GET['account6'])){
	//internal report
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$account = $_GET['account6'];

	$filename = "Current Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

	if($result = mysqli_query($con,"
	SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol FROM(
		SELECT CTOUnderlying,CTOPosition,isJPM, customer.Account as account ,CTOVolumeCurrent * serie.SMultiplier AS Volume FROM customertransactionopen 
		INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID
		INNER JOIN customer ON customertransactionopen.CustomerID = customer.CustomerID
		WHERE CTOVolumeCurrent > 0 and isJPM is null and CTOUnderlying like concat('%','$account','%'))
	AS  CTO group by CTOUnderlying,CTOPosition ORDER by CTOUnderlying;")){

			//header info for browser
			header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
			header("Content-type: application/x-msexcel; charset=UTF-8");
			header("Content-Disposition: attachment; filename=$filename.xls");
			//header("Pragma: no-cache");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			//table caption
			echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
			
			echo "<table border='1'><tr>";
			echo "<th>Stock</th>
					<th>Side</th>
					<th>Quantity</th>";
			echo "</tr></table>";
		
			while($row = mysqli_fetch_array($result)) {
				echo "<table border='1'><tr>";
				echo "<td style='text-align:center;'>".$row['CTOUnderlying']."</td>";
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";
				echo "<td>".number_format($row['Vol'])."</td>";
				echo "</tr></table>";
			}
	}

	echo "<br><br>";
	
	//table caption
	echo "<table><tr><th  style='text-align:right;'>Outstanding </th><th  style='text-align:left;'> Client Position</th></tr></table>";
	
	$sql = $_GET['currentstock6'];
	if($result = mysqli_query($con,$sql)){
	
		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>";
		echo "</tr></table>";

		while($row = mysqli_fetch_array($result)) {
			if($row['isJPM'] == ''){
				echo "<table border='1'><tr>";
				$Newformat = strtotime($row['CTOTranDate']);
				$myFormatForView = date("d/m/Y", $Newformat);
				echo "<td>".$myFormatForView."</td>";
				echo "<td>".$row['Account']."</td>";
				echo "<td style='text-align:center;'>".$row['CTOPosition']."</td>";		
				echo "<td>".$row['CTOFutureName']."</td>";
				echo "<td>".$row['CTOFuturePrice']."</td>";
				echo "<td>".$row['CTOVolumeCurrent']."</td>";
				echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
				echo "</tr>";
				$totalValue += $row['CTOValue'];
			}
		}
		if ($totalValue > 0){
			echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
		}else{
			echo "</table>";
		}	
	}

	mysqli_close($con);
}elseif(isset($_GET['open'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	
	$filename = "Open Postion[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = $_GET['open'];
	if($result = mysqli_query($con,$sql)){
		
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		echo "<table border='1'><tr>";
		echo "<th>Transaction Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Spot</th>
				<th>Up-Front Interest</th>
				<th>Open Price</th>
				<th>Start Volume</th>
				<th>Current Volume</th>
				<th>Value</th>";
		echo "</tr></table>";
		
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td>".$row['CTOPosition']."</td>";
			echo "<td>".$row['CTOFutureName']."</td>";
			// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			echo "<td>".$row['CTOSpot']."</td>";
			echo "<td>".$row['CTOUpfrontInterest']."</td>";
			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeStart']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
			echo "</tr>";
			$totalValue += $row['CTOValue'];
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";
	}
	
	mysqli_close($con);
}elseif(isset($_GET['close'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Close Position[".date("d/m/Y")."]";
	$totalValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = $_GET['close'];
	if($result = mysqli_query($con,$sql)){

		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

		echo "<table border='1'><tr>";
		echo "<th>Close Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Spot</th>
				<th>Net Interest</th>
				<th>Close Price</th>
				<th>Volume</th>
				<th>Value</th>";
		echo "</tr></table>";

		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTCTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			echo "<td>".$row['CTCPosition']."</td>";
			echo "<td>".$row['CTCFutureName']."</td>";
			// echo "<td>".$row['CTCFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			echo "<td>".$row['CTCSpot']."</td>";
			echo "<td>".$row['CTCNetInterest']."</td>";
			echo "<td>".$row['CTCFuturePrice']."</td>";
			echo "<td>".$row['CTCVolume']."</td>";
			echo "<td>".number_format($row['CTCValue'],2,'.',',')."</td>";
			echo "</tr>";
			$totalValue += $row['CTCValue'];
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue,2,'.',',')."</td></tr></table>";

	}

	mysqli_close($con);
}elseif(isset($_GET['company'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Company Transaction [".date("d/m/Y")."]";
	$totalValue = 0;
	$totalCash = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = $_GET['company'];
	if($result = mysqli_query($con,$sql)){

		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

		echo "<table><tr><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Company Transaction</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Transaction Date</th>
				<th>Position</th>
				<th>Name</th>
				<th>Volume</th>
				<th>Price</th>
				<th>Value</th>
				<th>Cash</th>";
		echo "</tr></table>";

		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['COTDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['COTPosition']."</td>";

			echo "<td>".$row['COTUnderlying']."</td>";
			// echo "<td>".$row['COTUnderlying']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td>".$row['COTVolume']."</td>";
			echo "<td>".$row['COTCost']."</td>";
			echo "<td>".number_format($row['COTValue'],2,'.',',')."</td>";
			echo "<td>".number_format($row['COTCash'],2,'.',',')."</td>";
			echo "</tr>";
			$totalValue += $row['COTValue'];
			$totalCash += $row['COTCash'];
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalCash,2,'.',',')."</td></tr></table>";
	}

	mysqli_close($con);
}elseif(isset($_GET['fair'])){
	//update for outstanding unpaid cash at the end of table
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	
	$filename = "Company Position Summary[".date("d/m/Y")."]";
	$totalValue = 0;
	$totalCash = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}


	$unpaidpart = "";
	$totalunpaid = 0;
	$sql = "SELECT UNstock,UnCurrentValue FROM `unpaid` ORDER BY UNstock";
	if($result = mysqli_query($con,$sql)){
		while($row = mysqli_fetch_array($result)) {
			$unpaidpart .= "<tr><td></td><td>Unpaid</td><td>".$row['UNstock']."</td><td></td><td></td><td>".number_format($row['UnCurrentValue'],2,'.',',')."</td><td>".number_format($row['UnCurrentValue'],2,'.',',')."</td></tr>";
			$totalunpaid += ($row['UnCurrentValue']);
		}
	}

	$dividendpart = "";
	$totaldividend = 0;
	$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
	if($result = mysqli_query($con,$sql)){
		while($row = mysqli_fetch_array($result)) {
			$dividendpart .= "<tr><td></td><td>Dividend</td><td>".$row['DStock']."</td><td>".$row['DVolume']."</td><td>".$row['DDividend']."</td><td>".number_format($row['DDividend']*$row['DVolume'],2,'.',',')."</td><td>".number_format($row['DDividend']*$row['DVolume'],2,'.',',')."</td></tr>";
			$totaldividend += ($row['DDividend']*$row['DVolume']);
		}
	}
	
	$sql = "SELECT COT.CompanyTransactionID,COT.COTDate,COT.COTPosition,COT.COTUnderlying,COT.COTVolume,COT.COTCost,COT.COTValue,COT.COTCash,S.SName,C.Account FROM companytransaction AS COT INNER JOIN customer AS C ON C.CustomerID = COT.CustomerID INNER JOIN serie AS S ON COT.SerieID = S.SerieID";
	if($result = mysqli_query($con,$sql)){
	
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
	
		echo "<table border='1'><tr>";
		echo "<th>Transaction Date</th>
				<th>Position</th>
				<th>Name</th>
				<th>Volume</th>
				<th>Price</th>
				<th>Value</th>
				<th>Cash</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['COTDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['COTPosition']."</td>";
			echo "<td>".$row['COTUnderlying']."</td>";
			echo "<td>".$row['COTVolume']."</td>";
			echo "<td>".$row['COTCost']."</td>";
			echo "<td>".number_format($row['COTValue'],2,'.',',')."</td>";
			echo "<td>".number_format($row['COTCash'],2,'.',',')."</td>";
			echo "</tr>";
			$totalValue += $row['COTValue'];
			$totalCash += $row['COTCash'];
		}
		echo $dividendpart;
		echo $unpaidpart;
		echo "<tr><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalCash+$totaldividend+$totalunpaid,2,'.',',')."</td></tr>";
		echo "</table>";
	}

	mysqli_close($con);
}elseif(isset($_GET['stock'])){
	//Get Current Price From Aspen on Excel
	error_reporting(E_ALL ^ E_NOTICE);
	require_once 'excel/excel_reader2.php';
	$data = new Spreadsheet_Excel_Reader("excel/example.xls");
	
	$spot = [];
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
	
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$filename = "Unrealized Profit (Stock)[".date("d/m/Y")."]";
	$totalValue = 0;
	$currentValue = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	if($result = mysqli_query($con,"SELECT CTOUnderlying,CTOPosition,isJPM,jpmfutureprice,SUM(CTOVolumeCurrent) AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 GROUP BY CTOUnderlying,CTOPosition")){
	
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		
		//table caption
		echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Port Position</th></tr></table>";
		
		echo "<table border='1'><tr>";
		echo "<th>Stock</th>
				<th>Side</th>
				<th>Quantity</th>
				<th>Price</th>
				<th>Value</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			if($row['isJPM'] == ""){

				echo "<table border='1'><tr>";
					
				echo "<td>".$row['CTOUnderlying']."</td>";
				// echo "<td>".$row['CTOUnderlying']."&#8203"."</td>";
				// add noneprintable character to convert all name to string
				
				echo "<td>".$row['CTOPosition']."</td>";
				echo "<td>".number_format($row['Volume']*$row['SMultiplier'])."</td>";
				$currentValue = 0;
				if($row['CTOPosition'] == "Short"){
					echo "<td>".$spot[$row['CTOUnderlying']]['ASK']."</td>";
					$currentValue = $spot[$row['CTOUnderlying']]['ASK']*$row['Volume']*$row['SMultiplier'];
					$currentValue = $currentValue*-1;
				}
				else{
					echo "<td>".$spot[$row['CTOUnderlying']]['BID']."</td>";
					$currentValue = $spot[$row['CTOUnderlying']]['BID']*$row['Volume']*$row['SMultiplier'];
				}
				echo "<td>".number_format($currentValue)."</td>";
				$totalValue += $currentValue;
				echo "</tr>";
			
			// }else{

			// 	echo "<table border='1'><tr>";
			// 	echo "<td>".$row['CTOUnderlying']."</td>";
			// 	echo "<td>".$row['CTOPosition']."</td>";
			// 	echo "<td>".number_format($row['Volume']*$row['SMultiplier'])."</td>";
			// 	$currentValue = 0;
			// 	if($row['CTOPosition'] == "Short"){
			// 		echo "<td>".$row['jpmfutureprice']."</td>";
			// 		$currentValue = $row['jpmfutureprice']*$row['Volume']*$row['SMultiplier'];
			// 		$currentValue = $currentValue*-1;
			// 	}
			// 	else{
			// 		echo "<td>".$row['jpmfutureprice']."</td>";
			// 		$currentValue = $row['jpmfutureprice']*$row['Volume']*$row['SMultiplier'];
			// 	}
			// 	echo "<td>".number_format($currentValue)."</td>";
			// 	$totalValue += $currentValue;
			// 	echo "</tr>";
			}	
		}
		echo "<tr><td></td><td></td><td></td><th>Total</th><td>".number_format($totalValue)."</td></tr></table>";
	}

	mysqli_close($con);
}elseif(isset($_GET['futures'])){
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

	$filename = "Unrealized Profit (Futures)[".date("d/m/Y")."]";
	$totalValue = 0;
	$totalCurrent = 0;
	$today = new DateTime();
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
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
	if($result = mysqli_query($con,"SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOTotalInterest,CTO.CTODiscount,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 ORDER BY CTO.CTOTranDate")){

		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

		echo "<table border='1'><tr>";
		echo "<th>Date</th>
				<th>Account</th>
				<th>Position</th>
				<th>Future</th>
				<th>Open Price</th>
				<th>Current Volume</th>
				<th>Value</th>
				<th>Futures Price</th>
				<th>Holding Period</th>
				<th>Open Spot</th>
				<th>Spot</th>
				<th>Upfront int</th>
				<th>Discount</th>
				<th>Dividend</th>
				<th>Fair</th>
				<th>Profit/Loss</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			$tranDate = new DateTime($row['CTOTranDate']);
			echo "<table border='1'><tr>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			echo "<td>".$row['Account']."</td>";
			if($row['CTOPosition'] == "Long")
				echo "<td>Short</td>";
			else 
				echo "<td>Long</td>";

			echo "<td>".$row['CTOFutureName']."</td>";
			// echo "<td>".$row['CTOFutureName']."&#8203"."</td>";
			// add noneprintable character to convert all name to string
			
			echo "<td>".$row['CTOFuturePrice']."</td>";
			echo "<td>".$row['CTOVolumeCurrent']."</td>";
			echo "<td>".number_format($row['CTOValue'],2,'.',',')."</td>";
			echo "<td>".$futures[$row['CTOFutureName']]."</td>";
			$holdingperiod = 0;
			if($tranDate->diff($today)->format("%a") < $row['CTOMinimumDay'])
				$holdingperiod = $row['CTOMinimumDay'];
			else 
				$holdingperiod = $tranDate->diff($today)->format("%a");
			echo "<td>".$holdingperiod."</td>";
			echo "<td>".$row['CTOSpot']."</td>";
			$currentspot = 0;
			if($row['CTOPosition'] == "Long"){
				$currentspot = $spot[$row['CTOUnderlying']]['BID'];
			}
			elseif($row['CTOPosition'] == "Short"){
				$currentspot = $spot[$row['CTOUnderlying']]['ASK'];
			}
			echo "<td>".$currentspot."</td>";
			echo "<td>".$row['CTOUpfrontInterest']."</td>";
			echo "<td>".$row['CTODiscount']."</td>";
			$dividendpayback = 0;
			if (array_key_exists($row['CTOUnderlying'], $dividend)) {
				if($dividend[$row['CTOUnderlying']]['XD']>$row['CTOTranDate']){
					$dividendpayback = $dividend[$row['CTOUnderlying']]['Dividend']*$dividend[$row['CTOUnderlying']]['PercentOut'];
				}
			}
			echo "<td>$dividendpayback</td>";
			$totalint = 0;
			if(($row['CTOSpot']*$row['CTOPercentInterest']/100*$holdingperiod/365) < $row['CTOMinimumInt'])
				$totalint = $row['CTOMinimumInt'];
			else 
				$totalint = ($row['CTOSpot']*$row['CTOPercentInterest']/100*$holdingperiod/365);
			$fairprice = 0;
			$current = 0;

			if($row['CTOPosition'] == "Long"){
				if($row['isJPM'] == "Y"){
					$fairprice = ($row['CTOUpfrontInterest'] - $totalint - $row['CTODiscount']);
					// $fairprice = ($row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
					$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
				}else{
					$fairprice = ($currentspot + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
					$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
				}
			}
			else{ 
				if($row['isJPM'] == "Y"){
					$fairprice = ($totalint - $row['CTOUpfrontInterest']  + $row['CTODiscount']);
					// $fairprice = ($totalint - $row['CTOUpfrontInterest'] + $dividendpayback + $row['CTODiscount']);
					$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
				}else{
					$fairprice = ($currentspot - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback + $row['CTODiscount']);
					$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
				}
			}
			
			echo "<td>$fairprice</td>";
			echo "<td>".number_format($current,2,'.',',')."</td>";
			echo "</tr>";
			$totalValue += $row['CTOValue'];
			$totalCurrent += $current;
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalCurrent,2,'.',',')."</td></tr></table>";
	}

	mysqli_close($con);
}elseif(isset($_GET['reward'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	
	$filename = "Reward [".date("d/m/Y")."]";
	$totalcompanyprofit = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con,"utf8");
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = $_GET['reward'];
	if($result = mysqli_query($con,$sql)){
	
		//header info for browser
		header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
		header("Content-type: application/x-msexcel; charset=UTF-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		//header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
	
		echo "<table border='1'><tr>";
		echo "	<th>MKT ID</th>
				<th>MKT Name</th>
				<th>Team</th>
				<th>Account</th>
				<th>Open Date</th>
				<th>Close Date</th>
				<th>Underlying</th>
				<th>Position</th>
				<th>Open Price</th>
				<th>Open Volume</th>
				<th>Company Profit</th>
				<th>IC Profit</th>";
		echo "</tr></table>";
	
		while($row = mysqli_fetch_array($result)) {
			echo "<table border='1'><tr>";
			echo "<td>".$row['MKTID']."</td>";
			echo "<td>".$row['MKTName']."</td>";
			echo "<td>".$row['Team']."</td>";
			echo "<td>".$row['Account']."</td>";
			$Newformat = strtotime($row['CTOTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			$Newformat = strtotime($row['CTCTranDate']);
			$myFormatForView = date("d/m/Y", $Newformat);
			echo "<td>".$myFormatForView."</td>";
			$companyprofit = 0;
			if ($row['CTOPosition'] == "Long")
				$companyprofit = (($row['CTCSpot']-$row['CTOSpot'])+($row['CTOFuturePrice']-$row['CTCFuturePrice'])+$row['CTCDividend'])*$row['CTCVolume']*$row['SMultiplier'];
			elseif ($row['CTOPosition'] == "Short")
				$companyprofit = (($row['CTOSpot']-$row['CTCSpot'])+($row['CTCFuturePrice']-$row['CTOFuturePrice']))*$row['CTCVolume']*$row['SMultiplier'];
			echo "<td>".$row['CTOUnderlying']."</td>";
			echo "<td>".$row['CTOPosition']."</td>";
			echo "<td style='text-align:right;'>".$row['CTOSpot']."</td>";
			echo "<td style='text-align:right;'>".$row['CTCVolume']."</td>";
			echo "<td style='text-align:right;'>".number_format($companyprofit,2,'.',',')."</td>";
			echo "<td style='text-align:right;'>".number_format(round($companyprofit/8,3),2,'.',',')."</td>";
			echo "</tr>";
			$totalcompanyprofit += $companyprofit;
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th>Total</th><td>".number_format($totalcompanyprofit,2,'.',',')."</td><td style='text-align:right;'>".number_format(($totalcompanyprofit/8),2,'.',',')."</td></tr></table>";
	}
	
	mysqli_close($con);
}elseif(isset($_GET['account'])){
	
	
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
	$filename = "Profit [".date("d/m/Y")."]";
	$accounting = [];
	$underlying = "";
	$sum = 0;
		
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
				//$cashindividend[$row['DStock']] = ($row['DDividend']*$row['DVolume']);
				$cashindividend[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
			}
		}
	}

	// Sum cash dividend for fair
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
	
	// Sum Open deal for fair
	if($result = mysqli_query($con,"SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOTotalInterest,CTO.CTODiscount,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 ORDER BY CTO.CTOTranDate")){

		
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

		
				// if($row['CTOPosition'] == "Long"){
				// 	$fairprice = ($spot[$row['CTOUnderlying']]['BID'] + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
				// 	$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
				// }
				// else{
				// 	$fairprice = ($spot[$row['CTOUnderlying']]['ASK'] - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback - $row['CTODiscount']);
				// 	$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
				// }
				// $fair[$row['CTOUnderlying']] += $current;


				if($row['CTOPosition'] == "Long"){
					if($row['isJPM'] == "Y"){
							$fairprice = ($row['CTOUpfrontInterest'] - $totalint - $row['CTODiscount']);
							// $fairprice = ($row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
						}else{
							$fairprice = ($spot[$row['CTOUnderlying']]['BID'] + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
						}
					}
					else{ 
						if($row['isJPM'] == "Y"){
							$fairprice = ($totalint - $row['CTOUpfrontInterest'] + $row['CTODiscount']);
							// $fairprice = ($totalint - $row['CTOUpfrontInterest'] + $dividendpayback + $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
						}else{
							$fairprice = ($spot[$row['CTOUnderlying']]['ASK'] - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback + $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
						}
					}
					$fair[$row['CTOUnderlying']] += $current;


			}
		}
	}

	// Fair by m2m of outstanding stock in portm that aren't JPM 
	if($result = mysqli_query($con,"SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol 
		FROM(	SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume 
			FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 and isJPM is null )AS  CTO group by CTOUnderlying,CTOPosition;")){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$currentValue = 0;
				if($row['CTOPosition'] == "Short"){
					$currentValue = $spot[$row['CTOUnderlying']]['ASK']*$row['Vol'];
					$currentValue = $currentValue*-1;
				}
				else{
					$currentValue = $spot[$row['CTOUnderlying']]['BID']*$row['Vol'];
				}
				$fair[$row['CTOUnderlying']] += $currentValue;
			}
		}
	}

	// Fair :add dividend 100% and already -90% at calculate fair price; above code 
	$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
	if($result = mysqli_query($con,$sql)){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$fair[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
			}
		}
	}

	// Fair :adjust fair according to unpaid amount by underlying
	$sql = "SELECT SUM(UNCurrentValue) AS money, UNstock FROM `unpaid` GROUP BY UNstock";
	if($result = mysqli_query($con,$sql)){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$fair[$row['UNstock']] += ($row[money]*-1);
			}
		}
	}


	// Fair : sum only done cash to fair
	// Normally this sum all cash from transaction will diff in case upfront client, but we already change fairprice above,
	// So Upfront is already deducted.					

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
	// Creating Excel file

	//header info for browser
	header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
	header("Content-type: application/x-msexcel; charset=UTF-8");
	header("Content-Disposition: attachment; filename=$filename.xls");
	//header("Pragma: no-cache");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	
	echo "<table><tr><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Profit / Loss</th></tr></table>";
	
	echo "<table border='1'><tr>";
	echo "		<th>Underlying</th>
				<th>Accounting</th>
				<th>Fair</th>";
	echo "</tr></table>";
	echo "<table border='1'>";
	///todo
	foreach($accounting as $ul => $profit) {
		if(number_format($profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'])!=0 || number_format($fair[$ul]-$lastyearprofit[$ul]['Fair']) != 0 ){
			if($ul !="PS"){
			echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account']) ."</td><td style='text-align:right;'>".number_format($fair[$ul]-$lastyearprofit[$ul]['Fair'])."</td></tr>";
			$sum += $profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'];
			$sumFair += $fair[$ul]-$lastyearprofit[$ul]['Fair'];
			}
				
		}
	}
	echo "<tr><th style='text-align:center;'>Total</th><td style='text-align:right;'>".number_format($sum)."</td><td style='text-align:right;'>".number_format($sumFair)."</td></tr>";
	echo "</table>";
	
	
	// Notional Parts	
	$notional = [];
	$totalNotional = 0;
    $LongNotional = 0;
    $ShortNotional = 0;
	$sql = "SELECT CTOUnderlying,CTOPosition,CTOSpot,CTOVolumeCurrent AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0";
	$result = mysqli_query($con, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		echo "<br><br>";
		//skip to line in excel file
		echo "<table><tr><th style='text-align:right;'>Outstanding </th><th style='text-align:left;'> Notional</th></tr></table>";
		echo "<table border='1'><tr><th>Underlying</th><th>Notional</th></tr>";
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
		echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($value) ."</td></tr>";
		$totalNotional += $value;
	}
	echo "<tr><th style='text-align:center;'>Total</th><td>" . number_format($totalNotional) ."</td></tr></table>";
	//Insert Data into Notional Table
	date_default_timezone_set("Asia/Bangkok");
	$today = new DateTime();
	$closetime = date("Y-m-d H:i:s",mktime(17, 0, 0, date("m"), date("d"), date("Y")));
	
	$lastDate;
	$lastNotional;
	$lastShortNotional;
	$lastLongNotional;
	$sql = "SELECT NDate,Notional,LONGNotional,SHORTNotional FROM notional ORDER BY NID DESC LIMIT 1";
	$result = mysqli_query($con, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$lastDate = new DateTime($row['NDate']);
			$lastNotional = $row['Notional'];
			$lastShortNotional = $row['SHORTNotional'];
			$lastLongNotional = $row['LONGNotional'];
		}
	}

	$loop = $lastDate->diff($today)->format("%a");
	if(($today->format("Y-m-d H:i:s") > $closetime)) {
		if($lastDate->diff($today)->format("%a")>1) {
			for($x = 1; $x <= $loop; $x++){
				if($x == $loop){
					$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('$closetime','$totalNotional','$LongNotional','$ShortNotional')";
						
					if (mysqli_query($con, $sql)) {
						
					} else {
						echo "Error: " . $sql . "<br>" . mysqli_error($con);
						return;
					}
				}
				else{
					date_add($lastDate, date_interval_create_from_date_string('1 days'));
					$lastDate->format("Y-m-d H:i:s");
					$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('".$lastDate->format("Y-m-d H:i:s")."','$lastNotional','$lastLongNotional','$lastShortNotional')";
					if (mysqli_query($con, $sql)) {
						
					} else {
						echo "Error: " . $sql . "<br>" . mysqli_error($con);
						return;
					}
				}
			}
		}
		elseif($lastDate->diff($today)->format("%a")>0) {
			$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('$closetime','$totalNotional','$LongNotional','$ShortNotional')";
	
			if (mysqli_query($con, $sql)) {
				
			} else {
				echo "Error: " . $sql . "<br>" . mysqli_error($con);
				return;
			}
		}
	}
	// skip 1 line
	echo "<br>";
	echo "<table><tr><th style='text-align:center;'>Performance </th></tr></table>";
	$y = date("Y");
	$oldYear = $y-1;
	$sql = "SELECT SUM(notional) AS notional,Count(*) AS Day FROM `notional` WHERE NDate >='$oldYear-12-30' order by NDate DESC" ;
	$result = mysqli_query($con, $sql);
	// $result = mysqli_query($con, $sql);
	$day = 0;
	// 	$leap_year = date('L');
		
	// 	$day_duration = 365;
	// 	if($leap_year == '1'){
	// 		$day_duration = 366;
	// 	}
	if (mysqli_num_rows($result) > 0) {
		echo "<table border='1'>";
		while($row = mysqli_fetch_assoc($result)) {
			echo "<tr><th>Average Notional</th><td>".number_format($row['notional']/$row['Day'])."</td></tr>";
			echo "<tr><th>Notional Yield</th><td>".$sumFair/($row['notional']/$row['Day'])*365/$row['Day']*100 ."%</td></tr>";
			$day = $row['Day'];
		}
	}
	
	$sql = "Select  (LONGNotional-SHORTNotional) AS netnotional,NDate as NDate  FROM notional WHERE NDate >= '$oldYear-12-30'" ;
	$result = mysqli_query($con, $sql);
	$netnotional = 0;
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			if($row['netnotional'] > 0){
				$netnotional+=$row['netnotional'];
			}
		}
	}

	echo "<tr><th>Cash Yield</th><td>".$sumFair/($netnotional/$day)*365/$day*100 ."%</td></tr>";
	
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
	
	
	echo "<tr><th style='text-align:center;'>Total Cash Used</th><td style='text-align:right;'>".number_format($LongNotional-$ShortNotional)."</td></tr>";
	mysqli_close($con);
}elseif(isset($_GET['account2'])){
	
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
	$filename = "Profit [".date("d/m/Y")."]";
	$accounting = [];
	$underlying = "";
	$sum = 0;
		
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
				//$cashindividend[$row['DStock']] = ($row['DDividend']*$row['DVolume']);
				$cashindividend[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
			}
		}
	}

	// Sum cash dividend for fair
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
	
	// Sum Open deal for fair
	if($result = mysqli_query($con,"SELECT CTO.CTOID,CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,CTO.CTOValue,CTO.CTOUpfrontInterest,CTO.CTOMinimumDay,CTO.CTOMinimumInt,CTO.CTOTotalInterest,CTO.CTODiscount,CTO.CTOPercentInterest,CTO.CTOPercentUpfront,CTO.CTOFuturePrice,S.SName,S.SMultiplier,C.Account,CTO.isJPM,CTO.jpmfutureprice FROM customertransactionopen AS CTO INNER JOIN customer AS C ON C.CustomerID = CTO.CustomerID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID WHERE CTOVolumeCurrent > 0 ORDER BY CTO.CTOTranDate")){

		
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

		
				// if($row['CTOPosition'] == "Long"){
				// 	$fairprice = ($spot[$row['CTOUnderlying']]['BID'] + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
				// 	$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
				// }
				// else{
				// 	$fairprice = ($spot[$row['CTOUnderlying']]['ASK'] - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback - $row['CTODiscount']);
				// 	$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
				// }
				// $fair[$row['CTOUnderlying']] += $current;


				if($row['CTOPosition'] == "Long"){
					if($row['isJPM'] == "Y"){
							$fairprice = ($row['CTOUpfrontInterest'] - $totalint - $row['CTODiscount']);
							// $fairprice = ($row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
						}else{
							$fairprice = ($spot[$row['CTOUnderlying']]['BID'] + $row['CTOUpfrontInterest'] - $totalint + $dividendpayback - $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent'])*-1;
						}
					}
					else{ 
						if($row['isJPM'] == "Y"){
							$fairprice = ($totalint - $row['CTOUpfrontInterest'] + $row['CTODiscount']);
							// $fairprice = ($totalint - $row['CTOUpfrontInterest'] + $dividendpayback + $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
						}else{
							$fairprice = ($spot[$row['CTOUnderlying']]['ASK'] - $row['CTOUpfrontInterest'] + $totalint + $dividendpayback + $row['CTODiscount']);
							$current = ($fairprice*$row['SMultiplier']*$row['CTOVolumeCurrent']);
						}
					}
					$fair[$row['CTOUnderlying']] += $current;


			}
		}
	}

	// Fair by m2m of outstanding stock in portm that aren't JPM 
	if($result = mysqli_query($con,"SELECT   CTOUnderlying ,CTOPosition,SUM(Volume) as Vol 
		FROM(	SELECT CTOUnderlying,CTOPosition,isJPM,CTOVolumeCurrent * serie.SMultiplier AS Volume 
			FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0 and isJPM is null )AS  CTO group by CTOUnderlying,CTOPosition;")){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$currentValue = 0;
				if($row['CTOPosition'] == "Short"){
					$currentValue = $spot[$row['CTOUnderlying']]['ASK']*$row['Vol'];
					$currentValue = $currentValue*-1;
				}
				else{
					$currentValue = $spot[$row['CTOUnderlying']]['BID']*$row['Vol'];
				}
				$fair[$row['CTOUnderlying']] += $currentValue;
			}
		}
	}

	// Fair :add dividend 100% and already -90% at calculate fair price; above code 
	$sql = "SELECT DDividend,DVolume,DStock FROM `dividend` ORDER BY DStock";
	if($result = mysqli_query($con,$sql)){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$fair[$row['DStock']] += ($row['DDividend']*$row['DVolume']);
			}
		}
	}

	// Fair :adjust fair according to unpaid amount by underlying
	$sql = "SELECT SUM(UNCurrentValue) AS money, UNstock FROM `unpaid` GROUP BY UNstock";
	if($result = mysqli_query($con,$sql)){
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_array($result)) {
				$fair[$row['UNstock']] += ($row[money]*-1);
			}
		}
	}
	
	// Fair : sum only done cash to fair
	// Normally this sum all cash from transaction will diff in case upfront client, but we already change fairprice above,
	// So Upfront is already deducted.					

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
	// Creating Excel file

	//header info for browser
	header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
	header("Content-type: application/x-msexcel; charset=UTF-8");
	header("Content-Disposition: attachment; filename=$filename.xls");
	//header("Pragma: no-cache");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	
	echo "<table><tr><th></th><th></th><th></th><th>As of : ".date("d/m/Y")."</th></tr><tr><th>Profit / Loss</th></tr></table>";
	
	echo "<table border='1'><tr>";
	echo "		<th>Underlying</th>
				<th>Accounting</th>
				<th>Fair</th>";
	echo "</tr></table>";
	echo "<table border='1'>";
	///todo
	foreach($accounting as $ul => $profit) {
		if(number_format($profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'])!=0 || number_format($fair[$ul]-$lastyearprofit[$ul]['Fair']) != 0 ){
			if($ul !="PS"){
			echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account']) ."</td><td style='text-align:right;'>".number_format($fair[$ul]-$lastyearprofit[$ul]['Fair'])."</td></tr>";
			$sum += $profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'];
			$sumFair += $fair[$ul]-$lastyearprofit[$ul]['Fair'];
			}
				
		}
	}
	echo "<tr><th style='text-align:center;'>Total</th><td style='text-align:right;'>".number_format($sum)."</td><td style='text-align:right;'>".number_format($sumFair)."</td></tr>";
	echo "</table>";
	
	
	// Notional Parts	
	//0:total=old version  ;  2:internal ;  3:external 
	$notional = [];
	$notional2 = [];
	$notional3 = [];
	$totalNotional = 0;
	$totalNotional2 = 0;
	$totalNotional3 = 0;
	$LongNotional = 0;
	$LongNotional2 = 0;
	$LongNotional3 = 0;
	$ShortNotional = 0;
	$ShortNotional2 = 0;
	$ShortNotional3 = 0;
	$accountingTotal = 0;

	$sql = "SELECT CTOUnderlying,CTOPosition,CTOSpot,isJPM,CTOVolumeCurrent AS Volume,serie.SMultiplier FROM customertransactionopen INNER JOIN serie ON customertransactionopen.SerieID = serie.SerieID WHERE CTOVolumeCurrent > 0";
	$result = mysqli_query($con, $sql);
	if (mysqli_num_rows($result) > 0) {
		echo "<br><br>";
		//skip to line in excel file
		echo "<table><tr><th style='text-align:right;'>Outstanding </th><th style='text-align:left;'> Notional</th></tr></table>";
		echo "<table border='1'><tr><th>Underlying</th><th>Notional</th></tr>";
		while($row = mysqli_fetch_assoc($result)) {
			if($row['isJPM'] != "Y"){

				if(array_key_exists($row['CTOUnderlying'],$notional)){
					$notional[$row['CTOUnderlying']] += $row['CTOSpot']*$row['Volume']*$row['SMultiplier']; 
				}else{
					$notional[$row['CTOUnderlying']] = $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
				}
				if($row['CTOPosition'] == "Long"){
					$LongNotional += $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
				}elseif($row['CTOPosition'] == "Short"){
					$ShortNotional += $row['CTOSpot']*$row['Volume']*$row['SMultiplier'];
				}
			}
		}
	}
	foreach($notional as $ul => $value) {
		echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($value) ."</td></tr>";
		$totalNotional += $value;
	}
	echo "<tr><th style='text-align:center;'>Total</th><td>" . number_format($totalNotional) ."</td></tr></table>";
	
	//Insert Data into Notional Table
	date_default_timezone_set("Asia/Bangkok");
	$today = new DateTime();
	$closetime = date("Y-m-d H:i:s",mktime(17, 0, 0, date("m"), date("d"), date("Y")));
	
	$lastDate;
	$lastNotional;
	$lastShortNotional;
	$lastLongNotional;
	$sql = "SELECT NDate,Notional,LONGNotional,SHORTNotional FROM notional ORDER BY NID DESC LIMIT 1";
	$result = mysqli_query($con, $sql);
	
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$lastDate = new DateTime($row['NDate']);
			$lastNotional = $row['Notional'];
			$lastShortNotional = $row['SHORTNotional'];
			$lastLongNotional = $row['LONGNotional'];
		}
	}

	$loop = $lastDate->diff($today)->format("%a");
	if(($today->format("Y-m-d H:i:s") > $closetime)) {
		if($lastDate->diff($today)->format("%a")>1) {
			for($x = 1; $x <= $loop; $x++){
				if($x == $loop){
					$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('$closetime','$totalNotional','$LongNotional','$ShortNotional')";
						
					if (mysqli_query($con, $sql)) {
						
					} else {
						echo "Error: " . $sql . "<br>" . mysqli_error($con);
						return;
					}
				}
				else{
					date_add($lastDate, date_interval_create_from_date_string('1 days'));
					$lastDate->format("Y-m-d H:i:s");
					$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('".$lastDate->format("Y-m-d H:i:s")."','$lastNotional','$lastLongNotional','$lastShortNotional')";
					if (mysqli_query($con, $sql)) {
						
					} else {
						echo "Error: " . $sql . "<br>" . mysqli_error($con);
						return;
					}
				}
			}
		}
		elseif($lastDate->diff($today)->format("%a")>0) {
			$sql = "INSERT INTO notional (NDate,Notional,LONGNotional,SHORTNotional) VALUES ('$closetime','$totalNotional','$LongNotional','$ShortNotional')";
	
			if (mysqli_query($con, $sql)) {
				
			} else {
				echo "Error: " . $sql . "<br>" . mysqli_error($con);
				return;
			}
		}
	}
	// skip 1 line
	echo "<br>";
	echo "<table><tr><th style='text-align:center;'>Performance </th></tr></table>";
	$y = date("Y");
	$oldYear = $y-1;
	$sql = "SELECT SUM(notional) AS notional,Count(*) AS Day FROM `notional` WHERE NDate >='$oldYear-12-30' order by NDate DESC" ;
	$result = mysqli_query($con, $sql);
	// $result = mysqli_query($con, $sql);
	$day = 0;
	// 	$leap_year = date('L');
		
	// 	$day_duration = 365;
	// 	if($leap_year == '1'){
	// 		$day_duration = 366;
	// 	}
	if (mysqli_num_rows($result) > 0) {
		echo "<table border='1'>";
		while($row = mysqli_fetch_assoc($result)) {
			echo "<tr><th>Average Notional</th><td>".number_format($row['notional']/$row['Day'])."</td></tr>";
			echo "<tr><th>Notional Yield</th><td>".$sumFair/($row['notional']/$row['Day'])*365/$row['Day']*100 ."%</td></tr>";
			$day = $row['Day'];
		}
	}
	
	$sql = "Select  (LONGNotional-SHORTNotional) AS netnotional,NDate as NDate  FROM notional WHERE NDate >= '$oldYear-12-30'" ;
	$result = mysqli_query($con, $sql);
	$netnotional = 0;
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			if($row['netnotional'] > 0){
				$netnotional+=$row['netnotional'];
			}
		}
	}

	echo "<tr><th>Cash Yield</th><td>".$sumFair/($netnotional/$day)*365/$day*100 ."%</td></tr>";
	
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
	
	
	echo "<tr><th style='text-align:center;'>Total Cash Used</th><td style='text-align:right;'>".number_format($LongNotional-$ShortNotional)."</td></tr>";
	mysqli_close($con);
}


?>