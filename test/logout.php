<?php
session_start();
unset($_SESSION['loggedin']);
session_destroy();
echo "Logout Successful!";
echo "<meta http-equiv='refresh' content='1;url=login.php'>";
?>