<?php
include('db-connect.php');
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($mysqli) {

    try {
      
        $username = $_ENV["AUTHORIZATION_USERNAME"];
        $password = $_ENV["AUTHORIZATION_PASSWORD"];

        if (
            !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password
        ) {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="Restricted action"');
            exit('Enter a valid username and password to access this page.');
        }

        
        if (isset($_POST['delete'])) {
            $id = sanitize_input($_POST['id']); 

            
            if (!empty($id)) {
                
                $sql = "SELECT profile_picture FROM `students` WHERE id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $profilePicturePath = $row['profile_picture'];

                    
                    if (file_exists($profilePicturePath)) {
                        unlink($profilePicturePath); 
                    }
                }
                $sql = "DELETE FROM `students` WHERE `id` = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id);
             

                if ($stmt->execute()) {
                    echo "<p>Student with ID $id deleted successfully.</p>";
                    echo "<script src='delete.js'></script>";
                    header("Location: view_students.php");
                } else {
                    echo "<p style='text-align:center; justify-content:center; align-items:center;'>Error deleting student with ID $id.</p>";
                }
            } else {
                echo "<p style='text-align:center; justify-content:center; align-items:center;'>No valid student ID provided.</p>";
            }
        } else {
            echo "<p style='text-align:center; justify-content:center; align-items:center;'>No delete request received.</p>";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from delete.php from admin side";
        error_logger($log);
        echo "<div class='error-message' style='text-align:center; justify-content:center; align-items:center;'>An error occurred. Please try again later.</div>";
    }
}
