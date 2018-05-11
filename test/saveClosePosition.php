<?php
	$position = $_POST['position'];
	$tranDate = $_POST['tranDate'];
	$underlying = $_POST['underlying'];
	$futureName = $_POST['futureName'];
	$volume = $_POST['volume'];
	$spot = $_POST['spot'];
	$value = $_POST['value'];
	$totalInterest = $_POST['totalInterest'];
	$netInt = $_POST['netInterest'];
	$futurePrice = $_POST['futurePrice'];
	$jpmfutureclose = $_POST['jpmfutureclose'];
	$flag = $_POST['flag'];
	$account = $_POST['account'];
	$CTOID = $_POST['CTOID'];
	$serie = $_POST['serie'];
	$multiplier = $_POST['multiplier'];
	$serieName = substr($serie,0,4);
	$dividend = $_POST['dividend'];
	$unpaid = $_POST['unpaid'];
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
		
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
		return;
	}

	if($multiplier == 1000){	
		if($jpmfutureclose == "N"){
			$sql = "INSERT INTO customertransactionclose (CTCTranDate,CTCPosition,CTCUnderlying,CTCFutureName,CTCVolume,CTCSpot,CTCTotalInterest,CTCNetInterest,CTCFuturePrice,CTCValue,CTCForceCloseFlag,CTCDividend,CTCUnpaid,CTOID,SerieID,CustomerID) SELECT '$tranDate','$position','$underlying','$futureName','$volume','$spot','$totalInterest','$netInt','$futurePrice','$value','$flag','$dividend','$unpaid','$CTOID',S.SerieID,C.CustomerID FROM serie AS S,customer AS C WHERE S.SName = '$serie' AND C.Account = '$account'";
		}else{
			$sql = "INSERT INTO customertransactionclose (CTCTranDate,CTCPosition,CTCUnderlying,CTCFutureName,CTCVolume,CTCSpot,CTCTotalInterest,CTCNetInterest,CTCFuturePrice,CTCValue,CTCForceCloseFlag,CTCDividend,CTCUnpaid,CTOID,SerieID,CustomerID,jpmfutureclose) SELECT '$tranDate','$position','$underlying','$futureName','$volume','$spot','$totalInterest','$netInt','$futurePrice','$value','$flag','$dividend','$unpaid','$CTOID',S.SerieID,C.CustomerID,'$jpmfutureclose' FROM serie AS S,customer AS C WHERE S.SName = '$serie' AND C.Account = '$account'";
		}

		if (mysqli_query($con, $sql)) {
			echo "Closed Transaction record stored successfully";
			$CTCID = mysqli_insert_id($con);
		} else {
			echo "Error save Closed Transaction: " . $sql . mysqli_error($con);
			return;
		}
	
		//Insert Company's Transaction

		if($jpmfutureclose == "N"){	
			//Underlying
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

			//underlying
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','$underlying','$position','$underlyingVolume','$spot','$spotvalue','$spotcash',S.SerieID,C.CustomerID,'$CTCID' FROM serie AS S,customer AS C WHERE S.SName = 'UI' AND C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS stock record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value','$cash',S.SerieID,C.CustomerID,'$CTCID' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
		} else{
			if ($position == "Long"){
				$cash = $value;
				$companyFuture = "Short";
				$jpmvolume = $volume;
				$volume = -1*$volume;
				$jpmvalue = $jpmfutureclose*$jpmvolume*$multiplier;
				$jpmcash = 	-1*$jpmvalue;
			}
			elseif ($position == "Short"){
				$cash = -1*$value;
				$companyFuture = "Long";
				$jpmvolume = -1*$volume;
				$jpmvalue = $jpmfutureclose*$volume*$multiplier;
				$jpmcash = 	$jpmvalue;
			}

			//Future JPM
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$position','$jpmvolume','$jpmfutureclose','$jpmvalue','$jpmcash',S.SerieID,C.CustomerID,'$CTCID' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging JPM future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value','$cash', S.SerieID,C.CustomerID,'$CTCID' FROM serie AS S,customer AS C WHERE S.SName = '$serieName' AND C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
		}
	}
	elseif($multiplier != 1000){
		//Get serieID
		$serieID = 0;
		$sql = "SELECT SerieID FROM `serie` WHERE SName = '$serieName' AND Underlying = '$underlying'";
		if($serieName == "A100")
		{
			$sql = "SELECT SerieID FROM `serie` WHERE SName = '$serieName' ";
		}

		$result = mysqli_query($con,$sql);
		if(mysqli_num_rows($result)>0){
			while($row = mysqli_fetch_array($result)) {
				$serieID = $row['SerieID'];
			}
		}

		if($jpmfutureclose == "N"){	
			$sql = "INSERT INTO customertransactionclose (CTCTranDate,CTCPosition,CTCUnderlying,CTCFutureName,CTCVolume,CTCSpot,CTCTotalInterest,CTCNetInterest,CTCFuturePrice,CTCValue,CTCForceCloseFlag,CTCDividend,CTCUnpaid,CTOID,SerieID,CustomerID) SELECT '$tranDate','$position','$underlying','$futureName','$volume','$spot','$totalInterest','$netInt','$futurePrice','$value','$flag','$dividend','$unpaid','$CTOID','$serieID',C.CustomerID FROM customer AS C WHERE C.Account = '$account'";
		}else{

			$sql = "INSERT INTO customertransactionclose (CTCTranDate,CTCPosition,CTCUnderlying,CTCFutureName,CTCVolume,CTCSpot,CTCTotalInterest,CTCNetInterest,CTCFuturePrice,CTCValue,CTCForceCloseFlag,CTCDividend,CTCUnpaid,CTOID,SerieID,CustomerID,jpmfutureclose) SELECT '$tranDate','$position','$underlying','$futureName','$volume','$spot','$totalInterest','$netInt','$futurePrice','$value','$flag','$dividend','$unpaid','$CTOID','$serieID',C.CustomerID,'$jpmfutureclose' FROM customer AS C WHERE C.Account = '$account'";			
		}

		if (mysqli_query($con, $sql)) {
			echo "Closed Transaction record stored successfully";
		} else {
			echo "Error save Closed Transaction: " . $sql . mysqli_error($con);
			return;
		}	

		$CTCID = mysqli_insert_id($con);
		
		//Insert Company's Transaction
		if($jpmfutureclose == "N"){	
			//Underlying
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
			//underlying
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','$underlying','$position','$underlyingVolume','$spot','$spotvalue','$spotcash',S.SerieID,C.CustomerID,'$CTCID' FROM serie AS S,customer AS C WHERE S.SName = 'UI' AND C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS stock record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}

			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value','$cash','$serieID',C.CustomerID,'$CTCID' FROM customer AS C WHERE C.Account = '$account'";

			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
		}else{
			if ($position == "Long"){
				$cash = $value;
				$companyFuture = "Short";
				$jpmvolume = $volume;
				$volume = -1*$volume;
				$jpmvalue = $jpmfutureclose*$jpmvolume*$multiplier;
				$jpmcash = 	-1*$jpmvalue;
			}
			elseif ($position == "Short"){
				$cash = -1*$value;
				$companyFuture = "Long";
				$jpmvolume = -1*$volume;
				$jpmvalue = $jpmfutureclose*$volume*$multiplier;
				$jpmcash = 	$jpmvalue;
			}
			//Future JPM
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$position','$jpmvolume','$jpmfutureclose','$jpmvalue','$jpmcash','$serieID',C.CustomerID,'$CTCID' FROM customer AS C WHERE C.Account = '$account'";
			
			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging JPM future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}

			//Future
			$sql = "INSERT INTO companytransaction (COTDate,COTUnderlying,COTPosition,COTVolume,COTCost,COTValue,COTCash,SerieID,CustomerID,CTCID) SELECT '$tranDate','".$underlying.$serieName."','$companyFuture','$volume','$futurePrice','$value','$cash','$serieID',C.CustomerID,'$CTCID' FROM customer AS C WHERE C.Account = '$account'";

			if (mysqli_query($con, $sql)) {
				echo "\nCompany Hedging FSS future record stored successfully";
			} else {
				echo "\nError: " . $sql . mysqli_error($con);
				return;
			}
		}
	}


	$volume = $_POST['volume'];
	$position = $_POST['position'];
	$sql = "UPDATE customertransactionopen SET CTOVolumeCurrent = CTOVolumeCurrent-$volume,CTOValue=(CTOVolumeCurrent*CTOFuturePrice*$multiplier) WHERE CTOID = '$CTOID'";
	if (mysqli_query($con, $sql)) {
		echo "\nUpdate Close Volume successfully";
	} else {
		echo "\nError updating Volume record: " . mysqli_error($con);
		return;
	}

	if($dividend != 0){
		// add dividend back for only long position, we don't allow client to short at XD
		if($jpmfutureclose == "N"){
				if ($position == "Short"){
					$sql = "UPDATE dividend SET DCurrentVolume = DCurrentVolume - ($volume*$multiplier),CTCID = $CTCID 
					WHERE DCurrentVolume >= $volume and DStock='$underlying'
					and XDDate <= '$tranDate' and   XDDate > (select CTOTranDate from customertransactionopen  WHERE CTOID = '$CTOID')";
					// not limit 1 because we will deduct all dividend that match	
					if (mysqli_query($con, $sql)) {
						echo "\nUpdate Didend Current Volume successfully";
					} else {
						echo "\nError updating dividend record: " . $sql .mysqli_error($con);
						return;
					}
				}else if ($position == "Long"){
					echo "\n!!we don't allow client to short at XD, No change on XD volume";
				}
		}else{
			echo "\nThis deal is at JPM, No change on XD volume.";
		}		
	}

	if($unpaid > 0){
		$sql = "UPDATE unpaid AS UN INNER JOIN customer AS C ON UN.CustomerID = C.CustomerID SET UN.UNCurrentValue = '0',CTCID = $CTCID,UN.UNPaidDate = '$tranDate' WHERE C.Account = '$account' and UN.UNCurrentValue > '0'";
		if (mysqli_query($con, $sql)) {
			echo "\nUpdate all unpaid of this account to zero successfully";
		} else {
			echo "\nError updating unpaid record: " . mysqli_error($con);
			return;
		}
	}
	mysqli_close($con);
?>