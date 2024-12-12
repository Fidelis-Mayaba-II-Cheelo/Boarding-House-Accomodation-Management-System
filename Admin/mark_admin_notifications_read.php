<?php
include('db-connect.php');
include('functions.php');
include('session_handler.php');

if($mysqli){
    try{
        if (isset($_POST['admin_notifications_id'])) {
            $admin_notifications_id = sanitize_input($_POST['admin_notifications_id']);
        
            
            $sql = "UPDATE admin_notifications SET is_read = 1 WHERE id = ?";
            
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param('i', $admin_notifications_id); 
                if ($stmt->execute()) {
                    $sql = "DELETE FROM admin_notifications WHERE id = ? AND is_read = 1";
                $query = $mysqli->prepare($sql);
                $query->bind_param('i', $admin_notifications_id);
                if($query->execute()){
                    echo "<p class='success' style='text-align:center; justify-content:center; align-items:center;'>Successfully marked notification as read./p>";
                    header('Location: admin_notifications.php');
                } else{
                    echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the notifications as read.</p>";
                }  
                    
                } else {
                    echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Could not mark as read.</p>";
                }
                $stmt->close();
            }
        }
    }catch(Exception $ex){
        $log = $ex->getMessage() . ": " . "This is coming from mark_admin_notifications_read.php on admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the notification as read</div>";
    }
}

?>