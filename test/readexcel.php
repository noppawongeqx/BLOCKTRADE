<html>
<head>
</head>

<body>
<?php 
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

foreach($spot as $x => $x_value) {
	foreach ($x_value as $x_value){
		echo "Key=" . $x . ", Value=" . $x_value;
		echo "<br>";
	}
}

foreach($futures as $x => $x_value) {
    echo "Key=" . $x . ", Value=" . $x_value;
    echo "<br>";
}
?>
</body>
</html>
