setInterval(function() {
    fetch('student_menu.php')
    .then(response => response.json())
    .then(data => {
        let badge = document.getElementById('notification-badge');
        if (data.unread_count > 0) {
            badge.innerText = data.unread_count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    });
}, 30000);

/*
The Flow:
Step 1: Every 30 seconds, the JavaScript code runs.
Step 2: A request is sent to 'student_menu.php' to get the unread notification count.
Step 3: The response (JSON data) is received, e.g., { unread_count: 5 }.
Step 4: If unread_count > 0, the badge is updated with the number 5, and the badge is displayed.
Step 5: If unread_count == 0, the badge is hidden 
*/