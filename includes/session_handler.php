<?php

if(!isset($_SESSION['email'])){
    ob_get_clean();
    header('Location: Login.php');
    exit();
}


if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expires']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expires'] = time() + 3600;
}

$csrf_token = $_SESSION['csrf_token'];
$submission_successful = false;
$message = '';