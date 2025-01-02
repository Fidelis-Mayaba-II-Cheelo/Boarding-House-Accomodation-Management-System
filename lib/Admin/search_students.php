<?php
ob_start();
session_start();
include('db-connect.php');
include('functions.php');
define('UPLOADPATH', '../pictures/');
include('session_handler.php');
require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($mysqli) {
    try {
        $search_term = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Students</title>
    <link rel="stylesheet" href="search_students.css">
</head>

<body>
    <?php include('menu.php'); ?>
    <h2 class="headings">Search for Students by Name</h2>
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="search_term" placeholder="Enter student name" required />
        <input class='btn' type="submit" name="search" value="Search" />
    </form>

    <?php
    if (isset($_GET['search']) && !empty($_GET['search_term'])) {
        $search_term = sanitize_input($_GET['search_term']);
        $search_term = $mysqli->real_escape_string($search_term);

        $records_per_page = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $records_per_page;

        
        $key = $_ENV["SECRET_KEY"];
        $iv = $_ENV["IV"];

        $sql = "SELECT * FROM `students` WHERE `student_name` LIKE '%$search_term%' AND `status`='Approved' LIMIT $records_per_page OFFSET $offset";
        $query = $mysqli->query($sql);
        

        if ($query->num_rows > 0) {
            echo "<h2 class='headings'>Search Results</h2>";
            echo "<table cellspacing='0' border='1'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profile Picture</th>
                        <th>Student Name</th>
                        <th>Student Number</th>
                        <th>National Registration</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Program of Study</th>
                        <th>Year of Study</th>
                        <th>Phone Number</th>
                        <th>Guardian Phone Number</th>
                        <th>Email Address</th>
                        <th>Hostel Type</th>
                        <th>Room Number</th>
                        <th>Bedspace Number</th>
                        <th colspan='3'>Actions</th>
                    </tr>
                </thead>
                <tbody>";

            while ($result = $query->fetch_assoc()) {
                $encrypted_id = urlencode(openssl_encrypt($result['id'], 'AES-128-CTR', $key, 0, $iv));
                $encrypted_student_name = urlencode(openssl_encrypt($result['student_name'], 'AES-128-CTR', $key, 0, $iv));
                $encrypted_email = urlencode(openssl_encrypt($result['email'], 'AES-128-CTR', $key, 0, $iv));
                $encrypted_hostel = urlencode(openssl_encrypt($result['hostel'], 'AES-128-CTR', $key, 0, $iv));
                $encrypted_room_number = urlencode(openssl_encrypt($result['room_number'], 'AES-128-CTR', $key, 0, $iv));
                $encrypted_bedspace_number = urlencode(openssl_encrypt($result['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));

                $profile_picture_url = '/pictures/' . htmlspecialchars($result['profile_picture']);
                echo "<tr>
                    <td>" . htmlspecialchars($result['id']) . "</td>
                    <td><img src='" . $profile_picture_url . "' class='profile' alt='Profile Picture' /></td>
                    <td>" . htmlspecialchars($result['student_name']) . "</td>
                    <td>" . htmlspecialchars($result['student_number']) . "</td>
                    <td>" . htmlspecialchars($result['national_registration']) . "</td>
                    <td>" . htmlspecialchars($result['gender']) . "</td>
                    <td>" . htmlspecialchars($result['date_of_birth']) . "</td>
                    <td>" . htmlspecialchars($result['program_of_study']) . "</td>
                    <td>" . htmlspecialchars($result['year_of_study']) . "</td>
                    <td>" . htmlspecialchars($result['phone_number']) . "</td>
                    <td>" . htmlspecialchars($result['guardian_phone_number']) . "</td>
                    <td>" . htmlspecialchars($result['email']) . "</td>
                    <td>" . htmlspecialchars($result['hostel']) . "</td>
                    <td>" . htmlspecialchars($result['room_number']) . "</td>
                    <td>" . htmlspecialchars($result['bedspace_number']) . "</td>
                    <td><form method='post' action='delete.php'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($result['id']) . "'/>
                        <input type='submit' value='Delete' name='delete' onclick='return confirm(\"Are you sure you want to delete this student?\");'/>
                    </form></td>
                    <td><form method='get' action='edit.php'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($encrypted_id) . "'/>
                        <input type='submit' value='Edit'/>
                    </form></td>
                    <td><form method='get' action='remove_from_room.php'>
                        <input type='hidden' name='id' value='" . htmlspecialchars($encrypted_id) . "'/>
                        <input type='hidden' name='student_name' value='" . htmlspecialchars($encrypted_student_name) . "'/>
                        <input type='hidden' name='student_name' value='" . htmlspecialchars($encrypted_email) . "'/>
                        <input type='hidden' name='student_name' value='" . htmlspecialchars($encrypted_hostel) . "'/>
                        <input type='hidden' name='student_name' value='" . htmlspecialchars($encrypted_room_number) . "'/>
                        <input type='hidden' name='student_name' value='" . htmlspecialchars($encrypted_bedspace_number) . "'/>
                        <input type='submit' value='Evict' name='evict' onclick='return confirm(\"Are you sure you want to evict this student from their room?\");'/>
                    </form></td>
                </tr>";
            }

            echo "</tbody></table>";

            $total_sql = "SELECT COUNT(*) AS total FROM `students` WHERE `student_name` LIKE '%$search_term%'";
            $total_query = $mysqli->query($total_sql);
            $total_records = $total_query->fetch_assoc()['total'];
            $total_pages = ceil($total_records / $records_per_page);

            echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
            if ($page > 1) {
                $previous_page = $page - 1;
                echo "<a href='?search_term=$search_term&search=Search&page=$previous_page'>&laquo; Previous</a> ";
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<strong>$i</strong> ";
                } else {
                    echo "<a href='?search_term=$search_term&search=Search&page=$i'>$i</a> ";
                }
            }
            if ($page < $total_pages) {
                $next_page = $page + 1;
                echo "<a href='?search_term=$search_term&search=Search&page=$next_page'>Next &raquo;</a>";
            }
            echo "</div>";
        } else {
            echo "<p>No students found for search term: " . htmlspecialchars($search_term) . "</p>";
        }
    }
    ?>
</body>
</html>
<?php
    } catch (Exception $ex) {
        error_logger($ex->getMessage());
        echo "<p>Error: Maya Hostels search page unavailable. Please try again later.</p>";
    }
}
?>
