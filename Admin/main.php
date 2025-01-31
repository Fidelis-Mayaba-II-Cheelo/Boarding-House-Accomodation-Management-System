<?php
include('db-connect.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maya Hostels - Home</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

    
    <section class="hero-section">
        
        <div id="menu" class="menu">
            <ul><a href="main.php">Home</a>
            <ul><a href="entry.php">Sign up/Sign in</a></ul>
            <ul><a href="view_rooms.php">Image Gallery</a></ul>
            <ul><a href="about_us.php">About Us</a></ul>
        </div>

        <div id="main-body" class="main-body">
            <h1>Welcome To The Maya Hostels Website</h1>
            <p>Modern, Safe and Secure Accommodation just less than 350m from the university.</p>
            <p>Maya Hostels is a modern, safe and secure student accommodation residence conveniently located within 350m walking distance from the University. Hosting students with our single, double triple and quadruple occupancy rooms with high-quality finishes and state-of-the-art security,
                it is a logical choice for future graduates seeking affordable and furnished student accommodation.
            </p>
            <p>Contact us on 0963225635 or via email at info@mayahostels.com</p>
        </div>
    </section>

    
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Maya Hostels. All rights reserved.</p>
        <p>Contact us: <a href="mailto:info@mayahostels.com">info@mayahostels.com</a></p>
    </footer>

</body>
</html>

