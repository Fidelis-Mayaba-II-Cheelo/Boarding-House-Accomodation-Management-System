<?php
include('db-connect.php');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="menu.css">
</head>

<div class="menu">
    <a href="<?php echo isset($_SESSION['email']) ? 'add_student.php' : 'main.php' ?>">Home</a>

    <?php
    if (isset($_SESSION['email'])) {

        //This is for the number of ratings and reviews
        $sql = "SELECT count(*) as unread_ratings FROM `ratings` WHERE read_status = 0";
        $query = $mysqli->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();
            $unread_ratings = $row['unread_ratings'] ?? 0;
        }

        //This is for the complaints
        $sql = "SELECT count(*) as no_of_complaints FROM `complaints` WHERE is_read = 0";
        $query = $mysqli->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();
            $no_of_complaints = $row['no_of_complaints'] ?? 0;
        }

        //This is for the approval requests
        $sql = "SELECT count(*) as approval_requests FROM `students` WHERE `status` = 'Pending'";
        $query = $mysqli->query($sql);
        if ($query) {
            $row = $query->fetch_assoc();
            $approval_requests = $row['approval_requests'] ?? 0;
        }

        //This is for admin notifications
        $sql = "SELECT COUNT(*) as unread_count FROM admin_notifications WHERE is_read = 0";
        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $unread_count = $row['unread_count'] ?? 0; 
    ?>
        <a href="add_student.php">Add Students</a>
        <li class="menu-item">
            <a href="approve_accomodation.php">Approve Accomodation
                <?php if ($approval_requests > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($approval_requests); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <a href="custom_message.php">Send Message To All Students</a>
        <a href="individual_messages.php">Send Message To Particular Student</a>
        <a href="view_pricing.php">Manage Pricing</a>
        <li class="menu-item">
            <a href="view_complaints.php">View Complaints
                <?php if ($no_of_complaints > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($no_of_complaints); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="menu-item">
            <a href="admin_notifications.php">My Notifications
                <?php if ($unread_count > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($unread_count); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="menu-item">
            <a href="review.php">View Student ratings/reviews
                <?php if ($unread_ratings > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($unread_ratings); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <a href="search_students.php">Search For Students</a>
        <a href="view_students.php">View All Students</a>
        <a href="view_single_students.php">View Students In Single Rooms</a>
        <a href="view_double_students.php">View Students In Double Rooms</a>
        <a href="view_triple_students.php">View Students In Triple Rooms</a>
        <a href="view_quad_students.php">View Students In Quadruple Rooms</a>
        <a href="add_to_gallery.php">Add Images to View Rooms Gallery</a>
        <a href="delete_image_from_gallery.php">Delete Images from View Rooms Gallery</a>
        <a href="views.php">View Vacant Rooms</a>
        <a href="view_taken_rooms.php">View Taken Rooms</a>
        <a href="admin_help.php">Help</a>
        <a href="logout.php">Logout</a>
    <?php
    } else {
    ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php
    }
    ?>
</div>