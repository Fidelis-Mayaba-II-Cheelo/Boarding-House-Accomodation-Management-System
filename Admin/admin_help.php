<?php 
session_start();
include('db-connect.php');
include('menu.php');
include('session_handler.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Help Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        p {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Welcome to the Admin Help Page</h1>
    <div class="content">
        <h2>Logging In</h2>
        <p>To begin using the system, you must first log in with your admin credentials. Once logged in, you'll have access to all administrative functions, including managing student accommodations, viewing complaints, and more. The login process ensures that only authorized personnel can manage the system.</p>
    </div>

    <div class="content">
        <h2>Approving or Disapproving Students</h2>
        <p>As an admin, you have the ability to approve or disapprove student accommodation requests. Once a student applies for accommodation, their request will appear in the admin panel. You can choose to approve the request, granting them the room they applied for, or disapprove it. If you choose to disapprove, you may provide a reason for the disapproval. An email will be automatically sent to the student, informing them of the approval or disapproval of their request.</p>
    </div>

    <div class="content">
        <h2>Automated Tasks</h2>
        <p>The system automatically sends payment reminders to all students accomodated at maya hostels every 28th day during an academic semester</p>
        <p>The system also automatically notifies students who have accounts with us, but are not accomodated currently, of room vacancies that appear from time to time</p>
    </div>

    <div class="content">
        <h2>Viewing and Managing Students</h2>
        <p>Admins can view all students who have registered in the system. You can also search for a specific student by entering their name or ID in the search bar. This feature helps you quickly locate a student's information without manually scrolling through the entire list. Additionally, students can be categorized by the type of room they occupy, so you can view who is assigned to which room at any time.</p>
    </div>

    <div class="content">
        <h2>Editing and Deleting Student Information</h2>
        <p>To edit a student’s details, you can either search for the student directly or browse through the list of students. Once located, click the 'Edit' button to make changes to their information. Similarly, if you need to delete a student from the system, you can follow the same process as editing. After locating the student, simply click the 'Delete' button and confirm the action. Be cautious when deleting student records as this action is irreversible.</p>
        <p>You also have the ability to evict students from rooms. This won't delete their records from the system but it will remove them from the room they currently occupy and declare it vacant.</p>
    </div>

    <div class="content">
        <h2>Handling Complaints</h2>
        <p>The system allows students to submit complaints regarding their accommodation or other issues. As an admin, you can view all complaints submitted by students. You can respond to these complaints directly through the system, addressing their concerns or providing solutions. It's important to handle complaints in a timely manner to ensure student satisfaction.</p>
    </div>

    <div class="content">
        <h2>Viewing Notifications</h2>
        <p>The system allows students to send the admin messages regarding their accommodation or other issues. As an admin, you can view all notifications submitted by students. You can respond to these messages directly through the system, addressing their concerns or providing solutions. It's important to reply to these messages in a timely manner to ensure student satisfaction.</p>
    </div>

    <div class="content">
        <h2>Viewing Ratings and Reviews</h2>
        <p>Students can rate and leave reviews about their accommodation and overall experience. The ratings and reviews page allows you to monitor the feedback provided by students, mark them as read or delete them entirely. This is a valuable feature for understanding the quality of services offered and identifying areas where improvements can be made.</p>
    </div>

    <div class="content">
        <h2>Automatic Email Notifications</h2>
        <p>Whenever you approve or disapprove a student’s accommodation request, an automatic email will be sent to the student. This feature ensures that students are promptly notified of their accommodation status without any manual intervention from the admin. If you disapprove a request, you also have the option to include a reason, which will be included in the email notification.</p>
    </div>

    <div class="content">
        <h2>Managing Vacant and Taken Rooms</h2>
        <p>The system allows you as an admin to add students to vacant rooms directly and also evict, delete or edit information of students within a specific room</p>
    </div>

    <div class="content">
        <h2>Adding Images of the Facilities to the Main Page</h2>
        <p>The system allows you, the admin, to add images to the image gallery, for each particular hostel that students or visitors to the Maya Hostels website can view.</p>
    </div>

    <div class="content">
        <h2>Sending Custom Messages</h2>
        <p>The system allows you to send custom messages to particular students or to all students at a go.</p>
    </div>

    <div class="content">
        <h2>Need Further Assistance?</h2>
        <p>If you need further help with the system or encounter any issues, feel free to reach out to the technical support team for assistance.</p>
    </div>

</body>
</html>
