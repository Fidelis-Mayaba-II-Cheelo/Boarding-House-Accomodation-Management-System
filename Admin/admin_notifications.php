<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaints</title>
    <link rel="stylesheet" href="view_complaints.css">
</head>

<body>

    <?php
    if ($mysqli) {
        try {

            $records_per_page = 5;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;

            $offset = ($page - 1) * $records_per_page;


            $sql = "SELECT admin_notifications.id, admin_notifications.notification_text, admin_notifications.date_added, admin_notifications.student_id, students.student_name, students.hostel, students.room_number FROM admin_notifications INNER JOIN students ON admin_notifications.student_id = students.id WHERE is_read = 0 LIMIT $records_per_page OFFSET $offset";
            $query = $mysqli->query($sql);
           
            $key = $_ENV["SECRET_KEY"]; 
            $iv = $_ENV["IV"]; //
            if ($query->num_rows > 0) {
                while ($row = $query->fetch_assoc()) {
                    $encrypted_id = urlencode(openssl_encrypt($row['student_id'], 'AES-128-CTR', $key, 0, $iv));
                    $student_name = $row['student_name'];
                    $hostel = $row['hostel'];
                    $room_number = $row['room_number'];
                    echo '<div class="complaint-card">';
                    echo '<p class="complaint-text">' . htmlspecialchars($row['notification_text']) . '</p>';
                    echo '<p class="complaint-date">' . htmlspecialchars($row['date_added']) . '</p>';
                    echo "<p class='complaint-text'>From: $student_name from room $room_number in the $hostel hostel.</p>";

                    $admin_notifications_id = $row['id'];  

                    
                    echo '<button class="btn" onclick="markAsRead(' . $admin_notifications_id . ')">Mark as Read</button>';

                    
                    echo '<p class="btn"><a href="send_message_to_student.php?id=' . $encrypted_id . '">Reply to Student</a></p>';
                    echo '</div>';
                }
                $total_sql = "SELECT COUNT(*) AS total FROM admin_notifications WHERE is_read = 0";
                $total_query = $mysqli->query($total_sql);
                $total_result = $total_query->fetch_assoc();
                $total_records = $total_result['total'];

                 
                $total_pages = ceil($total_records / $records_per_page);

                
                echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
                if ($page > 1) {
                    $previous_page = $page - 1;
                    echo "<a href='?page=$previous_page'>&laquo; Previous</a> ";
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<strong>$i</strong>";
                    }
                }

                if ($page < $total_pages) {
                    $next_page = $page + 1;
                    echo "<a href='?page=$next_page'>Next &raquo;</a>";
                }
                echo "</div>";
            } else {
                echo "<p class='no-complaints'>No new notifications.</p>";
            }
        } catch (Exception $ex) {
            $log = $ex->getMessage() . ": " . "Error coming from admin_notifications.php on admin side";
            error_logger($log);
            echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>'Error: Maya Hostels send message page currently unavailable. Please try again later.'</p>";
        }
    }
    ?>

    <script>
        function markAsRead(admin_notifications_id) {
           
            var xhr = new XMLHttpRequest();

            
            xhr.open('POST', 'mark_admin_notifications_read.php', true);

            
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

         
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Notification marked as read!');
                    location.reload(); 
                } else {
                    alert('Error marking notification as read.');
                }
            };

        
            xhr.send('admin_notifications_id=' + admin_notifications_id);
        }
    </script>

</body>

</html>