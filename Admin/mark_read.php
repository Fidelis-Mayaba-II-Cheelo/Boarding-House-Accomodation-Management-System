<?php
include('db-connect.php');
include('functions.php');
include('session_handler.php');

if($mysqli){
    try{
        if (isset($_POST['complaint_id'])) {
            $complaint_id = sanitize_input($_POST['complaint_id']);
        
            
            $sql = "UPDATE complaints SET is_read = 1 WHERE id = ?";
            
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param('i', $complaint_id); 
                if ($stmt->execute()) {
                    $sql = "DELETE FROM complaints WHERE id = ? AND is_read = 1";
                    $query = $mysqli->prepare($sql);
                    $query->bind_param('i', $complaint_id);
                    if($query->execute()){
                        echo "<p class='success' style='text-align:center; justify-content:center; align-items:center;'>Successfully marked complaint as read./p>";
                    } else{
                        echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the complaint as read.</p>";
                    }  
                   
                } else {
                    echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Could not mark as read.</p>";
                }
                $stmt->close();
            }
        }
    }catch(Exception $ex){
        $log = $ex->getMessage() . ": " . "This is coming from mark_read.php on admin side";
        error_logger($log);
        echo "<div class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the complaint as read</div>";
    }
}

?>
