<?php
include('db-connect.php');
include_once('../Admin/functions.php');
define('UPLOADPATH', '../pictures/');
include('../Admin/session_handler.php');
session_start();

//Checking if the email is stored in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email']; 

    $sql = "SELECT * FROM `students` WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $profile_picture = $row['profile_picture'];
        $student_name = $row['student_name'];
        $student_number = $row['student_number'];
        $national_registration = $row['national_registration'];
        $gender = $row['gender'];
        $date_of_birth = $row['date_of_birth'];
        $program_of_study = $row['program_of_study'];
        $year_of_study = $row['year_of_study'];
        $phone_number = $row['phone_number'];
        $guardian_phone_number = $row['guardian_phone_number'];
        $email = $row['email'];
        $hostel = $row['hostel'];
        $bedspace_number = $row['bedspace_number'];
        $room_number = $row['room_number'];
        $status = $row['status'];

        $profile_picture_path = '/pictures/' . htmlspecialchars($profile_picture);
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <link rel="stylesheet" href="profile.css">
        </head>

        <body>
            <?php include('student_menu.php'); ?>
            <h1>Welcome To Your Profile <?php echo htmlspecialchars($student_name) ?></h1>
            <p><strong>Student Number: <?php echo htmlspecialchars($student_number) ?></strong></p>
            <table border="1">
                <thead>
                    <tr>
                        <th>Profile Picture</th>
                        <th>Student Name</th>
                        <th>Student Number</th>
                        <th>National Registration</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Program of Study</th>
                        <th>Year Of Study</th>
                        <th>Phone Number</th>
                        <th>Guardian Phone Number</th>
                        <th>Email</th>
                        <th>Hostel</th>
                        <th>Room Number</th>
                        <th>Bedspace Number</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo "<img src='". $profile_picture_path ."' class='profile' />" ?></td>
                        <td><?php echo htmlspecialchars($student_name) ?></td>
                        <td><?php echo htmlspecialchars($student_number) ?></td>
                        <td><?php echo htmlspecialchars($national_registration) ?></td>
                        <td><?php echo htmlspecialchars($gender) ?></td>
                        <td><?php echo htmlspecialchars($date_of_birth) ?></td>
                        <td><?php echo htmlspecialchars($program_of_study) ?></td>
                        <td><?php echo htmlspecialchars($year_of_study) ?></td>
                        <td><?php echo htmlspecialchars($phone_number) ?></td>
                        <td><?php echo htmlspecialchars($guardian_phone_number) ?></td>
                        <td><?php echo htmlspecialchars($email) ?></td>
                        <?php
                        if ($status === 'Approved') {
                            echo "<td>" . htmlspecialchars($hostel) . "</td>";
                            echo "<td>" . htmlspecialchars($room_number) . "</td>";
                            echo "<td>" . htmlspecialchars($bedspace_number) . "</td>";
                            echo "<td>" . htmlspecialchars($status) . "</td>";
                        } else if($status !== 'Approved') {
                            echo "<td colspan='4'>Accommodation status not approved</td>";
                        } else {
                            echo "<td colspan='4'>Accommodation status not approved</td>";
                        }
                        ?>
                    </tr>
                </tbody>
            </table>
            <br />
            <p class="intro">Welcome to Maya Hostels</br />
                You are using our student accommodation management system. Where you can apply for accommodation, view room prices, issue complaints, rate our facilities, and much more in the nearby future.<br />

                This platform allows students who are accommodated by us and our staff access to information relevant to them. In case you run into any problems please contact ICT support on the following number: 0963225635, or via email: fidelismcheeloii@gmail.com.<br />

                To all new accommodated students. Enjoy your stay at Maya Hostels!<br />
                - Maya Hostels Software Developer</p>

                
        </body>

        </html>

<?php
    } else {
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>No records found for this email.</div>";
    }
} else {
    $log = "Session email was not set";
    error_logger($log);
    echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Oops, we are experiencing technical issues. Contact the Admin for more information</div>";
}
?>
