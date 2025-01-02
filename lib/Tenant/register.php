<?php
session_start(); 
include('db-connect.php');
include_once('../Admin/functions.php');
define('UPLOADPATH', '../pictures/');

$submission_successful = false;
$message = '';

if (isset($_POST['submit']) && $mysqli) {

    try {

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
        $status = "None";
        $profile_picture = sanitize_input($_FILES['profile_picture']['name']);
        $password = sanitize_input(($_POST['password']));

        
        $current_date = new DateTime();

        
        $ten_years_ago_date = new DateTime();
        $ten_years_ago_date->modify('-10 years');

        $dob = new DateTime($date_of_birth);

        if ($email === null) {
            $message = "<p class='error'>Please enter your email address</p>";
        } else if ($national_registration === null) {
            $message = "<p class='error'>Please enter your national registration number</p>";
        } else if ($gender === null) {
            $message = "<p class='error'>Please select your gender</p>";
        }else if ($date_of_birth === null) {
            $message = "<p class='error'>Please enter your date of birth</p>";
        }else if ($student_name === null) {
            $message = "<p class='error'>Please enter your student name</p>";
        } else if ($student_number === null) {
            $message = "<p class='error'>Please enter your student number</p>";
        } else if ($program_of_study === null) {
            $message = "<p class='error'>Please enter your program of study</p>";
        } else if ($year_of_study === null) {
            $message = "<p class='error'>Please enter your year of study</p>";
        } else if ($phone_number === null) {
            $message = "<p class='error'>Please enter your phone number</p>";
        } else if ($guardian_phone_number === null) {
            $message = "<p class='error'>Please enter your guardian's phone number</p>";
        } else if ($password === null) {
            $message = "<p class='error'>Please assign the student a password</p>";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = "<p class='error'>Please enter a valid email address</p>";
            }else if (!preg_match('/^\d{6}\/\d{2}\/\d{1}$/',$national_registration)) {
                $message = "<p class='error'>Please enter a valid National Registration card number</p>";
            }else if ($dob > $ten_years_ago_date) {
                $message = "<p class='error'>Please enter a date more than ten years ago</p>";
            }else if (strlen($phone_number) !== 10) {
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

                        //if (trim($output) == "FACE_DETECTED") {

                            $hash = password_hash($password, PASSWORD_DEFAULT);

                            $sql = "INSERT INTO `students` 
                    (`profile_picture`, `student_name`, `student_number`, `national_registration`, `gender`, `date_of_birth`, `program_of_study`, `year_of_study`, `phone_number`, `guardian_phone_number`, `email`,  `password`, `status`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?, ?);";
                            $stmt = $mysqli->prepare($sql);
                            $stmt->bind_param("ssissssiiisss", $profile_picture, $student_name, $student_number, $national_registration, $gender, $date_of_birth, $program_of_study, $year_of_study, $phone_number, $guardian_phone_number, $email, $hash, $status);
                            $query = $stmt->execute();

                            if ($query) {
                                $message = "<div class='success'>Registration successful! Please proceed to login</div>";
                                $submission_successful = true;
                                @unlink($_FILES['profile_picture']['tmp_name']);

                                header("Location: login.php");
                                exit();
                            } else {
                                $message = "<div class='warning'>Student records NOT entered successfully!</div>";
                            }
                        /*} else {
                            echo "<div class='error'>The uploaded picture must be a clear photo of the student.</div>";
                            unlink($target);
                        }*/
                    }
                }
                }
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ":" . "This is due to a database issue from register.php on client side";
        error_logger($log);
        $message = "<div class='error'>Techical errors are at play. Please contact the admin for more information</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="register.css">
</head>

