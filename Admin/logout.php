<?php 
session_start();

// Unset all session variables
$_SESSION = array();

session_destroy();

setcookie('logged_in', '', time() - 3600, "/", "", true, true);

ob_end_clean();
header('location: login.php');
exit();