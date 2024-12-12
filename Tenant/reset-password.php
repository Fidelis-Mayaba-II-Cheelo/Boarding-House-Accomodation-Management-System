<?php
session_start();
include('db-connect.php');
include_once('../Admin/functions.php');

if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expires']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expires'] = time() + 3600;
}

$csrf_token = $_SESSION['csrf_token'];
$submission_successful = false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="reset-password.css">
</head>

<body>
    <h1 class="header">Maya Hostels
        <br />
        <?php
        include('student_menu.php');
        ?>
    </h1>

    <?php
    //Get the hashed random number and email from the URL
    $hash = sanitize_input(($_GET['hash']) ?? null);
    $check = sanitize_input(($_GET['check']) ?? null);
    //If the random number has been passed in the url:
    if ($hash == null) {
        echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Sorry, your request is invalid or has expired</div>';
    } else {
        //Get all the details(email, and student_id importantly) of the requesting student from our forgot password table
        $sql = "SELECT * FROM `forgot_password` WHERE `hash` = ?";
        $stmt = $mysqli?->prepare($sql);
        $stmt->bind_param('s', $hash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Sorry, your request is invalid or has expired</div>';
        } else {
            $row = $result->fetch_assoc();
            $status = $row['status'];
            $userId = $row['student_id'];
            $email = $row['email'];

            // Check if the status is not pending, and if not then throw an error
            // And also check if the hashed email in the forgot password table matches the one passed in the URL
            if ($status != 'PENDING') {
                echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Sorry, your request is invalid or has expired</div>';
            } else if (!password_verify($email, $check)) {
                echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Sorry, your request is invalid or has expired</div>';
            } else {
    ?>

                <h1>Reset Your Password</h1>
                <p>Please Enter new password below.</p>

                <?php
                
                if (isset($_POST['action-reset'])) {

                    csrf_token_validation($_SERVER['PHP_SELF']);

                    $password = sanitize_input(($_POST['password']));
                    $confirmPassword = sanitize_input(($_POST['confirmPassword']));
                    $email = sanitize_input(($_POST['email']));

                    if ($password == null) {
                        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Please enter your new password</p>";
                    } else if ($confirmPassword != $password) {
                        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Please make sure your passwords match</p>";
                    } else if ($email == null) {
                        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Warning: Please enter your email</p>";
                    } else {
                        // Once validation checks are complete, we can now enter our new passwords
                        $newPasswordHash = password_hash($password, PASSWORD_DEFAULT); 

                        if ($mysqli) {
                            try {
                                
                                $sql = "UPDATE `students` SET `password` = ? WHERE `email` = ? AND `id` = ?";
                                $stmt = $mysqli->prepare($sql);
                                $stmt->bind_param('ssi', $newPasswordHash, $email, $userId);
                                $stmt->execute();

                                if ($stmt->affected_rows > 0) {
                                    echo '<div class="success">';
                                    echo '<p>You have successfully reset your password.</p>';
                                    $submission_successful = true;
                                    echo '</div>';
                                    // Update the status to expired in the forgot password table
                                    $stmt = $mysqli?->prepare("UPDATE `forgot_password` SET `status` = 'EXPIRED' WHERE `hash` = ?");
                                    $stmt->bind_param('s', $hash);
                                    $stmt->execute();

                                    unset($_SESSION['csrf_token']);
                                    unset($_SESSION['csrf_token_expires']);
                                    header("Location: login.php");
                                    exit();
                                } else {
                                    echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Something Went wrong. Try again later</div>';
                                }
                            } catch (Exception $ex) {
                                $log = $ex->getMessage() . ":" . "Error coming from reset-password.php on client side";
                                error_logger($log);
                                echo '<div class="error" style="text-align:center; justify-content:center; align-items:center;">Sorry, an error occurred. Try later! </div>';
                            }
                        }
                    }
                }
                ?>
                <?php

                if (isset($submission_successful) && $submission_successful): ?>
                    <script>
                        refreshPageAfterSuccess();
                    </script>
                <?php endif; ?>

                <h1 class="headings">Reset Your Password</h1>
                <div class="form-container">
                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="input-group">
                            <label id="password_reset_validation" class="validation_style"></label><br /><br />
                            <label for="password">Enter New Password</label><br /><br />
                            <input class="contents" type="password" name="password" placeholder="Enter New Password" id="pass" value="<?php echo isset($_POST['password']) ? htmlspecialchars($password) : ''; ?>" required />
                        </div>

                        <div class="input-group">
                            <label id="confirm_password_reset_validation" class="validation_style"></label><br /><br />
                            <label for="confirmPassword">Confirm Password</label><br /><br />
                            <input class="contents" type="password" name="confirmPassword" placeholder="Confirm Password" id="confirm_pass" value="<?php echo isset($_POST['confirmPassword']) ? htmlspecialchars($confirmPassword) : ''; ?>" required />
                        </div>

                        <input type="submit" name="action-reset" value="Reset Password" class="btn" />
                    </form>
                </div>
                <br />
    <?php
            }
        }
    }
    ?>
    <script type="text/javascript" src="reset-password.js"></script>
</body>

</html>