<body>
    <h1 class="header">Maya Hostels
        <br />
        <?php
        include('student_menu.php');
        ?>
    </h1>
    <?php
    if(!empty($message)){
       echo "<div style='text-align:center; justify-content:center; align-items:center;'>$message</div>";
    }

    if (isset($submission_successful) && $submission_successful): ?>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>
    <h2 class="headings">Fill in the registration form below</h2>
    <div class="form-container">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

            <label id="profile_picture_validation" class="validation_style"></label>
            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" value="<?php echo isset($_FILES['profile_picture'])? htmlspecialchars($profile_picture) : ''; ?>" accept=".jpeg, .jpg, .png" required />
            <br /><br />

            <label id="student_name_validation" class="validation_style"></label><br />
            <label for="student_name">Student Name</label>
            <input type="text" name="student_name" placeholder="Student Name" id="student_name" value="<?php echo isset($_POST['student_name'])? htmlspecialchars($student_name) : ''; ?>" required />
            <br /><br />

            <label id="student_number_validation" class="validation_style"></label><br />
            <label for="student_number">Student Number</label>
            <input type="number" name="student_number" placeholder="Student Number" id="student_number" value="<?php echo isset($_POST['student_number'])? htmlspecialchars($student_number) : ''; ?>" required />
            <br /><br />

            <label for="national_registration">NRC number</label>
            <br />
            <label id="national_registration_validation" class="validation_style"></label>
            <input type="text" name="national_registration" placeholder="National Registration" id="national_registration" value="<?php echo isset($_POST['national_registration'])? htmlspecialchars($national_registration) : ''; ?>" required />
            <br /><br />

            <label for="gender">Gender</label>
            <br />
            <label id="gender_validation" class="validation_style"></label>
            <select name="gender" id="gender" required>
                <option value="Male" <?php echo (isset($_POST['gender']) &&$_POST['gender'] === 'Male')? 'selected': ' ';?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female')? 'selected': ' '; ?>>Female</option>
            </select>
            <br /><br />

            <label for="date_of_birth">Date of Birth</label>
            <br />
            <label id="date_of_birth_validation" class="validation_style"></label>
            <input type="date" name="date_of_birth" placeholder="Date of Birth" id="date_of_birth" value="<?php echo isset($_POST['date_of_birth'])? htmlspecialchars($date_of_birth) : ''; ?>" required />
            <br /><br />

            <label id="student_program_of_study_validation" class="validation_style"></label><br />
            <label for="program_of_study">Program of Study</label>
            <input type="text" name="program_of_study" placeholder="Program of Study" id="program_of_study" value="<?php echo isset($_POST['program_of_study'])? htmlspecialchars($program_of_study) : ''; ?>" required />
            <br /><br />

            <label id="student_year_of_study_validation" class="validation_style"></label><br />
            <label for="year_of_study">Year of Study</label>
            <input type="number" name="year_of_study" placeholder="Year of Study" id="year_of_study" value="<?php echo isset($_POST['year_of_study'])? htmlspecialchars($year_of_study) : ''; ?>" required />
            <br /><br />

            <label id="student_phone_number_validation" class="validation_style"></label><br />
            <label for="phone_number">Phone Number</label>
            <input type="number" name="phone_number" placeholder="Phone Number" id="phone_number" value="<?php echo isset($_POST['phone_number'])? htmlspecialchars($phone_number) : ''; ?>" required />
            <br /><br />

            <label id="student_guardian_phone_number_validation" class="validation_style"></label><br />
            <label for="guardian_phone_number">Guardian Phone Number</label>
            <input type="number" name="guardian_phone_number" placeholder="Guardian Phone Number" id="guardian_phone_number" value="<?php echo isset($_POST['guardian_phone_number'])? htmlspecialchars($guardian_phone_number) : ''; ?>" required />
            <br /><br />

            <label id="student_email_validation" class="validation_style"></label><br />
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Email" id="email" value="<?php echo isset($_POST['email'])? htmlspecialchars($email) : ''; ?>" required />
            <br /><br />

            <label id="student_password_validation" class="validation_style"></label><br />
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" id="password" value="<?php echo isset($_POST['password'])? htmlspecialchars($password) : ''; ?>" required />
            <br /><br />

            <input class="btn" type="submit" name="submit" value="Register" />

        </form>
        <script src="https://cdn.jsdelivr.net/npm/validator@latest/validator.min.js"></script>
        <script type="text/javascript" src="register.js"></script>
</body>

</html>