<?php

if(isset($_POST['CTOID'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = "SELECT CTO.CTOTranDate,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOFutureName,CTO.CTOVolumeStart,CTO.CTOVolumeCurrent,CTO.CTOSpot,
				   CTO.CTOValue,CTO.SerieID,CTO.CustomerID,CTO.CTOFuturePrice,S.SMultiplier 
			FROM customertransactionopen AS CTO INNER JOIN serie AS S ON CTO.SerieID = S.SerieID 
			WHERE CTOID = '".$_POST['CTOID']."'";
	$result = mysqli_query($con,$sql);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_array($result)) {
			$TranDate = $row['CTOTranDate'];
			$CTOPosition = $row['CTOPosition'];
			$CTOUnderlying = $row['CTOUnderlying'];
			$CTOFutureName = $row['CTOFutureName'];
			$CTOVolumeStart = $row['CTOVolumeStart'];
			$CTOVolumeStartFuture = $row['CTOVolumeStart'];
			$CTOVolumeCurrent = $row['CTOVolumeCurrent'];
			$CTOSpot = $row['CTOSpot'];
			$CTOValue = $row['CTOValue'];
			$SerieID = $row['SerieID'];
			$CustomerID = $row['CustomerID'];
			$Multiplier = $row['SMultiplier'];
			$FuturePrice = $row['CTOFuturePrice'];
		}
	}else{
		echo "Can't find this in customertransactionopen table: " . mysqli_error($con);
		return;
	}
	//Check beginning position 
	if($CTOVolumeCurrent == $CTOVolumeStart){
		$isNew = "SELECT * FROM companytransaction WHERE CTOID = '".$_POST['CTOID']."'";
		$resultnew = mysqli_query($con,$isNew);
		if(mysqli_num_rows($resultnew)>0){
			// this code will delete company transaction by CTOID in Company transaction table 
			$sql = "DELETE FROM companytransaction WHERE CTOID = '".$_POST['CTOID']."'";
			if (mysqli_query($con, $sql)) {
				echo "Companytransaction deleted successfully";
			}else{
				echo "Error deleting Companytransaction: " . mysqli_error($con);
				return;
			}
		}else{
			// this code will delete company transaction like the old day, that have no CTOID connection
			$StockVolume = $Multiplier*$CTOVolumeStart;
			if($CTOPosition == "Long"){
				$FuturePosition = "Short";
				$CTOVolumeStartFuture = $CTOVolumeStartFuture*-1;
			}else{
				$FuturePosition = "Long";
				$CTOVolumeStart = $CTOVolumeStart*-1;
			}
			$sql = "DELETE FROM `companytransaction` WHERE COTDate = '".$TranDate."' AND COTUnderlying = '".$CTOUnderlying."' AND COTPosition = '".$CTOPosition."' AND COTVolume = '".$StockVolume."'	AND COTCost = '".$CTOSpot."' AND SerieID = '28' AND CustomerID = '".$CustomerID."' LIMIT 1";
			if (mysqli_query($con, $sql)) {
				echo "Underlying in Companytransaction deleted successfully";
			} else {
				echo "Error deleting Underlying in Companytransaction: " . mysqli_error($con);
				return;
			}

			$sql = "DELETE FROM `companytransaction` WHERE COTDate = '".$TranDate."' AND COTUnderlying = '".$CTOFutureName."' 
					AND COTPosition = '".$FuturePosition."' AND COTVolume = '".$CTOVolumeStartFuture."' 
					AND COTCost = '".$FuturePrice."' AND SerieID = '".$SerieID."' AND CustomerID = '".$CustomerID."' LIMIT 1";
			if (mysqli_query($con, $sql)) {
				echo "\nFuture in Companytransaction deleted successfully";
			} else {
				echo "\nError deleting Future in Companytransaction: " . mysqli_error($con);
				return;
			}
		}
		$sql = "DELETE FROM `customertransactionopen` WHERE CTOID = '".$_POST['CTOID']."'";
		if (mysqli_query($con, $sql)) {
			echo "\nCustomertransactionopen deleted successfully";
		} else {
			echo "\nError deleting customertransactionopen: " . mysqli_error($con);
			return;
		}
		//check if there is any dividend happen
	  	$sql = "SELECT DDividend,XDDate FROM `dividend` WHERE XDDate > '".$TranDate."' AND DStock = '".$CTOUnderlying."'";
	  	$result = mysqli_query($con, $sql);
	  	if (mysqli_num_rows($result) > 0) {
	  		while($row = mysqli_fetch_assoc($result)) {
	  			echo "\nThere is dividend on ".$row['XDDate']." : ".$row['DDividend']." $";
	  		}
	  		echo "\nand we are not delete dividend record.";
	  	}else{
	  		echo "\nNo dividend happen since open.";
	  	}		

	}else{		
		echo "Already Closed Transaction, you have to delete SaveClose before.";
		return;
	}

	mysqli_close($con);
}
elseif(isset($_POST['CTCID'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = "SELECT CTC.CTCTranDate,CTC.CTCPosition,CTC.CTCUnderlying,CTC.CTCFutureName,CTC.CTCVolume,CTC.CTCSpot,CTC.CTCValue,
			CTC.SerieID,CTC.CustomerID,CTC.CTCFuturePrice,S.SMultiplier,CTC.CTOID,CTC.CTCDividend,CTC.CTCUnpaid,CTC.jpmfutureclose 
			FROM customertransactionclose AS CTC INNER JOIN serie AS S ON CTC.SerieID = S.SerieID 
			WHERE CTCID = '".$_POST['CTCID']."'";
	$result = mysqli_query($con,$sql);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_array($result)) {
			$TranDate = $row['CTCTranDate'];
			$CTCPosition = $row['CTCPosition'];
			$CTCUnderlying = $row['CTCUnderlying'];
			$CTCFutureName = $row['CTCFutureName'];
			$CTCVolumeStart = $row['CTCVolume'];
			$CTCVolumeStartFuture = $row['CTCVolume'];
			$Volume = $row['CTCVolume'];
			$CTCSpot = $row['CTCSpot'];
			$CTCValue = $row['CTCValue'];
			$SerieID = $row['SerieID'];
			$CustomerID = $row['CustomerID'];
			$Multiplier = $row['SMultiplier'];
			$FuturePrice = $row['CTCFuturePrice'];
			$CTOID = $row['CTOID'];
			$Dividend = $row['CTCDividend'];
			$Unpaid = $row['CTCUnpaid'];
			$jpmfutureclose = $row['jpmfutureclose'];
			
		}
	}else{
		echo "Can't find this in customertransactionclose table: " . mysqli_error($con)."\n";
		return;
	}
	
	$isNew = "SELECT * FROM companytransaction WHERE CTCID = '".$_POST['CTCID']."'";
	$resultnew = mysqli_query($con,$isNew);
	if(mysqli_num_rows($resultnew)>0){
		// this code will delete company transaction by CTCID in Company transaction table 
		$sql = "DELETE FROM companytransaction WHERE CTCID = '".$_POST['CTCID']."'";
		if (mysqli_query($con, $sql)) {
			echo "Companytransaction deleted successfully";
		}else{
			echo "Error deleting Companytransaction: " . mysqli_error($con);
			return;
		}
	}else{
		// this code will delete company transaction like the old day, that have no CTCID connection
		if($CTCPosition == "Long"){
			$FuturePosition = "Short";
			$CTCVolumeStartFuture = $CTCVolumeStartFuture*-1;
		}else{
			$FuturePosition = "Long";
			$CTCVolumeStart = $CTCVolumeStart*-1;
		}
		$StockVolume = $Multiplier*$CTCVolumeStart;

		$sql = "DELETE FROM `companytransaction` WHERE COTDate = '".$TranDate."' AND COTUnderlying = '".$CTCUnderlying."' AND COTPosition = '".$CTCPosition."' AND COTVolume = '".$StockVolume."' AND COTCost = '".$CTCSpot."' AND SerieID = '28' AND CustomerID = '".$CustomerID."' LIMIT 1";
		if (mysqli_query($con, $sql)) {
			echo "Underlying in Companytransaction deleted successfully";
		} else {
			echo "Error deleting Underlying in Companytransaction: " . mysqli_error($con);
			return;
		}
		$sql = "DELETE FROM `companytransaction` WHERE COTDate = '".$TranDate."' AND COTUnderlying = '".$CTCFutureName."' AND COTPosition = '".$FuturePosition."' AND COTVolume = '".$CTCVolumeStartFuture."' AND COTCost = '".$FuturePrice."' AND SerieID = '".$SerieID."' AND CustomerID = '".$CustomerID."' LIMIT 1";
		if (mysqli_query($con, $sql)) {
			echo "\nFuture in Companytransaction deleted successfully";
		} else {
			echo "\nError deleting Future in Companytransaction: " . mysqli_error($con);
			return;
		}		
	}
	$sql = "DELETE FROM `customertransactionclose` WHERE CTCID = '".$_POST['CTCID']."'";
	if (mysqli_query($con, $sql)) {
		echo "\nCustomertransactionclose deleted successfully";
	} else {
		echo "\nError deleting customertransactionclose: " . mysqli_error($con);
		return;
	}

	$sql = "UPDATE customertransactionopen SET CTOVolumeCurrent = (CTOVolumeCurrent + '$Volume'),
			CTOValue = (CTOVolumeCurrent * CTOFuturePrice * $Multiplier) WHERE CTOID = '".$CTOID."'";
	if (mysqli_query($con, $sql)) {
		echo "\nRestore Volume in customertransactionopen successfully";
	} else {
		echo "\nError updating Volume in customertransactionopen: " . mysqli_error($con);
		return;
	}

	if($Dividend > 0){
		// add dividend back for only long position, we don't allow client to short at XD
		if (is_null($jpmfutureclose)){
			if ($CTCPosition == "Short"){
				$sql = "UPDATE dividend SET DCurrentVolume = (DCurrentVolume+('$Volume' * '$Multiplier')) 
						WHERE DStock= '".$CTCUnderlying."'
						and XDDate <= '".$TranDate."' and XDDate > (select CTOTranDate from customertransactionopen  WHERE CTOID = '".$CTOID."')";
				if (mysqli_query($con, $sql)) {
					echo "\nRestore dividend volume back successfully";
				} else {
					echo "\nError updating dividend volume: " . mysqli_error($con);
					return;
				}
			}else if ($CTCPosition == "Long"){
				echo "\n!!we don't allow client to short at XD, No change on dividend volume";
			}
		}else{
			echo "\nThis deal is at JPM, No change on XD volume.";
		}	
	}

	if($Unpaid > 0){
		$sql = "UPDATE unpaid SET UNCurrentValue = UNStartValue,CTCID = '0' WHERE CTCID = '".$_POST['CTCID']."'";
		
		if (mysqli_query($con, $sql)) {
			echo "\nRestore unpaid back successfully";
		} else {
			echo "\nError updating unpaid record: " . mysqli_error($con);
			return;
		}
	}
	mysqli_close($con);
}
elseif(isset($_POST['COMID'])){
	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}
	$sql = "DELETE FROM `companytransaction` WHERE `CompanyTransactionID` = '".$_POST['COMID']."'";
	if (mysqli_query($con, $sql)) {
		echo "Record deleted successfully";
	} else {
		echo "Error deleting record: " . mysqli_error($con);
		return;
	}

	mysqli_close($con);
}
?>