<?php
session_start();
include('db-connect.php');
include('menu.php');
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
            $id = openssl_decrypt(urldecode($_GET['id']), 'AES-128-CTR', $key, 0, $iv);

           
        } else {
            echo "<div class='no-complaints'>No record found</div>";
            exit();
        }

        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);

            $id = sanitize_input($_POST['id']);
            $notification_text = sanitize_input($_POST['notification_text']);

            if ($notification_text !== "" && $id !== null) {

                $sql = "INSERT INTO `notifications` (`student_id`, `notification_text`) VALUES (?, ?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("is", $id, $notification_text);
               

                if ($stmt->execute()) {
                    echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Message sent successfully</div>";
                    $submission_successful = true;
                    unset($_SESSION['csrf_token']);
                    unset($_SESSION['csrf_token_expires']);
                    
                    if (isset($submission_successful) && $submission_successful): ?>
                        <script>
                            refreshPageAfterSuccess();
                        </script>
                    <?php endif; 
                    
                    exit();
                } else {
                    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error, message not sent</div>";
                }
            } 
        } else if(isset($id)){
            echo "<!DOCTYPE html>";
            echo "<html lang='en'>";
            echo "<head>";
            echo "<meta charset='UTF-8'>";
            echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
            echo "<title>My Notifications</title>";
            echo "<link rel='stylesheet' href='send_message.css'>";
            echo "</head>";
            echo "<body>";
            echo "<h1 class='headings'>Reply To Student</h1>";
            echo '<form method="post">';
            echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
            echo "<input type='hidden' name='id' value='$id'>";
            echo '<textarea name="notification_text" id="notification_text" rows="3" placeholder="Enter your message">' . (isset($_POST['notification_text']) ? htmlspecialchars($_POST['notification_text']) : '') . '</textarea><br/>';
            echo "<input class='btn' type='submit' value='submit' name='submit'/>";
            echo "</form>";
            echo "</body>";
            echo "</html>";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from send_message_to_student.php on admin side";
        error_logger($log);
        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>'Error: Maya Hostels send message page currently unavailable. Please try again later.'</p>";
    }
}
