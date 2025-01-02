<?php
session_start();
include('db-connect.php');
include('student_menu.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications</title>
    <link rel="stylesheet" href="notifications.css">
</head>

<body>

<?php
if ($mysqli) {
    try {
        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];

            $records_per_page = 5;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1;

            $offset = ($page - 1) * $records_per_page;

            $sql = "SELECT * FROM notifications WHERE student_id = ? AND is_read = 0 ORDER BY `created_at` LIMIT $records_per_page OFFSET $offset";
            $query = $mysqli->prepare($sql);
            $query->bind_param('i', $student_id);
            $query->execute();
            $result = $query->get_result();

            echo '<div class="notifications-container">';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="notification-item">';
                    echo "<p>" . htmlspecialchars($row['notification_text']) . "</p>";
                    echo "<span class='timestamp'>" . htmlspecialchars($row['created_at']) . "</span>";
                    echo '</div>';
                }

                echo "<button class='mark-all-read-btn'><a href='mark_notifications_as_read.php'>Mark all as read</a></button>";

                $total_sql = "SELECT COUNT(*) AS total FROM notifications WHERE student_id = ? AND is_read = 0";
                $total_query = $mysqli->prepare($total_sql);
                $total_query->bind_param('i', $student_id);
                $total_query->execute();
                $total_result = $total_query->get_result()->fetch_assoc();
                $total_records = $total_result['total'];

                $total_pages = ceil($total_records / $records_per_page);

                echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
                if ($page > 1) {
                    $previous_page = $page - 1;
                    echo "<a href='?page=$previous_page'>&laquo; Previous</a> ";
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<strong>$i</strong> ";
                    } else {
                        echo "<a href='?page=$i'>$i</a> ";
                    }
                }

                if ($page < $total_pages) {
                    $next_page = $page + 1;
                    echo "<a href='?page=$next_page'>Next &raquo;</a>";
                }
                echo "</div>";
            } else {
                echo '<div class="no-notifications">No new notifications.</div>';
            }

            echo '</div>';
        } else {
            echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Student is not logged in.</div>";
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": This is from the notifications.php file on the client side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error loading notifications. Try again later</div>";
    }
}
?>

</body>
</html>
