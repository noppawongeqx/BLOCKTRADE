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
	require 'menu.php';
?>
	<div class = "container">
		<?php 
			echo "<br><br><br><br>";
			echo "	<div class='col-md-6'>
						<table class='table'>
							<tr>
								<td>
									<th>Realized Profit</th>
								</td>
								<td>
									<td><a class='btn btn-success' href='excel.php?fair=1' role='button'>Download to Excel</a></td>
								</td>
							</tr>
							<tr>
								<td>
									<th>Unrealized Profit (Stock)</th>
								</td>
								<td>
									<td><a class='btn btn-success' href='excel.php?stock=1' role='button'>Download to Excel</a></td>
								</td>
							</tr>
							<tr>
								<td>
									<th>Unrealized Profit (Futures)</th>
								</td>
								<td>
									<td><a class='btn btn-success' href='excel.php?futures=1' role='button'>Download to Excel</a></td>
								</td>
							</tr>
					  	</table>
					</div>";

			// echo "<br>
			// 	  <br>
			// 	  <br>";
			// echo "<div class= 'row'>
			// 		<div class='col-md-4'>
			// 			<h3>Realized Profit</h3>
			// 		</div>
			// 		<div class='col-md-6'>
			// 			<a class='btn btn-success' href='excel.php?fair=1' role='button'>Download to Excel</a>
			// 		</div>
			// 	  </div>
			// 	  <br>
			// 	  <br>";
			// echo "<div class= 'row'>
			// 		<div class='col-md-4'>
			// 			<h3>Unrealized Profit (Stock)</h3>
			// 		</div>
			// 		<div class='col-md-6'>
			// 			<a class='btn btn-success' href='excel.php?stock=1' role='button'>Download to Excel</a>
			// 		</div>
			// 	  </div>
			// 	  <br>
			// 	  <br>";
			// echo "<div class= 'row'>
			// 		<div class='col-md-4'>
			// 			<h3>Unrealized Profit (Futures)</h3>
			// 		</div>
			// 		<div class='col-md-6'>
			// 			<a class='btn btn-success' href='excel.php?futures=1' role='button'>Download to Excel</a>
			// 		</div>
			// 	  </div>";
		?>
		
	</div>
</body>
</html>