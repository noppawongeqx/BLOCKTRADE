<!DOCTYPE html>
<html lang="th">
<head>
	<meta http-equiv="Content-Language" content="th"> 
	<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
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
 	<style type="text/css">
 	.papers {
	background-color:#fff;
	border:1px solid #ccc;
	box-shadow:inset 0 0 30px rgba(0,0,0,0.1),1px 1px 3px rgba(0,0,0,0.2);
	position:relative;
	width:98%;
	padding:2em;
	margin:0px auto;
	margin-top:40px;
	font-size:12px;
}
 	</style>
<title>Calculate Block Trade Price</title>
</head>
<body>
<div class='contrainer'>
	<div class="row">
			<div class="col-md-6">
				<input type="button" id="senddata" value="Login" class="btn btn-primary btn-block btn-lg">  
			</div>
			<div class="col-md-6">
				<p class="bg-danger gap" id="error"></p> 
			</div>
	</div>
</div>
</body>
<script type="text/javascript">
$('#senddata').click(function(){
	
		$.ajax({
	 		type: 'post',
	        url: 'info.php',
	        data: {test: 1},
	        success: function( data ) {
	        	document.getElementById('error').innerHTML = data;
	        }
	    });
	
});
</script>
</html>