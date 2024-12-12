<?php
session_start();
include('db-connect.php');
include('menu.php');
include('session_handler.php');

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($mysqli) {
    try {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reviews</title>
            <link type="text/css" rel="stylesheet" href="views.css" />
        </head>

        <body>
            <h1 class="headings">Ratings/Reviews</h1>
            <table border="1" cellspacing="0">
                <thead>
                    <tr>
                        <th>From a Scale of 1-10</th>
                        <th>Ratings</th>
                        <th>Improvement Suggestions</th>
                        <th>Status</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $records_per_page = 5;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    if ($page < 1) $page = 1;

                    $offset = ($page - 1) * $records_per_page;


                    
                    $stmt = $mysqli->prepare("SELECT * FROM `ratings` ORDER BY `date_added` LIMIT $records_per_page OFFSET $offset");
                    $stmt->execute();
                    $query = $stmt->get_result();
                    
                    $key = $_ENV["SECRET_KEY"]; 
                    $iv = $_ENV["IV"];
                    if ($query->num_rows > 0) {
                        while ($result = mysqli_fetch_array($query)) {
                            $readStatus = $result['read_status'];
                            echo "<tr>";
                            echo  "<td>" . htmlspecialchars($result['scale']) . "</td>";
                            echo  "<td>" . htmlspecialchars($result['ratings']) . "</td>";
                            echo  "<td>" . htmlspecialchars($result['improvements']) . "</td>";
                            echo  "<td>" . htmlspecialchars(($readStatus ? 'Read' : 'Unread')) . "</td>";
                            $encrypted_id = urlencode(openssl_encrypt($result['id'], 'AES-128-CTR', $key, 0, $iv));
                            
                            if (!$readStatus) {
                                echo "<td><a href='mark_rating_read.php?id=" . $encrypted_id . "'>Mark as Read</a></td>";
                            } else {
                                echo "<td></td>"; 
                            }
                           
                            echo "<td><a href='delete_rating.php?id=" . $encrypted_id . "'>Delete After Read</a></td>";
                            echo "</tr>";
                        }
                        $total_sql = "SELECT COUNT(*) AS total FROM `ratings`";
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
                    }
                    ?>
                </tbody>
            </table>
        </body>

        </html>
<?php
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from review.php on admin side";
        error_logger($log);
        echo '<p class="error" style="text-align:center; justify-content:center; align-items:center;">Error: ' . "Maya Hostels ratings review page currently unavailable. Please try again later.";
    }
}
?>