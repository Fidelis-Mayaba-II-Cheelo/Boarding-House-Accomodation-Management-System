<?php
session_start();
include('db-connect.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');

if($mysqli){
    try{
        if (isset($_SESSION['id'])) {
            $student_id = $_SESSION['id'];
        
            $sql = "UPDATE complaint_resolution SET is_read = 1 WHERE student_id = ? AND is_read = 0";
            $query = $mysqli->prepare($sql);
            $query->bind_param('i', $student_id);
        
            if ($query->execute()) {
                
                $sql = "DELETE FROM complaint_resolution WHERE student_id = ? AND is_read = 1";
                $query = $mysqli->prepare($sql);
                $query->bind_param('i', $student_id);
                $query->execute();
                header('Location: complaint_resolution.php');
                exit;
            } else {
                echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error marking the complaint solution as read.</p>";
            }
        } else {
            echo "<p class='error' style='text-align:center; justify-content:center; align-items:center;'>Error: Student is not logged in.</p>";
        }
    }catch(Exception $ex){
        error_logger($ex->getMessage() . "This is from mark_conflict_resolution_read.php on client side");
    }
}

?>