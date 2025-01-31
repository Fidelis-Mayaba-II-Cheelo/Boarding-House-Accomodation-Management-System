<?php 
session_start();
include('db-connect.php');
include('functions.php');
include('session_handler.php');
define('UPLOADPATH', '../Gallery/');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Gallery</title>
    <link rel="stylesheet" href="view_rooms.css">
</head>
<body>
<section class="hero-section">
    
    <div id="menu" class="menu">
        <ul><a href="main.php">Home</a></ul>
        <ul><a href="entry.php">Sign up/Sign in</a></ul>
        <ul><a href="view_rooms.php">Image Gallery</a></ul>
        <ul><a href="about_us.php">About Us</a></ul>
    </div>

    <section id="single_rooms" class="room-section">
        <h1>View Single Rooms</h1>
        <div class="carousel">
            <?php displayImages('Single', $mysqli); ?>
        </div>
    </section>
    <section id="double_rooms" class="room-section">
        <h1>View Double Rooms</h1>
        <div class="carousel">
            <?php displayImages('Double', $mysqli); ?>
        </div>
    </section>
    <section id="triple_rooms" class="room-section">
        <h1>View Triple Rooms</h1>
        <div class="carousel">
            <?php displayImages('Triple', $mysqli); ?>
        </div>
    </section>
    <section id="quadruple_rooms" class="room-section">
        <h1>View Quadruple Rooms</h1>
        <div class="carousel">
            <?php displayImages('Quadruple', $mysqli); ?>
        </div>
    </section>
</section>

<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="caption"></div>
</div>

<footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Maya Hostels. All rights reserved.</p>
        <p>Contact us: <a href="mailto:info@mayahostels.com">info@mayahostels.com</a></p>
    </footer>

<script src="view_rooms.js"></script>
</body>
</html>
