<?php
session_start();

include('student_menu.php');
include('db-connect.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');


if (isset($_POST['submit']) && $mysqli) {
    
    csrf_token_validation($_SERVER['PHP_SELF']);
       
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_expires']);

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expires'] = time() + 3600; // 

        $hostel = sanitize_input($_POST['hostel']);
        $room_number = sanitize_input($_POST['room_number']);
        $bedspace_number = sanitize_input($_POST['bedspace_number']);
        $student_id = (int) $_SESSION['id']; 
        $status = "Pending";

        // Start the transaction
        $mysqli->begin_transaction();

        try {
            // Check if the room and bedspace are still available (locking the row)
            $stmt = $mysqli->prepare(
                "SELECT * FROM students WHERE hostel = ? AND room_number = ? AND bedspace_number = ? FOR UPDATE"
            );
            $stmt->bind_param('sss', $hostel, $room_number, $bedspace_number);
            $stmt->execute();
            $result = $stmt->get_result();

            // If no results are returned, the bedspace is available
            if ($result->num_rows == 0) {
                // Update the student's accommodation details
                $updateStmt = $mysqli->prepare(
                    "UPDATE students SET hostel = ?, room_number = ?, bedspace_number = ?, status = ? WHERE id = ?"
                );
                $updateStmt->bind_param('ssssi', $hostel, $room_number, $bedspace_number, $status, $student_id);
                $updateStmt->execute();

                // Commit the transaction after successful update
                $mysqli->commit();

                echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Application successful, please wait for approval from the administrator</div>";
                $submission_successful = true;
                // Send notification email to the administrator
                $to = "fidelismcheeloii@gmail.com";
                $subject = "Accommodation approval request";
                $message = "<p>Dear Admin, <br /></p>";
                $message .= "<p>A new request for accommodation approval at Maya Hostels has been submitted. Please sign into your account to attend to it.</p>";
                $message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                $sent = mail($to, $subject, $message, $headers);
                if ($sent) {
                    echo "<div class='success' style='text-align:center; justify-content:center; align-items:center;'>Accommodation request email sent successfully</div>";
                } else {
                    $log = "Error sending accomodation request email to the admin. Check apply_for_accommodation.php script";
                    error_logger($log);
                    echo "<div class='warning' style='text-align:center; justify-content:center; align-items:center;'>Error sending accommodation request email</div>";
                }
            } else {
                // If results are returned, the bedspace is already taken
                echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>The selected room and bedspace are already taken. Please choose another.</div>";
            }
        } catch (Exception $ex) {
            // If there is any error, rollback the transaction
            $mysqli->rollback();
            $log = $ex->getMessage() . ": " . $ex->getTraceAsString() . "This is due to application for accomodation not being successful. Check apply_for_accomodation.php";
            error_logger($log);
            echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Application was NOT successful. Please try again.</div>";
        }
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Accommodation</title>
    <link rel="stylesheet" href="tenant.css">
</head>

<body>
<?php
    if (isset($submission_successful) && $submission_successful): ?>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>
    <h2 class="headings">Apply For a Room</h2>
    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="hostel">Hostel</label>
            <select name="hostel" id="hostel" required>
                <option value="Single" <?php echo (isset($_POST['hostel']) && $_POST['hostel'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                <option value="Double" <?php echo (isset($_POST['hostel']) && $_POST['hostel'] === 'Double') ? 'selected' : ''; ?>>Double</option>
                <option value="Triple" <?php echo (isset($_POST['hostel']) && $_POST['hostel'] === 'Triple') ? 'selected' : ''; ?>>Triple</option>
                <option value="Quadruple" <?php echo (isset($_POST['hostel']) && $_POST['hostel'] === 'Quadruple') ? 'selected' : ''; ?>>Quadruple</option>
            </select>
            <br /><br />

            <label for="room_number">Room Number</label>
            <select name="room_number" id="room_number" value="<?php echo isset($_POST['room_number'])? htmlspecialchars($room_number) : ''; ?>" required></select>
            <br /><br />

            <label for="bedspace_number">Bedspace Number</label>
            <select name="bedspace_number" id="bedspace_number" value="<?php echo isset($_POST['bedspace_number'])? htmlspecialchars($bedspace_number) : ''; ?>" required></select>
            <br /><br />

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

            <input class='btn' type="submit" name="submit" value="Apply" />
        </form>
    </div>

    <script>
        document.getElementById('hostel').addEventListener('change', function() {
            var hostel = this.value;
            var roomSelect = document.getElementById('room_number');
            var bedspaceSelect = document.getElementById('bedspace_number');

            roomSelect.innerHTML = ''; // Clear previous room options
            bedspaceSelect.innerHTML = ''; // Clear previous bedspace options

            // AJAX request to fetch available rooms and bedspaces
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_availability.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var rooms = response.rooms;

                    rooms.forEach(function(room) {
                        var roomOption = document.createElement('option');
                        roomOption.value = room.room_number;
                        roomOption.textContent = 'Room ' + room.room_number;
                        roomSelect.appendChild(roomOption);
                    });

                    // Populate bedspace options when a room is selected
                    roomSelect.addEventListener('change', function() {
                        bedspaceSelect.innerHTML = ''; // Clear previous bedspace options
                        var selectedRoom = this.value;
                        var selectedRoomData = rooms.find(r => r.room_number == selectedRoom);

                        if (selectedRoomData) {
                            selectedRoomData.bedspace_number.forEach(function(bedspace) {
                                var bedspaceOption = document.createElement('option');
                                bedspaceOption.value = bedspace;
                                bedspaceOption.textContent = 'Bedspace ' + bedspace;
                                bedspaceSelect.appendChild(bedspaceOption);
                            });
                        }
                    });

                    // Trigger the room select event to populate bedspaces for the first room
                    if (rooms.length > 0) {
                        roomSelect.dispatchEvent(new Event('change'));
                    }
                }
            };
            xhr.send('hostel=' + encodeURIComponent(hostel));
        });

        // Trigger the change event to populate room and bedspace options on initial load
        document.getElementById('hostel').dispatchEvent(new Event('change'));
    </script>
</body>

</html>