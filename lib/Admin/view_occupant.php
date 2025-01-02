<?php
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');

if ($mysqli) {
    require __DIR__ . "/../vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    try {

        $key = $_ENV["SECRET_KEY"];
        $iv = $_ENV["IV"];
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <link type="text/css" rel="stylesheet" href="views.css" />
        </head>

        <body>
            <?php include('menu.php'); ?>
            <h2 class="headings">View Room Occupant</h2>
            <?php
            if ($mysqli) {
                if (isset($_GET['hostel']) && $_GET['room_number'] && $_GET['bedspace_number']) {
                    $hostel = openssl_decrypt(urldecode($_GET['hostel']), 'AES-128-CTR', $key, 0, $iv);
                    $room_number = openssl_decrypt(urldecode($_GET['room_number']), 'AES-128-CTR', $key, 0, $iv);
                    $bedspace_number = openssl_decrypt(urldecode($_GET['bedspace_number']), 'AES-128-CTR', $key, 0, $iv);

                    if ($hostel === false && $room_number === false && $bedspace_number === false) {
                        $log = "Invalid Id retrieved from url in view_occupant.php on admin side";
                        error_logger($log);
                        die("<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error, please try again later!</P>");
                    }

                    viewOccupant($mysqli, $hostel, $room_number, $bedspace_number);
                }
            }
            ?>
        </body>

        </html>
<?php
    } catch (Exception $ex) {
        $log = $ex->getMessage() . ": " . "Error coming from view_occupant.php on admin side";
        error_logger($log);
        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Maya Hostels view all students page currently unavailable. Please try again later.</p>";
    }
}
?>