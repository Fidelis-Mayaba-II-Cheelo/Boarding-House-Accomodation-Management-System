<?php
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

        if (isset($_GET['id']) && $_GET['email'] && $_GET['student_name'] && $_GET['hostel'] && $_GET['room_number'] && $_GET['bedspace_number']) {
            $id = openssl_decrypt(urldecode($_GET['id']), 'AES-128-CTR', $key, 0, $iv);
            $decrypted_email = openssl_decrypt(urldecode($_GET['email']), 'AES-128-CTR', $key, 0, $iv);
            $decrypted_student_name = openssl_decrypt(urldecode($_GET['student_name']), 'AES-128-CTR', $key, 0, $iv);
            $decrypted_hostel = openssl_decrypt(urldecode($_GET['hostel']), 'AES-128-CTR', $key, 0, $iv);
            $decrypted_room_number = openssl_decrypt(urldecode($_GET['room_number']), 'AES-128-CTR', $key, 0, $iv);
            $decrypted_bedspace_number = openssl_decrypt(urldecode($_GET['bedspace_number']), 'AES-128-CTR', $key, 0, $iv);

            $status = 'None';
            $hostel = null;
            $bedspace_number = null;
            $room_number = null;
            $sql = "UPDATE `students` SET `status`='$status', `hostel`='$hostel', `bedspace_number`='$bedspace_number', `room_number`='$room_number' WHERE `id` = '$id'";
            $query = $mysqli->query($sql);
            if ($query) {
                $notification_text = "Hello, you have been evicted from your room at Maya hostels. To find out more, contact the admin.";
                $sql = "INSERT INTO `notifications` (`student_id`, `notification_text`) VALUES ('$id', '$notification_text')";
                $query = $mysqli->query($sql);
                if ($query) {
                    echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Message sent successfully</div>";

                    $to = $decrypted_email;
                    $subject = "Eviction Notice";
                    $message = "<p>Dear $decrypted_student_name, <br /></p>";
                    $message .= "<p>You have been evicted from room $decrypted_room_number, bedspace $decrypted_bedspace_number in the $decrypted_hostel hostel at maya hostels. Kindly contact the admin if you wish to find out more.</p>";
                    $message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                    $sent = mail($to, $subject, $message, $headers);
                    if ($sent) {
                        echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Eviction mail sent successfully</div>";
                    } else {
                        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error sending eviction mail</div>";
                    }

                    echo "<script src='evict.js'></script>";
                }
                echo "<p class='success' style='text-align:center; justify-content:center; align-items:center;'>Student evicted from room successfully</p>";
                header("Location: view_students.php");
                exit();
            } else {
                echo '<p class="error" style="text-align:center; justify-content:center; align-items:center;">Error evicting student from room</p>';
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This is from the remove_from_room.php file on the admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error evicting student from room</div>";
    }
}


