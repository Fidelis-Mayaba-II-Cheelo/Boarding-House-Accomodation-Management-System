<?php
session_start();
include('db-connect.php');
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

            
            $sql = "DELETE FROM `ratings` WHERE `id` = ?";
            $query = $mysqli->prepare($sql);
            $query->bind_param('i', $ratingId);

            if ($query->execute()) {
                
                header('Location: review.php');
                exit;
            } else {
                echo "<p style='text-align:center; justify-content:center; align-items:center;'>Error deleting the rating.</p>";
            }
        } else {
            echo "No rating ID provided.";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from delete_rating.php from admin side";
        error_logger($log);
        echo "<div class='error-message' style='text-align:center; justify-content:center; align-items:center;'>An error occurred. Please try again later.</div>";
    }
}

