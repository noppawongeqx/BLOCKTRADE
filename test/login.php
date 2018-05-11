<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link href="css/styles.css" rel="stylesheet">
<title>Insert title here</title>
</head>
<?php 
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
If(isset($_SESSION['loggedin'])){
	header('Location: index.php');
	exit();
}

if(isset($_POST['submit'])){
	$name = stripslashes($_POST['username']);
	$password = stripslashes($_POST['password']);
	$name = mysql_escape_string($name);
	$password = mysql_escape_string($password);
	
	if(login($name,$password)){
		header('Location: index.php');
		exit;
	} else {
		$error[] = 'Wrong username or password or Unregistered IP address';
	}
}//end if submit

function login($name,$userpass){
	
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
	
	$mail = mysqli_real_escape_string($con, $name);
	/* create a prepared statement */
	if ($stmt = mysqli_prepare($con, "SELECT Password,IP FROM `user` WHERE `Username`=?")) {
	
		/* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "s", $mail);
	
		/* execute query */
		mysqli_stmt_execute($stmt);
	
		/* bind result variables */
		mysqli_stmt_bind_result($stmt, $hashed, $IP);
		
		/* fetch values */
		mysqli_stmt_fetch($stmt);
				
		/* close statement */
		mysqli_stmt_close($stmt);
	}
	echo $IP;
	echo $hashed;
	
	/* close connection */
	mysqli_close($con);
	if($userpass == $hashed){
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';

		if($ipaddress == $IP){
			echo $ipaddress;
			$_SESSION['loggedin'] = $name;
			return true;
		}
		echo $ipaddress;
	}
	
	
}

?>
<body>
	<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Log In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <?php
								//check for any errors
								if(isset($error)){
									foreach($error as $error){
										echo '<p class="bg-danger">'.$error.'</p>';
									}
								}
							?>
						    <fieldset>
                                <div class="form-group">
                                    <div class="input-group">
                                		<span class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>
                                    	<input class="form-control" placeholder="Username" name="username" id="username" type="text" autofocus required>
                                	</div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                		<span class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>
                                    	<input class="form-control" placeholder="Password" name="password" id="password" type="password" value="" required>
                                    </div>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" name="submit" value="Login" class="btn btn-primary btn-block btn-lg">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>