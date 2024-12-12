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
    <h2 class="headings">View Vacant Rooms</h2>
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
                'Quad' => 4,
            ];

            $sql = "SELECT all_combinations.hostel, all_combinations.room_number, all_combinations.bedspace_number
                    FROM (";

            // Generate the UNION SELECT part for each hostel type
            $unionQueries = [];
            foreach ($hostelBedspaces as $hostel => $maxBedspace) {
                for ($room = 1; $room <= 15; $room++) {
                    for ($bedspace = 1; $bedspace <= $maxBedspace; $bedspace++) {
                        $unionQueries[] = "SELECT '$hostel' AS hostel, '$room' AS room_number, '$bedspace' AS bedspace_number";
                    }
                }
            }

            // Combine all queries with UNION
            $sql .= implode(" UNION ", $unionQueries);
            $sql .= ") AS all_combinations
                    LEFT JOIN students s ON all_combinations.hostel = s.hostel 
                                          AND all_combinations.room_number = s.room_number 
                                          AND all_combinations.bedspace_number = s.bedspace_number
                    WHERE s.hostel IS NULL
                    ORDER BY FIELD(all_combinations.hostel, 'Single', 'Double', 'Triple', 'Quad'),
                             all_combinations.room_number + 0 ASC, 
                             all_combinations.bedspace_number + 0 ASC 
                    LIMIT $records_per_page OFFSET $offset";

            
            $query = $mysqli->query($sql);

            // Check and display the vacant rooms
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
                        <form method='get' action='add_to_vacant_room.php'>
                            <input type='hidden' name='hostel' value='" . htmlspecialchars($encrypted_hostel) . "'/>
                            <input type='hidden' name='room_number' value='" . htmlspecialchars($encrypted_room_number) . "'/>
                            <input type='hidden' name='bedspace_number' value='" . htmlspecialchars($encrypted_bedspace_number) . "'/>
                            <input type='submit' value='Add student to room'/>
                        </form>
                      </td>";
                    echo "</tr>";
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo "<p style='text-align:center; justify-content:center; align-items:center;'>No vacant rooms available.</p>";
            }

            // Get the total number for pagination
            $total_sql = "SELECT COUNT(*) AS total FROM ( " . implode(" UNION ", $unionQueries) . " ) AS total_combinations
                           LEFT JOIN students s ON total_combinations.hostel = s.hostel 
                                                  AND total_combinations.room_number = s.room_number 
                                                  AND total_combinations.bedspace_number = s.bedspace_number
                           WHERE s.hostel IS NULL";

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
        } catch (Exception $ex) {
            $log = $ex->getMessage() . ": Error in viewing vacant rooms";
            error_logger($log);
            echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>'Error: Maya Hostels view vacant rooms page currently unavailable. Please try again later.'</p>";
        }
    }
    ?>
</body>

</html>