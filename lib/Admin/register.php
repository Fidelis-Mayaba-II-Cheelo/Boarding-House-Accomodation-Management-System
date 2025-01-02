<?php
session_start();
include('db-connect.php');
include('functions.php');

$message = '';
$submission_successful = false;

if (isset($_POST['submit']) && $mysqli) {
    try{

        $username = sanitize_input(($_POST['username']));
        $email = sanitize_input(($_POST['email']));
        $password = sanitize_input(($_POST['password']));

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $message = "<p class='error'>Invalid email address</p>";
        } else if($email === "" || strlen($email) === 0){
            $message =  "<p class='error'>Please enter a valid email address</p>";
        } else if ($password === "" || strlen($password) === 0){
            $message =  "<p class='error'>Please enter your password</p>";
        } else if (strlen($password) < 8) {
            $message =  "<p class='error'>Password must be at least 8 characters long</p>";
        } else if (!preg_match('/[A-Z]/', $password)) {
            $message =  "<p class='error'>Password must contain at least one upper-case letter</p>";
        } else if (!preg_match('/[a-z]/', $password)) {
            $message =  "<p class='error'>Password must contain at least one lower-case letter</p>";
        } else if (!preg_match('/[0-9]/', $password)) {
            $message =  "<p class='error'>Password must contain at least one number character</p>";
        } else if (!preg_match('/[\W]/', $password)) {
            $message =  "<p class='error'>Password must contain at least one special character</p>";
        } else if($username === "" || strlen($username) < 5){
            $message =  "<p class='error'>Please enter a username that is atleast 5 characters long</p>";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `users` (username, email , `password`) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hash);
            $query = $stmt->execute();
            if ($query) {
                $message =  '<div class="success">You have successfully registered! Please proceed to login</div>';
                $submission_successful = true;
            } else {
                $message =  '<div class="warning">Oops, registration failed.</div>';
            }
        }
    }catch(Exception $ex){
        $log = $ex->getMessage() . ": " . "This is from the register.php file on the admin side";
        error_logger($log);
        $message =  "<div class='error'>Register failed</div>";
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="register.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1 class="header">Maya Hostels
        <br/>
    <?php
    include('menu.php');
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <fieldset class="form-container">
            <legend>Administrator Register</legend>
            <label for="username">Username</label><br/>
            <label id="admin_username_validation" class="validation_style"></label>
            <input class="contents" type="text" name="username" placeholder="username" id="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($username): ''; ?>" required />
            <br />
            <br />
            <label for="email">Email</label><br/>
            <label id="admin_email_validation" class="validation_style"></label>
            <input class="contents" type="text" name="email" placeholder="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($email): ''; ?>" required />
            <br />
            <br />
            <label for="password">Password</label><br/>
            <label id="admin_password_validation" class="validation_style"></label>
            <input class="contents" type="password" name="password" placeholder="password" id="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($password): ''; ?>" required />
            <br />
            <br />
            <input class="btn" type="submit" name="submit" value="register" />
        </fieldset>
    </form>
    <br />
    <hr>
    <p style="font-size:1em">Forgot Password?
        <a href="forgot-password.php">Click Here</a>
    </p>
    <br />
    <script src="https://cdn.jsdelivr.net/npm/validator@latest/validator.min.js"></script>
    <script src="register.js"></script>
</body>
</html>