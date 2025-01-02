<?php
session_start();
include('db-connect.php');
include('functions.php');

if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expires']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expires'] = time() + 3600;
}

$csrf_token = $_SESSION['csrf_token'];


if (isset($_COOKIE['logged_in'])) {
    $email = $_COOKIE['logged_in'] ?? null;
    $_SESSION['email'] = $email;
    header('location: add_student.php');
    exit();
}

if (isset($_POST['submit']) && $mysqli) {

    try {
        
        csrf_token_validation($_SERVER['PHP_SELF']);

        
            $email = sanitize_input(($_POST['email']));
            $password = sanitize_input(($_POST['password']));

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $message = "<p class='error'>Invalid email address</p>";
            } else if($email === "" || strlen($email) === 0){
                $message = "<p class='error'>Please enter a valid email address</p>";
            } else if ($password === "" || strlen($password) === 0){
                $message = "<p class='error'>Please enter your password</p>";
            } else if (strlen($password) < 8) {
                $message = "<p class='error'>Please enter the correct password</p>";
            } else if (!preg_match('/[A-Z]/', $password)) {
                $message = "<p class='error' >Please enter the correct password</p>";
            } else if (!preg_match('/[a-z]/', $password)) {
                $message = "<p class='error'>Please enter the correct password</p>";
            } else if (!preg_match('/[0-9]/', $password)) {
                $message = "<p class='error'>Please enter the correct password</p>";
            } else if (!preg_match('/[\W]/', $password)) {
                $message = "<p class='error'>Please enter the correct password</p>";
            } else {

            $sql = "SELECT * FROM `users` WHERE `email` = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $email); 
            $stmt->execute();
            $result = $stmt->get_result();
          
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $hash = $row['password'];
                    if (password_verify($password, $hash)) {
                        echo '<div class="success" style="text-align:center; justify-content:center; align-items:center;">You have successfully logged in!</div>';
                        $_SESSION['email'] = $email;
                        $_SESSION['id'] = $admin_id;
                        session_regenerate_id(true);
                        $expires = time() + (86400 * 30 * 6);
                        setcookie('logged_in', $email, [
                            'expires' => $expires,
                            'path' => '/',
                            'secure' => true,      // Send over HTTPS only
                            'httponly' => true,    // JS cannot access this cookie
                            'samesite' => 'Strict' // Restrict cookie sending in cross-site requests
                        ]);

                        header('location: add_student.php');
                        
                        unset($_SESSION['csrf_token']);
                        unset($_SESSION['csrf_token_expires']);
                        exit();
                    } 
                }
            } else {
                $message = '<div class="warning">Oops, login failed!</div>';
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "This is from the login.php file on the admin side";
        error_logger($log);
        $message = "<div class='error'>Login failed</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="login.css" rel="stylesheet" type="text/css" />
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
    ?>
        
    
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <fieldset class="form-container">
            <legend>Administrator Login</legend>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <label id="email_validation" class="validation_style"></label><br/>
            <label for="Email">Email:</label>
            <input class="contents" type="text" name="email" placeholder="Email" id="email_entered" value="<?php echo isset($_POST['email']) ? htmlspecialchars($email): ''; ?>" required />
            <br />
            <br />
            <label for="Password">Password:</label>
            <input class="contents" type="password" name="password" placeholder="Password" id="password_entered" value="<?php echo isset($_POST['password']) ? htmlspecialchars($password): ''; ?>" required />
            <br />
            <br />
            <input class="btn" type="submit" name="submit" value="login" />
        </fieldset>
    </form>
    <br />
    <hr>
    <p style="font-size:1em">Forgot Password?
        <a href="forgot-password.php">Click Here</a>
    </p>
    <br />
    <script src="https://cdn.jsdelivr.net/npm/validator@latest/validator.min.js"></script>
    <script src="login.js"></script>
</body>
</html>