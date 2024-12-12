<?php
include('db-connect.php');

function getUrl(){
    return $_SERVER['REQUEST_SCHEME']. "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function sanitize_input($input)
{
    return htmlspecialchars(trim($input));
}

function error_logger($log)
{
    if (!file_exists('maya_hostels_log.txt')) {
        //Write a string to a file
        file_put_contents('maya_hostels_log.txt', '');
    }

    //what info do i want to capture(the ip address, datetime and the error message for what the user tried to do)
    $ip = $_SERVER['REMOTE_ADDR'];
    date_default_timezone_set('Africa/Lusaka');
    $time = date('d/m/y h:iA', time());

    //get the existing contents of our log file
    $contents = file_get_contents('maya_hostels_log.txt');
    //append new data(log) to our log file(t is for tabs and r is for carriage return)
    $contents .= "$ip\t$time\t$log\r";
    //This is where the appending happens
    file_put_contents('maya_hostels_log.txt', $contents);
}

function createStudentView($mysqli, $hostel = null, $status)
{
    define('UPLOADPATH', '../pictures/');
    $key = $_ENV["SECRET_KEY"];
    $iv = $_ENV["IV"];


    $records_per_page = 5;


    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


    $offset = ($page - 1) * $records_per_page;


    $sql = "SELECT * FROM `students` WHERE `hostel`='$hostel' AND `status`= '$status' ORDER BY `room_number` LIMIT $records_per_page OFFSET $offset";
    $query = $mysqli->query($sql);

    if ($query->num_rows < 1) {
        echo "<p style='text-align: center;'>No records found</p>";
        return;
    }

    echo '<div class="table-container">';
    echo '<table cellspacing="0" border="1">';


    echo '<thead><tr>';
    $result = $query->fetch_assoc();
    if ($result) {
        foreach ($result as $field => $value) {
            if ($field !== 'status' && $field !== 'password') {
                echo "<th>" . str_replace('_', ' ', htmlspecialchars($field)) . "</th>";
            }
        }
        echo '<th colspan="3">Actions</th>';
    }
    echo '</tr></thead>';

    $query->data_seek(0);


    echo "<tbody>";
    while ($result = $query->fetch_assoc()) {
        $encrypted_id = urlencode(openssl_encrypt($result['id'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_student_name = urlencode(openssl_encrypt($result['student_name'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_email = urlencode(openssl_encrypt($result['email'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_hostel = urlencode(openssl_encrypt($result['hostel'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_room_number = urlencode(openssl_encrypt($result['room_number'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_bedspace_number = urlencode(openssl_encrypt($result['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));

        echo "<tr>";
        foreach ($result as $field => $value) {
            if ($field === 'profile_picture') {

                $profile_picture_url =  '/pictures/' . htmlspecialchars($value);
                echo "<td><img src='" . $profile_picture_url . "' class='profile' alt='Profile Picture' /></td>";
            } elseif ($field !== 'status' && $field !== 'password') {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
        }

        echo '<td>
                  <form method="post" action="delete.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($result['id']) . '"/>
                      <input type="submit" value="Delete" name="delete" onclick="return confirm(\'Are you sure you want to delete this student?\');"/>
                  </form>
              </td>';
        echo '<td>
                  <form method="get" action="edit.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($encrypted_id) . '"/>
                      <input type="submit" value="Edit"/>
                  </form>
              </td>';
        echo '<td>
                  <form method="get" action="remove_from_room.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($encrypted_id) . '"/>
                      <input type="hidden" name="student_name" value="' . htmlspecialchars($encrypted_student_name) . '"/>
                            <input type="hidden" name="email" value="' . htmlspecialchars($encrypted_email) . '"/>
                            <input type="hidden" name="hostel" value="' . htmlspecialchars($encrypted_hostel) . '"/>
                            <input type="hidden" name="room_number" value="' . htmlspecialchars($encrypted_room_number) . '"/>
                            <input type="hidden" name="bedspace_number" value="' . htmlspecialchars($encrypted_bedspace_number) . '"/>
                      <input type="submit" value="Evict" name="evict" onclick="return confirm(\'Are you sure you want to evict this student from their room?\');"/>
                  </form>
              </td>';
        echo "</tr>";
    }
    echo "</tbody>";
    echo '</table>';
    echo '</div>';


    $count_sql = "SELECT COUNT(*) AS total FROM `students` WHERE `hostel`='$hostel' AND `status`= '$status'";
    $count_query = $mysqli->query($count_sql);
    $total_records = $count_query->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $records_per_page);


    echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
    if ($page > 1) {
        $previous_page = $page - 1;
        echo "<a href='?page=$previous_page'>&laquo; Previous</a> ";
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong> ";
        }
    }

    if ($page < $total_pages) {
        $next_page = $page + 1;
        echo "<a href='?page=$next_page'>Next &raquo;</a>";
    }
    echo "</div>";
}



function getImages($hostel, $mysqli)
{
    $stmt = $mysqli->prepare("SELECT hostel_image FROM `image_gallery` WHERE `hostel` = ?");
    $stmt->bind_param('s', $hostel);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['hostel_image'];
    }
    $stmt->close();
    return $images;
}

function displayImages($hostel, $mysqli)
{
    $images = getImages($hostel, $mysqli);
    foreach ($images as $index => $image) {
        $hidden = $index >= 3 ? 'hidden' : '';
        $image_url =  '/Gallery/' . htmlspecialchars($image);
        echo "<td><img src='" . $image_url . "' class='carousel-image $hidden' alt='$hostel Room' /></td>";
    }
    if (count($images) > 3) {
        echo "<button class='carousel-button prev'>❮</button>";
        echo "<button class='carousel-button next'>❯</button>";
    }
}

function viewOccupant($mysqli, $hostel, $room_number, $bedspace_number)
{

    $key = $_ENV["SECRET_KEY"];
    $iv = $_ENV["IV"];

    $sql = "SELECT * FROM `students` WHERE `hostel` = '$hostel' AND `room_number` = '$room_number' AND `bedspace_number` = '$bedspace_number'";
    $query = $mysqli->query($sql);
    if ($query) {
        echo '<div class="table-container">';

        echo '<table cellspacing="0" border="1">';

        // Table header (outside the loop)
        echo '<thead>';
        echo "<tr>";
        $result = $query->fetch_assoc(); // Fetch the first row to get headers
        if ($result) {
            foreach ($result as $field => $value) {
                if ($field !== 'status' && $field !== 'password') { // Exclude 'status' and 'password'
                    echo "<th>" . str_replace('_', ' ', htmlspecialchars($field)) . "</th>";
                }
            }
            echo '<th colspan="3">Actions</th>';
        }
        echo "</tr>";
        echo "</thead>";

        // Reset pointer to start at first row again
        $query->data_seek(0);


        echo "<tbody>";
        while ($result = $query->fetch_assoc()) {
            $encrypted_id = urlencode(openssl_encrypt($result['id'], 'AES-128-CTR', $key, 0, $iv));
            $encrypted_student_name = urlencode(openssl_encrypt($result['student_name'], 'AES-128-CTR', $key, 0, $iv));
            $encrypted_email = urlencode(openssl_encrypt($result['email'], 'AES-128-CTR', $key, 0, $iv));
            $encrypted_hostel = urlencode(openssl_encrypt($result['hostel'], 'AES-128-CTR', $key, 0, $iv));
            $encrypted_room_number = urlencode(openssl_encrypt($result['room_number'], 'AES-128-CTR', $key, 0, $iv));
            $encrypted_bedspace_number = urlencode(openssl_encrypt($result['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));

            echo "<tr>";
            foreach ($result as $field => $value) {
                if ($field === 'profile_picture') {
                    $profile_picture_url = '/pictures/' . htmlspecialchars($value);
                    echo "<td><img src='" . $profile_picture_url . "' class='profile' alt='Profile Picture' /></td>";
                } else if ($field !== 'status' && $field !== 'password') { // Exclude 'status' and 'password'
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }

            echo '<td>
                        <form method="post" action="delete.php">
                            <input type="hidden" name="id" value="' . htmlspecialchars($result['id']) . '"/>
                            <input type="submit" value="Delete" name="delete" onclick="return confirm(\'Are you sure you want to delete this student?\');"/>
                        </form>
                      </td>';
            echo "<td>
                        <form method='get' action='edit.php'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($encrypted_id) . "'/>
                            <input type='submit' value='Edit'/>
                        </form>
                      </td>";
            echo '<td>
                        <form method="get" action="remove_from_room.php">
                            <input type="hidden" name="id" value="' . htmlspecialchars($encrypted_id) . '"/>
                            <input type="hidden" name="student_name" value="' . htmlspecialchars($encrypted_student_name) . '"/>
                            <input type="hidden" name="email" value="' . htmlspecialchars($encrypted_email) . '"/>
                            <input type="hidden" name="hostel" value="' . htmlspecialchars($encrypted_hostel) . '"/>
                            <input type="hidden" name="room_number" value="' . htmlspecialchars($encrypted_room_number) . '"/>
                            <input type="hidden" name="bedspace_number" value="' . htmlspecialchars($encrypted_bedspace_number) . '"/>
                            <input type="submit" value="Evict" name="evict" onclick="return confirm(\'Are you sure you want to evict this student from their room?\');"/>
                        </form>
                      </td>';
            echo "</tr>";
        }
        echo "</tbody>";
        echo '</table>';
        echo '</div>';
    }
}


function createAllStudentView($mysqli, $status)
{

    $key = $_ENV["SECRET_KEY"];
    $iv = $_ENV["IV"];


    $records_per_page = 5;


    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


    $offset = ($page - 1) * $records_per_page;


    $sql = "SELECT * FROM `students` WHERE `status`= '$status' ORDER BY `room_number` LIMIT $records_per_page OFFSET $offset";
    $query = $mysqli->query($sql);

    if ($query->num_rows < 1) {
        echo "<p style='text-align: center;'>No records found</p>";
        return;
    }

    echo '<div class="table-container">';
    echo '<table cellspacing="0" border="1">';


    echo '<thead><tr>';
    $result = $query->fetch_assoc();
    if ($result) {
        foreach ($result as $field => $value) {
            if ($field !== 'status' && $field !== 'password') {
                echo "<th>" . str_replace('_', ' ', htmlspecialchars($field)) . "</th>";
            }
        }
        echo '<th colspan="3">Actions</th>';
    }
    echo '</tr></thead>';


    $query->data_seek(0);

    echo "<tbody>";
    while ($result = $query->fetch_assoc()) {
        $encrypted_id = urlencode(openssl_encrypt($result['id'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_student_name = urlencode(openssl_encrypt($result['student_name'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_email = urlencode(openssl_encrypt($result['email'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_hostel = urlencode(openssl_encrypt($result['hostel'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_room_number = urlencode(openssl_encrypt($result['room_number'], 'AES-128-CTR', $key, 0, $iv));
        $encrypted_bedspace_number = urlencode(openssl_encrypt($result['bedspace_number'], 'AES-128-CTR', $key, 0, $iv));

        echo "<tr>";
        foreach ($result as $field => $value) {
            if ($field === 'profile_picture') {
                $profile_picture_url = '/pictures/' . htmlspecialchars($value);
                echo "<td><img src='" . $profile_picture_url . "' class='profile' alt='Profile Picture' /></td>";
            } elseif ($field !== 'status' && $field !== 'password') {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
        }

        echo '<td>
                  <form method="post" action="delete.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($result['id']) . '"/>
                      <input type="submit" value="Delete" name="delete" onclick="return confirm(\'Are you sure you want to delete this student?\');"/>
                  </form>
              </td>';
        echo '<td>
                  <form method="get" action="edit.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($encrypted_id) . '"/>
                      <input type="submit" value="Edit"/>
                  </form>
              </td>';
        echo '<td>
                  <form method="get" action="remove_from_room.php">
                      <input type="hidden" name="id" value="' . htmlspecialchars($encrypted_id) . '"/>
                       <input type="hidden" name="student_name" value="' . htmlspecialchars($encrypted_student_name) . '"/>
                            <input type="hidden" name="email" value="' . htmlspecialchars($encrypted_email) . '"/>
                            <input type="hidden" name="hostel" value="' . htmlspecialchars($encrypted_hostel) . '"/>
                            <input type="hidden" name="room_number" value="' . htmlspecialchars($encrypted_room_number) . '"/>
                            <input type="hidden" name="bedspace_number" value="' . htmlspecialchars($encrypted_bedspace_number) . '"/>
                      <input type="submit" value="Evict" name="evict" onclick="return confirm(\'Are you sure you want to evict this student from their room?\');"/>
                  </form>
              </td>';
        echo "</tr>";
    }
    echo "</tbody>";
    echo '</table>';
    echo '</div>';

    $count_sql = "SELECT COUNT(*) AS total FROM `students` WHERE `status`= '$status'";
    $count_query = $mysqli->query($count_sql);
    $total_records = $count_query->fetch_assoc()['total'];
    $total_pages = ceil($total_records / $records_per_page);

    echo "<div class='pagination' style='text-align:center; justify-content:center; align-items:center;'>";
    if ($page > 1) {
        $previous_page = $page - 1;
        echo "<a href='?page=$previous_page'>&laquo; Previous</a> ";
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            echo "<strong>$i</strong> ";
        }
    }

    if ($page < $total_pages) {
        $next_page = $page + 1;
        echo "<a href='?page=$next_page'>Next &raquo;</a>";
    }
    echo "</div>";
}


function csrf_token_validation($page){
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_POST['csrf_token'], $_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expires']) {
        $log = "Invalid CSRF token provided in $page on admin side";
        error_logger($log);
        $message = die("<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error, please try again later!</P>");
    }
}


?>

<script>
    function refreshPageAfterSuccess() {
                setTimeout(() => {
                    window.location.replace('<?php echo getUrl(); ?>');
                }, 5000);
            }
</script>