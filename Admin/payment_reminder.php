<?php
session_start();
include('db-connect.php');
include('functions.php');


if($mysqli){
    try{
        $sql = "SELECT * FROM `students`";
        $stmt = $mysqli->prepare($sql);
        $query = $stmt->execute();
        $result = $stmt->get_result();
        if($query){
            while($row = mysqli_fetch_array($result)){
                $phoneNumbers = $row['phone_number'];
                $email = $row['email'];
                $id = $row['id'];
                $student_name = $row['student_name'];
            }
            $datenow = getdate();
            $theMonth = $datenow['month'];
            if($theMonth== 'February' || $theMonth == 'March' || $theMonth == 'April' || $theMonth == 'May' || $theMonth == 'June' || $theMonth == 'September' || $theMonth == 'October' || $theMonth == 'November'){
                $dayOfMonth = $datenow['mday'];
                if($dayOfMonth == 28){
                    $to = $email;
                    $subject = "Reminder to make your monthly payment";
                    $body = "<p>Hello, $student_name</p>";
                    $body .= "<p>We are nearing the end of the month and the maya hostels team would like to remind you to make your monthly deposit if you haven't yet done so</p>";
    
                    $body .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
    
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";
    
                    mail($to, $subject, $body, $headers);
    
                    $notification_text = "<p>We are nearing the end of the month and the maya hostels team would like to remind you to make your monthly deposit if you haven't yet done so</p>";
                    $notification_text .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
    
    
                    $sql = "INSERT INTO `notifications` (`student_id`, `notification_text`) VALUES (?, ?)";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("is",$id,$notification_text);
                    $query = $stmt->execute();

                    if($query){
                        echo "Payment reminders sent successfully";
                    } else {
                        echo "Payment reminders not sent";
                    }
    
                }
            }
        }
    }catch(Exception $ex){
        $log = $ex->getMessage() . ": " . "This is coming from payment_reminder.php on admin side";
        error_logger($log);
        echo "<div class='error-message'>Payment Reminder notifications not sent</div>";
    }
   
}