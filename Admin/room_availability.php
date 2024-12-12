<?php
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');

if ($mysqli) {
    try {
        $status = "Approved";
        $sql = "SELECT * FROM `students` WHERE `status` != ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $pending_students = [];
        while ($row = $result->fetch_assoc()) {
            $pending_students[] = $row;
        }

        $sql1 = "SELECT COUNT(*) AS no_of_approved_students FROM `students` WHERE `status` = ?";
        $stmt1 = $mysqli->prepare($sql1);
        $stmt1->bind_param("s", $status);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $row1 = $result1->fetch_assoc();
        $no_of_approved_students = $row1['no_of_approved_students'];

        
        $previous_count = $_SESSION['approved_count'] ?? $no_of_approved_students;

       
        if ($no_of_approved_students < $previous_count) {
            foreach ($pending_students as $row) {
                $email = $row['email'];
                $id = $row['id'];
                $student_name = $row['student_name'];

               
                $to = $email;
                $subject = "Room Availability Notification";
                $body = "<p>Hello, $student_name</p>";
                $body .= "<p>The Maya Hostels team would like to notify you of current room vacancies. Hurry and submit your application before we are fully booked again!</p>";
                $body .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: Maya Hostels<fidelismcheeloii@gmail.com>";

                mail($to, $subject, $body, $headers);

               
                $notification_text = "The Maya Hostels team would like to notify you of current room vacancies. Check the apply for accommodation tab to view and apply for vacant rooms.";
                $sql2 = "INSERT INTO `notifications` (`student_id`, `notification_text`) VALUES (?, ?)";
                $stmt2 = $mysqli->prepare($sql2);
                $stmt2->bind_param("is", $id, $notification_text);
                $stmt2->execute();
            }
            echo "<p class='success' style='text-align:center; justify-content:center; align-items:center;'>Room availability notifications sent successfully</p>";
        } else {
            echo "<p class='success' style='text-align:center; justify-content:center; align-items:center;'>No notifications needed; count has not decreased.</p>";
        }

        
        $_SESSION['approved_count'] = $no_of_approved_students;
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": This is coming from room_availability.php on admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Room Availability notifications not sent</div>";
    }
}
