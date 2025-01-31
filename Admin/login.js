document.addEventListener("DOMContentLoaded", () => {
    const email_entered = document.getElementById("email_entered");
    

    function setBackgroundColor(element,message){
        element.style.backgroundColor = message === 'success' ? 'green' : 'red';
    }


    email_entered.addEventListener("input", () => {
        if(email_entered.value === ""){
            setBackgroundColor(email_validation, "error");
            document.getElementById("email_validation").textContent = "Please enter an email address";
        } else if(!validator.isEmail(email_entered.value)){
            setBackgroundColor(email_validation, "error");
            document.getElementById("email_validation").textContent = "Please enter a valid email address";
        } else if(validator.isEmail(email_entered.value) && email_entered.value.length > 0){
            setBackgroundColor(email_validation, "success");
            document.getElementById("email_validation").textContent = "Email entered is valid";
        }
    })

})