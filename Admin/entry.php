<?php
include('db-connect.php');
session_start();

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


if ($mysqli) {
    try {

        $username = $_ENV["AUTHORIZATION_USERNAME"];
        $password = $_ENV["AUTHORIZATION_PASSWORD"];

        if (isset($_POST['student_login'])) {
            header('Location: /../Tenant/login.php');
            exit();
        } elseif (isset($_POST['admin_login'])) {

            if ($_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password || !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="Admin Login"');
                header('HTTP/1.0 401 Unauthorized');
                echo '<p class="error" style="text-align:center; justify-content:center; align-items:center;">You need to provide valid admin credentials to log in.</p>';
                exit();
            } else {
                header('Location: login.php');
                echo '<p class="success" style="text-align:center; justify-content:center; align-items:center;">Welcome Admin</p>';
                exit();
            }
        }
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from entry.php on admin side";
        error_logger($log);
        echo '<p class="error" style="text-align:center; justify-content:center; align-items:center;">Error: Maya Hostels entry page currently unavailable. Please try again later.</p>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        .form-container {
            width: 30%;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
        }

        h1 {
            font-size: 24px;
            color: white;
        }

        .btn {
            background-color: gainsboro;
            color: white;
            border: 2px solid white;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #333;
            color: white;
        }

        .header {
            text-align: center;
            background-color: #333;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error,
        .warning,
        .success {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .error {
            background-color: red;
            color: white;
            border: 2px solid red;
        }

        .warning {
            background-color: yellow;
            color: white;
            border: 2px solid yellow;
        }

        .success {
            background-color: green;
            color: white;
            border: 2px solid green;
        }



        .main-header span {
            font-size: 16px;
            display: block;
            margin-top: 10px;
            color: #fff8f8;
        }

        .main-header {
            background-color: #333;
            color: white;
            width: 100%;
            padding: 20px 0;
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0;
            
            position: fixed;
            
            top: 0;
            left: 0;
            z-index: 100;
            
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 40px;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
            flex-direction: column;
            padding-top: 100px;
            
        }
    </style>
</head>

<body>

    <div class="main-header">
        Welcome to Our Accommodation System!
        <span>Your gateway to seamless stay and experiences at Maya hostels</span>
    </div>

    <div class="form-container">
        <div class="header">
            <h1>Log in as Admin or Student</h1>
        </div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="submit" name="student_login" value="Student Login" class="btn" />
        </form>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <input type="submit" name="admin_login" value="Administrator Login" class="btn" />
        </form>
    </div>

</body>

</html>