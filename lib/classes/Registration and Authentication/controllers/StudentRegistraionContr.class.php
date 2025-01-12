<?php
include_once(__DIR__ . '/../../../../includes/autoloader.php');

class StudentRegistrationContr extends StudentRegistrationModel
{

    public function isEmpty()
    {

        $result = "";
        if (
            empty($this->student_name) || empty($this->student_number) || empty($this->national_registration) || empty($this->email) || empty($this->profile_picture) || empty($this->gender)
            || empty($this->date_of_birth) || empty($this->program_of_study) || empty($this->year_of_study) || empty($this->phone_number) || empty($this->guardian_phone_number) || empty($this->password)
        ) {
            $message = "<section class='section is-small'><div class='notification-container'><div class='notification is-warning'>
                <button class='delete'></button><p>Please ensure all form fields are filled before you submit your information.</p></div></div></section>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }

    public function validateEmail()
    {
        $result = "";
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $message = "<p class='error'>Please enter a valid email address</p>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }


    public function validateNationalRegistration()
    {
        $result = "";
        if (!preg_match('/^\d{6}\/\d{2}\/\d{1}$/', $this->national_registration)) {
            $message = "<p class='error'>Please enter a valid National Registration card number</p>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }

    public function validateDateOfBirth()
    {
        $result = "";

        $tenYearsAgo = new DateTime();
        $tenYearsAgo->modify("-10 years");

        if ($this->date_of_birth > $tenYearsAgo) {
            $message = "<p class='error'>You are not old enough to be an accommodated student</p>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }


    public function validatePhoneNumbers()
    {
        $result = "";
        if (strlen($this->phone_number) !== 10 || strlen($this->guardian_phone_number) !== 10 || !is_numeric($this->phone_number) || !is_numeric($this->guardian_phone_number)) {
            $message = "<p class='error'>Phone numbers must be 10 digits, please enter a valid phone number</p>";
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    public function validateYearOfStudy()
    {
        $result = "";
        if (!in_array(intval($this->year_of_study), range(1, 4)) || !is_numeric($this->year_of_study)) {
            $message = "<p class='error'>Year of study must be between 1 and 4</p>";
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    public function validateStudentNumber()
    {
        $result = "";
        if (!is_numeric($this->student_number)) {
            $message = "<p class='error'>Please enter a valid student number</p>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }

    public function validatePassword()
    {
        $result = "";
        if (strlen($this->password) < 8) {
            $message = "<p class='error'>Password must be at least 8 characters long</p>";
            $result = false;
        } else if (!preg_match('/[A-Z]/', $this->password)) {
            $message = "<p class='error'>Password must contain at least one upper-case letter</p>";
            $result = false;
        } else if (!preg_match('/[a-z]/', $this->password)) {
            $message = "<p class='error'>Password must contain at least one lower-case letter</p>";
            $result = false;
        } else if (!preg_match('/[0-9]/', $this->password)) {
            $message = "<p class='error'>Password must contain at least one number character</p>";
            $result = false;
        } else if (!preg_match('/[\W]/', $this->password)) {
            $message = "<p class='error'>Password must contain at least one special character</p>";
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }

    public function handleRegistration()
    {
        if ($this->isEmpty() && $this->validateEmail() && $this->validateDateOfBirth() && $this->validateNationalRegistration() && $this->validatePhoneNumbers() && $this->validateStudentNumber() && $this->validateYearOfStudy() && $this->validatePassword()) {
            $this->register();
            echo "<section class='section is-small'><div class='notification-container'><div class='notification is-success'>
            <button class='delete'></button><p>Registration was successful.</p></div></div></section>";
?><script>
                function registrationSuccessMessage(callback) {
                    // Show the success message and wait for 3 seconds
                    setTimeout(() => {
                        // Call the callback function after the delay
                        callback();
                    }, 3000); // 3 seconds delay
                }

                function successfulRegistration() {
                    // Redirect to the login page after the success message
                    window.location.href = 'Login.php'; // Using JavaScript for redirection
                }

                // Call the function with the callback
                registrationSuccessMessage(successfulRegistration);
            </script>
<?php
        } else {
            echo "<div class='notification-container'><div class='notification is-danger'>
            <button class='delete'></button><p>Registration Failed.</p></div></div>";
            echo "<script>refreshPageAfterSuccess();</script>";
        }
    }
}
