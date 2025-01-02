document.addEventListener('DOMContentLoaded', () => {
    const pass = document.getElementById('pass');
    const confirm_pass = document.getElementById('confirm_pass');

    const password_pattern =  /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    function setBackgroundColor(element,message){
        element.style.backgroundColor = message === 'success' ? 'green' : 'red';
    }

    pass.addEventListener('input', () => {
        if(!password_pattern.test(pass.value) || pass.value.length === 0){
            setBackgroundColor(password_reset_validation, 'error');
            document.getElementById('password_reset_validation').textContent = "Please assign a password to the student. It must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character";
        } else if(password_pattern.test(pass.value)) {
            setBackgroundColor(password_reset_validation, 'success');
            document.getElementById('password_reset_validation').textContent = "Password entered is valid";
        }
    })

    confirm_pass.addEventListener('input', () => {
        if(!password_pattern.test(confirm_pass.value) || confirm_pass.value.length === 0){
            setBackgroundColor(confirm_password_reset_validation, 'error');
            document.getElementById('confirm_password_reset_validation').textContent = "Please assign a password to the student. It must be 8 characters long and must contain atleast one uppercase letter, one lowercase letter, one digit and one special character";
        } else if(confirm_pass.value !== pass.value){
            setBackgroundColor(confirm_password_reset_validation, 'error');
            document.getElementById('confirm_password_reset_validation').textContent = "Please make sure that your passwords match";
        } else if (password_pattern.test(confirm_pass.value) && confirm_pass.value.length > 0 && confirm_pass.value === pass.value){
            setBackgroundColor(confirm_password_reset_validation, 'success');
            document.getElementById('confirm_password_reset_validation').textContent = "Password entered is valid";
        }
    })
})