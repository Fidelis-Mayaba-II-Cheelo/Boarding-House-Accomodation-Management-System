<?php
session_start();
include('db-connect.php');
include('menu.php');
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($mysqli) {
    try {

        $records_per_page = 5;
        $page = isset($_GET['page'])? (int)$_GET['page'] : 1;
        if($page < 1) $page = 1;

        $offset = ($page - 1) * $records_per_page;

        $status = 'Pending';
        $sql = "SELECT * FROM `students` WHERE `status`=? ORDER BY `hostel` LIMIT $records_per_page OFFSET $offset";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $query = $stmt->get_result();

        $key = $_ENV["SECRET_KEY"];
        $iv = $_ENV["IV"];
        if ($query) {
            echo "<div class='approval-container'>";
            if ($query->num_rows > 0) {
                echo "<table class='approval-table'>";
                echo "<thead><tr><th>Student Name</th><th>Email</th><th>Hostel</th><th>Room</th><th>Bedspace</th><th>Actions</th></tr></thead>";
                echo "<tbody>";
                while ($row = $query->fetch_assoc()) {
                    if ($row['status'] == 'Pending') {
                       
                        $encrypted_id = urlencode(openssl_encrypt($row['id'], 'AES-128-CTR', $key, 0, $iv));
                        $encrypted_student_name = urlencode(openssl_encrypt($row['student_name'], 'AES-128-CTR', $key, 0, $iv));
                        $encrypted_hostel = urlencode(openssl_encrypt($row['hostel'], 'AES-128-CTR', $key, 0, $iv));
                        $encrypted_room_number = urlencode(openssl_encrypt($row['room_number'], 'AES-128-CTR', $key, 0, $iv));
                        $encrypted_bedspace_number = urlencode(openssl_encrypt($row['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));
                        $encrypted_email = urlencode(openssl_encrypt($row['email'], 'AES-128-CTR', $key, 0, $iv));




                        echo '<tr class="approval-row">';
                        echo '<td><strong>' . htmlspecialchars($row['student_name']) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['hostel']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['room_number']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['bedspace_number']) . '</td>';
                        echo '<td>';
                        echo '<a class="btn-approve" href="admin_approval.php?id=' . $encrypted_id . '&amp;student_name=' . $encrypted_student_name . '&amp;hostel=' . $encrypted_hostel . '&amp;bedspace_number=' . $encrypted_bedspace_number . '&amp;room_number=' . $encrypted_room_number . '&amp;email=' . $encrypted_email . '">Approve</a>';
                        echo ' | ';
                        echo '<a class="btn-disapprove" href="disapprove_student.php?id=' . $encrypted_id . '&amp;student_name=' . $encrypted_student_name . '&amp;hostel=' . $encrypted_hostel . '&amp;bedspace_number=' . $encrypted_bedspace_number . '&amp;room_number=' . $encrypted_room_number . '&amp;email=' . $encrypted_email . '">Disapprove</a>';
                        echo '</td></tr>';
                    } else {
                        echo "<tr><td colspan='6'>No new applications found</td></tr>";
                    }
                }
                echo '</tbody>';
                echo '</table>';

                $total_sql = "SELECT COUNT(*) AS total FROM `students` WHERE `status`='Pending' ORDER BY `hostel`";
                $total_query = $mysqli->query($total_sql);
            $total_result = $total_query->fetch_assoc();
            $total_records = $total_result['total'];

            
            $total_pages = ceil($total_records / $records_per_page);

           
            echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
            if($page > 1){
                $previous_page = $page -1;
                echo "<a href='?page=$previous_page'>&laquo; Previous</a> ";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<strong>$i</strong>";
                }
            }

            if($page < $total_pages){
                $next_page = $page + 1;
                echo "<a href='?page=$next_page'>Next &raquo;</a>";
            }
            echo "</div>";
            } else {
                echo "<div class='no-applications'>No new applications found</div>";
            }
            echo "</div>"; 
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from approve_accomodtion.php from admin side";
        error_logger($log);
        echo "<div class='error-message'>An error occurred. Please try again later.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals</title>
    <link rel="stylesheet" href="approval.css"> 
</head>

<body>
</body>

</html>