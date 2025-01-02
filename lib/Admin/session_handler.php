<?php 

if(!isset($_SESSION['email'])){
    ob_get_clean();
    header('Location: login.php');
    exit(); 
}





