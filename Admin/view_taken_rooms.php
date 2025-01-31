<?php
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$key = $_ENV["SECRET_KEY"];
$iv = $_ENV["IV"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link type="text/css" rel="stylesheet" href="views.css" />
</head>

<body>
    <?php include('menu.php'); ?>
    <h2 class="headings">View Taken Rooms</h2>
    <?php
    if ($mysqli) {
        try {
           
            $records_per_page = 5;

            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) $page = 1; 

            
            $offset = ($page - 1) * $records_per_page;

            $hostelBedspaces = [
                'Single' => 1,
                'Double' => 2,
                'Triple' => 3,
                'Quadruple' => 4,
            ];

            $sql = "SELECT hostel, room_number, bedspace_number
                    FROM students 
                    WHERE `status` = 'Approved'
                    ORDER BY FIELD(hostel, 'Single', 'Double', 'Triple', 'Quadruple'),
                             room_number + 0 ASC, 
                             bedspace_number + 0 ASC 
                    LIMIT $records_per_page OFFSET $offset";

            
            $query = $mysqli->query($sql);

           
            if ($query && $query->num_rows > 0) {
                echo '<div class="table-container">';
                echo '<table cellspacing="0" border="1">';
                echo '<thead><tr><th>Hostel</th><th>Room Number</th><th>Bedspace Number</th><th>Action</th></tr></thead>';
                echo '<tbody>';

                while ($result = $query->fetch_assoc()) {
                    $encrypted_hostel = urlencode(openssl_encrypt($result['hostel'], 'AES-128-CTR', $key, 0, $iv));
                    $encrypted_room_number = urlencode(openssl_encrypt($result['room_number'], 'AES-128-CTR', $key, 0, $iv));
                    $encrypted_bedspace_number = urlencode(openssl_encrypt($result['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($result['hostel']) . "</td>";
                    echo "<td>" . htmlspecialchars($result['room_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($result['bedspace_number']) . "</td>";
                    echo "<td>
                    <form method='get' action='view_occupant.php'>
                        <input type='hidden' name='hostel' value='" . htmlspecialchars($encrypted_hostel) . "'/>
                        <input type='hidden' name='room_number' value='" . htmlspecialchars($encrypted_room_number) . "'/>
                        <input type='hidden' name='bedspace_number' value='" . htmlspecialchars($encrypted_bedspace_number) . "'/>
                        <input type='submit' value='View Occupant'/>
                    </form>
                  </td>";
                    echo "</tr>";
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo "<p style='text-align:center; justify-content:center; align-items:center;'>No taken rooms available.</p>";
            }

            
            $total_sql = "SELECT COUNT(*) AS total FROM students";
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
                    echo "<strong>$i</strong> ";
                } 
            }

            if($page < $total_pages){
                $next_page = $page + 1;
                echo "<a href='?page=$next_page'>Next &raquo;</a>";
            }
            echo "</div>";

        } catch (Exception $ex) {
            $log = $ex->getMessage() . ": Error in viewing taken rooms";
            error_logger($log);
            echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Maya Hostels view taken rooms page currently unavailable. Please try again later.</p>";
        }
    }
    ?>
</body>

</html>
