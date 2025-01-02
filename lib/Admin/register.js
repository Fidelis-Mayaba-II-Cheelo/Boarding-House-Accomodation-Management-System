document.addEventListener("DOMContentLoaded", () => {
    const admin_username = document.getElementById("username");
    const admin_email = document.getElementById("email");
    const admin_password = document.getElementById("password");

    function setBackgroundColor(element, message){
        element.style.backgroundColor = message === "success" ? "green" : "red";
    }

    admin_username.addEventListener("input", () => {
        if(admin_username.value === ""){
            setBackgroundColor(admin_username_validation, "error");
            document.getElementById("admin_username_validation").textContent = "Please enter a username";
        } else if (admin_username.value.length < 5){
            setBackgroundColor(admin_username_validation, "error");
            document.getElementById("admin_username_validation").textContent = "Your username should be atleast five characters long.";
        } else {
            setBackgroundColor(admin_username_validation, "success");
            document.getElementById("admin_username_validation").textContent = "Username entered is valid";
        }
    })

    admin_email.addEventListener("input", () => {
        if(validator.isEmail(admin_email.value)){
            setBackgroundColor(admin_email_validation, "success");
            document.getElementById("admin_email_validation").textContent = "Email entered is valid";
        } else {
            setBackgroundColor(admin_email_validation, "error");
            document.getElementById("admin_email_validation").textContent = "Please enter a valid email address";
        }
    })

    const password_pattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    admin_password.addEventListener("input", () => {
        if(admin_password.value.length === 0){
            setBackgroundColor(admin_password_validation, "error");
            document.getElementById("admin_password_validation").textContent = "Please enter a valid password. It must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character";
        } else if(!password_pattern.test(admin_password.value)){
            setBackgroundColor(admin_password_validation, "error");
            document.getElementById("admin_password_validation").textContent = "Please enter a valid password. It must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character";
        } else if(password_pattern.test(admin_password.value)){
            setBackgroundColor(admin_password_validation, "success");
            document.getElementById("admin_password_validation").textContent = "Password entered is valid";
        }
    })
})