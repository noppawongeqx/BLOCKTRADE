<?php
	if(isset($_POST['save'])){
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
		$accountingdividend = [];
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
		$sql="";
		foreach($accounting as $ul => $profit) {
			//echo "<tr><td style='text-align:center;'>" . $ul . "</td><td style='text-align:right;'>" . number_format($profit+$cashindividend[$ul]) ."</td><td style='text-align:right;'>".number_format($fair[$ul])."</td></tr>";
			//$sumAcc += $profit+$cashindividend[$ul];
			//$sumFair += $fair[$ul];
			$accountingdividend[$ul] = $profit+$cashindividend[$ul]-$lastyearprofit[$ul]['Account'];
			$fair[$ul] = $fair[$ul]-$lastyearprofit[$ul]['Fair'];
			$sql .= "INSERT INTO lastyearprofit (LYPStock, LYPAccount, LYPFair) VALUES ('$ul', '$accountingdividend[$ul]', '$fair[$ul]');";
		}
		//echo "<tr><th style='text-align:center;'>Total</th><th style='text-align:right;'>".number_format($sumAcc)."</th><th style='text-align:right;'>".number_format($sumFair)."</th></tr>";
		echo $sql;
		if (mysqli_multi_query($con, $sql)) {
			echo "New records created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . mysqli_error($con);
		}
		mysqli_close($con);
		//var_dump($accountingdividend);
	}
?>