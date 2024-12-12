<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals</title>
    <link rel="stylesheet" href="approval_and_disapproval.css"> 
</head>

<body>
</body>

</html>

<?php
require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include('session_handler.php');

if ($mysqli) {

    try {

        $key = $_ENV["SECRET_KEY"];
        $iv = $_ENV["IV"];

        if (isset($_GET['id']) && $_GET['hostel'] && $_GET['bedspace_number'] && $_GET['room_number'] && $_GET['student_name'] && $_GET['email']) {
            $id = openssl_decrypt(urldecode($_GET['id']), 'AES-128-CTR', $key, 0, $iv);
            $hostel = openssl_decrypt(urldecode($_GET['hostel']), 'AES-128-CTR', $key, 0, $iv);
            $bedspace_number = openssl_decrypt(urldecode($_GET['bedspace_number']), 'AES-128-CTR', $key, 0, $iv);
            $room_number = openssl_decrypt(urldecode($_GET['room_number']), 'AES-128-CTR', $key, 0, $iv);
            $student_name = openssl_decrypt(urldecode($_GET['student_name']), 'AES-128-CTR', $key, 0, $iv);
            $email = openssl_decrypt(urldecode($_GET['email']), 'AES-128-CTR', $key, 0, $iv);

            if ($id === false || $hostel === false || $room_number === false || $student_name === false || $email === false) {
                $log = "There was a decryption error in the url in the admin_approval.php script";
                error_logger($log);
                echo "<div class='error-message' style='text-align:center; justify-content:center; align-items:center;'>Decryption failed for ID.</div>";
                exit();
            } 
        }else {
                echo "<div class='error-message' style='text-align:center; justify-content:center; align-items:center;'>No record found</div>";
            exit();
            }



            if (isset($_POST['submit'])) {

                csrf_token_validation($_SERVER['PHP_SELF']);

                if ($_POST['confirm'] == 'yes') {
                    $id = sanitize_input($_POST['id']);
                    $hostel = sanitize_input($_POST['hostel']);
                    $room_number = sanitize_input($_POST['room_number']);
                    $bedspace_number = sanitize_input($_POST['bedspace_number']);
                    $status = 'Approved';
                    $sql = "UPDATE `students` SET `status` = '$status', `hostel` = '$hostel', `room_number` = '$room_number', `bedspace_number`= '$bedspace_number' WHERE `id` = '$id'";
                    $query = $mysqli->query($sql);

                    if ($query) {
                        echo "<div class='success-message'>Student status approved!</div>";

                        
                        $notification_text = "Your accommodation request for Hostel $hostel, Room $room_number, Bedspace $bedspace_number has been approved.";
                        $insert_notification = "INSERT INTO `notifications` (student_id, notification_text) VALUES (?, ?)";
                        $stmt = $mysqli->prepare($insert_notification);
                        $stmt->bind_param("is", $id, $notification_text);
                        $stmt->execute();
                        if ($stmt->execute()) {
                            echo "<div class='success-message'>Notification sent to the student!</div>";
                            $submission_successful = true;
                        } else {
                            echo "<div class='error-message'>Error sending notification.</div>";
                        }

                        $to = $email;
                        $subject = "Accommodation approval confirmation";
                        $message = "<p>Dear $student_name, <br /></p>";
                        $message .= "<p>Your request for accommodation at Maya Hostels has been successfully approved. Please sign into your account to view your accommodation status.</p>";
                        $message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                        $sent = mail($to, $subject, $message, $headers);
                        if ($sent) {
                            echo "<div class='success-message'>Approval mail sent successfully</div>";
                            
                        } else {
                            echo "<div class='error-message'>Error sending approval mail</div>";
                        }
                    } else {
                        echo "<div class='error-message'>Student status not approved!</div>";
                    }
                   
                } else {
                    header('Location: approve_accomodation.php');
                    exit();
                }

                echo '<p style="text-align:center;"><a class="back-link" href="approve_accomodation.php">&lt; &lt; Back to Approval page</a></p>';
                
                unset($_SESSION['csrf_token']);
                unset($_SESSION['csrf_token_expires']);
                exit();
            }
          
        

    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This is coming from admin_approval.php on admin side";
        error_logger($log);
        echo "<div class='error-message'>Student status not approved!</div>";
    }

    if (isset($submission_successful) && $submission_successful): ?>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; 

    if (isset($id) && isset($hostel) && isset($bedspace_number) && isset($room_number) && isset($student_name)) {      
        echo "<div class='confirmation-message'>";
        echo "<p>Are you sure you want to approve the following request for accommodation?</p>";
        echo '<p><strong>Name: </strong>' . htmlspecialchars($student_name) . '<br/><strong>Hostel: </strong>' . htmlspecialchars($hostel) . '<br/><strong>Bedspace Number: </strong>' . htmlspecialchars($bedspace_number) . '<br/><strong>Room Number: </strong>' . htmlspecialchars($room_number) . '</p>';

        echo '<form method="post">';
        echo '<label><input type="radio" name="confirm" value="yes" checked="checked"/> Yes</label>';
        echo '<label><input type="radio" name="confirm" value="no"/> No</label>';
        echo '<input class="btn-submit" type="submit" name="submit" value="Submit"/>';

        echo '<input type="hidden" name="hostel" value="' . htmlspecialchars($hostel) . '">';
        echo '<input type="hidden" name="bedspace_number" value="' . htmlspecialchars($bedspace_number) . '">';
        echo '<input type="hidden" name="room_number" value="' . htmlspecialchars($room_number) . '">';
        echo '<input type="hidden" name="id" value="' . htmlspecialchars($id) . '">';
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
        echo '</form>';
        echo "</div>";
    }

    echo '<p style="text-align:center;"><a class="back-link" href="approve_accomodation.php">&lt; &lt; Back to Approval page</a></p>';
}
?>


