<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');
include('session_handler.php');

if ($mysqli) {
    try {
        if (isset($_POST['submit'])) {

            csrf_token_validation($_SERVER['PHP_SELF']);

            $notification_text = sanitize_input($_POST['notification_text']);

            if ($notification_text !== "") {
                
                $sql = "SELECT * FROM students";
                $stmt = $mysqli->prepare($sql);
                $stmt->execute();
                $query = $stmt->get_result();

                if ($query) {
                    
                    while ($row = $query->fetch_assoc()) {
                        $id = $row['id'];
                        $email = $row['email'];

                        
                        $insert_sql = "INSERT INTO notifications (student_id, notification_text) VALUES (?, ?)";
                        $insert_stmt = $mysqli->prepare($insert_sql);
                        $insert_stmt->bind_param('is', $id, $notification_text); 
                        $insert_stmt->execute();
                        
                        if ($insert_stmt->execute()) {
                            continue;
                        } else {
                            echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error sending message to student ID: $id</div>";
                        }

                        $insert_stmt->close();
                    }
                    
                    echo "<div class='success'>Message sent successfully to all students</div>";
                    $submission_successful = true;
                    unset($_SESSION['csrf_token']);
                    unset($_SESSION['csrf_token_expires']);

                    $to = $email;
                    $subject = "Notice To All Students";
                    $message = "<p>Dear Student, <br /></p>";
                    $message .= "<p>You have received a general email sent to all students associated with Maya hostels from the Admin. Please sign into your account and check your notifications.</p>";
                    $message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                    $sent = mail($to, $subject, $message, $headers);
                    if ($sent) {
                        echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Mail sent successfully to all students</div>";
                    } else {
                        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error sending mail to all students</div>";
                    }

                } else {
                    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error fetching student list</div>";
                }
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from custom_message.php on admin side";
        error_logger($log);
        echo 'Error: ' . "Maya Hostels custom send message page currently unavailable. Please try again later.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Custom Message</title>
    <link rel='stylesheet' href='send_message.css'>
</head>

<body>

<?php
    if (isset($submission_successful) && $submission_successful): ?>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>

    <h1 class='headings'>Send Message To All Students</h1>
    <form method='post' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <textarea name='notification_text' id='notification_text' rows='3' placeholder='Enter your message'><?php echo isset($_POST['notification_text']) ? htmlspecialchars($notification_text): ''; ?></textarea><br />
        <input class='btn' type='submit' value='Submit' name='submit' />
    </form>
</body>

</html>