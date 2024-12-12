setInterval(
    function(){
        fetch('menu.php')
        .then(response => response.json())
        .then(data => {
            let badge = document.getElementById('notification-badge');
            if(data.unread_ratings > 0){
                badge.innerText = data.unread_ratings;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none'; 
            }
        });
}, 30000);

setInterval(
    function(){
        fetch('menu.php')
        .then(response => response.json())
        .then(data => {
            let badge = document.getElementById('notification-badge');
            if(data.no_of_complaints > 0){
                badge.innerText = data.no_of_complaints;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none'; 
            }
        });
}, 30000);

setInterval(
    function(){
        fetch('menu.php')
        .then(response => response.json())
        .then(data => {
            let badge = document.getElementById('notification-badge');
            if(data.approval_requests > 0){
                badge.innerText = data.approval_requests;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none'; 
            }
        });
}, 30000);