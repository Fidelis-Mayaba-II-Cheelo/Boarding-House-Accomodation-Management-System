<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');
include('session_handler.php');


if ($mysqli) {
    try {
      
        $columns = [];
        $sql = "SELECT id, student_name, email FROM students";
        $query = $mysqli->query($sql);
        if ($query) {
            while ($results = $query->fetch_assoc()) {
                $columns[] = $results; 
            }
        }

        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);

            $ourpostedValues = [];

            
            $student_id = $_POST['id'] ?? null;
            $message = $_POST['message'] ?? '';

            if ($student_id && $message) {
                $ourpostedValues[] = $student_id;
                $ourpostedValues[] = $message;

               
                $stmt = $mysqli->prepare("INSERT INTO `notifications` (`student_id`, `notification_text`) VALUES (?, ?)");
                $stmt->bind_param("is", $student_id, $message);
                $query = $stmt->execute();

                if ($query) {
                    echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Message sent successfully to the student</div>";
                    $submission_successful = true;

                    foreach ($columns as $student_data) {
                        if ($student_data['id'] == $student_id) {
                            $student = $student_data['student_name'];
                            $to = $student_data['email'];
                            break;
                        }
                    }

                    $subject = "Notice to $student";
                    $email_message = "<p>Dear $student, <br /></p>";
                    $email_message .= "<p>You have received a very personal and important notification from the admin. Please sign into your account and check your notifications to view why.</p>";
                    $email_message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                    $sent = mail($to, $subject, $email_message, $headers);
                    if ($sent) {
                        echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Mail sent successfully to $student</div>";
                        unset($_SESSION['csrf_token']);
                        unset($_SESSION['csrf_token_expires']);
                        exit();
                    } else {
                        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error sending mail to $student</div>";
                    }
                } else {
                    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error sending message to the student</div>";
                }
            } else {
                echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Invalid student ID or message.</div>";
            }
        }

        if (isset($submission_successful) && $submission_successful): ?>
            <script>
                refreshPageAfterSuccess();
            </script>
        <?php endif; 

        
        echo "<!DOCTYPE html>";
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Send Custom Message</title>';
        echo "<link rel='stylesheet' href='individual_messages.css'>";
        echo '</head>';
        echo '<body>';
        echo "<h1 class='headings'>Send Custom Message</h1>";
        echo '<div class="form-container">';
        echo "<form method='post'>";
        echo '<select name="id">';
        foreach ($columns as $student_data) {
            echo '<option value="' . $student_data['id'] . '">' . $student_data['student_name'] . '</option>';
        }
        echo '</select>';
        echo "<br />";
        echo '<textarea name="message" id="message">' . (isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '') . '</textarea>';
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
        echo "<input type='submit' name='submit' value='Send Message' class='btn' />";
        echo "</form>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": Error in individual_messages.php on admin side";
        error_logger($log);
        echo '<p class="error" style="text-align:center; justify-content:center; align-items:center;">Error: Maya Hostels send message to a particular student page is currently unavailable. Please try again later.</p>';
    }
}

