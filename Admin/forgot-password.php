<?php
session_start();
include('db-connect.php');
include('functions.php');


if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expires']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expires'] = time() + 3600;
}

$csrf_token = $_SESSION['csrf_token'];
$message = '';
$submission_successful = false;

if (isset($_POST['submit'])) {

    $email = sanitize_input(($_POST['email']));

    if ($email == null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="warning">Please enter a valid email address</div>';
    } else {

        if ($mysqli) {

            try {

                csrf_token_validation($_SERVER['PHP_SELF']);

                //Checking the DB for id of the submitted email address, so that we can use it as a reference point
                $sql = "SELECT `id` FROM `users` WHERE `email` = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $email);
                $query = $stmt->execute();
                $fetching_from_query = $stmt->get_result();
                if ($query && $fetching_from_query->num_rows > 0) {
                    while ($result = mysqli_fetch_array($fetching_from_query)) {
                        $userId = $result['id'];
                    }

                    if ($userId) {
                        //We then write another query to check if the user has already made a request to reset their password
                        //We do this to prevent one user from making multiple requests at the same time
                        //The script looks for any pending password reset requests associated with the user's ID in the reset_password_request table.
                        $sql = "SELECT * FROM `admin_forgot_password` WHERE `admin_id` = ? AND `status`= 'PENDING' ORDER BY `id` DESC LIMIT 1";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $hash = null;
                        //If there’s no pending request:
                        if ($result->num_rows == 0) {
                            //generates a random number (rand(10000, 99999)) to use as a unique key for resetting the password. This key is hashed using password_hash() to ensure security.
                            $random = rand(10000, 99999);
                            //This key is hashed using password_hash() to ensure security.
                            $hash = password_hash($random, PASSWORD_DEFAULT);
                            //Insert the data into the forgot_password table to keep track of password change requests.
                            $status = 'PENDING';
                            $sql = "INSERT INTO `admin_forgot_password` (`admin_id`, `email`, `status`, `hash`) VALUES (?, ?, ?, ?)";
                            //A new row is inserted into the reset_password_request table with the user's ID, email address, PENDING status, and the hash. This represents a new password reset request.
                            $stmt = $mysqli->prepare($sql);
                            $stmt->bind_param("isss", $userId, $email, $status, $hash);
                            $stmt->execute();
                            //If the query is unsuccessful, set the hash to null
                            if (!$result || $mysqli->affected_rows == 0) {
                                $hash = null;
                            }
                            //If the pending request already exists:
                        } else {
                            //If the pending request already exists:
                            if ($row = $result->fetch_assoc()) {
                                //if a pending request exists we just read the hash
                                $hash = $row['hash'];
                            }
                        }

                        //We then hash the email address as well so that it cannot be seen in the url when sending the email
                        $check = password_hash($email, PASSWORD_DEFAULT);
                        //Once a hash has been generated or retrieved:
                        if ($hash) {
                          
                            $to = $email;
                            $subject = "Request to Reset Password for Maya Hostels";
                            $body = "<p>Hello,</p>";
                            $body .= "<p>You recently requested to reset your password for Maya Hostels. Please click the link below to complete the reset.</p>";

                            $body .= "<p><a href=\"http://localhost/reset-password.php?hash=$hash&check=$check\">Reset Password</a></p>";
                            $body .= "<p>Kind regards,<br/>The Maya Hostels Team</p>";

                            $headers = 'MIME-Version: 1.0' . "\r\n";
                            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                            $headers .= "From:Maya Hostels<fidelismcheeloii@gmail.com>";

                            mail($to, $subject, $body, $headers);
                        }
                          
                          unset($_SESSION['csrf_token']);
                          unset($_SESSION['csrf_token_expires']);
                    }
                }
                $message = '<div class="success">Request received, we have sent the request to your email.</div>';
                $submission_successful = true;
            } catch (Exception $ex) {
                error_logger($ex->getMessage() . "This is from forgot-password.php on admin side");
                if (stripos($ex->getMessage(), 'duplicate') !== false) {
                    $message = '<div class="warning">An account with the email you have entered already exists.</div>';
                } else {
                    $message = '<div class="error">Sorry, an error occurred. Try later! </div>';
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="forgot-password.css">
</head>
<body>
<h1 class="header">Maya Hostels
        <br/>
    <?php
    include('menu.php');
    ?>
    </h1>
    <?php
    if (!empty($message)) {
        echo "<div style='text-align:center; justify-content:center; align-items:center;'>$message</div>";
    }

    if (isset($submission_successful) && $submission_successful): ?>
        <p class="success" style="text-align:center; justify-content:center; align-items:center;">Form submitted successfully!</p>
        <script>
            refreshPageAfterSuccess();
        </script>
    <?php endif; ?>
    <h1 class="headings">Forgot Password? - Reset it here</h1>
    <div class="form-container">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <label id="email_validation" class="validation_style"></label><br/><br/>
        <label for="email"><strong>Email</strong></label></br>
        <input class="contents" type="text" name="email" placeholder="Enter your email address" id="email_entered" value="<?php echo isset($_POST['email']) ? htmlspecialchars($email): ''; ?>" required/>
        <br /><br/>
        <input type="submit" name="submit" value="Reset Password" class="btn"/>
    </form>
    </div>
    <script type="text/javascript" src="forgot-password.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/validator@latest/validator.min.js"></script>
</body>

</html>