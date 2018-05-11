<!DOCTYPE html>
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="css/styles.css" rel="stylesheet">  
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
  	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  	<link rel="stylesheet" href="/resources/demos/style.css">

    <script>
	  $(function(){
	    $( "#startdate" ).datepicker({ dateFormat: "yy-mm-dd" });
	  });
	</script>

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
	require 'menu.php';?>
	<div class = "container">
		<div class = "row">	
			<div class = "col-md-12">
				<div class = "col-md-2">
					<h3>Account</h3>
				</div>
				<div class = "col-md-3">
					<h3>
						<input autofocus type="email" class="form-control" id="account" placeholder="Account / @JPM / .PTT ">
						<ul id="search_suggestion_holder"></ul>
					</h3>
				</div>
				<div class = "col-md-1">
					<h3></h3>
				</div>
				<div class = "col-md-2">
					<h3>Closing Date</h3>
				</div>
				<div class = "col-md-3">
					<h3><input type="email" class="form-control" name = 'startdate' id='startdate' placeholder="e.g. 2015-1-31"></h3>
				</div>	
			</div>
			<div class="col-md-6 col-md-offset-3">
					<h3><button type="button" id="search" class="btn btn-primary btn-lg btn-block" onclick="searchResult()">Search</button></h3>
			</div>
		</div>
		<div id="resulthere"></div>
	</div>
</body>
<script>
	$(document).ready(function(){
		var defaultdate = new Date();
		var dd = defaultdate.getDate();
		var mm = defaultdate.getMonth()+1; //January is 0!
		var yyyy = defaultdate.getFullYear();

		if(dd<10) {
		    dd='0'+dd
		} 

		if(mm<10) {
		    mm='0'+mm
		} 
		defaultdate = yyyy+'-'+mm+"-"+dd;
		document.getElementById("startdate").value = defaultdate;
		// this is all current position
		$.ajax({
	        type: 'post',
	        url: 'getResult.php',
	        data: {closedate2: defaultdate},
	        success: function( data ) {document.getElementById("resulthere").innerHTML = data;}
	    });
	});

	function searchResult(){

		var account2 = document.getElementById("account").value;
		var closedate2 = document.getElementById("startdate").value;

		if(account2.substring(0,1) == '@'){
			// this is @Search to find IF isJPM = Y
			// alert(account2);
			$.ajax({
		        type: 'post',
		        url: 'getResult.php',
		        data: {accountisjpm: account2.substring(1), closedate2: closedate2},
		        success: function( data ) {document.getElementById("resulthere").innerHTML = data;}
		    });		
		}else if(account2.substring(0,1) == '.'){
			// Add 1 dot + stock name to use this function, must be corrected name
			$.ajax({
		        type: 'post',
		        url: 'getResult.php',
		        data: {accountisstock: account2.substring(1), closedate2: closedate2},
		        success: function( data ) {document.getElementById("resulthere").innerHTML = data;}
		    });   
		}else if(account2 != ""){
			// this is normal search by account	
				$.ajax({
			        type: 'post',
			        url: 'getResult.php',
			        data: {account2: account2, closedate2: closedate2},
			        success: function( data ) {document.getElementById("resulthere").innerHTML = data;}
			    });
		}else{
			// this is all current position
				$.ajax({
			        type: 'post',
			        url: 'getResult.php',
			        data: {closedate2: closedate2},
			        success: function( data ) {document.getElementById("resulthere").innerHTML = data;}
			    });
		}

		document.getElementById("account").value = '';
		document.getElementById("account").focus();	

	}
	$(document).keypress(function(e) {
	    if(e.which == 13) {
	    	searchResult();
	    	
	    }    
	});
</script>
</html>