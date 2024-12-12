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

        $complaint = sanitize_input($_POST['complaint']);
        $id = sanitize_input($_POST['id']);

        if ($complaint !== "" && $id !== null) {
           
            $stmt = $mysqli->prepare("INSERT INTO `complaints` (`student_id`, `complaint`) VALUES (?, ?)");
            $stmt->bind_param("is", $id, $complaint);
            $query = $stmt->execute();

            if ($query) {
                echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Complaint issued successfully</div>";
                $submission_successful = true;
            } else {
                $log = "User complaint not sent successfully";
                error_logger($log);
                echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Complaint not issued.</div>";
            }
        } 

        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_expires']);
    } catch (Exception $ex) {
        $log = $ex->getMessage() . "This is from complaints.php on student side";
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
    echo "<h1 class='headings'>Issue a complaint</h1><br/>";
    echo "<form class='form-container' method='post'>";
    echo "<input type='hidden' name='id' value='$id' />";
    echo '<textarea name="complaint" id="complaint" rows="3" placeholder="Enter your message">' . (isset($_POST['complaint']) ? htmlspecialchars($_POST['complaint']) : '') . '</textarea><br/>';
    echo '<input type="hidden" name="csrf_token" value="' . $csrf_token . '">';
    echo "<input class='btn' type='submit' value='submit' name='submit'/>";
    echo "</form>";
    echo "</body>";
    echo "</html>";
}
