<?php
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');

if ($mysqli) {
    require __DIR__ . "/../vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    try{
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
        <h2 class="headings">View Students in Single Rooms</h2>
                <?php
                $status = 'Approved';
                $hostel = 'Single';
               createStudentView($mysqli, $hostel, $status);
                ?>
    </body>

    </html>
<?php
} catch (Exception $ex) {
    $log = $ex->getMessage() . ": " . "Error coming from view_single_students.php on admin side";
    error_logger($log);
    echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>'Error: Maya Hostels View Students in Single Rooms page currently unavailable. Please try again later.'</p>";
}
}
?>