<?php
	if(isset($_POST['tranDate'])){
		$tranDate = $_POST['tranDate'];
		$position = $_POST['position'];
		$underlying = $_POST['underlying'];
		$volume = $_POST['volume'];
		$spot = $_POST['spot'];
		$value = $_POST['value'];
		$upfront = $_POST['upfront'];
		$totalinterest = $_POST['totalint'];
		$mindate = $_POST['mindate'];
		$minbaht = $_POST['minbaht'];
		$acccount = $_POST['acccount'];
		$serie = $_POST['serie'];
		$percentInterest = $_POST['percentInterest'];
		$multiplier = $_POST['multiplier'];
		$percentUpfront = $_POST['percentUpfront'];
		$futurePrice = $_POST['futurePrice'];
		$discount = $_POST['discount'];
		$serieName = substr($serie,0,4);
		$isJPM = $_POST['isJPM'];
		$jpmfutureprice = $_POST['jpmfutureprice'];

		//connect to database
		$servername = "172.16.24.235";
		$username = "mycustom";
		$password = "mypass";
		$dbname = "blocktradetest";
		$con = mysqli_connect($servername, $username, $password, $dbname);
		// Check connection
		if (!$con) {
			die("Connection failed: " . mysqli_connect_error());
		}
		//Check Account
		$sql = "SELECT COUNT(*) AS TOTAL FROM `customer` WHERE Account = '$acccount'";
		$result = mysqli_query($con,$sql);
		if(mysqli_num_rows($result)>0){
			while($row = mysqli_fetch_array($result)) {
				$check_select = $row['TOTAL'];
			}
		}
		//Insert AccountID to Database if not exist
		if($check_select == 0){
			$sql = "INSERT INTO customer (Account) VALUES ('$acccount')";
			if (mysqli_query($con, $sql)) {
				echo "New Account record stored successfully\n";
			} else {
				echo "Error: " . $sql . "\n" . mysqli_error($con)."\n";
			}
		}
		if($multiplier == 1000){
			//Insert Customer's Transaction

			if($isJPM == "Y"){
				//Insert Customer's Transaction + JPM
				$sql = "INSERT INTO customertransactionopen (CTOTranDate,CTOPosition,CTOUnderlying,CTOFutureName,CTOVolumeStart,CTOVolumeCurrent,CTOSpot,CTOValue,CTOUpfrontInterest,CTODiscount,CTOTotalInterest,CTOMinimumDay,CTOMinimumInt,CTOPercentInterest,CTOPercentUpfront,CTOFuturePrice,SerieID,CustomerID,isJPM,jpmfutureprice) SELECT '$tranDate','$position','$underlying','".$underlying.$serieName."','$volume','$volume','$spot','$value','$upfront','$discount','$totalinterest','$mindate','$minbaht','$percentInterest','$percentUpfront','$futurePrice',S.SerieID,C.CustomerID,'$isJPM','$jpmfutureprice' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";
			}else{
				//Insert Customer's Transaction 
				$sql = "INSERT INTO customertransactionopen (CTOTranDate,CTOPosition,CTOUnderlying,CTOFutureName,CTOVolumeStart,CTOVolumeCurrent,CTOSpot,CTOValue,CTOUpfrontInterest,CTODiscount,CTOTotalInterest,CTOMinimumDay,CTOMinimumInt,CTOPercentInterest,CTOPercentUpfront,CTOFuturePrice,SerieID,CustomerID) SELECT '$tranDate','$position','$underlying','".$underlying.$serieName."','$volume','$volume','$spot','$value','$upfront','$discount','$totalinterest','$mindate','$minbaht','$percentInterest','$percentUpfront','$futurePrice',S.SerieID,C.CustomerID FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";	
			}

			if (mysqli_query($con, $sql)) {
					$last_id = mysqli_insert_id($con);
					echo "customertransactionopen record stored successfully";
				} else {
					echo "Error customertransactionopen : " . $sql . "\n" . mysqli_error($con);
					return;
				}

			//Insert Company's Transaction
			if($isJPM == "Y"){
				if ($position == "Long"){
					$cash = $value;
					$companyFuture = "Short";
					$jpmvolume = $volume;
					$volume = -1*$volume;
					$jpmvalue = $jpmfutureprice*$jpmvolume*$multiplier;
					$jpmcash = 	-1*$jpmvalue;
				}
				elseif ($position == "Short"){
					$cash = -1*$value;
					$companyFuture = "Long";
					$jpmvolume = -1*$volume;
					$jpmvalue = $jpmfutureprice * $volume * $multiplier;
					$jpmcash = 	$jpmvalue;
				}
				// $futurename = $underlying.$serieName;
				//Future of JPM
				$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$position','$jpmvolume','$jpmfutureprice','$jpmvalue','$jpmcash',S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";
				
				if (mysqli_query($con, $sql)) {
					echo "\nCompany Hedging record stored successfully";
				} else {
					echo "\nError: " . $sql . "\n" . mysqli_error($con);
					return;
				}
				//Future
				$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value', '$cash' , S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";
				
				if (mysqli_query($con, $sql)) {
					echo "\nCompany Hedging record stored successfully";
				} else {
					echo "\nError: " . $sql . "\n" . mysqli_error($con);
					return;
				}
			}else{
				if ($position == "Long"){
					$cash = $value;
					$companyFuture = "Short";
					$spotvalue = $spot * $volume * $multiplier;
					$spotcash = $spotvalue*-1;
					$underlyingVolume = $volume * $multiplier;
					$volume = -1 * $volume;
				}
				elseif ($position == "Short"){
					$cash = -1*$value;
					$companyFuture = "Long";
					$spotvalue = $spot * $volume * $multiplier;
					$spotcash = $spotvalue;
					$underlyingVolume = -1 * $volume * $multiplier;
				}
				// $futurename = $underlying.$serieName;
				//underlying
				$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','$underlying','$position','$underlyingVolume','$spot','$spotvalue', '$spotcash' , S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = 'UI' AND C.Account = '$acccount'";
				
				if (mysqli_query($con, $sql)) {
					echo "\nCompany record stored successfully";
				} else {
					echo "\nError: " . $sql . "\n" . mysqli_error($con);
					return;
				}
				//Future
				$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value', '$cash' , S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";
				
				if (mysqli_query($con, $sql)) {
					echo "\nCompany Hedging record stored successfully";
				} else {
					echo "\nError: " . $sql . "\n" . mysqli_error($con);
					return;
				}
			}
		}
		elseif($multiplier != 1000){
			//Get SeriesID from serie
			$serieID = 0;
			$sql = "SELECT SerieID FROM `serie` WHERE SName = '$serieName' AND Underlying = '$underlying'";
	//		$sql = "SELECT SerieID FROM `serie` WHERE SName = '$serieName' ";
			if (strpos($serieName, '100') !== false) {
				$sql = "SELECT SerieID FROM `serie` WHERE SName = '$serieName' ";
			}
			$result = mysqli_query($con,$sql);
			if(mysqli_num_rows($result)>0){
				while($row = mysqli_fetch_array($result)) {
					$serieID = $row['SerieID'];
				}
			}
			//Insert Customer's Transaction
			if($isJPM == "Y"){
				//Insert Customer's Transaction + JPM
				$sql = "INSERT INTO customertransactionopen (CTOTranDate,CTOPosition,CTOUnderlying,CTOFutureName,CTOVolumeStart,CTOVolumeCurrent,CTOSpot,CTOValue,CTOUpfrontInterest,CTODiscount,CTOTotalInterest,CTOMinimumDay,CTOMinimumInt,CTOPercentInterest,CTOPercentUpfront,CTOFuturePrice,SerieID,CustomerID,isJPM,jpmfutureprice) SELECT '$tranDate','$position','$underlying','".$underlying.$serieName."','$volume','$volume','$spot','$value','$upfront','$discount','$totalinterest','$mindate','$minbaht','$percentInterest','$percentUpfront','$futurePrice','$serieID',C.CustomerID,'$isJPM','$jpmfutureprice' FROM customer AS C WHERE C.Account = '$acccount'";
			}else{
				//Insert Customer's Transaction
				$sql = "INSERT INTO customertransactionopen (CTOTranDate,CTOPosition,CTOUnderlying,CTOFutureName,CTOVolumeStart,CTOVolumeCurrent,CTOSpot,CTOValue,CTOUpfrontInterest,CTODiscount,CTOTotalInterest,CTOMinimumDay,CTOMinimumInt,CTOPercentInterest,CTOPercentUpfront,CTOFuturePrice,SerieID,CustomerID) SELECT '$tranDate','$position','$underlying','".$underlying.$serieName."','$volume','$volume','$spot','$value','$upfront','$discount','$totalinterest','$mindate','$minbaht','$percentInterest','$percentUpfront','$futurePrice','$serieID',C.CustomerID FROM customer AS C WHERE C.Account = '$acccount'";
			}	
			if (mysqli_query($con, $sql)) {
				$last_id = mysqli_insert_id($con);
				echo "customertransactionopen record stored successfully";
			} else {
				echo "Error customertransactionopen : " . $sql . "\n" . mysqli_error($con);
				return;
			}

		//Insert Company's Transaction
		if($isJPM == "Y"){	
			if ($position == "Long"){
					$cash = $value;
					$companyFuture = "Short";
					$jpmvolume = $volume;
					$volume = -1*$volume;
					$jpmvalue = $jpmfutureprice*$jpmvolume*$multiplier;
					$jpmcash = 	-1*$jpmvalue;
				}
				elseif ($position == "Short"){
					$cash = -1*$value;
					$companyFuture = "Long";
					$jpmvolume = -1*$volume;
					$jpmvalue = $jpmfutureprice * $volume * $multiplier;
					$jpmcash = 	$jpmvalue;
				}
			// $futurename = $underlying.$serieName;
			//Future of JPM
				$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$position','$jpmvolume','$jpmfutureprice','$jpmvalue','$jpmcash', S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$acccount'";
				
				if (mysqli_query($con, $sql)) {
					echo "\nCompany Hedging record stored successfully";
				} else {
					echo "\nError: " . $sql . "\n" . mysqli_error($con);
					return;
				}
			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value', '$cash' , '$serieID',C.CustomerID,'".$last_id."' FROM customer AS C WHERE C.Account = '$acccount'";
				
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging record stored successfully";
			} else {
				echo "\nError: " . $sql . "\n" . mysqli_error($con);
				return;
			}
		}else{			
			if ($position == "Long"){
				$cash = $value;
				$companyFuture = "Short";
				$spotvalue = $spot * $volume * $multiplier;
				$spotcash = $spotvalue*-1;
				$underlyingVolume = $volume * $multiplier;
				$volume = -1 * $volume;
			}
			elseif ($position == "Short"){
				$cash = -1*$value;
				$companyFuture = "Long";
				$spotvalue = $spot * $volume * $multiplier;
				$spotcash = $spotvalue;
				$underlyingVolume = -1 * $volume * $multiplier;
			}
			// $futurename = $underlying.$serieName;
			//underlying
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','$underlying','$position','$underlyingVolume','$spot','$spotvalue', '$spotcash' , S.SerieID,C.CustomerID,'".$last_id."' FROM serie AS S,customer AS C WHERE S.SName = 'UI' AND C.Account = '$acccount'";
				
			if (mysqli_query($con, $sql)) {
				echo "\nCompany record stored successfully<br>";
			} else {
				echo "\nError: " . $sql . "\n" . mysqli_error($con);
				return;
			}
			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTOID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value', '$cash' , '$serieID',C.CustomerID,'".$last_id."' FROM customer AS C WHERE C.Account = '$acccount'";
				
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging record stored successfully";
			} else {
				echo "\nError: " . $sql . "\n" . mysqli_error($con);
				return;
			}
		}

		}
		mysqli_close($con);
	}
	
	// Chanage Multiplier
	elseif(isset($_POST['multi'])){
		$serie = $_POST['multi'];
		if(strpos($serie, " ") === false )
			$sql = "SELECT SMultiplier FROM `serie` WHERE SName = '$serie' AND Underlying = ''";
		else{
			$serie = substr($_POST['multi'],0,strpos($serie, " ")+1);
			$underlying = substr($_POST['multi'],strpos($serie, " ")+1,strlen($_POST['multi']));
			$sql = "SELECT SMultiplier FROM `serie` WHERE SName = '$serie' AND Underlying = '$underlying'";
		}
		//connect to database
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
		$result = mysqli_query($con, $sql);
			
		if (mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_assoc($result)) {
				echo $row['SMultiplier'];
			}
		}
		mysqli_close($con);
	}
?>