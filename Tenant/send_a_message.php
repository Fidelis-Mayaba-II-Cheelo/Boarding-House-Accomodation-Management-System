<?php
session_start();
include('db-connect.php');
include('student_menu.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');


// Get student data from the session
$id = $_SESSION['id'] ?? null; 
$student_name = $_SESSION['student_name'] ?? null;
$hostel = $_SESSION['hostel'] ?? null;
$bedspace_number = $_SESSION['bedspace_number'] ?? null;
$room_number = $_SESSION['room_number'] ?? null;
$email = $_SESSION['email'] ?? null;

if (isset($_POST['submit']) && $mysqli) {
    try {
        
        csrf_token_validation($_SERVER['PHP_SELF']);

        $notification_text = sanitize_input($_POST['notification_text']);
        $id = sanitize_input($_POST['id']);

        if ($notification_text !== "" && $id !== null) {
           
            $stmt = $mysqli->prepare("INSERT INTO `admin_notifications` (`student_id`, `notification_text`) VALUES (?, ?)");
            $stmt->bind_param("is", $id, $notification_text);
            $query = $stmt->execute();

            if ($query) {
                echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Message sent successfully</div>";
                $submission_successful = true;
            } else {
                $log = "User message not sent successfully";
                error_logger($log);
                echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Message not sent.</div>";
            }
        } 

       
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_expires']);
    } catch (Exception $ex) {
        $log = $ex->getMessage() . "This is from send_a_message.php on student side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>An error occurred: " . $ex->getMessage() . "</div>";
    }
}

if (isset($submission_successful) && $submission_successful): ?>
    <script>
        refreshPageAfterSuccess();
    </script>
<?php endif; 

if (isset($id, $student_name, $hostel, $bedspace_number, $room_number, $email)) {
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>My Notifications</title>";
    echo "<link rel='stylesheet' href='complaints.css'>";
    echo "</head>";
    echo "<body>";
    echo "<h1 class='headings'>Send a message to the admin</h1><br/>";
    echo "<form class='form-container' method='post'>";
    echo "<input type='hidden' name='id' value='$id' />";
    echo '<textarea name="notification_text" id="notification_text" rows="3" placeholder="Enter your message">' . (isset($_POST['notification_text']) ? htmlspecialchars($_POST['notification_text']) : '') . '</textarea><br/>';
    echo '<input type="hidden" name="csrf_token" value="' . $csrf_token . '">';
    echo "<input class='btn' type='submit' value='submit' name='submit'/>";
    echo "</form>";
    echo "</body>";
    echo "</html>";
}
