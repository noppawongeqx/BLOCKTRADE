<?php
if(isset($_POST['startdate']) AND isset($_POST['enddate'])){
	$account = $_POST['account'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$underlying = $_POST['underlying'];
	$name = $_POST['name'];

	$sql = "SELECT C.MKTID,C.MKTName,C.Account,C.Team,CTO.CTOPosition,CTO.CTOUnderlying,CTO.CTOSpot,CTO.CTOFuturePrice,CTO.CTOTranDate,CTO.CTOVolumeStart,CTC.CTCSpot,CTC.CTCFuturePrice,CTC.CTCTranDate,CTC.CTCVolume,CTC.CTCDividend,S.SMultiplier FROM customertransactionopen AS CTO INNER JOIN customertransactionclose AS CTC ON CTO.CTOID = CTC.CTOID INNER JOIN serie AS S ON CTO.SerieID = S.SerieID INNER JOIN customer AS C ON CTO.CustomerID = C.CustomerID WHERE CTC.CTCTranDate BETWEEN '$startdate' AND '$enddate'";

	echo "<div class='col-md-12'>";
	echo "<h3><p class='text-center'>All Reward ".date("d/m/Y", strtotime($startdate))." To  ".date("d/m/Y", strtotime($enddate));
	if($account != ""){
		$sql = $sql." AND C.Account = '$account'";
		echo " of Account <strong>$account</strong>";
	}
	if($underlying != ""){
		$sql = $sql." AND CTC.CTCUnderlying = '$underlying'";
		echo " AND  Underlying : $underlying";
	}
	if($name != "All"){
		$sql = $sql." AND C.MKTName = '$name'";
		echo " AND  Marketing Name : $name";
	}
	$sql .= " ORDER BY CTCTranDate";
	echo "</p></h3>";
	echo "</div>";

	$servername = "172.16.24.235";
	$username = "mycustom";
	$password = "mypass";
	$dbname = "blocktradetest";

	$queryresult = 0;
	$totalcompanyprofit = 0;
	// Create connection
	$con = mysqli_connect($servername, $username, $password, $dbname);
	mysqli_set_charset($con,"utf8");
	if (!$con) {
		die('Could not connect: ' . mysqli_error($con));
	}

	$result = mysqli_query($con,$sql);
	echo "<div class='col-md-12'>";
	echo "<table class='table'>";
	echo "<thead>
		        <tr>
				  <th>MKT ID</th>
				  <th>MKT Name</th>
				  <th>Account</th>
				  <th>Open Date</th>
				  <th>Close Date</th>
				  <th>Underlying</th>
				  <th>Position</th>
				  <th style='text-align:right;'>Open Price</th>
				  <th style='text-align:right;'>Open Future Price</th>
				  <th style='text-align:right;'>Open Volume</th>
		          <th style='text-align:right;'>Company Profit</th>
				  <th style='text-align:right;'>IC Profit</th>
		        </tr>
		      </thead>
		      <tbody>";
	if(mysqli_num_rows($result)>0){
		$queryresult = mysqli_num_rows($result);
		while($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			echo "<td>".$row['MKTID']."</td>";
			echo "<td>".$row['MKTName']."</td>";
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
			echo "<td style='text-align:right;'>".$row['CTOFuturePrice']."</td>";
			echo "<td style='text-align:right;'>".$row['CTCVolume']."</td>";
			echo "<td style='text-align:right;'>".number_format($companyprofit,2,'.',',')."</td>";
			echo "<td style='text-align:right;'>".number_format(round($companyprofit/8,3),2,'.',',')."</td>";
			echo "</tr>";
			$totalcompanyprofit += $companyprofit;
		}
	}
	echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><th style='text-align:right;'>Total</th><th style='text-align:right;'>".number_format($totalcompanyprofit,2,'.',',')."</th><th style='text-align:right;'>".number_format(($totalcompanyprofit/8),2,'.',',')."</th></tr>";
	echo "</tbody></table></div>";
	mysqli_close($con);
	if($queryresult == 0) {
		echo "No result";
	}
	else
		echo "<br><br><a class='btn btn-success' href='excel.php?reward=".urlencode($sql)."' role='button'>Download to Excel</a>";
}
?>