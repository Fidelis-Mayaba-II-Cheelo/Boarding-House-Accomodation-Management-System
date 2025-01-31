<?php
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($mysqli) {
    try {
        
        $key = $_ENV["SECRET_KEY"]; 
        $iv = $_ENV["IV"];
        if (isset($_GET['id'])) {
            $ratingId = openssl_decrypt(urldecode($_GET['id']), 'AES-128-CTR', $key, 0, $iv);

           
            $sql = "UPDATE `ratings` SET `read_status` = 1 WHERE `id` = ?";
            $query = $mysqli->prepare($sql);
            $query->bind_param('i', $ratingId);

            if ($query->execute()) {
                
                header('Location: review.php');
                exit;
            } else {
                echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the rating as read.</p>";
            }
        } else {
            echo "<p class='warning' style='text-align:center; justify-content:center; align-items:center;'>No rating ID provided.</p>";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This is coming from mark_rating_read.php on admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the rating as read</div>";
    }
}

