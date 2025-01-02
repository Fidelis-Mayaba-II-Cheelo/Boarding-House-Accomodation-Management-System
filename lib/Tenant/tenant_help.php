<?php 
session_start();
include('student_menu.php');
include('../Admin/session_handler.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Help Page</title>
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

    <h1>Welcome to the Student Help Page</h1>
    <div class="content">
        <h2>Applying for Accommodation</h2>
        <p>As a student, you can apply for accommodation through the system. To submit your application, simply log in to your account and fill out the accommodation request form. Ensure that you provide all required information, including your preferred room type and any special requests. Once your application is submitted, it will be reviewed by the admin.</p>
    </div>

    <div class="content">
        <h2>Issuing Complaints</h2>
        <p>If you encounter any issues during your stay, you have the option to issue complaints through the system. Navigate to the complaints section and fill out the form with details of your concern. Your complaints will be reviewed by the admin, who will respond accordingly. It is important to communicate any problems you experience to ensure they are addressed promptly.</p>
    </div>

    <div class="content">
        <h2>Rating the Facilities</h2>
        <p>You can provide feedback on the accommodation facilities by rating them. You are allowed to rate the facilities once every three months. Your feedback is valuable as it helps the administration improve services and maintain high standards. To submit a rating, go to the ratings and reviews section, rate the facility and give your feedback.</p>
    </div>

    <div class="content">
        <h2>Viewing Notifications</h2>
        <p>Throughout your time at Maya Hostels, you will receive important notifications from the admin via the system. These notifications may include information about events, maintenance schedules, policy changes, and other relevant updates. Make sure to check your notifications regularly to stay informed about any changes that may affect your accommodation experience.</p>
    </div>

    <div class="content">
        <h2>Complaint Resolution</h2>
        <p>Throughout your time at Maya Hostels, you will receive important updates on your complaints from the admin via the system.</p>
    </div>

    <div class="content">
        <h2>Reach out to Admin</h2>
        <p>Throughout your time at Maya Hostels, you may reach out to the admin via the system for any issues you may wish to clarify.</p>
    </div>

    <div class="content">
        <h2>Need Further Assistance?</h2>
        <p>If you need further help with the system or encounter any issues, feel free to reach out to the technical support team for assistance.</p>
    </div>

</body>
</html>
