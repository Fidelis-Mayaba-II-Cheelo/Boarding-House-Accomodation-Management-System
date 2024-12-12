<?php
session_start();

include('db-connect.php');
include('functions.php');
define('UPLOADPATH', '../pictures/');

include('session_handler.php');


if (isset($_POST['submit']) && $mysqli) {

    try {
        
        csrf_token_validation($_SERVER['PHP_SELF']);

        $student_name = sanitize_input($_POST['student_name']);
        $student_number = sanitize_input($_POST['student_number']);
        $national_registration = sanitize_input($_POST['national_registration']);
        $gender = sanitize_input($_POST['gender']);
        $date_of_birth = sanitize_input($_POST['date_of_birth']);
        $program_of_study = sanitize_input($_POST['program_of_study']);
        $year_of_study = sanitize_input($_POST['year_of_study']);
        $phone_number = sanitize_input($_POST['phone_number']);
        $guardian_phone_number = sanitize_input($_POST['guardian_phone_number']);
        $email = sanitize_input($_POST['email']);
        $hostel = sanitize_input($_POST['hostel']);
        $room_number = sanitize_input($_POST['room_number']);
        $bedspace_number = sanitize_input($_POST['bedspace_number']);
        $status = "Approved";
        $password = sanitize_input(($_POST['password']));
        $profile_picture = sanitize_input($_FILES['profile_picture']['name']);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        
        $current_date = new DateTime();

       
        $ten_years_ago_date = new DateTime();
        $ten_years_ago_date->modify('-10 years');

      
        $dob = new DateTime($date_of_birth);


        if ($email === null) {
            $message = "<p class='error'>Please enter your email address</p>";
        } else if ($student_name === null) {
            $message = "<p class='error'>Please enter your student name</p>";
        } else if ($student_number === null) {
            $message = "<p class='error'>Please enter your student number</p>";
        } else if ($national_registration === null) {
            $message = "<p class='error'>Please enter your national registration number</p>";
        } else if ($gender === null) {
            $message = "<p class='error'>Please select your gender</p>";
        } else if ($date_of_birth === null) {
            $message = "<p class='error'>Please enter your date of birth</p>";
        } else if ($program_of_study === null) {
            $message = "<p class='error'>Please enter your program of study</p>";
        } else if ($year_of_study === null) {
            $message = "<p class='error'>Please enter your year of study</p>";
        } else if ($phone_number === null) {
            $message = "<p class='error'>Please enter your phone number</p>";
        } else if ($guardian_phone_number === null) {
            $message = "<p class='error'>Please enter your guardian's phone number</p>";
        } else if ($hostel === null) {
            $message = "<p class='error'>Please assign the student to a hostel</p>";
        } else if ($room_number === null) {
            $message = "<p class='error'>Please assign the student to a room</p>";
        } else if ($bedspace_number === null) {
            $message = "<p class='error'>Please assign the student a bedspace</p>";
        } else if ($password === null) {
            $message = "<p class='error'>Please assign the student a password</p>";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "<p class='error'>Please enter a valid email address</p>";
            } else if (!preg_match('/^\d{6}\/\d{2}\/\d{1}$/', $national_registration)) {
                $message = "<p class='error'>Please enter a valid National Registration card number</p>";
            } else if ($dob > $ten_years_ago_date) {
                $message = "<p class='error'>Please enter a date more than ten years ago</p>";
            } else if (strlen($phone_number) !== 10) {
                $message = "<p class='error'>Phone number must be 10 digits, please enter a valid phone number</p>";
            } else if (strlen($guardian_phone_number) !== 10) {
                $message = "<p class='error'>Guardian phone number must be 10 digits, please enter a valid phone number</p>";
            } else if (!in_array(intval($year_of_study), range(1, 4))) {
                $message = "<p class='error'>Year of study must be between 1 and 4</p>";
            } else if (strlen($password) < 8) {
                $message = "<p class='error'>Password must be at least 8 characters long</p>";
            } else if (!preg_match('/[A-Z]/', $password)) {
                $message = "<p class='error'>Password must contain at least one upper-case letter</p>";
            } else if (!preg_match('/[a-z]/', $password)) {
                $message = "<p class='error'>Password must contain at least one lower-case letter</p>";
            } else if (!preg_match('/[0-9]/', $password)) {
                $message = "<p class='error'>Password must contain at least one number character</p>";
            } else if (!preg_match('/[\W]/', $password)) {
                $message = "<p class='error'>Password must contain at least one special character</p>";
            } else if (!is_numeric($student_number)) {
                $message = "<p class='error'>Student number must be a number with 9 digits</p>";
            } else if (!is_numeric($year_of_study)) {
                $message = "<p class='error'>Year of study must be a number between 1 and 4</p>";
            } else if (!is_numeric($phone_number)) {
                $message = "<p class='error'>Phone number must be a number with 10 digits</p>";
            } else if (!is_numeric($guardian_phone_number)) {
                $message = "<p class='error'>Guardian phone number must be a number with 10 digits</p>";
            } else {

                if ($_FILES['profile_picture']['error'] == 0) {
                    $target = UPLOADPATH . basename($profile_picture);

                    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
                    $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
                    
                    if (!in_array($file_type, $allowed_types)) {
                        $message = "<div class='error'>Invalid file type. Only JPEG, JPG, and PNG files are allowed.</div>";
                    } else if ($_FILES['profile_picture']['size'] > 15 * 1024 * 1024) {
                        $message = "<div class='error'>File size exceeds the maximum limit of 15MB.</div>";
                    } else{

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {

                        $pythonScript = escapeshellcmd("python3 check_face.py " . escapeshellarg($target));
                        $output = shell_exec($pythonScript);

                        //if (trim($output) === "FACE_DETECTED") {

                            $sql = "INSERT INTO `students` 
                        (`profile_picture`,`student_name`, `student_number`, `national_registration`, `gender`, `date_of_birth`,`program_of_study`, `year_of_study`, `phone_number`, `guardian_phone_number`, `email`, `hostel`, `room_number`, `bedspace_number`, `status`, `password`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

                            $stmt = $mysqli->prepare($sql);
                            $stmt->bind_param("ssissssiiissssss", $profile_picture, $student_name, $student_number, $national_registration, $gender, $date_of_birth, $program_of_study, $year_of_study, $phone_number, $guardian_phone_number, $email, $hostel, $room_number, $bedspace_number, $status, $hash);
                            $query = $stmt->execute();

                            if ($query) {
                                $message = "<div class='success'>Student records entered successfully!</div>";

                                $to = $email;
                                $subject = "Maya Hostels Accomodation Allocation for $student_name";
                                $message = "<p>Dear $student_name, <br /></p>";
                                $message .= "<p>Your accommodation at Maya Hostels has been successfully approved. Please sign into your account to view your accommodation status.</p>";
                                $message .= "<p>Your login credentials are:</p>";
                                $message .= "<p>Email: $email</p>";
                                $message .= "<p>Password: $password</p>";
                                $message .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";
                                $headers = 'MIME-Version: 1.0' . "\r\n";
                                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                                $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                                $sent = mail($to, $subject, $message, $headers);
                                if ($sent) {
                                    $message = "<div class='success-message'>Accomodation allocation mail sent successfully</div>";
                                    $submission_successful = true;
                                } else {
                                    $message = "<div class='error-message'>Error sending accomodation allocation mail</div>";
                                }

                                unset($_SESSION['csrf_token']);
                                unset($_SESSION['csrf_token_expires']);
                            }
                        /*} else {
                            $message = "<div class='error'>The uploaded picture must be a clear photo of the student.</div>";
                            unlink($target);
                        }*/
                    }
                }
                    @unlink($_FILES['profile_picture']['tmp_name']);
                } else {
                    $log = "There was an error manually adding a student to the system, check add_student.php on admin side";
                    error_logger($log);
                    $message = "<div class='warning'>Student records NOT entered successfully!</div>";
                }
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from add_student.php on admin side";
        error_logger($log);
        if (strpos($ex->getMessage(), 'duplicate') != false) {
            $message = '<div class="warning">Either the email or student number you have entered already exists.</div>';
        } else {
            $message = "<div class='error'>Student records NOT entered successfully!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="add_student.css">
</head>

<body>
    <?php include('menu.php'); ?>
    <?php
    if (!empty($message)) {
        echo "<div style='text-align:center; justify-content:center; align-items:center;'>$message</div>";
    }

    if (isset($submission_successful) && $submission_successful): ?>
        <p class="success" style="text-align:center; justify-content:center; align-items:center;">Student was added to room successfully!</p>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>

    <h2 class="headings">Add Students</h2>
    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token) ?>" />

            <label for="profile_picture">Profile Picture</label>
            <br />
            <label id="profile_picture_validation" class="validation_style"></label>
            <input type="file" accept=".jpeg, .jpg, .png" name="profile_picture" id="profile_picture" value="<?php echo isset($_FILES['profile_picture']) ? htmlspecialchars($profile_picture) : ''; ?>" required />
            <br /><br />

            <label for="student_name">Student Name</label>
            <br />
            <label id="student_name_validation" class="validation_style"></label>
            <input type="text" id="student_name" name="student_name" placeholder="Student Name" value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($student_name) : ''; ?>" required />
            <br /><br />

            <label for="student_number">Student Number</label>
            <br />
            <label id="student_number_validation" class="validation_style"></label>
            <input type="number" name="student_number" placeholder="Student Number" id="student_number" value="<?php echo isset($_POST['student_number']) ? htmlspecialchars($student_number) : ''; ?>" required />
            <br /><br />

            <label for="national_registration">NRC number</label>
            <br />
            <label id="national_registration_validation" class="validation_style"></label>
            <input type="text" name="national_registration" placeholder="National Registration" id="national_registration" value="<?php echo isset($_POST['national_registration']) ? htmlspecialchars($national_registration) : ''; ?>" required />
            <br /><br />

            <label for="gender">Gender</label>
            <br />
            <label id="gender_validation" class="validation_style"></label>
            <select name="gender" id="gender" required>
                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
            <br /><br />

            <label for="date_of_birth">Date of Birth</label>
            <br />
            <label id="date_of_birth_validation" class="validation_style"></label>
            <input type="date" name="date_of_birth" placeholder="Date of Birth" id="date_of_birth" value="<?php echo isset($_POST['date_of_birth']) ? htmlspecialchars($date_of_birth) : ''; ?>" required />
            <br /><br />

            <label for="program_of_study">Program of Study</label>
            <br />
            <label id="student_program_of_study_validation" class="validation_style"></label>
            <input type="text" name="program_of_study" placeholder="Program of Study" id="program_of_study" value="<?php echo isset($_POST['program_of_study']) ? htmlspecialchars($program_of_study) : ''; ?>" required />
            <br /><br />

            <label for="year_of_study">Year of Study</label>
            <br />
            <label id="student_year_of_study_validation" class="validation_style"></label>
            <input type="number" name="year_of_study" placeholder="Year of Study" id="year_of_study" value="<?php echo isset($_POST['year_of_study']) ? htmlspecialchars($year_of_study) : ''; ?>" required />
            <br /><br />

            <label for="phone_number">Phone Number</label>
            <br />
            <label id="student_phone_number_validation" class="validation_style"></label>
            <input type="number" name="phone_number" placeholder="Phone Number" id="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($phone_number) : ''; ?>" required />
            <br /><br />

            <label for="guardian_phone_number">Guardian Phone Number</label>
            <br />
            <label id="student_guardian_phone_number_validation" class="validation_style"></label>
            <input type="number" name="guardian_phone_number" placeholder="Guardian Phone Number" id="guardian_phone_number" value="<?php echo isset($_POST['guardian_phone_number']) ? htmlspecialchars($guardian_phone_number) : ''; ?>" required />
            <br /><br />

            <label for="email">Email</label>
            <br />
            <label id="student_email_validation" class="validation_style"></label>
            <input type="text" name="email" placeholder="Email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($email) : ''; ?>" required />
            <br /><br />

            <label for="hostel">Hostel</label>
            <br />
            <label id="student_hostel_validation" class="validation_style"></label>
            <select name="hostel" id="hostel" required>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Triple">Triple</option>
                <option value="Quadruple">Quadruple</option>
            </select>
            <br /><br />

            <label for="room_number">Room Number</label>
            <br />
            <label id="student_room_number_validation" class="validation_style"></label>
            <select name="room_number" id="room_number" required></select>
            <br /><br />

            <label for="bedspace_number">Bedspace Number</label>
            <br />
            <label id="student_bedspace_number_validation" class="validation_style"></label>
            <select name="bedspace_number" id="bedspace_number" required></select>
            <br /><br />

            <label for="password">Password</label>
            <br />
            <label id="student_password_validation" class="validation_style"></label>
            <input type="password" name="password" placeholder="Password" id="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($password) : ''; ?>" required />
            <br /><br />

            <input class='btn' type="submit" name="submit" value="Add Student" />
        </form>
    </div>
    <script>
        //Mixture of stolen code from stackoverflow(which i edited to make work for me) and my code.
        //I wouldn't touch it if i were you hahaha...
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
    <script src="https://cdn.jsdelivr.net/npm/validator@latest/validator.min.js"></script>
    <script src="add_student.js"></script>
</body>

</html>