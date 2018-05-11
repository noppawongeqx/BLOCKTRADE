<?php 
$servername = "172.16.24.235";
$username = "mycustom";
$password = "mypass";
$dbname = "blocktradetest";
	
$dividend = array();
// Create connection
$con = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$con) {
	die("Connection failed: " . mysqli_connect_error());
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
else
	echo "0";
mysqli_close($con);
var_dump($dividend);
echo "<br>";
echo $dividend['BTS']['Dividend'];

?>