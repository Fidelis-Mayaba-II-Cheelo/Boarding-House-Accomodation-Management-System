<?php
include('db-connect.php');
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="student_menu.css">
</head>

<?php
if ($mysqli && isset($_SESSION['email'])) {
    $sql = "SELECT * FROM `students`";
    $stmt = $mysqli->prepare($sql);
    $query_execution = $stmt->execute();
    if ($query_execution) {
        $query = $stmt->get_result();
        
        while ($row = $query->fetch_assoc()) {
            $status = $row['status'];
        }
    } else {
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Menu currently unavailable</div>";
    }
}
?>

<div class="menu">
    <a href="<?php echo isset($_SESSION['email']) ? 'profile.php' : '../Admin/main.php' ?>">Home</a>

    <?php
    if (isset($_SESSION['email']) && $status == "None") {

        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];

            
            $sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE student_id = ? AND is_read = 0";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $unread_count = $row['unread_count'] ?? 0; 
        }
    ?>
        <a href="profile.php">My Profile</a>
        <a href="apply_for_accomodation.php">Apply for accomodation</a>
        <a href="view_pricing.php">View Pricing</a>
        <li class="menu-item">
            <a href="notifications.php">My Notifications
                <?php if ($unread_count > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($unread_count); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <a href="tenant_help.php">Help</a>
        <a href="logout.php">Logout</a>
    <?php
    } else if (isset($_SESSION['email']) && $status == "Approved") {

        
        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];

            
            $sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE student_id = ? AND is_read = 0";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $unread_count = $row['unread_count'] ?? 0; 

             
             $sql1 = "SELECT COUNT(*) as complaint_unread_count FROM complaint_resolution WHERE student_id = ? AND is_read = 0";
             $stmt = $mysqli->prepare($sql1);
             $stmt->bind_param('i', $student_id);
             $stmt->execute();
             $result = $stmt->get_result();
             $row = $result->fetch_assoc();
             $complaint_unread_count = $row['complaint_unread_count'] ?? 0; 
        }
    ?>
        <a href="profile.php">My Profile</a>
        <a href="view_pricing.php">View Pricing</a>
        <li class="menu-item">
            <a href="notifications.php">My Notifications
                <?php if ($unread_count > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($unread_count); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="menu-item">
            <a href="complaint_resolution.php">Complaint Resolution
                <?php if ($complaint_unread_count > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($complaint_unread_count); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <a href="complaints.php">Issue a complaint</a>
        <a href="send_a_message.php">Reach out to admin</a>
        <a href="ratings.php">Rate Us</a>
        <a href="tenant_help.php">Help</a>
        <a href="logout.php">Logout</a>
        <?php
    } else if (isset($_SESSION['email']) && $status == "Pending") {
       
        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];

            
            $sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE student_id = ? AND is_read = 0";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $unread_count = $row['unread_count'] ?? 0; 
        }
    ?>
        <a href="profile.php">My Profile</a>
        <a href="view_pricing.php">View Pricing</a>
        <li class="menu-item">
            <a href="notifications.php">My Notifications
                <?php if ($unread_count > 0): ?>
                    <span id="notification-badge" class="badge"><?php echo htmlspecialchars($unread_count); ?></span>
                <?php endif; ?>
            </a>
        </li>
        <a href="tenant_help.php">Help</a>
        <a href="logout.php">Logout</a>
    <?php
    } else {
    ?>
        <a href="view_pricing.php">View Pricing</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>

    <?php
    }
    ?>
</div>